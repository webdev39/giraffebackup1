<template>
    <div class="bill-dropdown-wrapper" v-click-outside="handleHide">
        <span class="billing-status-block" :class="{mixed: getMixedAlias}" :style="{ backgroundColor: getColor }"></span>
        <span>{{ $t(getAlias) }}</span>
        <span class="billing-status-pencil" :title="$t('change_bill_status')" v-if="selectedDetails">
            <!-- <i class="fa fa-pencil" v-on:click="handleShow" style="cursor: pointer"></i> -->
            <i class="icon-pencil" v-on:click="handleShow">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-pencil">
                    </use>
                </svg>
            </i>
        </span>
        <div v-if="showDropDown" class="dropdown-menu show" :class="{ bill_dropdown_top: showTop }">
            <div class="bill-dropdown-item" v-for="option in options" v-on:click="change(option)">
                <span class="billing-status-block" :style="{ backgroundColor: option.color }"></span>
                <span>{{ $t(`select_bill_options.${option.id}`) }}</span>
            </div>
        </div>
    </div>
</template>

<script>
    import clickOutside         from 'v-click-outside';

    export default {
        props: {
            selectedDetails: { type: Boolean },
            row: { default: {}, type: Object },
            selectionOptions: { default: {}, type: Array },
            month: { type: Number},
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        data(){
            return {
                showDropDown: false,
                selected: '',
                options: this.selectionOptions,
                fetch: true,
                showTop: true,
            }
        },
        computed:{
            getAlias(){
                return this.row.billing_status_alias.toLowerCase().replace(' ', '_');
            },
            getMixedAlias () {
                return this.getAlias === 'Mixed'
            },
            getColor(){
                return this.row.billing_status_color;
            }
        },
        watch: {
            selected: function () {
                this.updateStatus(this.selected);
            },
            showDropDown: function(){

                if(this.showDropDown) {
                    this.$nextTick(function () {

                        let dropDownTop = this.$el.offsetTop;
                        let dropDownOpenHeight = this.$el.querySelector('.dropdown-menu').offsetHeight + this.$el.offsetHeight;
                        let dropDownAbsoluteHeight = dropDownTop + dropDownOpenHeight;

                        if( dropDownAbsoluteHeight > this.$el.offsetParent.offsetHeight ) {
                            this.showTop = true;
                        } else {
                            this.showTop = false;
                        }
                    })
                }
            }
        },
        methods: {
            change(option) {
                this.selected = option.id;
                this.handleHide();
            },
            handleShow(e) {
                e.preventDefault();

                this.showDropDown = !this.showDropDown;
            },
            handleHide() {
                this.showDropDown = false;
            },
            updateStatus(id) {

                let data = {
                    timerBillingId: this.row.timer_billing_id,
                    billingStatusId: id
                };

                if(this.fetch) {
                    this.fetch = false;

                    this.$api.bills.updateStatus(data).then(data => {
                        this.$t('update_bill');

                        this.options.forEach((item) => {
                            if(item.id === data.timerBilling.billing_status_id){
                                this.row.billing_status_color = item.color;
                                this.row.billing_status_alias = item.alias;
                            }
                        });

                        this.$store.dispatch('timers/changeBillStatusTimer', data);

                        this.$event.$emit('update-year-overview', this.row.board_id, this.month);

                        this.fetch = true;
                    }).catch(err => {
                        this.$notify({type:'error', text: err.response.data.message});
                    });
                }
            }
        }
    }
</script>
