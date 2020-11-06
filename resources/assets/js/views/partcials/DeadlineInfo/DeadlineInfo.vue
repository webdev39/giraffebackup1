<template>
    <div class="group-board-info-wrapper">
        <div class="group-board-info">
            <div class="deadline-name" v-if="$route.name === 'deadline' && $route.params.period === 'day'">
                <span>
                    <i class="icon-calendar">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-calendar"></use></svg>
                    </i>
                </span>
                <div><span>{{ $t('your_day') }}</span></div>
            </div>
            <div class="deadline-name" v-if="$route.name === 'deadline' && $route.params.period === 'week'">
                <span class="">
                    <i class="icon-calendar">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-calendar"></use></svg>
                    </i>
                </span>
                <div><span>{{ $t('your_week') }}</span></div>
            </div>
            <div class="group-board-info__item">
                <subscribersByTask class="subscribers-board" />
            </div>
            <div class="group-board-info__item group-board-info__item_info-setting">
                <dropDown>
                    <div class="info-setting__list">
                        <div class="info-setting__item">
                            <div class="info-setting__item-text">
                                Sorting:
                            </div>
                            <div class="info-setting__item-control">
                                <board-task-sorting :sortTypes="getSortTypes" />
                            </div>
                        </div>
                    </div>
                </dropDown>
            </div>
        </div>
    </div>
</template>

<script>
    import timeMixin            from '@mixins/time'

    import subscribersByTask    from '@views/components/subscribers/subscribersByTask'
    import boardTaskSorting     from '@views/partcials/BoardTaskSorting/BoardTaskSorting'
    import dropDown             from '@views/components/dropDown/dropDown'

    export default {
        components: {
            subscribersByTask,
            dropDown,
            boardTaskSorting
        },
		mixins:[
			timeMixin
		],
        computed: {
            getSortTypes() {
                return this.$store.getters['groups/getSortTypes'].filter(item => item.name !== 'todo' )
            },
        },
    }
</script>

<style lang="scss">
    .priority-tag {
        border-radius: 4px;
        padding: 0px 4px;
        font-size: 12px;
        margin: 2px 4px;
        color: #fff;
    }
    .group-board-info__piority-setting{
        margin-left: 10px;
    }
    .subscribers-board{
        margin-left: 5px;
    }

    .deadline-name {
        display:flex;
        align-items:center;
        margin-right:5px;
        margin-left: 5px;
        min-height:30px;
        padding:5px;
    }
    .deadline-name .icon-calendar .icon {
        width:15px;
        height:15px;
        position:relative;
        top:2px;
        margin-right:4px;
    }
</style>
