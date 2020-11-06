<template>
    <div class="toolbar-container">
        <div class="main-container">
            <span class="ql-formats">
                <select class="ql-header">
                    <option value="1"></option>
                    <option value="2"></option>
                    <option value="3"></option>
                    <option value="4"></option>
                    <option value="5"></option>
                    <option value="6"></option>
                    <option selected="selected"></option>
                </select>
            </span>

            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
            </span>

            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
            </span>

            <span class="ql-formats">
                <select class="ql-align">
                    <option selected="selected"></option>
                    <option value="center"></option>
                    <option value="right"></option>
                    <option value="justify"></option>
                </select>
            </span>

            <span class="ql-formats" v-if="canBlock">
                <button class="ql-blockquote"></button>
                <button class="ql-code-block"></button>
            </span>
        </div>

        <div class="extended-container">
            <span class="ql-formats">
                <select class="ql-size">
                    <option value="small"></option>
                    <option selected="selected"></option>
                    <option value="large"></option>
                    <option value="huge"></option>
                </select>
            </span>

            <span class="ql-formats">
                <button class="ql-script" value="sub"></button>
                <button class="ql-script" value="super"></button>

                <select class="ql-color">
                    <option selected="selected"></option>
                    <option value="#e60000"></option>
                    <option value="#ff9900"></option>
                    <option value="#ffff00"></option>
                    <option value="#008a00"></option>
                    <option value="#0066cc"></option>
                    <option value="#9933ff"></option>
                    <option value="#ffffff"></option>
                    <option value="#facccc"></option>
                    <option value="#ffebcc"></option>
                    <option value="#ffffcc"></option>
                    <option value="#cce8cc"></option>
                    <option value="#cce0f5"></option>
                    <option value="#ebd6ff"></option>
                    <option value="#bbbbbb"></option>
                    <option value="#f06666"></option>
                    <option value="#ffc266"></option>
                    <option value="#ffff66"></option>
                    <option value="#66b966"></option>
                    <option value="#66a3e0"></option>
                    <option value="#c285ff"></option>
                    <option value="#888888"></option>
                    <option value="#a10000"></option>
                    <option value="#b26b00"></option>
                    <option value="#b2b200"></option>
                    <option value="#006100"></option>
                    <option value="#0047b2"></option>
                    <option value="#6b24b2"></option>
                    <option value="#444444"></option>
                    <option value="#5c0000"></option>
                    <option value="#663d00"></option>
                    <option value="#666600"></option>
                    <option value="#003700"></option>
                    <option value="#002966"></option>
                    <option value="#3d1466"></option>
                </select>

                <select class="ql-background">
                    <option value="#000000"></option>
                    <option value="#e60000"></option>
                    <option value="#ff9900"></option>
                    <option value="#ffff00"></option>
                    <option value="#008a00"></option>
                    <option value="#0066cc"></option>
                    <option value="#9933ff"></option>
                    <option selected="selected"></option>
                    <option value="#facccc"></option>
                    <option value="#ffebcc"></option>
                    <option value="#ffffcc"></option>
                    <option value="#cce8cc"></option>
                    <option value="#cce0f5"></option>
                    <option value="#ebd6ff"></option>
                    <option value="#bbbbbb"></option>
                    <option value="#f06666"></option>
                    <option value="#ffc266"></option>
                    <option value="#ffff66"></option>
                    <option value="#66b966"></option>
                    <option value="#66a3e0"></option>
                    <option value="#c285ff"></option>
                    <option value="#888888"></option>
                    <option value="#a10000"></option>
                    <option value="#b26b00"></option>
                    <option value="#b2b200"></option>
                    <option value="#006100"></option>
                    <option value="#0047b2"></option>
                    <option value="#6b24b2"></option>
                    <option value="#444444"></option>
                    <option value="#5c0000"></option>
                    <option value="#663d00"></option>
                    <option value="#666600"></option>
                    <option value="#003700"></option>
                    <option value="#002966"></option>
                    <option value="#3d1466"></option>
                </select>
            </span>

            <span class="ql-formats" v-if="canIndent">
                <button class="ql-indent" value="+1"></button>
                <button class="ql-indent" value="-1"></button>
            </span>

            <!--<span class="ql-formats" v-if="canUpload">-->
                <!--<button class="ql-file" @click="$refs.file.click()">-->
                    <!--<i class="icon-document">-->
                        <!--<svg class="icon" xmlns="http://www.w3.org/2000/svg">-->
                            <!--<use xmlns:xlink="http://www.w3.org/1999/xlink"-->
                            <!--xlink:href="#icon-document">-->
                            <!--</use>-->
                        <!--</svg>-->
                    <!--</i>-->
                    <!--<input type="file" ref="file" @change="uploadFile" style="display: none;"/>-->
                <!--</button>-->
            <!--</span>-->

            <!--<span class="ql-formats" v-if="canLink">-->
                <!--&lt;!&ndash;<button class="ql-link"></button>&ndash;&gt;-->
                <!--<button class="ql-video"></button>-->
            <!--</span>-->
        </div>
    </div>
</template>

<script>
    import clickOutside         from 'v-click-outside'
    // import DatePicker           from 'vue-bootstrap-datetimepicker'
    // import LoggedTime           from '@views/components/logged/LoggedTime'

    //import {mapGetters}                     from 'vuex'

    export default {
        name: "quill-toolbar",
        props: {
            loggedDate:     String,
            loggedHours:    Number,
            loggedMinutes:  Number,
            taskId:        Number,
            canLogged: {
                type: Boolean,
                default: true,
            },
            // canLink: {
            //     type: Boolean,
            //     default: true,
            // },
            // canUpload: {
            //     type: Boolean,
            //     default: true,
            // },
            canBlock: {
                type: Boolean,
                default: true,
            },
            canIndent: {
                type: Boolean,
                default: true,
            },
            replyTask: {
                type: [Boolean, Number],
                default: false,
            }
        },
        computed: {
            /*...mapGetters({
                getTask:    'task/getTask',
            }),*/
            /*innerLoggedDate: {
                get() {
                    return this.loggedDate;
                },
                set(value) {
                    this.$emit('update:loggedDate', value);
                }
            },*/
            /*innerLoggedHours: {
                get() {
                    return Number(this.loggedHours);
                },
                set(value) {
                    this.$emit('update:loggedHours', Number(value));
                }
            },*/
            /*innerLoggedMinutes: {
                get() {
                    return Number(this.loggedMinutes);
                },
                set(value) {
                    this.$emit('update:loggedMinutes', Number(value));
                }
            },*/
        },
        // components: {
        //     DatePicker,
        //     LoggedTime
        // },
        directives: {
            'clickOutside': clickOutside.directive
        },
        methods: {
            /*handleShowTimeLog() {
                if (!this.taskId) {
                    return this.$notify({ type:'error', text: this.$t('pls_select_task') });
                }

                this.show.loggedTime = !this.show.loggedTime;
            },*/
            clickOutsideModal(modal) {
                if (modal === 'loggedTime') this.$emit('updateLoggedTime');
                if (modal === 'loggedDate') this.$emit('updateLoggedDate');

                this.show[modal] = false;
            },
            uploadFile(e) {
                let files = e.target.files || e.dataTransfer.files;

                if (files.length) {
                    this.$emit('uploadCustomFile', files[0]);
                }
            },
        }
    }
</script>

<style lang="scss">
    .icon-document {
        width: 100%;
        svg {
            width: 100%;
        }
    }
    .toolbar-container {
        //border: none !important;
        // border-bottom: 1px solid #ccc !important;

        .ql-right {
            float:right;
        }

        .ql-logged-date i,
        .ql-logged-time i {
            position: relative;
            top: -2px;
        }

        .ql-advanced i {
            position: relative;
            top: -2px;
            padding: 2px 5px;
        }

        .ql-modal-container {
            position: relative;
            display: inline;
        }

        .ql-modal-logged-date {
            width: 180px;
            position: absolute;
            top: 27px;
            right: -37px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .ql-modal-logged-time {
            position: absolute;
            top: 32px;
            right: -64px;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
        }
    }

    @media (max-width: 650px) {
        .ql-modal-logged-date.modal-log-date {
            left: auto;
        }
    }
</style>
