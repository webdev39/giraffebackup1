import { mapGetters }   from 'vuex'
import find             from '@helpers/findInGroups'

export default {

    computed:{
        ...mapGetters({
            _getGroups:         "groups/getStateGroups",
            _getUserViewTypes:   "user/getUserViewTypes",
        }),
        _getCurrentBoard() {
            if (this._getGroups && this.$route.params.board_id) {
                return find.searchBoardById(this._getGroups, Number(this.$route.params.board_id));
            }

            return null;
        },
        _getCurrentGroup() {
            if (this._getGroups) {
                let groupId;

                if (this._getCurrentBoard) {
                    groupId = this._getCurrentBoard.group_id
                } else {
                    groupId = Number(this.$route.params.group_id);
                }

                return find.searchGroupById(this._getGroups, groupId);
            }
        },
        _getCurrentBoardId() {
            if (this._getCurrentBoard) {
                return this._getCurrentBoard.id;
            }
        },
        _getCurrentGroupId() {
            return this._getCurrentGroup.id;
        },
        _getCurrentGroupName() {
            if (this._getCurrentGroup) {
                return this._getCurrentGroup.name
            }
        },
        _getCurrentBoardName() {
            if (this._getCurrentBoard) {
                return this._getCurrentBoard.name
            }
        },
        _getUserViewType() {
            return this._getUserViewTypes.find(item => item.board_id === this._getCurrentBoardId)
        },
        _getUserViewTypeId () {
            if (this._getUserViewType) {
                return this._getUserViewType.view_type_id
            }

            return false;
        },
    },
};