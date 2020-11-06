<!--Optimized-->
<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="90%" :maxWidth="700" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose">
        <modal-wrapper :name="$options.name" v-if="member">
            <template slot="title">
                {{ $t('manage_users') }}
            </template>

            <template slot="body">
                <h4 class="member-setting-modal-title">{{ $t('global_roles') }}</h4>
                <div v-for="role in member.roles">
                    {{ role | toRoleDescription }}
                </div>
                <table class="member-setting-modal-table">
                    <thead>
                        <tr>
                            <th>{{ $t('group_name') }}</th>
                            <th>{{ $t('group_roles') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(role, index) in member.group_role">
                            <td>
                                <div class="">
                                    {{role.group.name}}
                                </div>
                            </td>
                            <td>
                                <div class="">
                                    {{role.display_name}}
                                </div>
                            </td>
                        </tr>
                    </tbody>

                </table>
                <!--<div class="" v-for="(role, index) in member.group_role">
                    <div class="">
                        {{role.group.name}}
                    </div>
                    <div class="">
                        {{role.display_name}}
                    </div>
                </div>-->
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import find                 from '@helpers/findInGroups'
    import ModalWrapper from "@assets/js/views/layouts/ModalWrapper";

    export default {
        name: "member-setting-modal",
        data() {
            return {
                memberId: null,
            }
        },
        computed:{
            ...mapGetters({
                groups:         'groups/getStateGroups',
                members:        'members/getOnlyMembers',
                globalRoles:    'permissions/getGlobalRoles',
                customRoles:    'permissions/getCustomRoles',
            }),
            member() {
                if (this.memberId) {
                    return this.setCustomRole({...this.members.find(member => member.id === this.memberId)});
                }

                return null;
            },
        },
        components: {
            ModalWrapper
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.memberId) {
                    this.memberId = event.params.memberId;
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            showBoardSettingsModal(board) {
                this.$modal.show('board-setting-modal', {boardId: board.id})
            },
            setCustomRole(member) {
                member.group_role = member.group_role.map(groupRole => {
                    let customRole = this.customRoles.find(item => item.id === groupRole.role_id);
                    let group = find.searchGroupById(this.groups, groupRole.group_id);

                    return Object.assign(groupRole, customRole, {group: group})
                });

                return member;
            },
            changeMemberRole(event, member, group) {
                let role = this.customRoles.find(role => role.id === parseInt(event.target.value));

                if (role) {
                    this.$api.members.attachMemberRoleGroup({
                        group_id:       group.id,
                        role_id:        role.id,
                        user_tenant_id: member.id,
                    }).catch((err) => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                } else {
                    this.$modal.show('role-setting-modal', {callback: (role) => {
                        this.$modal.hide('role-setting-modal');

                        this.$api.members.attachMemberRoleGroup({
                            group_id:       group.id,
                            role_id:        role.id,
                            user_tenant_id: member.id,
                        }).catch((err) => {
                            this.$notify({type:'error', text: err.response.data.message});
                        });
                    }});
                }
            },
            handleAttachGlobalRole(event, member) {
                let role = this.globalRoles.find(role => role.id === parseInt(event.target.value));

                this.$api.members.attachMemberGlobalRole({
                    role_id:        role.id,
                    member_id:      member.id,
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            }
        }
    }
</script>

<style lang="scss">
    .member-setting-modal-title{
        font-size: 20px;
        color: #636b6f;
        font-weight: 600;
        margin: 15px 0;
    }
    .member-setting-modal-table{
        width: 100%;
        margin-top: 15px;
        thead{
            tr{
                background-color: #eaeaea;
                border-bottom: 1px solid #fff;
            }
        }
        tbody{
            tr{
                background-color: aliceblue;
                padding: 10px;
                border-bottom: 1px solid #fff;
            }
        }
        td,th{
            padding: 10px;
        }

    }
</style>
