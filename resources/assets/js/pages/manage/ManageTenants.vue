<!--Optimized-->
<template>
    <div id="manage-page" class="manage-tenants">
        <theme-manage-container class="manage-container">
            <h1 class="manage-title">{{ $t("manage_tenants") }}</h1>

            <div class="manage-wrapper-header row">
                <div class="col-sm-12 col-xs-12 padding-0">
                    <div class="col-xs-12 col-sm-1 control-label-center">
                        {{ $t("id") }}
                    </div>
                    <div class="col-xs-12 col-sm-5 control-label">
                        {{ $t("name") }}
                    </div>
                    <div class="col-xs-12 col-sm-4 control-label-center">
                        {{ $t("date_of_registration") }}
                    </div>
                    <div class="col-xs-12 col-sm-2 control-label-right">
                        {{ $t("count_of_users") }}
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item manage-wrapper-item_white row" v-for="(tenant, index) in tenants" :key="tenant.id">
                <div class="col-sm-12 col-xs-12 padding-0">
                    <div class="col-xs-12 col-sm-1 control-label-center">
                        {{ index + 1 }}
                    </div>
                    <div class="col-xs-12 col-sm-5 control-label">
                        {{ tenant.name }}
                    </div>
                    <div class="col-xs-12 col-sm-4 control-label-center">
                        {{ tenant.created_at | toLocalTime }}
                    </div>
                    <div class="col-xs-12 col-sm-2 control-label-center">
                        <div class="manage-stats">
                            <div class="manage-wrapper-buttons">
                                <button class="btn" @click.prevent>
                                    <span>{{ tenant.count_users }} X</span>
                                    <i class="oc icon-user"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </theme-manage-container>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";

    export default {
        name: "manage-tenants",
        computed:{
            ...mapGetters({
                tenants: 'tenants/getTenants',
            }),
        },
        components: {
            ThemeManageContainer
        },
        mounted(){
            this.$api.tenants.getTenants();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
    }
</script>
