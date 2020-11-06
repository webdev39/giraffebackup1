<template>
    <div class="v-align-center" v-if="!isAppLoading">
        <div class="login-wrapper login-container">
            <language-button></language-button>

            <div id="oc-logo-login">
                <img src="/images/oclogo-big.png" />
            </div>

            <div class="login-container">
                <slot name="body"></slot>

                <div class="footer">
                    <div class="login-help">
                        <router-link to="/register" v-if="registerLink">
                            <a class="account-erstellen-link logform-actions">
                                {{ $t('create_account') }}
                            </a>
                        </router-link>

                        <router-link to="/reset-password" v-if="resetLink">
                            <a class="account-erstellen-link logform-actions">
                                {{ $t('forgot_password') }}
                            </a>
                        </router-link>

                        <router-link to="/login" v-if="loginLink">
                            <a class="account-erstellen-link logform-actions">
                                {{ $t('to_login') }}
                            </a>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'
    import LanguageButton       from '@views/elements/LanguageButton'

    export default {
        props: {
            registerLink: {
                type: [Boolean, Number],
                default: true,
            },
            resetLink: {
                type: [Boolean, Number],
                default: true,
            },
            loginLink: {
                type: [Boolean, Number],
                default: false,
            }
        },
        computed:{
            ...mapGetters({
                isAppLoading:   'loading/isAppLoading',
            }),
        },
        components: {
            LanguageButton,
        }
    }
</script>

<style lang="scss">
    .login-wrapper {
        .is-invalid {
            input {
                border-bottom: solid 1px rgba(252, 52, 52, 0.84) !important;
            }

            div.invalid-feedback {
                display: block;
                color: #ffc8c8 !important;
                font-weight: 700;
            }
        }

        .is-valid {
            input.form-control {
                border-color: #c8ffc8 !important;
            }
        }

        .waiting-spinner {
            color: #c8c8c8 !important;
        }
    }
</style>
