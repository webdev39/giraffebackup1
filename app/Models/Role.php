<?php

namespace App\Models;

use Zizaco\Entrust\EntrustRole;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $is_default
 * @property int $is_manual
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $perms
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereIsManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 */
class Role extends EntrustRole
{
    const USER_TENANT_ENTITY        = 'user_tenant';
    const USER_TENANT_GROUP_ENTITY  = 'user_tenant_group';

    const TENANT_LEVEL_ROLES_NAMES  = [
        self::OWNER_ROLE['name'],
        self::MEMBER_ROLE['name'],
        self::CUSTOM_ROLE['name'],
        self::SUPER_ADMIN_ROLE['name']
    ];

    const AVAILABLE_TENANT_LEVEL_ROLES = [
        self::ADMIN_ROLE['name'],
        self::MEMBER_ROLE['name'],
        self::MEMBER_ROLE_1['name'],
        self::PRIVILEGED_MEMBER_ROLE['name'],
        self::PRIVILEGED_MEMBER_ROLE_2['name'],
        self::PRIVILEGED_MEMBER_ROLE_3['name']
    ];

    const AVAILABLE_GROUP_LEVEL_ROLES = [
        self::GROUP_LEADER_ROLE['name'],
        self::GROUP_MEMBER_ROLE['name'],
        self::EXTERNAL_ROLE['name']
    ];

    const SUPER_ADMIN_ROLE = [
        'name'         => 'super-admin',
        'display_name' => 'Owner of the project',
        'description'  => 'User has all permissions',
        'permissions'  => []
    ];

    const OWNER_ROLE = [
        'name'         => 'owner',
        'display_name' => 'User Owner of the tenant',
        'description'  => 'User is allowed to manage his tenant',
        'permissions'  => [
            Permission::CREATE_SUBSCRIPTION_PERMISSION,
            Permission::EDIT_SUBSCRIPTION_PERMISSION,
            Permission::DELETE_SUBSCRIPTION_PERMISSION,

            Permission::CAN_INVITE_OTHERS_PERMISSIONS,
            Permission::EDIT_TENANT_INVITE_PERMISSION,
            Permission::DELETE_TENANT_INVITE_PERMISSION,

            Permission::CREATE_ROLE_PERMISSION,
            Permission::EDIT_ROLE_PERMISSION,
            Permission::DELETE_ROLE_PERMISSION,

            Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION,
            Permission::MANAGE_TENANT_LEVEL_ROLE_PERMISSION,

            Permission::CREATE_PIPELINE_PERMISSION,
            Permission::UPDATE_PIPELINE_PERMISSION,
            Permission::DELETE_PIPELINE_PERMISSION,
            Permission::READ_PIPELINE_PERMISSION,

            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,

            Permission::CREATE_BOARD_PERMISSION,
            Permission::UPDATE_BOARD_PERMISSION,
            Permission::DELETE_BOARD_PERMISSION,
            Permission::READ_BOARD_PERMISSION,

            Permission::CREATE_GROUP_PERMISSION,
            Permission::UPDATE_GROUP_PERMISSION,
            Permission::DELETE_GROUP_PERMISSION,
            Permission::READ_GROUP_PERMISSION,
            Permission::MANAGE_GROUP_MEMBERS_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::EDIT_TIME_LOGS_PERMISSION,
            Permission::DELETE_TIME_LOGS_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,
            Permission::REPORT_OWN_DATA_PERMISSION,
            Permission::REPORT_OTHER_DATA_PERMISSION,
            Permission::BILLING_MODULE_PERMISSION,
            Permission::BILLING_ACTIONS_PERMISSION,
            Permission::ACP_ACCESS_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,

            Permission::READ_BILLING_PERMISSION,
            Permission::ADD_ASSIGNEES_TASK_PERMISSION,
            Permission::DELETE_ASSIGNEES_TASK_PERMISSION,

            Permission::READ_OTHER_COMMENTS_PERMISSION,
            Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION,
        ]
    ];

    const ADMIN_ROLE = [
        'name'         => 'admin',
        'display_name' => 'User Administrator of tenant',
        'description'  => 'User is allowed to manage the tenant',
        'permissions'  => [
            Permission::CREATE_SUBSCRIPTION_PERMISSION,
            Permission::EDIT_SUBSCRIPTION_PERMISSION,
            Permission::DELETE_SUBSCRIPTION_PERMISSION,

            Permission::CAN_INVITE_OTHERS_PERMISSIONS,
            Permission::EDIT_TENANT_INVITE_PERMISSION,
            Permission::DELETE_TENANT_INVITE_PERMISSION,

            Permission::MANAGE_GROUP_LEVEL_ROLE_PERMISSION,
            Permission::MANAGE_TENANT_LEVEL_ROLE_PERMISSION,

            Permission::CREATE_ROLE_PERMISSION,
            Permission::EDIT_ROLE_PERMISSION,
            Permission::DELETE_ROLE_PERMISSION,

            Permission::CREATE_PIPELINE_PERMISSION,
            Permission::UPDATE_PIPELINE_PERMISSION,
            Permission::DELETE_PIPELINE_PERMISSION,
            Permission::READ_PIPELINE_PERMISSION,

            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,
            Permission::ADD_ASSIGNEES_TASK_PERMISSION,
            Permission::DELETE_ASSIGNEES_TASK_PERMISSION,

            Permission::CREATE_BOARD_PERMISSION,
            Permission::UPDATE_BOARD_PERMISSION,
            Permission::DELETE_BOARD_PERMISSION,
            Permission::READ_BOARD_PERMISSION,

            Permission::CREATE_GROUP_PERMISSION,
            Permission::UPDATE_GROUP_PERMISSION,
            Permission::DELETE_GROUP_PERMISSION,
            Permission::READ_GROUP_PERMISSION,
            Permission::MANAGE_GROUP_MEMBERS_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::EDIT_TIME_LOGS_PERMISSION,
            Permission::DELETE_TIME_LOGS_PERMISSION,

            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,
            Permission::REPORT_OWN_DATA_PERMISSION,
            Permission::REPORT_OTHER_DATA_PERMISSION,
            Permission::ACP_ACCESS_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,
            Permission::READ_BILLING_PERMISSION,
        ]
    ];

    const PRIVILEGED_MEMBER_ROLE = [
        'name'         => 'privileged-member-1',
        'display_name' => 'Privileged Member 1',
        'description'  => 'Has access to his own data in Reports',
        'permissions'  => [
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,
            Permission::REPORT_OWN_DATA_PERMISSION,

            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,
            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::TIME_TRACKING_PERMISSION,
            Permission::CREATE_GROUP_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,
        ]
    ];

    const PRIVILEGED_MEMBER_ROLE_2 = [
        'name'         => 'privileged-member-2',
        'display_name' => 'Privileged Member 2',
        'description'  => 'Has access to all data in Reports',
        'permissions'  => [
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,
            Permission::REPORT_OWN_DATA_PERMISSION,
            Permission::REPORT_OTHER_DATA_PERMISSION,

            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,
            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::TIME_TRACKING_PERMISSION,

            Permission::CREATE_GROUP_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,
        ]
    ];

    const PRIVILEGED_MEMBER_ROLE_3 = [
        'name'         => 'privileged-member-3',
        'display_name' => 'Privileged Member 3',
        'description'  => 'Has access to all data in Reports + Billing',
        'permissions'  => [
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,
            Permission::REPORT_OWN_DATA_PERMISSION,
            Permission::REPORT_OTHER_DATA_PERMISSION,

            Permission::READ_BILLING_PERMISSION,

            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,
            Permission::TIME_TRACKING_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::CREATE_GROUP_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,
        ]
    ];

    const MEMBER_ROLE = [
        'name'         => 'member',
        'display_name' => 'Member 1',
        'description'  => 'No access to Reports + Billing',
        'permissions'  => [
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,

            Permission::READ_PIPELINE_PERMISSION,

            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION
        ]
    ];

    const MEMBER_ROLE_1 = [
        'name'         => 'member-2',
        'display_name' => 'Member 2',
        'description'  => 'Can Create Groups, no access to Reports + Billing',
        'permissions'  => [
            Permission::MANAGEMENT_OTHER_DATA_PERMISSION,
            Permission::MANAGEMENT_OWN_DATA_PERMISSION,

            Permission::READ_PIPELINE_PERMISSION,

            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::CREATE_GROUP_PERMISSION,

            Permission::MANAGE_CUSTOMERS_PERMISSION,
        ]
    ];

    const EXTERNAL_ROLE = [
        'name'         => 'external',
        'display_name' => 'External',
        'description'  => 'User is allowed to manage only his tasks',
        'permissions'  => [
            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION
        ]
    ];

    const GROUP_LEADER_ROLE = [
        'name'         => 'group-admin',
        'display_name' => 'Group Admin',
        'description'  => 'User is an administrator of the group',
        'permissions'  => [
            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,
            Permission::ADD_ASSIGNEES_TASK_PERMISSION,
            Permission::DELETE_ASSIGNEES_TASK_PERMISSION,

            Permission::CREATE_BOARD_PERMISSION,
            Permission::UPDATE_BOARD_PERMISSION,
            Permission::DELETE_BOARD_PERMISSION,
            Permission::READ_BOARD_PERMISSION,

            Permission::UPDATE_GROUP_PERMISSION,
            Permission::DELETE_GROUP_PERMISSION,
            Permission::READ_GROUP_PERMISSION,
            Permission::MANAGE_GROUP_MEMBERS_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::EDIT_TIME_LOGS_PERMISSION,
            Permission::DELETE_TIME_LOGS_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,
            Permission::READ_OTHER_COMMENTS_PERMISSION,
            Permission::MANAGE_OTHER_TIME_LOGS_PERMISSION,
        ]
    ];

    const GROUP_MEMBER_ROLE = [
        'name'         => 'group-member',
        'display_name' => 'Member',
        'description'  => 'User is a member of the group',
        'permissions'  => [
            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,
            Permission::ADD_ASSIGNEES_TASK_PERMISSION,
            Permission::DELETE_ASSIGNEES_TASK_PERMISSION,

            Permission::READ_BOARD_PERMISSION,

            Permission::READ_GROUP_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION
        ]
    ];

    const CUSTOM_ROLE = [
        'name'         => 'custom',
        'display_name' => 'Custom Role',
        'description'  => 'Custom Role with permissions user can design his own role',
        'permissions'  => [
            Permission::CREATE_TASK_PERMISSION,
            Permission::UPDATE_TASK_PERMISSION,
            Permission::DELETE_TASK_PERMISSION,
            Permission::READ_TASK_PERMISSION,

            Permission::CREATE_BOARD_PERMISSION,
            Permission::UPDATE_BOARD_PERMISSION,
            Permission::DELETE_BOARD_PERMISSION,
            Permission::READ_BOARD_PERMISSION,

            Permission::UPDATE_GROUP_PERMISSION,
            Permission::DELETE_GROUP_PERMISSION,
            Permission::READ_GROUP_PERMISSION,

            Permission::TIME_TRACKING_PERMISSION,

            Permission::READ_TIME_LOGS_PERMISSION,
            Permission::EDIT_TIME_LOGS_PERMISSION,
            Permission::DELETE_TIME_LOGS_PERMISSION,
        ]
    ];

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_default',
        'is_manual',
        'is_one_permission',
    ];

    protected $table = 'roles';


    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
}
