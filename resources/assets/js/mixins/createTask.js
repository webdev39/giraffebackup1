import { mapGetters } from 'vuex'

export default {
	data() {
		return {
			showUser: false,
			showDeadline: false
		}
	},
	props: {
		_currentBoard:   { type: Object, default: null }
	},
	computed: {
		...mapGetters({
			_getCurrentDraftTask:    'groups/getCurrentDraftTask',
		}),
		_getCurrentBoard() {
			return this._currentBoard || this.$store.getters['groups/getCurrentBoard']
		},
		_getCurrentGroupId() {
			return this._getCurrentBoard.group_id
		},
	},
	methods: {
		_showDraftDetail() {
			if (!this._getCurrentBoard) {
				return this.$notify({type:'error', text: this.$t('choose_board_please')});
			}

			if (!this.handlePermissionByGroupId('create-task', this._getCurrentGroupId)) {
				return this.sendNotifyPermissionInfo('create-task');
			}

			this.$router.replace({query: {taskId: this._getCurrentDraftTask.id}});
		},
		_showModalSubscriber() {
			if (!this._getCurrentBoard) {
				return this.$notify({type:'error', text: this.$t('choose_board_please')});
			}

			this.showUser = !this.showUser;
		},
		_showModalDeadline() {
			if (!this._getCurrentBoard) {
				return this.$notify({type:'error', text: this.$t('choose_board_please')});
			}

			this.showDeadline = !this.showDeadline;
		},
		_hideModalSubscriber() {
			this.showUser = false;
		},
		_hideModalDeadline() {
			this.showDeadline = false;
		},
	},
};
