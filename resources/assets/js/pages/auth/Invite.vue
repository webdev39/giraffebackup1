<!--Optimized-->
<template>
    <auth-wrapper>
        <template slot="body">
            <div class="form-group" >
                <form @submit.prevent="handleInviteSubmit">
                    <p id="welcome-text">
                        {{ $t("welcome") }} {{ form.user_name }}
                    </p>

                    <validation :validator="validator" label="password">
                        <input type="password" id="password" name="password" v-model="form.password" :placeholder="$t('password')" required>
                    </validation>

                    <button type="submit" class="btn btn-default form-btn">
                        {{ $t("confirm") }}
                    </button>
                </form>
            </div>
        </template>
    </auth-wrapper>
</template>

<script>
    import formMixin            from '@mixins/form'
    import Validation           from "@views/components/Validation";
    import AuthWrapper          from "@views/layouts/AuthWrapper";

    export default {
        data() {
            return {
                user: {},
                form: {
                    confirm_hash:   null,
                    invite_hash:    null,
                    invite_user:    true,
                    user_id:        null,
                    user_name:      null,
                    password:       null,
                },
            }
        },
        components: {
            AuthWrapper,
            Validation,
        },
        mixins: [
            formMixin
        ],
        mounted() {
            let { confirm_hash, invite_hash } = this.$route.query;

            if (!invite_hash) {
                invite_hash = this.$route.query['amp;invite_hash'];
            }

            this.form = { invite_hash, confirm_hash };

            this.$api.auth.getJoin(this.form).then((data) => {
                this.form.user_id      = data.user.id;
                this.form.user_name    = data.user.name;

                this.defaultResponse();
            }).catch((err) => {
                this.$router.push({ name: 'home' });
                this.defaultError(err.response)
            });
        },
        methods: {
            handleInviteSubmit() {
                this.$api.auth.join(this.form).then(() => {
                    this.defaultResponse();
                }).catch((err) => {
                    this.defaultError(err.response)
                });
            }
        }
    }
</script>
