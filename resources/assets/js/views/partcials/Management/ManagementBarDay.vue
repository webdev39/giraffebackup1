<template>
    <div class="bar-day">
        <div class="bar-day__item"  v-for="day in getEvents">
            <div class="bar-day__value" :class="getClassDayValue(day.tasks)">
                <div class="bar-day__task" :title="`tasks: ${day.tasks}`" v-for="tasks in getTasksPerDay(day.tasks)"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
          days:     { type: Array },
          tasks:    { type: [Array, Object] }
        },
        computed:{
            getDays() {
                return this.days;
            },
            getTasks() {
                return this.tasks;
            },
            getEvents() {

                let tasks = this.getTasks;
                let days = this.getDays;

                if(!Array.isArray(tasks)) {
                    tasks = Object.entries(tasks).map( ([date, tasks]) => ({date,tasks}) );
                }

                let dayTasks = {};

                return _.map(days, function(item) {
                    dayTasks = _.find(tasks, { 'date' : item.date });

                    if (dayTasks) {
                        return dayTasks
                    }

                    return item;
                });
            },
        },
        methods: {
            getClassDayValue: function (count) {
                if(count > 20 ) {
                    return 'full'
                } else if (count <= 20 && count > 15){
                    return 'much'
                } else if (count <= 15 && count > 9){
                    return 'enough'
                }
            },
            getTasksPerDay: function(count) {
                if (count > 9) {
                    return 9
                }

                return count;
            }
        }
    }
</script>

<style lang="scss">

    
</style>