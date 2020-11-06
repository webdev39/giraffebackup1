<template>
    <div class="kanban-drag-collapse">
        <button  class="kanban-collapse-button" :class="open">
            <slot name="collapse-header"></slot>
            <!-- <i class="kanban-collapse-button__icon" :class="showIcon"></i> --> 
        </button>
        <div class="kanban-collapse-content" v-show="show">
            <slot name="collapse-content"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                show: true
            }
        },
        computed: {
            showIcon: function() {

                return {
                    'fa': true,
                    'fa-chevron-down': this.show,
                    'fa-chevron-up': !this.show
                };
            },
            open: function () {

                return {
                    'kanban-collapse-open': this.show
                };
            }
        },
        methods: {
            toggleShow(){
                this.show = !this.show
            }
        }
    }
</script>

<style lang="scss" scoped>
    .kanban-collapse-button{
        display: block;
        width:  100%;
        border: none;
        background: none;
        position: relative;
        padding: 0;
        text-align: left;
        background-color:transparent;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px; 
        cursor:auto
    }
    .kanban-collapse-button__icon{
        position: absolute;
        right: 15px;
        top: 13px;
    }
    .kanban-drag-collapse{
        height:auto;
        display: flex;
        display: -webkit-flex; 
        flex-direction: column;
        max-height: 100%;
        background: rgba(255,255,255,0.4);
        border-radius: 5px;
        -webkit-box-shadow: 0px 2px 5px 1px rgba(0, 0, 0, 0.4);
        box-shadow: 0px 2px 5px 1px rgba(0, 0, 0, 0.4);
    }
    .kanban-collapse-content{
        height: 100%;
        display: flex;
        flex:1;
        flex-direction: column;
        overflow-y:auto;
    }

    // @media (min-width: 576px) {
    //      .kanban-drag-collapse {
    //         height:auto;
    //  }
    // }

@supports (-webkit-overflow-scrolling: touch) {
  .kanban-drag-collapse {
  height:100%;
    }
}


</style>