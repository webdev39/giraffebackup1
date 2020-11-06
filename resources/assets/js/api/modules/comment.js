import http         from '@utils/http'
import store        from '@store'
import arrayToTree  from 'array-to-tree'

export default {
    // getCommentDetails (commentId) {
    //     return new Promise((resolve, reject) => {
    //         http.get(`/api/v1/comment/${commentId}?comment_details=1`).then(response => {
    //
    //             response.data.comment.replies = arrayToTree(response.data.comment.replies, {childrenProperty: 'replies'});
    //
    //             store.dispatch('actions/changeAction',  response.data.comment);
    //             store.dispatch('task/changeAction',     response.data.comment);
    //
    //             resolve(response.data);
    //         }, error => {
    //             reject(error);
    //         })
    //     })
    // },
    createComment (data) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/comment/create?comment_res=long`, data).then(response => {
                window.app.$notify({type:'success', text: window.app.$t('create_comment')});
                window.app.$api.tag.getTags();

                /* todo remove after fixed in BE part */
                    if (!response.data.comment.task_title && response.data.comment.task_name) {
                        response.data.comment.task_title = response.data.comment.task_name
                    }
                /**/

                /* for replies */
                /*todo optimization */
                if (data.parentId) {

                    if (data.action_id) {
                        response.data.comment.action_id = data.action_id;
                    } else {
                        response.data.comment.action_id = response.data.comment.parent_id;
                    }

                    if (data.inTask) {
                        store.dispatch('task/addReply', response.data.comment);
                    } else {
                        store.dispatch('actions/addReply', response.data.comment);
                    }

                    return resolve(response.data);
                }

                store.dispatch('groups/changeCommentsTasksCount', {task_id: response.data.comment.task_id, count: 1});
                store.dispatch('groups/changeAttachmentTasksCount', {
                    task_id: response.data.comment.task_id,
                    count: response.data.comment.attachments.length
                });

                store.dispatch('actions/addActions', response.data.comment);

                resolve(response.data);
            }, error => {
                reject(error);
            })
        })
    },
    removeComment (comment) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/comment/${comment.id}`).then(response => {

                /* for replies */
                if (comment.parent_id) {
                    store.dispatch('actions/removeReply', comment);
                    store.dispatch('task/removeReply', comment);
                }

                store.dispatch('actions/removeAction', comment);
                store.dispatch('task/removeAction', comment);

                /* todo remove comment.task_id || comment.taskId */
                store.dispatch('groups/changeCommentsTasksCount', {task_id: comment.task_id || comment.taskId , count: -1});
                store.dispatch('groups/changeAttachmentTasksCount', {task_id: comment.task_id || comment.taskId, count: `-${comment.attachments.length}`});
                /* */

                resolve(response.data);
            }, error => {
                reject(error)
            })
        })
    },
    updateComment (data) {
        return new Promise((resolve, reject) => {
            http.put('/api/v1/comment/update', data).then(response => {
                window.app.$api.tag.getTags();

                /* todo wait.... fix backend */
                if (response.data.comment.replies) {
                    delete response.data.comment.replies;
                    delete response.data.comment.count_replies;
                }
                /* for replies */
                if (response.data.comment.parent_id) {

                    if (data.action_id) {
                        response.data.comment.action_id = data.action_id;
                    } else {
                        response.data.comment.action_id = response.data.comment.parent_id;
                    }

                    store.dispatch('actions/changeReply', response.data.comment);
                    store.dispatch('task/changeReply', response.data.comment);

                    return resolve(response.data)
                }

                store.dispatch('actions/changeAction', response.data.comment);
                store.dispatch('task/changeAction', response.data.comment);

                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    getLikesMembers (commentId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/reaction/comment/${commentId}/likers`).then(response => {
                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    toggleLike (commentId) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/reaction/comment/${commentId}/like`).then(response => {

                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    stickyComment (data) {
        return new Promise((resolve, reject) => {
            let stickyComment = '';
            let newData = {
                id: data.id,
                reactions: {
                    like: data.reactions.like,
                    stick: {}
                }
            };

            if (data.task_id) {
                stickyComment = `/task/${data.task_id}`;
                newData.reactions.stick.task_id = data.reactions.stick.task_id ? null : data.task_id
            } else {
                stickyComment = `/group/${data.group_id}`;
                newData.reactions.stick.group_id = data.reactions.stick.group_id ? null : data.group_id
            }

            http.put(`/api/v1/reaction/comment/${data.id}${stickyComment}/stick`).then(response => {

                // let newData = {
                //     id: data.id,
                //     reactions: {
                //         like: data.reactions.like,
                //         stick: {
                //             task_id: data.reactions.stick.task_id ? null : data.task_id
                //         }
                //     }
                // };
                //
                // newData.stick

                store.dispatch('actions/changeAction', newData);
                store.dispatch('task/changeAction', newData);

                resolve(response.data);
            }, error => {
                reject(error)
            })
        })
    },
    createAttachmentComment (data) {
        return new Promise((resolve, reject) => {
            http.post(`/api/v1/attachment/${data.attachmentId}/comment`, data).then(response => {
                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    getAttachmentComment (attachmentId) {
        return new Promise((resolve, reject) => {
            http.get(`/api/v1/attachment/${attachmentId}/comment`).then(response => {
                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    updateAttachmentComment (data) {
        return new Promise((resolve, reject) => {
            http.put(`/api/v1/attachment/${data.attachmentId}/comment`, data).then(response => {
                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
    removeAttachmentComment (data) {
        return new Promise((resolve, reject) => {
            http.delete(`/api/v1/attachment/${data.attachmentId}/comment/${data.commentId}`).then(response => {
                resolve(response.data)
            }, error => {
                reject(error)
            })
        })
    },
};
