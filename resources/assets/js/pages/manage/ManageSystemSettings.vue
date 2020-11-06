<template>
    <div id="manage-page" class="manage-system-settings">

        <div class="system-settings">

            <theme-manage-container class="manage-container system-settings-container">

                <h1 class="manage-title">{{ $t("manage_system_settings") }}</h1>

                <div class="system-settings-block">
                    <BillSettings/>
                    <BillTemplate/>
                </div>

            </theme-manage-container>

        </div>

    </div>
</template>

<script>
	import { mapGetters } from 'vuex';
	import config  from '@config';

	import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
	import BillSettings from "@views/layouts/settings/BillSettings";
	import BillTemplate from "@views/layouts/settings/BillTemplate";

	export default {
		components: {
			ThemeManageContainer,
			BillSettings,
			BillTemplate,
        },
		mounted() {
			if (window.innerWidth < config.size.tablet) {
				return this.$router.push({name: 'home'});
			}

			if (!this.checkPermission('acp-access', true)) {
				return this.$router.push({name: 'home'});
			}

			this.$store.dispatch('setPagePreloader', false);
		},
	}
</script>

<style lang="scss">
    .system-settings-block {
        display: flex;
        align-items: stretch;
        justify-content: space-between;

        .system-settings-bill-col {
            width: 49%;
        }
    }
    #manage-page {
        .system-settings-container {
            padding: 10px 10px 14px;
            border-radius: 5px;
            box-shadow: none;
            margin-bottom: 20px;

            .system-settings-block {
                box-shadow: none;
            }
        }

        .manage-form {
            height: 100%;
            background-color: #fff;
        }
    }
</style>