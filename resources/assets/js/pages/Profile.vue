<template>
    <div id="manage-page" class="manage-profile">

        <!-- Heading -->
        <div class="manage-customers-navbar-header mb-2">
            <div class="manage-customers-navbar-title">
                {{ $t('user_profile') }}
            </div>
        </div>

        <!-- Form -->
        <form class="manage-form" @submit.prevent="updateUser">
            <theme-manage-container class="manage-form-header">
                <h1 class="manage-title">{{ $t('default') }}</h1>
             </theme-manage-container>
              <theme-manage-container class="manage-form-body">
                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("email") }}</span>
                            </div>

                            <div class="col-xs-12 col-sm-7 col-lg-6">
                                <input type="email" class="form-control" id="email" v-model="email"  :placeholder="$t('email')" autocomplete='email' disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("screen_name") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="nickname">
                                <input type="text" class="form-control" id="nickname" v-model="nickname" :placeholder="$t('screen_name')">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("first_name") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="name">
                                <input type="text" class="form-control" id="first_name" v-model="firstName" :placeholder="$t('first_name')">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("last_name") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="last_name">
                                <input type="text" class="form-control" id="last_name" v-model="lastName" :placeholder="$t('last_name')">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("avatar") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="avatar">
                                <button-file ref="avatar" name="avatar" :accept="['image/*']" @change="uploadFile($event, 'avatar')"/>
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-offset-5 col-sm-7">
                                <theme-button-success type="submit" class="btn btn-md" :disabled="isLoading">
                                    {{ $t("save") }}
                                </theme-button-success>
                                <theme-button-warning type="button" class="btn btn-md" @click.native="setDefaultAvatar">
                                    {{ $t("set_default_avatar") }}
                                </theme-button-warning>
                            </div>
                        </div>
                    </div>
                </div>
            
            </theme-manage-container>    
        </form>

        <!-- Form -->
        <form class="manage-form" @submit.prevent="updatePassword">
            <theme-manage-container class="manage-form-header">
                <h1 class="manage-title">{{ $t('change_password') }}</h1>
            </theme-manage-container>
            <theme-manage-container class="manage-form-body">
                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("old_password") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="old_password">
                                <input type="password" class="form-control" id="old_password" v-model="form.password.old_password" :placeholder="$t('current_password')" autocomplete="current-password">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("new_password") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="password">
                                <input type="password" class="form-control" id="password" v-model="form.password.password" :placeholder="$t('new_password')" autocomplete="new-password">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12 col-lg-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5 col-lg-offset-3 col-lg-3 control-label text-align-right">
                                <span>{{ $t("repeat_password") }}</span>
                            </div>

                            <validation class="col-xs-12 col-sm-7 col-lg-6" :validator="validator" label="password_confirmation">
                                <input type="password" class="form-control" id="password_confirmation" v-model="form.password.password_confirmation" :placeholder="$t('repeat_new_password')" autocomplete="new-password">
                            </validation>
                        </div>
                    </div>
                </div>

                <div class="manage-wrapper-item row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="row">
                            <div class="col-xs-12 col-sm-offset-5 col-sm-7">
                                <theme-button-success type="submit" class="btn btn-md" :disabled="isLoading">
                                    {{ $t("save") }}
                                </theme-button-success>
                            </div>
                        </div>
                    </div>
                </div>
            </theme-manage-container>
        </form>

        <!-- Form --> 
        <profile-setting/>

        <!-- Form -->
        <theme-manage-container class="manage-form">

            <div class="manage-wrapper-item row text-center">
                <manage-web-push-subscription />
            </div>

        </theme-manage-container >
        
    </div>
</template>

<script>
    import {mapGetters}                 from 'vuex'
    import {Slider}                     from 'vue-color'

    import MixinForm                    from '@mixins/form'
    import Validation                   from '@views/components/Validation'
    import ButtonFile                   from "@views/elements/button/ButtonFile";
    import ProfileSetting               from "@views/layouts/profile/ProfileSetting";
    import ManageWebPushSubscription    from "@views/partcials/ManageWebPushSubscription";

    import ThemeManageContainer         from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess           from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonWarning           from "@views/layouts/theme/buttons/ThemeButtonWarning";

    export default {
        data() {
            return {
                default: {},
                form:{
                    profile:  {
                        email:      null,
                        name:       null,
                        last_name:  null,
                        nickname:   null,
                        avatar:     null,
                    },
                    password: {
                        password:               null,
                        password_confirmation:  null,
                        old_password:           null,
                    },
                },
            }
        },
        computed: {
            ...mapGetters({
                getUserProfile:     'user/getUserProfile',
            }),
            email: {
                get: function() {
                    return this.form.profile.email = this.getUserProfile.email
                },
                set: function(value) {
                    this.form.profile.email = value
                },
            },
            firstName: {
                get: function() {
                    return this.form.profile.name = this.getUserProfile.firstName
                },
                set: function(value) {
                    this.form.profile.name = value
                },
            },
            lastName: {
                get: function() {
                    return this.form.profile.last_name = this.getUserProfile.lastName
                },
                set: function(value) {
                    this.form.profile.last_name = value
                },
            },
            nickname: {
                get: function() {
                    return this.form.profile.nickname = this.getUserProfile.nickname
                },
                set: function(value) {
                    this.form.profile.nickname = value
                },
            },
            avatar: {
                get: function() {
                    return this.getUserProfile.avatar
                },
                set: function(avatar) {
                    this.form.profile.avatar = avatar;
                    this.$store.dispatch('user/setAvatar', avatar);
                },
            },
        },
        components: {
            ProfileSetting,
            ManageWebPushSubscription,
            ButtonFile,
            Slider,
            Validation,
            ThemeManageContainer,
            ThemeButtonSuccess,
            ThemeButtonWarning
        },
        mixins: [
            MixinForm
        ],
        created() {
            this.default = {...this.getUserProfile};
        },
        mounted() {
			this.$nextTick(() => { this.$store.dispatch('setPagePreloader', false); });
        },
        methods: {
            updateUser() {
                this.$api.user.updateUser(this.form.profile).then((res) => {
                    this.defaultResponse(res);
                }).catch(err => this.defaultError(err.response))
            },
            updatePassword() {
                this.$api.user.changePassword(this.form.password).then((res) => {
                    this.defaultResponse(res);
                }).catch(err => this.defaultError(err.response))
            },
            uploadFile(e, type) {
                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = (e) => {
                        this[type] = e.target.result;
                    };

                    reader.readAsDataURL(file);
                    
                }
            },
            setDefaultAvatar() {
                this.form.profile.avatar = 'remove';
                this.$refs.avatar.clear();

                this.$store.dispatch('user/setAvatar',          null);
                this.updateUser();
            },
        },
    }
</script>
