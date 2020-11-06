<template>
    <div class="info-placeholder">
        <i class="info-placeholder__icon" >
            <svg class="icon" xmlns="http://www.w3.org/2000/svg">
                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                     xlink:href="#icon-question">
                </use>
            </svg>
        </i>
        <div class="info-placeholder__content">
            <div class="info-placeholder__content-inner">
                {{ content }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            content: {
                type: String,
                default: null
            }
        },
    }
</script>

<style lang="scss">
    .info-placeholder{
        position: absolute;
        &:hover{
            .info-placeholder__content{
                display: block;
            }
        }
    }
    .info-placeholder__icon{
        background-color: #fff;
        width: 16px;
        height: 16px;
        border-radius: 100%;
        border: 1px solid;
        display: flex;
        align-items: center;
        .icon{
            width: 100%;
            height: 100%;
        }
    }
    .info-placeholder__content{
        position: absolute;
        z-index: 1;
        display: none;
        width: 400px;
        top: 17px;
        right: 0;
        font-size: 14px;
        text-align: right;
    }
    .info-placeholder__content-inner{
        text-align: left;
        display: inline-block;
        padding: 0 10px;
        border-radius: 5px;
        background-color: #fff;
        -webkit-box-shadow: 0px 1px 3px 0 rgba(0, 0, 0, 0.5);
        box-shadow: 0px 1px 3px 0 rgba(0, 0, 0, 0.5);
    }
</style>