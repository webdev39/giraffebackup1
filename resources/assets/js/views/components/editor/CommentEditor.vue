<template>
    <div class="editor-wrapper">
        <quill-editor
            v-model="form.content"
            ref="editor"
            :class="[`quill-editor--${getCurrentLang}`, {'quill-editor-fetch':fetchImage}]"
            :options="options"
            enable="false"
            :placeholder="$t('quill.placeholder')"
        />
        <quill-footer
            :id="toolbarId"
            :canLogged="canLogged"
            :replyTask="replyTask"
            :task_id="form.task_id || taskId"
            :files.sync="form.attachments"
            :loggedHours.sync="form.loggedTime.hours"
            :loggedMinutes.sync="form.loggedTime.minutes"
            :loggedDate.sync="form.loggedDate"
            @updateLoggedMinutes="updateLoggedMinutes"
            @updateLoggedHours="updateLoggedHours"
            @updateLoggedDate="updateLoggedDate"
            @uploadCustomFile="setCustomFile"
            @showMention="showMention"
        >
            <template slot="footer" slot-scope="slot">
                <slot name="footer" v-bind:loggedInfo="slot.loggedInfo"></slot>
            </template>
        </quill-footer>
    </div>
</template>

<script>
	import { mapGetters }       from 'vuex'
	import { Drop }             from 'vue-drag-drop'

	import find                 from '@helpers/findInGroups'

	import quillTagMixin        from '@mixins/quillTag'
	import QuillToolbar         from '@views/components/editor/QuillToolbar';
	import QuillFooter          from "@views/components/editor/QuillFooter";

	export default {
		name: "comment-editor",
		mixins:[
			quillTagMixin
		],
		props: {
			value: {
				type: Object,
				default:() => {
					return {};
				}
			},
			users: {
				type: Array,
				default:() => {
					return [];
				}
			},
			placeholder: String,
			replyTask: {
				type: [Boolean, Number],
				default: false,
			},
			listenerChange: {
				type: Boolean,
				default: false,
			},
			canLogged: {
				type: Boolean,
				default: true,
			},
			taskId: {
				type: Number,
				default: null,
			},
		},
		data () {
			return {
				fetchImage: false,
				enterTarget: null,
				dragStart: false,
				form: {
					task_id: null,
					attachments:    [],
					content:        '',
					mentions:       [],
					loggedDate:     null,
					loggedTime: {
						hours:   0,
						minutes: 0,
						seconds: 0
					},
				},
				earlyForm: {},
				options: {
					theme: 'snow',
					placeholder: this.placeholder,
					bounds: '.editor-wrapper',
					debug: 'error',
					modules: {
						"emoji-toolbar": true,
						"emoji-textarea": false,
						"emoji-shortname": false,
						magicUrl: true,
						mention: {
							dataAttributes: ['id'],
							defaultMenuOrientation: 'bottom',
							mentionDenotationChars: ["@", "#"],
							source: this.handleMentions,
							renderItem(item, searchTerm) {
								return item.template(item);
							},
						},
						keyboard: {
							bindings: [
								{
									key: 13,
									ctrlKey: true,
									handler: this.handleEnter,
								},
								{
									key: 32,
									handler: this.handleSpace
								},
							],
						},
						blotFormatter: {
							specs: [],
						},
						dragAndDrop: {
							draggables: [{
								content_type_pattern: '^(.*)',
								tag: 'img',
								attr: 'src',
							}],
							onDrop: (file) => {
								this.setCustomFile(file);
								document.body.classList.remove("drag-comment");
								this.enterTarget = null;

								return false;
							},
						}
					},
				},
				typeShareUser: '',
                listTypeShareUser: {
                    board: 'board',
					task: 'task',
                }
			}
		},
		computed: {
			...mapGetters({
				getChangeComments: 'task/getChangeComments',
				getGroups: 'groups/getStateGroups',
				getTags: 'tags/getTags',
				getUserProfile: 'user/getUserProfile',
			}),
            getCurrentLang() {
				return this.getUserProfile.language.iso_639_1;
            },
			quill() {
				return this.$refs.editor.quill;
			},
			mentions() {
				return this.users.map(user => {
					user.value      = `${user.name} ${user.last_name}`;
					user.initials   = `${user.name[0]}${user.last_name[0]}`;

					return user;
				});
			},
			toolbarId() {
				const id = `toolbar-${this._uid}`;

				this.options.modules.toolbar = `#${id}`;

				return id;
			},
			getShareUsers() {
				let result = {};

				if (this.taskId) {

					if (this.typeShareUser === this.listTypeShareUser.task) {
						find.search(this.getGroups, {id: this.taskId}, 'task', (tasks, task, index, board, group) => {
							result = task.subscribers.notify;
						});
                    }

					if (this.typeShareUser === this.listTypeShareUser.board) {
						result = this.users.map(item => item.id);
					}

				} else {
					if (this.users.length) {
						result = this.users.map(item => item.id);
                    }
                }

				if (! Object.keys(result).length) {
					return false;
				}

				return result;
			},

		},
		components: {
			QuillFooter,
			QuillToolbar,
			'drop': Drop,
		},
		mounted() {
			if (this.quill) {
				this.quill.root.addEventListener('click', (event) => {
					if (event.target.tagName.toLowerCase() === 'img') {
						const file = this.form.attachments.find(item => item.name === event.target.alt);

						if (file && file.is_image) {
							return this.$modal.show('light-box-modal', {image: file, task_id: this.form.task_id || this.taskId});
						}

						window.open(event.target.src, '_blank');
					}
				});
				this.quill.root.addEventListener('paste', (event) => {
					this.getImageFromPaste(event, (imageBlob) => {
						this.setCustomFile(imageBlob);
					});
				});
			}
			this.setDefaultData();
			this.addListener();
			this.addWatcher();
		},
		beforeDestroy(){
			this.removeListener();
		},
		methods: {
			/**
			 * Method for get image for buffer (after paste in textarea)
			 *
			 * @param {Object} e
			 * @param {Function} callback
			 */
			getImageFromPaste(e, callback){
				let items = e.clipboardData.items;

				if(!e.clipboardData || (items === undefined || items === null)){
					return;
				}

				for (let i = 0; i < items.length; i++) {
					if (items[i].type.indexOf("image") === -1) {
						continue;
					}
					let blobFile = items[i].getAsFile();
					if(typeof(callback) == "function"){
						callback(blobFile);
					}
				}
			},

			handleSpace(event) {
				return this._handleSpace(event, 'content');
			},

			addWatcher() {
				if (this.listenerChange) {
					this.$watch('form',
						function (newVal, oldVal) {
							let commentid = this.getChangeComments.find(item => item === this._uid);

							if (JSON.stringify(this.earlyForm) !== JSON.stringify(this.form) && !commentid) {
								return this.$store.dispatch('task/addChangeComments', this._uid);
							}

							if (JSON.stringify(this.earlyForm) === JSON.stringify(this.form) && commentid) {
								return this.$store.dispatch('task/removeChangeComments', this._uid);
							}

						}, { deep: true });
				}
			},

			addListener() {
				document.addEventListener("dragenter",  this.dragenter);
				document.addEventListener("dragstart",  this.dragstart);
				document.addEventListener("dragleave",  this.dragleave);
				document.addEventListener("dragend",    this.dragend);
			},

			removeListener() {
				document.removeEventListener("dragenter",  this.dragenter);
				document.removeEventListener("dragleave",  this.dragleave);
				document.removeEventListener("dragend",    this.dragend);
				document.removeEventListener("dragstart",  this.dragstart);
			},

			dragenter (event) {
				if (this.dragStart) {
					return;
				}
				event.preventDefault();
				this.enterTarget++;
				document.body.classList.add("drag-comment");
			},

			dragstart (event) {
				this.dragStart = true;
			},

			dragleave () {
				if (this.enterTarget > 0) {
					this.enterTarget--;
				}

				if (this.enterTarget === 0) {
					document.body.classList.remove("drag-comment");
				}
			},

			dragend (event) {
				this.dragStart = false;
				document.body.classList.remove("drag-comment");
			},

			showMention () {
				let getSelection = this.quill.getSelection();

				if (getSelection) {
					if (getSelection.length === 0) {
						this.quill.insertText(getSelection.index, '@');

					} else {
						this.quill.deleteText(getSelection.index, getSelection.length);
						this.quill.insertText(getSelection.index, '@');
					}

					this.quill.setSelection(getSelection.index, 0, 'api');
					this.quill.setSelection(getSelection.index + 1, 0, 'api');

				} else {
					if (this.quill.getLength() === 1) {
						this.quill.insertText(0, '@');
						this.quill.setSelection(1, 0, 'api');
					} else {
						this.quill.insertText(this.quill.getLength() -1, '@');
						this.quill.setSelection(this.quill.getLength(), 0, 'api');
					}
				}
			},

			setDefaultData() {
				this.resetComponentData();

				if (this.value) {
					this.form.task_id       = this.value.task_id;
					this.form.content       = this.value.body || '';
					this.form.attachments   = this.copyCollection(this.value.attachments || []);

					if (this.value.timer) {
						this.form.content             = this.value.timer.comment || '';
						this.form.loggedTime.hours    = this.value.timer.time.h  || 0;
						this.form.loggedTime.minutes  = this.value.timer.time.i  || 0;
						this.form.loggedTime.seconds  = this.value.timer.time.s  || 0;
						this.form.loggedDate          = this.toLocalTime(this.value.timer.end_time);
					}
				}

				this.earlyForm = {...this.form};
			},

			getComment: function () {
				const elements = this.$refs.editor.$el.getElementsByClassName("mention");
				const result = {
					content: this.form.content,
					time: null,
					attachments: [],
					mentions: []
				};

				const { attachments, loggedTime, loggedDate } = this.form;
				const { hours, minutes, seconds } = loggedTime;

				for (let element of elements) {
					if (this.checkAllAttachUser(element.dataset.value) && this.getShareUsers) {
						this.getShareUsers.forEach(item => {
							if (!result.mentions.includes(item)) {
								result.mentions.push(Number(item));
							}
						});
					}

					element.dataset.id ? result.mentions.push(Number(element.dataset.id)) : '';
				}

				if (attachments) {
					result.attachments = attachments.map((item) => item.id)
				}

				if (hours || minutes || seconds) {
					result.loggedTime = `${hours}:${minutes}:${seconds}`;
				}

				if (loggedDate) {
					result.loggedDate = this.toUTCTime(loggedDate);
				}

				return result;
			},

            checkFileSize(files) {
                let filesSize = Object.values(files).reduce((acc, currentValue) => acc + currentValue.size, 0);
                let maxSize = 52428800; //50 * 1024 * 1024;
                if (filesSize > maxSize) {
                    return this.$notify({type:'error', text: 'Maximum file size 50 MB exceeded'});
                }
            },

			setCustomFile(files) {
                const isBiggerFileSize = this.checkFileSize(files);

				if (isBiggerFileSize) {
					return isBiggerFileSize;
				}

				this.handleUploadFile(files).then((attachment) => {
					let index = 0;
					let range = this.quill.getSelection();

					if (range) {
						index = range.index;

						if (range.length !== 0) {
							index = range.length
						}
					}
					attachment.forEach(item => {
					    if(item.is_image) {
                            this.quill.clipboard.dangerouslyPasteHTML(index, `<img src="${item.path}" alt="${item.name}">`);
                            this.quill.setSelection(index + 1, 0);
                        } else {
                            this.quill.clipboard.dangerouslyPasteHTML(index, `<a style="color: #77a0d2" href="${item.path}" target="_blank">${item.name}</a>`);
                            this.quill.setSelection(index + item.name.length, 0);
                        }
                    });
				});
			},

			updateLoggedHours(value) {
				this.form.loggedTime.hours = value;
			},

			updateLoggedMinutes(value) {
				this.form.loggedTime.minutes = value;
			},

			updateLoggedDate(value) {
				this.form.loggedDate = value;
				this.$emit('update-logged-date');
			},

			/**
			 * Method for handle getting mentions
			 * @param {String} searchTerm
			 * @param renderList
			 * @param {String} mentionChar
			 */
			handleMentions(searchTerm, renderList, mentionChar) {
				let matches = [];

				if (mentionChar === "@") {
					matches = this.renderMentionItem(this.mentions, searchTerm);
				}

				if (mentionChar === "#") {
					matches = this.renderTagItem(this.getTags, searchTerm);
				}

                renderList(matches, searchTerm);
			},

			/**
			 * Method for check send all includes users
			 * @param {Array} list
			 * @param {String} needle
			 */
			renderMentionItem(list, needle) {
				let render = [];
                let checkAll = this.checkAllAttachUser(needle);
                let taskId = this.$route.query.hasOwnProperty('taskId');

                if (! taskId) {
					checkAll = false
                }

				if(checkAll) {
					render.push({
						value: checkAll,
						template: () => {
							return `<div class="ql-mention-list-everyone"><b>@${checkAll}</b> ${this.$t('notify_task')} ${checkAll}</div>`;
						}
					});
				}

				if (!needle.length && taskId) {
					Object.entries(this.listTypeShareUser).forEach(([key, value]) => {
						render.push({
							value: value,
							template: () => {
								return `<div class="ql-mention-list-everyone"><b>@${value}</b> ${this.$t('notify_task')} ${this.$t(value)}</div>`;
							}
						});
					});
                }

                list.forEach(item => {
                    if (~item.value.toLowerCase().indexOf(needle.toLowerCase())) {
                        render.push({
                            id: item.id,
                            value: item.value,
                            template: () => {
                                return `<img src="${item.avatar}" alt="${item.initials}"/><span>${item.value}</span>`;
                            }
                        });
                    }
                });

				return render;
			},

			/**
			 * Method for check send all includes users
			 * @param {Array} list
			 * @param {String} needle
			 */
			renderTagItem(list, needle) {
                let render = [];

				list.forEach(item => {
					if (~item.value.toLowerCase().indexOf(needle.toLowerCase())) {
						render.push({
							id: item.id,
							value: item.value,
							template: () => {
								return `<span>${item.value}</span>`;
							}
						});
					}
				});

				return render;
			},

			/**
			 * Method for check send all includes users
			 * @param {String} row
			 */
			checkAllAttachUser(row) {
				let list = Object.values(this.listTypeShareUser);
				let type = false;

				list.some(item => {
					if (item.includes(row) && row.length) {
						type = item;
						this.typeShareUser = item;
                    }
                });

				return type;
			},

			handleUploadFile(files) {
				let formData = new FormData();

				if (files.length) {
					for (let i = 0; i < files.length; i++) {
						formData.append('attachment[]', files[i], files[i].name);
					}
                } else {
					formData.append('attachment[]', files, files.name);
                }

                this.fetchImage = true;

				return this.$api.media.createMedia(formData).then(data => {
					data.attachment.forEach(item => this.form.attachments.push(item));
					return data.attachment;
				}).catch((error) => {
				    if(error.response.data.errors) {
                        this.$notify({type:'error', text: error.response.data.errors.attachment[0]});
                    }
					throw error;
				}).finally(() => this.fetchImage = false)
			},
			handleEnter() {
				this.$emit('send', this.form);
				this.earlyForm = {...this.form};
			},
		}
	}
</script>

<style lang="scss" >
    .ap {
        background-image: url("/images/sheet_apple_64.png");
    }

    .comment-edit .ql-toolbar.ql-snow{
        padding: 0;
    }

    .editor-wrapper {
        max-width: calc(100vw - 80px);
        .toolbar-container{
            border-top: 1px solid #444;
            padding: 10px 20px;
            margin: 15px -5px 0;
        }

        .ql-toolbar{
            .quill-editor-button-state {
                margin: 0;
                /*margin-left:20px;*/
                border: 1px solid #376aa7;
                background: #fff;
                color: #376aa7;
                padding: 2px 15px;
                width: auto;
                height: auto;

                &:hover {
                    background:#376aa7;
                    color:#fff;
                }
            }

            .control-container{
                color: #6c757d;
                /*span {*/
                    /*color: #6c757d;*/
                /*}*/
            }
            &.ql-snow{
                border: none;
                .comment-box-options{
                    display:flex;
                    flex-wrap: wrap;
                    align-items:center;
                    justify-content:flex-end;
                    margin:10px 20px;

                    .ql-stroke{
                        stroke: #d5d5d5;
                        stroke-width: 1px;
                    }
                    .ql-fill{
                        fill:  #d5d5d5;
                    }
                    .ql-formats{
                        position: relative;
                        cursor:pointer;
                        font-size: 17px;
                        //margin-left:20px;
                        width:25px;
                        height:25px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#d5d5d5;
                        border-radius:5px;
                        button {
                            height: auto;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 2px;
                            svg {
                                width: 100%;
                            }
                            .fa-ellipsis-v{
                                margin-top: 1px;
                            }
                        }
                        &:hover{
                            background:#f3f3f3;
                            color:#376aa7;
                            button {
                                .ql-stroke{
                                    stroke: #376aa7;
                                }
                                .ql-fill{
                                    fill:  #376aa7;
                                }
                            }
                        }
                    }
                    .ql-formats-last{
                        margin-right: 0;
                    }
                }
                .comment-box-options-item{
                    margin-left: 15px;
                    margin-right: 0;
                    &:first-child{
                        margin-left: 0;
                    }
                }
            }
        }

        .ql-container {
            font-size: 14px;
            margin:10px 20px;

            &.ql-snow {
                border: none;
                .ql-editor {
                    border-radius: 10px;
                }
            }

            .textarea-emoji-control {
                display: none;
            }

            .mention {
                font-size: 14px;

                & > span {
                    padding: 0 4px;
                }
            }
        }
        .ql-editor {
            /*border-radius: 10px;*/
            border: 1px solid #f3f3f3;
            transition:.2s;

            &:focus {
                -webkit-box-shadow: 0 10px 6px -6px #d5d5d5;
                box-shadow: 0 10px 6px -6px #d5d5d5;
                border: 1px solid #d5d5d5;
            }

            a{
                color: #06c;
                text-decoration: underline;
                &:hover{
                    text-decoration: none;
                    color: inherit;
                }
            }
        }

    }

    .sidebar-show .editor-wrapper {
        max-width: calc(100vw - 260px - 80px);
    }

    .ql-mention-list-container {
        width: auto;
        min-width: 400px;
        max-height: 230px;
        overflow-x: auto;

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
                    object-fit: cover;
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
    .drag-comment {
        .comments .comment-edit .ql-editor::before,
        .ql-container .ql-editor::before{
            right: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #e6f4ff;
            border: 1px dashed rgb(80, 120, 242);
            color: #5078f2;
            border-radius: 10px;
            content: "Drop your file to upload'";
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1;
            position: absolute;
        }
    }
    .quill-editor-fetch{
        position: relative;
        &:before{
            content: " ";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        &:after{
            content: " ";
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 46px;
            height: 46px;
            margin: -23px -23px 0 0;
            border-radius: 50%;
            border-width: 5px;
            border-style: solid;
            border-color: #3669a7 transparent #3669a7 transparent;
            -webkit-animation: spin 1.2s linear infinite;
            animation: spin 1.2s linear infinite;
            z-index: 1;
        }
    }

    @media (max-width: 768px) {
        .editor-wrapper {
            max-width: calc(100vw - 40px);
        }
        .ql-container {
            // margin: 10px;
        }
				.group-setting-modal {
						.validation-container {
            .ql-container {
                margin: 0;
            }
        	}
				}
        .comment-box-options {
            margin: 10px;
        }
    }
</style>
