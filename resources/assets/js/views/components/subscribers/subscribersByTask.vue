<template>
    <subscribers-view
        :members="_filterMembers"
        :subscribers="getSubscribers"
        :selectMembers="_getSelectMembers"
        :options="options"
        @click-member="_handleSelectMember"
    />
</template>

<script>
    import { mapGetters }     from 'vuex'

    import subscribersView  from '@views/components/subscribers/subscribers-view'
    import subscribersMixin from '@mixins/subscribers'

    export default {
        components: {
            subscribersView
        },
        mixins: [
            subscribersMixin
        ],
        data() {
            return {
                options: {
					isSelect: true,
					count: {
						desktop: 3,
						tablet:  3,
						phone:   3,
						phoneNewRow: 8,
					},
					showOthers: true,
                }
            }
        },
        computed: {
            ...mapGetters({
				getTasksList:   'task/getTasksList',
            }),
            getSubscribers() {
                let membersId = new Set();

                this.getTasksList.map(item => {
                    item.subscribers.task.map(subscriberId => membersId.add(subscriberId));
                });

                return Array.from(membersId);
            },
        },
	}
</script>
