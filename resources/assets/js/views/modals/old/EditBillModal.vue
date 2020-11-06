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
        @before-close="beforeClose">

        <div v-if="form.billId" class="bill-modal">
            <div class="edit-bill-table table-component__theme_green" style="margin-bottom: 20px;">
                <table>
                    <tr>
                        <td>{{ form.billInvoiceNumber }}</td>
                        <td style="width: 30%">{{ selectCustomer.name }}</td>
                        <td style="width: 10%;">{{ form.billDate }}</td>
                        <td>{{ totalTime }}</td>
                        <td>{{ totalRate }}</td>
                        <td>
                            <a
                                :class="{'disabled': isDraftBill}"
                                class="link_theme_default"
                                href="#"
                                @click.prevent="downloadPdf"
                            >PDF</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div>
                <div class="bill-modal__filters">
                    <div class="row bill-modal__row">
                        <div class="col-sm-6 bill-modal__column_sm">
                            <div class="bill-modal__selected bill-modal__input_inline">
                                <span class="bill-modal__selected-text">{{ $t('clients') }} </span>

                                <multiselect
                                    v-model="form.customerId"
                                    :options="optionsCustomers"
                                    :searchable="false"
                                    :close-on-select="true"
                                    :show-labels="false"
                                    trackBy="id"
                                    :placeholder="$t('select_bill_layout')"
                                    @change="changeSelectCustomer"
                                >
                                    <template slot="singleLabel" slot-scope="{ option }">
                                        <span>{{ option.name }}</span>
                                    </template>
                                    <template slot="option" slot-scope="{ option }">
                                        <span>{{ option.name }}</span>
                                    </template>
                                </multiselect>
                            </div>
                        </div>
                        <div class="col-sm-6 bill-modal__column_sm ">
                            <div class="bill-modal__input_inline">
                                <span class="bill-modal__selected-text">{{ $t('bill_date') }}</span>
                                <date-picker
                                    v-model="form.billDate"
                                    class="form-control-theme__light form-size_s"
                                    :config="Object.assign(datetimeOptions, { locale: getUserProfile.language.iso_639_1 })"
                                ></date-picker>
                            </div>
                        </div>
                    </div>
                    <div class="row bill-modal__row">
                        <div class="col-sm-6 bill-modal__column_sm">
                            <div class="bill-modal__selected">
                                <input
                                    v-model="isArchivedCustomers"
                                    type="checkbox"
                                    class="pull-right multi-select-checkbox"
                                    style="margin-top:1px;"
                                    @change="changeSelectCustomer">
                                <span class="pull-right bill-modal__selected-text" style="padding-right:10px;">{{ $t('see_archived_customers') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row bill-modal__row">
                        <div class="col-sm-6 bill-modal__column_sm">
                            <div class="bill-modal__input_inline">
                                <span class="bill-modal__selected-text">{{ $t('bill_rate') }}</span>
                                <input
                                    v-model="form.customerRate"
                                    type="text"
                                    class="form-control form-control-theme__light form-size_s"
                                    @input="handleChangeCustomerRate"
                                >
                            </div>
                        </div>
                        <div class="col-sm-6 bill-modal__column_sm bill-modal__input_inline">
                            <span class="bill-modal__selected-text">{{ $t('bill_layout') }}</span>

                            <multiselect
                                v-model="form.billLayoutTypeId"
                                :options="billLayoutTypes"
                                :searchable="false"
                                :close-on-select="true"
                                :show-labels="false"
                                trackBy="id"
                                :placeholder="$t('select_bill_layout')"
                            >
                                <template slot="singleLabel" slot-scope="{ option }">
                                    <span>{{ $t(`bill_form_${option.name}`) }}</span>
                                </template>
                                <template slot="option" slot-scope="{ option }">
                                    <span>{{$t(`bill_form_${option.name}`) }}</span>
                                </template>
                            </multiselect>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 bill-modal__column_sm bill-modal__input_inline">
                            <span class="bill-modal__selected-text">{{ $t('bill_status') }}</span>

                            <multiselect
                                v-model="form.billStatus"
                                :options="billStatuses"
                                :searchable="false"
                                :close-on-select="true"
                                :show-labels="false"
                                :placeholder="$t('select_bill_status')"
                            >
                                <template slot="singleLabel" slot-scope="{ option }">
                                    <span>{{ option | capitalize }}</span>
                                </template>
                                <template slot="option" slot-scope="{ option }">
                                    <span>{{ option | capitalize }}</span>
                                </template>
                            </multiselect>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bill-modal__table table-component_first-number" style="position: relative;">
                <table-component
                    :data="billTimers"
                    :show-filter="false"
                    :show-caption="false"
                    :head="false"
                    class="table-component__theme_black"
                    :filter-no-results="$t('filter_no_results')"
                >
                    <template slot="thead">
                        <thead class="table-component__table__head first-head">
                            <ThemeTableTr>
                                <th class="text-center">#</th>
                                <th style="width: 25%; min-width: 120px;"> {{ $t('project_task_id') }}</th>
                                <th style="width: 35%; min-width: 240px;">{{ $t('comment') }}</th>
                                <th class="text-center">{{ $t('quantity') }}</th>
                                <th class="text-center">{{ $t('unit') }}</th>
                                <th class="text-center">{{ $t('euro_unit') }}</th>
                                <th class="text-center">&euro;</th>
                                <th></th>
                            </ThemeTableTr>
                        </thead>
                    </template>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <textarea
                                v-if="! row.id"
                                v-model="row.title"
                                style="min-height: 58px;"
                                class="form-control textarea-vertical-resize"
                            />
                            <p v-if="row.id">{{ row.title }}</p>
                            <div style="height: 20px;"></div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <textarea style="min-height: 58px;" class="form-control textarea-vertical-resize" v-model="row.comment" />
                            <div>{{ $t('from') }} {{row.user.name}} {{ $t('in_the') }} {{row.end_time | toLocalTime }}</div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <input class="form-control edit-bills-table__input" type="text" v-model="row.time">
                            <div style="height: 20px;">was: {{row.present}}</div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <input class="form-control edit-bills-table__input" type="text" v-model="row.unit">
                            <div style="height: 20px;"></div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <input class="form-control edit-bills-table__input" type="text" v-model="row.rate">
                            <div style="height: 20px;"></div>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <p style="margin: 0 0 22px">{{ (row.time * comaToNumber(row.rate)).toFixed(2) | replaceDot }}</p>
                        </template>
                    </table-column>
                    <table-column :sortable="false">
                        <template slot-scope="row">
                            <button class="button__theme_dark button_icon_size_s fa fa-close" :disabled="isLoading" @click="removeBillTimer(row.vueTableComponentInternalRowId)"></button>
                        </template>
                    </table-column>
                </table-component>
            </div>
            <div class="bill-modal-controls bill-modal-controls--edit">
                <div class="bill-modal-controls-col">
                    <div class="bill-modal-controls-item">
                        <div class="bill-modal-controls-tabs">
                            <div
                                v-for="item in tabs"
                                :key="item.val"
                                :class="['bill-modal-controls-tab', { active : currentTab === item.val }]"
                                @click="currentTab = item.val"
                            >{{ item.name }}</div>
                        </div>
                    </div>
                </div>
                <div class="bill-modal-controls-col">
                    <div class="bill-modal-controls-item">
                        <button
                            :disabled="isLoading"
                            class="button__theme_dark button__size_s"
                            @click="addBillTimer"
                        >
                            {{ $t('add_position') }}
                        </button>
                    </div>
                    <div class="bill-modal-controls-item">
                        <button
                            v-if="!isDraftBill"
                            :disabled="isLoading"
                            class="button__theme_dark button__size_s"
                            @click="removeBill"
                        >
                            {{ $t('delete_bill') }}
                        </button>
                    </div>
                    <div class="bill-modal-controls-item">
                        <button
                            :disabled="isLoading"
                            class="button__theme_dark button__size_s"
                            @click="updateBill"
                        >
                            {{ $t('save') }}
                        </button>
                    </div>
                </div>
            </div>
            <div
                v-if="currentTab === 'comment'"
                class="bill-modal-comment"
            >
                <label class="bill-modal__selected-text">{{ $t('bill_comment') }}</label>
                <textarea
                     v-model="form.billComment"
                     rows="2"
                     class="form-control textarea-vertical-resize"
                 ></textarea>
            </div>
            <div
                v-if="currentTab === 'logs'"
                class="bill-modal-logs"
            >
                <div
                    v-if="!logs.length"
                    class="bill-modal-logs-empty"
                >{{ $t('bill_empty_log') }}</div>

                <div
                    v-if="logs.length"
                    class="bill-modal-logs-list"
                >

                    <div class="bill-modal-logs-header">
                        <span>{{ $t('action') }}</span>
                        <span>{{ $t('comment') }}</span>
                        <span>{{ $t('time') }}</span>
                    </div>

                    <div
                        v-for="(item, index) in logs"
                        :key="index"
                        class="bill-modal-logs-content"
                    >
                        <span>{{ item.action }}</span>
                        <span>{{ item.comment }}</span>
                        <span>{{ item.created_at }}</span>
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

    import timeMixin                        from '@mixins/time'
    import { TableComponent, TableColumn }  from '@views/elements/vue_table_component/index'
    import RowBillingStatusDropDown         from '@views/partcials/RowBillingStatusDropDown'
    import ThemeTableTr from '@views/layouts/theme/ThemeTableTr'

    export default {
        name: "edit-bill-modal",
		mixins:[
			timeMixin
		],
		components: {
			TableComponent,
			TableColumn,
			DatePicker,
			RowBillingStatusDropDown,
			ThemeTableTr
		},
		directives: {
			'clickOutside': clickOutside.directive
		},
		data() {
            return {
                isSaved: false,
                isDraftBill: false,
                form: {
                    billId:             null,
                    billInvoiceNumber:  null,
                    billDate:           null,
                    billLayoutTypeId:   null,
					billStatus:         null,
                    billSumTime:        0,
                    billSumRate:        0,
                    billTimers:         [],
					billComment:        null,
                    customerId:         null,
                    customerRate:       null,
                },
                billTimers: [],
                currentUser: {},
                isArchivedCustomers: false,
                datetimeOptions: {
                    format: 'YYYY-MM-DD HH:mm:ss',
                    toolbarPlacement: 'bottom',
                    showTodayButton: true,
                    showClose: true,
                    showClear: true,
                },
                defaultTitle: 'Project N/A\nTask N/A',
                billStatuses: [
                    window.app.$t('paid'),
                    window.app.$t('draft'),
                    window.app.$t('finished'),
                    window.app.$t('cancelled'),
				],

                currentTab: 'comment',
                tabs: [{
                	val: 'comment',
                    name: window.app.$t('bills_add_comment')
                }, {
					val: 'logs',
					name: window.app.$t('show_logs')
                }],
                logs: []
            }
        },
        computed: {
            ...mapGetters({
                billLayoutTypes:    'default/getBillLayoutTypes',
                customerStatuses:   'default/getCustomerStatuses',
                activeCustomers:    'customers/getActiveCustomers',
                archiveCustomers:   'customers/getArchiveCustomers',
				getUserProfile:     'user/getUserProfile',
			}),
            optionsCustomers() {
                return this.isArchivedCustomers ? this.archiveCustomers : this.activeCustomers;
            },
            selectCustomer() {
				return this.form.customerId ? this.form.customerId : {};
			},
            selectBillLayoutTypes() {
                return this.form.billLayoutTypeId ? this.form.billLayoutTypeId.id : {};
            },
            totalTime() {
                let time = 0;

                this.billTimers.map((item) => {
                    time += parseFloat(item.time);
                });

                return time.toFixed(2);
            },
            totalRate() {
                let rate = 0;

                this.billTimers.map((item) => {
                    rate += item.time * this.comaToNumber(item.rate);
                });

                return rate.toFixed(2).replace(/\./, ",");
            },
        },
        // watch: {
        //     "form.customerRate": function(option) {
        //         if(this.billTimers.length > 0) {
        //             this.billTimers.map((timer) => {
        //                 timer.rate = option;
        //             })
        //         }
        //     },
        // },
        methods: {
            beforeOpen(event) {
                this.$api.customers.getCustomers().then().catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });

                if (!event.params) {
                    return;
                }
                if (event.params.bill) {
                    this.isDraftBill = true;
                    this.setFields(event.params.bill);
                }

                if (event.params.billId) {
                    this.getFields(event.params.billId)
                }
            },
            beforeClose(event) {
                if (this.isDraftBill && !this.isSaved) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('save_bill'),
                        body: this.$t('bill_was_not_saved'),
                        confirmCallback: () => {
                            this.updateBill();
                        },
                        cancelCallback: () => {
                            this.isSaved = true;
                            this.closeModal();
                        }
                    });

                    return event.stop();
                }

                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('create-bill-modal');
            },
            changeSelectCustomer(customer, id) {
                if (customer) {
                    this.form.customerId = customer.id;
                    this.form.customerRate = customer.hourly_rate;

                    return;
                }

                this.form.customerId = null;
                this.form.customerRate = 0;
            },
            downloadPdf(){
                if (this.isDraftBill) {
                    return this.$notify({ type:'error', text: this.$t('save_bill_before_download') });
                }
                this.$api.bills.downloadPdf(this.form.billId)
                    .then((data) => {
                        FileSaver.saveAs(data.content, data.filename);
                    });
            },
            comaToNumber (value) {
                return parseFloat(value.toString().replace(/\,/, "."));
            },
            handleChangeCustomerRate() {
                if (this.billTimers.length > 0) {
                    this.billTimers.map((timer) => {
                        timer.rate = this.form.customerRate;
                    })
                }
            },

            getFields(billId) {
                this.$api.bills.getBill(billId).then(data => {
                    this.setFields(data.bill);
                });
            },
            setFields(data) {
                this.form.billId            = data.id;
                this.form.billInvoiceNumber = data.invoice_number;
                this.form.billStatus        = data.status;
                this.form.billDate          = this.toLocalTime(data.bill_date);
                this.form.billLayoutTypeId  = data.bill_layout.bill_layout_type;
                this.form.customerId        = data.customer;
                this.form.customerRate      = data.rate.toString().replace(/\./, ",");
                // this.form.customerRate      = data.rate;

                if (!data.customer.status) {
                    this.isArchivedCustomers = true;
                }

                this.billTimers       = data.bill_timers;
                this.currentUser      = data.user;

                this.logs = data.logs;

                this.billTimers.forEach(timer => {
                	if (!timer.title) {
                        timer.title = this.defaultTitle;
                    }

                    timer.time          = timer.present = this.getTimeHours(timer.time);
                    timer.rate          = timer.rate.toString().replace(/\./, ",");

                    let timer_billing = data.timer_billings.find(item => item.id === timer.timer_billing_id);

                    if (timer_billing) {
                        if (timer_billing.timer.task) {
                            timer.title  = `${timer_billing.timer.task.board[0].name} ${timer_billing.timer.task.name}`;
                        }

                        if (this.isDraftBill) {
                            timer.comment = `${timer.comment || timer_billing.timer.comment || ''}`;
                        }

                        if (timer.comment) {
                            timer.comment = timer.comment.replace(/<[^>]+>/g,'')
                        }
                    }
                });
            },
            addBillTimer() {
                this.billTimers.push(Object.assign({}, {
                    id:         null,
                    rate:       this.form.customerRate,
                    time:       0,
					title:      this.defaultTitle,
                    present:    0,
                    comment:    null,
                    user:       this.currentUser,
                    end_time:   this.$moment().format('YYYY-MM-DD HH:mm:ss')
                }));
            },
            removeBillTimer(id) {
                this.billTimers = this.billTimers.filter((item) => {
                    return item.vueTableComponentInternalRowId !== id;
                });
            },
            updateBill() {
                let billTimers = this.billTimers.map(item => {
                    let totalSeconds = parseInt(item.time * 60 * 60, 10);

                    let amount = (item.time * this.comaToNumber(item.rate)).toFixed(2);

                    let hours   = Math.floor(totalSeconds / 3600);
                    let minutes = Math.floor(totalSeconds / 60) % 60;
                    let seconds = totalSeconds % 60;

                    let billTimerTime = [hours,minutes,seconds].map(value => value < 10 ? "0" + value: value).join(":");

                    return {
                        'billTimerId':          item.id,
                        'billTimerRate':        this.comaToNumber(item.rate),
                        'billTimerComment':     item.comment,
                        'billTimerUserId':      item.user.id,
                        'billTimerAmount':      amount,
                        'billTimerTime':        billTimerTime,
                        'billUnit':             item.unit,
                        'billTitle':            item.title,
                        'timerBillingId':       item.timer_billing_id || null,
                    }
                });

                let data = {
                    'customerId':           this.selectCustomer.id,
                    'customerRate':         this.comaToNumber(this.form.customerRate),
                    'billId' :              this.form.billId,
                    'billDate':             this.toUTCTime(this.form.billDate),
                    'billLayoutTypeId':     this.selectBillLayoutTypes,
                    'billSumTime':          this.totalTime,
                    'billSumRate':          this.comaToNumber(this.totalRate),
                    'billStatus':           this.form.billStatus,
                    'billComment':          this.form.billComment,
                    'billInvoiceNumber':    this.form.billInvoiceNumber,
                    'billTimers':           billTimers
                };

                this.$api.bills.updateBill(data).then(data => {
                    if(this.$route.path !== '/bills'){
                        this.$router.push('/bills');
                    } else {
                        this.$event.$emit('update-bill', data.bill);
                    }

                    this.isSaved = true;
                    this.$modal.hide('edit-bill-modal');
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            removeBill() {
                this.$event.$emit('show-modal-remove-bill', this.form.billId);
            },
        }
    }
</script>

<style lang="scss">
    #edit-bill-modal {
        select.form-control {
            border: none;
            height: auto;
            padding: 5px 5px 5px 6px;
        }
    }
    .link_theme_default.disabled {
        opacity: 0.5
    }
    .table-component .table-component__table-wrapper table .table-component__table__head.first-head tr th {
        color: #FFFFFF;
    }

    .bill-modal-controls--edit {
        display: flex;
        justify-content: space-between;
        align-items: center;

        .bill-modal-controls-col {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .bill-modal-controls-item {

            button {
                margin: 0 5px;
                border: none;
                border-radius: 5px;
                line-height: 1;
                background-color: #5d98de;
                box-shadow: 0 1px 6px rgba(57, 73, 76, 0.35);
            }
        }

        .bill-modal-controls-tabs {
            display: flex;
        }

        .bill-modal-controls-tab {
            border: 2px solid #5d98de;
            transition: color 0.3s, background-color 0.3s;
            padding: 5px 10px;
            cursor: pointer;

            &:first-child {
                border-right: 0;
            }

            &.active {
                color: #fff;
                background-color: #5d98de;
            }
        }
    }

    .bill-modal-logs {
        padding: 20px 0;

        &-empty {
            padding: 10px;
            border-radius: 5px;
            background-color: #ebebec;
            text-align: center;
            font-size: 18px;
        }

        &-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: #ebebec;

            span {
                width: 33.3333%;
                padding: 5px 0 5px 30px;
                border-right: 1px solid #cecdcd;
                font-weight: bold;

                &:last-child {
                    border-right: 0;
                }
            }
        }

        &-content {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-color: #fbfbfb;
            margin: 10px 0;

            span {
                width: 33.3333%;
                padding: 5px 0 5px 30px;
            }
        }
    }

    .bill-modal-comment {
        padding: 20px 0;
    }
</style>
