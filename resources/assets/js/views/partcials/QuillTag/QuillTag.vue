<template>
    <div class="quill-tag" :class="{'quill-tag-input': isInput}">
        <quill-editor
            v-model="form.content"
            ref="editor"
            :options="editorOption"
            :placeholder="$t('quill.placeholder')"
            :class="[`quill-editor--${getCurrentLang}`]"
        >
        </quill-editor>
    </div>
</template>

<script>
    import { mapGetters } from "vuex";

    import quillTagMixin from '@mixins/quillTag'

    export default {
        mixins:[
            quillTagMixin
        ],
        props: {
            content: {
                type: String,
                default: '',
            },
            isInput: {
               type: Boolean,
               default: false,
            }
        },
        data() {
            return {
                form: {
                    content: null,
                },
                editorOption: {
                    theme: 'snow',
                    placeholder: '',
                    modules: {
                        toolbar: false,
                        mention: {
                            dataAttributes: ['id', 'value'],
                            defaultMenuOrientation: 'bottom',
                            mentionDenotationChars: ["#"],
                            renderItem(item, searchTerm) {
                                return item.value;
                            },
                            source: this.handleMentions,
                        },
                        keyboard: {
                            bindings: [
                                {
                                    key: 13,
                                    handler: this.handleEnter,
                                },
                                {
                                    key: 13,
                                    shiftKey: true,
                                    handler: this.handleEnter,

                                },
                                {
                                    key: 32,
                                    handler: this.handleSpace,
                                },
                            ],
                        },
                    },
                }
            }
        },
        watch: {
            content: function (val) {
                this.form.content = this._replaceContent(val);
            },
        },
        computed: {
            ...mapGetters({
                getTags: 'tags/getTags',
				getUserProfile: 'user/getUserProfile',
			}),
			getCurrentLang() {
				return this.getUserProfile.language.iso_639_1;
			},
            quill() {
                return this.$refs.editor.quill;
            },
            quillEditor() {
                return this.$refs.editor.$el.querySelector('.ql-editor')
            },
        },
        mounted() {
            this.form.content = this._replaceContent(this.content);
            setTimeout(() => { document.activeElement.blur() }, 10)
        },
        methods: {
            handleSpace(event) {
                return this._handleSpace(event, 'content')
            },
            handleEnter() {
                if (!this.isInput) {
                    return true;
                }
            },
            getEditorContent() {
                return this.quillEditor.textContent
            },
            handleMentions(searchTerm, renderList, mentionChar) {
                let values;

                if (mentionChar === "#") {
                    values = this.getTags;
                }

                if (searchTerm.length === 0) {
                    renderList(values, searchTerm);
                } else {
                    const matches = [];

                    for (let i = 0; i < values.length; i++) {
                        if (~values[i].value.toLowerCase().indexOf(searchTerm.toLowerCase())) {
                            matches.push(values[i]);
                        }
                    }

                    renderList(matches, searchTerm);
                }
            },
        },
    }
</script>

<style lang="scss">
    .quill-tag {
        .ql-container{
            border-radius: 4px;
        }
        .ql-mention-list-container{
            left: 0 !important;
        }
    }

    .quill-tag-input{
        .quill-editor{
            position: relative;
            top: -5px;
            z-index: 1;
        }
        .ql-container.ql-snow{
            border: none;
        }
        .ql-editor{
            padding: 0;
            white-space: normal;
            height: 30px;
            min-height: 18px;
            position: relative;
            overflow: hidden;
            p {
                white-space: pre;
                line-height: 24px;
                width: 100%;
                overflow: auto;
                position: relative;
                top: 6px;
            }
        }
        .ql-editor.ql-blank::before{
            left: 0;
            top: 10px;
        }
    }
</style>