<template>
    <div class="v-align-center">
        <div class="login-wrapper">
            <language-button />

            <div id="oc-logo-login">
                <img src="/sass/images/oclogo-big.png" />
            </div>

            <slot></slot>
        </div>
    </div>
</template>

<script>
    import LanguageButton       from '@views/elements/LanguageButton'

    export default {
        name: "form-wrapper",
        components: {
            LanguageButton,
        },
    }
</script>