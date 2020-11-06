<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    
    /**
     * @var AuthService
     */
    private $authService;
    
    /**
     * RegisterController constructor.
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->middleware('guest');
        $this->authService = $authService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => $data['password'],
        ]);
    }
    
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->back();
        }
    }
    
    /**
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback($provider)
    {
        try {
            /** @var \Laravel\Socialite\Two\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();
            if($user = User::whereEmail($socialUser->email)->first()) {
                abort_if(!$user->is_confirmed, 403,'You need to confirm your registration by email');
                abort_if($user->status != 'active', 403, 'You account was deactivated');
                
                $token = $this->authService->loginFromUser($user);
                return Redirect::route('oauth',['token' => $token],302);
            }
            
            $authUser = $this->createUser($socialUser);
            return  redirect(url('/confirm?confirm_hash=' .$authUser->confirm_hash));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect('/');
        }
    }
    
    /**
     * @param $user
     * @return User
     */
    private function createUser($user)
    {
        return $this->authService->createUser([
            'name' => $user->name,
            'last_name' => '',
            'email' => $user->email,
            'password' => \Hash::make(Str::random(8))
        ]);
    }
}
