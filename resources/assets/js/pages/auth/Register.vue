<template>
    <auth-wrapper :register-link="false" :reset-link="!showMessage" :login-link="!showMessage">
        <template slot="body" v-if="showMessage">
            <div class="notify_after_registr">
                <p>{{ $t("welcome") }}, {{ user.name }} {{ user.last_name }}!</p>
                <p>{{ $t("check_your_email_continue_registration")}}</p>
            </div>
        </template>

        <template slot="body" v-else>
            <div class="form-group account-create-formular">
                <form @submit.prevent="handleRegister" autocomplete="nope">
                    <validation :validator="validator" label="name">
                        <input type="text" id="firstName" name="name" v-model="form.name" :placeholder="$t('first_name')" required>
                    </validation>

                    <validation :validator="validator" label="last_name">
                        <input type="text" id="lastName" name="lastName" v-model="form.last_name" :placeholder="$t('last_name')" required>
                    </validation>

                    <validation :validator="validator" label="email">
                        <input type="email" id="email" name="email" v-model="form.email" :placeholder="$t('email')" required>
                    </validation>

                    <validation class="password-wrapper" :validator="validator" label="password">
                        <input :type="inputType" id="password" name="password" v-model="form.password" class="password-with-eye" :placeholder="$t('password')" required>
                        <span class="eye-icon glyphicon glyphicon-eye-open" :class="[showPassword ? 'glyphicon-eye-close' : 'glyphicon-eye-open']" @click="showPassword = !showPassword"></span>
                    </validation>

                    <button type="submit" class="btn btn-default form-btn">
                        {{ $t("create_account") }}
                    </button>
                </form>
            </div>
        </template>
    </auth-wrapper>
</template>

<script>
    import {mapGetters}     from "vuex";

    import formMixin        from "@mixins/form";
    import AuthWrapper      from "@views/layouts/AuthWrapper";
    import Validation       from "@views/components/Validation";
    import ContentLoading from "@assets/js/views/components/ContentLoading";

    export default {
        data() {
            return {
                user: {},
                form: {
                    name:       null,
                    last_name:  null,
                    email:      null,
                    password:   null,
                },
                showPassword: false,
            }
        },
        computed: {
            inputType() {
                return !this.showPassword ? 'password' : 'text' ;
            },
            showMessage() {
                return Object.keys(this.user).length > 0;
            }
        },
        components: {
            ContentLoading,
            Validation,
            AuthWrapper,
        },
        mixins: [
            formMixin
        ],
        methods: {
            handleRegister() {
                this.$api.auth.register(this.form).then((data) => {
                    this.user = data.user;
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            }
        }
    }
</script>

<style>
    .notify_after_registr {
        color: #fff;
        font-size: 16px;
        white-space: pre-line;
        text-align: center;
    }
</style>
