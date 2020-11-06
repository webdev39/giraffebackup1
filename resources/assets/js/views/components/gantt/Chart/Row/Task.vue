<!--
/**
 * @fileoverview Task component
 * @license MIT
 * @author Rafal Pospiech <neuronet.io@gmail.com>
 * @package GanttElastic
 */
-->
<template>
  <g
    :class="'gantt-elastic__chart-row-bar-wrapper gantt-elastic__chart-row-task-wrapper draggable' + task.id"
    :style="root.style('chart-row-bar-wrapper', 'chart-row-task-wrapper', task.style['chart-row-bar-wrapper'])"
  >
    <foreignObject
      class="gantt-elastic__chart-expander gantt-elastic__chart-expander--task"
      :style="root.style('chart-expander', 'chart-expander--task', task.style['chart-expander'])"
      :x="task.x - root.state.options.chart.expander.offset - root.state.options.chart.expander.size"
      :y="task.y + (root.state.options.row.height - root.state.options.chart.expander.size) / 2"
      :width="root.state.options.chart.expander.size"
      :height="root.state.options.chart.expander.size"
      v-if="displayExpander"
    >
      <expander :tasks="[task]" :options="root.state.options.chart.expander" type="chart"></expander>
    </foreignObject>
    <svg
      :class="'gantt-elastic__chart-row-bar gantt-elastic__chart-row-task'"
      :style="root.style('chart-row-bar', 'chart-row-task', task.style['chart-row-bar'])"
      :x="task.x"
      :y="task.y"
      :width="task.width"
      :height="task.height"
      :viewBox="`0 0 ${task.width} ${task.height}`"
      @click="emitEvent('click', $event)"
      @mouseenter="emitEvent('mouseenter', $event)"
      @mouseover="emitEvent('mouseover', $event)"
      @mouseout="mouseOut"
      @mouseup="mouseUp"
      @mousewheel="emitEvent('mousewheel', $event)"
      @mousemove="progressOver"
      @mousedown="progressDrag"
      @touchstart="emitEvent('touchstart', $event)"
      @touchmove="emitEvent('touchmove', $event)"
      @touchend="emitEvent('touchend', $event)"
      xmlns="http://www.w3.org/2000/svg"
      ref="progress"
    >
      <defs>
        <clipPath :id="clipPathId">
          <polygon :points="points"></polygon>
        </clipPath>
      </defs>
      <polygon
        class="gantt-elastic__chart-row-bar-polygon gantt-elastic__chart-row-task-polygon"
        :style="
          root.style(
            'chart-row-bar-polygon',
            'chart-row-task-polygon',
            task.style['base'],
            task.style['chart-row-bar-polygon']
          )
        "
        :points="points"
      ></polygon>
      <progress-bar :task="task" :clip-path="'url(#' + clipPathId + ')'"></progress-bar>
    </svg>
    <chart-text :task="task" v-if="root.state.options.chart.text.display"></chart-text>
  </g>
</template>

<script>
import ChartText from '../Text.vue';
import ProgressBar from '../ProgressBar.vue';
import Expander from '../../Expander.vue';
import taskMixin from './Task.mixin.js';

export default {
  name: 'Task',
  components: {
    ChartText,
    ProgressBar,
    Expander
  },
  inject: ['root'],
  props: ['task'],
  mixins: [taskMixin],
  data() {
    return {
      points: `0,0 ${this.task.width},0 ${this.task.width},${this.task.height} 0,${this.task.height}`,
      resizeLeftFlag: false,
      resizeRightFlag: false,
      dragFlag: false,
      prevX: -1,
      selector: '.draggable' + this.task.id,
      offset: document.querySelector(selector).getBoundingClientRect(),
      left: e.pageX - this.offset.left,
      right: e.pageX - this.offset.right,
      objSvg: document.querySelector(this.selector + ' .gantt-elastic__chart-row-bar.gantt-elastic__chart-row-task')
    };
  },
  mounted() {
    document.addEventListener('mouseup', (e) => {
      this.dragFlag = this.resizeLeftFlag = this.resizeRightFlag = false;
      this.emitEvent('mouseup', e);
    });
    document.addEventListener('mousemove', (e) => {
      let dx = this.prevX - (e.pageX);
      if(this.resizeLeftFlag || this.resizeRightFlag) {
        if (this.prevX == -1) {
          this.prevX = e.pageX;
          return false;
        }
      }

      if(this.resizeLeftFlag) {
        let newWidth = parseInt(this.objSvg.getAttribute('width')) + dx;
        if(newWidth) {
          this.points = `0,0 ${newWidth},0 ${newWidth},${this.task.height} 0,${this.task.height}`;
          this.objSvg.setAttribute('width', newWidth);
          this.objSvg.setAttribute('viewBox', `0 0 ${newWidth} ${this.task.height}`);
          e.width = newWidth;
          this.emitEvent('mousemove', e);
        }
      }
      else if(this.resizeRightFlag) {
        let newWidth = parseInt(this.objSvg.getAttribute('width')) - dx;
        if(newWidth) {
          this.points = `0,0 ${newWidth},0 ${newWidth},${this.task.height} 0,${this.task.height}`;
          this.objSvg.setAttribute('width', newWidth);
          this.objSvg.setAttribute('viewBox', `0 0 ${newWidth} ${this.task.height}`);
          e.width = newWidth;
          this.emitEvent('resizeright', e);
        }
      } else if(this.dragFlag) {
          this.emitEvent('mousemove', e);
      }
      this.prevX = e.pageX;
    });
  },
  watch: {},
  methods: {
    mouseOut(e) {
      this.dragFlag = this.resizeLeftFlag = this.resizeRightFlag = false;
      this.lastClientX = e.clientX;
    },
    mouseUp(e) {
      this.emitEvent('mouseup', e);
      this.dragFlag = this.resizeLeftFlag = this.resizeRightFlag = false;
    },
    progressDrag(e) {
      this.emitEvent('mousedown', e);
      if(this.left < 15) {
        this.resizeLeftFlag = true;
      } else if(this.left > this.objSvg.getAttribute('width') - 15) {
        this.resizeRightFlag = true;
      }
      else {
        this.dragFlag = true;
      }
    },
    progressOver(e) {
      let selectorRect = this.selector + ' ' + '.gantt-elastic__chart-row-progress-bar-pattern';
      if(this.left < 15 || this.left > this.objSvg.getAttribute('width') - 15) {
        document.querySelector(selectorRect).style.cursor = "col-resize";
      } else {
        document.querySelector(selectorRect).style.cursor = "pointer";
      }
    }
  },
  computed: {
    /**
     * Get clip path id
     *
     * @returns {string}
     */
    clipPathId() {
      return 'gantt-elastic__task-clip-path-' + this.task.id;
    },

    /**
     * Get points
     *
     * @returns {string}
     */
    getPoints() {
      return `0,0 ${this.task.width},0 ${this.task.width},${this.task.height} 0,${this.task.height}`;
    }
  }
};
</script>
