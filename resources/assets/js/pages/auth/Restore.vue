<template>
    <auth-wrapper>
        <template slot="body">
            <div class="form-group">
                <form @submit.prevent="handleForgetPassword" class="confirm-form" autocomplete="nope">
                    <p id="welcome-text">
                        {{ $t("reset_password") }}
                    </p>

                    <validation class="password-wrapper" :validator="validator" label="name">
                        <input :type="inputType" class="password-with-eye" id="password" name="password" v-model="form.password" :placeholder="$t('new_password')" required>
                    </validation>

                    <button type="submit" class="btn btn-default form-btn">
                        {{ $t('reset') }}
                    </button>
                </form>
            </div>
        </template>
    </auth-wrapper>
</template>

<script>
    import formMixin                from "@mixins/form";
    import AuthWrapper              from "@assets/js/views/layouts/AuthWrapper";
    import ContentLoading           from "@views/components/ContentLoading";
    import Validation               from "@views/components/Validation";

    export default {
        data() {
            return {
                user: {},
                form: {
                    password:   null,
                    token:      null,
                },
                showPassword: false,
            }
        },
        computed: {
            inputType() {
                return !this.showPassword ? 'password' : 'text' ;
            }
        },
        components: {
            Validation,
            ContentLoading,
            AuthWrapper
        },
        mixins: [
            formMixin
        ],
        mounted() {
            this.form.token = this.$route.query.resetToken;

            this.$api.auth.getRestoreToken(this.form.token).then((data) => {
                this.user = data.user;
            }).catch((err) => {
                this.$notify({type:'error', text: err.response.data.message});
            })
        },
        methods: {
            handleForgetPassword() {
                this.$api.auth.restorePassword(Object.assign(this.form, {user_id: this.user.id})).catch((err) => {
                    this.defaultError(err.response);
                });
            }
        }
    }
</script>
