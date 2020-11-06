<template>
    <subscribers-view
        :members="_getMembers"
        :subscribers="getSubscribers"
        :selectMembers="_getSelectMembers"
        :options="options"
        @click-member="_handleSelectMember"
    />
</template>

<script>
    import {mapGetters}     from 'vuex'

    import subscribersView  from '@views/components/subscribers/subscribers-view'
    import subscribersMixin from '@mixins/subscribers'
    import find             from '@helpers/findInGroups'

    export default {
		components: {
			subscribersView
		},
		mixins: [
			subscribersMixin
		],
        props: {
            group_id: {
                type: Number
            },
        },
		data() {
			return {
				options: {
					isSelect:   true,
					count:      {
						desktop: 'infinite',
						phone:   3,
						tablet: 16,
						phoneNewRow: 8,
					},
					showOthers: true
				}
			}
		},
        computed: {
            ...mapGetters({
                getGroups: 'groups/getStateGroups',
            }),
            getSubscribers() {
                return find.searchGroupById(this.getGroups, this.group_id).members
            }
        },
	}
</script>