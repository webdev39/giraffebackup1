<template>
    <div class="editor-wrapper">
        <quill-editor
            v-model="timelogComment"
            ref="editor"
            :options="options"
            enable="false"
            :placeholder="$t('quill.placeholder')"
            :class="[`quill-editor--${getCurrentLang}`]"
        />
    </div>
</template>

<script>
	import {mapGetters} from 'vuex';
	import {Drop} from 'vue-drag-drop'

	import quillTagMixin from '@mixins/quillTag'
	import QuillToolbar from '@views/components/editor/QuillToolbar';
	import QuillFooter from "@views/components/editor/QuillFooter";

	export default {
		mixins: [
			quillTagMixin
		],
		props: {
			value: {
				type: String,
				default: '',
			},
		},
		data() {
			return {
				options: {
					placeholder: "",
					modules: {
						toolbar: false,
						keyboard: {
							bindings: [
								{
									key: 13,
									handler: () => {
									},
								},
								{
									key: 13,
									shiftKey: true,
									handler: () => {
									},
								}
							],
						},
					},
				},
			}
		},
		computed: {
			timelogComment: {
				get: function () {
					return this.value
				},
				set: function (newValue) {
					this.$emit('input', newValue)
				}
			},
			...mapGetters({
				getUserProfile: 'user/getUserProfile',
			}),
		},
		components: {
			QuillFooter,
			QuillToolbar,
			'drop': Drop,
		},
		methods: {}
	}
</script>

<style lang="scss">
    .editor-wrapper {
        .ql-container {
            margin: 10px 0;
        }

        .ql-container.ql-snow {
            border-radius: 0;
        }
    }
</style>
