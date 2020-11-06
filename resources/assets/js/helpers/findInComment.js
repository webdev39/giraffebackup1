export default {
    /**
     * find Comment
     *
     * @param comment
     * @param findParam
     * @param callback
     * @returns {*}
     */
    findComment(comment, findParam, callback) {

        comment.replies.find(item => {
            if (item.id === findParam) {
                callback(item);
                return true;
            }

            this.findComment(item, findParam, callback);
        });
    },
    /**
     * Get count comments
     *
     * @param comment
     * @returns {*}
     */
    countComments(comment) {

        let count = 0;

        const getCount = (comment) => {

            if (!comment.replies || !comment.replies.length) {
                count += 1;
                return;
            }

            comment.replies.find(item => {
                if (item.replies && comment.replies.length) {
                    count += 1;
                    return getCount(item);
                }
            });
        }

        getCount(comment);
        return count;
    },
}