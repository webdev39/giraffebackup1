<template>
    <div id="manage-page" class="manage-users">

        <!-- Header -->
        <div class="manage-customers-navbar-header">
            <div class="manage-customers-navbar-title flex-1">
                {{ $t("manage_users") }}
            </div>
            <div class="button-holder">
                <theme-button-success class="btn btn-xs" @click.native="showInactiveUsers = !showInactiveUsers">
                    <i :class="[showInactiveUsers ? 'icon-eye-slash' : 'icon-eye']">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                           <use xmlns:xlink="http://www.w3.org/1999/xlink"
                           :xlink:href="[showInactiveUsers ? '#icon-eye-slash' : '#icon-eye']">
                           </use>
                        </svg>
                    </i>
                    {{ showInactiveUsers ? $t("hide_inactive_user") : $t("show_inactive_user") }}
                </theme-button-success>

                <theme-button-success class="button__icon new-client-btn" @click.native="showInviteUserModal" v-if="canInvite">
                    <i class="icon-plus">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-plus">
                            </use>
                        </svg>
                    </i>
                </theme-button-success>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <theme-manage-container class="table">
                <div class="table-head">
                    <div class="col">{{ $t("id") }}</div>
                    <div class="col">{{ $t("name") }}</div>
                    <div class="col">{{ $t("email") }}</div>
                    <div class="col text-center">{{ $t("status") }}</div>
                    <div class="col text-right">{{ $t("groups") }}</div>
                    <div class="col">{{ $t("last_activity") }}</div>
                    <div class="col action">{{ $t("actions") }}</div>
                </div>

                <template v-for="(members, company) in groupMembers">
                    <div class="table-row">
                        <td class="col" colspan="7"><h4><b>{{ company }}</b></h4></td>
                    </div>
                    <div class="table-row" v-for="(member, index) in members" :key="member.id">
                        <div class="col">{{ index + 1 }}</div>
                        <div class="col">{{ member | fullName }}</div>
                        <div class="col"><a :href="'mailto:' + member.user.email">{{ member.user.email }}</a></div>
                        <div class="col text-center">{{ getUserStatus(member) }}</div>
                        <div class="col text-right">
                            <span class="link" v-on:click="selectedMember(member)">
                                {{ member.group_role.length }}
                            </span>
                        </div>
                        <div class="col">{{ member.user.last_activity | toLocalTime }}</div>
                        <div class="col action">
                            <a :title="$t('show_setting_invite_user')" @click="showInviteUserModal(member)" v-if="checkPermission('acp-access')">
                                <i class="icon-settings">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-settings">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                            <a :title="$t('using_another_member')" @click="loginUsingMemberId(member)" v-if="isTenant">
                                 <i class="icon-user-secret">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-user-secret">
                                        </use>
                                    </svg>
                                </i>
                            </a>
                        </div>
                    </div>
                </template>
            </theme-manage-container>
        </div>
    </div>
</template>

<script>
    import { mapGetters }       from 'vuex'
    import config               from '@config'

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        data() {
            return {
                showInactiveUsers: false,
            };
        },
        components: {
            ThemeButtonSuccess,
            ThemeManageContainer,
        },
        computed:{
            ...mapGetters({
                getMembers:     'members/getOnlyMembers',
                getOwner:       'members/getOwner',
                userStatuses:   'default/getUserStatuses',
            }),
            canInvite() {
                return this.isTenant || this.checkPermission('can-invite-others');
            },
            groupMembers() {
                const members = this.getMembers.filter((member) => {
                    if (this.showInactiveUsers) {
                        return true;
                    }

                    return member.user.is_confirmed && member.user.status === 1;
                });

                const group   = this.$lodash.groupBy(members, 'company_name');
                const ordered = {};

                ordered[this.getOwner.company_name] = group['null'];

                Object.keys(group).sort().forEach((key) => {
                    if (key !== 'null') {
                        ordered[key] = group[key];
                    }
                });

                return ordered;
            }
        },
        filters: {
            fullName(member) {
                return `${member.user.name} ${member.user.last_name}`
            },
        },
        mounted() {
            if (window.innerWidth < config.size.tablet) {
                return this.$router.push({name: 'home'});
            }

            if (!this.checkPermission('acp-access', true)) {
                return this.$router.push({name: 'home'});
            }
            this.$api.permissions.getRoles();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            getUserStatus(member) {
                if (member.user.status !== 1) {
                    return this.userStatuses[member.user.status];
                } else if (member.user.last_activity) {
                    return this.$t('active')
                } else if (member.user.is_confirmed) {
                    return this.$t('confirmed')
                } else {
                    return this.$t('invited');
                }
            },
            selectedMember(member) {
                this.$modal.show('member-setting-modal', {memberId: member.id})
            },
            showInviteUserModal(member) {
                this.$modal.show('invite-user-modal', {member_id: member.id})
            },
            loginUsingMemberId(member) {
                this.$api.auth.loginUsingMemberId(member.id).then(() => {
                    // location.reload();
                    this.$router.push({name: 'home'});
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            }
        },
    }
</script>
