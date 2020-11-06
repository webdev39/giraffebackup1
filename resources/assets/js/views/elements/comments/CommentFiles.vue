<template>
    <div class="attachment-container" v-show="files.length">
        <div class="attachment-item" v-for="(file, index) in files" :key="index">
            <button class="btn-remove" @click="deleteFile(index)" v-if="canDelete">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>

            <div class="image-box" @click="showPreview(file)">
                <div class="image-container" v-bind:style="{ backgroundImage: 'url(' + file.path + ')' }" v-if="file.is_image"></div>

                <div class="image-not-found" v-else>
                    <i class="oc icon-document-empty"></i>
                </div>
            </div>

            <p class="filename">{{ file.name | cropExtension }}</p>
        </div>
    </div>
</template>

<script>
    import {mapGetters}         from 'vuex'

    export default {
        name: "comment-files",
        props: {
            value: {
                type: Array,
                default:() => {
                    return [];
                }
            },
            user: {
                type: Object,
                default: null,
            },
            canDelete: {
                type: Boolean,
                default: false,
            },
            task_id: Number,
        },
        computed:{
            ...mapGetters({
                currentUser: 'user/getUserProfile'
            }),
            files: {
                get() {
                    return this.value;
                },
                set(value) {
                    return this.$emit('input', value);
                }
            }
        },
        methods: {
            showPreview(file) {
                if (file.is_image) {
                    return this.$modal.show('light-box-modal', {image: file, user: this.user || this.currentUser, task_id: this.task_id});
                }

                return window.open(file.path, '_blank');
            },
            deleteFile(index) {
                this.files.splice(index, 1);
            },
        }
    }
</script>

<style lang="scss">
    .attachment-container {
        width: 100%;
        padding: 15px 15px 0 15px;
        background: #efefef66;
        border-top: 1px solid #ccc;
        display: inline-block;
        text-align: center;

        .attachment-item {
            position: relative;
            width: 80px;
            height: 100px;
            cursor: pointer;
            margin: 5px;
            display: inline-block;
        }

        .image-box {
            background: #fafafa;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 1px 1px 1px rgba(0,0,0,0.5);

            .image-container {
                display: block;
                width:100%;
                padding-top: 100%;
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
            }

            .image-not-found {
                width:100%;
                padding-top: 100%;
                background: #C7C7C7;
                position: relative;
                border-radius: 5px;

                i.icon-document-empty {
                    position: absolute;
                    color: #f1f0f0;
                    font-size: 56px;
                    top: 50%;
                    left: 50%;
                    margin: -28px -26px;

                    &:before {
                        content:'\0044';
                    }
                }
            }
        }

        .filename {
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            margin: 0 0 10px;
            font-size: 12px;
            font-style: italic;
            text-overflow: ellipsis;
        }

        .btn-remove  {
            background: none;
            border: none;
            opacity: 0;
            transition: opacity 200ms;
            position: absolute;
            right: -9px;
            top: -9px;
            line-height: 0;
            background: #f0f0f0 !important;
            width: 24px;
            height: 24px;
            border-radius: 100%;
            color: #b2b2b2;
            box-shadow: -1px 1px 1px;
            z-index: 5;

            i {
                font-size: 14px;
                padding: 0;
                color: #bf5329;
            }
        }

        .attachment-item:hover .btn-remove {
            opacity: 1;
        }
    }
</style>
