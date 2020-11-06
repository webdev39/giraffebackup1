<template>
    <div class="navbar-dropdown-menu open">
        <h6 class="navbar-dropdown-header">
            {{ $t("profile") }}
            <i class="icon-user">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
             <use xmlns:xlink="http://www.w3.org/1999/xlink"
                xlink:href="#icon-user">
             </use>
             </svg>
             </i>
        </h6>

        <router-link
            :to="{name:'profile'}"
            id="profile"
            class="dropdown-item"
        >
            {{ $t("user_profile") }}
        </router-link>

        <!--<router-link :to="{name:'setting'}">-->
            <!--<a id="setting" class="dropdown-item">-->
                <!--{{ $t("bill_setting") }}-->
            <!--</a>-->
        <!--</router-link>-->

        <router-link
            :to="{name:'manage-groups'}"
            id="manage-groups"
            class="dropdown-item"
        >
            {{ $t("manage_groups") }}
        </router-link>

        <h6 class="navbar-dropdown-header">
            {{ $t("administration") }}
            <i class="icon-settings">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
             <use xmlns:xlink="http://www.w3.org/1999/xlink"
                xlink:href="#icon-settings">
             </use>
             </svg>
             </i>
        </h6>

        <router-link
            v-if="checkPermission('acp-access') && !isTablet"
            :to="{ name: 'manage-system-settings' }" 
            class="dropdown-item"
        >
            {{ $t("manage_system_settings") }}
        </router-link>

        <router-link
            v-if="checkPermission('acp-access') && !isTablet"
            :to="{name:'manage-users'}"
            id="manage_users"
            class="dropdown-item"
        >
            {{ $t("manage_users") }}
        </router-link>

        <router-link
            v-if="checkPermission('manage-group-level-role') && !isTablet"
            :to="{name:'manage-roles'}"
            id="role"
            class="dropdown-item"
        >
            {{ $t("manage_roles") }}
        </router-link>

        <router-link
            v-if="!isTablet"
            :to="{name:'manage-pipelines'}"
            id="pipeline"
            class="dropdown-item"
        >
            {{ $t("manage_pipelines") }}
        </router-link>

        <!--<router-link :to="{name:'manage-customers'}" v-if="isTenant | checkPermission('manage-customers')">
            <a id="client" style="cursor:pointer;" class="dropdown-item">
                {{ $t("manage_clients") }}
            </a>
        </router-link>-->

        <a href="#" class="dropdown-item" @click.prevent="$api.auth.logout()">{{ $t("logout") }} 
            <i class="icon-logout">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
             <use xmlns:xlink="http://www.w3.org/1999/xlink"
                xlink:href="#icon-logout">
             </use>
             </svg>
             </i>
        </a>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import config       from '@config'

    export default {
        computed: {
            ...mapGetters({
                canInvited: 'user/canInvited'
            }),
            isTablet() {
                if (window.innerWidth < config.size.tablet) {
                    return true
                }

                return false
            }
        },
    }
</script>
