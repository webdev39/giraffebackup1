<!--
/**
 * @fileoverview ItemColumn component
 * @license MIT
 * @author Rafal Pospiech <neuronet.io@gmail.com>
 * @package GanttElastic
 */
-->
<template>
  <div
    class="gantt-elastic__task-list-item-column"
    :style="
      root.style('task-list-item-column', column.style['task-list-item-column'], {
        width: column.finalWidth + 'px',
        height: column.height + 'px'
      })
    "
  >
    <div
      class="gantt-elastic__task-list-item-value-wrapper"
      :style="root.style('task-list-item-value-wrapper', column.style['task-list-item-value-wrapper'])"
    >
      <slot></slot>
      <div
        class="gantt-elastic__task-list-item-value-container"
        :style="root.style('task-list-item-value-container', column.style['task-list-item-value-container'])"
      >
        <div
          v-if="!html"
          class="gantt-elastic__task-list-item-value"
          :style="root.style('task-list-item-value', column.style['task-list-item-value'])"
          @click="emitEvent('click', $event)"
          @mouseenter="emitEvent('mouseenter', $event)"
          @mouseover="emitEvent('mouseover', $event)"
          @mouseout="emitEvent('mouseout', $event)"
          @mousemove="emitEvent('mousemove', $event)"
          @mousedown="emitEvent('mousedown', $event)"
          @mouseup="emitEvent('mouseup', $event)"
          @mousewheel="emitEvent('mousewheel', $event)"
          @touchstart="emitEvent('touchstart', $event)"
          @touchmove="emitEvent('touchmove', $event)"
          @touchend="emitEvent('touchend', $event)"
        >
          <template v-if="column.componentName === 'TaskItemDeadline'">
            <task-item-deadline
              :time="value"
              :task="task"
              :type="column.columnType"
            />
          </template>
          <template v-else-if="column.componentName === 'TaskItemTitle'">
            <task-unread-notification :task="task" class="gantt-elastic__task-list-item-notify" />
            <task-item-title :task="task" />
          </template>
          <template v-else>
            {{ value }}
          </template>
        </div>
        <div
          v-else
          class="gantt-elastic__task-list-item-value"
          :style="root.style('task-list-item-value', column.style['task-list-item-value'])"
          @click="emitEvent('click', $event)"
          @mouseenter="emitEvent('mouseenter', $event)"
          @mouseover="emitEvent('mouseover', $event)"
          @mouseout="emitEvent('mouseout', $event)"
          @mousemove="emitEvent('mousemove', $event)"
          @mousedown="emitEvent('mousedown', $event)"
          @mouseup="emitEvent('mouseup', $event)"
          @mousewheel="emitEvent('mousewheel', $event)"
          @touchstart="emitEvent('touchstart', $event)"
          @touchmove="emitEvent('touchmove', $event)"
          @touchend="emitEvent('touchend', $event)"
          v-html="value"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>

import TaskItemDeadline from '@views/elements/TaskItemDeadline/TaskItemDeadline'
import TaskItemTitle from '@views/elements/TaskItemTitle/TaskItemTitle'
import TaskUnreadNotification from '@views/partcials/TaskUndreadNotification/TaskUnreadNotification'


export default {
  name: 'ItemColumn',
  inject: ['root'],
  props: ['column', 'task'],
  data() {
    return {};
  },
  components: {
    TaskItemDeadline,
    TaskItemTitle,
    TaskUnreadNotification,
  },
  methods: {
    /**
     * Emit event
     *
     * @param {String} eventName
     * @param {Event} event
     */
    emitEvent(eventName, event) {
      if (typeof this.column.events !== 'undefined' && typeof this.column.events[eventName] === 'function') {
        this.column.events[eventName]({ event, data: this.task, column: this.column });
      }
      this.root.$emit(`taskList-${this.task.type}-${eventName}`, { event, data: this.task, column: this.column });
    }
  },
  computed: {
    /**
     * Should we display html or just text?
     *
     * @returns {boolean}
     */
    html() {
      if (typeof this.column.html !== 'undefined' && this.column.html === true) {
        return true;
      }
      return false;
    },

    /**
     * Get column value
     *
     * @returns {any|string}
     */
    value() {
      if (typeof this.column.value === 'function') {
        return this.column.value(this.task);
      }
      return this.task[this.column.value];
    }
  }
};
</script>

<style>
  .gantt-elastic__task-list-item-value-wrapper{
    position: relative;
  }
  .gantt-elastic .gantt-elastic__task-list-item-notify{
    position: absolute;
    z-index: 1;
    height: 13px;
    min-width: 13px;
    font-size: 10px;
    line-height: 10px;
    left: 4px;
    top: 3px;
  }
</style>
