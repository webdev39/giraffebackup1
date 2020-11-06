<template>
    <div class="zoom-wrapper">
        <div v-dragscroll:nochilddrag
             class="zoom" ref="zoom" @mousedown="handleDown" @mouseup="handleUp" @mousemove="handleMove" @mouseleave="handleMouseleave" >
            <div @click.prevent="addComment" class="zoom-add-comment" v-if="zoom.isAddComment">
                <div class="zoom-add-comment-text">{{ $t('click_on_image_for_add_comment') }}</div>
            </div>
            <template v-if="image.id">
                <div class="zoom-comment" v-for="comment in getComments" :style="{'left': comment.spatial.left, 'top': comment.spatial.top}">
                    <button class="zoom-comment-point" @click="showCommentModal(comment)"></button>
                    <div class="zoom-comment-content" :style="setPositionCommentContent(comment)">
                        <div class="zoom-comment-content-inner">
                            {{ comment.body }}
                        </div>
                    </div>
                </div>
            </template>

            <img
                data-dragscroll
                :src="zoom.img.src" class="preview" :style="{
                'width':                getBackgroundSize,
                'background-position':  getBackgroundPosition,
                'background-size':      'contain',
                'background-image':     'url(' + image.path + ')'
                }" >

        </div>
        <div v-if="image.id" class="clearfix zoom-controllers">
            <button class="btn pull-right"
                    :class="{'btn-success': !zoom.isAddComment, 'btn-danger': zoom.isAddComment}"
                    @click="zoom.isAddComment = !zoom.isAddComment">
                {{ zoom.isAddComment ? $t('disable') : $t('enable_add_comment') }}
            </button>
        </div>
    </div>

</template>

<script>
    export default {
        name: 'Zoom',
        props: {
            image:      Object,
            task_id:    Number,
        },
        data() {
            return {
                zoom: {
                    isStart:            0,
                    isAddComment:       false,
                    container:          null,
                    img:            {
                        container:      null,
                        src:            this.image.path,
                    },
                    setting: {
                        factor: 0.02,
                        max_scale: 6,
                        min_scale: 1,
                    },
                    scale:      1,
                    bg_pos_x:   0,
                    bg_pos_y:   0,
                    bg_width:   0,
                    bg_height:  0,
                    previousEvent: {
                        pageX:  0,
                        pageY:  0
                    },
                    width:      0,
                    height:     0

                },
                comments: []
            }
        },
        computed: {
            getImageWidth() {
                if (this.zoom.width) {
                    return `${this.zoom.width}px`
                }
            },
            getImageHeight() {
                if (this.zoom.height) {
                    return `${this.zoom.height}px`
                }
            },
            getBackgroundPosition() {
                return `center center`
            },
            getBackgroundSize() {
                return `${this.zoom.bg_width}px`
            },
            getComments: {
              get: function() {
                let newComment = [];

                this.comments.forEach(item => {
                  item.spatial.left = item.spatial.x * this.zoom.scale + this.zoom.bg_pos_x + 'px';
                  item.spatial.top = item.spatial.y * this.zoom.scale + this.zoom.bg_pos_y + 'px';

                  newComment.push(item);
                });

                return newComment;
              }
            }
        },
        mounted: function() {
            this.$nextTick(() => {
                this.zoom.img.container = this.$el.getElementsByTagName('img')[0];
                this.zoom.img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';

				if (this.image.id) {
					this.$api.comment.getAttachmentComment(this.image.id).then(res => {
						this.comments = res.comments;
					});
				}

                this.addEventWhell(this.handleWheel);

                setTimeout(() => {
                    this.zoom.container = this.$refs.zoom.getBoundingClientRect();
                    this.zoom.width = this.zoom.bg_width = parseInt(window.getComputedStyle(this.zoom.img.container, null).width, 10);
                    this.zoom.height = this.zoom.bg_height = parseInt(window.getComputedStyle(this.zoom.img.container, null).height, 10);
                }, 100);
            });
        },
        methods: {
            setPositionCommentContent(comment) {
                if (this.zoom.bg_width / 2 < parseInt(comment.spatial.left)) {
                     return {right: '28px', 'justify-content': 'flex-end'}
                }

                return {left: '28px'}
            },
            updateBgStyle() {
                if (this.zoom.bg_pos_x > 0) {
                    this.zoom.bg_pos_x = 0;
                } else if (this.zoom.bg_pos_x < this.zoom.container.width - this.zoom.bg_width) {
                    this.zoom.bg_pos_x = this.zoom.container.width - this.zoom.bg_width;
                }

                if (this.zoom.bg_pos_y > 0) {
                    this.zoom.bg_pos_y = 0;
                } else if (this.zoom.bg_pos_y < this.zoom.container.height - this.zoom.bg_height) {
                    this.zoom.bg_pos_y = this.zoom.container.height - this.zoom.bg_height;
                }
            },
            reset() {
                this.zoom.bg_width = this.zoom.container.width;
                this.zoom.bg_height = this.zoom.container.height;
                this.zoom.bg_pos_x = this.zoom.bg_pos_y = 0;
            },
            handleWheel(e) {

                /*zoom-in/zoom-out*/
                e.preventDefault();

                let delta = e.deltaY || e.detail || e.wheelDelta;

                if (delta === undefined) {
                    delta = e.originalEvent.detail;
                }

                delta = Math.max(-1, Math.min(1,delta));

                /**/
                let newScale = this.zoom.scale - delta * this.zoom.setting.factor * this.zoom.scale;

                if (newScale > this.zoom.setting.max_scale) {
                    this.zoom.scale = this.zoom.setting.max_scale
                } else if (newScale < this.zoom.setting.min_scale) {
                    this.zoom.scale = this.zoom.setting.min_scale
                } else {
                    this.zoom.scale = newScale;
                }

                let rect = this.zoom.img.container.getBoundingClientRect();
                let offset_x = e.pageX - rect.left - window.pageXOffset;
                let offset_y = e.pageY - rect.top - window.pageYOffset;

                let bg_cursor_x = offset_x - this.zoom.bg_pos_x;
                let bg_cursor_y = offset_y - this.zoom.bg_pos_y;

                let bg_ratio_x = bg_cursor_x / this.zoom.bg_width;
                let bg_ratio_y = bg_cursor_y / this.zoom.bg_height;

                if (delta < 0) {
                    this.zoom.bg_width += this.zoom.bg_width * this.zoom.setting.factor;
                    this.zoom.bg_height += this.zoom.bg_height * this.zoom.setting.factor;
                } else {
                    this.zoom.bg_width -= this.zoom.bg_width * this.zoom.setting.factor;
                    this.zoom.bg_height -= this.zoom.bg_height * this.zoom.setting.factor;
                }

                if (this.zoom.setting.max_scale) {
                    this.zoom.bg_width = Math.min(this.zoom.container.width * this.zoom.setting.max_scale, this.zoom.bg_width);
                    this.zoom.bg_height = Math.min(this.zoom.container.height * this.zoom.setting.max_scale, this.zoom.bg_height);
                }

                this.zoom.bg_pos_x = offset_x - (this.zoom.bg_width * bg_ratio_x);
                this.zoom.bg_pos_y = offset_y - (this.zoom.bg_height * bg_ratio_y);

                if (this.zoom.bg_width <= this.zoom.container.width || this.zoom.bg_height <= this.zoom.container.height) {
                    this.reset();
                } else {
                    this.updateBgStyle();
                }
            },
            handleMove(e) {
                if (this.zoom.isStart === 1) {
                    this.zoom.bg_pos_x += (e.pageX - this.zoom.previousEvent.pageX);
                    this.zoom.bg_pos_y += (e.pageY - this.zoom.previousEvent.pageY);
                    this.zoom.previousEvent = e;
                    this.updateBgStyle();
                }
            },

            /**/

            addComment (e) {

                let rect    = this.zoom.img.container.getBoundingClientRect();
                let offset_x = e.pageX - rect.left - window.pageXOffset - this.zoom.bg_pos_x;
                let offset_y = e.pageY - rect.top - window.pageYOffset - this.zoom.bg_pos_y;

                let data = {
                    attachmentId:   this.image.id,
                    taskId:         this.task_id,
                    body:           '',
                    mentions:       [],
                    spatial: {
                        x:          Math.trunc(offset_x / this.zoom.scale),
                        y:          Math.trunc(offset_y / this.zoom.scale),
                        w:          0,
                        h:          0,
                    }
                };

                this.$modal.show('image-comment-modal', {
                    comment: data,
                    create: true,
                    createCallback: (res) => {
                        this.comments.push(res.comment)
                    },
                });
            },
            showCommentModal(comment) {
                comment.taskId          = this.task_id;
                comment.attachmentId    = this.image.id;

                this.$modal.show('image-comment-modal', {
                    comment: comment,
                    edit: true,
                    updateCallback: (res) => {
                        this.comments.some(item => {
							if (item.id === res.comment.id) {
								return {...item,...res.comment}
							}
						});
                    },
                    removeCallback: (res) => {
                        this.comments = this.comments.filter(item => item.id !== comment.id)
                    },
                });
            },
            addEventWhell (callback){
                if (this.$refs.zoom.addEventListener) {
                    if ('onwheel' in document) {
                        this.$refs.zoom.addEventListener("wheel", callback);
                    } else if ('onmousewheel' in document) {
                        this.$refs.zoom.addEventListener("mousewheel", callback);
                    } else {
                        this.$refs.zoom.addEventListener("MozMousePixelScroll", callback);
                    }
                } else {
                    this.$refs.zoom.attachEvent("onmousewheel", callback);
                }
            },
            handleDown(e) {
                this.zoom.isStart = 1;
                this.zoom.previousEvent = e;
            },
            handleUp() {
                this.zoom.isStart = 0;
            },
            handleMouseleave() {
                this.zoom.isStart = 0;
            },
        }
    }
</script>

<style lang="scss">
    .zoom{
        width: 100%;
        height: 65vh;
        margin: 0 auto;
        overflow: hidden;
        position: relative;
    }
    .zoom img{
        //position: relative;
        cursor: move;
        left: 0;
        top: 0;
        pointer-events: none;
        //transform-origin: 0px 0px;
        /*border: 2px solid transparent;*/
    }
    .zoom-comment{
        position: absolute;
        z-index: 1;
        margin-left: -10px;
        margin-top: -10px;
    }
    .zoom-comment-point{
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 100%;
        background-color: rgba(255,255,255, 0.5);
    }
    .zoom-comment-point:after {
        content: '';
        display: block;
        width: 4px;
        height: 4px;
        background-color: rgba(63, 95, 195, 0.8);
        position: absolute;
        left: 50%;
        top: 50%;
        margin-top: -5px;
        margin-left: -2px;
        border-radius: 100%;
    }
    .zoom-comment-content{
        position: absolute;
        top: 0;
        width: 300px;
        display: none;
    }
    .zoom-comment:hover .zoom-comment-content{
        display: flex;
    }
    .zoom-comment-content-inner{
        padding: 5px 10px;
        background-color: #fff;
        border-radius: 2px;
        max-width: 300px;
        display: inline-block;
        word-wrap: break-word;
    }
    .zoom-add-comment{
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 2;
        display: flex;
        align-items: center;
        background-color: rgba(0,0,0,0.2);
        justify-content: center;
    }
    .zoom-add-comment-text{
        color: #fff;
        font-size: 18px;
    }
    .zoom-add-comment:hover{
        background-color: rgba(0,0,0,0);
        .zoom-add-comment-text{
            display: none;
        }
    }
    .zoom-controllers{
        margin-top: 10px;
    }
    .preview{
        background-repeat: no-repeat;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -o-user-select: none;
        user-select: none;
    }
    .grab-bing {
        cursor : grab;
    }
    .grab-bing:active {
        cursor : grabbing;
    }
</style>
