<template>
    <div class="bills-list" v-if="isLoggedIn">
        <template>
            <fixed-component is-scroll >
                <div class="pagination-mobile pagination_inline">
                    <div class="pagination-text">
                        {{ $t("bill_overview") }}
                    </div>
                    <YearPagination
                        :years="yearsDate"
                        :current="yearsDate[paginationCurrent - 1]"
                        @page-changed="value => this.onChangeFilter('paginationCurrent', value)"
                    />
                </div>
                <div class="bills-navbar bills-navbar-desktop">
                    <div class="pagination-desktop pagination_inline">
                        <div class="pagination-text">
                            {{ $t("bill_overview") }}
                        </div>
                        <YearPagination
                            :years="yearsDate"
                            :current="yearsDate[paginationCurrent - 1]"
                            @page-changed="value => this.onChangeFilter('paginationCurrent', value)"
                        />
                    </div>
                    <div class="bills-select-month">
                        <multiselect
                            :value="selectMonth.start"
                            :options="months.start"
                            track-by="id"
                            label="name"
                            :allow-empty="false"
                            deselect-label=""
                            class="multiselect_size_s"
                            :searchable="false"
                            @select="value => this.onChangeFilter('selectMonth.start', value, this.changeMonthOptionsStartDisabled)"
                        >
                            <template slot-scope="{ option }">
                                {{ option.name }}
                            </template>
                        </multiselect>
                        <span class="separator-dash">-</span>
                        <multiselect
                            :value="selectMonth.end"
                            :options="months.end"
                            track-by="id"
                            label="name"
                            :allow-empty="false"
                            deselect-label=""
                            class="multiselect_size_s"
                            :searchable="false"
                            @select="value => this.onChangeFilter('selectMonth.end', value, this.changeMonthOptionsEndDisabled)"
                        >
                            <template slot-scope="{ option }">
                                {{ option.name }}
                            </template>
                        </multiselect>
                    </div>
                    <div class="select-client">
                        <multiselect
                            v-model="selectClient"
                            :options="getCustomersWithAll"
                            track-by="id"
                            label="name"
                            deselect-label=""
                            @select="value => this.onChangeFilter('selectClient', value)"
                            class="multiselect_size_s"
                            :searchable="false"
                        >
                        </multiselect>
                    </div>
                    <div v-if="false" class="button-holder">
                        <button class="button__icon" :disabled="isLoading" >
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

            <div class="bills-navbar bills-navbar-mobile">
                <div class="select-client">
                    <multiselect
                        v-model="selectClient"
                        :options="getCustomersWithAll"
                        track-by="id"
                        label="name"
                        deselect-label=""
                        class="multiselect_size_s"
                        @select="value => this.onChangeFilter('selectClient', value)"
                        :searchable="false"
                    >
                    </multiselect>
                </div>
            </div>
            </fixed-component>
        </template>
        <div class="bill-table">
            <div class="table-component table-component__theme_black" v-if="bills.length > 0">
                <div class="table-component__table-wrapper">
                    <table class="table-component__table" >
                        <thead >
                            <template>
                            <ThemeTableTr>
                                <th class="text-center">#</th>
                                <th class="text-center">{{ $t("bill_number") }}</th>
                                <th class="text-center" style="width: 30%;">{{ $t("customer") }}</th>
                                <th class="text-center" style="min-width: 100px">{{ $t("date") }}</th>
                                <th class="text-center" style="min-width: 100px">{{ $t("hours") }}</th>
                                <th class="text-center" style="min-width: 100px">{{ $t("amount") }}</th>
                                <th class="text-center" style="min-width: 50px">PDF</th>
                                <th class="text-center" style="min-width: 100px">{{ $t("control") }}</th>
                            </ThemeTableTr>
                            </template>
                        </thead>

                        <tbody class="table-component__table__body">
                            <tr v-for="(bill, index) in billSortById">
                                <td class="text-center">
                                    <span>{{ index + 1 }}</span>
                                </td>
                                <td class="text-center">
                                    <span>{{ bill.invoice_number }}</span>
                                </td>
                                <td>
                                    <span>{{ bill.customer_name }}</span>
                                </td>
                                <td class="text-center">
                                    <span>{{ bill.bill_date | toLocalTime('YYYY-MM-DD') }}</span>
                                </td>
                                <td class="text-center">
                                    <span>{{ bill.time }}</span>
                                </td>
                                <td class="text-center">
                                    <span>{{ bill.amount | replaceDot }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="" @click.prevent="downloadPdf(bill.id)" class="download-btn link_theme_default table-text-link">
                                        <!-- <i class="fa fa-download" aria-hidden="true" style="color:#636b6f;font-size:21px;"></i> -->
                                        <i class="icon-download">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-download">
                                                </use>
                                            </svg>
                                        </i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a @click="handleShowEditBill(bill.id)" class="edit-bill-btn link_theme_default table-text-link">
                                        <!-- <i class="fa fa-pencil-square" aria-hidden="true" style="color:#2f91ed;padding:0 10px 6px 0;font-size:21px;"></i> -->
                                        <i class="icon-pencil">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-pencil">
                                                </use>
                                            </svg>
                                        </i>
                                    </a>
                                    <a @click="removeBill(bill.id)" class="link_theme_default table-text-link">
                                        <!-- <i class="fa fa-trash" aria-hidden="true" style="color:red;padding:0 0 6px 0;font-size:21px;"></i> -->
                                        <i class="icon-trash">
                                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                                    xlink:href="#icon-trash">
                                                </use>
                                            </svg>
                                        </i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bill-table-not-found" v-if="bills.length === 0">
                {{ $t("bills_not_found") }}
            </div>
        </div>
        <theme-button-success @click="addNewBill" class="btn">
            <span @click="addNewBill">{{ $t("bill_add") }}</span>
        </theme-button-success>
    </div>
</template>
<script>
    import { mapGetters }                   from "vuex"
    import FileSaver                        from 'file-saver'

    import { TableComponent, TableColumn }  from '@views/elements/vue_table_component'
    import YearPagination                   from '@views/elements/year_pagination/YearPagination'
    import config                           from '@config'
    import ThemeTableTr                     from '@views/layouts/theme/ThemeTableTr'
    import ThemeButtonSuccess               from '@views/layouts/theme/buttons/ThemeButtonSuccess'
    import FixedComponent                   from '@views/components/fixedComponent/FixedComponent';
    import {_setDocumentTitle} from "@helpers/controlDocumentTitle";

    const configMonth = [
        { id: 0, name:  window.app.$t('january'), $isDisabled: false },
        { id: 1, name:  window.app.$t('february'), $isDisabled: false },
        { id: 2, name:  window.app.$t('march'), $isDisabled: false },
        { id: 3, name:  window.app.$t('april'), $isDisabled: false },
        { id: 4, name:  window.app.$t('may'), $isDisabled: false },
        { id: 5, name:  window.app.$t('june'), $isDisabled: false },
        { id: 6, name:  window.app.$t('july'), $isDisabled: false },
        { id: 7, name:  window.app.$t('august'), $isDisabled: false },
        { id: 8, name:  window.app.$t('september'), $isDisabled: false },
        { id: 9, name:  window.app.$t('october'), $isDisabled: false },
        { id: 10, name: window.app.$t('november'), $isDisabled: false },
        { id: 11, name: window.app.$t('december'), $isDisabled: false },
    ];

    export default {
        data(){
            return{
                yearsDate: [],
                selectMonth: {
                    start: configMonth[0],
                    end: configMonth[11],
                },
                paginationCurrent: 1,
                bills: [],
                months: {
                    start: configMonth,
                    end: configMonth,
                } ,
                selectClient: null,
            }
        },
        computed: {
            ...mapGetters({
                getCustomers: 'customers/getCustomers',
            }),
			getCustomersWithAll() {
				if (this.getCustomers.length) {
					return [{
						'id': null,
						'name': this.$t('all')
					}].concat(this.getCustomers);
				}
				return [];
			},
            billSortById() {
                return this.bills.sort((a, b) => {
                    return a.id - b.id;
                });
            },
        },
        created() {
            if (!this.checkPermission('read-billing', true)) {
                return this.$router.push({name: 'home'});
            }

            this.generateYears("January 1, 2000 12:00:00");
            this.setCurrentMonth();
            // this.changeMonthOptionsStartDisabled({ value: this.selectMonth.start });
            // this.changeMonthOptionsEndDisabled({ value: this.selectMonth.end });
            this.receiveBills();

            this.$api.reports.getBoards().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$api.reports.getGroups().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$event.$on('update-bill',              this.updateBill);
            this.$event.$on('show-modal-remove-bill',   this.removeBill);
        },
        components: {
            YearPagination,
            TableComponent,
            TableColumn,
            ThemeTableTr,
            ThemeButtonSuccess,
            FixedComponent
        },
        mounted() {
            if (window.innerWidth < config.size.tablet) {
                return this.$router.push({name: 'home'});
            }

            if (!this.checkPermission('read-billing', true)) {
                return this.$router.push({name: 'home'});
            }
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });

            this.$api.customers.getCustomers();
        },
        methods: {
            onChangeFilter(type, value, call) {
                if (this.$lodash.get(this, type) === value) {
                    return
                }

                this.$lodash.set(this, type, value);
                this.receiveBills();
            },
            changeMonthOptionsStartDisabled({ value }) {
                this.months['end'] = this.months['end'].map(item => {
                    return {
                        ...item,
                        $isDisabled: value.number > item.number,
                    }
                })
            },
            changeMonthOptionsEndDisabled({ value }) {
                this.months['start'] = this.months['start'].map(item => {
                    return {
                        ...item,
                        $isDisabled: value.number > item.number,
                    }
                })
            },
            downloadPdf(billId) {
                this.$api.bills.downloadPdf(billId).then((data) => {
                    FileSaver.saveAs(data.content, data.filename);
                });
            },
            setCurrentMonth(){
                this.selectMonth.start = this.months.start[this.$moment.utc().month()];
                this.selectMonth.end = this.months.start[this.$moment.utc().month()];
            },
            handleShowEditBill(id) {
                this.$modal.show('edit-bill-modal', {billId: id});
            },
            generateYears(oldDate) {
                let Start = new Date(oldDate);
                let End = new Date();

                let years = this.$moment(End).diff(Start, 'years');
                let yearsBetween = [];

                for(let year = 0; year <= years; year++) {
                    yearsBetween.unshift(Start.getFullYear() + year);
                }

                this.yearsDate = yearsBetween;
            },
            addNewBill() {
                this.$modal.show('create-bill-modal', { month: this.selectMonth.id, year: this.yearsDate[this.paginationCurrent - 1] });
            },
            receiveBills() {
            	let startMonth = this.$moment([ this.yearsDate[this.paginationCurrent - 1], this.selectMonth.start.id, 1 ]);
            	let endMonth = this.$moment([ this.yearsDate[this.paginationCurrent - 1], this.selectMonth.end.id, 1 ]);

                const params = {
                    start_month: startMonth.format("YYYY-MM-DD HH:mm:ss"),
                    end_month: endMonth.endOf('month').format("YYYY-MM-DD HH:mm:ss"),
                    client: this.selectClient && this.selectClient.id,
                };

                this.$api.bills.getBills(params).then(data => {
                    this.bills = data.bills;
                }).catch(err => console.error(err));
            },
            updateBill(bill) {
                let index = this.$lodash.findKey(this.bills, {'id': bill.id});

                if (index) {
                    return this.bills.splice(index, 1, bill);
                }

                this.bills.push(bill);
            },
            removeBill(billId) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('delete_this_bill'),
                    body: this.$t('bill_will_be_deleted'),
                    confirmCallback: () => {
                        this.$api.bills.removeBill(billId).then(() => {
                            let index = this.$lodash.findKey(this.bills, {'id': billId});

                            this.$modal.hide('edit-bill-modal');
                            this.bills.splice(index, 1);
                        });
                    },
                });
            },
            beforeDestroy(){
                this.$event.$off('update-bill');
                this.$event.$off('show-modal-remove-bill');
            },
        }
    }
</script>
