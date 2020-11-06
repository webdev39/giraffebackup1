<template>
    <div class="subscribers">
        <div class="subscribers__list" v-if="getMembers">
            <theme-subscribers class="subscribers__list-item"
                 v-for="(item, index) in sliceMembers" :key="index"
                 :class="{'subscribers-active' : isSelectMembers(item), 'cursor-pointer' : options.isSelect  }"
                 @click.prevent.native="options.isSelect ? handleClick(item) : null"
                 :disabled="options.isSelect && isLoading"
                 :title="item.user | userFullName"
                 :style="{'background-image': 'url(' + item.user.avatar + ')', 'z-index': lengthSliceMembers + index}">
                <span v-if="!item.user.avatar">
                    {{ item.user | userInitials }}
                </span>
            </theme-subscribers>
            <theme-subscribers class="subscribers__list-item" :class="{'cursor-pointer': options.showOthers}" v-if="getShowCountSubscribers !== 'infinite' && lengthMembers > getShowCountSubscribers">
                <div @click="handleToggleShowSubscribers">
                    <span class="subscribers-title" :title="getOthersMembers" v-if="!showOthers">
                        +{{ lengthOtherMembers }}
                    </span>
                    <i class="subscribers-right-arrow" v-else>
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                 xlink:href="#icon-left-arrow">
                            </use>
                        </svg>
                    </i>
                </div>
            </theme-subscribers>
        </div>
    </div>
</template>

<script>
    import ThemeSubscribers from '@views/layouts/theme/ThemeSubscribers'
    import config           from '@config'

    export default {
        name: 'subscribers-view',
		components: {
			ThemeSubscribers
		},
        props: {
            subscribers: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            members: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            selectMembers: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            options: {
                type: Object,
                default: () => {
                    return {
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
        },
		data() {
			return {
				showOthers: false,
				windowWidth: window.innerWidth,
				offsetLeft: null,
			}
		},
        computed: {
            getShowCountSubscribers() {
                if (this.windowWidth < config.size.tablet && this.windowWidth > config.size.phone ) {
                    return this.options.count.tablet;
                }
                if (this.windowWidth < config.size.phone) {
                    if (this.offsetLeft < 100) {
                        return this.options.count.phoneNewRow;
                    }
                    return this.options.count.phone;
                }
                return this.options.count.desktop;
            },
            getMembers() {
                if (!this.members) {
                    return;
                }

                if (!this.subscribers || !this.subscribers.length) {
                    return;
                }

                let getMembers = [];

                this.members.map(member => {
                    this.subscribers.map(userTaskId => {
                        if (userTaskId === member.id && member.user.status) {
                            getMembers.push(member);
                        }
                    });
                });

                return getMembers;
            },
            sliceMembers() {
                if (this.getShowCountSubscribers === 'infinite' || this.showOthers) {
                    return this.getMembers;
                }

                return this.getMembers.slice(0, this.getShowCountSubscribers);
            },
            getOthersMembers() {
                if (this.lengthMembers > this.getShowCountSubscribers) {
                    let names = '';
                    let lengthGetMembers = this.lengthSliceMembers + this.getShowCountSubscribers - 1;
                    let i = this.getShowCountSubscribers;

                    for (i; i <= lengthGetMembers; i++) {
                        names += `${this.getMembers[i]['user']['name']} ${this.getMembers[i]['user']['last_name']}, `
                    }

                    return names.slice(0, -2);
                }
            },
            lengthMembers() {
                return this.getMembers.length;
            },
            lengthSliceMembers() {
                return this.lengthMembers - this.getShowCountSubscribers;
            },
            lengthOtherMembers() {
                if (this.lengthSliceMembers < 100) {
                    return this.lengthSliceMembers
                }

                return 99
            }
        },
        filters: {
            userInitials(user) {
                if(user.name && user.last_name){
                    return user.name[0] + user.last_name[0];
                }

                return '';
            },
            userFullName(user) {
                if(user.name && user.last_name){
                    return `${user.name} ${user.last_name}`;
                }

                return '';
            },
        },
        mounted() {
            this.offsetLeft = this.$el.offsetLeft;
        },
        methods: {
          test() {
            alert(1);
          },
            handleClick(member) {
                if (this.isLoading) {
                    return false;
                }

                this.$emit('click-member', member)
            },
            isSelectMembers(member) {
                if (!this.selectMembers.length) {
                    return false
                }

                return this.selectMembers.some(item => item === member.id)
            },
            handleToggleShowSubscribers() {
                if (this.options.showOthers) {
                    this.showOthers = !this.showOthers
                }
            }
        }
    }
</script>

<style lang="scss">
    .subscribers-right-arrow{
        width: 10px;
        height: 10px;
        display: flex;
        fill: #fff;
    }
</style>