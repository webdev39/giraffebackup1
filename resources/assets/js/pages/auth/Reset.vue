<template>
    <auth-wrapper :login-link="true" :reset-link="false">
        <template slot="body">
            <div class="form-group password-reset-formular">
                <form @submit.prevent="handleForgetPassword" class="confirm-form" autocomplete="nope">
                    <p id="welcome-text">
                        {{ $t("reset_password") }}
                    </p>

                    <validation class="password-wrapper" :validator="validator" label="email">
                        <input type="email" id="loginEmail" name="email" v-model="form.email" :placeholder="$t('email')" required>
                    </validation>

                    <button type="submit" class="btn btn-default form-btn">
                        {{ $t("reset") }}
                    </button>
                </form>
            </div>
        </template>
    </auth-wrapper>
</template>

<script>
    import formMixin        from "@mixins/form";
    import AuthWrapper      from "@views/layouts/AuthWrapper";
    import Validation       from "@views/components/Validation";

    export default {
        data() {
            return {
                form: {
                    email: null,
                },
            }
        },
        components: {
            Validation,
            AuthWrapper,
        },
        mixins: [
            formMixin
        ],
        methods: {
            handleForgetPassword() {
                this.$api.auth.resetPassword(this.form).then((res) =>{
                    this.resetComponentData();
                }).catch((err) => {
                    this.defaultError(err.response);
                });
            }
        }
    }
</script>
