<!--Optimized-->
<template>
    <auth-wrapper :registerLink="Object.keys(user).length" :resetLink="Object.keys(user).length"  >
        <template slot="body">
            <div class="form-group">
                <form @submit.prevent="handleConfirm" class="confirm-form" autocomplete="nope" v-if="Object.keys(user).length">
                    <p id="welcome-text">
                        {{ $t("welcome") }} {{ user.name }}
                    </p>

                    <validation class="form-group" :validator="validator" label="type">
                        <div class="radio confirm-radio">
                            <label>
                                <input type="radio" name="type" v-model="form.type" value="project_name" />{{ $t("project_name") }}
                            </label>
                        </div>

                        <div class="radio confirm-radio">
                            <label>
                                <input type="radio" name="type" v-model="form.type" value="company_name"/>{{ $t("company_name") }}
                            </label>
                        </div>
                    </validation>

                    <validation :validator="validator" label="name">
                        <input type="text" name="name" id="name" v-model="form.name" :placeholder="placeholder" :disabled="!form.type || isLoading" required>
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
    import formMixin        from "@mixins/form";
    import AuthWrapper      from "@views/layouts/AuthWrapper";
    import Validation       from "@views/components/Validation";

    export default {
        data(){
            return{
                user: {},
                form: {
                    type:       'project_name',
                    name:       null,
                    hash:       null,
                    user_id:    null,
                }
            }
        },
        computed: {
            placeholder(){
                if(this.form.type === 'project_name'){
                    return this.$t('project_name');
                }

                return this.$t('company_name');
            },
        },
        components:{
            Validation,
            AuthWrapper
        },
        mixins: [
            formMixin,
        ],
        mounted() {
            this.form.hash = this.$route.query.confirm_hash;

            this.$api.auth.getConfirmToken(this.form.hash).then((data) =>{
                this.user = data.user;
            }).catch((err) => {
                this.$router.push({ name: 'home' });
                this.$notify({type:'error', text: err.response.data.message});
            })
        },
        methods: {
            handleConfirm() {
                if (!this.form.type || !this.form.name) {
                    return this.$notify({type:'error', text: this.$t('not_valid_input')});
                }

                this.form.user_id = this.user.id;

                this.$api.auth.confirm(this.form).catch((err) => {
                    this.defaultError(err.response);
                })
            }
        }
    }
</script>
