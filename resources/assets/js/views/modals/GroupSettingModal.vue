<!-- Todo need Optimization -->
<template>
    <modal
            :name="$options.name"
            :id="$options.name"
            :maxWidth="700"
            :pivotY="0.2"
            :adaptive="true"
            :scrollable="true"
            height="auto"
            width="100%"
            @before-open="beforeOpen"
            @before-close="beforeClose">
        <modal-wrapper :name="$options.name">
            <template slot="header">
                <theme-button-close
                        class="btn-close-header"
                        @click.native="closeModal"
                >
                    <i class="icon-close">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
            </template>
            <template slot="title">
                {{ $t('title_setting_group') }} {{ group.name }}
            </template>


            <template slot="body">
                <div class="row">
                    <!-- <label class="col-xs-9 col-form-label boldAndLarger">
                        {{ $t('general') }}
                    </label> -->
                </div>

                <div class="form-group row">
                    <label class="col-xs-12 col-form-label notBold secColor">
                        {{ $t('group_name') }}
                    </label>

                    <validation
                            data-v-step="group_2"
                            class="col-xs-12"
                            :validator="validator"
                            label="name"
                    >
                        <QuillTag class="form-control"
                                  ref="quill-name"
                                  :is-input="true"
                                  :content="groupName">
                        </QuillTag>
                    </validation>
                </div>

                <div class="form-group row">
                    <label class="col-xs-12 col-form-label notBold secColor" for="group-description-input">
                        {{ $t('description') }}
                    </label>

                    <validation class="col-xs-12" :validator="validator" label="description">
                        <QuillTag ref="quill-description"
                                  :content="groupDescription">
                        </QuillTag>
                    </validation>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="col-form-label boldAndLarger">
                            {{ $t('members') }} ( {{ (internalMembersLength) }} )
                        </label>
                    </div>

                    <!--<div class="col-md-3" >
                        <button type="button" class="btn btn-outline-danger clone-group-btn" role="button" @click="handleCloneGroup" :disabled="isLoading">
                            {{ $t('clone_group') }}
                        </button>
                    </div>-->

                    <!--<div class="col-md-3" v-if="isTenant || canInvited">
                        <button type="button" class="btn btn-primary clone-group-btn pull-right" role="button" @click.prevent="showModalInviteUser">
                            {{ $t('invite_user') }}
                        </button>
                    </div>-->
                </div>

                <div class="members-group-list">
                    <div
                            v-for="member in internalMembers"
                            :key="member.id"
                            class="members-group-item flx clearfix"
                    >
                        <div class="col-md-6 col-sm-6 col-xs-6 flx-h-center">
                            <div>
                                <i class="icon-user">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#icon-user">
                                        </use>
                                    </svg>
                                </i>
                                <!-- <i class="oc icon-user" aria-hidden="true"></i>  -->{{ member | fullName }}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="member-role-item-wrapper">
                                <div v-if="group_role.group_id === group.id"
                                     v-for="group_role in member.group_role"
                                     :key="group_role.role_id"
                                     :value="group_role.role_id"
                                     class="member-role-item">
                                <span
                                        v-if="selectMemberRoleId !== member.id && memberIdForChangeRole !== member.id"
                                        class="member-role-name">
                                    <template v-if="!member.is_owner">
                                        {{ group_role.display_name }}
                                    </template>
                                    <template v-else>
                                        {{ $t('group_admin_roleName') }}
                                    </template>
                                </span>
                                    <select
                                            v-else
                                            class="newMemberRole group-settings-select-role"
                                            :disabled="memberIdForChangeRole === member.id"
                                            :value="group_role.role_id"
                                            @change="changeMemberRole($event, member)">
                                        <option
                                                v-for="customRole in customRoles"
                                                :key="customRole.id"
                                                :value="customRole.id">
                                            {{ customRole.display_name }}
                                        </option>
                                        <option value="0">+ {{ $t('new_role') }}</option>
                                    </select>
                                    <content-loading
                                            class="member-role-item-loading"
                                            :absolute="styleLoading.absolute"
                                            :autosize="styleLoading.autosize"
                                            :loading="memberIdForChangeRole === member.id"/>
                                </div>
                                <button type="button" class="btn button-setting group-setting-member-setting"
                                        :title="$t('member_role')"
                                        v-if="isHasManageGroupMembersPermission && !member.is_owner"
                                        @click="toggleToSelect(member)">
                                    <i class="icon-settings">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#icon-settings">
                                            </use>
                                        </svg>
                                    </i>
                                </button>

                                <div v-if="isHasManageGroupMembersPermission" class="group-settings-trash secColor">
                                    <button type="button" class="btn" :disabled="isLoading"
                                            @click="handleDetachMemberToGroup(member)">
                                        <!-- <i class="fa fa-trash bordered" aria-hidden="true"></i> -->
                                        <i class="icon-trash">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     xlink:href="#icon-trash">
                                                </use>
                                            </svg>
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <!--<div v-if="member.is_owner" style="display: flex">-->
                            <!--<span class="memberrolename">{{ $t('group_admin_roleName') }}</span>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-xs-12 group-member-list">
                        <div class="group-member-controls">
                            <theme-button-success
                                    v-if="isTenant || isHasManageGroupMembersPermission"
                                    @click.native="handleToggleShowMembers"
                                    v-html="$t('add_user')"
                                    data-v-step="group_3"
                                    class="btn clone-group-btn"
                            />
                            <theme-button-success
                                    v-if="isCanInvite"
                                    class="btn clone-group-btn"
                                    @click.prevent.native="showModalInviteUser"
                                    v-html="$t('invite_user')"
                            />
                        </div>

                        <div class="group-member-multiselect" v-if="externalMembers.length && showSelectMembers">
                            <div class="group-member-multiselect-inner">
                                <multiselect
                                    v-model="selectMembers"
                                    :options="externalMembers"
                                    :searchable="false"
                                    :internal-search="false"
                                    :clear-on-select="false"
                                    :show-no-results="false"
                                    :close-on-select="false"
                                    :hide-selected="true"
                                    :limit="2"
                                    :limit-text="limitText"
                                    :custom-label="fullName"
                                    :openDirection="'bottom'"
                                    track-by="id"
                                    :disabled="isLoading"
                                    @input="handleAttachMemberToGroup"
                                    :placeholder="$t('select_user_in_group_settings')"
                                    :selectLabel="$t('press_enter_to_select')"
                                >
                                    <template slot="option" slot-scope="props">
                                        <span class="mr-2">{{ props.option | fullName }}</span>
                                    </template>
                                </multiselect>
                            </div>
                            <!--<div class="group-member-multiselect-add" v-if="selectMembers.length" >-->
                            <!--<theme-button-success class="btn" id="attach-members-button" @click.native="handleAttachMemberToGroup">-->
                            <!--{{ $t('add') }}-->
                            <!--</theme-button-success>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>

                <div class="row" v-if="activeBoards.length > 0">
                    <div class="col-lg-12">
                        <table class="table text-center table-group-st" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                <th style="max-width: 70px;">
                                    <label class=" col-form-label boards-title">
                                        {{ $t('boards') }}
                                    </label>
                                </th>
                                <th class="text-center secColor">
                                    <span class="notBold">
                                        {{ $t('tasks') }}
                                    </span>
                                    <span class="notBold">
                                        {{ $t('open_closed_tasks') }}
                                    </span>
                                </th>
                                <th class="secColor" v-if="checkPermission('time-tracking')">
                                    <span class="notBold">
                                        {{ $t('time') }}
                                    </span>
                                    <span class="notBold">
                                        {{ $t('logged_billed_hours') }}
                                    </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="board in activeBoards" :key="board.id">
                                <td>{{ board.name }}</td>
                                <td>{{ getBoardTaskCount(board.tasks) }}</td>
                                <td v-if="checkPermission('time-tracking')">
                                    <div class="flex">
                                        <span>
                                            <span class="nowrap">{{ board.trackedTime | minutesToHours }}</span>
                                            /
                                            {{ board.billedTime | minutesToHours }}
                                        </span>
                                        <button type="button" class="btn button-setting"
                                                :title="$t('title_setting_board')" @click="showBoardSettings(board)">
                                            <i class="icon-settings">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#icon-settings">
                                                    </use>
                                                </svg>
                                            </i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="archiveBoards.length" class="row">
                    <div class="col-xs-12 secColor">
                    <span class="show-archived-boards" @click="isShowArchivedBoards = !isShowArchivedBoards">
                        {{ archiveBoards.length }} {{ $t('archive_boards') }}
                    </span>
                    </div>
                </div>

                <div class="row" v-if="archiveBoards.length > 0 && isShowArchivedBoards">
                    <div class="col-lg-12">
                        <table class="table text-center table-group-st" style="table-layout: fixed;">
                            <thead>
                            <tr>
                                <th style="max-width: 70px;">
                                    <label class=" col-form-label boards-title">
                                        {{ $t('boards') }}
                                    </label>
                                </th>
                                <th class="text-center secColor">
                                    <span class="notBold">
                                        {{ $t('tasks') }}
                                    </span>
                                    <span class="notBold">
                                        {{ $t('open_closed_tasks') }}
                                    </span>
                                </th>
                                <th class="secColor" v-if="checkPermission('time-tracking')">
                                    <span class="notBold">
                                        {{ $t('time') }}
                                    </span>
                                    <span class="notBold">
                                         {{ $t('logged_billed_hours') }}
                                    </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="board in archiveBoards" :key="board.id">
                                <td>{{ board.name }}</td>
                                <td>{{ getBoardTaskCount(board.tasks) }}</td>
                                <td v-if="checkPermission('time-tracking')">
                                    <div class="flex">
                                        <span>
                                            <span class="nowrap">{{ board.trackedTime | minutesToHours }}</span>
                                            /
                                            {{ board.billedTime | minutesToHours }}
                                        </span>
                                        <button type="button" class="btn button-setting"
                                                @click="showBoardSettings(board)">
                                            <i class="icon-settings">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#icon-settings">
                                                    </use>
                                                </svg>
                                            </i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template slot="footer">
                <theme-button-dangerous
                        v-if="!this.group.is_archive && isCanDeleteOrArchive"
                        :disabled="isLoading"
                        type="button"
                        class="btn btn-archived"
                        @click.native="handleArchivedGroup(group)"
                >
                    <span>
                       {{ $t('archivate') }}
                    </span>
                </theme-button-dangerous>

                <theme-button-dangerous
                        v-if="this.group.is_archive && isCanDeleteOrArchive"
                        :disabled="isLoading"
                        type="button"
                        class="btn btn-archived"
                        @click.native="handleUnarchivedGroup(group)"
                >
                    <span>{{ $t('un_archive') }}</span>
                    <i class="fa fa-archive" aria-hidden="true"></i>
                </theme-button-dangerous>

                <button
                        v-if="isCanDeleteOrArchive"
                        :title="$t('title_remove_group')"
                        :disabled="isLoading"
                        type="button"
                        class="btn btn-remove"
                        @click="handleRemoveGroup"
                >
                    <i class="icon-trash">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-trash">
                            </use>
                        </svg>
                    </i>
                </button>

                <theme-button-close
                        type="button"
                        class="btn"
                        @click.native="closeModal"
                >
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success
                        v-if="group.id && isHasUpdateGroupPermission"
                        :disabled="isLoading"
                        type="button"
                        class="btn"
                        @click.native="handleUpdateGroup"
                >
                    {{ $t('save') }}
                </theme-button-success>

                <theme-button-success
                        v-if="!group.id"
                        :disabled="isLoading"
                        type="button"
                        class="btn"
                        @click.native="handleCreateGroup"
                        data-v-step="group_4"
                >
                    {{ $t('create') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
	import {mapGetters} from 'vuex'
	import clickOutside from 'v-click-outside'

	import MixinForm from '@mixins/form'
	import find from '@helpers/findInGroups'
	import ContentLoading from "@views/components/ContentLoading";
	import Validation from "@views/components/Validation";
	import ModalWrapper from "@views/layouts/ModalWrapper";
	import QuillTag from "@views/partcials/QuillTag/QuillTag";

	import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";
	import ThemeButtonClose from "@views/layouts/theme/buttons/ThemeButtonClose";
	import ThemeButtonDangerous from "@views/layouts/theme/buttons/ThemeButtonDangerous";

	export default {
		name: "group-setting-modal",
		data() {
			return {
				isShowArchivedBoards: false,
				selectMemberRoleId: null,
				selectMembers: [],
				showSelectMembers: false,
				draft: {
					attachMembers: [],
					selectMembers: []
				},
				form: {
					group_id: 0,
					name: null,
					description: null,
				},
				initForm: {
					group_id: 0,
					name: null,
					description: null,
				},
				styleLoading: {
					absolute: true,
					autosize: true,
				},
				memberIdForChangeRole: null,
			}
		},
		computed: {
			...mapGetters({
				groups: 'groups/getStateGroups',
				getStateGroups: 'management/getStateGroups',
				members: 'members/getActiveMembers',
				onlyMembers: 'members/getOnlyMembers',
				customRoles: 'permissions/getCustomRoles',
				defaultRole: 'permissions/getDefaultRole',
				canInvited: 'user/canInvited',
				getUserId: 'user/getUserId',
				getCurrentTour: 'getCurrentTour',
			}),
			isCanInvite() {
				return this.isTenant || (this.checkPermission('can-invite-others') && (this.isPermissionManageGroupMembers || this.isPermissionReadJoinGroup));
			},
			isPermissionManageGroupMembers() {
				return this.handlePermissionByGroupId('manage-group-members', this.form.group_id);
			},
			isPermissionUpdateGroup() {
				return this.handlePermissionByGroupId('update-group', this.form.group_id);
			},
			isPermissionReadJoinGroup() {
				return this.checkPermission('read-all-groups');
			},
			isHasUpdateGroupPermission() {
				if (!this.group.id) {
					return true
				}

				if (this.isTenant) {
					return true
				}

				if (this.isPermissionUpdateGroup) {
					return true
				}

				return false
			},
			/**
			 * Permission for deleting and archive group
			 * @return {Boolean}
			 */
			isCanDeleteOrArchive() {
				if (!this.group.id) {
					return false
				}

				if (this.isTenant) {
					return true
				}

				if (this.isPermissionReadJoinGroup) {
					return true;
				}

				if (this.form.group_id) {
					return this.handlePermissionByGroupId('delete-group', this.form.group_id);
				}

				return true
			},
			isHasManageGroupMembersPermission() {
				if (this.isTenant) {
					return true
				}

				if (this.isPermissionReadJoinGroup) {
					return true;
				}

				if (this.form.group_id) {
					return this.handlePermissionByGroupId('manage-group-members', this.form.group_id);
				}

				return true
			},
			group() {
				if (this.form.group_id) {
					/* todo remove after optimization management (we are get all groups) */
					return find.searchGroupById(this.getStateGroups.length ? this.getStateGroups : this.groups, this.form.group_id) || this.form;
					/**/

					// return find.searchGroupById(this.groups, this.form.group_id) || this.form;
				}

				return this.form;
			},
			activeBoards() {
				if (Array.isArray(this.group.boards)) {
					return this.group.boards.filter(board => !board.is_archive).sort((a, b) => sorter(a.name, b.name));
				}

				return [];
			},
			archiveBoards() {
				if (Array.isArray(this.group.boards)) {
					return this.group.boards.filter(board => board.is_archive).sort((a, b) => sorter(a.name, b.name));
				}

				return [];
			},
			internalMembers() {
				let members = [];

				if (this.group && this.group.members) {
					members = this.members.filter(member => this.group.members.includes(member.id));
				} else {
					members = this.draft.attachMembers;
				}

				return this.setCustomRoleToMembers(members).sort((a, b) => {
					return sorter(a.user.nickname, b.user.nickname)
				});
			},
			internalMembersLength() {
				return this.internalMembers.length
			},
			externalMembers() {
				if (this.group.id && this.group.members) {
					return this.members.filter(member => {
						return !this.group.members.includes(member.id)
					}).sort((a, b) => sorter(a.user.nickname, b.user.nickname));
				}

				/*return this.onlyMembers.filter(member => {
                    if (this.draft.selectMembers.length > 0) {
                        return this.draft.selectMembers.find(draft => draft.id === member.id);
                    }

                    return true;
                });*/

				return this.onlyMembers.filter(member => {

					if (!member.user.status) {
						return false
					}

					if (this.draft.attachMembers.length > 0) {
						return !this.draft.attachMembers.some(draft => draft.id === member.id);
					}

					return true;
				});
			},
			groupName: {
				get: function () {
					if (this.form.name === null && this.group.name) {
						this.form.name = this.group.name;
						this.initForm.name = this.group.name;
					}

					return this.form.name
				},
				set: function (value) {
					this.form.name = value;
				}
			},
			groupDescription: {
				get: function () {
					if (this.form.description === null && this.group.description) {
						this.form.description = this.group.description;
						this.initForm.description = this.group.description;
					}

					return this.form.description
				},
				set: function (value) {
					this.form.description = value;
				}
			}
		},
		filters: {
			fullName(member) {
				return `${member.user.name} ${member.user.last_name}`
			},
		},
		components: {
			ModalWrapper,
			Validation,
			ContentLoading,
			ThemeButtonSuccess,
			ThemeButtonClose,
			ThemeButtonDangerous,
			QuillTag
		},
		directives: {
			'clickOutside': clickOutside.directive
		},
		mixins: [
			MixinForm
		],
		methods: {
			getTags() {
				this.$api.tag.getTags()
			},
			handleToggleShowMembers() {
				this.showSelectMembers = !this.showSelectMembers;
			},
			getBoardTaskCount(tasks) {
				let countDoneTasks = 0, countOpenTasks = 0;

				tasks.forEach(task => {
					if (!task.draft) {
						if (task.done_by) {
							countDoneTasks += 1;
						} else {
							countOpenTasks += 1;
						}
					}
				});

				return countOpenTasks + ' / ' + countDoneTasks;
			},
			beforeOpen(event) {
				this.$api.permissions.getGlobalRoles();
				this.$api.permissions.getRoles();

				this.getTags();

				if (!event.params) {
					return;
				}

				if (event.params.groupId) {
					this.form.group_id = event.params.groupId;

					this.$api.group.getGroupById(event.params.groupId).then((res) => {
						this.form.group_id = res.group.id;
						Object.assign(this.form, res.group);
						Object.assign(this.initForm, this.form);
					}).catch(err => {
						this.$notify({type: 'error', text: err.response.data.message});
					})
				}

				if (event.params.name) {
					this.form.name = event.params.name;
					this.initForm.name = event.params.name;
				}

				if (event.params.description) {
					this.form.description = event.params.description;
					this.initForm.description = event.params.description;
				}
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
			setQuillDescription() {
				this.form.description = this.$refs['quill-description'].getEditorContent();
			},
			setQuillName() {
				this.form.name = this.$refs['quill-name'].getEditorContent();
			},
			fullName(member) {
				return `${member.user.name} ${member.user.last_name}`
			},
			limitText(count) {
				return `and ${count} other members`
			},
			showModalInviteUser() {
				this.$modal.show('invite-user-modal', {group: {id: this.form.group_id, name: this.form.name}})
			},
			showBoardSettings(board) {
				this.$modal.show('board-setting-modal', {boardId: board.id})
			},
			closeModal() {
				this.$modal.hide('group-setting-modal')
			},
			toggleToSelect(member) {
				if (this.isHasManageGroupMembersPermission) {
					this.selectMemberRoleId = this.selectMemberRoleId === member.id ? null : member.id;
				}
			},
			hideSelectRole(event) {
				if (!event.target.classList.contains('memberrolename')) {
					this.selectMemberRoleId = null;
				}
			},
			setCustomRoleToMembers(members) {

				return members.map(member => {
					member.group_role.map(groupRole => {
						let customRole = this.customRoles.find(item => item.id === groupRole.role_id);

						if (customRole) {
							return Object.assign(groupRole, customRole);
						}

						return Object.assign(groupRole, {
							display_name: 'Undefined'
						})
					});

					return member
				});
			},
			changeDraftMemberRole(memberId, role) {
				this.draft.attachMembers.find(item => {
					if (item.id === memberId) {
						item.group_role = [{
							group_id: this.group.id,
							role_id: role.id,
						}];

						return true;
					}
				});

				this.selectMemberRoleId = null;
			},
			attachDraftMemberToGroup(membersIds) {
				this.members.find(item => {
					if (membersIds.includes(item.id)) {
						this.draft.attachMembers.push(Object.assign({}, item, {
							group_role: [{
								group_id: this.group.id,
								role_id: this.defaultRole.id,
							}]
						}))
					}
				});

				this.selectMembers = [];
			},
			changeMemberRole(event, member) {
				let role = this.customRoles.find(role => role.id === parseInt(event.target.value));

				if (!this.group.id) {
					return this.changeDraftMemberRole(member.id, {...role});
				}

				const group_id = this.group.id;
				const user_tenant_id = member.id;

				if (role) {
					const role_id = role.id;
					this.attachMemberRoleGroup(group_id, role_id, user_tenant_id);
				} else {
					this.$modal.show('role-setting-modal', {
						callback: (role) => {
							const role_id = role.id;
							this.$modal.hide('role-setting-modal');
							this.attachMemberRoleGroup(group_id, role_id, user_tenant_id);
						}
					});
				}
			},

			async attachMemberRoleGroup(group_id, role_id, user_tenant_id) {
				this.memberIdForChangeRole = user_tenant_id

				await this.$api.members.attachMemberRoleGroup({
					group_id,
					role_id,
					user_tenant_id,
				}).catch((err) => {
					this.$notify({type: 'error', text: err.response.data.message});
				});

				this.memberIdForChangeRole = null;
				this.selectMemberRoleId = null;
			},

			handleAttachMemberToGroup(selectedOption) {
				if (!this.isHasManageGroupMembersPermission) {
					return this.sendNotifyPermissionInfo('manage-group-members');
				}

				if (!this.group.id) {
					return this.attachDraftMemberToGroup([selectedOption.id]);
				}

				this.$api.members.attachMemberToGroup({
					group_id: this.group.id,
					user_tenant_ids: [selectedOption.id],
				}, this.form).then(() => {
					this.$store.dispatch('members/setGroupRole', {
						groupId: this.group.id,
						roleId: this.defaultRole.id,
						memberId: selectedOption.id,
					});
					this.handleToggleShowMembers();
					this.selectMembers = [];
				}).catch((err) => {
					this.$notify({type: 'error', text: err.response.data.message});
				})
			},
			handleDetachMemberToGroup(member) {
				if (!this.group.id) {
					return this.draft.attachMembers = this.draft.attachMembers.filter(item => item.id !== member.id);
				}

				this.$api.members.detachMemberToGroup({
					member_id: member.id,
					group_id: this.group.id,
					user_tenant_ids: [member.id],
				}).catch((err) => {
					this.$notify({type: 'error', text: err.response.data.message});
				}).finally(() => {
					if (this.getUserId === member.id) this.$modal.hide('group-setting-modal');
				});

			},
			/*handleCloneGroup() {
                this.$api.group.cloneGroup(this.group.id).then((data) => {
                    this.internalMembers.map((member) => {
                        member.group_role.map(role => {
                            if (role.group_id === this.group.id) {
                                this.$store.dispatch('members/setGroupRole', {
                                    groupId:  data.group.id,
                                    roleId:   role.id,
                                    memberId: member.id,
                                });
                            }
                        });
                    });
                    this.initForm = {...this.form};
                    this.closeModal();
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                })
            },*/
			handleCreateGroup() {

				this.setQuillName();
				this.setQuillDescription();

				let form = Object.assign({}, this.form, {
					group_id: null,
					members: this.draft.attachMembers.map(item => {
						return {
							role_id: item.group_role[0].id,
							user_tenant_id: item.id,
						}
					})
				});

				this.$api.group.createGroup(form).then((data) => {
					this.draft.attachMembers.map((member) => {
						this.$store.dispatch('members/setGroupRole', {
							groupId: data.group.id,
							memberId: member.id,
							roleId: member.group_role[0].role_id,
						});
					});

					this.$event.$emit('set-create-group-name', null);
					this.initForm = {...this.form};
					// window.location.reload();
					this.closeModal();
				}).catch((err) => {
					this.defaultError(err.response);
				})
			},
			handleUpdateGroup() {
				this.setQuillName();
				this.setQuillDescription();

				this.$api.group.updateGroup(this.form).then(() => {
					this.initForm = {...this.form};

					this.closeModal();
				}).catch((err) => {
					this.defaultError(err.response);
				})
			},
			handleRemoveGroup() {
				this.$modal.show("confirm-modal", {
					title: this.$t('delete_this_group'),
					body: this.$t('are_you_sure_you_want_to_delete_this_group'),
					confirmCallback: () => {
						this.$api.group.removeGroup(this.group.id).then(() => {
							this.$api.task.getTasksDeadline();
							this.initForm = {...this.form};

							this.closeModal();

							if (this.$route.name === 'board') {
								return this.$router.push({name: 'home'});
							}
						})
							.catch(err => {
								this.$notify({type: 'error', text: err.response.data.message});
							});
					},
				});
			},
			handleArchivedGroup() {
				this.$modal.show("confirm-modal", {
					title: this.$t('archive_this_group'),
					body: this.$t('are_you_sure_you_want_to_archive_this_group'),
					confirmCallback: () => {
						this.$api.group.archivedGroup(this.group.id).then(() => {
							this.$api.task.getTasksDeadline();
							this.initForm = {...this.form};

							this.closeModal();
						}).catch((err) => {
							this.$notify({type: 'error', text: err.response.data.message});
						})
					},
				});
			},
			handleUnarchivedGroup() {
				this.$api.group.unarchivedGroup(this.group.id).then(() => {
					this.$api.task.getTasksDeadline();
					this.initForm = {...this.form};

					this.closeModal();
				}).catch((err) => {
					this.$notify({type: 'error', text: err.response.data.message});
				})
			},
		}
	}
</script>

<style lang="scss">
    #group-setting-modal {
        overflow: hidden;
        /*#attach-members-button {*/
        /*border: 0;*/
        /*border-radius: 6px;*/
        /*font-size: 22px;*/
        /*font-weight: bold;*/
        /*height: 42px;*/
        /*padding: 6px 0px !important;*/
        /*color: #636b6e;*/

        /*@media (max-width: 400px) {*/
        /*margin-left: 13px;*/
        /*}*/
        /*}*/

        .group-member-multiselect {
            display: flex;
            justify-content: flex-end;
        }

        .group-member-multiselect-inner {
            width: 100%;

            .multiselect__content-wrapper {
                max-height: initial !important;
                height: auto;
                margin-bottom: 20px;
                box-shadow: rgba(0, 0, 0, 0.2) 0px 3px 5px 0px;
            }
        }

        .group-member-multiselect-add {
            margin-left: 20px;
        }

        .multiselect__placeholder {
            margin-bottom: 0 !important;
        }

        .group-settings-select-role {
            border: 1px solid #e0e0e0;
            height: 30px;
            font-weight: 600;
            margin-left: -5px;
        }

        .group-settings-select-role:disabled {
            opacity: 0.2;
        }

        .boardSetting {
            background-color: #fff;
            color: #5078f2;
            font-size: 20px;
            padding: 0;
            position: relative;
            top: -4px;
        }

        table.table {
            td {
                font-weight: bold;
            }
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .group-settings-trash button {
            padding: 0 5px !important;

            &:hover {
                background-color: #7d7d7d;

                i {
                    .icon {
                        fill: #fff;
                    }
                }
            }
        }

        .btn-close {
            background-color: #425967;
            padding: 8px;
        }

        .btn-close-header {
            background: transparent;
            border: none;
            box-shadow: none;
            fill: #fff;
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);

            &:hover {
                background: transparent;
                border: none;
                box-shadow: none;
                fill: #e2e6e9;
            }

            .icon-close {
                display: block;

                .icon {
                    width: 14px;
                    height: 14px;
                }
            }
        }

        .btn-board-settings-save {
            background-color: #5078f2;
            padding: 8px;
        }

        .group-member-controls {
            margin-bottom: 10px;
        }


        @media (max-width: 767px) {
            .modal-body {
                label {
                    font-size: 16px;
                }

                .members-group-list {
                    margin: 5px -15px;
                }
            }
        }
    }
</style>
