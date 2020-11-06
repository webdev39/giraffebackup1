<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        height="auto"
        width="30%"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >
        <div class="bill-modal bill-modal_add" v-if="showModal">
            <div class="bill-modal__filters">
                <div class="row bill-modal__row">
                    <div class="col-sm-12 bill-modal__column_sm">
                        <div class="bill-modal__selected">
                            <span class="bill-modal__selected-text"> {{ $t('choose_the_existing_bill') }}</span>

                            <select
                                v-model="selectBillId"
                                class="form-control"
                            >
                                <option
                                    v-for="item in bills"
                                    :key="item.id"
                                    :value="item.invoice_number"
                                >
                                    {{ item.invoice_number }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <button class="bill-modal__filters__button_indent button__theme_dark button__size_s button__w_100" @click="closeModal">{{ $t('cancel') }}</button>
                    </div>
                    <div class="col-sm-6">
                        <button class="button__theme_dark button__size_s button__w_100" :disabled="isLoading || !selectBillId" @click="setAddBill">{{ $t('next') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
    import clickOutside     from 'v-click-outside';

    export default {
        name: "add-bill-modal",
        data() {
            return {
                bills:              [],
                billTimers:         [],
                selectBillId:       null,
                selectCustomerId:   null,
                showModal:          false,
            }
        },
        computed: {
            selectBill() {
                if (this.selectBillId) {
                    return this.bills.find(item => item.id === this.selectBillId);
                }

                return {};
            }
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        methods: {
            beforeOpen(event) {
                if (event.params && event.params.billTimers) {
                    this.billTimers = event.params.billTimers;
                }

                if (event.params && event.params.client) {
                    this.selectCustomerId = event.params.client;
                }

                if(!this.selectCustomerId) {
                    this.$notify({type:'error', text: this.$t('not_selected_customer')});
                    this.closeModal();
                }

                if(!this.billTimers.length) {
                    this.$notify({type:'error', text: this.$t('not_bill_timers')});
                    this.closeModal();
                }

                this.getAddBill(event.params);
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
                this.$modal.hide('add-bill-modal')
            },
            getAddBill(data) {
                this.$api.bills.getBills(data)
                    .then(data => {
                        this.bills = data.bills;
                        this.showModal = true;
                    }).catch(err => {
                        this.closeModal();
                        this.$notify({type:'error', text: err.response.data.message});
                    });
            },
            setAddBill () {
                this.$api.bills.addToBill({
                    "billId":       this.selectBill.id,
                    "billTimers":   this.billTimers,
                    "customerId":   this.selectCustomerId,
                    "rate":         this.selectBill.rate,
                }).then(data => {
                    this.$t('create_bill');
                    this.closeModal();
                    this.$modal.show('edit-bill-modal', {bill: data.bill});
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            }
        }
    }
</script>

<style lang="scss">
    #add-bill-modal {
        select.form-control {
            border: none;
            height: auto;
            padding: 5px 5px 5px 6px;
        }
    }
</style>
