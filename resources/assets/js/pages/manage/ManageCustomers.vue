<!--Optimized-->
<template>
    <div id="manage-page" class="manage-customers">
        <template>
            <fixed-component is-scroll >
            <div class="manage-customers-navbar-header">
                    <div class="manage-customers-navbar-title">
                        {{ $t('clients') }}
                    </div>
                    <div class="multiselect-customer-status">
                            <multiselect
                            v-model="statusCustomers"
                            :options="options"
                            track-by="id"
                            label="name"
                            :allow-empty="false"
                            deselect-label=""
                            class=" multiselect_size_s"
                            :searchable="false"
                            >
                                <template slot-scope="{ option }">
                                    {{ option.alias }}
                                </template>
                        </multiselect>
                    </div>
                <div class="button-holder">
                    <button class="button__icon new-client-btn" @click="showCustomerSettingModal(null)">
                        <i class="icon-plus">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-plus">
                            </use>
                        </svg>
                        </i>
                    </button>
                    <button class="button__icon client-settings-btn">
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
            </fixed-component>
        </template>
        <div class="table-responsive">
                        <theme-manage-container class="table">
                <!-- <h1 class="manage-title">
                    {{ $t('manage_clients') }}

                    <div class="manage-title-control">
                        <theme-button-success class="btn btn-xs "
                                :class="{ 'btn-primary': switchActiveCustomers, 'btn-warning': !switchActiveCustomers }"
                                @click.native="toggleCustomers">
                                    <i class="icon-archive">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                        xlink:href="#icon-archive">
                                        </use>
                                    </svg>
                                    </i>{{ switchActiveCustomers ? $t("archivate") : $t("active") }}
                        </theme-button-success>
                        <theme-button-success class="btn btn-xs btn-primary" @click.native="showCustomerSettingModal(null)">
                            <i class="icon-plus">
                            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                xlink:href="#icon-plus">
                                </use>
                            </svg>
                            </i>
                            {{ $t('new_client') }}
                        </theme-button-success>
                    </div>
                </h1> -->

                <div class="table-head">
                    <div class="col hide-mbl">{{ $t("id") }}</div>
                    <div class="col first-mbl">{{ $t("name") }}</div>
                    <div class="col hide-mbl text-center">{{ $t("status") }}</div>
                    <div class="col text-right">{{ $t("invoices") }}</div>
                    <div class="col text-right">
                        <span class="hidden-xs">{{ $t("invoices_amount") }}</span>
                        <span class="visible-xs hidden-md"">{{ $t("amount") }}</span>
                    </div>
                    <div class="col hide-mbl">{{ $t("createdAt") }}</div>
                    <div class="col action">{{ $t("actions") }}</div>
                </div>

                <div class="table-row" v-for="(customer, index) in getCustomers" :key="customer.id">
                    <div class="col hide-mbl" @click="showCustomerSettingModal(customer.id)">{{ index + 1 }}</div>
                    <div class="col first-mbl" @click="showCustomerSettingModal(customer.id)">
                        {{ customer.name }}
                        <h5 class="visible-xs text-grey hidden-md m-0">{{ customer.created_at | toLocalTime }}</h5>
                    </div>
                    <div class="col hide-mbl text-center text-capitalize" @click="showCustomerSettingModal(customer.id)">{{ customer.status }}</div>
                    <div class="col text-right" @click="showCustomerSettingModal(customer.id)">{{ $t("0") }}</div>
                    <div class="col text-right" @click="showCustomerSettingModal(customer.id)">{{ $t("253.22") }}</div>
                    <div class="col hide-mbl" @click="showCustomerSettingModal(customer.id)">{{ customer.created_at | toLocalTime }}</div>
                    <div class="col action">
                        <a :title="$t('edit_clients')" @click="showCustomerSettingModal(customer.id)">
                            <i class="icon-settings">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-settings"></use>
                                </svg>
                            </i>
                        </a>
                    </div>
                </div>
            </theme-manage-container>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import FixedComponent       from '@views/components/fixedComponent/FixedComponent';

    export default {
        name: "manage-customers",
        computed:{
            ...mapGetters({
                getActiveCustomers:     'customers/getActiveCustomers',
                getArchiveCustomers:    'customers/getArchiveCustomers',
            }),
            customersFactory () {
              return {
                status: [...this.getActiveCustomers, ...this.getArchiveCustomers],
                active: this.getActiveCustomers,
                archive: this.getArchiveCustomers
              }
            },
            getCustomers () {
              return this.customersFactory[this.statusCustomers.name]
            }
        },
        components: {
            ThemeManageContainer,
            ThemeButtonSuccess,
            FixedComponent
        },
        data() {
          return {
             switchActiveCustomers: true,
             statusCustomers: {id: 0, name: "status", alias: this.$t("satus")},
             options: [
                 {id: 1, name: "active",  alias: this.$t("active")},
                 {id: 2, name: "archive", alias: this.$t("archivate")},
             ],
          }
        },
        mounted() {
            if (!this.checkPermission('manage-customers', true)) {
                return this.$router.push({name: 'home'});
            }

            this.$api.customers.getCustomers().catch(err => {
                this.$notify({type:'error', text: err.response.data.message});
            });
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            showCustomerSettingModal(customerId) {
                this.$modal.show('customer-setting-modal', {customerId: customerId})
            },
            // toggleCustomers () {
            //    this.switchActiveCustomers = !this.switchActiveCustomers;
            // },
        }
    }
</script>
