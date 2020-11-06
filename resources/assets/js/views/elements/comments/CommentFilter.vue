<template>
    <div class="comments-filter" v-click-outside="handleHideCommentFilters">
        <div class="comments-filter-controls">
            <button
                :class="['comments-filter-controls__button', {active: !isActive}]"
                @click="toggleSeen"
            >{{ button.text }}</button>

            <i class="icon-settings__filter" @click="handleToggleCommentFilters">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-settings"></use>
                </svg>
            </i>
        </div>
        <div class="comments-filter-list" v-if="blockCommentFilters" style="position: absolute; right: 0;" >
            <content-loading
                :absolute="styleLoading.absolute"
                :autosize="styleLoading.autosize"
                :loading="fetching"
            />
            <div class="comments-filter-list-item" v-if="!short && !isCommunicationGroup">
                <div class="comments-filter-board-view-type-select__content">
                    <div class="comments-filter-list-item__text">
                        {{ $t('view')}}:
                    </div>
                    <board-view-type-select class="comments-filter-board-view-type-select"/>
                </div>

            </div>
            <div class="comments-filter-list-item" >
                <label>
                    <input
                       v-model="selectAll"
                       :id="commentFilterAll.name"
                       type="checkbox"
                    />
                    {{ commentFilterAll.alias }}
                </label>
            </div>
            <div
                v-for="(filter, index) in getCommentFilters"
                :key="index"
                class="comments-filter-list-item"
            >
                <label>
                    <input
                        v-model="selectedFilters"
                        :value="filter"
                        type="checkbox"
                    />
                    {{ filter.alias }}
                </label>
            </div>

            <div
                v-if="short"
                class="comments-filter-list-item comments-filter-list-item_indent-top"
            >
                <label>
                    <input
                        v-model="selectedAllUsers"
                        type="checkbox"
                    />
                    {{ $t('for_all_users') }}
                </label>
            </div>

            <div
                class="comments-filter-list-item"
                v-for="subs in subscribers"
                :key="subs.id"
                v-if="!selectedAllUsers && short"
            >
                <label>
                    <input
                        v-model="selectedSubscribers"
                        :value="subs.id"
                        type="checkbox"
                    />
                    {{ subs.user.name }}
                </label>
            </div>

            <div v-if="!short" class="comments-filter-list-item comments-filter-list-item_time-range">
                <span class="comments-filter-list-item-text-range">{{ $t('time_range') }}</span>
                <vue-datepicker-local
                        v-model="range"
                        :placeholder="$t('all')"
                        :local="local"
                />
            </div>
        </div>
    </div>
</template>

<script>
	import {mapGetters} from 'vuex'
	import clickOutside from 'v-click-outside'
	import VueDatepickerLocal from 'vue-datepicker-local'

	import ContentLoading from '@views/components/ContentLoading'
	import boardViewTypeSelect from '@views/partcials/BoardViewTypeSelect/BoardViewTypeSelect'

    export default {
        components: {
            ContentLoading,
            VueDatepickerLocal,
            boardViewTypeSelect
        },
        props: {
            fetching: {
                type: Boolean,
                default: false
            },
            short: {
                type: Boolean,
                default: false
            },
            subscribers: {
                type: Array,
                required: false,
                default() {
                    return []
                }
            },
        },
        data() {
            return {
                blockCommentFilters: false,
                selectedFilters: this.getDefaultFilter(),
                selectedAllUsers: true,
                // other:                  false,
                // subscribed:             [],
                isActive: true,
                commentFilterAll: { name: "all", alias: this.$t("all") },
                commentFilters: this.getDefaultFilter(),
                styleLoading: {
                    'absolute': true,
                    'autosize': true,
                },
                selectedSubscribers: [],
                local: {
                    dow:                0, // Sunday is the first day of the week
                    hourTip:            'Select Hour', // tip of select hour
                    minuteTip:          'Select Minute', // tip of select minute
                    secondTip:          'Select Second', // tip of select second
                    yearSuffix:         '', // suffix of head year
                    monthsHead:         'January_February_March_April_May_June_July_August_September_October_November_December'.split('_'), // months of head
                    months:             'Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec'.split('_'), // months of panel
                    weeks:              'Su_Mo_Tu_We_Th_Fr_Sa'.split('_'), // weeks,
                    cancelTip:          'cancel',
                    submitTip:          'confirm'
                },
                range:                  [],
                button: {
                    text: this.$t('hide_details'),
                },
            }
        },
        computed: {
            ...mapGetters({
                getOwner:   'members/getOwner',
            }),
            isCommunicationGroup() {
                return this.$route.name === 'communication'
            },
            getCommentFilters() {
                if (this.checkPermission('time-tracking')) {
                    return this.commentFilters
                }

                return this.commentFilters.filter(item => item.name !== 'timer_logs')
            },
            selectAll: {
                get() {
                    if (this.selectedFilters.length === this.commentFilters.length) {
                        return true;
                    }

                    return false;
                },
                set(value) {
                    if (value) {
                        this.selectedAll();
                    } else {
						this.$emit('clearAction');
                        this.selectedFilters = [];
                    }
                }
            },
        },
        watch: {
            selectedSubscribers(newVal) {
                this.$emit('filter', newVal)
            },
            selectedFilters(filters) {
				if (this.selectedFilters.length > 0) {
					this.prepareForCommentFilter(filters);
				}
            },
            range(range) {
                range[0] = this.$moment(range[0]).format('YYYY-MM-DD HH:mm:ss');
                range[1] = this.$moment(range[1]).format('YYYY-MM-DD HH:mm:ss');

                this.$emit('update', {range: range});
            },
        },
        directives: {
            'clickOutside': clickOutside.directive
        },
        mounted() {
            this.prepareForCommentFilter(this.getDefaultFilter());
        },
        methods: {
            getDefaultFilter() {
                return [
                    { name: 'comments',                        alias: this.$t('title_comments') },
                    { name: 'timer_logs',                      alias: this.$t('timer_entries') },
                    { name: 'changed planned_deadline',        alias: this.$t('todo') },
                    { name: 'changed deadline',                alias: this.$t('title_deadline') },
                    { name: 'files',                           alias: this.$t('files') },
                    { name: 'assigned',                        alias: this.$t('assigned') },
                    { name: 'unassigned',                      alias: this.$t('unassigned') },
                    { name: 'opened',                          alias: this.$t('opened') },
                    { name: 'closed',                          alias: this.$t('closed') },
                    { name: 'archived',                        alias: this.$t('archived') },
                    { name: 'unarchived',                      alias: this.$t('unarchived') },
                    { name: 'changed name',                    alias: this.$t('task_renamed') },
                    { name: 'changed soft_budget hard_budget', alias: this.$t('budget_changed') },
                    { name: 'increased',                       alias: this.$t('increased_sorting') },
                    { name: 'decreased',                       alias: this.$t('decreased_sorting') },
                    { name: 'deleted',                         alias: this.$t('delete') },
                    { name: 'created',                         alias: this.$t('created') },
                    { name: 'subscribed',                      alias: this.$t('subscribed') },
                    { name: 'unsubscribed',                    alias: this.$t('unsubscribed') },
                ]
            },
            prepareForCommentFilter(filters = []) {
                let commentFilters = {
                    names: [],
                    columns: []
                };

                let countChanged = 0;

				if (! this.selectAll) {
                    filters.map(filter => {

                        if (filter.name.indexOf('changed') !== -1) {
                            let column  = filter.name.split(' ');
                            let name    = column.splice(0,1);

                            if (countChanged === 0) {
                                countChanged = 1;
                                commentFilters.names.push(...name);
                            }

                            commentFilters.columns = [...commentFilters.columns,...column];
                        } else {
                            commentFilters.names.push(filter.name);
                        }
                    });
				}

                this.$emit('update', { filters: commentFilters });
            },
            selectedAll () {
                this.selectedFilters = this.commentFilters
            },
            handleToggleCommentFilters () {
                this.blockCommentFilters = !this.blockCommentFilters;
            },
            handleHideCommentFilters () {
                this.blockCommentFilters = false;
            },
            toggleSeen() {
				if (this.isActive) {
					this.selectAll = false;
					this.selectedFilters = [this.getCommentFilters.find(item => item.name === 'comments')];
				} else {
					this.selectAll = true;
					this.selectedFilters = this.getDefaultFilter();
				}

				this.blockCommentFilters = false;
                this.isActive = !this.isActive;
                this.button.text = ! this.isActive ? this.$t('show_details') : this.$t('hide_details');
            },
        },
    }
</script>

<style lang="scss" scoped>
    .comments-filter-list{
        width: 300px;
        background-color: #fafafa;
        padding: 20px;
        background-color: #fff;
        -webkit-box-shadow: 0px 2px 4px 0 rgba(0, 0, 0, 0.4);
        box-shadow: 0px 2px 4px 0 rgba(0, 0, 0, 0.4); 
        border-radius: 5px;
    }
    .comments-filter-list-item{
        display: block;
        &:last-child {
            label{
                margin-bottom: 0;
            }
        }
        label {
                 input {
                    cursor:pointer;
                }
        }
    }
    .comments-filter{
        position: absolute;
        right: 15px;
        top: 0;
        z-index: 1;
    }
    .comments-filter-controls{
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .comments-filter-controls__button{
        border: none;
        background: none;
        color: #376aa7;
        padding: 2px 10px;
        border: 1px solid #376aa7;
        border-radius: 4px;
        &:hover{
            text-decoration: none;
            color: inherit;
        }
        &.active{
            color: inherit;
            border-color: inherit;
                &:hover{
                    text-decoration: none;
                    color: #376aa7;
                }
        }
    }
    .icon-settings__filter{
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #b3b3b2;
        margin-left: 10px;
        svg {
            width: 18px;
            height: 18px;
            fill: #a2a2a2;
        }
    }
    .comments-filter-board-view-type-select__content{
        display: flex;
        margin-bottom: 10px;
    }
    .comments-filter-board-view-type-select{
        margin-left: 15px;
    }
</style>
