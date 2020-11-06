<template>
    <div class="year-overview-pagination">
        <multiselect
            v-model="currentYear"
            :options="years"
            :show-labels="false"
            deselect-label=""
            :allow-empty="false"
            class="multiselect_size_s"
            :searchable="false"
            @select="year => $emit('page-changed', +years.indexOf(year) + 1)"
        ></multiselect>
    </div>
</template>

<script>
    export default {
        props: {
            years: {
            	type: Array,
                default: () => ([])
            },
            current: {
            	type: Number
            }
        },
        data() {
        	return {
                currentYear: '',
            }
        },
        mounted() {
            this.currentYear = this.current;
        },
    }
</script>