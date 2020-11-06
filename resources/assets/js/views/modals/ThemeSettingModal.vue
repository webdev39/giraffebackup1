<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="100%" :maxWidth="500" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1001" >
        <modal-wrapper :name="$options.name" color="white" v-if="theme !== null">
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
                {{ $t('theme_setting') }}
            </template>

            <template slot="body">
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('sidebar') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.sidebar.bg"  />
                        <theme-color-picker :text="$t('color')" v-model="theme.sidebar.color" />
                        <theme-color-picker :text="$t('count_background')" v-model="theme.sidebar.task_count_bg" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('navbar') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.navbar.bg" />
                        <theme-color-picker :text="$t('color')" v-model="theme.navbar.color" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('task_detail') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.task_detail.header.bg" />
                        <theme-color-picker :text="$t('color')" v-model="theme.task_detail.header.color" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('manage') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.manage.bg" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('subscribers') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.subscribers.bg" />
                        <theme-color-picker :text="$t('color')" v-model="theme.subscribers.color" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('management') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('collapse_background')" v-model="theme.management.collapse_bg" />
                        <theme-color-picker :text="$t('collapse_color')" v-model="theme.management.collapse_color" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('modal') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('header_background')" v-model="theme.modal.header_bg" />
                        <theme-color-picker :text="$t('header_color')" v-model="theme.modal.header_color" />
                    </div>
                </div>
                <div class="theme-setting-modal__block">
                    <div class="theme-setting-modal__title">{{ $t('buttons') }}</div>
                    <div class="theme-setting-modal__sub-title">{{ $t('success') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.buttons.success.bg" />
                        <theme-color-picker :text="$t('background_hover')" v-model="theme.buttons.success.bg_hover" />
                        <theme-color-picker :text="$t('border')" v-model="theme.buttons.success.border" />
                        <theme-color-picker :text="$t('border_hover')" v-model="theme.buttons.success.border_hover" />
                        <theme-color-picker :text="$t('color')" v-model="theme.buttons.success.color" />
                        <theme-color-picker :text="$t('color_hover')" v-model="theme.buttons.success.color_hover" />
                    </div>
                    <div class="theme-setting-modal__sub-title">{{ $t('warning') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.buttons.warning.bg" />
                        <theme-color-picker :text="$t('background_hover')" v-model="theme.buttons.warning.bg_hover" />
                        <theme-color-picker :text="$t('border')" v-model="theme.buttons.warning.border" />
                        <theme-color-picker :text="$t('border_hover')" v-model="theme.buttons.warning.border_hover" />
                        <theme-color-picker :text="$t('color')" v-model="theme.buttons.warning.color" />
                        <theme-color-picker :text="$t('color_hover')" v-model="theme.buttons.warning.color_hover" />
                    </div>
                    <div class="theme-setting-modal__sub-title">{{ $t('primary') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.buttons.primary.bg" />
                        <theme-color-picker :text="$t('background_hover')" v-model="theme.buttons.primary.bg_hover" />
                        <theme-color-picker :text="$t('border')" v-model="theme.buttons.primary.border" />
                        <theme-color-picker :text="$t('border_hover')" v-model="theme.buttons.primary.border_hover" />
                        <theme-color-picker :text="$t('color')" v-model="theme.buttons.primary.color" />
                        <theme-color-picker :text="$t('color_hover')" v-model="theme.buttons.primary.color_hover" />
                    </div>
                    <div class="theme-setting-modal__sub-title">{{ $t('close') }}</div>
                    <div class="theme-setting-modal__list">
                        <theme-color-picker :text="$t('background')" v-model="theme.buttons.close.bg" />
                        <theme-color-picker :text="$t('background_hover')" v-model="theme.buttons.close.bg_hover" />
                        <theme-color-picker :text="$t('border')" v-model="theme.buttons.close.border" />
                        <theme-color-picker :text="$t('border_hover')" v-model="theme.buttons.close.border_hover" />
                        <theme-color-picker :text="$t('color')" v-model="theme.buttons.close.color" />
                        <theme-color-picker :text="$t('color_hover')" v-model="theme.buttons.close.color_hover" />
                    </div>
                </div>
            </template>

            <template slot="footer">
                <theme-button-close type="button" class="btn" @click.native="closeModal">
                    {{ $t('close') }}
                </theme-button-close>

                <theme-button-success class="btn" @click.native="handleActionTheme" :disabled="isLoading">
                    {{ type_action === 'create' ? $t('create') : $t('save') }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import { mapGetters }       from "vuex";

    import ModalWrapper         from "@views/layouts/ModalWrapper";
    import ThemeColorPicker     from "@views/partcials/ThemeColorPicker/ThemeColorPicker";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "theme-setting-modal",
        data() {
            return {
                theme_id:       null,
                type_action:    null,
                theme:          null
            }
        },
        computed: {
            ...mapGetters({
                getThemes:          'user/getThemes',
                getCurrentThemes:   'user/getCurrentTheme',
                getPriorities:      'priorities/getPriorities',
            }),
            getTheme() {
                if (this.type_action === 'create') {
                    return this.getCurrentThemes;
                }

                return this.getThemes.find(item => item.id === this.theme_id)
            }
        },
        components: {
            ModalWrapper,
            ThemeColorPicker,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.theme_id === 0) {
                    this.$notify({type:'error', text: 'You can\'t edit default theme'});
                }

                if (event.params.theme_id) {
                    this.theme_id = event.params.theme_id;
                }
                if (event.params.type_action) {
                    this.type_action = event.params.type_action;
                }

                this.theme = JSON.parse(JSON.stringify(this.getTheme));
            },
            beforeClose(event) {
                if (JSON.stringify(this.theme) !== JSON.stringify(this.getTheme)) {
                    this.$modal.show("confirm-modal", {
                        title: this.$t('confirm_modal'),
                        body: this.$t('are_you_sure_you_want_to_close_modal'),
                        confirmCallback: () => {
                            this.theme = {...this.getTheme};
                            this.closeModal();
                        }
                    });

                    return event.stop();
                }

                this.resetComponentData();
            },
            closeModal() {
                this.theme = {...this.getTheme};
                this.$modal.hide('theme-setting-modal');
            },
            handleActionTheme() {
                if (this.type_action === 'create' ) {
                    return this.handleAddTheme();
                }

                this.handleChangeTheme();
            },
            handleChangeTheme() {
                this.$api.user.updateTheme(this.theme);
                this.closeModal();
            },
            handleAddTheme() {
                this.$api.user.addTheme(this.theme);
                this.closeModal();
            }
        }
    }
</script>

<style lang="scss">
#theme-setting-modal {
overflow: hidden;
    .theme-setting-modal__title{
         font-size: 20px;
         color: #636b6f;
         font-weight: 600;
        margin-bottom: 20px;
     }
    .theme-setting-modal__title + .theme-setting-modal__sub-title{
        margin-top: -10px;
    }
    .theme-setting-modal__sub-title{
        font-size: 16px;
        color: #4e77f2;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .theme-setting-modal__block{
        margin-bottom: 20px;
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

