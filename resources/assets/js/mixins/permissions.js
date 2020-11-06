import { mapGetters }  from "vuex";
import find            from "@helpers/findInGroups";

// Optimized

export default {
    computed: {
        ...mapGetters({
            globalPermissions: 'permissions/getPermissions',
            groupPermissions:  'groups/getGroupsPermissions',
        }),
    },
    methods: {
        /**
         * Get a message about no access permissions
         *
         * @param {String} permission
         * @returns {*|void}
         */
        getPermissionMessage(permission)  {
            return window.app.$t('permissionMessage', {'permission': permission});
        },

        /**
         * Send notification about no access permissions
         *
         * @param {string} permission
         * @returns {*|void}
         */
        sendNotifyPermissionInfo(permission){
            this.$notify({type:'info', text: this.getPermissionMessage(permission)});
        },

        /**
         * Checking user permissions
         *
         * @param {string}      name
         * @param {boolean}     notify
         * @returns {boolean}
         */
        checkPermission(name, notify = false) {
            if (typeof name === "string") {
                if (this.globalPermissions.find(permission => permission.name === name)) {
                    return true;
                }

                if (notify) {
                    this.sendNotifyPermissionInfo(name);
                }

                return false;
            }
        },

        /**
         * Checking user permissions for board
         *
         * @param name
         * @param boardId
         * @returns {*}
         */
        handlePermissionByBoardId(name, boardId) {
            let permission  = this.groupPermissions.find(permission => permission.name === name);
            let group       = find.searchGroupByBoardId(this.$store.getters['groups/getGroups'], boardId);

            if (!group || !permission) {
                return false;
            }

            return group.permissions.includes(permission.id);
        },

        /**
         * Checking user permissions for group
         *
         * @param name
         * @param groupId
         * @returns {*}
         */
        handlePermissionByGroupId(name, groupId) {
            let permission = this.groupPermissions.find(permission => permission.name === name);

            let groups = this.$store.getters['groups/getStateGroups'];

            let group = find.searchGroupById(groups, groupId);

            if (!group || !permission) {
                return false;
            }
            
            return group.permissions.includes(permission.id);
        },

        /**
         * Checking management
         *
         * @param groupId
         * @returns {*}
         */
        handleManagementByGroupId(groupId) {
            let group = find.searchGroupById(this.$store.getters['groups/getGroups'], groupId);

            if (group) {
                return true;
            }

            return false;
        }
    }
};
