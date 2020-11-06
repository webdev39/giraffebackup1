<!--Optimized-->
<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="40%" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1000">
        <modal-wrapper :name="$options.name" color="white">
            <template slot="title">
                {{ $t('choose_members') }}
            </template>
            <template slot="body">
                <table class="choose-members-modal-table">
                    <thead>
                        <tr>
                            <th>{{ $t('member_name') }}  </th>
                            <th class="choose-members-modal-table__column_input">{{ $t('move_member') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(member, index) in getMembers" :key="member.id">
                            <td>
                                {{ member.user.name }} {{ member.user.last_name }}
                            </td>
                            <td class="choose-members-modal-table__column_input">
                                <input class="choose-members-modal-table__checkbox" type="checkbox" :value="member.id" v-model="checkedMembers" :disabled="isLoading" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>

            <template slot="footer">
                <theme-button-close type="button" class="btn" @click.native="close" :disabled="isLoading">
                    {{ $t("close") }}
                </theme-button-close>
                <theme-button-success type="button" class="btn" @click.native="handleUpdateBoard" :disabled="isLoading">
                    {{ $t("save") }}
                </theme-button-success>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import ModalWrapper         from "@views/layouts/ModalWrapper";

    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonClose     from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "choose-members-modal",
        data() {
            return {
                form: {
                    members: [],
                    data: {}
                },
                checkedMembers:     [],
            };
        },
        computed: {
            getMembers: {
                get: function () {
                    return this.$store.getters['members/getMembers'].filter(item => {
                        if (!item.user.status) {
                           return false
                        }
                        if (this.form.members.includes(item.id)) {
                            item.new_group = false;
                            return true
                        }
                    });
                },
            }
        },
        components: {
            ModalWrapper,
            ThemeButtonSuccess,
            ThemeButtonClose
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.members) {
                    this.form.members = event.params.members;
                }

                if (event.params.data) {
                    this.form.data = event.params.data;
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            handleUpdateBoard() {
                this.form.data.members = this.checkedMembers;

                this.$api.board.updateBoard(this.form.data, 'board_res=long', true).then(() => {
                    this.closeModal();
                }).catch((err) => {
                    this.defaultError(err.response);
                })
            },
            close() {
                this.closeModal();
            },
            closeModal() {
                this.$modal.hide("choose-members-modal");
            },
        }
    }
</script>

<style lang="scss">
    .choose-members-modal-table{
        width: 100%;

        tr{
            background-color: aliceblue;
            border-bottom: 1px solid #fff;
        }
        td, th{
            padding: 10px 20px;
        }

        .choose-members-modal-table__column_input{
            width: 135px;
            text-align: right;
        }
        .choose-members-modal-table__checkbox{
            width: 15px;
            height: 15px;
        }

    }
</style>
