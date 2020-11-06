<template>
    <div v-if="isLoggedIn">
        <board-list-modal />
        <board-setting-modal />
        <choose-modal />
        <confirm-modal />
        <customer-setting-modal />
        <filter-setting-modal />
        <group-setting-modal />
        <invite-user-modal />
        <light-box-modal />
        <member-setting-modal />
        <priority-setting-modal />
        <role-modal />
        <pipeline-modal />
        <pipeline-rule-modal />
        <info-setting-modal />
        <image-comment-modal />
        <choose-members-modal />

        <!--Not optimization-->
        <create-bill-modal />
        <edit-bill-modal />
        <add-bill-modal />
        <theme-setting-modal />
    </div>
</template>

<script>
    import BoardListModal       from "@views/modals/BoardListModal";
    import BoardSettingModal    from "@views/modals/BoardSettingModal";
    import CustomerSettingModal from "@views/modals/CustomerSettingModal";
    import FilterSettingModal   from "@views/modals/FilterSettingModal";
    import GroupSettingModal    from "@views/modals/GroupSettingModal";
    import InviteUserModal      from "@views/modals/InviteUserModal";
    import LightBoxModal        from "@views/modals/LightBoxModal";
    import MemberSettingModal   from "@views/modals/MemberSettingModal";
    import PrioritySettingModal from "@views/modals/PrioritySettingModal";
    import RoleModal            from "@views/modals/RoleModal";
    import CreateBillModal      from "@views/modals/old/CreateBillModal";
    import EditBillModal        from "@views/modals/old/EditBillModal";
    import AddBillModal         from "@views/modals/old/AddBillModal";
    import ChooseModal          from "@views/modals/ChooseModal";
    import ConfirmModal         from "@views/modals/ConfirmModal";
    import PipelineModal        from "@views/modals/PipelineModal";
    import PipelineRuleModal    from "@views/modals/PipelineRuleModal";
    import ChooseMembersModal   from "@views/modals/ChooseMembersModal";
    import ImageCommentModal    from "@views/modals/ImageCommentModal";
    import InfoSettingModal     from "@views/modals/InfoSettingModal";
    import ThemeSettingModal    from "@views/modals/ThemeSettingModal";

    export default {
        name: "external-modals",
        components: {
            InfoSettingModal,
            PipelineRuleModal,
            PipelineModal,
            AddBillModal,
            BoardListModal,
            BoardSettingModal,
            ChooseModal,
            ConfirmModal,
            CreateBillModal,
            CustomerSettingModal,
            EditBillModal,
            FilterSettingModal,
            GroupSettingModal,
            InviteUserModal,
            LightBoxModal,
            MemberSettingModal,
            PrioritySettingModal,
            RoleModal,
            ImageCommentModal,
            ChooseMembersModal,
            ThemeSettingModal
        }
    }
</script>
