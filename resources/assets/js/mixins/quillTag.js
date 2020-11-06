import { mapGetters }  from "vuex";
import find            from "@helpers/findInGroups";

// Optimized

export default {
    computed: {
        ...mapGetters({

        }),
    },
    methods: {
        _replaceContent(content, callback) {
            if (!content) {
                return
            }

            return content.replace(/#\w+/g, (match) => {
                if (callback) {
                    callback(match);
                }

                return `<span class="mention" data-index="0" data-denotation-char="#" data-id="1" contenteditable="false" data-value="${match.substring(1)}"></span>`
            });
        },
        _handleSpace(event, name) {
            let matchLength = 0;
            let startIndex = event.index;

            this.form[name] = this._replaceContent(this.form[name] , (match) => {matchLength = match.length;});

            if (matchLength) {
                startIndex = startIndex - matchLength + 1;
                setTimeout(() => {
                    this.quill.insertText(startIndex, " ");
                    this.quill.setSelection(startIndex + 1, 0, 'api')
                }, 10);

                return false;
            }

            return true;
        },
    }
};
