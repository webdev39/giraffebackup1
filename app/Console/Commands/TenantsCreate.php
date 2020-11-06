<?php

namespace App\Console\Commands;

use App\Events\UserTenantHasBeenCreated;
use App\Http\Requests\ConfirmRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use Illuminate\Console\Command;

class TenantsCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        \DB::transaction(function () {
            $registerForm = $this->getRegisterForm();

            if ($registerForm) {
                $user = app('AuthSer')->createUser($registerForm);

                $confirmForm = $this->getConfirmForm($user->id);

                if ($confirmForm) {
                    $userTenant = app('AuthSer')->createTenant($confirmForm['type'], $confirmForm['name'], $user->id);

                    app('TenantSer')->markTenantAsChosen($userTenant->user_id, $userTenant->tenant_id);

                    event(new UserTenantHasBeenCreated($userTenant));

                    app('AuthSer')->markUserAsConfirmed($user->id);
                    app('AuthSer')->attachToUserTenantRole($userTenant, Role::OWNER_ROLE['name']);
                }
            }
        });
    }

    /**
     * @return array
     */
    private function getRegisterForm(): array
    {
        $this->info('Fill in these fields');

        $form               = [];
        $form['name']       = $this->ask('What is your name?', 'test');
        $form['last_name']  = $this->ask('What is your last name?', 'test');
        $form['email']      = $this->ask('What is your email?', 'test@test.com');
        $form['password']   = $this->secret('What is your password?');

        $validator = \Validator::make($form, (new RegisterRequest())->rules());

        if ($validator->fails()) {
            $errors = $validator->messages();

            $this->warn('Invalid data entered:');

            foreach ($errors->messages() as $errors) {
                foreach ($errors as $error) {
                    $this->warn("* {$error}");
                }
            }

            return [];
        }

        return $form;
    }

    /**
     * @param int $userId
     *
     * @return array
     */
    private function getConfirmForm(int $userId): array
    {
        $this->info('Fill in these fields');

        $form               = [];
        $form['user_id']    = $userId;
        $form['type']       = $this->choice('What is type?', ['Company', 'Project'], 0);
        $form['name']       = $this->ask("What is {$form['type']} name?", 'test');

        $form['type']       = strtolower("{$form['type']}_name");

        $validator = \Validator::make($form, (new ConfirmRequest())->rules());

        if ($validator->fails()) {
            $errors = $validator->messages();

            $this->warn('Invalid data entered:');

            foreach ($errors->messages() as $errors) {
                foreach ($errors as $error) {
                    $this->warn("* {$error}");
                }
            }

            return [];
        }

        return $form;
    }
}
