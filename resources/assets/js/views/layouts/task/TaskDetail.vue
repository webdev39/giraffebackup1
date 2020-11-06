<template>
    <div
        v-if="Object.keys(getTask).length"
        :style="{top: getPositionTop}"
        class="task-detail-wrapper"
    >

        <div
            @click="hideModal"
            class="task-modal-backdrop"
        ></div>

        <div
            id="task-detail"
            class="col-sm-6"
        >
            <div class="task-detail">
                <theme-task-detail-header class="header">
                    <div class="margin-0 task-details-header-body">
                        <div class="text-center task-priority-icon task-details-header-item">
                            <label class="ocam-checkbox-label-1">
                                <input type="checkbox" name="check" class="ocam-checkbox-1" v-model="taskIsDone" :disabled="isLoading || isDraftTask">
                                <span class="label-text" :style="getTopCheckboxStyle"></span>
                            </label>
                        </div>

                        <div class="task-details-header-item task-details-header-item-center-block">
                            <div class="task-details-header-item-title">
                                <span id="task-name">{{ getTaskName || 'New Task'}}
                                    <template>
                                        <span
                                            v-if="!showEdit.name && canUpdateTask"
                                            class="editTask"
                                            :title="$t('edit_task')"
                                            @click="toggleEditFillable('name', true)"
                                            :disabled="isLoading"
                                        >
                                            <i class="icon-pencil">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#icon-pencil">
                                                    </use>
                                                </svg>
                                            </i>
                                        </span>
                                    </template>
                                </span>
                                <place-popup
                                    v-if="showEdit.name"
                                    v-model="form.name"
                                    @save="updateTaskName"
                                    @cancel="toggleEditFillable('name', false)"
                                    style="z-index: 21;"
                                />
                                <span
                                    v-if="task.draft"
                                    class="task-details-header-create"
                                >
                                </span>
                            </div>
                            <div class="task-details-header-item-info">
                                <div>
                                    <i class="icon-boards">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#icon-boards">
                                            </use>
                                        </svg>
                                    </i>

                                    <a href="#" class="detailsHeaderLinks" @click.prevent="showBoardSettings">
                                        {{ getTask.board.name }}
                                    </a>

                                </div>

                                <div>
                                    <i class="icon-groups">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#icon-groups">
                                            </use>
                                        </svg>
                                    </i>

                                    <a href="#" class="detailsHeaderLinks" @click.prevent="showGroupSettings">
                                        {{ getTask.group.name }}
                                    </a>
                                </div>

                                <div>
                                      <i class="icon-folder task-detail-header__awesome-text">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                 xlink:href="#icon-folder">
                                            </use>
                                        </svg>
                                    </i>
                                    <span> {{ getTask.id }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center task-details-header-item task-details-header-item-right-block">
                            <template v-if="!isDraftTask">
                                <button
                                    v-if="!isTrackingTask && canTimeTracking && checkPermission('time-tracking')"
                                    :title="$t('title_create_timer')"
                                    @click="createStartTimer"
                                    type="button"
                                    class="btn btn-start timer-button-list control-btns-hide"
                                >
                                    <i class="icon-time">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-time"></use>
                                        </svg>
                                    </i>
                                </button>

                                <button
                                    v-if="isTrackingTask && canTimeTracking && checkPermission('time-tracking')"
                                    :title="$t('stop')"
                                    @click="stopTimer"
                                    type="button"
                                    class="btn btn-start timer-button-list control-btns-hide"
                                >
                                   <i class="icon-stop">
                                        <svg class="icon font-color-red" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-stop"></use>
                                        </svg>
                                    </i>
                                </button>

                                <button
                                    v-if="isTrackingTask && canTimeTracking && checkPermission('time-tracking')"
                                    type="button"
                                    class="btn btn-start timer-button-list control-btns-hide"
                                    :title="$t('pause')"
                                    @click="pauseTimer"
                                >
                                    <i class="icon-pause">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-pause"></use>
                                        </svg>
                                    </i>
                                </button>

                                <button
                                    v-if="canDeleteTask"
                                    :title="$t('title_remove_task')"
                                    @click="removeTask"
                                    type="button"
                                    class="btn control-btns-hide"
                                >
                                    <i class="icon-trash-solid">
                                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-trash-solid"></use>
                                        </svg>
                                    </i>
                                </button>
                            </template>

                            <button type="button" class="btn control-btns-hide" :title="$t('close_modal_task_detail')" @click="hideModal">
                                <i class="icon-close">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                    xlink:href="#icon-close">
                                    </use>
                                </svg>
                                </i>
                            </button>
                        </div>
                    </div>
                </theme-task-detail-header>
                <div class="task-detail-content" ref="taskDetails">
                    <div>
                        <div class="details-wrapper-task">
                            <div class="details">
                                <ul class="details__list" v-click-outside="hideDetailsModal">
                                    <li
                                        data-v-step="task_1"
                                        class="details__item details__item_deadline"
                                    >
                                        <div
                                            v-if="taskDetails"
                                            class="details-task-item__content"
                                            :title="$t('todo')"
                                            @click="toggleDetailsModal('deadline')"
                                        >
                                            <i class="icon-calendar-empty details-task-icon">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-calendar-empty"></use></svg>
                                            </i>

                                            <div class="details-task-text">
                                                <small class="display-block details-task-item__deadline" :style="generalBeforeDeadline | deadlineColor">
                                                    {{ generalBeforeDeadline !== 0 ? `${generalBeforeDeadline} ${$t('days_to_deadline')}` : `${$t('deadline')}!` }}
                                                </small>

                                                <span>{{ getTask.deadline | toLocalTime('DD.MM.YY') || $t('not_selected') }}</span>

                                                <small :style="plannedBeforeDeadline | deadlineColor">
                                                    ToDo {{ plannedBeforeDeadline }}d
                                                </small>
                                            </div>
                                        </div>
                                        <skeleton-box v-else  style="min-height: 20px;width: 100%" />

                                        <modal-deadline
                                            v-if="showModal === 'deadline'"
                                            :task="getTask"
                                            @hide="toggleDetailsModal(false)"
                                        />
                                    </li>
                                    <li
                                        data-v-step="task_2"
                                        class="details__item details__item_user"
                                    >
                                        <div
                                            v-if="taskDetails"
                                            class="details-task-item__content"
                                            :title="$t('title_subscribers')"
                                            @click="toggleDetailsModal('users')"
                                        >
                                            <i class="icon-user details-task-icon">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#icon-user">
                                                    </use>
                                                </svg>
                                            </i>
                                            <span class="details-task-text">
                                            {{ getSubscribersUsers }}
                                        </span>
                                        </div>
                                        <skeleton-box v-else  style="min-height: 20px;width: 100%;" />

                                        <modal-subscriber
                                            v-if="showModal === 'users'"
                                            :task="getTask"
                                            @hide="toggleDetailsModal(false)"
                                        />
                                    </li>
                                    <li
                                        data-v-step="task_3"
                                        class="details__item details__item_priority"
                                    >
                                        <div
                                            v-if="taskDetails"
                                            class="details-task-item__content"
                                            :title="$t('priorities')"
                                            @click="toggleDetailsModal('priority')"
                                        >
                                            <i class="fa fa-square" aria-hidden="true" :style="{ color: getPriorityColor }"></i>
                                            <span class="details-task-text">
                                                {{ getPriorityName }}
                                            </span>
                                        </div>
                                        <skeleton-box v-else  style="min-height: 20px;width: 100%" />
                                        <modal-priority
                                            v-if="showModal === 'priority'"
                                            :task="getTask"
                                            @hide="toggleDetailsModal(false)"
                                        />
                                    </li>
                                    <li
                                        v-if="isGlobalTimeTracking"
                                        data-v-step="task_4"
                                        class="details__item"
                                    >
                                        <div v-if="taskDetails" class="details-task-item__content" :title="$t('title_budget')" @click="toggleDetailsModal('budget')">
                                            <i class="icon-clock details-task-icon">
                                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         xlink:href="#icon-clock">
                                                    </use>
                                                </svg>
                                            </i>
                                            <span class="details-task-text">
                                            {{ budgetDetails }}
                                        </span>
                                        </div>
                                        <skeleton-box v-else  style="min-height: 20px;width: 100%" />
                                        <modal-budget
                                            v-if="showModal === 'budget'"
                                            :taskId="taskId"
                                            @hide="toggleDetailsModal(false)"
                                        />
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="details-wrapper-task">
                            <div v-if="taskDetails" class="row subtasks">
                                <div class="col-md-12 task-detail__item_input">
                                    <div class="input-create-details cursor-pointer">
                                        <div class="input-task-description">
                                            <TaskDescriptionEditor
                                                :getTask="getTask"
                                                :key="getTask.description"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <skeleton-box v-else style="min-height: 150px;width: 100%;" />
                        </div>
                        <loader class="task-detail-loader" v-if="!taskDetails" />
                        <div class="details-wrapper-task">
                            <div v-if="taskDetails" class="row subtasks">
                                <div class="col-md-12 task-detail__item_input">
                                    <div class="input-create-details" :title="$t('title_create_subtask')">
                                        <i class="input-create-details__icon fa fa-check col-md-1" :class="{'disabled' : isLoading }" aria-hidden="true"></i>
                                        <div class="input-create-details__input clearfix">
                                            <input type="text" class="col-md-11" v-model="form.subtask" @keyup.enter.prevent="createSubTask" :placeholder="$t('create_new_subtask')" :disabled="isLoading" />
                                        </div>
                                        <div class="input-create-details__controls">
                                            <button type="button" class="button-description" @click="createSubTask" :disabled="isLoading || !isFormSubtask">
                                                {{$t("create")}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-11 col-md-offset-1" ref="subtasks">
                                    <draggable v-model="subTasks" @end="dragSubTaskEnd" style="position: relative;" :disabled="isDragSubTask || isLoading">
                                        <div v-for="sub in subTasks" :key='sub.id'>
                                            <subtask
                                                @showEdit="handleChangeStatusDrag"
                                                :subtask="sub"
                                            />
                                        </div>
                                    </draggable>
                                </div>
                            </div>
                        </div>
                        <div class="details-wrapper-task" style="margin-top: 10px; z-index: 11; position: relative;" v-if="!isDraftTask">
                            <div class="row comments" v-if="taskDetails">
                                <div class="task-detail__item_input" style="z-index: 2;margin:0;">
                                    <comment-edit :inTask="true" :task-id="taskId" />
                                </div>
                            </div>
                        </div>
                        <div class="details-wrapper-task" style="margin-top: 10px;" v-if="!isDraftTask">
                            <div class="row comments" v-if="taskDetails">
                                <div class="task-detail__item_input" style="z-index:2;">
                                    <comment-filter
                                        :fetching="isLoading"
                                        :short="true"
                                        :subscribers="subscribedUsers"
                                        @filter="filterBySubs"
                                        @update="updateActions"
                                        @clearAction="clearTaskAction"
                                    />
                                </div>
                            </div>
                        </div>

                        <transition name="fade">

                            <div class="details-wrapper-task" v-if="!isDraftTask">
                                <div class="row" v-if="taskDetails">
                                    <div class="col-md-12 details-wrapper-comments">
                                        <comment-list
                                            v-model="commentsFiltered"
                                            ref="comment-list"
                                            :inTask="true"
                                            :task-id="taskId"
                                            :showReply="true"
                                        />
                                        <content-loading
                                            v-if="!fetchData"
                                            :absolute="false"
                                            :loading="!fetchData"
                                        >
                                        </content-loading>
                                    </div>
                                </div>
                            </div>

                        </transition>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters }                   from 'vuex'
    import { Drag, Drop }                   from 'vue-drag-drop'
    import datePicker                       from 'vue-bootstrap-datetimepicker'
    import draggable                        from 'vuedraggable'
    import clickOutside                     from 'v-click-outside'

    import commentMixin                     from '@mixins/comment'
    import permissionsMixin                 from '@mixins/permissions'

    import task                             from '@helpers/task'
    import find                             from '@helpers/findInGroups'

    import ModalSubscriber                  from "@views/partcials/TaskDetail/ModalSubscriber";
    import ModalDeadline                    from "@views/partcials/TaskDetail/ModalDeadline";
    import ModalPriority                    from "@views/partcials/TaskDetail/ModalPriority";
    import ModalBudget                      from "@views/partcials/TaskDetail/ModalBudget";
    import Subtask                          from '@views/partcials/TaskDetail/Subtask'
    import CommentEdit                      from '@views/elements/comments/CommentEdit'
    import CommentList                      from '@views/elements/comments/CommentList'
    import CommentFilter                    from '@views/elements/comments/CommentFilter'
    import Editor                           from "@views/elements/editor/Editor";
    import TaskDescriptionEditor            from "@views/components/editor/TaskDescriptionEditor";
    import SkeletonBox                      from "@views/components/SkeletonBox/SkeletonBox";
    import PlacePopup                       from "@views/components/placePopup/PlacePopup";
    import CommentContent                   from "@views/elements/comments/CommentContent";

    import ThemeModalHeader                 from "@views/layouts/theme/ThemeModalHeader";
    import ThemeTaskDetailHeader            from "@assets/js/views/layouts/theme/taskDetail/ThemeTaskDetailHeader";
    import Loader                           from "@assets/js/views/components/loader/loader";

    import ContentLoading                   from '@views/components/ContentLoading'
	import TaskCreate                       from '@views/components/task/TaskCreate'
	import moment                           from "moment";
	import pusher                           from '@utils/pusher/index';
    import { _setDocumentTitle } from '@helpers/controlDocumentTitle';

    export default {
        name: "task-detail",
		components: {
			ThemeTaskDetailHeader,
			ModalPriority,
			Drag,
			datePicker,
			draggable,
			CommentList,
			CommentEdit,
			ModalBudget,
			ModalDeadline,
			ModalSubscriber,
			Subtask,
			CommentFilter,
			Editor,
			CommentContent,
			TaskDescriptionEditor,
			ThemeModalHeader,
			Loader,
			SkeletonBox,
			PlacePopup,
			ContentLoading,
			TaskCreate
		},
		mixins: [
			commentMixin,
			permissionsMixin,
		],
        data() {
            return {
                taskDetails:        false,
                assigned:   [],
                form: {
                    name:           null,
                    subtask:        null,
                },
                showEdit: {
                    name:           false,
                    description:    false,
                },

                showModal: false,

                pagination: {
                    current_page:   1,
                    per_page:       0,
                    count:          0,
                },
                container: {
                    content:        null,
                    appHeader:      null,
                },
                filters: {
                    names:          [],
                    columns:        [],
                },
                taskMembersForFiltering: [],
                positionTop: null,
                fetchData: false,
                isDragSubTask: false,
                taskId: Number(this.$route.query.taskId),
				task: {
					board: {
                		name: '',
                    },
					group: {
                		name: '',
                	}
                },
            }
        },
        computed: {
            ...mapGetters({
                getGroups:              'groups/getStateGroups',
				getTask:                'groups/getCurrentTask',
                getMembers:             'members/getMembers',
                getPriorities:          'priorities/getPriorities',
                getCurrentStartTimer:   'timers/getCurrentStartTimer',
				isDraftTask:            'task/isDraftTask',
                getSubTasks:            'task/getSubTasks',
                getActions:             'task/getActions',
                getChangeComments:      'task/getChangeComments',
                getUserId:              'user/getUserId',
				getCurrentTour:         'getCurrentTour',
				getUserProfile:         'user/getUserProfile',
			}),
            getTaskName() {
              if (this.getTask) {
                  const taskName = this.getTask.name;
                  _setDocumentTitle(taskName);
                  return taskName;
              }
            },
			isDone() {
				return this.getTask.done_by > 0;
			},
			getPriority() {
				return this.getPriorities.find(item => item.id === this.getTask.priority_id)
			},
			beforeDeadline() {
				let currentDate = moment.utc(moment()).format('YYYY-MM-DD');

				let result = {
					general: null,
					planned: null,
				};

				if (this.getTask.deadline) {
					result.general = moment(this.getTask.deadline).diff(currentDate, 'days');
				}

				if (this.getTask.planned_deadline) {
					result.planned = moment(this.getTask.planned_deadline).diff(currentDate, 'days');
				}

				return result;
			},
            getPositionTop() {
                return this. positionTop + 'px'
            },
            getPriorityColor() {
                if (this.getPriority) {
                    return this.getPriority.color
                }
            },
            getPriorityName() {
                if (this.getPriority) {
                    return this.getPriority.name
                }
            },
            isFormSubtask() {
                if (this.form.subtask) {
                    return this.form.subtask.length
                }

                return false
            },
            taskIsDone: {
                get() {
                    return this.isDone;
                },
                set() {
                    return this.changeTaskWorkflow();
                }
            },
            isGlobalTimeTracking () {
                return this.checkPermission('time-tracking')
            },
            isTrackingTask() {
                if (this.getCurrentStartTimer) {
                    return this.getCurrentStartTimer.task_id === this.taskId;
                }

                return null;
            },
            budgetDetails() {
                if (this.getTask.soft_budget || this.getTask.hard_budget) {
                    return this.getTask.soft_budget + ' / ' + this.getTask.hard_budget
                }

                return 'Not entered';
            },
            plannedBeforeDeadline() {
                return this.beforeDeadline.planned
            },
            generalBeforeDeadline() {
                return this.beforeDeadline.general
            },
            canUpdateTask() {
                return this.getTask.creator_id === this.getUserId || this.handlePermissionByGroupId('update-task', this.getTask.group_id);
            },
            canDeleteTask() {
                return this.getTask.creator_id === this.getUserId || this.handlePermissionByGroupId('delete-task', this.getTask.group_id);
            },
            canTimeTracking() {
                return this.handlePermissionByGroupId('time-tracking', this.getTask.group_id);
            },
            getTopCheckboxStyle() {
                let style = {
                    backgroundColor :   '#fff',
                    fontSize:           '32px',
                    lineHeight:         0
                };

                if (this.getTask) {
                    if (this.getPriority) {
                        style.backgroundColor = this.getPriority.color;
                    }

                    if (this.getTask.done_by) {
                        style.fontSize = '26px';
                    }
                }

                return style;
            },
            getInternalMembers() {
                if (this.getTask && this.getTask.group.members) {
                    return find.searchMembersInGroups(this.getTask.group, this.getMembers)
                }
                return [];
            },
            subscribedUsers() {
                return this.getInternalMembers.filter((member) => this.getTask.subscribers.task.includes(member.id));
            },
            getSubscribersUsers() {
                let limit = 3;
                let count = 0;
                let title = '';
                this.getInternalMembers.map((member) => {
                    if (this.getTask.subscribers.task.includes(member.id)) {
                        const { name, status } = member.user;
                        const isNotDisabled = name.toLowerCase() !== 'disabled';
                        /**
                         * Status - user is active or inactive
                         */
                        if (count < limit && isNotDisabled && status) {
                            title += `${member.user.name}, `;
                        }
                        count++;
                    }
                });

                title = title.substring(0, title.lastIndexOf(", "));

                if (count > limit) {
                    title += ` + ${count - limit}`;
                }

                return title;
            },
            subTasks: {
                get() {
                    return this.getSubTasks;
                },
                set(value) {
                    value.map((item, index) => {
                        return Object.assign(item, {order: index});
                    });

                    this.$store.dispatch('groups/changeTask', Object.assign({}, this.getTask, {sub_tasks: value}));
                    this.$store.dispatch('task/updateTaskUpdateAt');
                }
            },
            commentsFiltered() {
                if (this.taskMembersForFiltering.length) {
                    return [...this.getActions].filter((action) => {
                        return this.taskMembersForFiltering.indexOf(action.user.id) !== -1;
                    })
                }
                return this.getActions;
            },
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        mounted() {
			this.$store.dispatch('task/setTaskId', this.taskId);
			this.$store.dispatch('groups/setCurrentTaskId', this.taskId);

			if (this.getGroups.length) {
                this.getTaskDetail();
                this.initPositionModal();
                this.$event.$on('update-header-height', this.setPositionTop);

                if (this.isDraftTask) {
                    return;
                }
			}

		},
        beforeDestroy() {
            if (this.$refs['subtasks']) {
                this.$refs['subtasks'].addEventListener('touchstart.subtask touchend.subtask', () => {}, false);
            }

            // window.removeEventListener('resize', this.setPositionTop);
            this.$event.$off('update-header-height', this.setPositionTop);

            this.$store.dispatch('task/clearTaskId');
            this.clearPositionModal();
        },
        destroyed() {
			document.removeEventListener("keyup", this.hideOnEsc);
		},
        filters: {
            deadlineColor(value) {
                if (value === null) {
                    return {display: 'none'}
                }

                return {color: value > 0 ? 'Green' : 'Red'};
            }
        },
        watch: {
            '$route'(to, from) {
                this.getTaskDetail();
            },
        },
        methods: {
			clearTaskAction() {
				this.$store.dispatch('task/clearActions');
            },
            handleChangeStatusDrag(status) {
                this.isDragSubTask = status;
            },
            getTags() {
                this.$api.tag.getTags()
            },
            setPositionTop() {
                if (this.container.appHeader.clientHeight !== this.positionTop) {
                    this.positionTop = this.container.appHeader.clientHeight
                }
            },
            filterBySubs(subs) {
                this.taskMembersForFiltering = subs;
            },
            getTaskDetail() {
				if (!this.taskId) {
				    return;
				}
				this.$api.task.getTaskById(this.taskId).then((data) => {
                    this.form.name = data.task.name;
                    this.taskDetails = true;
                    this.task = data.task;
					this.getTags();
					this.nextCommentsAndLogs();

					document.addEventListener("keyup", this.hideOnEsc);

					this.$api.task.markReadTask(this.taskId);

					this.$store.dispatch('task/setBoardId', this.task.board_id);
					this.$store.dispatch('task/setGroupId', this.task.group_id);
				});
            },
            hideOnEsc(event) {
				event.stopPropagation();
				if (event.key === 'Escape') {
					this.hideModal();
				}
            },
            getTaskActions(clear) {
                this.fetchData = false;

                if (clear) {
                    this.pagination = {
                        current_page:   1,
                        per_page:       0,
                        count:          0,
                    };

                    this.$store.dispatch('task/clearActions');
                }

                this.$api.actions.getActionsByTaskId(this.taskId, this.pagination.current_page, {
                        assigned: this.assigned,
                        filters: this.filters.names,
                        columns: this.filters.columns,
                    }).then(data => {
                        this.pagination = data.pagination;
                        this.fetchData = true;
                    });
            },

            isChangeTaskDetail () {
                if (this.form.name !== this.getTaskName) {
                    return true
                }

                if (this.form.subtask) {
                    return true
                }

                if (this.getChangeComments.length) {
                    return true;
                }

                return false
            },
            hideModal(changeTaskDetail = true) {
                if (this.isChangeTaskDetail() && changeTaskDetail) {
                    return this.$modal.show("confirm-modal", {
                        title: this.$t('you_have_entered_new_data'),
                        body: this.$t('are_you_sure_you_want_to_continue_without_saving'),
                        confirmCallback: () => {
                            this.$store.dispatch('notify/markRead', {
                                taskId: this.getTask.id,
                            });

                            this.$store.dispatch('groups/changeTask', Object.assign({}, this.getTask, {unreadNotificationsCount: 0}));
                            this.$store.dispatch('task/clearChangeComments');
                            this.routerReplace()
                        },
                    });
                }
                this.$store.dispatch('notify/markRead', {
                    taskId: this.getTask.id,
                });

                this.$store.dispatch('groups/changeTask', Object.assign({}, this.getTask, {unreadNotificationsCount: 0}));
                this.routerReplace();
            },
            routerReplace() {
                return this.$router.replace({query: this.$lodash.omit(this.$route.query, ['taskId'])});
            },
            initPositionModal () {
                this.container.content = document.querySelector('.content');
                this.container.appHeader = document.getElementById('top-wrapper');

                this.container.content.classList.add('show-task-detail');
                this.setPositionTop();
            },
            clearPositionModal () {
                this.container.content.classList.remove('show-task-detail');
            },
            updateActions(obj) {
                if (obj.filters) {
                    this.filters = obj.filters;
                }
                if (obj.assigned) {
                    this.assigned = obj.assigned;
                }
                this.getTaskActions(true);
            },
            changeTaskWorkflow() {
                this.$api.task.changeWorkflowTask({task_id: this.taskId, is_done: !this.isDone});
            },
            createStartTimer() {
                this.$api.timer.createStartTimer({taskId: this.taskId}).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            pauseTimer() {
                this.$api.timer.pauseTimer(this.getCurrentStartTimer.id).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            stopTimer() {
                this.$api.timer.stopTimer(this.getCurrentStartTimer.id).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            toggleDetailsModal(modal) {
				if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', this.getTask.group_id)) {
					return this.sendNotifyPermissionInfo('read-task');
				}

				return modal ? this.showModal = modal : this.showModal = false;
            },
            hideDetailsModal($event) {
                let modalConfirm = document.querySelector('.v--modal-overlay');

                if (modalConfirm) {
                    return modalConfirm.contains($event.target);
                }

                return this.showModal = false;
            },
            showGroupSettings() {
                if (!this.handlePermissionByGroupId('read-group', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('read-group');
                }

                this.$modal.show('group-setting-modal', {groupId: this.getTask.group_id})
            },
            showBoardSettings() {
                this.$modal.show('board-setting-modal', {boardId: this.getTask.board_id})
            },
            toggleEditFillable(fillable, status) {

                if (!status) {
                    this.form[fillable] = this.getTask[fillable];
                }

                this.showEdit[fillable] = status;
            },
            updateTaskName() {
                if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('update-task', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('update-task');
                }

                if (!this.form.name || this.form.name.length < 2) {
                    return this.$notify({type:'error', text: 'Task name must be more then 1 char'});
                }

                if (this.form.name.length > 255) {
                    return this.$notify({type:'error', text: 'Task name must be no more then 255 char'});
                }

                let data = Object.assign({}, this.getTask, {name: this.form.name, task_id: this.getTask.id});

                this.$api.task.updateTask(data).then((res) => {
                    this.toggleEditFillable('name', false);
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            // updateTaskDescription(content) {
            //     if (!this.handlePermissionByGroupId('read-task', this.getTask.group_id)) {
            //         return this.sendNotifyPermissionInfo('read-task');
            //     }
            //
            //     if (this.form.description === this.getTask.description) {
            //         return this.toggleEditFillable('description', false);
            //     }
            //
            //     if (!this.form.description) {
            //         return this.$notify({type:'error', text: 'Description is empty'});
            //     }
            //
            //     let data = Object.assign({}, this.getTask, {description: content.content.trim(), task_id: this.getTask.id});
            //
            //     this.$api.task.updateTask(data).then(() => {
            //         this.toggleEditFillable('description', false);
            //     }).catch((err) => {
            //         this.$notify({type:'error', text: err.response.data.message});
            //     });
            // },
            removeTask() {
                if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('delete-task', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('delete-task');
                }

                this.$modal.show("confirm-modal", {
                    title: 'Delete this Task',
                    body: 'Are you sure you want to delete this task?',
                    confirmCallback: () => {
                        this.$api.task.removeTask(this.getTask).then(() => {
                            this.hideModal(false);

                            if (this.$route.name === 'filter') {
								this.$api.task.getTasksByFilterId(this.$route.params.id).catch(err => {
									this.$notify({type:'error', text: err.response.data.message});
								})
							}

                        }).catch(err => {
                            this.$notify({type:'warning', text: err.response.data.message});

                            this.$modal.show("confirm-modal", {
                                title: 'Update status a Task',
                                body: 'You cannot delete this task. Do you want to change the status to \'done\'?',
                                confirmCallback: () => {
                                    this.$api.task.changeWorkflowTask({task_id: this.getTask.id, is_done: !this.getTask.done_by}).then(() => {
                                        this.hideModal(false);
                                    }).catch(err => {
                                        this.$notify({type:'error', text: err.response.data.message});
                                    })
                                },
                            });
                        })
                    },
                });
            },
            createSubTask() {
                if (this.getTask.creator_id !== this.getUserId && !this.handlePermissionByGroupId('read-task', this.getTask.group_id)) {
                    return this.sendNotifyPermissionInfo('read-task');
                }

                if (!this.form.subtask) {
                    return this.$notify({type:'error', text: 'Task name must be more then 1 char'});
                }

                this.$api.subtask.createSubtask({
                    task_id:    this.getTask.id,
                    name:       this.form.subtask
                }).then(res => {
                    this.form.subtask = null;
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            dragSubTaskEnd() {
                let data = {task_id : this.getTask.id, order: this.subTasks.map(subTask => subTask.id)};

                this.$api.subtask.changeOrderSubtask(data).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            nextCommentsAndLogs() {
                setTimeout(() => {
                    let content = this.$refs.taskDetails;

                    if (!content) {
                        return;
                    }

					content.addEventListener('scroll', e => {
						if (Math.ceil(content.scrollTop + content.clientHeight) >= content.children[0].scrollHeight) {
                            if (this.pagination.count && this.pagination.count === this.pagination.per_page && !this.isLoading) {

								this.pagination.current_page = this.pagination.current_page + 1;

                                this.getTaskActions();
                            }
                        }
                    });
                }, 600);
            },
            createComment(comment) {
                if (this.taskId === comment.task_id) {
                    this.logsAndComments.unshift(comment);
                }
            },
            removeComment(commentId) {
                let index = this.$lodash.findIndex(this.logsAndComments, {id: commentId});
                this.logsAndComments.splice(index, 1);
            },
            updateComment(updateCommentId, comment = null) {
                let index = this.$lodash.findIndex(this.logsAndComments, {id: comment.id});

                if (comment === null) {
                    this.logsAndComments.splice(index, 1);
                } else {
                    this.logsAndComments.splice(index, 1, comment);
                }
            },
        }
	}
</script>

<style lang="scss">
    #task-detail {
        position: relative;
        z-index: 2;
        .task-details-header-body {
            .btn {
                background-color: inherit;
                padding: 0;
                line-height:1;

                    &:active{
                        box-shadow:none;
                     }

                i {
                .icon {
                        width:16px;
                        height:16px;
                            &:hover {
                                fill:#b8b8b8;
                            }
                        @media (min-width: 768px) {
                                width:20px;
                                height:20px;
                            }
                    }
                }

                .icon-close {
                    .icon {
                        width:14px;
                        height:14px;

                         @media (min-width: 768px) {
                            width:20px;
                            height:20px;
                         }
                    }
                }
                 .icon-time {
                    .icon {
                        fill:#19e24f;
                        width:18px;
                        height:18px;
                          @media (min-width: 768px) {
                            width:22px;
                            height:22px;
                         }
                    }
                }
            }

            .btn-start .icon-time .icon {


                &:hover {
                    animation: clock-shake .4s;

                }
                   @keyframes clock-shake {
                    0% {
                        transform: rotate(-5deg);
                    }
                    25% {
                        transform: rotate(15deg);
                    }
                    50% {
                        transform: rotate(-15deg);
                    }
                    75% {
                        transform: rotate(20deg);
                    }
                    100% {
                        transform: rotate(-5deg);
                    }
                }
            }
        }

        .input-create-details {
                .input-controls-btns {
                    display: flex;
                    justify-content: flex-end;

                     .btn {
                        background: none;
                        font-size: 15px;
                        padding:0;
                        color:#b2b2b2;
                        }

                     .btn-ok {
                        color:#376aa7;
                        margin-right:10px;

                 }

                    .btn-cancel {
                        border:none;
                }

            }
    }

        .input-create-details__icon {
            left: 9px;
            top: 11px;
        }

        .input-create-details__input input,
        .create-textarea {
            padding: 12px 60px 10px 44px;
        }

        .input-task-description {
            width: 100%;
            /*min-height: 46px;*/
            /*background: #fafafa;*/
        }

        .comments-filter {
            position: relative;
            padding: 10px 5px 5px 20px;
            z-index: 2;
        }

        .details-wrapper-comments {
            position: relative;
            min-height: 30px;
            margin-left: -4px;
            width: calc(100% + 4px);
        }
    }

    .show-task-detail{
        overflow: hidden;
    }
    .input-create-details__controls{
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }
    .task-detail-loader{
        position: static;
        margin: 20px auto;
        display: block;
    }
</style>
