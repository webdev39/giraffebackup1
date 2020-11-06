<!--Optimized-->
<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        :maxWidth="700"
        height="auto"
        width="100%"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
        <!-- style="z-index:1000" -->
        <modal-wrapper :name="$options.name">
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
                {{ form.member_id ? $t('user_setting') : $t('invite_user') }}
            </template>

            <template slot="body">
                <div class="row">
                    <div class="col-sm-12">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label bodered-label">
                                    {{ $t('email') }}
                                </label>

                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="email">
                                    <input type="email" class="form-control font-additional-info" id="email" v-model="form.email">
                                </validation>
                            </div>

                            <div class="form-group">
                                <label for="firstName" class="col-sm-3 control-label bodered-label">
                                    {{ $t('first_name') }}
                                </label>

                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="name">
                                    <input type="text" class="form-control font-additional-info" id="firstName" v-model="form.name">
                                </validation>
                            </div>

                            <div class="form-group">
                                <label for="lastName" class="col-sm-3 control-label bodered-label">
                                    {{ $t('last_name') }}
                                </label>

                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="last_name">
                                    <input type="text" class="form-control font-additional-info" id="lastName" v-model="form.last_name">
                                </validation>
                            </div>

                            <div class="form-group">
                                <label for="company" class="col-sm-3 control-label bodered-label">
                                    {{ $t('company_name') }}
                                </label>

                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="company_name">
                                    <input type="text" class="form-control font-additional-info" id="company" v-model="form.company_name">
                                </validation>
                            </div>

                            <div class="form-group" v-if="form.id">
                                <label for="password" class="col-sm-3 control-label bodered-label">
                                    {{ $t('password') }}
                                </label>

                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="password">
                                    <input type="password" class="form-control" id="password" v-model="form.password" placeholder="New password">
                                </validation>
                            </div>

                            <div class="form-group" v-if="form.id">
                                <label for="status" class="col-sm-3 control-label bodered-label">{{ $t('user_statues') }}</label>
                                <validation class="col-xs-12 col-sm-9" :validator="validator" label="status">
                                    <select class="form-control font-additional-info" id="status" v-model="form.status" style="text-transform: capitalize;">
                                        <option v-for="(status, key) in userStatuses" :value="key" :key="key">
                                            {{ $t(status) }}
                                        </option>
                                    </select>
                                </validation>
                            </div>

                            <div class="form-group">
                                <label for="chooseGroup" class="col-sm-3 control-label bodered-label">{{ $t('select_global_role') }}</label>
                                <div class="col-xs-12 col-sm-9">
                                    <validation :validator="validator" label="global_role">
                                        <div class="form-check" v-for="(role, index) in globalRoles">
                                            <label class="checkbox-holder">
                                            <input  :value="role.id" class="form-check-input" type="checkbox" v-model="form.roles" :id="`global-role-`+index" />
                                            <span class="checkmark"></span>
                                            </label>
                                            <label
                                                :title="$t(`description_role.${role.name.replace(/-/g, '_')}`)"
                                                :for="`global-role-`+index"
                                                class="form-check-label"
                                                data-toggle="tooltip"
                                                data-placement="right"
                                            >
                                                {{role.display_name}}
                                            </label>
                                        </div>

                                        <!--<select class="form-control font-additional-info" id="chooseGlobalRole" v-model="form.global_role">
                                            <option :value="null">{{ $t('please_select_global_role') }}</option>

                                            <option v-for="(role, index) in globalRoles" :value="role.name" :key="index">
                                                {{ role | toRoleDescription }}
                                            </option>
                                        </select>-->
                                    </validation>
                                    <!--<input type="checkbox" id="user_can_invited" v-model="form.can_invited">
                                    <label for="user_can_invited">{{ $t('user_can_invited') }}</label>-->
                                </div>
                            </div>

                            <div class="form-group" v-for="(item, index) in groupRoles" :key="index">
                                <label for="chooseGroup" class="col-sm-2 control-label bodered-label">
                                    {{index + 1}}. {{ $t('group') }}
                                </label>
                                <div class="col-xs-12 col-sm-4 col-lg-4 form-control-inline_sm">
                                    <input
                                            type="text"
                                            class="form-control group-role-item font-additional-info"
                                            :value="item.group.name"
                                            readonly
                                    >
                                </div>

                                <div class="col-xs-12 col-sm-4 col-lg-4 form-control-inline_sm">
                                    <select
                                            class="form-control font-additional-info"
                                            v-model="item.role.id"
                                    >
                                        <option
                                                v-for="(customRole, index) in customRoles"
                                                :value="customRole.id"
                                                :key="index"
                                        >
                                            {{ customRole.display_name }}
                                        </option>
                                    </select>
                                    <!--<input type="text" class="form-control group-role-item font-additional-info" :value="item.role.name" readonly>-->
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <button
                                            class="btn-delete-group-role-item"
                                            @click.prevent="deleteGroupRoles(index)"
                                    >
                                        <i class="icon-trash">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     xlink:href="#icon-trash">
                                                </use>
                                            </svg>
                                        </i>
                                    </button>
                                </div>
                                <div class="form-group-border"></div>
                            </div>

                            <div class="form-group choose-group-role-section">
                                <label for="chooseGroup" class="col-sm-2 control-label bodered-label">
                                    {{ groupRoles.length + 1 }}. {{ $t('group') }}
                                </label>
                                <div class="col-xs-12 col-sm-4 form-control-inline_sm">
                                    <select
                                            class="form-control font-additional-info"
                                            id="chooseGroup"
                                            v-model="selectGroup"
                                            @change="changeGroup"
                                    >
                                        <option :value="null">{{ $t('please_select_group') }}</option>
                                        <option
                                                v-for="(group, index) in groups"
                                                :value="group.id"
                                                :key="index"
                                                v-if="!groupRoles.find(item => item.group.id === group.id)">
                                            {{ group.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-xs-12 col-sm-4">
                                    <select class="form-control font-additional-info" id="chooseRole" v-model="selectRole"  @change="changeRole">
                                        <option :value="null">{{ $t('please_select_role') }}</option>

                                        <option v-for="(customRole, index) in customRoles" :value="customRole.id" :key="index">
                                            {{ customRole.display_name }}
                                        </option>

                                        <option :value="0">+ {{ $t('new_role') }}</option>
                                    </select>
                                </div>
                            </div>



                            <div class="form-group" v-if="!form.id">
                                <div class="col-xs-12 col-sm-9 col-sm-push-3">
                                    <label class="checkbox-holder">
                                    <input type="checkbox" id="without_verify" v-model="form.without_verify">
                                    <span class="checkmark"></span>
                                    </label>
                                    <label for="without_verify">{{ $t('activate_user_without_verify') }}</label>
                                </div>
                            </div>

                            <!--<div class="form-group">
                                <div class="col-xs-12 col-sm-9 col-sm-push-3">
                                    <input type="checkbox" id="user_can_invited" v-model="form.can_invited">
                                    <label for="user_can_invited">{{ $t('user_can_invited') }}</label>
                                </div>
                            </div>-->


                        </form>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success type="button" class="btn" @click.native="handleUpdate" :disabled="isLoading" v-if="form.id">
                    {{ $t('save') }}
                </theme-button-success>

                <theme-button-success type="button" class="btn" @click.native="handleCreate" :disabled="isLoading || buttonIsReady" v-if="!form.id">
                    {{ $t('send') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import formMixin            from '@mixins/form'
    import Validation           from '@views/components/Validation'
    import ModalWrapper         from "@assets/js/views/layouts/ModalWrapper";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";
    import ThemeButtonDangerous from "@views/layouts/theme/buttons/ThemeButtonDangerous";

    export default {
        name: "invite-user-modal",
        data() {
            return {
                form: {
                    email:          '',
                    name:           '',
                    last_name:      '',
                    company_name:   null,
                    password:       null,
                    global_role:    [],
                    roles:          [],
                    group_roles:    [],
                    without_verify: false,
                    can_invited:    false,
                    status:         null,
                    tenant_id:      null,
                    member_id:      null,
                },
                initForm:           {},
                selectGroup:        null,
                selectRole:         null,
                groupRoles:         [],
            }
        },
        computed: {
            ...mapGetters({
                groups:         'groups/getStateGroups',
                members:        'members/getOnlyMembers',
                owner:          'members/getOwner',
                globalRoles:    'permissions/getGlobalRoles',
                defaultRole:    'permissions/getDefaultRole',
                customRoles:    'permissions/getCustomRoles',
                tenantId:       'user/getTenantId',
                userStatuses:   'default/getUserStatuses',
            }),
            buttonIsReady() {
                return this.groupRoles.length < 1;
            },
        },
        components: {
            ModalWrapper,
            Validation,
            ThemeButtonSuccess,
            ThemeButtonClose,
            ThemeButtonDangerous
        },
        mixins: [
            formMixin
        ],
        methods: {
            beforeOpen(event) {
                this.$api.permissions.getRoles();
                this.$api.permissions.getGlobalRoles();

                if (!event.params) {
                    this.initForm = {...this.form};
                    return;
                }

                if (event.params.group && event.params.group.id) {

                    this.groupRoles.push({
                        group: event.params.group,
                        role: {
                            id:   this.defaultRole.id,
                            name: this.defaultRole.display_name
                        }
                    })
                }

                if (event.params.member_id) {
                    this.form.member_id = event.params.member_id;
                    this.setFormData();
                } else {
                    this.form.company_name = this.owner.company_name;
                }

                this.initForm = {...this.form};
            },
            beforeClose(event) {
                if (JSON.stringify(this.form) !== JSON.stringify(this.initForm)) {
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
                this.$modal.hide('invite-user-modal');
            },
            setFormData() {
                let member = this.members.find(item => item.id === this.form.member_id);

                if (member) {
                    this.form.id            = member.id;
                    this.form.email         = member.user.email;
                    this.form.name          = member.user.name;
                    this.form.last_name     = member.user.last_name;
                    this.form.status        = member.user.status;
                    this.form.can_invited   = member.user.can_invited;
                    this.form.tenant_id     = member.tenant_id;
                    this.form.company_name  = member.company_name || this.owner.company_name;

                    if (member.roles && member.roles.length) {
                        member.roles.forEach(item => this.form.roles.push(item.id))
                    }

                    this.groupRoles = member.group_role.map(item => {
                        return this.findGroupRoles(item.group_id, item.role_id)
                    }).filter((item) => {
                        return item !== null;
                    });
                }
            },
            changeRole() {
                if (this.selectRole === 0) {
                    this.$modal.show('role-setting-modal', {callback: (role) => {
                        this.$modal.hide('role-setting-modal');

                        this.selectRole = role.id;
                        this.triggerChoose();
                    }});

                    return false;
                }

                this.triggerChoose();
            },
            changeGroup(){
                this.triggerChoose();
            },
            triggerChoose(){
                if (this.$lodash.isNumber(this.selectGroup) && this.$lodash.isNumber(this.selectRole)) {
                    if (this.selectGroup >= 0 && this.selectRole >= 0) {
                        const result = this.findGroupRoles(this.selectGroup, this.selectRole);

                        if (result) {
                            this.groupRoles.push(result);
                        }

                        this.selectGroup = null;
                        this.selectRole  = null;
                    }
                }
            },
            findGroupRoles(groupId, roleId) {
                let group = this.groups.find(group => group.id === groupId);
                let role  = this.customRoles.find(role => role.id === roleId);

                if (!group || !role) {
                    return null;
                }

                return {
                    group: {
                        id:     group.id,
                        name:   group.name,
                    },
                    role: {
                        id:     role.id,
                        name:   role.display_name,
                    }
                };
            },
            deleteGroupRoles(index) {
                this.groupRoles.splice(index, 1);
            },
            handleCreate() {
                this.form.tenant_id = this.tenantId;
                this.form.group_roles = this.groupRoles.map(item => {
                    return {
                        group_id: item.group.id,
                        role_id:  item.role.id,
                    };
                });

                this.$api.auth.invite(this.form).then(() => {
                    this.initForm = {...this.form};

                    this.closeModal();
                    this.defaultResponse();
                }).catch((err) => {
                    this.defaultError(err.response)
                });
            },
            handleUpdate() {
                let form = Object.assign({}, this.form, {
                    group_roles: this.groupRoles.map(item => {
                        return {
                            group_id: item.group.id,
                            role_id:  item.role.id,
                        };
                    })
                });

                if (form.company_name === this.owner.company_name) {
                    form.company_name = null;
                }

                this.$api.members.updateMember(form).then(() => {
                    this.initForm = {...this.form};

                    this.closeModal();
                    this.defaultResponse();
                }).catch((err) => {
                    this.defaultError(err.response)
                });
            }
        }
    }
</script>

<style lang="scss">
    #invite-user-modal {
        input, input:focus,
        select, select:focus {
            border: 1px solid #e0e0e0;
            font-weight: 600;
        }

        input[type=password] {
            height: 36px;
        }

        .font-additional-info {
            font-size: 16px;
            padding-bottom: 0;
            padding-top: 2px;
        }

              @media (max-width: 767px) {
                  .form-group-border{
                      display: block;
                      width: 100%;
                      margin: auto;
                      clear: both;
                      padding: 0 15px;
                      &:after{
                          content: '';
                          display: block;
                          height: 15px;
                          border-bottom: 2px solid #7d7d7d;
                      }
                  }
                /*.form-group:nth-child(6) {*/
                    /*.form-control-inline_sm:nth-child(3) {*/
                                /*&:after{*/
                                  /*content: '';*/
                                  /*display: block;*/
                                  /*width: 100%;*/
                                  /*margin: auto;*/
                                  /*height: 20px;*/
                                  /*border-bottom: 2px solid #7d7d7d;*/
                                /*}*/
                    /*}*/
                /*}*/
                /*.form-group:nth-child(7) {*/
                    /*.form-control:first-child {*/
                        /*margin-bottom: 10px;*/
                    /*}*/
                /*}*/
              }

              .form-group:nth-child(8) {
                  margin-top: 20px;
              }
    }
</style>
<style lang="scss">
#invite-user-modal {
    overflow: hidden;
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

    /* The checkbox-holder */
.checkbox-holder {
  display: inline-block;
  position: relative;
  width: 17px;
  height: 17px;
  margin-right: 10px;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.checkbox-holder input {
  position: relative;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 3px;
  left: 0;
  height: 17px;
  width: 17px;
  background-color: #eee;
  border: 1px solid #dadada;
  border-radius: 2px;
  transition: all .3s;
}

/* On mouse-over, add a grey background color */
.checkbox-holder:hover input ~ .checkmark {
  background-color: #dadada;
  transition: all .3s;
}

/* When the checkbox is checked, add a blue background */
.checkbox-holder input:checked ~ .checkmark {
  background-color: #458fe6;
  border-color: #458fe6;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.checkbox-holder input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.checkbox-holder .checkmark:after {
  left: 5px;
  top: 2px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
  transition: all .3s;
}
.validation-container {
    padding-top: 10px;
}
}

</style>
