<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        height="auto"
        width="60%"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
        <div v-if="isModalShow" class="bill-modal">
            <div class="bill-modal__filters">
                <div class="row bill-modal__row">
                    <div class="col-sm-4 bill-modal__column_sm">
                        <div class="bill-modal__selected">
                            <span class="bill-modal__selected-text">{{ $t('bill') }}</span>

                            <select class="form-control" v-model="form.billId">
                                <option v-for="item in criteria.bill" :value="item.id">
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4 bill-modal__column_sm">
                        <div class="bill-modal__selected">
                            <span class="bill-modal__selected-text">{{ $t('time_filter') }}</span>

                            <select class="form-control" v-model="form.timeFilterId">
                                <option v-for="item in criteria.time_filter" :value="item.id">
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="bill-modal__selected">
                            <span class="bill-modal__selected-text">{{ $t('board') }}</span>

                            <select class="form-control" v-model="form.boardId">
                                <option :value="null" disabled hidden>{{ $t('select_board') }}</option>
                                <option v-for="item in optionsBoards" :value="item.value">
                                    {{ item.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row bill-modal__row">
                    <div class="col-sm-8 bill-modal__column_sm">
                        <div class="bill-modal__selected">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span class="bill-modal__selected-text">{{ $t('period') }}</span>

                                    <select class="form-control" v-model="form.periodId">
                                        <option v-for="item in criteria.period" :value="item.id">
                                            {{ item.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-sm-3" v-if="isCustomRange" >
                                    <span class="bill-modal__selected-text">{{ $t('date_start') }}</span>
                                    <date-picker
                                        v-model="minDate"
                                        :config="Object.assign(dateTimeMin, { locale: getUserProfile.language.iso_639_1 })"
                                        @dp-change="handleChangeMinDate"
                                        class="form-control-theme__light form-size_s"
                                        ref="pickerMinDate"
                                    />
                                </div>

                                <div class="col-sm-3" v-if="isCustomRange" >
                                    <span class="bill-modal__selected-text">{{ $t('date_end') }}</span>
                                    <date-picker
                                        v-model="maxDate"
                                        :config="Object.assign(dateTimeMax, { locale: getUserProfile.language.iso_639_1 })"
                                        class="form-control-theme__light form-size_s"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bill-modal__selected">
                            <input type="checkbox" class="pull-right multi-select-checkbox" style="margin-top:1px;" v-model="isArchivedBoards" @change="resetSelectBoard">
                            <span class="pull-right bill-modal__selected-text" style="padding-right:10px;">{{ $t('see_archived_boards') }}</span>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="optionsCustomers">
                    <div class="col-sm-4 bill-modal__column_sm">
                        <span class="bill-modal__selected-text">{{ $t('client') }}</span>

                        <select class="form-control" v-model="form.customerId">
                            <option :value="null" disabled selected hidden>{{ $t('select_client') }}</option>
                            <option v-for="item in optionsCustomers" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <span class="bill-modal__selected-text">{{ $t('rate') }}</span>
                        <input type="text" v-model="form.hourlyRate" class="form-control-theme__light form-size_s">
                    </div>
                    <div class="col-sm-4">
                        <span class="bill-modal__selected-text">{{ $t('person') }}</span>
                        <div class="bill-modal__selected-with-button_inline">
                            <div class="bill-modal__selected">
                                <select class="form-control" v-model="form.memberId">
                                    <option v-for="item in optionsMembers" :value="item.value">
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>
                            <button class="button__theme_dark button__size_s" @click="getFields">{{ $t('update') }}</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 bill-modal__column_sm" style="padding: 10px 15px 0;">
                        <div class="bill-modal__selected">
                            <input type="checkbox" class="pull-right multi-select-checkbox" style="margin-top:1px;" v-model="isArchivedCustomers" @change="resetSelectCustomer">
                            <span class="pull-right bill-modal__selected-text" style="padding-right:10px;">{{ $t('see_archived_customers') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bill-modal__table" style="position: relative;">
                <table-component
                        :data="fields"
                        :show-filter="false"
                        :show-caption="false"
                        :head="false"
                        class=""
                        :filter-no-results="$t('filter_no_results')">
                    <template slot="thead">
                        <thead class="table-component__table__head first-head">
                            <ThemeTableTr>
                                <th class="column-date">{{ $t('date') }}</th>
                                <th class="column-project">{{ $t('project_board_task_id_user') }}</th>
                                <th class="">{{ $t('comment') }}</th>
                                <th class="column-time">{{ $t('time_used') }}</th>
                                <th class="column-time">{{ $t('time_bill') }}</th>
                                <th class="">{{ $t('bill_status') }}</th>
                                <th class="column-checkbox text-center">
                                    <input type="checkbox" v-model="selectAll" :disabled="disabledSelectedAll">
                                </th>
                            </ThemeTableTr>
                        </thead>
                    </template>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <span>{{row.end_time | toLocalTime }}</span>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <div class="table-component__task-name">{{row.task_name}}</div>
                            <div class="table-component__board-name">{{row.board_name}}</div>
                            <div class="table-component__user-name">{{row.user_name}}</div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <span>{{ `${row.comment || ''}`.replace(/<[^>]+>/g,'') }}</span>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <span>{{row.time_used | timerToHours}}</span>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <span>{{row.time_bill | timerToHours}}</span>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <div>
                                <RowBillingStatusDropDown
                                    :selectedDetails="row.billing_status_id !== 4"
                                    :row="row"
                                    :month="monthId"
                                    :selectionOptions="billingStatuses">
                                </RowBillingStatusDropDown>
                                <div
                                    v-if="row.billing_status_id === 4 && row.bill_id"
                                    class="bill-modal-download"
                                >
                                    Bill id: <a href="" @click.prevent="downloadPdf(row.bill_id)" class="link_theme_default table-text-link"># {{ row.bills_invoice_number }}</a>
                                </div>
                            </div>
                        </template>
                    </table-column>
                    <table-column  :sortable="false">
                        <template  slot-scope="row">
                            <span><input v-model="selectedField" :value="row" :disabled="row.billing_status_id === 4" type="checkbox"></span>
                        </template>
                    </table-column>
                    <template slot="tfoot">
                        <tr class="table-component-footer-tr">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ totalTimeUsed | timerToHours }}</th>
                            <th>{{ totalTimeBill | timerToHours }}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </template>
                </table-component>
            </div>
            <div class="clearfix">
                <div class="pull-right">
                    <div class="bill-modal-controls" style="width: 120px;">
                        <div class="bill-modal-controls__item">
                            <button class="button__theme_dark button__size_s" :disabled="validateUpdateStatuses" @click="createBill">{{ $t('new_bill') }}</button>
                            <span class="bill-modal-controls__or">{{ $t('or') }}</span>
                        </div>
                        <div class="bill-modal-controls__item">
                            <button class="button__theme_dark button__size_s" :disabled="validateUpdateStatuses" @click="addBill">{{ $t('add_bill') }}</button>
                            <span class="bill-modal-controls__or">{{ $t('or') }}</span>
                        </div>
                        <div class="bill-modal-controls__item">
                            <multiselect
                                v-model="form.status"
                                :options="billingStatuses"
                                track-by="id"
                                label="alias"
                                :allow-empty="false"
                                deselect-label=""
                                open-direction="top"
                                class="multiselect_size_s"
                                :searchable="false"
                                :placeholder="$t('select_bill_status')"
                            >
                                <template slot="singleLabel" slot-scope="props">
                                    <span class="billing-status-block" :style="{ backgroundColor: props.option.color }"></span>
                                    <span>{{ $t(`${props.option.alias.toLowerCase().replace(' ', '_')}`) }}</span>
                                </template>
                                <template slot="option" slot-scope="props">
                                    <span class="billing-status-block" :style="{ backgroundColor: props.option.color }"></span>
                                    <span>{{ $t(`${props.option.alias.toLowerCase().replace(' ', '_')}`) }}</span>
                                </template>
                            </multiselect>
                            <button @click="updateStatuses" :disabled="validateUpdateStatuses" class="button__theme_dark button__size_s">{{ $t('update') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
    import { mapGetters }                   from "vuex";
    import DatePicker                       from 'vue-bootstrap-datetimepicker'
    import clickOutside                     from 'v-click-outside'
	import FileSaver                        from 'file-saver'

    import { TableComponent, TableColumn }  from '@views/elements/vue_table_component/index'
    import RowBillingStatusDropDown         from '@views/partcials/RowBillingStatusDropDown'
    import CommentContent                   from "@assets/js/views/elements/comments/CommentContent";
    import ThemeTableTr from '@views/layouts/theme/ThemeTableTr'

    export default {
        name: "create-bill-modal",
        data() {
            return {
                form: {
                    billId:         1,
                    boardId:        null,
                    customerId:     null,
                    memberId:       0,
                    periodId:       8,
                    timeFilterId:   1,
                    hourlyRate:     0,
                    status:         null,
                },
                criteria: {
                    bill: [
                        {id: 1, name: window.app.$t('all')},
                        {id: 2, name: window.app.$t('billed_time')},
                        {id: 3, name: window.app.$t('no_bill_time_with_parked_time')},
                        {id: 4, name: window.app.$t('no_bill_time_with_parked_time')},
                        {id: 6, name: window.app.$t('no_billed')}
                    ],
                    period: [
                        {id: 1, name: window.app.$t('today')},
                        {id: 2, name: window.app.$t('yesterday')},
                        {id: 3, name: window.app.$t('this_week')},
                        {id: 4, name: window.app.$t('last_week')},
                        {id: 5, name: window.app.$t('last_14_days')},
                        {id: 6, name: window.app.$t('this_month')},
                        {id: 7, name: window.app.$t('last_month')},
                        {id: 8, name: window.app.$t('custom')}
                    ],
                    time_filter: [
                        {id: 1, name: window.app.$t('all')},
                        {id: 2, name: window.app.$t('no_time_under.two_min')},
                        {id: 3, name: window.app.$t('no_time_under.five_min')},
                        {id: 4, name: window.app.$t('no_time_under.fifteen_min')},
                        {id: 5, name: window.app.$t('no_time_under.thirty_min')},
                        {id: 6, name: window.app.$t('no_time_under.sixty_min')}
                    ]
                },
                monthId: null,
                isArchivedCustomers: false,
                isArchivedBoards: false,
                isBoardLoading: false,
                isCustomerLoading: false,
                isGroupLoading: false,

                customTimeRange: {},
                minDate: '',
                maxDate: '',
                dateTimeMin:{
                    format: 'YYYY-MM-DD',
                    showClose: true
                },
                dateTimeMax: {
                    format: 'YYYY-MM-DD',
                    showClose: true,
                    useCurrent: false
                },
                year: '',
                fetchField: true,
                fields: [],
                totalTimeUsed: '',
                totalTimeBill: '',
                selectedField: [],
                errors: true,
            }
        },
        computed: {
            ...mapGetters({
                owner:              'members/getOwner',
                members:            'members/getOnlyMembers',
                boards:             'reports/getBoards',
                billingStatuses:    'default/getBillingStatuses',
                activeCustomers:    'customers/getActiveCustomers',
                archiveCustomers:   'customers/getArchiveCustomers',
				getUserProfile:     'user/getUserProfile',
			}),
            isModalShow() {
                return this.isBoardLoading && this.isCustomerLoading && this.isGroupLoading
            },
            isCustomRange() {
                return this.form.periodId === 8
            },
            optionsCustomers() {
                return this.isArchivedCustomers ? this.archiveCustomers : this.activeCustomers;
            },
            optionsMembers() {
                let options = [
                    {value: 0, label: window.app.$t('all')},
                ];

                options.push({value: this.owner.id, label: "Me"});

                this.members.map(member => {
                    options.push({value: member.id, label: `${member.user.last_name} ${member.user.name}`});
                });

                return options;
            },
            optionsBoards() {
                let options = [];

                this.boards.filter(board => {
                    if (board.is_archive === this.isArchivedBoards) {
                        options.push({value: board.id, label: board.name});
                    }
                });

                return options;
            },


            disabledSelectedAll() {
                let fields = this.fields.filter(item => item.billing_status_id !== 4);

                return !fields.length;
            },
            selectAll: {
                get() {
                    let fields = this.fields.filter(item => item.billing_status_id !== 4);

                    return fields.length ? this.selectedField.length === fields.length : false;
                },
                set(value) {
                    let selected = [];

                    if (value) {
                        this.fields.forEach((post) => {
                            if(post.billing_status_id !== 4){
                                selected.push(post);
                            }
                        });
                    }

                    this.selectedField = selected;
                }
            },
            validateUpdateStatuses() {
                return this.fields ? !this.selectedField.length : true;
            },
        },
        watch: {
            'form.customerId': function(option) {
                let customer = this.optionsCustomers.find(item => {
                    return item.id === option;
                });

                if (customer) {
                    this.form.hourlyRate = (customer.hourly_rate + "").replace(/\./, ",");
                }
            },
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        components: {
            CommentContent,
            TableComponent,
            TableColumn,
            DatePicker,
            RowBillingStatusDropDown,
            ThemeTableTr,
        },
        methods: {
			downloadPdf(billId){
				this.$api.bills.downloadPdf(billId)
                    .then(data => {
					    FileSaver.saveAs(data.content, data.filename);
				    })
                    .catch(error => {
						this.$notify({type:'error', text: this.$t('bill_not_found')});
                    });
			},
            beforeOpen(event) {
                let month = this.$moment().format('MMMM');

                this.year = event.params.year;

                this.$api.customers.getCustomers().then(data => {
                    if (data.clients && !data.clients.length) {
                        this.closeModal();
                        return this.$notify({type:'error', text: this.$t('havent_clients')});
                    }
                    this.isCustomerLoading = true;

                    this.$api.reports.getBoards().then(() => {
                        this.isBoardLoading = true;
                    }).catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });

                    this.$api.reports.getGroups().then(() => {
                        this.isGroupLoading = true;
                    }).catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });

                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });


                if (event.params.month) {
                    month = event.params.month;
                }

                this.monthId = this.$moment().month(month).format("M") - 1;

                this.minDate = this.$moment().set('year', event.params.year).set('month', this.monthId).startOf('month').format('YYYY-MM-DD');
                this.maxDate = this.$moment().set('year', event.params.year).set('month', this.monthId).endOf('month').format('YYYY-MM-DD');

                if (event.params.boardId) {
                    this.form.boardId = event.params.boardId;

                    let board = this.boards.find(item => item.id === this.form.boardId);

                    if (board) {
                        this.isArchivedBoards = board.is_archive;
                    }
                }

                this.getFields(false);
            },
            beforeClose() {
                this.resetComponentData()
            },
            closeModal() {
                this.$modal.hide('create-bill-modal')
            },
            resetSelectCustomer() {
                this.form.customerId = null;
                this.form.hourlyRate = 0;
            },
            resetSelectBoard() {
                this.form.boardId = null;
            },

            handleChangeMinDate(e) {
                if(this.$moment(this.minDate).diff(this.maxDate, 'days') > 0){
                    this.maxDate = this.minDate;
                }
            },
            notifyError(notify = true) {
                let textErrors = [];

                if (this.form.periodId === 8 && (!this.minDate || !this.maxDate)){
                    textErrors.push('Please select the custom period.');
                }

                if(!this.form.boardId) {
                    textErrors.push(this.$t('board_field_is_required'));
                }

                if (notify) {
                    textErrors.forEach(item => this.$notify({type:'error', text: item}));
                }

                this.errors = textErrors.length !== 0
            },
            getFields(notify = true) {
                this.notifyError(notify);

                if(this.errors) {
                    return
                }

                let data = {
                    'selectPeriod':       [this.form.periodId],
                    'selectBill':         [this.form.billId],
                    'selectTimeFilter':   [this.form.timeFilterId],
                    'selectMembers':      [this.form.memberId],
                    'selectBoards':       [this.form.boardId],
                    'customTimerange':    [
                        this.minDate,
                        this.maxDate,
                    ],
                };

                this.$api.bills.getFilter(data).then(data => {
                    this.fields = data.records.records;

                    if(this.selectedField.length){
                        this.selectedField = [];

                        this.selectedField.forEach(field => {
                            this.fields.forEach(item => {
                                if(field.timer_billing_id === item.timer_billing_id) {
                                    this.selectedField.push(item);
                                }
                            });
                        });
                    }

                    this.totalTimeUsed = data.records.totalTimeUsed;
                    this.totalTimeBill = data.records.totalTimeBill;
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            createBill() {
                if (!this.form.customerId) {
                    return this.$notify({type:'error', text: this.$t('create_bill_error')});
                }

                this.$api.bills.createBill({
                    "customerId":   this.form.customerId,
                    "billTimers":   this.getBillTimers(),
                    "rate":         parseFloat(this.form.hourlyRate.replace(/\,/, ".")).toFixed(2),
                }).then(data => {
                    this.closeModal();
                    this.$modal.show('edit-bill-modal', {bill: data.bill});
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            updateStatuses() {
                let timerBillingId = this.selectedField.map(item => {
                    return item.timer_billing_id;
                });

                this.$api.bills.updateStatuses({
                    "timerBillingIds": timerBillingId,
                    "billingStatusId": this.form.status.id
                }).then(res => {
                    this.$t('update_bill');
                    this.$event.$emit('update-year-overview', this.form.boardId, this.monthId);
                    this.getFields();
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            getBillTimers() {
                let fields = this.selectedField.filter(function(item){
                    return item.billing_status_id !== 4;
                });

                return fields.map( item => {
                    return {
                        "timerBillingId":       item.timer_billing_id,
                        "timerBillingStatusId": item.billing_status_id
                    }
                });
            },
            addBill () {
                if (!this.form.customerId) {
                    return this.$notify({type:'error', text: this.$t('create_bill_error')});
                }

                this.$modal.show('add-bill-modal', {
					start_month: this.$moment(this.minDate).format("YYYY-MM-DD HH:mm:ss"),
					end_month: this.$moment(this.maxDate).format("YYYY-MM-DD HH:mm:ss"),
					client: this.form.customerId,
					billTimers: this.getBillTimers(),
                });
                this.closeModal();
            },
        }
    }
</script>

<style lang="scss">
    #create-bill-modal {
        select.form-control {
            border: none;
            height: auto;
            padding: 5px 5px 5px 6px;
        }
    }
</style>
