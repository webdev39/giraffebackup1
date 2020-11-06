<template>
    <div class="comment-item-likes" @mouseover="getLikesMembers(comment)" @mouseleave="hideTitleMembers">
        <i class="fa fa-heart custom-fa-log-del" @click="handleToggleLike(comment)"></i>
        <tooltip v-if="likesMembers.length && showTitleMembers" position="left" class="comment-item-likes__members">
            <div class="comment-item-likes__member" v-for="member in likesMembers">
                {{ member.user.name }} {{ member.user.last_name }}
            </div>
        </tooltip>
        <span v-if="comment.reactions.like " class="comment-item-likes__count">
            {{ comment.reactions.like }}
        </span>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import tooltip      from '@views/components/tooltip/tooltip'

    export default {
        components: {
            tooltip
        },
        data() {
            return {
                likesMembers: [],
                showTitleMembers: false,
                fetchLikesMembers: false
            }
        },
        props: {
            comment: {
              type: Object
            },
            action_id: {
                type: Number,
                default: null
            },
            inTask: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapGetters({
                getOwner: 'members/getOwner',
            }),
        },
        methods: {
            handleToggleLike (comment) {
                if (this.isLoading) {
                    return
                }

                this.$api.comment.toggleLike(comment.id).then(res => {

                    let isMember;

                    this.likesMembers = this.likesMembers.filter(item => {
                        if (item.id !== this.getOwner.id) {
                            return true;
                        }

                        isMember = true;
                        return false
                    });

                    let data = {...this.comment};

                    data.action_id = this.action_id;

                    if (isMember) {
                        /*todo optimization*/
                        if (this.inTask) {
                            this.$store.dispatch('task/decrementLikes', data);
                        } else {
                            this.$store.dispatch('actions/decrementLikes', data);
                        }


                    } else {

                        /*todo optimization*/
                        if (this.inTask) {
                            this.$store.dispatch('task/incrementLikes', data);
                        } else {
                            this.$store.dispatch('actions/incrementLikes', data);
                        }

                        this.likesMembers.unshift(this.getOwner);
                    }

                }).catch(err => {
                    this.$notify({type:'error', text: this.$t('set_like_faild')});
                })
            },
            hideTitleMembers () {
                this.showTitleMembers = false;
            },
            getLikesMembers (comment) {



                this.showTitleMembers = true;

                if (this.isLoading || this.fetchLikesMembers) {
                    return
                }

                this.$api.comment.getLikesMembers(comment.id).then(res => {
                    this.likesMembers = res.members;
                    this.fetchLikesMembers = true;

                }).catch(err => {
                    this.$notify({type:'error', text: this.$t('can_not_get_members')});
                })
            },
        }
    }

</script>