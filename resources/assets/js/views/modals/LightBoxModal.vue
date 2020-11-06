<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="90%" :maxWidth="700" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1000" >
        <div class="modal-content">
            <div class="modal-header">
                <a href="#" class="btn-close-header" @click.prevent="closeModal">
                    <i class="icon-close" >
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink"
                            xlink:href="#icon-close">
                            </use>
                        </svg>
                    </i>
                </a>
            </div>
            <div class="modal-body">
                <div class="lightbox-modal__img">
                    <Zoom :image="image" :task_id="task_id" />
                </div>

                <div class="lightbox-modal__info">
                    <div v-if="image.size">
                        <b>{{ $t('size') }}:</b> {{ image.size | sizeForHumans }}
                    </div>

                    <div v-if="image.user">
                        <b>{{ $t('person') }}:</b> {{ user.name }} {{ user.last_name }}
                    </div>

                    <div v-if="image.created_at">
                        <b>{{ $t('upload_time') }}:</b> {{ image.created_at | toLocalTime }}
                    </div>
                </div>

                <div class="clearfix">
                    <a class="btn btn-primary pull-right" :href="image.download_url || image.path" @click="closeModal" target="_blank">{{ $t('download') }}</a>
                </div>

            </div>
        </div>
    </modal>
</template>

<script>
    import Zoom from "@views/components/zoom/Zoom";

    export default {
        name: "light-box-modal",
        data() {
            return {
                image: {
                    id:         null,
                    name:       null,
                    path:       null,
                    size:       null,
                    created_at: null,
                },
                user: {
                    name:       null,
                    last_name:  null,
                },
                task_id:        null,
            }
        },
        components: {
            Zoom
        },
        methods: {
            beforeOpen(event) {
                if (!event.params) {
                    return;
                }

                if (event.params.task_id) {
                    this.task_id = event.params.task_id;
                }

                if (event.params.user) {
                    this.user = event.params.user;
                }

                if (event.params.image) {
                    this.image = {
                      ...event.params.image,
                      path: event.params.image.path.replace(/-thumb/, ''),
                    };
                }
            },
            beforeClose(event) {
                this.resetComponentData();
            },
            closeModal() {
                this.$modal.hide('light-box-modal')
            }
        }
    }
</script>

<style lang="scss">
    #light-box-modal {

    .lightbox-modal__img img {
        min-width: 100%;
        width: 100%;
        height: auto;
        position: relative;
    }
    .lightbox-modal__info {
        margin-bottom: 10px;
        margin-top: 10px;
        color: #333;
    }
    .zoom-container {
        position: relative;
        overflow: hidden;
        &:hover{
            .zoom-preview{
                opacity: 1;
            }
            .zoom-cursor{
                opacity: 1;
            }
        }
    }

    .zoom-cursor {
        position: absolute;
        z-index: 1;
        width: 40px;
        height: 40px;
        background: -webkit-radial-gradient(9px 10px, #3f5fc3 10px, transparent 10px);
        background-size: 3px 3px;
        opacity: 0;
        pointer-events: none;
    }

    .zoom-preview {
        border: 1px solid #d4d4d4;
        width: 300px;
        height: 300px;
        position: absolute;
        left: 100%;
         margin-left: 35px;
        top: 0;
        pointer-events: none;
        background-repeat: no-repeat;
        z-index: 0;
        opacity: 0;
    }

    .zoom-container-disable{
        &:hover{
            .zoom-preview{
                opacity: 0;
            }
            .zoom-cursor{
                opacity: 0;
            }
        }
    }
    .zoom-container:not(.zoom-container-disable) {
        cursor: none;
    }

    .btn-close-header {
            background: transparent;
            border:none;
            box-shadow: none;
            fill:#fff;
            position: absolute;
            right: 13px;
            top: 17px;
            transform: translateY(-50%);
            z-index: 9999;
            padding: 0 5px;
                &:hover {
                    background: transparent;
                    border:none;
                    box-shadow: none;
                    fill:#e2e6e9;
                }
            .icon-close {
                display: block;
                 .icon {
                     width: 14px;
                     height: 14px;
                 }
            }
        }

        .modal-content {
            border:none;
        }
}
</style>
