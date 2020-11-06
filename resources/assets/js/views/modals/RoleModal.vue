<!--Optimized-->
<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        :maxWidth="600"
        height="auto"
        width="100%"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
        <!-- style="z-index:1001" -->
        <modal-wrapper :name="$options.name" color="white">
               <template slot="header">
                <theme-button-close
                    class="btn-close-header"
                    @click.native="closeModal"
                >
                    <i class="icon-close" >
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
              </template>
            <template slot="title">
                {{ role.id ?  $t('edit_role') : $t('new_role') }}
            </template>

            <template slot="body">
                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('role_name') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="display_name">
                        <input type="text" class="form-control" id="role-name-input" maxlength="50" v-model="roleDisplayName">
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-4 col-sm-3 col-form-label">{{ $t('description') }}</label>

                    <validation class="col-xs-12 col-sm-9" :validator="validator" label="description">
                        <textarea
                            v-model="roleDescription"
                            class="form-control"
                            id="role-description-input"
                        ></textarea>
                    </validation>
                </div>

                <div class="form-group row" v-for="(group, group_index) in availablePermissions" :key="group_index">
                    <div class="col-xs-12 permission_group_title">
                        {{ group.name }}
                    </div>
                    <div class="col-xs-12 form-group margin-0" v-for="(permission, permission_index) in group.permissions" :key="permission_index">
                        <div class="col-xs-8 permission-label">
                            <label>{{ permission.label }}</label>
                        </div>
                        <div class="col-xs-4 permission-checkbox">
                            <div class="checkbox-wrapper" style="display: inline-block;margin-top: 0;">
                                <input type="checkbox"
                                       :value="permission.value"
                                       v-model="permissions"
                                       @click.stop>
                                <span class="checkbox checkbox_theme_blue">
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <button type="button" class="btn btn-remove" @click="handleDelete" :disabled="isLoading" v-if="role.id">
                    <i class="icon-trash" aria-hidden="true">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-trash">
                            </use>
                        </svg>
                    </i>
                </button>

                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleUpdate" v-if="role.id">
                    {{ $t('save') }}
                </theme-button-success>

                <theme-button-success type="submit" class="btn" :disabled="isLoading" @click.native="handleCreate" v-if="!role.id">
                    {{ $t('create') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from 'vuex'
    import MixinForm            from '@mixins/form'

    import ContentLoading       from "@views/components/ContentLoading";
    import Validation           from "@views/components/Validation";
    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "role-setting-modal",
		components: {
			ModalWrapper,
			Validation,
			ContentLoading,
			ThemeButtonSuccess,
			ThemeButtonClose
		},
		mixins: [
			MixinForm
		],
		data: function () {
            return {
                canCloseWithoutAccept: null,
                form: {
                    role_id: null,
                    display_name: null,
                    description: null,
                    callback: null,
                    permissions: [],
                },
                initForm: {
                    role_id: null,
                    display_name: null,
                    description: null,
                    callback: null,
                    permissions: [],
                },
                fetchPermissions: false
            }
        },
		computed:{
			availablePermissions() {
			    return [
					{
						name: this.$t('task'),
						permissions: [ {
							label: this.$t('create_a_new_task'),
							value: 'create-task'
						}, {
							label: this.$t('read_tasks_from_others'),
							value: 'read-task'
						}, {
							label: this.$t('update_the_task_from_others'),
							value: 'update-task'
						}, {
							label: this.$t('delete_the_task_from_others'),
							value: 'delete-task'
						}, {
							'label': this.$t('add_assignees_to_the_task'),
							'value': 'add-assignees-task'
						}, {
							'label': this.$t('delete_assignees_from_the_task'),
							'value': 'delete-assignees-task'
						}
						]
					}, {
						name: this.$t('board'),
						permissions: [{
							label: this.$t('create_a_new_board'),
							value: 'create-board'
						}, {
							label: this.$t('update_other_boards'),
							value: 'update-board'
						}, {
							label: this.$t('delete_other_boards'),
							value: 'delete-board'
						}]
					}, {
						name: this.$t('group'),
						permissions: [{
							label: this.$t('update_the_group'),
							value: 'update-group'
						}, {
							label: this.$t('delete_the_group'),
							value: 'delete-group'
						}, {
							label: this.$t('mange_group_members'),
							value: 'manage-group-members'
						}]
					}, {
						name: this.$t('tracker'),
						permissions: [{
							label: this.$t('time_tracking'),
							value: 'time-tracking'
						}, {
							label: this.$t('read_other_comments'),
							value: 'read-other-comments'
						}, {
							label: this.$t('read_other_time_logs'),
							value: 'read-other-time-logs'
						}, {
							label: this.$t('edit_delete_other_time_logs'),
							value: 'manage-other-time-logs'
						}]
					}
				]
            },
            ...mapGetters({
                customRoles:        'permissions/getCustomRoles',
                defaultCustomRoles: 'permissions/getDefaultCustomRoles',
                globalPermissions:  'permissions/getPermissions',
                allPermissions:     'permissions/getAllPermissions',
            }),
            role() {
                if (this.form.role_id) {
                    return this.customRoles.find(item => item.id === this.form.role_id) || this.form;
                }

                return this.form;
            },
            permissions: {
                get: function () {
                    if (this.form.role_id && !this.fetchPermissions) {
                        this.fetchPermissions = true;
                        this.form.permissions = this.allPermissions.filter((global) => {
                            return this.role.permissions.find(permissionId => permissionId === global.id)
                        }).map(permission => permission.name);

                        this.initForm.permissions = this.form.permissions;
                    }

                    return this.form.permissions
                },
                set: function (permission) {
                    this.form.permissions = permission;
                }
            },
            roleDisplayName: {
                get: function () {
                    if (!this.form.display_name) {
                        this.form.display_name = this.role.display_name;
                        this.initForm.display_name = this.role.display_name;
                    }

                    return this.form.display_name;
                },
                set: function (value) {
                    this.form.display_name = value;
                }
            },
            roleDescription: {
                get: function () {
                    if (!this.form.description) {
                        this.form.description = this.role.description;
                        this.initForm.description = this.role.description;
                    }

                    return this.form.description;
                },
                set: function (value) {
                    this.form.description = value;
                }
            }
        },
        methods: {
            beforeOpen(event) {
                this.$api.permissions.getGlobalRoles();
                this.$api.permissions.getRoles();

                if (!event.params) {
                    return;
                }

                if (event.params.role_id) {
                    this.form.role_id = event.params.role_id;
                    this.initForm.role_id = event.params.role_id;
                }

                if (event.params.callback) {
                    this.form.callback  = event.params.callback;
                    this.initForm.callback  = event.params.callback;
                }
            },
            beforeClose(event) {
                if (JSON.stringify(this.form) !== JSON.stringify(this.initForm) && !this.canCloseWithoutAccept) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('confirm_modal'),
						body: this.$t('are_you_sure_you_want_to_close_modal'),
                        confirmCallback: () => {
                            this.initForm = {...this.form};
                            this.closeModal();
                        }
                    });

                    return event.stop();
                }

                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('role-setting-modal')
            },
            handleCreate() {
                this.canCloseWithoutAccept = true;
                this.$api.role.createRole(this.form).then((data) => {
                    if (this.form.callback) {
                        this.form.callback(data.role);
                    }
                    this.initForm = {...this.form};

                    this.defaultResponse();
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleUpdate() {
                this.canCloseWithoutAccept = false;
                this.$api.role.updateRole(this.form).then(() => {
                    this.initForm = {...this.form};

                    this.defaultResponse();
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleDelete() {
                this.$api.role.removeRole(this.form.role_id).then(() => {
                    this.initForm = {...this.form};

                    this.defaultResponse();
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            }
        }
    }
</script>

<style lang="scss">
    @import "../../../sass/_variables.scss";

    #role-setting-modal {
        font-size: 14px;
        line-height: 1.6;
        overflow: hidden;

        .permission-label {
            padding-left: 40px;
            float: left;
            text-align: left;

            label{
                font-weight: 100;
            }
        }

        .permission_group_title{
            font-weight: 900;
            text-align: left;
        }

        .permission-checkbox {
            /*width: 50%;*/
            float: right;
            text-align: center;

            .form-check-input {
                height: 15px;
                width: 15px;
                cursor: pointer;
            }
        }

        .modal-body {
            input, select {
                border: 2px solid #e0e0e0;
                height: 40px;
                font-weight: 600;
                padding: 3px 5px;
                width: 100%;
            }
        }

        .form-group{
            .col-form-label{
                text-align: left;
            }
        }

        .btn-info {
            background-color: $btn-close-color;
        }
        .btn-close-header {
            background: transparent;
            border:none;
            box-shadow: none;
            fill:#fff;
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
                &:hover {
                    background: transparent;
                    border:none;
                    box-shadow: none;
                    fill:#e2e6e9;
                }
            .icon-close {
                display: block;
                 .icon {
                     width: 14px;
                     height: 14px;
                 }
            }
        }
    }
</style>
