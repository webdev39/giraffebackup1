<template>
    <div class="upload-btn-wrapper">
        <label :for="name">{{ innerPlaceholder }}</label>
        <input ref="file" type="file" :name="name" :id="name" :accept="innerAccept" @change="upload($event)" :multiple="multiple"/>
    </div>
</template>

<script>
    export default {
        name: "button-file",
        props: {
            value: {
                type: FileList,
                default: null
            },
            name: {
                type: String,
                default: 'upload-file'
            },
            maxSize: {
                type: Number,
                default: 10240
            },
            accept: {
                type: Array,
                default: function() {
                    return [];
                }
            },
            multiple: {
                type: Boolean,
                default: false,
            },
            placeholder: {
                type: String,
                default: `${window.app.$t('choose_file')}...`
            }
        },
        data() {
            return {
                files: null,
            }
        },
        computed:{
            innerValue: {
                get() {
                    this.files = this.value;

                    return this.files;
                },
                set(files) {
                    this.files = files;

                    this.$emit("input", files)
                }
            },
            innerAccept() {
                return this.accept.join();
            },
            innerPlaceholder() {
                let files = Array.from(this.files || []);

                if (files.length === 0) {
                    return this.placeholder;
                }

                if (files.length === 1) {
                    return files[0].name;
                }

                return `Selected ${files.length} files`;
            }
        },
        methods: {
            upload(event) {
                let files   = Array.from(event.target.files);
                let maxSize = parseInt(this.maxSize) * 1024;

                for (let key in files) {
                    if (files[key].size >= maxSize) {
                        this.$refs['file'].value = null;

                        return this.$notify({type:'warning', text: `${this.$t('maximum_file_size_exceeded')} (${ this.sizeForHumans(maxSize) })`});
                    }
                }

                this.innerValue = event.target.files;

                this.$emit('change', event);
            },
            clear() {
                this.innerValue = this.$refs['file'].value = null;
            }
        }
    }
</script>

<style lang="scss">
    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;

        label {
            min-width: 200px;
            padding: 4px 30px;
            margin-top: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            color: #777;
            text-align: center;
            background-color: #fff;
            border-radius: 5px;
            cursor: pointer;

            &:hover {
                border: 1px solid #aaa;
                color: #222;
            }
        }

        input {
            display: none;
        }
    }
</style>
