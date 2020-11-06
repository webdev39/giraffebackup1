<template>
    <div @click.stop
        :class="['details-modal modal-subscriber', {'last-task-modal' : lastTask}]"
        v-click-outside="hideModal"
    >
        <button type="button" class="btn btn-lg btn_details-modal_close">
            <i class="icon-close" @click="hideModal">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-close"></use></svg>
            </i>
        </button>

        <content-loading :absolute="styleLoading.absolute" :autosize="styleLoading.autosize" :loading="isLoading"/>

        <!-- <div class="subscribers-table">
            <table class="col-xs-12">
                <thead>
                    <tr>
                        <td class="col-xs-3">Name</td>
                        <td class="col-xs-2">{{ $t("todo") }}</td>
                        <td class="col-xs-2">{{ $t("info") }}</td>
                        <td class="col-xs-2" v-if="checkPermission('time-tracking')">{{ $t("time") }}</td>
                        <td class="col-xs-3">{{ $t("last_action") }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(member, index) in members" :key="member.id"
                        v-if="member.user.status || member.todo || member.notify">
                        <td :class="['text-align-left', {'disabled': !member.user.status}]">
                            <span class="avatar"></span> {{ member.user.name }} {{ member.user.last_name }}
                        </td>
                        <td>
                            <label class="checkbox-holder container">
                                <input type="checkbox" v-model="member.todo"
                                       @change="getUpdateSubscriber('task', member, member.todo)"
                                       :disabled="isLoading || !isChangeSubscribers(member.todo)"/>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="checkbox-holder container">
                                <input type="checkbox" v-model="member.notify"
                                       @change="getUpdateSubscriber('notify', member, member.notify)"
                                       :disabled="isLoading || !isChangeSubscribers(member.notify)"/>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td v-if="checkPermission('time-tracking')">
                            {{ member.logged.time | formatTime }}
                        </td>
                        <td>
                            {{ member.logged.activity_at | formatDate }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div> -->

				    <div class="subscribers-table"> 
            <table class="col-xs-12">
                <thead>
                    <tr>
                        <td class="col-xs-6">Name </td>
                        <td class="col-xs-3">{{ $t("todo") }}</td>
                        <td class="col-xs-3">{{ $t("info") }}</td>
                        <!-- <td class="col-xs-2" v-if="checkPermission('time-tracking')">{{ $t("time") }}</td>
                        <td class="col-xs-3">{{ $t("last_action") }}</td> -->
												
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(member, index) in members" :keymember-info-icon="member.id"
                        v-if="member.user.status || member.todo || member.notify">
                        <td :class="['text-align-left', {'disabled': !member.user.status}]">
                            <span class="avatar">{{ member.user.name }} {{ member.user.last_name }}</span> 
														<span class="member-info-icon">
																	<i class="icon-info" >
																			<svg class="icon" xmlns="http://www.w3.org/2000/svg">
																					<use xmlns:xlink="http://www.w3.org/1999/xlink"
																							xlink:href="#icon-info">
																					</use>
																			</svg>
																	</i>
																			<div class="member-info-content">
																					<span class="member-time-info" v-if="checkPermission('time-tracking')">
																						{{ $t("time") }}	
																							<span> {{ member.logged.time | formatTime }}</span>
																					</span>
																					<span class="member-activity-info">
																						{{ $t("last_action") }}	
																						<span>{{ member.logged.activity_at | formatDate }}</span>
																					</span>
																			</div>
															</span>

                        </td>
                        <td>
                            <label class="checkbox-holder container">
                                <input type="checkbox" v-model="member.todo"
                                       @change="getUpdateSubscriber('task', member, member.todo)"
                                       :disabled="isLoading || !isChangeSubscribers(member.todo)"/>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <label class="checkbox-holder container">
                                <input type="checkbox" v-model="member.notify"
                                       @change="getUpdateSubscriber('notify', member, member.notify)"
                                       :disabled="isLoading || !isChangeSubscribers(member.notify)"/>
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <!-- <td v-if="checkPermission('time-tracking')">
                            {{ member.logged.time | formatTime }}
                        </td>
                        <td>
                            {{ member.logged.activity_at | formatDate }}
                        </td> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
	import { mapGetters } from 'vuex';
	import moment from 'moment';

	import find from '@helpers/findInGroups';
	import helpersTask from '@helpers/task';

	import ContentLoading from '@views/components/ContentLoading';
	import clickOutside from 'v-click-outside';

	export default {
		name: "modal-subscriber",
		props: {
			task: {
				type: Object,
			},
			lastTask: {
				type: Boolean,
				default: false
			},
		},
		components: {
			ContentLoading
		},
		mixins: [
			helpersTask
		],
		directives: {
			'clickOutside': clickOutside.directive
		},
		data() {
			return {
				styleLoading: {
					'absolute': true,
					'autosize': true
				},
				canClose: false,
			}
		},
		computed: {
			...mapGetters({
				getMembers: 'members/getMembers',
				getOwner: 'members/getOwner',
				getGroups: 'groups/getStateGroups',
				getUserId: 'user/getUserId',
			}),
			getCurrentGroup() {
				if (!this.task.group_id) {
					return
				}

				return find.searchGroupById(this.getGroups, this.task.group_id);
			},
			internalMembers() {
				if (this.getCurrentGroup && this.getCurrentGroup.members) {
					return find.searchMembersInGroups(this.getCurrentGroup, this.getMembers)
				}
			},
			members() {
				let members = this.internalMembers;

				if (this.task) {
					/* add userTodo and userInfo */
					members
						.filter(member => member.user.is_confirmed)
						.map(member => {
							// const member = { ...item };
							member.logged = this.getLoggedTime(member);
							member.todo = this.getSubscribersUser(member, 'task');
							member.notify = this.getSubscribersUser(member, 'notify');

							return member;
						});
				}

				return members;
			},
			isOwnerTask() {
				return this.task.creator_id === this.getUserId;
			},
			getSubscribers() {
				let data = {
					notify: [],
					task: []
				};

				this.members.forEach(item => {
					if (item.todo) {
						data.task.push(item.user.user_tenant_id)
					}
					if (item.notify) {
						data.notify.push(item.user.user_tenant_id)
					}
				});

				return data;
			}
		},
		filters: {
			formatDate: (value) => {
				if (value) {
					return moment(String(value)).local().format('YYYY-MM-DD');
				}
			},
			formatTime: (value) => {
				if (value) {
					const hours = Math.floor(value / 60);
					const minutes = value % 60;

					return hours + 'h ' + minutes + 'm';
				}
			}
		},
		mounted() {
			setTimeout(() => {
				this.canClose = true;
			}, 100);
		},
		methods: {
			isChangeSubscribers(isSelect) {
				if (this.isOwnerTask) {
					return this.getPermissionChangeSubscribers(isSelect);
				}

				if (this.handlePermissionByGroupId('update-task', this.task.group_id)) {
					return this.getPermissionChangeSubscribers(isSelect);
				}

				return false
			},
			getPermissionChangeSubscribers(isSelect) {
				if (isSelect) {
					return this.handlePermissionByGroupId('delete-assignees-task', this.task.group_id);
				}

				return this.handlePermissionByGroupId('add-assignees-task', this.task.group_id);
			},
			getSubscribersUser(member, type) {
				if (this.task.subscribers) {
					return this.task.subscribers[type].some(userTaskId => userTaskId === member.id);
				}
			},
			getLoggedTime(member) {
				if (this.task.tracked_time) {
					return this.task.tracked_time[member.id] || {};
				}

				return {};
			},
			getUpdateSubscriber(subscriberType, member, action) {
				// If action true it is action: "add member", if action false it is action: "remove member".
				if (action) {
					if (subscriberType === "task" && !member.notify) {
						return this.handleAttachAndSubscribe(member);
					}

					if (subscriberType === 'task') {
						return this.handleAttach(member);
					}

					if (subscriberType === 'notify') {
						return this.handleSubscribe(member);
					}
				}

				if (subscriberType === 'task') {
					return this.handleDetach(member);
				}

				if (subscriberType === 'notify') {
					return this.handleUnSubscribe(member);
				}
			},
			getDataForSubscriber(member) {
				return {
					group_id: this.task.group_id,
					board_id: this.task.board_id,
					task_id: this.task.id,
					subscriberId: member.id
				}
			},
			getDataRequest(member) {
				return {
					isDraftTask: this.task.draft,
					task_id: this.task.id,
					user_id: [member.user.id],
					user_tenant_id: [member.id],
					planned_deadline: this.task.planned_deadline,
					done_by: this.task.done_by,
					subscribers: this.getSubscribers
				};
			},

			handleDetach(member) {
				if (! this.task.draft) {
				    this.$api.task.detach(this.getDataRequest(member), member.user);
                }
				this.$store.dispatch('groups/detachSubscribers', this.getDataForSubscriber(member));
			},
			handleAttach(member) {
				if (! this.task.draft) {
					this.$api.task.attach(this.getDataRequest(member), member.user);
				}
				this.$store.dispatch('groups/attachSubscribers', this.getDataForSubscriber(member));
			},
			handleSubscribe(member) {
				if (! this.task.draft) {
					this.$api.task.subscribe(this.getDataRequest(member), member.user);
				}
				this.$store.dispatch('groups/subscribeSubscribers', this.getDataForSubscriber(member));
			},
			handleUnSubscribe(member) {
				if (! this.task.draft) {
					this.$api.task.unsubscribe(this.getDataRequest(member), member.user);
				}
				this.$store.dispatch('groups/unsubscribeSubscribers', this.getDataForSubscriber(member));
			},
			handleAttachAndSubscribe(member) {
				if (! this.task.draft) {
					this.$api.task.subscribeAndAttach({user_tenant_id: [member.id], task_id: this.task.id});
				}
				this.$store.dispatch('groups/attachSubscribers', this.getDataForSubscriber(member));
				this.$store.dispatch('groups/subscribeSubscribers', this.getDataForSubscriber(member));
			},
			hideModal() {
				if (this.canClose) {
					this.$emit('hide');
				}
			},
		}
	}
</script>

<style lang="scss">
    .text-align-left.disabled {
        color: #bcbcbc;
    }

    .modal-subscriber {
        z-index: 12;
        padding: 30px 0px 0px 0px;

        .btn_details-modal_close {
            position: absolute;
            right: 0;
            top: 5px !important;
            background-color: transparent;
            border: none;
            padding: 0;
            padding-bottom: 0;
            -webkit-box-shadow: none;
            box-shadow: none;

            .icon-close {
                // display: block;
                padding: 5px 10px;

                .icon {
                    width: 11px;
                    height: 11px;
                    fill: #b2b2b2;
                }
            }

            &:hover {
                .icon-close {
                    .icon {
                        fill: #62a8ea;
                    }
                }
            }
        }
    }

    .modal-subscriber .subscribers-table {
        overflow-y: auto;
        height: 300px;
		}
		.subscribers-table {
			.member-info-icon {
				// display: inline;
				position: relative;
				padding-left: 10px;
					.icon-info {
						width: 18px;
						height: 18px;
						display: inline-block;
						position: relative;
						top: 3px;
						fill: #376aa7;
						.icon {
							width: 18px;
							height: 18px;
						}
					}
			}
			.member-info-content {
				visibility: hidden;
				opacity: 0;
				position: absolute;
				left: 36px;
				top: -36px;
				display: flex;
				flex-direction: column;
				background: #fff;
				border-radius: 5px;
				z-index: 5;
				padding: 7px 10px;
				width: 200px;
    		// height: 40px;
				box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
				transition: all .3s;
				.member-time-info  {
					margin-bottom: 5px;
					display: flex;
    			justify-content: space-between;
					
				}
				.member-activity-info {
					display: flex;
    			justify-content: space-between;
				}
			}
			.member-info-icon  {
							&:hover {
									.member-info-content {
										visibility: visible;
										opacity: 1;
											&:hover {
													visibility: hidden;
													opacity: 0;
											}
									}
							}
			}
		}

    .modal-subscriber thead {
        padding: 0 15px;

        tr {
            border-bottom: 1px solid #e8e8e8;

        }

        td {
            padding-bottom: 5px;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 5;

            &:first-child {
                padding-left: 15px;
                text-align: left;
            }
        }
    }

    .modal-subscriber tbody {
        tr {
            border-bottom: 1px solid #e8e8e8;
            cursor: default;
            transition: background .3s;

            &:hover {
                background: #f1f1f1;
            }

            &:last-child {
                border-bottom: none;
            }

            td {
								padding: 0;
								padding-top: 8px;
								padding-bottom: 8px;

                &:first-child {
                    padding-left: 15px;
                }

                input {
                    cursor: pointer;
                }
            }
        }

    }

    /* The container */
    .checkbox-holder {
        display: inline;
				position: relative;
				padding: 0;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .checkbox-holder input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: -8px;
        left: -8px;
        height: 17px;
        width: 17px;
        background-color: #eee;
        border: 1px solid #dadada;
        border-radius: 2px;
        transition: all .3s;
		}
		@-moz-document url-prefix() {
			.checkmark {
				top: 5px;
			}
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

</style>
