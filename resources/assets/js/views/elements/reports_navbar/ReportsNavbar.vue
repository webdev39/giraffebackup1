<template>
    <div id="reports-navbar">
        <div class="reports-navbar-header">
         <div class="reports-navbar-title">
            {{ $t("reports") }}
         </div>
            <div class="reports-header-select">
                <timerange-multi-select class=" time-range" default-label="Timerange" selected-array-name="selectTimeranges" :options="timerangeOptions" :selected="selectItems.selectTimeranges" @trigger-multi-select="passMultiSelectValues" />

                <multi-select class="" :default-label="$t('group')" selected-array-name="selectGroups" :options="optionsGroups" :selected="selectItems.selectGroups" @trigger-multi-select="passMultiSelectValues" />
                <multi-select class="" :default-label="$t('board')" selected-array-name="selectBoards" :options="optionsBoards" :selected="selectItems.selectBoards" @trigger-multi-select="passMultiSelectValues" />
                <multi-select class="" :default-label="$t('user')"  selected-array-name="selectMembers" v-if="showUser" :options="optionsMembers" :selected="selectItems.selectMembers" @trigger-multi-select="passMultiSelectValues" />
                <multi-select class="" :default-label="$t('client')" selected-array-name="selectClients" :options="optionsCustomers" :selected="selectItems.selectClients" @trigger-multi-select="passMultiSelectValues" />

                <multi-select class="" default-label="Report Details" selected-array-name="selectShowOptions" :options="reportsShowOptions" :selected="selectItems.selectShowOptions" :without-dynamic-label="true" @trigger-multi-select="passMultiSelectValues">
                    <hr/>
                    <multi-select-item selected-array-name="selectGrouping" :options="groupingOptions" :selected="selectItems.selectGrouping" @trigger-multi-select="passMultiSelectValues" :singleSelect="true" />
                    <hr/>
                    <multi-select-item selected-array-name="selectDetails" :options="detailsOptions" :selected="selectItems.selectDetails" @trigger-multi-select="passMultiSelectValues" :singleSelect="true" />
                </multi-select>
            </div>
            <template v-if="totalTimeUsed && totalTimeBill">
                <div id="total-time-wrapper"
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
                </div>
            </template>



            <div class="button-holder">
                <button class="button__icon" @click="getFeedReportsExport">
                    <i class="icon-download">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                             <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-download">
                             </use>
                         </svg>
                    </i>
                </button>
                <button class="button__icon">
                    <i class="icon-settings">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                         <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-settings">
                         </use>
                     </svg>
                    </i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters}         from "vuex";

    import MultiSelect          from './MultiSelect'
    import MultiSelectItem      from './MultiSelectItem'
    import TimerangeMultiSelect from './TimerangeMultiSelect'

    export default {
        name: 'reports-navbar',
        props: {
            criterias: Object,
            selectItems: Object,
            selectedDetails: Boolean,
            totalTimeUsed: {
                type: Object,
                default: () => {},
            },
            totalBilledTime: {
                type: Object,
                default: () => {},
            },
            totalTimeBill: {
                type: Object,
                default: () => {},
            }
        },
        computed: {
            ...mapGetters({
                members:    'members/getMembers',
                owner:      'members/getOwner',
                groups:     'reports/getGroups',
                allGroups:  'groups/getGroups',
                boards:     'reports/getBoards',
                customers:  'customers/getCustomers',
                getFullName: 'user/getFullName',
            }),
            showUser() {
                if (this.checkPermission('report-other-data')) {
                    return true
                }

                if (this.checkPermission('report-own-data')) {
                    return false
                }
            },
            optionsMembers() {
                let options = [];
                let members = this.members.sort((a,b) => b.user.status - a.user.status);
                let optionMember = {};

                if (this.selectItems.selectGroups.length) {
                    members = members.filter(member => {
                        return this.selectItems.selectGroups.some(item =>
                            this.groups.find(group => group.id === item).members.some(groupMember => groupMember === member.id)
                        );
                    });
                }

                members.map(member => {
                    optionMember = {id: member.id, name: `${member.user.last_name} ${member.user.name}`};

                    if (!member.user.status) {
                        optionMember.disabled = 1;
                    }

                    options.push(optionMember);
                });

                return options;
            },
            optionsGroups() {
                let options = [];

                this.groups.filter(group => {
                    options.push({id: group.id, name: group.name});
                });

                return options;
            },
            optionsBoards() {
                let options = [];

                let boards = this.boards;

                if (this.selectItems.selectGroups.length) {
                    boards = boards.filter(board => {
                        return this.selectItems.selectGroups.some(item => item === board.group_id);
                    });
                }

                boards.filter(board => {
                    if (!board.is_archive) {
                      options.push({id: board.id, name: board.name});
                    }
                });

                return options;
            },
            optionsCustomers() {
                let options = [];

                this.customers.filter(customer => {
                    options.push({id: customer.id, name: customer.name});
                });

                return options;
            },



            timerangeOptions(){
                if (!this.criterias || typeof this.criterias.timerange === 'undefined') {
                    return [];
                }
                return this.criterias.timerange.map((timerangeItem) => {
                    return {id: timerangeItem.id, name: timerangeItem.name}
                });
            },
            reportsShowOptions(){
                if (!this.criterias || typeof this.criterias.show === 'undefined' ){
                    return [];
                }
                if(!this.selectedDetails){
                    return this.criterias.show.map((showItem) => {
                        if (showItem.name === 'Show Comment'){
                            return {id: showItem.id, name: showItem.name, disabled: true}
                        }
                        return {id: showItem.id, name: showItem.name}
                    })
                }
                return this.criterias.show.map((showItem) => {
                    return { id: showItem.id, name: this.translateKey(showItem.name) }
                });
            },
            groupingOptions(){
                if (!this.criterias || typeof this.criterias.grouping === 'undefined' ){
                    return [];
                }
                return this.criterias.grouping.map((groupingItem) => {
                    return {id: groupingItem.id, name: this.translateKey(groupingItem.name)}
                });
            },
            detailsOptions(){
                if (!this.criterias || typeof this.criterias.details === 'undefined' ){
                    return [];
                }
                return this.criterias.details.map((detailsItem) => {
                    return {id: detailsItem.id, name: this.translateKey(detailsItem.name)}
                });
            },
        },
        components: {
            MultiSelect,
            MultiSelectItem,
            TimerangeMultiSelect
        },
        mounted() {
            this.$api.customers.getCustomers().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$api.reports.getBoards().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$api.reports.getGroups().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
        },
        methods: {
            getFeedReportsExport() {
                this.$event.$emit('export-report');
            },
            passMultiSelectValues(id, selectedArray, singleSelect, customId = null){
                this.$emit('trigger-multi-select-navbar', id, selectedArray, singleSelect, customId);
            },
            translateKey(key) {
                if (key) {
                    return this.$t(key.toLowerCase().toString().replace(/(\s+|\-)/g,"_"));
                }
            },
        }
    }
</script>

<style lang="scss">
    .multi-select-checkbox-wrapper .label-text{
        width: 160px;
    }
    .multi-select-checkbox-wrapper .multi-select-checkbox-label-text{
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
