<template>
    <div v-if="isLoggedIn">
        <div class="pagination_inline year-pagination-holder">
            <div class="pagination-text"> {{ $t('yearly_overview') }} </div>
            <YearPagination
                :years="yearsDate"
                :current="yearsDate[paginationCurrent - 1]"
                @page-changed="updatePagination"
            />
        </div>

        <year-overview-table-section :year="yearsDate[paginationCurrent -1]" :yearOverview="yearOverview" :single-table="singleTable" />
    </div>
</template>

<script>
    import YearOverviewTableSection     from '@views/elements/year_overview_table_section/YearOverviewTableSection'
    import YearPagination               from '@views/elements/year_pagination/YearPagination'
    import config                       from '@config'

    export default {
        data(){
            return{
                yearOverview:[],
                singleTable: false,
                yearsDate: [],
                paginationCurrent: 1,
                showCreateBill: false,
                criteria: {},
                statuses: {},
                boardSelected: {},
                defaultSelected: {},
                customers: [],
                fetchField: false
            }
        },
        components: {
            YearOverviewTableSection,
            YearPagination
        },
        watch: {
            paginationCurrent: function() {
                this.getYearOverview();
            }
        },
        created() {
            this.$event.$on('update-year-overview', this.updateYearOverview);
            this.generateYears("January 1, 2000 12:00:00");
        },
        mounted() {
            if (window.innerWidth < config.size.tablet) {
                return this.$router.push({name: 'home'});
            }

            if (!this.checkPermission('read-billing', true)) {
                return this.$router.push({name: 'home'});
            }

            this.$api.reports.getBoards().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.$api.reports.getGroups().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });

            this.getYearOverview();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            updateYearOverview(boardId, monthIndex) {
                this.getYearOverview('update',boardId,monthIndex);
            },
            generateYears(oldDate) {
                let Start = new Date(oldDate);
                let End   = new Date();

                let years = this.$moment(End).diff(Start, 'years');
                let yearsBetween = [];

                for(let year = 0; year <= years; year++) {
                    yearsBetween.unshift(Start.getFullYear() + year);
                }

                this.yearsDate = yearsBetween;
            },
            updatePagination(index) {
                this.paginationCurrent = index;
            },
            getYearOverview(update, boardId, monthIndex) {
                if (!this.fetchField) {
                    this.fetchField = true;
                }

                let year = this.yearsDate[this.paginationCurrent - 1];

                this.$api.billing.getBillings(year).then(data => {
                    let months = {};

                    for (let key in data.records) {
                        if (data.records[key]) {
                            months[key] = data.records[key];
                        } else {
                            months[key] = [
                                {
                                    all_time: {},
                                    billed_time: {},
                                    board_id: "",
                                    board_name: "",
                                    group_id: "",
                                    group_name: "",
                                    not_billable: {},
                                    parked_time: {},
                                    total_billed_time: {},
                                    total_time_used: {},
                                    unbilled_time: {},
                                    emptyMonth: true
                                }
                            ]
                        }
                    }

                    for (let prop in months) {
                        months[prop].sort((a,b) => {
                            return sorter(a.group_name, b.group_name);
                        });
                    }

                    if (update) {
                        let month = months[Object.keys(months)[monthIndex]].find(item => item.board_id === boardId);

                        this.yearOverview[Object.keys(this.yearOverview)[monthIndex]].forEach(item => {
                            if (item.board_id === boardId) {
                                for (let prop in item) {
                                    if (item.hasOwnProperty(prop) && month.hasOwnProperty(prop)) {
                                        item[prop] = month[prop];
                                    }
                                }
                            }
                        });

                    } else {
                        this.yearOverview = months;
                    }

                    this.fetchField = false;

                }).catch(err => {
                    this.fetchField = false;
                });
            },
            beforeDestroy(){
                this.$event.$off('update-year-overview');
            },
        }
    }
</script>
