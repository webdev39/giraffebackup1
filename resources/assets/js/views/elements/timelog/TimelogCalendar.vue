<template>
    <div v-click-outside="closeFromOutside">
        <div @click="toggleModalDateLog">
            <i class="icon-calendar">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                    xlink:href="#icon-calendar">
                    </use>
                </svg>
            </i>
        <span>{{ $moment(dateLog.timer.end_time ).format("YYYY-MM-DD") }}</span>
        </div>
        <div
            v-if="modalDateLog"
            class="timelog-list-item-element-calendar"
            :title="$t('change_date')"
        >
            <div class="modal-log-date">
                <date-picker
                    v-model="getDateLogged"
                    :config="Object.assign(dateLogOptions, { locale: getUserProfile.language.iso_639_1 })"
                    @dp-change="dateTimeLog"
                />
                <content-loading
                    :absolute="styleLoading.absolute"
                    :autosize="styleLoading.autosize"
                    :loading="loading"
                />
            </div>
        </div>
    </div>
</template>

<script>
	import {mapGetters}             from "vuex";
	import clickOutside             from 'v-click-outside'
    import DatePicker               from 'vue-bootstrap-datetimepicker'

    import ContentLoading           from '@views/components/ContentLoading'

    export default {
        props: {
            dateLog:    { type: Object, required: true},
            loading:    { type: Boolean, required: true, default: false}
        },
        watch: {
            modalDateLog(newVal) {
                if(newVal) {
                    setTimeout(() => {
                        this.canClose = true;
                    })
                }
            },
        },
        data(){
            return{
                canClose: false,
                modalDateLog: false,
                datetimeFormat: 'YYYY-MM-DD HH:mm',
                dateLogOptions: {
                    format: 'YYYY-MM-DD HH:mm',
                    toolbarPlacement: 'bottom',
                    showTodayButton: false,
                    showClose: false,
                    showClear: true,
                    inline: true,
                },
                dateLogged: '',
                styleLoading: {
                    'absolute': true,
                    'autosize': true
                }
            }
        },
        computed: {
			...mapGetters({
				getUserProfile: 'user/getUserProfile',
			}),
            getDateLogged: {
                get () {
                    return this.toLocalTime(this.dateLog.timer.end_time, this.datetimeFormat);
                },
                set (value) {
                    return this.dateLogged = value;
                }
            }
        },
        components: {
            DatePicker,
            ContentLoading
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        methods: {
            closeFromOutside() {
                if(this.canClose) {
                    this.modalDateLogClose();
                }
            },
            toggleModalDateLog () {
                this.modalDateLog = !this.modalDateLog;
            },
            modalDateLogClose () {
                this.modalDateLog = false;
            },
            dateTimeLog (e) {
                let data = {
                    id:         this.dateLog.id,
                    task_id:    this.dateLog.task_id,
                    time:       this.dateLog.timer.time,
                    end_time:   this.toUTCTime(e.date)
                };

                this.$emit('update', data, this.dateLog);
                // this.modalDateLogClose();
            },
        },
    }
</script>

<style scoped lang="scss">
    .timelog-list-item-element-calendar{
        display: inline;
        position: relative;
        .modal-log-date{
            left: 0;
            right: inherit;
            z-index: 10;
            width: auto;
        }
    }
    .timelog-list-item-element .icon-calendar .icon{
        width:13px;
        height:13px;
        position:relative;
        top:1px;
        cursor: pointer;
    }
</style>