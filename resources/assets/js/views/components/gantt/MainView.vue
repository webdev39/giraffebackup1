<!--
/**
 * @fileoverview MainView component
 * @license MIT
 * @author Rafal Pospiech <neuronet.io@gmail.com>
 * @package GanttElastic
 */
-->
<template>
  <div class="gantt-elastic__main-view" :style="root.style('main-view')">
    <div
      class="gantt-elastic__main-container-wrapper"
      :style="root.style('main-container-wrapper', { height: root.state.options.height + 'px' })"
    >
      <div
        class="gantt-elastic__main-container"
        :style="
          root.style('main-container', {
            width: root.state.options.clientWidth + 'px',
            height: root.state.options.height + 'px'
          })
        "
        ref="mainView"
      >
        <div
          class="gantt-elastic__container"
          :style="root.style('container')"
          @mousemove="mouseMove"
          @mouseup="mouseUp"
        >
          <div
            ref="taskList"
            class="gantt-elastic__task-list-container"
            :style="
              root.style('task-list-container', {
                width: root.state.options.taskList.finalWidth + 'px',
                height: root.state.options.height + 'px'
              })
            "
            v-show="root.state.options.taskList.display">
            <task-list></task-list>
          </div>
          <div ref="gantt" class="gantt-elastic__task-list-container-right"
               :style="'width:'+(root.state.options.clientWidth - root.state.options.taskList.finalWidth)+'px;height:auto;'"></div>
        </div>
      </div>
      <div
        class="gantt-elastic__chart-scroll-container gantt-elastic__chart-scroll-container--vertical"
        :style="root.style('chart-scroll-container', 'chart-scroll-container--vertical', verticalStyle)"
        ref="chartScrollContainerVertical"
        @scroll="onVerticalScroll"
      >
        <div
          class="gantt-elastic__chart-scroll--vertical"
          :style="{ width: '1px', height: root.state.options.allVisibleTasksHeight + 'px' }"
        ></div>
      </div>
    </div>
    <div
      class="gantt-elastic__chart-scroll-container gantt-elastic__chart-scroll-container--horizontal"
      :style="root.style('chart-scroll-container', { marginLeft: getMarginLeft })"
      @scroll="onHorizontalScroll"
      ref="chartScrollContainerHorizontal"
    >
      <div
        class="gantt-elastic__chart-scroll--horizontal"
        :style="{ height: '1px', width: root.state.options.width + 'px' }"
      ></div>
    </div>
  </div>
</template>
<script>
import TaskList from './TaskList/TaskList.vue';
import Chart from './Chart/Chart.vue';
import {gantt} from 'dhtmlx-gantt';
import moment from "moment";
import {mapGetters} from "vuex";
import {secondsToHHMM} from "@helpers/time";

export default {
  name: 'MainView',
  components: {
    TaskList,
    Chart
  },
  inject: ['root'],
  data() {
    return {
      defs: '',
      mousePos: {
        x: 0,
        y: 0,
        movementX: 0,
        movementY: 0,
        lastX: 0,
        lastY: 0,
        positiveX: 0,
        positiveY: 0,
        currentX: 0,
        currentY: 0
      },
      ganttTasks: [],
    };
  },
  /**
   * Mounted
   */
  mounted() {
    this.viewBoxWidth = this.$el.clientWidth;
    this.root.state.refs.mainView = this.$refs.mainView;
    this.root.state.refs.chartContainer = this.$refs.chartContainer;
    this.root.state.refs.taskList = this.$refs.taskList;
    this.root.state.refs.chartScrollContainerHorizontal = this.$refs.chartScrollContainerHorizontal;
    this.root.state.refs.chartScrollContainerVertical = this.$refs.chartScrollContainerVertical;
    document.addEventListener('mouseup', this.chartMouseUp.bind(this));
    document.addEventListener('mousemove', this.chartMouseMove.bind(this));
    document.addEventListener('touchmove', this.chartMouseMove.bind(this));
    document.addEventListener('touchend', this.chartMouseUp.bind(this));

    this.$root.$on('modaldeadline-update', this.updateGantt);
    this.$root.$on('scale-update', this.updateGanttScale);
    this.$root.$on('add-task', this.addTaskToGantt);

    this.initGantt();
  },
  computed: {
    ...mapGetters({
      zoomConfig: 'task/getScales'
    }),
    /**
     * Get margin left
     *
     * @returns {string}
     */
    getMarginLeft() {
      if (!this.root.state.options.taskList.display) {
        return '0px';
      }
      return this.root.state.options.taskList.finalWidth + 'px';
    },

    /**
     * Get vertical style
     *
     * @returns {object}
     */
    verticalStyle() {
      return {
        width: this.root.state.options.scrollBarHeight + 'px',
        height: this.root.state.options.rowsHeight + 'px',
        'margin-top': this.root.state.options.calendar.height + this.root.state.options.calendar.gap + 'px'
      };
    },

    /**
     * Get view box
     *
     * @returns {string}
     */
    getViewBox() {
      if (this.root.state.options.clientWidth) {
        return `0 0 ${this.root.state.options.clientWidth - this.root.state.options.scrollBarHeight} ${
          this.root.state.options.height
        }`;
      }
      return `0 0 0 ${this.root.state.options.height}`;
    }
  },
  methods: {
    updateGanttScale(e) {
      let level = e.data.name;
      gantt.ext.zoom.setLevel(level);
    },
    updateGantt(e) {
      this.ganttTasks.find((element, index, array) => {
        if(e.data.id === element.id) {
          let dateToStr = gantt.date.date_to_str("%d %M");
          gantt.getTask(e.data.id).start_date = new Date(this.toLocalTime(e.data.planned_deadline, 'YYYY-MM-DD HH:mm:ss'));
          gantt.getTask(e.data.id).end_date = new Date(this.toLocalTime(e.data.deadline, 'YYYY-MM-DD HH:mm:ss'));
          gantt.updateTask(e.data.id);
        }
      });
    },
    addTaskToGantt(e) {
      let taskId = gantt.addTask({
        id: e.data.id,
        text: '',
        duration: 1,
        start_date: e.data.planned_deadline,
        end_date: e.data.deadline
      });
    },
    emitEvent(eventName, data) {
      this.root.$emit(`ganttmainview-${eventName}`, {data});
    },
    initGantt() {
      for(let index in this.root.visibleTasks) {
        let plannedDeadline = this.root.visibleTasks[index].planned_deadline;
        let deadline = this.root.visibleTasks[index].deadline;
        let task = {};
        task['id'] = this.root.visibleTasks[index].id;
        task['text'] = '';
        task['duration'] = 1;
        task['start_date'] = this.toLocalTime(plannedDeadline, 'YYYY-MM-DD HH:mm:ss');
        task['end_date'] = this.toLocalTime(deadline, 'YYYY-MM-DD HH:mm:ss');
        this.ganttTasks.push(task);
      }
      gantt.attachEvent("onAfterTaskUpdate", async (id, new_item) => {
        let plannedDeadline = this.toUTCTime(new_item.start_date, 'YYYY-MM-DD HH:mm:ss');
        let deadline = this.toUTCTime(new_item.end_date, 'YYYY-MM-DD HH:mm:ss');
        let softBudget = (moment(deadline) - moment(plannedDeadline)) / 1000;
        softBudget = secondsToHHMM(softBudget);
        this.emitEvent('updatetask', {
          task_id: new_item.id,
          planned_deadline: plannedDeadline,
          deadline: deadline,
          soft_budget: softBudget
        });
      });
      gantt.attachEvent("onTaskDrag", (id, mode, task, original, e) => {
        let startDate = moment(task.start_date);
        let endDate = moment(task.end_date);
        let state = gantt.getState();
        let hourStartAfter =  moment(state.min_date).add(1, 'h');
        let hourEndBefore =  moment(state.max_date).subtract(2, 'h');
        if(moment(startDate).diff(hourStartAfter, 'h') <= -1
                || moment(endDate).diff(hourEndBefore, 'h') >= 1) {
          gantt.render();
        }
      });
      gantt.config.date_format = "%Y-%m-%d %H:%i";
      gantt.config.columns = [];
      gantt.config.duration_unit = "hour";
      gantt.config.duration_step = 1;
      gantt.config.sort = 0;
      gantt.config.row_height = 36;
      gantt.config.layout = {
        css: "gantt_container",
        rows:[
          {
            cols: [
              {
                view: "grid",
                scrollX:"scrollHor",
                scrollY:"scrollVer"
              },
              { resizer: true, width: 1 },
              {
                view: "timeline",
                scrollX:"scrollHor",
                scrollY:"scrollVer"
              }
            ]},
          {
            view: "scrollbar",
            id:"scrollHor"
          }
        ]
      };
      gantt.init(this.$refs.gantt);
      gantt.ext.zoom.init(this.zoomConfig);
      gantt.parse({data: this.ganttTasks});
      gantt.ext.zoom.setLevel("hours");
      gantt.showLightbox = (id) => {
          this.$router.replace({query: {taskId: +id}});
      };
    },
    /**
     * Emit event when mouse is moving inside main view
     */
    mouseMove(event) {
      this.root.$emit('main-view-mousemove', event);
    },

    /**
     * Emit mouseup event inside main view
     */
    mouseUp(event) {
      this.root.$emit('main-view-mouseup', event);
    },

    /**
     * Horizontal scroll event handler
     */
    onHorizontalScroll(ev) {
      this.root.$emit('chart-scroll-horizontal', ev);
    },

    /**
     * Vertical scroll event handler
     */
    onVerticalScroll(ev) {
      this.root.$emit('chart-scroll-vertical', ev);
    },

    /**
     * Mouse wheel event handler
     */
    chartWheel(ev) {
      this.root.$emit('chart-wheel', ev);
    },

    /**
     * Chart mousedown event handler
     * Initiates drag scrolling mode
     */
    chartMouseDown(ev) {
      if (typeof ev.touches !== 'undefined') {
        this.mousePos.x = this.mousePos.lastX = ev.touches[0].screenX;
        this.mousePos.y = this.mousePos.lastY = ev.touches[0].screenY;
        this.mousePos.movementX = 0;
        this.mousePos.movementY = 0;
        this.mousePos.currentX = this.$refs.chartScrollContainerHorizontal.scrollLeft;
        this.mousePos.currentY = this.$refs.chartScrollContainerVertical.scrollTop;
      }
      this.root.state.options.scroll.scrolling = true;
    },

    /**
     * Chart mouseup event handler
     * Deactivates drag scrolling mode
     */
    chartMouseUp(ev) {
      this.root.state.options.scroll.scrolling = false;
    },

    /**
     * Chart mousemove event handler
     * When in drag scrolling mode this method calculate scroll movement
     */
    chartMouseMove(ev) {
      if (this.root.state.options.scroll.scrolling) {
        ev.preventDefault();
        ev.stopImmediatePropagation();
        ev.stopPropagation();
        const touch = typeof ev.touches !== 'undefined';
        let movementX, movementY;
        if (touch) {
          const screenX = ev.touches[0].screenX;
          const screenY = ev.touches[0].screenY;
          movementX = this.mousePos.x - screenX;
          movementY = this.mousePos.y - screenY;
          this.mousePos.lastX = screenX;
          this.mousePos.lastY = screenY;
        } else {
          movementX = ev.movementX;
          movementY = ev.movementY;
        }
        const horizontal = this.$refs.chartScrollContainerHorizontal;
        const vertical = this.$refs.chartScrollContainerVertical;
        let x = 0,
          y = 0;
        if (touch) {
          x = this.mousePos.currentX + movementX * this.root.state.options.scroll.dragXMoveMultiplier;
        } else {
          x = horizontal.scrollLeft - movementX * this.root.state.options.scroll.dragXMoveMultiplier;
        }
        horizontal.scrollLeft = x;
        if (touch) {
          y = this.mousePos.currentY + movementY * this.root.state.options.scroll.dragYMoveMultiplier;
        } else {
          y = vertical.scrollTop - movementY * this.root.state.options.scroll.dragYMoveMultiplier;
        }
        vertical.scrollTop = y;
      }
    }
  },

  /**
   * Before destroy event - clean up
   */
  beforeDestroy() {
    document.removeEventListener('mouseup', this.chartMouseUp);
    document.removeEventListener('mousemove', this.chartMouseMove);
    document.removeEventListener('touchmove', this.chartMouseMove);
    document.removeEventListener('touchend', this.chartMouseUp);

    this.$root.$off('modaldeadline-update', this.updateGantt);
    this.$root.$off('scale-update', this.updateGanttScale);
    this.$root.$off('add-task', this.addTaskToGantt);
  }
};
</script>
<style>
  @import "~dhtmlx-gantt/codebase/dhtmlxgantt.css";
</style>