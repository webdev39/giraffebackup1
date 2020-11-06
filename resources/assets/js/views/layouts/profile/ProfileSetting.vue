<template>
    <form class="manage-form" @submit.prevent="updateProfile">
        <theme-manage-container class="manage-form-header">
            <h1 class="manage-title">{{ $t('main_settings') }}</h1>
        </theme-manage-container>
        <theme-manage-container class="manage-form-body">    
            <div class="manage-wrapper-item row" style="z-index: 50;">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("language") }}</span>
                        </div>

                        <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="language_id">
                            <multiselect
                                v-model="language" track-by="id" label="name"
                                :options="getLanguages"
                                :allow-empty="false"
                                :searchable="true"
                                @select="selectLanguage"
                            />
                        </validation>
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item row" style="z-index: 50;">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("font") }}</span>
                        </div>

                        <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="font_id">
                            <multiselect
                                v-model="font" track-by="id" label="name"
                                :options="getFonts"
                                :allow-empty="false"
                                :searchable="true"
                                @select="selectFont"
                            />
                        </validation>
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("notifications") }}</span>
                        </div>

                        <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="notification_types">
                            <label v-for="type in getNotifyTypes" class="checkbox-inline font-additional-info">
                                <input type="checkbox" v-model="form.notification_types" v-bind:value="type.id">{{ $t(type.name) }}
                            </label>
                        </validation>
                    </div>
                </div>
            </div>

            <!--<div class="manage-wrapper-item row">
                <div class="col-sm-offset-1 col-sm-10 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <span>{{ $t("primary_color") }}</span>
                    </div>

                    <validation class="col-xs-12 col-sm-7" :validator="validator" label="primary_color">
                        <slider v-model="form.primary_color" @change="changePrimaryColor"/>
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-offset-1 col-sm-10 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <span>{{ $t("secondary_color") }}</span>
                    </div>

                    <validation class="col-xs-12 col-sm-7" :validator="validator" label="secondary_color">
                        <slider v-model="form.secondary_color" @change="changeSecondaryColor" />
                    </validation>
                </div>
            </div>-->

            <div class="manage-wrapper-item row">
                <Themes @selectTheme="changeTheme" />
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("background") }}</span>
                        </div>

                        <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="background">
                            <button-file ref="background" name="background" :accept="['image/*']" @change="uploadFile($event)"/>
                        </validation>
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("time_zone") }}</span>
                        </div>

                        <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="notification_types">
                            <multiselect
                                v-model="timeZone"
                                class="multiselect-timezone"
                                :options="getTimeZones"
                                :allow-empty="false"
                                :searchable="true"
                                @select="selectTimeZone"
                            />
                        </validation>
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12 col-lg-10">
                    <div class="row">
                        <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                            <span>{{ $t("audio_setting") }}</span>
                        </div>

                        <div class="col-xs-12 col-sm-7 col-lg-6">
                            <div
                                v-if="form.audio"
                                class="profile-audio"
                            >
                                <div
                                    v-for="(item, type) in form.audio"
                                    class="profile-audio-item"
                                >
                                    <span class="profile-audio-item-title">{{ $t(`audio_${type}`) }}</span>
                                    <div class="profile-audio-block">
                                        <div
                                            v-for="(sound, index) in getAudioSounds[type]"
                                            class="profile-audio-examples"
                                        >
                                            <input
                                                v-model="form.audio[type]"
                                                type="radio"
                                                :name="type"
                                                :value="sound"
                                            />
                                            <label :for="`${type}_${index}`">{{ sound.name }}</label>
                                            <audio-player :file="sound.file"></audio-player>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <theme-button-success
                                v-if="form.audio"
                                type="submit"
                                class="btn btn-md"
                                :disabled="isLoading"
                                @click.native.prevent="disableSounds"
                            >
                                {{ $t("disable_sounds") }}
                            </theme-button-success>
                            <div
                                v-if="! form.audio"
                                class="profile-audio"
                            >
                                <theme-button-success
                                    type="submit"
                                    class="btn btn-md"
                                    :disabled="isLoading"
                                    @click.native.prevent="enableSounds"
                                >
                                    {{ $t("enable_sounds") }}
                                </theme-button-success>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-offset-5 col-sm-7">
                            <theme-button-success type="submit" class="btn btn-md" :disabled="isLoading">
                                {{ $t("update") }}
                            </theme-button-success>

                            <theme-button-warning type="button" class="btn btn-md" @click.native="setDefaultSettings">
                                {{ $t("set_default_settings") }}
                            </theme-button-warning>
                        </div>
                    </div>
                </div>
            </div>
        </theme-manage-container>
    </form>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import {Slider}             from 'vue-color'

    import MixinForm            from '../../../mixins/form'
    import Validation           from '../../components/Validation'
    import ButtonFile           from "../../elements/button/ButtonFile";

    import Themes               from "@views/components/themes/Themes";
    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonWarning   from "@views/layouts/theme/buttons/ThemeButtonWarning";
    import AudioPlayer          from "@views/components/audio/AudioPlayer";

    export default {
		name: "profile-setting",
		components: {
			ButtonFile,
			Slider,
			Validation,
			Themes,
			ThemeManageContainer,
			ThemeButtonSuccess,
			ThemeButtonWarning,
			AudioPlayer
		},
		mixins: [
			MixinForm
		],
		data() {
            return {
                language: null,
                font: null,
                timeZone: null,
                form: {
                    language_id:                null,
                    font_id:                    null,
                    selected_color_scheme_id:   null, 
                    notification_types:         [],
                    background:                 null,
                    time_zone:                  null,
                    audio:                      null,
                },
            }
        },
		computed: {
            ...mapGetters({
                getNotifyTypes: 'default/getNotifyTypes',
                getLanguages: 'default/getLocalLanguages',
				getFonts: 'default/getFonts',
				getTimeZones: 'default/getTimeZones',
				getAudioSounds: 'default/getAudioSounds',
				getUserProfile: 'user/getUserProfile',
            })
        },
        mounted() {
            this.language                       = this.getUserProfile.language;
			this.font                           = this.getUserProfile.font;
			this.timeZone                       = this.getUserProfile.timeZone;

            this.form.font_id                   = this.font.id;
            this.form.language_id               = this.language.id;
            this.form.selected_color_scheme_id  = this.getUserProfile.selected_color_scheme_id;
			this.form.notification_types        = this.getUserProfile.notifyTypes;
			this.form.time_zone                 = this.getUserProfile.timeZone;
			this.form.audio                     = this.getUserProfile.audio;
        },
        methods: {
            changeTheme(themeId) {
                this.form.selected_color_scheme_id = themeId;
            },
            selectLanguage(value) {
                this.form.language_id = value.id;
                this.$store.dispatch('user/setLanguageById', value.id);
                this.$i18n.locale = this.getUserProfile.language.iso_639_1;
            },
            selectFont(value) {
                this.form.font_id = value.id;
                this.$store.dispatch('user/setFontById', value.id);
            },
			selectTimeZone(value) {
				this.form.time_zone = value;
				this.$store.dispatch('user/setTimeZone', value);
			},
            changePrimaryColor(color) {
                this.$store.dispatch('user/setPrimaryColor', color.hex);
            },
            changeSecondaryColor(color) {
                this.$store.dispatch('user/setSecondaryColor', color.hex);
            },
            updateProfile() {
                this.$api.user.updateProfile({...this.form}).then((res) => {
                    location.reload();
                    this.defaultResponse(res);
					window.app.$notify({type:'success', text: window.app.$t('update_user')});
                }).catch((err) => this.defaultError(err.response))
            },
			enableSounds() {
				this.form.audio = {
					finish_task: {}
                };
			},
			disableSounds() {
				this.form.audio = null;
				this.$store.dispatch('user/setAudio', this.form.audio);
			},
            uploadFile(e) {
                let file = e.target.files[0];
                if (file) {
                    let reader = new FileReader();

                    reader.onload = (e) => {
                        this.form.background = e.target.result;

                        this.$store.dispatch('user/setBackground', this.form.background);
                    };

                    reader.readAsDataURL(file);
                }
            },
            setDefaultSettings() {
                this.form.background = 'remove';
                this.$refs.background.clear();

                this.$store.dispatch('user/setFontById', null);
                this.$store.dispatch('user/setLanguageById', null);
                this.$store.dispatch('user/setNotifyTypes', null);

                this.$store.dispatch('user/setBackground', null);
                this.$store.dispatch('user/setPrimaryColor', null);
                this.$store.dispatch('user/setSecondaryColor', null);

				this.$store.dispatch('user/setDefaultTimeZone');

				this.$store.dispatch('user/setDisableAudio');
            }
        },
    }
</script>

<style lang="scss">

</style>
