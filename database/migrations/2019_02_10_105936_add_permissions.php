<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

class AddPermissions extends Migration
{
    /**
     * @throws ReflectionException
     */
    public function up()
    {
        $refl = new ReflectionClass(Permission::class);
        foreach ($refl->getConstants() as $constantCode => $constantData) {
            if(substr($constantCode, -10) === 'PERMISSION' && !Permission::whereName($constantData['name'])->count()) {
                $constant = new Permission();
                $constant
                    ->forceFill($constantData)
                    ->save();
            }
        }
    }
}
