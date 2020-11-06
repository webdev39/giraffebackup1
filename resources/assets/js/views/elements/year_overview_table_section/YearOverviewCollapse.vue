<template>
    <div class="table-component">
        <div class="table-component__table-wrapper">
            <div class="table-component_table-collapse">
                <table v-on:click="handleShow" v-bind:class="{tableActive: isActive}" class="table-component__table table-component__table-group_year_overview">
                    <tbody class="table-component__table__body">
                        <tr class="group-by-tr">
                            <td colspan="2" class="group-by-td">{{ translateMonth(monthName) }}</td>
                            <td
                                :class="{hover: hoverColumn === 3}"
                                @mouseover="$emit('mouseoverColumn', 3)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                {{ ShowAllDate('total_time_bill')     | formatDate }}
                            </td>
                            <td
                                :class="{hover: hoverColumn === 4}"
                                @mouseover="$emit('mouseoverColumn', 4)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                {{ openTimeSum }}
                            </td>
                            <td
                                :class="{hover: hoverColumn === 5}"
                                @mouseover="$emit('mouseoverColumn', 5)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                {{ ShowAllDate('total_billed_time')   | formatDate }}
                            </td>
                            <td
                                :class="{hover: hoverColumn === 6}"
                                @mouseover="$emit('mouseoverColumn', 6)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                {{ ShowAllDate('parked_time')         | formatDate }}
                            </td>
                            <td
                                :class="{hover: hoverColumn === 7}"
                                @mouseover="$emit('mouseoverColumn', 7)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                {{ ShowAllDate('not_billable')        | formatDate }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table-component table-component_without-head table-component__table_year_overview" v-if="!showTable">
            <div class="table-component__table-wrapper">
                <table class="table-component__table" >
                    <tbody class="table-component__table__body">
                        <ThemeTableTr :key="index" v-for="(row, key, index) in month" v-if="!showCell" >
                            <td><span>{{ row.group_name }}</span></td>
                            <td><span class="table-text-link" @click="handleShowModal(row, monthName, year)">{{ row.board_name }}</span></td>
                            <td
                                :class="{hover: hoverColumn === 3}"
                                @mouseover="$emit('mouseoverColumn', 3)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                <span>{{ row.total_time_bill | formatDate }}</span>
                            </td>
                            <td
                                :class="{hover: hoverColumn === 4}"
                                @mouseover="$emit('mouseoverColumn', 4)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                <span>
                                    {{ calculate(row.total_unbilled_time, row.parked_time, row.not_billable) }}
                                </span>
                            </td>
                            <td
                                :class="{hover: hoverColumn === 5}"
                                @mouseover="$emit('mouseoverColumn', 5)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                <span>
                                    {{ row.total_billed_time | formatDate }}
                                </span>
                            </td>
                            <td
                                :class="{hover: hoverColumn === 6}"
                                @mouseover="$emit('mouseoverColumn', 6)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                <span>{{ row.parked_time | formatDate }}</span>
                            </td>
                            <td
                                :class="{hover: hoverColumn === 7}"
                                @mouseover="$emit('mouseoverColumn', 7)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >
                                <span>{{ row.not_billable | formatDate }}</span>
                            </td>
                        </ThemeTableTr>
                        <tr class="table-component__row-not-hover" v-else>
                            <td>
                                
                            </td>
                            <td>
                                <span>{{ $t('no_data') }}</span>
                            </td>
                            <td
                                :class="{hover: hoverColumn === 3}"
                                @mouseover="$emit('mouseoverColumn', 3)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >

                            </td>
                            <td
                                :class="{hover: hoverColumn === 4}"
                                @mouseover="$emit('mouseoverColumn', 4)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >

                            </td>
                            <td
                                :class="{hover: hoverColumn === 5}"
                                @mouseover="$emit('mouseoverColumn', 5)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >

                            </td>
                            <td
                                :class="{hover: hoverColumn === 6}"
                                @mouseover="$emit('mouseoverColumn', 6)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >

                            </td>
                            <td
                                :class="{hover: hoverColumn === 7}"
                                @mouseover="$emit('mouseoverColumn', 7)"
                                @mouseleave="$emit('mouseleaveColumn')"
                            >

                             </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import { TableComponent, TableColumn }    from '../vue_table_component/index';
    import ThemeTableTr from '@views/layouts/theme/ThemeTableTr';

    export default {
        props: {
            month: {
                type: [Object, Array]
            },
            monthName: {
                type: String
            },
            monthIndex: {
                type: Number
            },
            year: {
                type: Number
            },
            boards: [],
            hoverColumn: {
                type: Number
            }
        },
        data(){
            return {
                showTable: true,
                sumAllTime: '',
                isActive: false,
            }
        },
        watch: {
            month: function() {
                this.showTable = true;
            }
        },
        components: {
            'table-component': TableComponent,
            'table-column': TableColumn,
            ThemeTableTr,
        },
        filters: {
            formatDate(value) {
                if (!value) {
                    return '';
                }

                let hours = value.d > 0 ? value.d * 24 : 0;
                    hours += value.h > 0 ? value.h : 0;
                    hours += value.i > 0 ? value.i / 60 : 0;
                    hours += value.s > 0 ? value.s / 60 / 60 : 0;

                return hours.toFixed(2) + ' h ';
            }
        },
        computed: {
            showCell() {
                return this.handleColumnHide();
            },
            openTimeSum() {
                const unbilled = this.convertToHours(this.ShowAllDate('total_unbilled_time'));
                const parked = this.convertToHours(this.ShowAllDate('parked_time'));
                const not_billable = this.convertToHours(this.ShowAllDate('not_billable'));

                return unbilled - parked - not_billable + ' h ';
            }
        },
        methods: {
            calculate(a, b, c) {
                return this.convertToHours(a) - this.convertToHours(b) - this.convertToHours(c) + ' h ';
            },
            convertToHours(value) {
                let hours = value.d > 0 ? value.d * 24 : 0;
                    hours += value.h > 0 ? value.h : 0;
                    hours += value.i > 0 ? value.i / 60 : 0;
                    hours += value.s > 0 ? value.s / 60 / 60 : 0;
                return hours;
            },
            handleShowModal(row, month, year){
                this.$modal.show('create-bill-modal', {boardId: row.board_id, month: month, year: year});
            },
            handleColumnHide(){
                if(!this.month[0].emptyMonth) {
                    return false;
                }

                return true;
            },
            handleShow() {
                this.showTable = !this.showTable;
                this.isActive = !this.isActive;
            },
            ShowAllDate(value) {
                if(!this.month) return;

                let timeCount = {};
                    timeCount.d = 0;
                    timeCount.h = 0;
                    timeCount.i = 0;
                    timeCount.s = 0;

                this.month.forEach((item) => {
                    let time = item[value];

                    if (!time) {
                        return;
                    }

                    timeCount.s += time.s > 0 ? time.s : 0;

                    if(timeCount.s >= 60) {
                        timeCount.s = timeCount.s - 60;
                        timeCount.i += 1;
                    }

                    timeCount.i += time.i > 0 ? time.i : 0;

                    if(timeCount.i >= 60) {
                        timeCount.i = timeCount.i - 60;
                        timeCount.h += 1;
                    }

                    timeCount.h += time.h > 0 ? time.h : 0;

                    if(timeCount.h >= 24){
                        timeCount.h = timeCount.h - 24;
                        timeCount.d += 1;
                    }

                    timeCount.d += time.d > 0 ? time.d : 0
                });

                return timeCount;
            },
            translateMonth(month) {
                if (month) {
                    return this.$t(month.toLowerCase().toString());
                }
            }
        }
    }
</script>
