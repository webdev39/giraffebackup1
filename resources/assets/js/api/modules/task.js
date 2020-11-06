import http                 from '@utils/http'
import store                from '@store'
import { getTimeMinutes }   from '@helpers/time'
import helpersTask          from '@helpers/task'

let request = {
    getTaskId: []
};

export default {
    getTaskById(taskId, params = {}) {
        return new Promise((resolve, reject) => {
            if (request.getTaskId.includes(taskId)) {
                return;
            }

            request.getTaskId.push(taskId);

            http.get(`/api/v1/task/${taskId}`, Object.assign({task_res: 'long'}, params)).then(response => {
                delete response.data.task.sort_order;

                store.dispatch('groups/addTask', response.data.task);
                // store.dispatch('management/addTask', response.data.task);
                store.dispatch('task/updateTaskUpdateAt');

                resolve(response.data);
            }).catch(error => {
                reject(error);
            }).finally(() => {
                request.getTaskId = request.getTaskId.filter(id => id !== taskId);
            })
        })
    },
    getTaskByBoardId(boardId, done) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/task/board/${boardId}?task_res=long&hide_done=${done ? done : ''}`)
                .then(response => {

                    if (response.data.tasks.length) {
                        store.dispatch('groups/setTasksListByBoardId', {
                            board_id: boardId,
                            tasks: response.data.tasks
                        });
                    }

                    resolve(response.data);
                }).catch(error => {
                    reject(error);
                })
        })
    },
    getTasksListByIds(ids) {
        return new Promise((resolve, reject) => {
            http.get('/api/v1/task/list?task_res=long', {
                ids: ids
            }).then(response => {
                resolve(response.data);
            }, error => {
                reject(error);
            });
        });
    },
    getLastTasks() {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/task/last`).then((response) => {
                store.dispatch('groups/setLastTasksIds', response.data.tasks.map(item => item.id));

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    getTasksByFilterId(filterId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/filter/${filterId}`).then(response => {
                store.dispatch('task/setTasksList', response.data.tasks);
                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    getTasksByDeadline (period) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/user-task/${period}?deadline_task_res=short`).then(response => {
                if (period === 'today') {
                    store.dispatch('groups/setTodayTaskIds', response.data.tasks);
                } else if (period === 'week') {
                    store.dispatch('groups/setWeekTaskIds', response.data.tasks);
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    getTasksDeadline() {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/user-task`).then(response => {
                store.dispatch('groups/setTodayTaskIds', response.data.today);
                store.dispatch('groups/setWeekTaskIds', response.data.week);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    createTask(form) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/create?task_res=long', form).then(response => {
                window.app.$api.tag.getTags();
                if (response.data.draft_task && response.data.task) {
                    store.dispatch('groups/removeTask', response.data.task);
                    store.dispatch('groups/addTask', response.data.draft_task);
                    store.dispatch('groups/addTask', response.data.task);
                    store.dispatch('task/addTaskToTaskList', response.data.task);
                    store.dispatch('management/addTask', response.data.task);
                } else if (response.data.draft_task) {
                    store.dispatch('groups/addTask', response.data.draft_task);
                    store.dispatch('management/addTask', response.data.draft_task);
                } else {
                    store.dispatch('groups/addTask', response.data.task);
                    store.dispatch('management/addTask', response.data.task);
                }

                if (!form.is_draft) {
                    window.app.$notify({type:'success', text: window.app.$t('create_new_task') + '<span class="link link_theme_default" onclick="window.app.$router.replace({query: {taskId: '+response.data.task.id+'}})"> ' + response.data.task.board_name + ' </span>'});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    updateTask(data, currentData, isUpdate = true) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/update?task_res=long`, data).then(response => {
                if (currentData) {
                    store.dispatch('groups/removeTask', currentData);
                    store.dispatch('management/removeTask', currentData);
                    store.dispatch('groups/addTask',    response.data.task);
                    store.dispatch('management/addTask',    response.data.task);
                } else if (isUpdate) {
                    // since we do not know what type of sorting to use,
                    // we delete it to leave the default data
                    delete response.data.task.sort_order;

                    store.dispatch('groups/changeTask', response.data.task);
                    store.dispatch('management/changeTask', response.data.task);
                }

                store.dispatch('task/updateTaskUpdateAt');

                window.app.$notify({type:'success', text: window.app.$t('update_task')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    markReadTask(taskId) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/${taskId}/read`)
                .then(response => {
                    resolve(response.data);
                }, error => {
                    reject(error);
                })
        });
    },
    changeOrderTasks(data) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/order/change/${data.sort_type}`, data).then(response => {
                window.app.$notify({type:'success', text: window.app.$t('change_order_task')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    updateTaskSortWeight(data, currentData) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/task/${data.id}/change-sort-weight`, data).then(response => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    changeWorkflowTask(data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/task/workflow/change', data).then(response => {
                // since we do not know what type of sorting to use,
                // we delete it to leave the default data
                delete response.data.task.sort_order;

                store.dispatch('groups/changeTask', response.data.task);

                // Audio
                if (response.data.task.done_by) {
                    if(store.getters['user/getUserProfile'].audio) {
						let audio = new Audio(store.getters['user/getUserProfile'].audio.finish_task.file);
						audio.volume = 0.02;
						audio.play();
					}
                }

                helpersTask.updateCountTask(response.data.task);

                window.app.$notify({type:'success', text: window.app.$t('change_workflow_task')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    removeTask(task) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/task/remove/${task.id}`).then(response => {
                store.dispatch('groups/removeTask', task);

                store.dispatch('groups/removeTaskDeadline', {period: 'today', task_id: task.id});
                store.dispatch('groups/removeTaskDeadline', {period: 'week',  task_id: task.id});

                if (task.hard_budget) {
                    store.dispatch('groups/decrementBudgedBoard', getTimeMinutes(task.hard_budget));
                    store.dispatch('management/decrementBudgedBoard', getTimeMinutes(task.hard_budget));
                }

                window.app.$notify({type:'success', text: window.app.$t('remove_task')});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    attach(form, user) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/subscriber/attach', form).then(response => {
                if (form.user_tenant_id === store.getters['user/getUserTenantId']) {
                    helpersTask.updateCountTask(form);
                }

                if (user) {
                    window.app.$notify({type:'success', text: `${user.name} ${user.last_name} ${window.app.$t('was_add_as_task')}`});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },

    subscribeAndAttach(data) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/subscribe-and-attach', data).then(response => {
                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },

    detach(form, user) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/subscriber/detach', form).then(response => {
                if (form.user_tenant_id === store.getters['user/getUserTenantId']) {
                    helpersTask.updateCountTask(form);
                }

                window.app.$notify({type:'success', text: `${user.name} ${user.last_name} ${window.app.$t('was_remove_as_task')}`});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    subscribe(form, user) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/notification/subscribe', form).then(response => {

                if (user) {
                    window.app.$notify({type:'success', text: `${user.name} ${user.last_name} ${window.app.$t('was_add_as_notify')}`});
                }

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
    unsubscribe(form, user) {
        return new Promise((resolve, reject) => {
            http.post('/api/v1/task/notification/unsubscribe', form).then(response => {
                window.app.$notify({type:'success', text: `${user.name} ${user.last_name} ${window.app.$t('was_remove_as_notify')}`});

                resolve(response.data);
            }, error => {
                reject(error);
            })
        });
    },
};
