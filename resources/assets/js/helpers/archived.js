export default {
    /**
     * Change archiving status for child group objects
     *
     * @param group
     * @param status
     * @returns {number}
     */
    archivedGroup (group, status) {
        group.is_archive = status;
        group.boards = group.boards.map(board => {
            return this.archivedBoard(board, status);
        });

        return group;
    },

    /**
     * Change archiving status for child board objects
     *
     * @param board
     * @param status
     * @returns {number}
     */
    archivedBoard (board, status) {
        board.is_archive = status;
        board.tasks = board.tasks.map(task => {
            task.is_archive = status;

            return task;
        });

        return board
    },
}
