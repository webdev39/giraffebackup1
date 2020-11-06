<!--Optimized-->
<template>
    <modal
        :name="$options.name"
        :id="$options.name"
        :maxWidth="700"
        :pivotY="0.2"
        :adaptive="true"
        :scrollable="true"
        height="auto"
        width="100%"
        @before-open="beforeOpen"
        @before-close="beforeClose"
    >

        <modal-wrapper :name="$options.name">
            <template slot="header">
                <theme-button-close
                    class="btn-close-header"
                    @click.native="closeModal"
                >
                    <i class="icon-close" >
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </theme-button-close>
              </template>
            <template slot="title">
                {{ customer.id ? $t("edit_client") : $t("new_client") }}
            </template>

            <template slot="body">
                <div class="form-group settings-group">
                    <div class="row">
                        <div class="col-sm-push-3 col-sm-9">
                            <h3 class="margin-0" style="padding:4px 0 10px">{{ $t("main_info") }}</h3>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("client_custom_id") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="custom_id">
                            <input type="text" class="form-control" id="client-custom_id-input" minlength="1"  maxlength="150" v-model="form.custom_id">
                        </validation>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("client_name") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="name">
                            <input type="text" class="form-control" id="client-client-name-input" minlength="1" maxlength="150" v-model="form.name">
                        </validation>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("client_contact") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="contact">
                            <input type="text" class="form-control" id="client-company-name-input" minlength="1" maxlength="150" v-model="form.contact">
                        </validation>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("email") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="email">
                            <input type="text" class="form-control" id="client-email-input" minlength="1" maxlength="150" v-model="form.email">
                        </validation>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("telephone") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="phone">
                            <input type="text" class="form-control" id="client-telephone-input" minlength="3" maxlength="22" v-model="form.telephone">
                        </validation>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("hourly_rate") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="hourly_rate">
                            <input type="number" min="0" step="0.01" class="form-control" id="client-hourly-rate-input" maxlength="22" v-model="form.hourly_rate" @keydown="handleInputNumber">
                        </validation>
                    </div>
                </div>

                <div class="form-group settings-group">
                    <div class="row">
                        <div class="col-sm-push-3 col-sm-9">
                            <h3 class="margin-0" style="padding:4px 0 10px">{{ $t("address") }}</h3>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("country") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="country_id">
                            <multiselect v-model="country" track-by="id" label="name"
                                         :options="countries"
                                         :allow-empty="false"
                                         :searchable="true"
                                         @select="selectCountry">

                                <template slot="singleLabel" slot-scope="props">
                                    <span :class="[`flag-icon`, `flag-icon-${props.option.iso_3166_2.toLowerCase()}`]"></span>
                                    <span>{{ props.option.name }}</span>
                                </template>

                                <template slot="option" slot-scope="props">
                                    <span :class="[`flag-icon`, `flag-icon-${props.option.iso_3166_2.toLowerCase()}`]"></span>
                                    <span>{{ props.option.name }}</span>
                                </template>
                            </multiselect>
                        </validation>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("street") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="street">
                            <input type="text" class="form-control" id="client-street-input" minlength="1" maxlength="150" v-model="form.street">
                        </validation>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("house_number") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="house">
                            <input type="text" class="form-control" id="client-house-input" minlength="1" maxlength="150" v-model="form.house">
                        </validation>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("postcode") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="postcode">
                            <input type="text" class="form-control" id="client-postcode-input" minlength="1" maxlength="10" v-model="form.postcode">
                        </validation>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">{{ $t("city") }}</label>

                        <validation class="col-sm-9" :validator="validator" label="city">
                            <input type="text" class="form-control" id="client-city-input" minlength="1" maxlength="150" v-model="form.city">
                        </validation>
                    </div>
                </div>
            </template>

            <template slot="footer">

                <theme-button-dangerous
                    v-if="customer.id && !customer.is_archived"
                    :disabled="isLoading"
                    type="submit"
                    class="btn btn-archived"
                    @click.native="archiveCustomer"
                >
                    {{ $t('archivate') }}
                </theme-button-dangerous>

                <theme-button-dangerous
                    v-if="customer.id && customer.is_archived"
                    :disabled="isLoading"
                    type="submit"
                    class="btn btn-archived"
                    @click.native="unArchiveCustomer"
                >
                    {{ $t('un_archive') }}
                </theme-button-dangerous>

                <button
                    v-if="customer.id && customer.status === 'active'"
                    :disabled="isLoading"
                    type="button"
                    class="btn btn-remove"
                    @click="handleDelete"
                >
                    <i class="icon-trash">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-trash">
                            </use>
                        </svg>
                    </i>
                </button>

                <theme-button-close
                    type="button"
                    class="btn"
                    @click.native="closeModal"
                >
                    {{ $t("close")}}
                </theme-button-close>

                <theme-button-success
                    v-if="customer.id"
                    :disabled="isLoading"
                    type="submit"
                    class="btn btn-update"
                    @click.native="handleUpdate"
                >
                    {{ $t("save")}}
                </theme-button-success>

                <theme-button-success
                    v-if="!customer.id"
                    :disabled="isLoading"
                    type="submit"
                    class="btn"
                    @click.native="handleCreate"
                >
                    {{ $t("create")}}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from 'vuex'

    import formMixin            from '@mixins/form'
    import Validation           from "@views/components/Validation";
    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonDangerous from "@views/layouts/theme/buttons/ThemeButtonDangerous";

    export default {
        name: "customer-setting-modal",
        data() {
            return {
                country: null,
                form: {
                    customer_id:    null,
                    custom_id:      null,
                    name:           null,
                    contact:        null,
                    email:          null,
                    country_id:     null,
                    city:           null,
                    telephone:      null,
                    postcode:       null,
                    house:          null,
                    street:         null,
                    hourly_rate:    null,
                },
                initForm: {
                    customer_id:    null,
                    custom_id:      null,
                    name:           null,
                    contact:        null,
                    email:          null,
                    country:        null,
                    city:           null,
                    telephone:      null,
                    postcode:       null,
                    house:          null,
                    street:         null,
                    hourly_rate:    null,
                }
            }
        },
        computed:{
            ...mapGetters({
                customers: 'customers/getCustomers',
                countries: 'default/getCountries',
            }),
            customer: {
                get() {
                    let customer = this.customers.find(customer => customer.id === this.form.customer_id);

                    if (customer) {
                        this.country = this.countries.find(country => country.id === customer.country_id);

                        Object.assign(this.initForm, customer);
                    }

                    return this.form = {...this.initForm};
                },
                set(value) {
                    console.log(value);
                }
            }
        },
        components: {
            ModalWrapper,
            Validation,
            ThemeButtonClose,
            ThemeButtonSuccess,
            ThemeButtonDangerous,
        },
        mixins: [
            formMixin
        ],
        methods: {
            handleInputNumber (event) {
                let code = event.keyCode;

                if (code === 69 || code === 107 || code === 109 || code === 189 || code === 187) {
                    event.preventDefault();
                }
            },
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.customerId) {
                    this.form.customer_id       = event.params.customerId;
                    this.initForm.customer_id   = event.params.customerId;
                }
            },
            beforeClose(event) {

                if (JSON.stringify(this.form) !== JSON.stringify(this.initForm)) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('confirm_modal'),
						body: this.$t('are_you_sure_you_want_to_close_modal'),
                        confirmCallback: () => {
                            this.initForm = {...this.form}
                            this.closeModal();
                        }
                    });

                    return event.stop();
                }


                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('customer-setting-modal')
            },
            selectCountry(value) {
                this.form.country_id = value.id;
            },
            handleCreate() {
                this.$api.customers.create(this.form).then(() => {
                    this.initForm = {...this.form};
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleUpdate() {
                this.$api.customers.update(this.form).then(() => {
                    this.initForm = {...this.form};
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            handleDelete() {
                this.$api.customers.delete(this.form.customer_id).then(() => {
                    this.initForm = {...this.form};
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            archiveCustomer() {
                console.info('this.customer', this.customer);
                this.$api.customers.archive(this.customer.id)
            },
            unArchiveCustomer() {
                this.$api.customers.unArchive(this.customer.id)
            }
        }
    }
</script>

<style lang="scss">
    #customer-setting-modal {
        overflow: hidden;
        .col-form-label {
            padding-top: 5px;
        }
        .btn-close-header {
            background: transparent;
            border:none;
            box-shadow: none;
            fill:#fff;
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
                &:hover {
                    background: transparent;
                    border:none;
                    box-shadow: none;
                    fill:#e2e6e9;
                }
            .icon-close {
                display: block;
                 .icon {
                     width: 14px;
                     height: 14px;
                 }
            }
        }
    }
</style>
