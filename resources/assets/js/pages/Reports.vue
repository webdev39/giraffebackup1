<template>
    <div id="reports-wrapper" v-if="isLoggedIn">
    <template>
        <fixed-component is-scroll >
            <reports-navbar
                :criterias="criterias"
                :select-items="selectItems"
                :selected-details="selectedDetails"
                :totalTimeUsed="totalTimeUsed"
                :totalBilledTime="totalBilledTime"
                :totalTimeBill="totalTimeBill"
                @trigger-multi-select-navbar="handleMultiSelect"
            />
        <!--<div id="total-time-wrapper"
             v-show="typeof totalTimeUsed.y !== 'undefined' && typeof totalTimeBill.y !== 'undefined'">
            <div class="total-time-wrapper-item">
                <span class="total-label">
                 {{ $t('total_time_used') }}
                </span>

                <span class="total-time">
                    {{ totalTimeUsed | timerToHours }}
                </span>
            </div>

            <div class="total-time-wrapper-item">
                <span class="total-label" style="text-indent: 10px;">
                    {{ $t('total_billed_time') }}
                </span>
                <span class="total-time">
                    {{ totalBilledTime | timerToHours }}
                </span>
            </div>
        </div>-->
        </fixed-component>
    </template>

    <reports-table-section
        :criterias="criterias"
        :select-items="selectItems"
        :reports-timers="reportsTimers"
        :single-table="singleTable"
        :total-time-used="totalTimeUsed"
        :total-time-bill="totalTimeBill"
        :get-feed-is-processing="getFeedIsProcessing"
        :selected-details="selectedDetails"
        :total-billed-time="totalBilledTime"
        :total-unbilled-time="totalUnbilledTime"/>
    </div>
</template>

<script>
    import {mapGetters}                 from "vuex";

    import ReportsNavbar                from '@views/elements/reports_navbar/ReportsNavbar'
    import ReportsTableSection          from '@views/elements/reports_table_section/ReportsTableSection';
    import FixedComponent               from '@views/components/fixedComponent/FixedComponent';

    export default {
        data(){
            return{
                selectItems:{
                    selectGroups:       [],
                    selectBoards:       [],
                    selectMembers:      [],
                    selectClients:      [],
                    selectTimeranges:   [],
                    selectShowOptions:  [],
                    selectGrouping:     [],
                    selectDetails:      [],
                },
                criterias: {},
                reportsTimers: [],
                totalTimeUsed: {},
                totalTimeBill: {},
                totalUnbilledTime: {},
                totalBilledTime: {},
                singleTable: true,
                customTimerange: [],
                getFeedIsProcessing: true,
                columnComment: 5,
            }
        },
        computed: {
            ...mapGetters({
                billingStatuses: 'default/getBillingStatuses',
            }),
            selectedDetails() {
                return this.selectItems.selectDetails[0] === 1;
            },
        },
        components: {
            ReportsNavbar,
            ReportsTableSection,
            FixedComponent,
        },
        watch:{
            selectItems: {
                handler: function (val, oldVal) {
                    this.getFeed();
                },
                deep: true,
            },
            selectedDetails: {
                handler: function (val) {
                    let selectedIndex = this.selectItems.selectShowOptions.findIndex((item) => item === this.columnComment);
                    if (!val){
                        if (selectedIndex >= 0){
                            this.selectItems.selectShowOptions.splice(selectedIndex, 1);
                            this.getFeedIsProcessing = true;
                        }
                    }else{
                        if (selectedIndex < 0){
                            this.selectItems.selectShowOptions.push(this.columnComment);
                            this.getFeedIsProcessing = true;
                        }
                    }
                }
            }
        },
        created(){
            this.$event.$on('change-custom-timerange', this.changeCustomTimerange);
            this.$event.$on('export-report',   this.getFeedReportsExport);
        },
        mounted() {

            if (!this.checkPermission('report-own-data') && !this.checkPermission('report-other-data')) {
                this.sendNotifyPermissionInfo('report-data');
                return this.$router.push({name: 'home'});
            }

            this.$api.reports.getBoards().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$api.reports.getGroups().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.getReports();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods:{
            getFeedReportsExport() {
                let values = {
                    'selectedItems':    this.selectItems,
                    'customTimerange':  this.customTimerange
                };

                this.$api.reports.getFeedReportsExport(values);
            },
            getReports() {
                this.criterias = window.Laravel.reports.criteria;

                if (window.Laravel.reports.defaultSelected){
                    for (let prop in window.Laravel.reports.defaultSelected){
                        this.selectItems[prop] = window.Laravel.reports.defaultSelected[prop];
                    }
                }
            },
            getFeed() {
                this.getFeedIsProcessing = true;

                let values = {
                    'selectedItems':    this.selectItems,
                    'customTimerange':  this.customTimerange
                };

                this.$api.reports.getFilter(values).then(data => {
                    this.singleTable        = Array.isArray(data.records);
                    this.reportsTimers      = data.records;
                    this.totalTimeUsed      = data.totalTimeUsed;
                    this.totalTimeBill      = data.totalTimeBill;
                    this.totalUnbilledTime  = data.totalUnbilledTime;
                    this.totalBilledTime    = data.totalBilledTime;
                    this.getFeedIsProcessing = false;
                }).catch(err =>{
                    this.getFeedIsProcessing = false;
                });
            },
            handleMultiSelect(id, selectedArray, singleSelect, customId = null) {
                let selectedIndex = this.selectItems[selectedArray].findIndex((item) => item === id);

                if (singleSelect) {
                    this.selectItems[selectedArray] = [];
                    if (customId){
                        if (selectedIndex < 0) {
                            this.selectItems[selectedArray].push(id);
                        }
                    }else{
                        this.selectItems[selectedArray].push(id);
                    }
                } else {
                    if (customId){
                        let customSelectedIndex = this.selectItems[selectedArray].findIndex((item) => item === customId);
                        if (customSelectedIndex >= 0){
                            this.selectItems[selectedArray].splice(customSelectedIndex, 1);
                        }
                    }
                    if (selectedIndex >= 0) {
                        this.selectItems[selectedArray].splice(selectedIndex, 1);
                    } else {
                        this.selectItems[selectedArray].push(id);
                    }
                }
            },
            changeCustomTimerange(timerange, refresh = false) {
                this.customTimerange = timerange.map((timestamp) => {
                    return this.$moment(timestamp).seconds(0).minutes(0).hours(0).format();
                });

                if (refresh){
                    this.getFeed();
                }
            },
            beforeDestroy(){
                this.$event.$off('export-report');
            },
        },
    }
</script>
