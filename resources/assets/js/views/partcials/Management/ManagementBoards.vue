<template>
    <div class="board-list">
        <table style="width: 100%;">
            <management-board-item v-for="(board,index) in boards" :board="board" :days="days" :group="group" :key="index" />
        </table>
    </div>

</template>

<script>

    import ManagmentBoardsItem  from '@views/partcials/Management/ManagmentBoardsItem'

    export default {
        props: {
            boards: {type: Array},
            days:   {type: Array},
            group:  {type: Object}
        },
        components: {
            'management-board-item':    ManagmentBoardsItem
        },
    }
</script>
