<template>
    <subscribers-view :members="getMembers" :subscribers="getSubscribers" :options="options"  />
</template>

<script>
    import {mapGetters}     from 'vuex'

    import subscribersView  from '@views/components/subscribers/subscribers-view'

    export default {
        props: {
            task: {
                type: Object
            }
        },
        components: {
            subscribersView
        },
        computed: {
            ...mapGetters({
                getMembers:         'members/getMembers',
            }),
            getSubscribers() {
                return this.task.subscribers.task
            }
        },
        data() {
            return {
                options: {
					isSelect: false,
					count: {
						desktop: 3,
						tablet:  3,
						phone:   3,
						phoneNewRow: 8,
					},
					showOthers: true,
                }
            }
        }
    }
</script>