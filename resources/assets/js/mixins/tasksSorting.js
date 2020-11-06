export default {
    methods: {
        async sortTasks(list, evt, type, curTask) {
            //   comment if sombebody complains that sorting doesn't function properly
            if (this.getSelectSortType.name !== 'sort_weight' || !list.length || type === 'removed') {
                return;
            }

            const { newIndex, oldIndex } = evt;
            let sort_weight = 0;
            let task = {};

            if (type && type === 'added') {
                sort_weight = this.getSortWeightForAdd(list, newIndex);
                task = curTask;
            } else {
                sort_weight = this.getSortWeightForMove(list, newIndex, oldIndex);
                task = list[oldIndex];
            }

            await this.$store.dispatch('task/setTaskSortWeight', {
                sort_weight,
                task,
            });

            /**
             * Copy store data
             * @type {Object}
             */
            let payload = Object.assign({}, task);

            /**
             * Update sort_weight
             * @type {Number}
             */
            payload = Object.assign(payload, { sort_weight });

            this.$store.dispatch('groups/setTaskSortWeight', payload);

            this.$api.task.updateTaskSortWeight(payload);
        },

        getSortWeightForMove(list, newIndex, oldIndex) {
            /**
             * Direction of moving task. If is move to top = true
             * @type {Boolean}
             */
            const isToTop = newIndex < oldIndex;

            /**
             * Weight of task with newIndex position
             * @type {Number}
             */
            let curIndexTaskWeight = list.length ? list[newIndex].sort_weight : 1000;

            /**
             * If move to top
             */
            if (isToTop) {
                const prevTask = list[newIndex - 1];
                /** 
                 * Check if has prev task
                 */
                if (prevTask) {
                    let prevTaskWeight = prevTask.sort_weight;
                    let newWight = (+prevTaskWeight - +curIndexTaskWeight) / (Math.random() + Math.PI);
                    return (+curIndexTaskWeight + +this.getRandomWight(newWight)).toFixed(6)
                }
                return (+curIndexTaskWeight + +this.getRandomWight(Math.PI)).toFixed(6);
            } else {
                const nextTask = list[newIndex + 1];
                /**
                 * Check if has next task
                 */
                if (nextTask) {
                    let nextTaskWeight = nextTask.sort_weight;
                    let newWight = (+curIndexTaskWeight - +nextTaskWeight) / (Math.random() + Math.PI);
                    return (+nextTaskWeight + +this.getRandomWight(newWight)).toFixed(6)
                }
                return (+curIndexTaskWeight - +this.getRandomWight(Math.PI)).toFixed(6);
            }
        },

        getRandomWight(maxWight) {
           return Math.random() * parseFloat(maxWight);
        },

        getSortWeightForAdd(list, newIndex) {
            /**
             * If task on new index exist 
             */
            if (list[newIndex]) {
                const curSortWeight = list[newIndex].sort_weight;
                let curIndexTaskWeight = list.length ? curSortWeight : 1000;
                const prevTask = list[newIndex - 1];

                if (prevTask) {
                    let prevTaskWeight = prevTask.sort_weight;
                    let newWight = (+prevTaskWeight - +curIndexTaskWeight) / (Math.random() + Math.PI);
                    return (+curIndexTaskWeight + +this.getRandomWight(newWight)).toFixed(6)
                }

                return (+curIndexTaskWeight + +this.getRandomWight(Math.PI)).toFixed(6);
            }
            const prevTaskWeight = list[newIndex - 1].sort_weight;
            return (+prevTaskWeight - +this.getRandomWight(Math.PI)).toFixed(6);
        }
    }
};
