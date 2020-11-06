<template>
    <div id="timer-list" class="dropdown-menu timer-list open" >
           <theme-modal-header class="timer-list-header modal-header">
            <slot class="timer-list-title modal-title">{{ $t("title_timer_list") }}</slot>
            <theme-button-close class="btn-close-header">
                <i class="icon-close" @click="$emit('hide')">
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                        xlink:href="#icon-close">
                        </use>
                    </svg>
                </i>
            </theme-button-close>
        </theme-modal-header>
        <span class="empty-timer-list" v-if="!isTimerList">{{ $t("empty_timer_list") }}</span>
        <template v-for="timer in timerList">
            <timer-list-item v-if="isTimerList" :timer="timer" />
        </template>
    </div> 
</template>

<script>
    import {mapGetters}         from 'vuex'

    import TimerCommentDropdown from "@views/components/timer/TimerCommentDropdown";
    import TimerListItem        from "@views/components/timer/TimerListItem";
    import ThemeModalHeader         from '@views/layouts/theme/ThemeModalHeader'
    import ThemeButtonClose         from "@views/layouts/theme/buttons/ThemeButtonClose";

    export default {
        name: "timer-list",
        data() {
            return {
                limit: null,
            }
        },
        computed: {
            ...mapGetters({
                groups: 'groups/getStateGroups',
                timers: 'timers/getTimers',
            }),
            timerList() {
                return this.timers.filter(item => item.status !== "stopped" && item.status !== "started");
            },
            isTimerList () {
                // return this.timerList.length ? true : this.$notify({type:'info', text: this.$t('you_dont_have_paused_time')});
                 return this.timerList.length ? true : false;
            }
        },
        components: {
            TimerListItem,
            TimerCommentDropdown,
            ThemeModalHeader,
            ThemeButtonClose
        },
    }
</script>

<style lang="scss">
    #timer-list {
        display: block;
        padding: 0;
        .timer-list-item {
            padding: 0 10px;
        }
        .timer-list-item:last-child {
            border: none;
        }
        .empty-timer-list {
            font-size: 16px;
            text-align: center;
            display: block;
            color: #b2b2b2;
            padding: 20px 0;
        }
            .timer-list-header {
                font-weight: 700;
                font-size: 20px;
                overflow: hidden;
                text-overflow: ellipsis;
                position: relative;
            }
    }
</style>
