import { mapGetters }  from "vuex";

export default {
    props: {
        members: {
            type: Array,
            default:() => {
                return [];
            }
        }
    },
    computed: {
        ...mapGetters({
            _getMembers:         'members/getMembers',
            _getSelectMembers:   'members/getSelectMembers',
        }),
        _filterMembers() {
            if (!this.members || !this.members.length) {
                return this._getMembers
            }

            return this._getMembers.filter(member => this.members.some(id => id === member.id))
        },
    },
    beforeDestroy(){
        this.$store.dispatch('members/clearSelectMembers');
    },
    methods: {
        _handleSelectMember(member) {
            if (this._getSelectMembers.some(item => item === member.id)) {
                this.$store.dispatch('members/removeSelectMembers', member.id);
            } else {
                this.$store.dispatch('members/addSelectMembers', member.id);
            }
        }
    }
};
