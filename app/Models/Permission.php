<?php

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Permission query()
 */
class Permission extends EntrustPermission
{
    const CAN_INVITE_OTHERS_PERMISSIONS = [
        'name'         => 'can-invite-others',
        'display_name' => 'User can invite other users',
        'description'  => 'User can invite other users'
    ];

    const CREATE_USER_PERMISSION = [
        'name'         => 'create-user',
        'display_name' => 'Create user',
        'description'  => 'Create existing user'
    ];

    const EDIT_USER_PERMISSION = [
        'name'         => 'edit-user',
        'display_name' => 'Edit user',
        'description'  => 'Edit existing user'
    ];

    const DELETE_USER_PERMISSION = [
        'name'         => 'delete-user',
        'display_name' => 'Delete user',
        'description'  => 'Delete existing user'
    ];

    const MANAGE_TENANT_LEVEL_ROLE_PERMISSION = [
        'name'         => 'manage-tenant-level-role',
        'display_name' => 'manage tenant level role',
        'description'  => 'manage tenant level role'
    ];

    const MANAGE_GROUP_LEVEL_ROLE_PERMISSION = [
        'name'         => 'manage-group-level-role',
        'display_name' => 'manage group level role',
        'description'  => 'manage group level role'
    ];

    const CREATE_TENANT_PERMISSION = [
        'name'         => 'create-tenant',
        'display_name' => 'Create tenant',
        'description'  => 'Create existing tenant'
    ];

    const EDIT_TENANT_PERMISSION = [
        'name'         => 'edit-tenant',
        'display_name' => 'Edit tenant',
        'description'  => 'Edit existing tenant'
    ];

    const DELETE_TENANT_PERMISSION = [
        'name'         => 'delete-tenant',
        'display_name' => 'Delete tenant',
        'description'  => 'Delete existing tenant'
    ];

    const CREATE_SUBSCRIPTION_PERMISSION = [
        'name'         => 'create-subscription',
        'display_name' => 'Create subscription',
        'description'  => 'Create existing subscription'
    ];

    const EDIT_SUBSCRIPTION_PERMISSION = [
        'name'         => 'edit-subscription',
        'display_name' => 'Edit subscription',
        'description'  => 'Edit existing subscription'
    ];

    const DELETE_SUBSCRIPTION_PERMISSION = [
        'name'         => 'delete-subscription',
        'display_name' => 'Delete subscription',
        'description'  => 'Delete existing subscription'
    ];

    const CREATE_TENANT_INVITE_PERMISSION = [
        'name'         => 'create-tenant-invite',
        'display_name' => 'Create tenant-invite',
        'description'  => 'Create existing tenant-invite'
    ];

    const EDIT_TENANT_INVITE_PERMISSION = [
        'name'         => 'edit-tenant-invite',
        'display_name' => 'Edit tenant-invite',
        'description'  => 'Edit existing tenant-invite'
    ];

    const DELETE_TENANT_INVITE_PERMISSION = [
        'name'         => 'delete-tenant-invite',
        'display_name' => 'Delete tenant-invite',
        'description'  => 'Delete existing tenant-invite'
    ];

    const CREATE_ROLE_PERMISSION = [
        'name'         => 'create-role',
        'display_name' => 'Create role',
        'description'  => 'Create existing role'
    ];

    const EDIT_ROLE_PERMISSION = [
        'name'         => 'edit-role',
        'display_name' => 'Edit role',
        'description'  => 'Edit existing role'
    ];

    const DELETE_ROLE_PERMISSION = [
        'name'         => 'delete-role',
        'display_name' => 'Delete role',
        'description'  => 'Delete existing role'
    ];

    const CREATE_PIPELINE_PERMISSION = [
        'name'         => 'create-pipeline',
        'display_name' => 'Create a new Pipeline',
        'description'  => 'Create a new Pipeline'
    ];

    const UPDATE_PIPELINE_PERMISSION = [
        'name'         => 'update-pipeline',
        'display_name' => 'Update the Pipeline',
        'description'  => 'Update the Pipeline'
    ];

    const DELETE_PIPELINE_PERMISSION = [
        'name'         => 'delete-pipeline',
        'display_name' => 'Delete the Pipeline',
        'description'  => 'Delete the Pipeline'
    ];

    const READ_PIPELINE_PERMISSION = [
        'name'         => 'read-pipeline',
        'display_name' => 'Read Pipelines',
        'description'  => 'User can read Pipelines'
    ];

    const CREATE_TASK_PERMISSION = [
        'name'         => 'create-task',
        'display_name' => 'Create a new Task',
        'description'  => 'Create a new Task'
    ];

    const UPDATE_TASK_PERMISSION = [
        'name'         => 'update-task',
        'display_name' => 'Update the Task',
        'description'  => 'Update the Task'
    ];

    const DELETE_TASK_PERMISSION = [
        'name'         => 'delete-task',
        'display_name' => 'Delete the Task',
        'description'  => 'Delete the Task'
    ];

    const READ_TASK_PERMISSION = [
        'name'         => 'read-task',
        'display_name' => 'Read Tasks',
        'description'  => 'User can read tasks'
    ];

    const ADD_ASSIGNEES_TASK_PERMISSION = [
        'name'         => 'add-assignees-task',
        'display_name' => 'Add Assignees to the Task',
        'description'  => 'User can add new assignees to the task'
    ];

    const DELETE_ASSIGNEES_TASK_PERMISSION = [
        'name'         => 'delete-assignees-task',
        'display_name' => 'Delete Assignees from the Task',
        'description'  => 'User can delete assignees from the task'
    ];

    const CREATE_BOARD_PERMISSION = [
        'name'         => 'create-board',
        'display_name' => 'Create a new Board',
        'description'  => 'Create a new Board'
    ];

    const UPDATE_BOARD_PERMISSION = [
        'name'         => 'update-board',
        'display_name' => 'Update the Board',
        'description'  => 'Update the Board'
    ];

    const DELETE_BOARD_PERMISSION = [
        'name'         => 'delete-board',
        'display_name' => 'Delete the Board',
        'description'  => 'Delete the Board'
    ];

    const READ_BOARD_PERMISSION = [
        'name'         => 'read-board',
        'display_name' => 'Read Boards',
        'description'  => 'User can read Boards'
    ];

    const CREATE_GROUP_PERMISSION = [
        'name'         => 'create-group',
        'display_name' => 'Create a new Group',
        'description'  => 'Create a new Group'
    ];

    const UPDATE_GROUP_PERMISSION = [
        'name'         => 'update-group',
        'display_name' => 'Update the Group',
        'description'  => 'Update the Group'
    ];

    const DELETE_GROUP_PERMISSION = [
        'name'         => 'delete-group',
        'display_name' => 'Delete the Group',
        'description'  => 'Delete the Group'
    ];

    const READ_ALL_GROUPS_PERMISSION = [
        'name'         => 'read-all-groups',
        'display_name' => 'Read All Groups',
        'description'  => 'User can read Groups'
    ];

    const READ_GROUP_PERMISSION = [
        'name'         => 'read-group',
        'display_name' => 'Read Groups',
        'description'  => 'User can read Groups'
    ];

    const CLONE_GROUP_PERMISSION = [
        'name'         => 'clone-group',
        'display_name' => 'Clone Groups',
        'description'  => 'User can clone Groups'
    ];

    const MANAGE_GROUP_MEMBERS_PERMISSION = [
        'name'         => 'manage-group-members',
        'display_name' => 'Manage group members',
        'description'  => 'User can manage group members'
    ];

    const ADD_GROUP_PERMISSION = [
        'name'         => 'add-group',
        'display_name' => 'Add new Group',
        'description'  => 'User can add new group'
    ];

    const TIME_TRACKING_PERMISSION = [
        'name'         => 'time-tracking',
        'display_name' => 'Time tracking',
        'description'  => 'User can track the time in task'
    ];

    const READ_TIME_LOGS_PERMISSION = [
        'name'         => 'read-other-time-logs',
        'display_name' => 'Read other time logs',
        'description'  => 'User can read other time logs of tasks'
    ];

    const EDIT_TIME_LOGS_PERMISSION = [
        'name'         => 'edit-other-time-logs',
        'display_name' => 'Edit other time logs',
        'description'  => 'User can edit other time logs of tasks'
    ];

    const DELETE_TIME_LOGS_PERMISSION = [
        'name'         => 'delete-other-time-logs',
        'display_name' => 'Delete other time logs',
        'description'  => 'User can delete other time logs of tasks'
    ];

    const REPORT_OWN_DATA_PERMISSION = [
        'name'         => 'report-own-data',
        'display_name' => 'Report-Module own data',
        'description'  => 'Read report-module with own data'
    ];

    const REPORT_OTHER_DATA_PERMISSION = [
        'name'         => 'report-other-data',
        'display_name' => 'Report-Module other data',
        'description'  => 'Read report-module with other user data'
    ];

    const MANAGEMENT_OWN_DATA_PERMISSION = [
        'name'         => 'management-own-data',
        'display_name' => 'Management Module own data',
        'description'  => 'Read management-module with own data'
    ];

    const MANAGEMENT_OTHER_DATA_PERMISSION = [
        'name'         => 'management-other-data',
        'display_name' => 'Management Module other data',
        'description'  => 'Read management-module with other user data'
    ];

    const BILLING_MODULE_PERMISSION = [
        'name'         => 'billing-module',
        'display_name' => 'Billing Module',
        'description'  => 'Access to billing module'
    ];

    const BILLING_ACTIONS_PERMISSION = [
        'name'         => 'billing-actions',
        'display_name' => 'Billing actions',
        'description'  => 'Access to billing actions'
    ];

    const ACP_ACCESS_PERMISSION = [
        'name'         => 'acp-access',
        'display_name' => 'Acp access',
        'description'  => 'Acp access'
    ];

    const MANAGE_CUSTOMERS_PERMISSION = [
        'name'         => 'manage-customers',
        'display_name' => 'Manage customers',
        'description'  => 'User can manage customers'
    ];

    const READ_BILLING_PERMISSION = [
        'name'         => 'read-billing',
        'display_name' => 'Read billing reports',
        'description'  => 'User can read billing report'
    ];

    const SYSTEM_SETTINGS_PERMISSION = [
        'name'         => 'system-settings',
        'display_name' => 'System Settings',
        'description'  => 'The tenant can change his system setting including pipelines'
    ];

    const READ_OTHER_COMMENTS_PERMISSION = [
        'name' => 'read-other-comments',
        'display_name' => 'Read other comments',
        'description' => '',
    ];


    const MANAGE_OTHER_TIME_LOGS_PERMISSION = [
        'name' => 'manage-other-time-logs',
        'display_name' => 'Read other time logs',
        'description' => '',
    ];

    protected $table = 'permissions';
}
