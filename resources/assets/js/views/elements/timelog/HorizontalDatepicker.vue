<template>
    <div class="horizontal-datepicker-wrapper">
        <div class="horizontal-datepicker-body">
            <div class="horizontal-datepicker-item horizontal-datepicker-arrow" @click="previousDate" :title="$t('change_date')">
                <slot name="previousArrow">
                    <i class="icon-left-arrow">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-left-arrow"></use>
                        </svg>
                    </i>
                </slot>
            </div>
            <div class="horizontal-datepicker-item horizontal-datepicker-content" :title="$t('change_date')">
                <div class="horizontal-datepicker-date">{{displayDate}}</div>
                <div class="horizontal-datepicker-title">
                    <i class="icon-calendar">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"xlink:href="#icon-calendar"></use>
                        </svg>
                    </i>
                    <datepicker
                        v-model="currentDate"
                        @dp-change="datepickerChange"
                        :config="datetimeOptions"
                        style="display: inline;width: 1px;opacity: 0;" >
                    </datepicker>
                </div>
            </div>
            <div class="horizontal-datepicker-item horizontal-datepicker-arrow" @click="nextDate" :title="$t('change_date')">
                <slot name="nextArrow">
                    <i class="icon-right-arrow">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-right-arrow"></use>
                        </svg>
                    </i>
                </slot>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapGetters} from 'vuex';
    import moment       from "moment";
    import datePicker   from 'vue-bootstrap-datetimepicker'

    export default{
        props:{
          startDate: {
              type: Object,
              default: () => moment(),
              validator: value => {
                  return moment(value).isValid();
              }
          },
        },
        data(){
            return{
                currentDate: this.startDate,
                datetimeOptions: {
                    format: 'YYYY-MM-DD',
                    useCurrent: false,
                    showTodayButton: false,
                    showClear: false,
                    showClose: true
                },
            }
        },
        components:{
            'datepicker':datePicker
        },
        computed:{
            displayDate() {
                return moment(this.currentDate).format("YYYY-MM-DD");
            }
        },
        created() {
            this.dispatchSetCurrentDate(this.currentDate);
        },
        methods:{
            previousDate(){
                const newDate = moment(this.currentDate).subtract(1, 'd');
                this.setCurrentDate(newDate);
            },
            nextDate(){
                const newDate = moment(this.currentDate).add(1, 'd');
                this.setCurrentDate(newDate);
            },
            setCurrentDate(newDate) {
              this.currentDate = newDate;
            },
            datepickerChange() {
                this.dispatchSetCurrentDate(this.currentDate);
            },
            dispatchSetCurrentDate(date) {
                this.$store.dispatch('timers/setCurrentDate', date);
            }
        }
    }
</script>
