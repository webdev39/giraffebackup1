<template>
    <div class="manage-wrapper-create row" v-if="checkPermission('create-group')">
        <div class="col-md-10 col-sm-11 col-xs-10 create-input-wrapper">
            <theme-sidebar class="btn-create-holder" :style="{'background-color': primaryColor}">
            <button id="add-group" type="button" :title="$t('create_group')" :disabled="isLoading" @click="createGroup" class="btn btn-create">
                <!-- <i aria-hidden="true" class="oc icon-add font-color-green"></i> -->
                    <i class="icon-plus">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-plus">
                        </use>
                    </svg>
                    </i>
            </button>
            </theme-sidebar>
            <input class="create-input" type="text" maxlength="150" @keyup.enter="createGroup" v-model="form.name" :disabled="isLoading" :placeholder="$t('new_group_input')" autofocus>
        </div>

        <div class="col-md-2 col-sm-1 col-xs-2">
            <button type="button" :title="$t('title_setting_group')" :disabled="isLoading" @click="showGroupSetting" class="btn btn-setting">
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
</template>

<script>
    import {mapGetters}         from 'vuex'
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeSidebar         from '@views/layouts/theme/ThemeSidebar'

    export default {
        name: "group-create",
        components: {
            ThemeButtonSuccess,
            ThemeSidebar,
        },
        data() {
            return {
                form: {
                    name: null,
                },
            }
        },
        computed: {
             ...mapGetters({
				    primaryColor: 'user/getPrimaryColor',
 			}),
        },
        mounted() {
            this.$event.$on('set-create-group-name', this.setGroupName)
        },
        methods: {
            setGroupName(name) {
                this.form.name = name;
            },
            showGroupSetting() {
                this.$modal.show('group-setting-modal', this.form);
            },
            createGroup() {
                this.$api.group.createGroup(this.form).then(() => {
                    this.resetComponentData();
                }).catch((err) => {
                    this.$notify({type:'error', text: err.response.data.message});
                })
            },
        },
    }
</script>
