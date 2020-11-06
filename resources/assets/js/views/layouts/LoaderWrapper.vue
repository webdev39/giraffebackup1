<template>
   <div class="lds-dual-ring login-lds-dual-ring" v-if="isAppLoading"></div>
</template>

<script>
    import 'nprogress/nprogress.css'

    import NProgress            from 'nprogress'
    import {mapGetters}         from 'vuex'

    export default {
        name: 'loader-wrapper',
        computed: {
            ...mapGetters({
                isLoading: 'loading/isLoading',
                isAppLoading: 'loading/isAppLoading',
            })
        },
        created() {
            NProgress.configure({ showSpinner: false });

            this.$store.watch((state) => state.loading.loading, (nextState) => {
                return nextState === true ? this.start() : this.done();
            });

            this.$store.watch((state) => state.loading.appLoading, (nextState) => {
                document.body.className = nextState === true ? 'appLoading' : '' ;
            });
        },
        methods: {
            start() {
                if (!this.isAppLoading) {
                    NProgress.start();
                }
            },
            done() {
                NProgress.done();
            }
        }
    }
</script>

<style lang="scss">
    body.appLoading {
        overflow: hidden;
    }

    .login-lds-dual-ring {
        width: 100px;
        height: 100px;
        transform: translate(-50%, -50%);
        margin: 0;

    }

    .login-lds-dual-ring:after {
        width: 100px;
        height: 100px;
        border: 10px solid #ffffff;
        border-color: #ffffff transparent #ffffff transparent;
    }

 
</style>