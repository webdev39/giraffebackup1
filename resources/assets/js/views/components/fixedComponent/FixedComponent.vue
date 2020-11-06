<template>
    <div
        ref="container"
        v-resize="updateFixedContent"
        class="fixed-component"
    >
        <div
            class="fixed-component__content"
            ref="content"
            :style="contentStyle"
        >
            <slot></slot>
        </div>
        <div class="fixed-component__placeholder" :style="placeholderStyle"></div>
    </div>
</template>

<script>
    import resize       from 'vue-resize-directive'
    import { mapGetters } from 'vuex'

    export default {
        data() {
            return {
                contentStyle: {
                    top: '81px'
                },
                placeholderStyle: {},
            }
        },
        props: {
            isScroll: {
                type: Boolean,
                required: false,
                default: false,
            },
            fixed: {
                type: Boolean,
                default: false,
            },
        },
        directives: {
            resize,
        },
        methods: {
            updateFixedContent() {
                let leftOffset = 0;
                const { content, container } = this.$refs;

                if (!content) {
                  return
                }

                const { height } = content.getBoundingClientRect();
                // const placeholderHeight = height - 14;
                

                const { width, left, top } = container.getBoundingClientRect();

                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                const position = isMobile && !this.fixed ? 'static' : 'fixed';
                const display = isMobile && !this.fixed ? 'none' : 'block';

                if (this.isScroll) {
                    const mainContainer = document.querySelector('.content.content_show-bg').getBoundingClientRect()
                    leftOffset = mainContainer.left + 10;
                } else {
                    leftOffset = left;
                }

                this.contentStyle =  {
                    width: `${width}px`,
                    left:  `${leftOffset}px`,
                    position,
                }

                if(top > 0) {
                    this.contentStyle.top = `${top}px`;
                }

                this.placeholderStyle = {
                    height: `${height}px`,
                    display
                }
            }
        },
        computed: {
            ...mapGetters( {
                userView:   "user/getUserViewTypes",
                currentTimer: 'timers/getCurrentTimer',
            }),
            getUserViewId(){
            	if (this.userView.length) {
                    return this.userView[0].view_type_id
                }
            }
        },
        watch: {
            getUserViewId(newVal) {
                this.updateFixedContent();
            },
            currentTimer() {
                this.updateFixedContent();
            }
        },
        mounted() {
            this.$nextTick(() => {
				setTimeout(() => {
					this.updateFixedContent();
				}, 300);
                setTimeout(() => {
                    this.updateFixedContent();
                }, 600);
                setTimeout(() => {
                    this.updateFixedContent();
                }, 900);
                setTimeout(() => {
                    this.updateFixedContent();
                }, 1200);
            });
        },
    }
</script>

<style>
    .fixed-component{
        position: relative;
        z-index: 2;
    }
         @media (max-width: 768px)  {
            .fixed-component {
                width: 100%;
            }
            .fixed-component .group-board-info-wrapper {
                margin-left: 0px;
                margin-right:0px;
                padding: 0;
            }
            .fixed-component .row.task-create {
                margin-left:  0px;
                margin-right: 0px;
            }
             .filter-page  .fixed-component .row.task-create {
                margin-left: 0px;
                margin-right: 0px;
            }

/*            .Kanban .fixed-component .group-board-info-wrapper {
                margin-left: -5px;
                margin-right: 5px;
                padding: 0;
            }
            .Kanban .fixed-component .row.task-create {
                margin-left:  -5px;
                margin-right: 5px;
            }*/

         }

</style>
<style lang="scss">
@media (max-width: 768px)  {
    .Kanban, .List, .Gantt {
         .fixed-component .group-board-info-wrapper {
                    // margin-left: -5px;
                    // margin-right: 5px;
                    padding: 0;
         }

        .fixed-component .row.task-create {
                    // margin-left:  -5px;
                    // margin-right: 5px;
         }
    }

    .Calendar {
        overflow-x:hidden;
        .group-board-info-wrapper {
                    margin-left:  0px;
                    margin-right: 0px;
                    padding: 0;
         }

        .row.task-create {
                    margin-left:  0px;
                    margin-right: 0px;
         }
    }

}


//  .List, .Gantt  {
//     .fixed-component {
//         margin-left:5px;
//     }
// }

 .List, .deadline-page, .filter-page  {
    .fixed-component {
        margin-bottom: 11px;
    }
}

@media (min-width: 550px) {
     .List, .deadline-page, .filter-page  {
    .fixed-component {
        margin-bottom: 10px;
    }
}
}

.filter-page {
    .fixed-component {
        margin-left:0;
                    .row.task-create {
                    margin-left:  0px;
                    margin-right: 0px;
         }
    }
}

 </style>
