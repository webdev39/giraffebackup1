<template>
    <div class="tables_without-head tables_scroll table__theme_black table__theme_colorful table-year-overview">
        <div class="table-component">
            <div class="table-component__table-wrapper">
                <table class="table-component__table">
                    <thead class="table-component__table__head table-component__table__head_year_overview">
                        <template>
                            <ThemeTableTr>
                                    <th colspan="2" role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('month') }}</th>
                                    <th role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('all_time') }}</th>
                                    <th role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('unbilled_time') }}</th>
                                    <th role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('billed_time') }}</th>
                                    <th role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('parked_time') }}</th>
                                    <th role="columnheader" aria-disabled="true" class="table-component__th">{{ $t('not_billable') }}</th>
                            </ThemeTableTr>
                        </template>
                    </thead>
                </table>
            </div>
        </div>

        <year-overview-collapse v-for="(month,key,index) in yearOverview" @mouseoverColumn="mouseoverColumn" @mouseleaveColumn="mouseleaveColumn" :hoverColumn="hoverColumn" :key="key" :monthIndex="index" :monthName="key" :year="year" :month="month" />
    </div>
</template>

<script>
    import YearOverviewCollapse from '../year_overview_table_section/YearOverviewCollapse'
    import ThemeTableTr                     from '@views/layouts/theme/ThemeTableTr'

    export default {
        data() {
            return {
                hoverColumn: null
            }
        },
        props: {
            yearOverview: {
                type: [Object, Array]
            },
            year: {
                type: Number
            }
        },
        components: {
            'year-overview-collapse': YearOverviewCollapse,
            ThemeTableTr
        },
        methods: {
            mouseoverColumn(numberColumn) {
                this.hoverColumn = +numberColumn
            },
            mouseleaveColumn() {
                this.hoverColumn = null
            },
        }
    }
</script>

<style lang="scss">
    .table-year-overview {
        border-radius: 5px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
        overflow-x: unset;
        .table-component {
            &:nth-child(odd) 
            .table-component__table-wrapper {

                .table-component_table-collapse {
                    table {
                        background: #f4f4f4;
                    }
                    .tableActive {
                        background: #376aa7;
                         .group-by-tr {
                             color: #fff;
                         }
                    }
                }
            }
            &:first-child {
                 .table-component__table {
                    border-top-left-radius: 5px;
                    border-top-right-radius: 5px;
                }
            }
            &:last-child {
                 .table-component__table {
                    border-bottom-left-radius: 5px;
                    border-bottom-right-radius: 5px;
                }
            }
        }


        .table-component__table__head_year_overview {
            border-bottom: 1px solid #ddd;
        }
    }

</style>