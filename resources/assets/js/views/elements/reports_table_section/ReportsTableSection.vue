<template>
    <section id="reports-table-section" class="table-group">
        <div class="table-root-wrapper" style="position: relative;" v-show="!getFeedIsProcessing">
            <table-component
                    v-if="singleTable"
                    :data="reportsTimers"
                    :show-filter="false"
                    :show-caption="false"
                    :thead-class="calcTableHeadClass()"
                    filter-no-results=""
            >
                <table-column show="group_name" :label="$t('group')" :sortable="false"
                              :header-class="getHeaderClass('group')" :hidden="!showGroup">
                    <template slot-scope="row">
                        <span class="table-component_group_column"> {{ row.group_name }}</span>
                    </template>
                </table-column>

                <table-column show="board_name" :label="$t('board')" :sortable="false"
                              :header-class="getHeaderClass('board')" :hidden="!showBoard">
                    <template slot-scope="row">
                        <span class="table-component_board_column">{{ row.board_name }}</span>
                    </template>
                </table-column>

                <table-column show="task_name" :label="$t('task')" :sortable="false"
                              :header-class="getHeaderClass('task')" :hidden="!showTask">
                    <template slot-scope="row">
                        <span class="table-component_task_column">{{ row.task_name }}</span>
                    </template>
                </table-column>

                <table-column show="user_name" :label="$t('user')" :sortable="false"
                              :header-class="getHeaderClass('user')" :hidden="!showUser">
                    <template slot-scope="row">
                        <span v-if="selectedDetails">{{ row.user_name }} {{ row.user_last_name }}</span>
                        <span v-else>{{ formatUsersNames(row.users_name) }}</span>
                    </template>
                </table-column>

                <table-column show="time_used" :label="$t('time_used')" :sortable="false"
                              :header-class="getHeaderClass('time-used')" :hidden="!showTimeUsed">
                    <template slot-scope="row">
                        <span>{{ row.time_used || row.total_time_used | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="time_bill" :label="$t('time_bill')" :sortable="false"
                              :header-class="getHeaderClass('time-bill')" :hidden="!showBillTime">
                    <template slot-scope="row">
                        <span>{{ row.time_bill || row.total_billed_time | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="billed" :label="$t('billed_time')" :sortable="false"
                              :header-class="getHeaderClass('billed')" :hidden="!showBilled">
                    <template slot-scope="row">
                        <span>{{ row.billed_time  | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="unbilled" :label="$t('unbilled_time')" :sortable="false"
                              :header-class="getHeaderClass('unbilled')" :hidden="!showUnbilled">
                    <template slot-scope="row">
                        <span>{{ row.unbilled_time_total | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="billing_status_alias" :label="$t('bill_status')" :sortable="false"
                              :header-class="getHeaderClass('billing-status')" :hidden="!showBillStatus">
                    <template slot-scope="row">
                        <div>
                            <RowBillingStatusDropDown :selectedDetails="disableEditStatus(row)" :row="row"
                                                      :selectionOptions="statuses"/>
                        </div>
                    </template>
                </table-column>

                <table-column show="last_changes" :label="$t('last_changes')" :sortable="false"
                              :hidden="!showLastChanges">
                    <template slot-scope="row">
                        <span class="reports-item-comment">{{ row.end_time }}</span>
                    </template>
                </table-column>

                <table-column show="comment" :label="$t('comment')" :sortable="false"
                              :header-class="getHeaderClass('comment')" :formatter="formatComment"
                              :hidden="!showComment">
                    <template slot-scope="row">
                        <span class="reports-item-comment">{{ `${row.comment || ''}`.replace(/<[^>]+>/g,'') }}</span>
                    </template>
                </table-column>

                <template slot="tfoot">
                    <tr class="table-component-footer-tr">
                        <th class="column-group" v-show="showGroup">&nbsp;</th>
                        <th class="column-board" v-show="showBoard">&nbsp;</th>
                        <th class="column-task" v-show="showTask">&nbsp;</th>
                        <th class="column-user" v-show="showUser">&nbsp;</th>
                        <th class="column-time-used padding-time-total" v-show="showTimeUsed">{{ totalTimeUsed |
                            timerToStringFormat }}
                        </th>
                        <th class="column-time-bill padding-time-total" v-show="showBillTime">{{ totalTimeBill |
                            timerToStringFormat }}
                        </th>
                        <th class="column-billed padding-time-total" v-show="showBilled">{{ totalBilledTime |
                            timerToStringFormat }}
                        </th>
                        <th class="column-unbilled padding-time-total" v-show="showUnbilled">{{ totalUnbilledTime |
                            timerToStringFormat }}
                        </th>
                        <th class="column-billing-status" v-show="showBillStatus">&nbsp;</th>
                        <th class="column-last-changes" v-show="showLastChanges">&nbsp;</th>
                        <th class="column-comment" v-show="showComment">&nbsp;</th>
                    </tr>
                </template>

            </table-component>

            <table-component
                    v-else
                    v-for="(timers, key, index) in reportsTimers"
                    :data="reportsTimers[key]"
                    :key="key"
                    :show-filter="false"
                    :show-caption="false"
                    :cache-key="key"
                    :group-by="key"
                    :thead-class="calcTableHeadClass(index)"
                    filter-no-results="''"
            >
                <table-column show="group_name" label="Group" :sortable="false"
                              :header-class="getHeaderClass('group', index)" :hidden="!showGroup">
                    <template slot-scope="row">
                        <span class="table-component_group_column"> {{ row.board_name }}</span>
                    </template>
                </table-column>

                <table-column show="board_name" label="Board" :sortable="false"
                              :header-class="getHeaderClass('board', index)" :hidden="!showBoard">
                    <template slot-scope="row">
                        <span class="table-component_board_column">{{ row.board_name }}</span>
                    </template>
                </table-column>

                <table-column show="task_name" label="Task" :sortable="false"
                              :header-class="getHeaderClass('task', index)" :hidden="!showTask">
                    <template slot-scope="row">
                        <span class="table-component_task_column">{{ row.task_name }}</span>
                    </template>
                </table-column>

                <table-column show="user_name" label="User" :sortable="false"
                              :header-class="getHeaderClass('user', index)" :hidden="!showUser">
                    <template slot-scope="row">
                        <span v-if="selectedDetails">{{ row.user_name }} {{ row.user_last_name }}</span>
                        <span v-else>{{ formatUsersNames(row.users_name) }}</span>
                    </template>
                </table-column>

                <table-column show="time_used" label="Time-Used" :sortable="false"
                              :header-class="getHeaderClass('time-used', index)" :hidden="!showTimeUsed">
                    <template slot-scope="row">
                        <span>{{ row.time_used || row.total_time_used | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="time_bill" label="Time-Bill" :sortable="false"
                              :header-class="getHeaderClass('time-bill', index)" :hidden="!showBillTime">
                    <template slot-scope="row">
                        <span>{{ row.time_bill || row.total_billed_time | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="billed" label="Billed-Time" :sortable="false"
                              :header-class="getHeaderClass('billed', index)" :hidden="!showBilled">
                    <template slot-scope="row">
                        <span>{{ row.billed_time  | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="unbilled" label="Unbilled-Time" :sortable="false"
                              :header-class="getHeaderClass('unbilled', index)" :hidden="!showUnbilled">
                    <template slot-scope="row">
                        <span>{{ row.unbilled_time_total | timerToStringFormat }}</span>
                    </template>
                </table-column>

                <table-column show="billing_status_alias" label="Bill-Status" :sortable="false"
                              :header-class="getHeaderClass('billing-status', index)" :hidden="!showBillStatus">
                    <template slot-scope="row">
                        <span>
                            <RowBillingStatusDropDown :selectedDetails="disableEditStatus(row)" :row="row"
                                                      :selectionOptions="statuses"/>
                        </span>
                    </template>
                </table-column>

                <table-column show="last_changes" :label="$t('last_changes')" :sortable="false"
                              :hidden="!showLastChanges">
                    <template slot-scope="row">
                        <span class="reports-item-comment">{{ row.end_time }}</span>
                    </template>
                </table-column>

                <table-column show="comment" label="Comment" :sortable="false" :header-class="getHeaderClass('comment')"
                              :formatter="formatComment" :hidden="!showComment">
                    <template slot-scope="row">
                        <span class="reports-item-comment">{{ row.comment.replace(/<[^>]+>/g,'') }}</span>
                    </template>
                </table-column>

                <template slot="tfoot" slot-scope="{ rows }">
                    <tr class="table-component-footer-tr">
                        <th class="column-group" v-show="showGroup"></th>
                        <th class="column-board" v-show="showBoard">&nbsp;</th>
                        <th class="column-task" v-show="showTask">&nbsp;</th>
                        <th class="column-user" v-show="showUser">&nbsp;</th>
                        <th class="column-time-used padding-time-total" v-show="showTimeUsed">{{
                            calculateGropedTimeUsed(rows, "used") }}
                        </th>
                        <th class="column-time-bill padding-time-total" v-show="showBillTime">{{
                            calculateGropedTimeUsed(rows, "bill") }}
                        </th>
                        <th class="column-billed" v-show="showBilled">{{ calculateGropedTimeUsed(rows, "billed") }}</th>
                        <th class="column-unbilled" v-show="showUnbilled">{{ calculateGropedTimeUsed(rows, "unbilled")
                            }}
                        </th>
                        <th class="column-billing-status" v-show="showBillStatus">&nbsp;</th>
                        <th class="column-last-changes" v-show="showLastChanges">&nbsp;</th>
                        <th class="column-comment" v-show="showComment">&nbsp;</th>
                    </tr>
                </template>
            </table-component>
        </div>

        <div class="reports-spinner-root-wrapper" v-show="getFeedIsProcessing">
            <span class="reports-spinner-wrapper">
                <i class="fa fa-circle-o-notch fa-spin"></i>
            </span>
        </div>
    </section>
</template>

<script>
	import {mapGetters} from "vuex";

	import commentMixin from "@mixins/comment";
	import {TableComponent, TableColumn} from '@views/elements/vue_table_component/index'
	import RowBillingStatusDropDown from '@views/partcials/RowBillingStatusDropDown'
	import ThemeTableTr from '@views/layouts/theme/ThemeTableTr'

	export default {
		props: {
			criterias: {default: {}, type: Object},
			selectItems: {default: {}, type: Object},
			reportsTimers: {type: [Object, Array]},
			singleTable: {type: Boolean},
			totalTimeUsed: {type: Object},
			totalTimeBill: {type: Object},
			totalUnbilledTime: {type: Object},
			totalBilledTime: {type: Object},
			getFeedIsProcessing: {type: Boolean},
			selectedDetails: {type: Boolean}
		},
		computed: {
			...mapGetters({
				statuses: 'default/getBillingStatuses',
			}),
			showGroup() {
				return this.handleColumnHide('Show Group');
			},
			showBoard() {
				return this.handleColumnHide('Show Board');
			},
			showTask() {
				return this.handleColumnHide('Show Task');
			},
			showUser() {
				return this.handleColumnHide('Show User');
			},
			showComment() {
				return this.handleColumnHide('Show Comment');
			},
			showLastChanges() {
				return this.handleColumnHide('Last changes');
			},
			showBilled() {
				return this.handleColumnHide('Show Billed-Time');
			},
			showTimeUsed() {
				return this.handleColumnHide('Show Used-Time');
			},
			showBillTime() {
				return this.handleColumnHide('Show Bill-Time');
			},
			showBillStatus() {
				return this.handleColumnHide('Show Bill-Status');
			},
			showUnbilled() {
				return this.handleColumnHide('Show Unbilled-Time');
			},
		},
		components: {
			TableComponent,
			TableColumn,
			RowBillingStatusDropDown,
			ThemeTableTr,
		},
		mixins: [
			commentMixin
		],
		methods: {
			disableEditStatus(row) {
				return row.billing_status_id !== 4 && this.selectedDetails;
			},
			formatComment(value) {
				if (value) {
					return value;
				}
			},
			getCriteriaItem(object, name) {
				if (typeof this.criterias[object] === 'undefined') {
					return false;
				}

				return _.find(this.criterias[object], ['name', name]);
			},
			handleColumnHide(name) {
				let criteriaShowItem = this.getCriteriaItem('show', name);
				if (!criteriaShowItem) {
					return false;
				}
				return !!this.selectItems.selectShowOptions.find(element => element === criteriaShowItem.id);
			},
			getHeaderClass(column, index = 0) {
				if (index !== 0) {
					return `column-header-hidden column-${column}`;
				}

				return `column-${column}`;
			},
			calcTableHeadClass(index = 0) {
				if (index === 0) {
					return 'first-head'
				}

				return 'other-head';
			},
			formatUsersNames(names) {
				if (!names) {
					return null;
				}

				let namesLength = names.length - 1,
					others = namesLength > 0 ? ' + ' + namesLength : '';

				return names[0] + others;
			},
			calculateGropedTimeUsed(rows, type) {
				let time_prop = this.getTimeTypeProp(type);

				if (rows.length > 0) {
					if (typeof rows[0].data[time_prop] === 'undefined') {
						return '';
					}

					let total = rows.reduce((acc, value) => {
						return acc.add(this.$moment.duration({
							days: value.data[time_prop].d || 0,
							hours: value.data[time_prop].h || 0,
							minutes: value.data[time_prop].i || 0,
						}));
					}, this.$moment.duration());

					let hours = Math.floor(total.asDays()) * 24 + total.hours(),
						minutes = total.minutes();

					return (hours > 0 ? hours + ' h ' : '') + (minutes > 0 ? minutes + ' m' : '');
				}

				return null;
			},
			getTimeTypeProp(type) {
				switch (type) {
					case 'used':
						return this.selectedDetails ? 'time_used' : 'total_time_used';
					case 'bill':
						return this.selectedDetails ? 'time_bill' : 'total_billed_time';
					case 'billed':
						return 'billed_time';
					case 'unbilled':
						return 'unbilled_time_total';
				}
			}
		}
	}
</script>
