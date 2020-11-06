<!--Optimized-->
<template>
    <auth-wrapper>
        <template slot="body">
            <div class="form-group">
                <form @submit.prevent="handleLoginSubmit" autocomplete="nope">
                    <validation :validator="validator" label="email">
                        <input type="email" name="email" id="email" class="font-additional-info" :placeholder="$t('email')" v-model="form.email" autocomplete='email' required>
                    </validation>

                    <validation class="password-wrapper" :validator="validator" label="email">
                        <input :type="typePassword" name="password"  id="password" class="password-with-eye" :placeholder="$t('password')" v-model="form.password" autocomplete='current-password' required>
                        <span class="eye-icon glyphicon" :class="iconPassword" @click="isShowPassword = !isShowPassword"></span>
                    </validation>

                    <button type="submit" class="btn btn-default form-btn">
                        {{ $t("login") }}
                    </button>
                </form>
            </div>
            <div class="form-group">
                <div class="form-social">
                    <span>{{ $t("login_with") }}</span>

                    <div class="form-social-list">
                        <div class="form-social-item">
                            <a href="/signup/twitter" target="_blank" class="form-social-link">
                                <i class="icon-user">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-social-twitter"></use></svg>
                                </i>
                            </a>
                        </div>

                        <div class="form-social-item">
                            <a href="/signup/facebook" target="_blank" class="form-social-link">
                                <i class="icon-user">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-social-facebook"></use></svg>
                                </i>
                            </a>
                        </div>

                        <div class="form-social-item">
                            <a href="/signup/github" target="_blank" class="form-social-link">
                                <i class="icon-user">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-social-github"></use></svg>
                                </i>
                            </a>
                        </div>

                        <div class="form-social-item">
                            <a href="/signup/google" target="_blank" class="form-social-link">
                                <i class="icon-user">
                                    <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-social-google"></use></svg>
                                </i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </auth-wrapper>
</template>

<script>
    import MixinForm            from '@mixins/form'
    import Validation           from '@views/components/Validation'
    import AuthWrapper          from "@views/layouts/AuthWrapper";
    import ContentLoading       from "@views/components/ContentLoading";

    export default {
		components: {
			ContentLoading,
			AuthWrapper,
			Validation
		},
		mixins: [
			MixinForm
		],
        data() {
            return {
                form: {
                    email:      null,
                    password:   null
                },
                isShowPassword: false,
            }
        },
        computed: {
            typePassword() {
                return !this.isShowPassword ? 'password' : 'text';
            },
            iconPassword() {
                return this.isShowPassword ? 'glyphicon-eye-close' : 'glyphicon-eye-open';
            }
        },
        methods: {
            handleLoginSubmit() {
                this.$store.dispatch('loading/setAppLoading', true);

                this.$api.auth.login(this.form).then(() => {
                    this.$store.dispatch('loading/setAppLoading', false);
                }).catch((err) => {
                    this.$store.dispatch('loading/setAppLoading', false);
                    this.defaultError(err.response)
                });
            }
        }
    }
</script>
