<template>
    <div id="footer-container">
        <comment-files v-model="files" :task_id="task_id" :can-delete="true" />

        <div class="control-container">
           <div class="comment-box-options">
                <div class="ql-formats comment-box-options-item">
                    <button class="ql-link"></button>
                </div>
                <div class="ql-formats comment-box-options-item">
                   <button>
                       <i class="fa fa-at" @click="$emit('showMention')" aria-hidden="true"></i>
                   </button>
                </div>
                <div class="ql-formats comment-box-options-item">
                   <button class="ql-emoji"></button>
                </div>


                <template v-if="canLogged && !replyTask">
                    <div class="ql-formats comment-box-options-item" v-click-outside="clickOutsideModal.bind(null, 'loggedTime')">
                        <button class="ql-logged-time" @click.prevent="handleShowTimeLog">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                        </button>
                        <span
                            v-if="show.loggedTime"
                            class="ql-modal-logged-time"
                        >
                            <logged-time :hours.sync="innerLoggedHours" :minutes.sync="innerLoggedMinutes" />
                        </span>
                    </div>
                    <div class="ql-formats comment-box-options-item" v-click-outside="clickOutsideModal.bind(null, 'loggedDate')">
                        <button @click.prevent="show.loggedDate = !show.loggedDate"
                                class="ql-logged-date" type="button">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <span
                            class="ql-modal-logged-date modal-log-date"
                            v-if="show.loggedDate" >
                            <date-picker
                                v-model="innerLoggedDate"
                                :config="Object.assign(datetimeOptions, { locale: getUserProfile.language.iso_639_1 })"
                            />
                        </span>
                    </div>
                </template>
                <div class="ql-formats comment-box-options-item" v-if="canUpload">
                   <button class="ql-file" @click="$refs.file.click()">
                       <i class="ql-fill icon-document">
                           <svg class="icon" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                               <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                    xlink:href="#icon-document">
                               </use>
                           </svg>
                       </i>
                       <input
                           multiple
                           type="file"
                           ref="file"
                           @change="uploadFile"
                           style="display: none;"
                       />
                   </button>
                </div>
                <div class="ql-formats comment-box-options-item" v-if="canLink">
                   <button class="ql-video"></button>
                </div>
                <div class="ql-formats comment-box-options-item">
                    <button @click="showToolBar = !showToolBar" >
                       <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                </div>
                <slot name="footer" v-bind:loggedInfo="loggedInfo">
                    <span v-if="loggedInfo">
                        {{ loggedInfo }}
                    </span>
                </slot>
            </div>
        </div>
        <quill-toolbar  v-show="showToolBar"
                        :canLogged="canLogged"
                        :replyTask="replyTask"
                        :taskId="task_id"
                        :loggedHours.sync="getLoggedHours"
                        :loggedMinutes.sync="getLoggedMinutes"
                        :loggedDate.sync="getLoggedDate"
                        @uploadCustomFile="getCustomFile" />
    </div>
</template>

<script>
	import { mapGetters } from "vuex";
	import clickOutside from 'v-click-outside'
    import CommentFiles from "@assets/js/views/elements/comments/CommentFiles";
    import QuillToolbar from '@views/components/editor/QuillToolbar';
    import LoggedTime   from '@views/components/logged/LoggedTime';
    import DatePicker   from 'vue-bootstrap-datetimepicker';

    export default {
        name: "quill-footer",
        props: {
            files:          Array,
            loggedDate:     String,
            loggedHours:    Number,
            loggedMinutes:  Number,
            task_id:        Number,
            replyTask: {
                type: [Boolean, Number],
                default: false,
            },
            canLogged: {
                type: Boolean,
                default: true,
            },
            canLink: {
                type: Boolean,
                default: true,
            },
            canUpload: {
                type: Boolean,
                default: true,
            },
        },
        data() {
            return {
                datetimeFormat: 'YYYY-MM-DD HH:mm',
                showToolBar: false,
                show: {
                    advanced:   false,
                    loggedTime: false,
                    loggedDate: false,
                },
                datetimeOptions: {
                    format: 'YYYY-MM-DD HH:mm',
                    toolbarPlacement: 'bottom',
                    showTodayButton: false,
                    showClose: false,
                    showClear: true,
                    inline: true
                },
            };
        },
        computed:{
			...mapGetters({
				getUserProfile: 'user/getUserProfile',
			}),
            innerLoggedDate: {
                get() {
                  return this.loggedDate;
                },
                set(value) {
                  this.$emit('update:loggedDate', value);
                }
            },
            getLoggedDate: {
                get() {
                    return this.loggedDate;
                },
                set(value) {
                    return this.$emit('updateLoggedDate', value);
                }
            },
            getLoggedMinutes: {
                get() {
                    return this.loggedMinutes;
                },
                set(value) {
                    return this.$emit('updateLoggedMinutes', value);
                }
            },
            getLoggedHours: {
                get() {
                    return this.loggedHours;
                },
                set(value) {
                    return this.$emit('updateLoggedHours', value);
                }
            },
            innerFiles: {
                get() {
                    return this.files;
                },
                set(value) {
                    return this.$emit('update:files', value);
                }
            },
            // innerLoggedDate() {
            //     if (this.loggedDate) {
            //         return this.$moment(this.loggedDate).format(this.datetimeFormat)
            //     }
            //
            //     return null;
            // },
            innerLoggedTime() {
                if (this.loggedHours > 0 || this.loggedMinutes > 0) {
                    const hours   = (this.loggedHours   > 9 ? this.loggedHours   : '0' + this.loggedHours);
                    const minutes = (this.loggedMinutes > 9 ? this.loggedMinutes : '0' + this.loggedMinutes);

                    return (hours > 0 ? `${hours}h ` : '') + (minutes > 0 ? `${minutes}m ` : '');
                }

                return null;
            },
            loggedInfo() {
                let info = '';

                if (this.innerLoggedTime) {
                    info += `${this.innerLoggedTime}`;

                    if (this.innerLoggedDate) {
                        info += ` for `;
                    }
                }

                if (this.innerLoggedDate) {
                    info += `${this.innerLoggedDate}`;
                }

                return info;
            },
            innerLoggedHours: {
                get() {
                    return Number(this.loggedHours);
                },
                set(value) {
                    this.$emit('update:loggedHours', Number(value));
                }
            },
            innerLoggedMinutes: {
                get() {
                    return Number(this.loggedMinutes);
                },
                set(value) {
                    this.$emit('update:loggedMinutes', Number(value));
                }
            },
        },
        components: {
            CommentFiles,
            QuillToolbar,
            LoggedTime,
            DatePicker,
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        methods: {
            clickOutsideModal(modal) {
                this.show[modal] = false;
            },
            handleShowTimeLog() {
                this.show.loggedTime = !this.show.loggedTime;
            },
            uploadFile(e) {
                let files = e.target.files || e.dataTransfer.files;

                if (files.length) {
                  this.$emit('uploadCustomFile', files);
                }
            },
            showPreview(file) {
                if (file.is_image) {
                    return this.$modal.show('light-box-modal', {image: file, user: this.user, task_id: this.task_id});
                }

                return window.open(file.path, '_blank');
            },
            deleteFile(index) {
                this.files.splice(index, 1);
            },
            getCustomFile (file) {
                this.$emit('uploadCustomFile', file);
            }
        }
    }
</script>


<style lang="scss">
    .comment-box-options-item{
        .ql-modal-logged-time {
            position: absolute;
            top: 32px;
            left: 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }
        .ql-file{
            .icon-document{
                width: 16px;
                fill: #dbdbdb;
            }
        }
    }
</style>
