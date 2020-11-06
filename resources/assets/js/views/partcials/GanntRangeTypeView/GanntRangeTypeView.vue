<template>
    <div class="board-range-type-select">
        <input v-model="range" list="scales" max="4" data-range='{"animate": true}' type="range">
        <datalist id="scales">
            <select>
                <option :value="scale.name" :label="scale.name" v-for="scale in scales.levels"/>
            </select>
        </datalist>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        data() {
            return {
                range: 'hours'
            }
        },
        computed: {
            ...mapGetters({
                rangeGanttTypeView: 'groups/getRangeGanttTypeView',
                scales: 'task/getScales'
            }),
        },
        created() {
            this.scales.levels.find((element, index, array) => {
                if (element.name === this.rangeGanttTypeView.name) {
                    this.range = index;
                }
            });
        },
        watch: {
            range: function (value) {
                this.$store.dispatch('groups/changeRangeGanttTypeView', this.scales.levels[value]);
            }
        },
    }
</script>