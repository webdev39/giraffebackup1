<template>
    <div class="col-xs-12 task-create-input task-title-wrapper">
        <div class="task-create-icon pull-left">
            <theme-sidebar
                    :title="$t('create_task')"
                    id="add-task"
                    type="button"
                    class="btn"
                    @click.native="handleEnter"
										:style="{'background-color': primaryColor}"
            >
                <i class="icon-plus">
                    <svg class="icon font-color-green" xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-plus">
                        </use>
                    </svg>
                </i>
            </theme-sidebar>
        </div>

        <div class="task-headline pull-left" @click="$emit('click-input')">
            <quill-editor
                    :content="form.name"
                    :options="editorOption"
                    @change="onEditorChange($event)"
                    ref="editor"
                    :placeholder="$t('quill.placeholder')"
                    :class="[`quill-editor--${getCurrentLang}`]"
            />
        </div>
    </div>

</template>

<script>
	import quillTagMixin from '@mixins/quillTag'

	import {mapGetters} from 'vuex'

	import ThemeSidebar         from '@views/layouts/theme/ThemeSidebar'

	export default {
		mixins: [
			quillTagMixin
		],
		props: {
			users: {
				type: Array,
				default: () => {
					return [];
				}
			},
			placeholder: {
				type: String,
			},
		},
		data() {
			return {
				form: {
					name: null,
					planned_deadline: null,
                    deadline: null,
                    soft_budget: null,
					addMentions: []
				},
				name: null,
				editorOption: {
					theme: 'snow',
					modules: {
						toolbar: false,
						mention: {
							dataAttributes: ['id'],
							defaultMenuOrientation: 'bottom',
							mentionDenotationChars: ["@", "#"],
							renderItem(item) {
								if (item.hasOwnProperty('avatar')) {
									if (item.avatar) {
										return `<img src="${item.avatar}" alt="${item.initials}"/><span>${item.value}</span>`;
									}

									return `<img src="" alt="${item.initials}"/><span>${item.value}</span>`;
								}

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
									handler: () => {
									},

								},
								{
									key: 32,
									handler: this.handleSpace,
								},
							],
						},
					},
					placeholder: this.placeholder,
				}
			}
		},
		computed: {
			...mapGetters({
				getTags: 'tags/getTags',
				getUserProfile: 'user/getUserProfile',
				primaryColor: 'user/getPrimaryColor',
			}),
			getCurrentLang() {
				return this.getUserProfile.language.iso_639_1;
			},
			mentions() {
				let users = this.users;

				if (this.form.addMentions.length) {
					users = users.filter(user => {
						return !this.form.addMentions.some(mention => mention === user.id)
					});
				}

				return users.map(user => {
					user.value = `${user.name} ${user.last_name}`;
					user.initials = `${user.name[0]}${user.last_name[0]}`;

					return user;
				});
			},
			quill() {
				return this.$refs.editor.quill;
			},
		},
		  components: {
          ThemeSidebar,
        },
		watch: {
			placeholder(newValue) {
				this.$refs.editor.quill.root.dataset.placeholder = newValue
			}
		},
		methods: {
			handleSpace(event) {
				return this._handleSpace(event, 'name')
			},
			onEditorChange({html}) {
				this.form.name = html;
				this.form.addMentions = [];

				html.replace(/data-denotation-char="@" data-id="(\d)/gi, (match, str) => {
					this.form.addMentions.push(+str);
				});
				this.$emit('set-task-name', this.form.name);
			},
			transformText() {
				const elements = this.$refs.editor.$el.getElementsByClassName("mention");

				let content = this.$refs.editor.$el.querySelector('.ql-editor');
				let withoutMention = content.textContent.replace(/@\w+/, "");
				this.form.name = withoutMention
					.trim()
					.replace(/date\/time:\s*([0-3][0-9].[0-1][0-9].\d{4}\s*(\d{2}:\d{2})|tomorrow|next week)*/gi, (match) => {
						let time = match.replace(/date\/time:/gi, "").trim();

						switch (time) {
							case 'tomorrow':
								time = this.$moment().add(1, 'days').utc().format('YYYY-MM-DD HH:mm')
								break;
							case 'next week':
								time = this.$moment().add(7, 'days').utc().format('YYYY-MM-DD HH:mm')
								break;
							default:
								time = this.$moment(time, 'DD.MM.YYYY HH:mm').utc().format('YYYY-MM-DD HH:mm');
						}

						this.form.planned_deadline = time;

						return ""
					}).replace(/\s\s+/gi, " ");
				if(!this.form.planned_deadline) {
                    this.form.planned_deadline = this.$moment().format('YYYY-MM-DD HH:mm:ss');
                    this.form.deadline = this.$moment().add(24, 'hours').format('YYYY-MM-DD HH:mm:ss');
                    this.form.soft_budget = '24:00';
                }
			},
			handleEnter() {
				this.transformText();

				this.$emit('send', this.form);
				return false;
			},
			clearForm() {
				this.form = {
					name: null,
					planned_deadline: null
				}
			},
			handleMentions(searchTerm, renderList, mentionChar) {
				let values;

				if (mentionChar === "@") {
					values = this.mentions;
				} else {
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
    .task-create-input {
        .quill-editor {
            position: relative;
            top: -3px;
            z-index: 1;
        }

        .ql-container.ql-snow {
            border: none;
        }

        .ql-editor {
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

        .ql-editor.ql-blank::before {
            left: 0;
            top: 10px;
        }

        .ql-mention-list-container {
            .ql-mention-list {
                .ql-mention-list-item {
                    cursor: pointer;
                    height: 44px;
                    line-height: 40px;
                    font-size: 16px;
                    padding: 3px 20px;
                    vertical-align: middle;

                    span {
                        padding-left: 6px;
                    }

                    img {
                        position: relative;
                        top: -3px;
                        left: -5px;
                        width: 26px;
                        height: 26px;
                        display: inline-block;
                        font-size: 1px;
                        border-radius: 100%;
                    }

                    img[alt]:after {
                        content: "";
                        display: block;
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 26px;
                        height: 26px;
                        background-color: #fff;
                    }

                    img[alt]:before {
                        content: attr(alt);
                        display: block;
                        position: absolute;
                        background: #4060c2;
                        top: 0;
                        left: 0;
                        width: 26px;
                        height: 26px;
                        font-size: 12px;
                        line-height: 26px;
                        border-radius: 100%;
                        text-align: center;
                        color: #fff;
                        z-index: 1;
                    }

                    &.selected {
                        img[alt]:after {
                            background-color: #d3e1eb;
                        }
                    }
                }
            }
        }
    }

</style>
