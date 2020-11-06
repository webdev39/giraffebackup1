<template>
    <div class="place-popup" >
        <div class="place-popup-content">
            <textarea-autosize class="place-popup-textarea" type="text" v-model="content"></textarea-autosize>
        </div>
        <div class="place-popup-controller">
            <button class="place-popup-save button__icon" @click="$emit('save')">
                <i class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-check">
                        </use>
                    </svg>
                </i>
            </button>
            <button class="place-popup-cancel button__icon" @click="$emit('cancel')">
                <i class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                             xlink:href="#icon-close">
                        </use>
                    </svg>
                </i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                default: ''
            },
        },
        computed: {
            content: {
                get() {
                    return this.value
                },
                set(value) {
                    return this.$emit('input', value);
                }
            }
        },
    }
</script>

<style lang="scss">
    .place-popup{
        position: absolute;
        background-color: #fff;
        // padding: 20px;
        width: 245px;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16), 0 2px 3px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }
    .place-popup-content{
        position: relative;
        .place-popup-textarea{
            width: 100%;
            color: #383838;
            min-height: 36px;
            font-size: 16px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            border-color: #f3f3f3;
            padding: 10px;
        }
    }
    .place-popup-remove{
        position: absolute;
        right: 7px;
        top: 6px;
    }
    .place-popup-cancel,
    .place-popup-save{
        width: 26px;
        height: 26px;
        fill: #d5d5d5;
        padding: 5px;
        border-radius: 5px;
        .icon{
            display: flex;
            width: 100%;
            height: 100%;
        }
        &:hover{
            background: #f3f3f3;
        }
    }
    .place-popup-save{
        margin-right: 5px;
        fill:#7b94b3;
           &:hover{
            fill: #376aa7;
        }
    }
    .place-popup-cancel {
    fill:#e28888;
          &:hover{
            fill: #e04545;
        }
}
    .place-popup-controller{
        text-align: right;
        padding: 5px 7px;
    }

    @media (min-width: 360px) {
        .place-popup {
            width: 280px;
    }
}
    @media (min-width: 420px) {
        .place-popup {
            width: 340px;
    }
}

    @media (min-width: 576px) {
        .place-popup {
            width: 400px;
    }
}


</style>
