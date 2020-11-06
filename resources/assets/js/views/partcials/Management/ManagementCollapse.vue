<template>
    <div class="oc-collapse" :class="{active: show}">

        <styleContainer
            :bg-color="getCurrentTheme.modal.header_bg"
            :color="getCurrentTheme.management.collapse_color"
            style="border-radius: 10px"
        >
            <div
                @click="toggleCollapse"
                class="oc-collapse__header"
            >
                <slot name="header"></slot>
            </div>
        </styleContainer>

        <div
            v-if="show"
            class="oc-collapse__content"
        >
            <slot name="content"></slot>
        </div>

    </div>
</template>

<script>
	import { mapGetters } from 'vuex';
	import styleContainer from '@views/layouts/theme/styles/styleContainer';

    export default {
        props: {
            data: {
            	type: Array
            }
        },
		components: {
			styleContainer,
		},
        data() {
            return {
                show: false
            }
        },
        computed: {
			...mapGetters({
				getCurrentTheme: 'user/getCurrentTheme',
			}),
        },
        methods: {
            toggleCollapse() {
                this.show = !this.show;
				this.$emit('toggleShow', this.show);
            }
        }
    }
</script>