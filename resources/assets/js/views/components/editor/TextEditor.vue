<template>
    <div class="editor-wrapper">
        <quill-toolbar :id="toolbarId"
                       :can-block="false"
                       :can-indent="false"
                       :can-link="false"
                       :can-upload="false"
                       :can-logged="false"/>
        <quill-editor
            v-model="form.content"
            ref="editor"
            :options="options"
            :placeholder="$t('quill.placeholder')"
            :class="[`quill-editor--${getCurrentLang}`]"
        />
    </div>
</template>

<script>
	import { mapGetters }       from 'vuex';

	import QuillToolbar         from '@views/components/editor/QuillToolbar';
    import QuillFooter          from "@views/components/editor/QuillFooter";

    export default {
        name: "text-editor",
        props: {
            value: {
                type: String,
                default: '',
            },
        },
        data () {
            return {
                form: {
                    content: '',
                },
                options: {
                    theme: 'snow',
                    placeholder: this.placeholder,
                    bounds: '.editor-wrapper',
                    debug: 'error',
                    modules: {
                        magicUrl: true,
                        keyboard: {
                            bindings: {
                                handleEnter: {
                                    key: 13,
                                    ctrlKey: true,
                                    handler: this.handleEnter,
                                }
                            }
                        },
                        blotFormatter: {},
                        // dragAndDrop: {
                        //     draggables: [{
                        //         content_type_pattern: '^(.*)',
                        //         tag: 'img',
                        //         attr: 'src',
                        //     }],
                        //     onDrop: (file) => {
                        //         this.setCustomFile(file);
                        //
                        //         return false;
                        //     }
                        // }
                    },
                },
            }
        },
        computed: {
			...mapGetters({
				getUserProfile: 'user/getUserProfile',
			}),
			getCurrentLang() {
				return this.getUserProfile.language.iso_639_1;
			},
            quill() {
                return this.$refs.editor.quill;
            },
            toolbarId() {
                const id = `toolbar-${this._uid}`;

                this.options.modules.toolbar = `#${id}`;

                return id;
            }
        },
        components: {
            QuillFooter,
            QuillToolbar
        },
        mounted() {
            this.form.content = this.value;
        },
        methods: {
            getContent() {
                return this.form.content;
            },
            setCustomFile(file) {
                this.handleUploadFile(file).then((attachment) => {
                    let index = 0;
                    let image = `<img src="${attachment.path}" alt="${attachment.name}" />`;
                    let range = this.quill.getSelection();

                    if (range) {
                        index = range.index;

                        if (range.length !== 0) {
                            index = range.length
                        }
                    }

                    this.quill.clipboard.dangerouslyPasteHTML(index, image);
                    this.quill.setSelection(index + 1, 0);
                });
            },
            handleUploadFile(file) {
                let formData = new FormData();
                formData.append('attachment', file);

                return this.$api.media.createMedia(formData).then(data => {
                    return data.attachment;
                }).catch((error) => {
                    this.$notify({type:'error', text: error.response.data.errors.attachment[0]});

                    throw error;
                });
            },
            handleEnter() {
                this.$emit('send', this.form);
            },
        }
    }
</script>

<style lang="scss">
    .ap {
        background-image: url("/images/sheet_apple_64.png");
    }

    .ql-container {
        font-size: 14px;

        &.ql-snow {
            border: none;
        }
    }
</style>
