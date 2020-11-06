<!--Optimized-->
<template>
    <div id="manage-page" class="manage-roles">

        <!-- Header -->
        <div class="manage-customers-navbar-header">
            <div class="manage-customers-navbar-title flex-1">
                {{ $t("manage_roles") }}
            </div>
            <div class="button-holder">
                <theme-button-success class="button__icon new-client-btn" @click.native="showRoleModal(null)">
                    <i class="icon-plus">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-plus">
                            </use>
                        </svg>
                    </i>
                </theme-button-success>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <theme-manage-container class="table">
                <div class="table-head">
                    <div class="col">{{ $t("id") }}</div>
                    <div class="col">{{ $t("role") }}</div>
                    <div class="col">{{ $t("description") }}</div>
                    <div class="col action">{{ $t("actions") }}</div>
                </div>
                <div class="table-row" v-for="(role, index) in customRoles" :key="role.id">
                    <div class="col">{{ index + 1 }}</div>
                    <div class="col">{{ role.display_name }}</div>
                    <div class="col">{{ role.description }}</div>
                    <div class="col action">
                        <a :title="$t('edit_role')" @click="showRoleModal(role.id)">
                            <i class="icon-settings">
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                         xlink:href="#icon-settings">
                                    </use>
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
    import {mapGetters} from 'vuex'
    import config               from '@config'

    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        data() {
            return {
                roles: [],
            }
        },
        components: {
            ThemeManageContainer,
            ThemeButtonSuccess
        },
        computed:{
            ...mapGetters({
                customRoles: 'permissions/getCustomRoles',
            })
        },
        mounted() {
            if (window.innerWidth < config.size.tablet) {
                return this.$router.push({name: 'home'});
            }

            if (!this.checkPermission('manage-group-level-role', true)) {
                return this.$router.push({name: 'home'});
            }

            this.$api.permissions.getRoles();
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            showRoleModal(roleId) {
                this.$modal.show('role-setting-modal', {role_id: roleId})
            }
        },
    }
</script>
