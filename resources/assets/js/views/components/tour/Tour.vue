<template>

    <div
        :class="['tour', { 'tour-start' : showWelcome }]"
        @click.self="showWelcome ? $emit('hideTour') : null"
    >

        <v-tour
            v-if="tours[currentTour]"
            :name="currentTour"
            :steps="tourSteps[currentTour]"
            :options="tourOptions"
            :callbacks="tourCallbacks"
        >
            <template slot-scope="tour">
                <transition name="fade">
                    <v-step
                        v-if="tour.currentStep === index"
                        v-for="(step, index) of tour.steps"
                        :key="index"
                        :step="step"
                        :previous-step="tour.previousStep"
                        :next-step="tour.nextStep"
                        :stop="tour.stop"
                        :is-first="tour.isFirst"
                        :is-last="tour.isLast"
                        :labels="tour.labels"
                    >
                        <template v-if="tour.currentStep === 0">
                            <div slot="actions">
                                <button @click="handlerTourStop(tour.stop)" class="btn btn-primary">{{ $t("tour_finish") }}</button>
                                <button @click="handlerTourNext(tour.currentStep, tour.nextStep)" class="btn btn-primary">{{ $t("tour_next") }}</button>
                            </div>
                        </template>
                        <template v-if="tour.currentStep > 0 && tour.currentStep < (tourSteps[currentTour].length - 1)">
                            <div slot="actions">
                                <button @click="handlerTourStop(tour.stop)" class="btn btn-primary">{{ $t("tour_finish") }}</button>
<!--                                <button @click="tour.previousStep" class="btn btn-primary">Previous step</button>-->
                                <button @click="handlerTourNext(tour.currentStep, tour.nextStep)" class="btn btn-primary">{{ $t("tour_next") }}</button>
                            </div>
                        </template>
                        <template v-if="tour.currentStep === (tourSteps[currentTour].length - 1)">
                            <div slot="actions">
                                <button @click="handlerTourStop(tour.stop)" class="btn btn-primary">{{ $t("tour_finish") }}</button>
<!--                                <button @click="tour.previousStep" class="btn btn-primary">Previous step</button>-->
                            </div>
                        </template>
                    </v-step>
                </transition>
            </template>
        </v-tour>

        <transition name="fade">

            <div
                v-if="showWelcome"
                class="tour-intro"
            >
                <div class="tour-intro-text">
                    <p class="tour-intro-text-welcome">{{ $t('tour_welcome.welcome') }}</p>
                    <p class="tour-intro-text-make">{{ $t('tour_welcome.make') }}</p>
                    <p class="tour-intro-text-start">{{ $t('tour_welcome.start') }}</p>
                </div>
                <div class="tour-intro-block">
                    <div class="tour-intro-buttons">
                        <button
                            v-if="checkPermission('create-group') && checkPermission('create-task')"
                            type="button"
                            class="btn btn-md"
                            @click="handlerTour('task')"
                        >{{ $t("tour_button_start_task") }}</button>
                        <button
                            v-if="checkPermission('time-tracking')"
                            type="button"
                            class="btn btn-md"
                            @click="handlerTour('time')"
                        >{{ $t("tour_button_start_time") }}</button>
                        <button
                            v-if="checkPermission('create-group')"
                            type="button"
                            class="btn btn-md"
                            @click="handlerTour('group')"
                        >{{ $t("tour_button_start_group") }}</button>
                    </div>
                    <button type="button" class="btn btn-md tour-stop" @click="tourStop">
                        {{ $t("tour_button_start_exit") }}
                    </button>
                </div>

            </div>

        </transition>

    </div>

</template>

<script>
	import { mapGetters } from 'vuex';
	import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";
	import ThemeButtonWarning from "@views/layouts/theme/buttons/ThemeButtonWarning";

	export default {
		name: "my-tour",
        components: {
			ThemeButtonSuccess,
            ThemeButtonWarning
        },
		props: {
			showTour: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				currentTour: '',

				tours: {
					task: false,
					time: false,
					group: false
				},

				tourSteps: {
					task: [
						{
							target: '[data-v-step="task_0"]',
							content: this.$t('tour_task.new'),
							params: {
								tourId: 0,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="task_1"]',
							content: this.$t('tour_task.deadline'),
							params: {
								tourId: 1,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="task_2"]',
							content: this.$t('tour_task.roles'),
							params: {
								tourId: 2,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="task_3"]',
							content: this.$t('tour_task.priority'),
							params: {
								tourId: 3,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="task_4"]',
							content: this.$t('tour_task.time'),
							params: {
								tourId: 4,
								placement: 'right'
							}
						}
					],
                    time: [
						{
							target: '[data-v-step="time_0"]',
							content: this.$t('tour_time.start'),
							params: {
								tourId: 0,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="time_1"]',
							content: this.$t('tour_time.tracked'),
							params: {
								tourId: 1,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="time_2"]',
							content: this.$t('tour_time.comment'),
							params: {
								tourId: 2,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="time_3"]',
							content: this.$t('tour_time.pause'),
							params: {
								tourId: 3,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="time_4"]',
							content: this.$t('tour_time.currently'),
							params: {
								tourId: 4,
								placement: 'right'
							}
						}, {
							target: '[data-v-step="time_5"]',
							content: this.$t('tour_time.completed'),
							params: {
								tourId: 5,
								placement: 'right'
							}
						}
					],
                    group: [
						{
							target: '[data-v-step="group_0"]',
							content: this.$t('tour_group.create'),
							params: {
								tourId: 0,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_1"]',
							content: this.$t('tour_group.settings'),
							params: {
								tourId: 1,
								placement: 'left'
							}
						}, {
							target: '[data-v-step="group_2"]',
							content: this.$t('tour_group.name_group'),
							params: {
								tourId: 2,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_3"]',
							content: this.$t('tour_group.team'),
							params: {
								tourId: 3,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_4"]',
							content: this.$t('tour_group.new'),
							params: {
								tourId: 4,
								placement: 'top'
							}
						}, {
							target: '[data-v-step="group_5"]',
							content: this.$t('tour_group.board_name'),
							params: {
								tourId: 5,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_6"]',
							content: this.$t('tour_group.board_deadline'),
							params: {
								tourId: 6,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_7"]',
							content: this.$t('tour_group.board_budget'),
							params: {
								tourId: 7,
								placement: 'bottom'
							}
						}, {
							target: '[data-v-step="group_8"]',
							content: this.$t('tour_group.board_save'),
							params: {
								tourId: 8,
								placement: 'top'
							}
						}
					]
                },

				tourOptions: {
					useKeyboardNavigation: false,
					labels: {
						buttonSkip: 'Skip tour',
						buttonPrevious: 'Previous step',
						buttonNext: 'Next step',
						buttonStop: 'Finish'
					}
				},
				tourCallbacks: {
					onStart: this.tourStart,
				},

				showWelcome: true,

                testData: {
					group: {},
                    board: {},
                    task: {},
                },

                forms: {
					group: {
                        name: "Group for tour",
                        description: "Group for tour",
                        group_id: null,
                        members: [],
                    },
                    board: {
						board_id: null,
						budget_id: null,
						budget_type_id: 2,
						deadline: null,
						description: "",
						group_id: null,
						hard_budget: "00:00",
						hide_done_tasks: false,
						is_archive: false,
						name: "Board for tour",
						priority_id: null,
						soft_budget: "00:00",
						view_type_id: null,
                    },
                    task: {
						name: "Task for tour",
						board_id: null,
						created_at: "2019-12-09 10:54:59",
						draft_task_id: null,
						is_draft: 0,
						subscribers: {
							notify: [],
							task: []
                        }
                    }
                }
			}
		},
		computed: {
			...mapGetters({
				getUserProfile: 'user/getUserProfile',
				pagePreloader: 'getPagePreloader',
			}),
		},
		methods: {
			tourStart() {
				this.$store.dispatch('setTourStep', `${this.currentTour}_0`);
            },

			tourStop() {
				this.$emit('hideTour');
				this.$store.dispatch('setTourStep', null);
				this.updateProfile().then((data) => {
                    if (this.$route.name !== 'deadline') {
                        this.$router.push({ name: 'deadline', params: { period: 'day' }});
                    }
                });
			},

			updateProfile() {
				if (! this.getUserProfile.tour) {
					return this.$api.user.updateProfile({ tour: true });
                }
			},

			handlerTourNext(currentStep, callback) {
				this.$store.dispatch('setTourStep', `${this.currentTour}_${currentStep + 1}`);

                if (currentStep === 0) {
                    if (this.currentTour === 'task') {
                		if(!this.$route.query.hasOwnProperty('taskId')) {
					        this.$router.replace({ query: { taskId: this.testData.task.id } });
                        }

					    return setTimeout(_ => {
					    	if (callback) callback();
                        }, 1000)
                    }

                    if (this.currentTour === 'time') {
						this.$root.$emit('tour-start-timer');

						return setTimeout(_ => {
							if (callback) callback();
						}, 1200)
					}
				}

                if (currentStep === 1) {
					if (this.currentTour === 'group') {
						 this.$modal.show('group-setting-modal');

                         return setTimeout(_ => {
                            if (callback) callback();
                         }, 600);
					}
                }

                if (currentStep === 4) {
					if (this.currentTour === 'group') {
						this.$modal.hide('group-setting-modal');
						this.$modal.show('board-setting-modal');

						return setTimeout(_ => {
							if (callback) callback();
						}, 800);
					}
                }

				return setTimeout(_ => {
					if (callback) callback();
				}, 600)
            },

            handlerTourStop(callback) {
				if (['task', 'group'].includes(this.currentTour)) {
					if (this.currentTour === 'task') {
						this.$api.task.removeTask(this.testData.task)
							.then(_ => {
								this.$api.group.removeGroup(this.testData.group.id);
								this.$router.replace({query: ''})
							});
					}
                }

				if (this.currentTour === 'time') {
					this.$root.$emit('tour-stop-timer');
				}

				this.$modal.hide('group-setting-modal');
				this.$modal.hide('board-setting-modal');

				this.updateProfile();
				this.tours[this.currentTour] = false;
				this.currentTour = null;
				this.$store.dispatch('setTourStep', null);
				this.showWelcome = true;

				if (callback) {
					callback();
				}
            },

			handlerTour(name) {
                this.currentTour = name;
				this.testData[this.currentTour] = {};
                if (this.$route.name !== 'deadline') {
                    this.$router.push({ name: 'deadline', params: { period: 'day' }});
                }
                switch (this.currentTour) {
                    case 'task': this.runTourTask(); break;
                    case 'time': this.runTourTime(); break;
                    case 'group': this.runTourGroup(); break;
                }
            },

			runTourTask() {
                let generate = this.createTestData();

                if (generate) {
                	generate.then(data => {
						this.tours[this.currentTour] = true;
						this.showWelcome = false;

						setTimeout(_ => {
							this.$tours[this.currentTour].start()
						}, 600);
                    });
                }
            },

			runTourTime() {
				this.tours[this.currentTour] = true;
				this.showWelcome = false;

				setTimeout(_ => {
					this.$tours[this.currentTour].start()
				}, 600);
            },

			runTourGroup() {
                this.tours[this.currentTour] = true;
                this.showWelcome = false;

                setTimeout(_ => {
                    this.$tours[this.currentTour].start()
                }, 600);
            },

            createTestData() {
				if (Object.keys(this.testData[this.currentTour]).length) {
				    return null;
                }

				return this.$api.group.createGroup(this.forms.group)
					.then(group => {
						this.testData.group = group.group;
						this.forms.board.group_id = group.group.id;

						this.$api.board.createBoard(this.forms.board, 'board_res=long')
							.then(board => {
								this.testData.board = board.board;
								this.forms.task.board_id = board.board.id;

								if (this.currentTour === 'task') {
								    this.$api.task.createTask(this.forms.task)
									    .then(task => {
										    this.testData.task = task.task;
									    });
                                }

							});
					});
            }
		},
	}
</script>
