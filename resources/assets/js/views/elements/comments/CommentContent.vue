<template>
    <section>
        <div class="ql-editor" v-html="getContent" v-if="html"></div>
    </section>
</template>

<script>
    export default {
        name: "comment-content",
        props: {
            html: {
                type: String,
                default: '',
            },
            files: {
                type: Array,
                default: () => {
                    return [];
                },
            },
            task_id: Number,
        },
        computed: {
            getContent() {
                return this.html.replace(/(#\w+)/g, '<span class="tag">$1</span>');
            },
        },
        mounted() {
            this.$el.addEventListener('click', (event) => {
                let arrayOfStrings = event.target.alt?event.target.alt.split('.'):[];
                let ext = arrayOfStrings.pop();
                this.$el.addEventListener('click', (event) => {
                    if (event.target.tagName.toLowerCase() === 'img') {
                        let file = this.files.find(item => item.name === event.target.alt);

                        if (file && file.is_image) {
                            return this.$modal.show('light-box-modal', {image: file, task_id: this.task_id});
                        }

                        file = {
                            id:         null,
                            name:       null,
                            path:       event.target.src,
                            size:       null,
                            created_at: null,
                        };
                        return this.$modal.show('light-box-modal', {image: file, task_id: this.task_id});
                    }

                    if (event.target.className === 'tag') {
                        this.$event.$emit('show-search');
                        this.$event.$emit('set-search-text', event.target.innerHTML);
                    }
                });

                if (event.target.className === 'tag') {
                    this.$event.$emit('show-search');
                    this.$event.$emit('set-search-text', event.target.innerHTML);
                }
            });
        },
        methods: {
            test(elem) {
                // Create our event (with options)
                var evt = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                // If cancelled, don't dispatch our event
                var canceled = !elem.dispatchEvent(evt);
            }
        }
    }
</script>

<style lang="scss">

</style>
