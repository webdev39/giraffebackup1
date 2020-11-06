<template>
    <div class="col-sm-12 col-xs-12 col-lg-10">
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                <span>
                    {{ $t('current_theme') }}
                </span>
            </div>
            <div class="col-xs-12 col-sm-7 col-lg-6">
                <div class="themes">
                    <div class="themes__list">
                        <div class="themes__item"
                             :class="{'themes__item_current': theme.id === getCurrentTheme.id}"
                             v-for="theme in getThemes"
                             @click="handleSetTheme(theme)"
                        >
    <!--                         <div class="theme__preview"-->
    <!--                         :class="{'theme__default' : theme.sidebar.bg === '#376aa7', 'theme__dark' : theme.sidebar.bg === '#495665'}"-->
    <!--                         ></div>-->
                            <div class="themes__item-inner">
                                <div class="themes__color color__sidebar"  :style="{'background-color': theme.sidebar.bg}">
                                        <div class="themes__color color__sidebar-text"  :style="{'background-color': theme.sidebar.color}"></div>
                                        <div class="themes__color color__sidebar-text"  :style="{'background-color': theme.sidebar.color}"></div>
                                        <div class="themes__color color__sidebar-text"  :style="{'background-color': theme.sidebar.color}"></div>
                                </div>
                                <div class="themes__color color__navbar"  :style="{'background-color': theme.navbar.bg}"></div>
                                <div class="themes__color color__subscribers"  :style="{'background-color': theme.subscribers.bg}"></div>
                                <div class="color__modal">
                                    <div class="themes__color color__modal-header"  :style="{'background-color': theme.modal.header_bg}">
                                        <div class="themes__color color__modal-header-color"  :style="{'background-color': theme.modal.header_color}"></div>
                                    </div>
                                    
                                </div>
                                

                                <div
                                    v-if="theme.id !== 0 && !theme.default"
                                    class="themes__color themes__more"
                                    @click.stop="handleShowModal(theme)"
                                >
                                    <i class="fa fa-pencil"></i>
                                </div>
                                <div
                                    v-if="getCurrentTheme.id !== theme.id && theme.id !== 0 && !theme.default"
                                    class="themes__color themes__remove"
                                    @click.stop="handleRemoveTheme(theme.id)"
                                >
                                    <i class="fa fa-trash"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <theme-button-success class="btn btn-md" @click.prevent.native="handleShowModal(null,'create')">
                        {{ $t('add_theme') }}
                    </theme-button-success>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapGetters}         from 'vuex'
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";

    export default {
        computed: {
            ...mapGetters({
                getThemes:          'user/getThemes',
                getCurrentTheme:    'user/getCurrentTheme',
            }),
        },
        components: {
            ThemeButtonSuccess
        },
        data() {
            return { }
        },
        methods: {
            handleSetTheme(theme) {
                this.$emit('selectTheme', theme.id);
                this.$store.dispatch('user/setThemeId', theme.id);
            },
            handleShowModal(theme, action) {
                let data = {};

                if (theme) {
                    data.theme_id = theme.id
                }
                if (action) {
                    data.type_action = action
                }

                this.$modal.show('theme-setting-modal', data);
            },
            handleRemoveTheme(id) {
                this.$modal.show("confirm-modal", {
                    title: this.$t('confirm_modal'),
                    body: 'Are you sure you want to remove theme ?',
                    confirmCallback: () => {
                        this.$api.user.removeTheme(id);
                    }
                });
            }
        }

    }
</script>

<style lang="scss">
    .themes__list{
        position: relative;
        // display: flex;
        // flex-wrap: nowrap;
        margin-bottom: 10px;
        // justify-content: center;
    }
    .themes__item{
        display: inline-block;
        width: 180px;
        height: 100px;
        cursor: pointer;
        // padding: 7px;
        background-color: #eaeaea;
        margin-left: 15px;
        margin-right: 15px;
        margin-bottom: 10px;
        border: 1px solid #c7c7c7;
        transition: .3s;
        position: relative;
        &:last-child{
            // margin-right: 0;
        }
        &:hover{
            border-color: #3669a7;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,.16), 0 2px 8px 0 rgba(0,0,0,.12);
            .themes__more,
            .themes__remove{
                visibility: visible;
                opacity: 1;
                transition: .3s;
            }
            .theme__preview {
                visibility: visible;
                opacity: 1;
                transition: .5s;
            }
        }
        .theme__preview {
            position: absolute;
            top: -50%;
            z-index: 2;
            visibility: hidden;
            opacity: 0;
            background-size: cover;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,.16), 0 2px 8px 0 rgba(0,0,0,.12);
            
        }
        .theme__dark {
            background-image: url('/images/dark-theme-preview-bg.png');
        }
        .theme__default {
            background-image: url('/images/default-theme-preview-bg.png');
        }
    }
    .themes__item_current{
        border-color: #3669a7;
    }
    .themes__item-inner{
        display: flex;
        width: 100%;
        height: 100%;
        position: relative;
    }
    .themes__color{
        // width: 20%;
        height: 100%;
    }
    .color__sidebar {
        width: 35px;
            .color__sidebar-text {
                position: absolute;
                left: 5px;
                height: 1px;
                width: 25px;
                z-index: 0;
                &:nth-child(1){
                    top: 25px;
                }
                &:nth-child(2){
                    top: 30px;
                }
                &:nth-child(3){
                    top: 35px;
                }
            }
    }
    .color__navbar {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 15px;
    }
    .color__subscribers {
        position: absolute;
        top: 3px;
        right: 5px;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        border:1px solid #fff;
    }
    .color__modal {
        position: absolute;
        right: 50px;
        top: 20px;
        width: 55px;
        height: 70px;
        background-color: #bfbfbf;
            .color__modal-header {
                height: 12px;
                width: 100%;
                .color__modal-header-color {
                    position: absolute;
                    height: 1px;
                    width: 35px;
                    top: 5px;
                    left: 5px;
                }
            }
    }
    .themes__more,
    .themes__remove{
        visibility: hidden;
        opacity: 0;
        position: absolute;
        width: 30px;
        height: 30px;
        color: #fff;
        border-radius: 1000%;
        right: -16px;
        text-align: center;
        font-size: 12px;
        align-items: center;
        justify-content: center;
        background-color: #3669a7;
        padding: 5px;
        transition: .3s;
        cursor: pointer;
        .fa{
            color: #fff !important;
            transition: .3s;
        }
        &:hover {
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.16), 0 2px 8px 0 rgba(0, 0, 0, 0.12);
            background-color: #fff;
                    .fa{
                        color: #3669a7 !important;
                    }
        }
    }
    .themes__more{
        top: 15px;
    }
    .themes__remove{
        bottom: 15px;
    }
    @media (min-width: 768px) {
        .themes__list {
            // justify-content: flex-start;
        }
         .themes__item {
             margin-left: 0;
             margin-right: 30px;
         }
         .theme__preview  {
            width: 310px;
            height: 210px;
            left: -215px;
            top: -180px;
         }
    }
    @media (min-width: 1122px) {
        .theme__preview {
            width: 440px;
            height: 300px;
            left: -360px;
            top: -185px;
        }
    }
</style>