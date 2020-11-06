<template>
    <div :class="[
			'cv-wrapper',
			'locale-' + languageCode(displayLocale),
			'locale-' + displayLocale,
			'y' + periodStart.getFullYear(),
			'm' + paddedMonth(periodStart),
			'period-' + displayPeriodUom,
			'periodCount-' + displayPeriodCount,
			{
				past: isPastMonth(periodStart),
				future: isFutureMonth(periodStart),
				noIntl: !supportsIntl,
			}]">
        <div class="cv-calendar">
            <slot name="above-header"></slot>
            <slot :header-props="headerProps" name="header">
                <calendar-view-header
                        :header-props="headerProps"
                        @input="onChangeDate">
                    <template slot="label">{{ periodLabel }}</template>
                </calendar-view-header>
            </slot>

            <template v-if="!isDisplayDays">
                <div class="cv-header-days" >
                    <template v-for="(label, index) in weekdayNames">
                        <slot :index="`${index}-dow`" :label="label" name="dayHeader">
                            <div :key="`${index}-dow`" :class="'dow'+index" class="cv-header-day">{{ label }}</div>
                        </slot>
                    </template>
                </div>
                <div class="cv-weeks">
                    <div v-for="(weekStart, weekIndex) in weeksOfPeriod"
                         :key="`${weekIndex}-week`"
                         :class="['cv-week', 'week' + (weekIndex+1), 'ws' + isoYearMonthDay(weekStart)]">

                        <div v-for="(day, dayIndex) in daysOfWeek(weekStart)"
                             :key="`${dayIndex}-day`"
                             :class="[
                            'cv-day',
                            'dow' + day.getDay(),
                            'd' + isoYearMonthDay(day),
                            'd' + isoMonthDay(day),
                            'd' + paddedDay(day),
                            'instance' + instanceOfMonth(day),
                            {
                                outsideOfMonth: !isSameMonth(day, defaultedShowDate),
                                today: isSameDate(day, today()),
                                past: isInPast(day),
                                future: isInFuture(day),
                                last: isLastDayOfMonth(day),
                                lastInstance: isLastInstanceOfMonth(day)
                            },
                            ...((dateClasses && dateClasses[isoYearMonthDay(day)]) || null)
                        ]"
                             @click="onClickDay(day)"
                             @drop.prevent="onDrop(day, $event)"
                             @dragover.prevent="onDragOver(day)"
                             @dragenter.prevent="onDragEnter(day, $event)"
                             @dragleave.prevent="onDragLeave(day, $event)">
                            <div class="cv-day-number">{{ day.getDate() }}</div>
                            <slot :day="day" name="dayContent" />
                        </div>
                        <template v-for="(e,index) in getWeekEvents(weekStart)">
                            <slot :event="e" :weekStartDate="weekStart" :top="getEventTop(e)" name="event">
                                <drag :transferData="[e.id, e.originalEvent]">
                                    <div
                                            :key="e.id"
                                            :draggable="enableDragDrop && !e.originalEvent.copy_task"
                                            :class="e.classes"
                                            :title="e.title"
                                            :style="'top:' + getEventTop(e)"
                                            class="cv-event"
                                            @dragstart="onDragStart(e, $event)"
                                            @click.stop="onClickEvent(e)" >
                                        <span class="startTime">{{ e.startDate | getHour }}</span>
                                        <span class="endTime">{{ e.endDate | getHour }}</span>
                                        {{ e.title }}
                                    </div>
                                </drag>
                            </slot>
                        </template>
                    </div>
                </div>
            </template>

            <div class="cv-content" :class="{ 'task-drag': isTaskDrag }" v-else>
                <div class="cv-header-hours">
                    <div class="cv-header-hour-all-day">All day</div>
                    <div class="cv-header-hour" v-for="hour in hours">{{ hour }}</div>
                </div>

                <div style="width: 100%;">
                    <div class="cv-header-days">
                        <template v-for="(label, index) in weekdayNames">
                            <slot :index="`${index}-dow`" :label="label" name="dayHeader">
                                <div :key="`${index}-dow`" :class="'dow'+index" class="cv-header-day">{{ label }}</div>
                            </slot>
                        </template>
                    </div>

                    <div class="cv-weeks" style="border-bottom: none;">
                        <div v-for="(weekStart, weekIndex) in createWeek(1)" class="cv-week">
                            <div v-for="(day, dayIndex) in daysOfWeek(weekStart)"
                                 :key="`${dayIndex}-day`"
                                 :class="[
                                'cv-day',
                                'dow' + day.getDay(),
                                'd' + isoYearMonthDay(day),
                                'd' + isoMonthDay(day),
                                'd' + paddedDay(day),
                                'instance' + instanceOfMonth(day),
                                {
                                    outsideOfMonth: !isSameMonth(day, defaultedShowDate),
                                    today: isSameDate(day, today()),
                                    past: isInPast(day),
                                    future: isInFuture(day),
                                    last: isLastDayOfMonth(day),
                                    lastInstance: isLastInstanceOfMonth(day)
                                },
                                ...((dateClasses && dateClasses[isoYearMonthDay(day)]) || null)
                            ]"
                                 @click="onClickDay(day)"
                                 @drop.prevent="onDrop(day, $event, 'allDay')"
                                 @dragover.prevent="onDragOver(day)"
                                 @dragenter.prevent="onDragEnter(day, $event)"
                                 @dragleave.prevent="onDragLeave(day, $event)">
                                <div class="cv-day-number">{{ day.getDate() }}</div>
                                <slot :day="day" name="dayContent" />
                            </div>

                            <template v-for="(e,index) in getWeekEvents(weekStart, {allDay: true})">
                                <slot :event="e" :weekStartDate="weekStart" :top="getEventTop(e)" name="event">
                                    <drag :transferData="[e.id, e.originalEvent]">
                                        <div
                                                :key="e.id"
                                                :draggable="enableDragDrop && !e.originalEvent.copy_task"
                                                :class="e.classes"
                                                :title="e.title"
                                                :style="'top:' + getEventTop(e)"
                                                class="cv-event"
                                                @dragstart="onDragStart(e, $event)"
                                                @dragend="onDragEnd"
                                                @click.stop="onClickEvent(e)" >
                                            <span class="startTime">{{ e.startDate | getHour }}</span>
                                            <span class="endTime">{{ e.endDate | getHour }}</span>
                                            {{ e.title }}
                                        </div>
                                    </drag>
                                </slot>
                            </template>
                        </div>
                    </div>


                    <div class="cv-weeks cv-weeks_day">
                        <div v-for="(weekStart, weekIndex) in weeksOfPeriod"
                             :key="`${weekIndex}-week`"
                             :class="['cv-week', 'week' + (weekIndex+1), 'ws' + isoYearMonthDay(weekStart)]">
                            <div v-for="(day, dayIndex) in daysOfWeek(weekStart)"
                                     :key="`${dayIndex}-day`"
                                     :class="[
                                            'cv-day',
                                            'dow' + day.getDay(),
                                            'd' + isoYearMonthDay(day),
                                            'd' + isoMonthDay(day),
                                            'd' + paddedDay(day),
                                            'instance' + instanceOfMonth(day),
                                            {
                                                outsideOfMonth: !isSameMonth(day, defaultedShowDate),
                                                today: isSameDate(day, today()),
                                                past: isInPast(day),
                                                future: isInFuture(day),
                                                last: isLastDayOfMonth(day),
                                                lastInstance: isLastInstanceOfMonth(day)
                                            },
                                            ...((dateClasses && dateClasses[isoYearMonthDay(day)]) || null)
                                        ]"
                                     @click="onClickDay(day)"
                                     @drop.prevent="onDrop(day, $event, 'days')"
                                     @dragover.prevent="onDragOver(day)"
                                     @dragenter.prevent="onDragEnter(day, $event)"
                                     @dragleave.prevent="onDragLeave(day, $event)">
                                    <slot :day="day" name="dayContent" />
                            </div>

                            <template v-for="(e,index) in getDayEvents(weekStart)">
                                <slot :event="e" :weekStartDate="weekStart" :top="getEventTop(e)" name="event">
                                    <drag :transferData="[e.id, e.originalEvent]">
                                        <div
                                                :key="e.id"
                                                :draggable="enableDragDrop && !e.originalEvent.copy_task"
                                                :class="e.classes"
                                                :title="e.title"
                                                :style="{
                                                    top:    getEventTop(e, true),
                                                    height: getEventHeight(e),
                                                    width:  getEventWidth(e),
                                                    left:   getEventOffset(e)
                                                }"
                                                class="cv-event"
                                                @dragstart="onDragStart(e, $event)"
                                                @dragend="onDragEnd"
                                                @click.stop="onClickEvent(e)" >
                                            {{ e.title }}
                                        </div>
                                    </drag>
                                </slot>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Drag}               from 'vue-drag-drop'
    import moment               from 'moment'

    import CalendarMathMixin    from "@mixins/CalendarMathMixin"
    import CalendarViewHeader   from "./CalendarViewHeader"

    const hours = [
        "00:00",
        "01:00",
        "02:00",
        "03:00",
        "04:00",
        "05:00",
        "06:00",
        "07:00",
        "08:00",
        "09:00",
        "10:00",
        "11:00",
        "12:00",
        "13:00",
        "14:00",
        "15:00",
        "16:00",
        "17:00",
        "18:00",
        "19:00",
        "20:00",
        "21:00",
        "22:00",
        "23:00",
    ];

    export default {
        name: "CalendarView",

        components: {
            CalendarViewHeader,
            Drag
        },

        mixins: [CalendarMathMixin],

        props: {
            showDate: { type: Date, default: () => undefined },
            displayPeriodUom: { type: String, default: () => "month" },
            displayPeriodCount: { type: Number, default: () => 1 },
            locale: { type: String, default: () => undefined },
            monthNameFormat: { type: String, default: () => "long" },
            weekdayNameFormat: { type: String, default: () => "short" },
            showEventTimes: { type: Boolean, default: () => false },
            timeFormatOptions: { type: Object, default: () => {} },
            disablePast: { type: Boolean, default: () => false },
            disableFuture: { type: Boolean, default: () => false },
            enableDragDrop: { type: Boolean, default: () => true },
            startingDayOfWeek: { type: Number, default: () => 0 },
            events: { type: Array, default: () => [] },
            dateClasses: { type: Object, default: () => {} },
            eventTop: { type: String, default: () => "1.4em" },
            eventContentHeight: { type: String, default: () => "1.4em" },
            eventBorderHeight: { type: String, default: () => "2px" },
        },

        data: () => ({
            currentDragEvent: null,
            hours: hours,
            isTaskDrag: false
        }),
        created () {
            this.$event.$on('task-calendar-drag-start', this.onDragStart);
            this.$event.$on('task-calendar-drag-end', this.onDragEnd);
        },
        beforeDestroy () {
            this.$event.$off('task-calendar-drag-start');
            this.$event.$off('task-calendar-drag-end');
        },
        computed: {
            isDisplayDays() {
                return this.displayPeriodUom === 'days'
            },
            /*
            Props cannot default to computed/method returns, so create defaulted version of this
            property and use it rather than the bare prop (Vue Issue #6013).
            */
            displayLocale() {
                return this.locale || this.getDefaultBrowserLocale()
            },

            /*
            ShowDate, but defaulted to today. Needed both for periodStart below and for the
            "outside of month" class.
            */
            defaultedShowDate() {
                return this.showDate || this.today()
            },

            /*
            Given the showDate, defaulted to today, computes the beginning and end of the period
            that the date falls within.
            */
            periodStart() {
                return this.beginningOfPeriod(
                    this.defaultedShowDate,
                    this.displayPeriodUom,
                    this.startingDayOfWeek
                )
            },

            periodEnd() {
                return this.addDays(
                    this.incrementPeriod(
                        this.periodStart,
                        this.displayPeriodUom,
                        this.displayPeriodCount
                    ),
                    -1
                )
            },

            /*
            For month and year views, the first and last dates displayed in the grid may not
            be the same as the intended period, since the period may not start and stop evenly
            on the starting day of the week.
            */
            displayFirstDate() {
                const fistDate = this.beginningOfWeek(this.periodStart, this.startingDayOfWeek);
                this.$emit("show-first-date", fistDate);

                return fistDate
            },

            displayLastDate() {
                const lastDate = this.endOfWeek(this.periodEnd, this.startingDayOfWeek)
                this.$emit("show-last-date", lastDate);

                return lastDate
            },
            /*
            Create an array of dates, where each date represents the beginning of a week that
            should be rendered in the view for the current period.
            */
            weeksOfPeriod() {
                // Returns an array of object representing the date of the beginning of each week
                // included in the view.

                if (this.isDisplayDays) {
                    return Array(24)
                        .fill()
                        .map((_, i) => {
                            return new Date(
                                this.displayFirstDate.getFullYear(),
                                this.displayFirstDate.getMonth(),
                                this.displayFirstDate.getDate(),
                                i
                            );
                        })
                }

                const numWeeks = Math.floor(
                    (this.dayDiff(this.displayFirstDate, this.displayLastDate) + 1) / 7
                )

                return this.createWeek(numWeeks);
            },

            // Cache the names based on current locale and format settings
            monthNames() {
                return this.getFormattedMonthNames(
                    this.displayLocale,
                    this.monthNameFormat
                )
            },
            weekdayNames() {
                return this.getFormattedWeekdayNames(
                    this.displayLocale,
                    this.weekdayNameFormat,
                    this.startingDayOfWeek
                )
            },

            // Ensure all event properties have suitable default
            fixedEvents() {
                return this.events.map(this.normalizeEvent)
            },

            // Creates the HTML to render the date range for the calendar header.
            periodLabel() {
                return this.formattedPeriod(
                    this.periodStart,
                    this.periodEnd,
                    this.displayPeriodUom,
                    this.monthNames
                )
            },

            headerProps() {
                return {
                    // Dates for UI navigation
                    previousYear: this.getIncrementedPeriod(-12),
                    previousPeriod: this.getIncrementedPeriod(-1),
                    nextPeriod: this.getIncrementedPeriod(1),
                    nextYear: this.getIncrementedPeriod(12),
                    currentPeriod: this.beginningOfPeriod(
                        this.today(),
                        this.displayPeriodUom,
                        this.startingDayOfWeek
                    ),
                    // Dates for header display
                    periodStart: this.periodStart,
                    periodEnd: this.periodEnd,
                    // Extra information that could be useful to a custom header
                    displayLocale: this.displayLocale,
                    displayFirstDate: this.displayFirstDate,
                    displayLastDate: this.displayLastDate,
                    monthNames: this.monthNames,
                    fixedEvents: this.fixedEvents,
                }
            },

        },

        filters: {
            getHour: function(date){
                return moment(date).format('HH:mm');
            }
        },

        methods: {
            createWeek(numWeeks) {
                return Array(numWeeks)
                    .fill()
                    .map((_, i) => this.addDays(this.displayFirstDate, i * 7))
            },
            // ******************************
            // UI Events
            // ******************************

            onClickDay(day) {
                if (this.disablePast && this.isInPast(day)) return
                if (this.disableFuture && this.isInFuture(day)) return
                this.$emit("click-date", day)
            },

            onClickEvent(e, day) {
                this.$emit("click-event", e, day)
            },

            onChangeDate(d) {
                this.$emit("show-date-change", d)
            },

            // ******************************
            // Date Periods
            // ******************************

            /*
            Returns a date for the current display date moved forward or backward by a given
            number of the current display units. Returns null if said move would result in a
            disallowed display period.
            */
            getIncrementedPeriod(count) {
                const newStartDate = this.incrementPeriod(
                    this.periodStart,
                    this.displayPeriodUom,
                    count
                )
                const newEndDate = this.incrementPeriod(
                    newStartDate,
                    this.displayPeriodUom,
                    this.displayPeriodCount
                )
                if (this.disablePast && newEndDate <= this.today()) return null
                if (this.disableFuture && newStartDate > this.today()) return null
                return newStartDate
            },

            // ******************************
            // Drag and drop events
            // ******************************

            onDragStart(calendarEvent, windowEvent) {

                this.isTaskDrag = true;

                if (!this.enableDragDrop || (calendarEvent.originalEvent && calendarEvent.originalEvent.copy_task)) return false;
                // Not using dataTransfer.setData to store the event ID because it (a) doesn't allow access to the data being
                // dragged during dragover, dragenter, and dragleave events, and because storing an ID requires an unnecessary
                // lookup. This does limit the drop zones to areas within this instance of this component.

                if(!calendarEvent.startDate && !calendarEvent.endDate) {
                    if (calendarEvent.hard_budget !== '00:00' && calendarEvent.hard_budget) {
                        calendarEvent.endDate = this.getTime('hard_budget', calendarEvent);
                    }
                    else if (calendarEvent.soft_budget !== '00:00' && calendarEvent.soft_budget) {
                        calendarEvent.endDate = this.getTime('soft_budget', calendarEvent);
                    } else {
                        calendarEvent.endDate = calendarEvent.planned_deadline || null;
                    }

                    calendarEvent.startDate = calendarEvent.planned_deadline || null;

                    calendarEvent.title = calendarEvent.name;

                    //calendarEvent = this.normalizeEvent(calendarEvent);
                    calendarEvent = Object.assign({}, calendarEvent, this.normalizeEvent(calendarEvent))

                }

                this.currentDragEvent = calendarEvent;
                // Firefox and possibly other browsers require dataTransfer to be set, even if the value is not used. IE11
                // requires that the first argument be exactly "text" (not "text/plain", etc.).

                if(windowEvent){
                    windowEvent.dataTransfer.setData("text", "foo");
                }

                this.$emit("drag-start", calendarEvent);
                return true
            },
            onDragEnd() {
                this.isTaskDrag = false;
            },
            getTime(typeBudget, calendarEvent) {

                if (!calendarEvent.planned_deadline) {
                    return null
                }

                let budgetTime = calendarEvent[typeBudget].split(':');

                return moment(calendarEvent.planned_deadline)
                    .add(Number(budgetTime[0]), 'hours')
                    .add(Number(budgetTime[1]), 'minutes')
                    .format('YYYY-MM-DD HH:mm:ss');
            },

            handleDragEvent(bubbleEventName, bubbleParam, typeChange = null) {
                if (!this.enableDragDrop) return false
                if (!this.currentDragEvent) {
                    // shouldn't happen
                    // If current drag event is not set, check if user has set its own slot for events
                    if (!this.$scopedSlots["event"]) return false
                }
                this.$emit(bubbleEventName, this.currentDragEvent, bubbleParam, typeChange);
                return true
            },

            onDragOver(day) {
                this.handleDragEvent("drag-over-date", day)
            },

            onDragEnter(day, windowEvent) {
                if (!this.handleDragEvent("drag-enter-date", day)) return;
                windowEvent.target.classList.add("draghover")
            },

            onDragLeave(day, windowEvent) {
                if (!this.handleDragEvent("drag-leave-date", day)) return
                windowEvent.target.classList.remove("draghover")
            },

            onDrop(day, windowEvent, typeChange) {
                if (!this.handleDragEvent("drop-on-date", day, typeChange)) return
                windowEvent.target.classList.remove("draghover")
            },

            // ******************************
            // Calendar Events
            // ******************************

            findAndSortEventsInWeek(weekStart, options) {
                // Return a list of events that INCLUDE any day of a week starting on a
                // particular day. Sorted so the events that start earlier are always
                // shown first.
                options = Object.assign({days: false, allDay: false}, options);

                let events = this.fixedEvents
                    .filter(
                        event => {
                            if (event.startDate < this.addDays(weekStart, 7) &&
                                event.endDate >= weekStart) {

                                if (options.days) {
                                    if (event.startDate.getHours() === weekStart.getHours()
                                        && this.minutesDiff(event.endDate, event.startDate) <= 1438
                                        && this.dayDiff(event.startDate, event.endDate) < 1 ) {
                                        return true
                                    }
                                    return false
                                }

                                if (options.allDay) {
                                    if (this.minutesDiff(event.endDate, event.startDate) > 1438
                                        || this.dayDiff(event.startDate, event.endDate) > 0) {
                                        return true
                                    }

                                    return false
                                }

                                return true
                            }
                        }
                    )
                    .sort((a, b) => {
                        if (a.startDate < b.startDate) return -1
                        if (b.startDate < a.startDate) return 1
                        if (a.endDate > b.endDate) return -1
                        if (b.endDate > a.endDate) return 1
                        return a.id < b.id ? -1 : 1
                    })

                return events
            },

            getDayEvents(weekStart) {
                const events = this.findAndSortEventsInWeek(weekStart, {days: true});
                const results = [];
                const eventRows = [[], [], [], [], [], [], []];

                for (let k = 0; k < events.length; k++) {
                    const ep = Object.assign({}, events[k], {
                        classes: [...events[k].classes],
                        eventRow: 0,
                        width: 0,
                        left: 0,
                        events: 0,
                        span: 0
                    })

                    if (events[k].originalEvent.copy_task) {
                        ep.classes.push('copy');
                    }

                    const continued = ep.startDate < weekStart;
                    const startOffset = continued ? 0 : this.dayDiff(weekStart, ep.startDate);
                    const span = Math.min( 7 - startOffset, this.dayDiff(this.addDays(weekStart, startOffset), ep.endDate) + 1 );

                    for (let d = 0; d < 7; d++) {
                        if (d === startOffset) {
                            eventRows[d].push(ep);
                        }
                    }

                    ep.span = span;
                    ep.classes.push(`offset${startOffset}`)
                    ep.classes.push(`span${span}`)
                }

                eventRows.forEach((row,index) => {
                    if (row) {
                        row.forEach((event, indexEvent) => {
                            event.events = row.length;
                            event.width = 100 * event.span / event.events / 7;

                            let part = 0;

                            if (event.events > 1) {
                                part = 100 / event.events * indexEvent;
                            }

                            event.left = (100 * index + part) / 7;

                            results.push(event);
                        })
                    }
                })

                return results
            },

            getWeekEvents(weekStart, options) {
                // Return a list of events that CONTAIN the week starting on a day.
                // Sorted so the events that start earlier are always shown first.
                const events = this.findAndSortEventsInWeek(weekStart, options);
                const results = []
                const eventRows = [[], [], [], [], [], [], []];
                for (let i = 0; i < events.length; i++) {
                    const ep = Object.assign({}, events[i], {
                        classes: [...events[i].classes],
                        eventRow: 0,
                    })

                    if (events[i].originalEvent.copy_task) {
                        ep.classes.push('copy');
                    }

                    const continued = ep.startDate < weekStart
                    const startOffset = continued
                        ? 0
                        : this.dayDiff(weekStart, ep.startDate)
                    const span = Math.min(
                        7 - startOffset,
                        this.dayDiff(this.addDays(weekStart, startOffset), ep.endDate) + 1
                    )
                    if (continued) ep.classes.push("continued")
                    if (this.dayDiff(weekStart, ep.endDate) > 6)
                        ep.classes.push("toBeContinued")
                    if (ep.url) ep.classes.push("hasUrl")
                    for (let d = 0; d < 7; d++) {
                        if (d === startOffset) {
                            let s = 0
                            while (eventRows[d][s]) s++
                            ep.eventRow = s
                            eventRows[d][s] = true
                        } else if (d < startOffset + span) {
                            eventRows[d][ep.eventRow] = true
                        }
                    }
                    ep.classes.push(`offset${startOffset}`)
                    ep.classes.push(`span${span}`)
                    results.push(ep)
                }
                return results
            },

            /*
            Creates the HTML to prefix the event title showing the event's start and/or
            end time. Midnight is not displayed.
            */
            getFormattedTimeRange(e) {
                const startTime = this.formattedTime(
                    e.startDate,
                    this.displayLocale,
                    this.timeFormatOptions
                )
                let endTime = ""
                if (!this.isSameDateTime(e.startDate, e.endDate)) {
                    endTime = this.formattedTime(
                        e.endDate,
                        this.displayLocale,
                        this.timeFormatOptions
                    )
                }
                return (
                    (startTime !== ""
                        ? `<span class="startTime">${startTime}</span>`
                        : "") +
                    (endTime !== "" ? `<span class="endTime">${endTime}</span>` : "")
                )
            },

            getEventTitle(e) {
                if (!this.showEventTimes) return e.title
                return this.getFormattedTimeRange(e) + " " + e.title
            },

            getEventTop(e, isDays) {
                let eventTop = this.eventTop;

                if (isDays) {
                    eventTop  = 0
                }

                // Compute the top position of the event based on its assigned row within the given week.
                const r = e.eventRow
                const h = this.eventContentHeight
                const b = this.eventBorderHeight
                return `calc(${eventTop} + ${r}*${h} + ${r}*${b})`
            },
            getEventHeight(e) {
                let diffHours = this.hoursDiff(e.endDate, e.startDate);

                if (diffHours) {
                    return `calc(${diffHours * 100}% - 1px)`
                }

                return 'auto'
            },
            getEventWidth(e) {
                return `calc(${e.width}% - 0.05em)`
            },
            getEventOffset(e) {
                return e.left + '%'
            }
        },
    }
</script>
<!--

The CSS below represents only the CSS required for proper rendering (positioning, etc.) and
minimalist default borders and colors. Special-day colors, holiday emoji, event colors,
and decorations like border-radius should be part of a theme. Styles related to the default
header are in the CalendarViewHeader component.

-->
<style lang="scss">
    .task-list-item{
        background-color: #fff;
    }

    /* Position/Flex */

    /* Make the calendar flex vertically */
    .cv-wrapper {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        height: 100%;
        min-height: 100%;
        max-height: 100%;
        /*overflow-x: hidden;
        overflow-y: hidden;*/
    }

    .cv-wrapper,
    .cv-wrapper div {
        box-sizing: border-box;
        line-height: 1em;
        font-size: 1em;
    }


    .cv-header-days-event{
        display: flex;
        flex-grow: 1;
        flex-shrink: 0;
        flex-basis: 0;
        flex-flow: row nowrap;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-width: 1px 1px 0 0;
    }

    .cv-header-days {
        display: flex;
        flex-grow: 0;
        flex-shrink: 0;
        flex-basis: auto;
        flex-flow: row nowrap;
        border-width: 0 0 0 1px;
    }

    .cv-header-day {
        display: flex;
        flex-grow: 1;
        flex-shrink: 0;
        flex-basis: 0;
        flex-flow: row nowrap;
        align-items: center;
        justify-content: center;
        text-align: center;
        border-width: 1px 1px 0 0;
    }

    /* The calendar grid should take up the remaining vertical space */
    .cv-weeks {
        display: flex;
        flex-grow: 1;
        flex-shrink: 1;
        flex-basis: auto;
        flex-flow: column nowrap;
        border-width: 0 0 1px 1px;

        /* Allow grid to scroll if there are too may weeks to fit in the view */
        overflow-y: auto;
        -ms-overflow-style: none;
    }



    /*.cv-weeks_one-week{*/
        /*flex-flow: initial;*/
    /*}*/

    /* Use flex basis of 0 on week row so all weeks will be same height regardless of content */
    .cv-week {
        display: flex;
        /* Shorthand flex: 1 1 0 not supported by IE11 */
        flex-grow: 1;
        flex-shrink: 0;
        flex-basis: 0;
        flex-flow: row nowrap;
        min-height: 3em;
        border-width: 0;

        /* Allow week events to scroll if they are too tall */
        position: relative;
        width: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        -ms-overflow-style: none;
    }

    .cv-day {
        display: flex;
        /* Shorthand flex: 1 1 0 not supported by IE11 */
        flex-grow: 1;
        flex-shrink: 0;
        flex-basis: 0;
        position: relative; /* Fallback for IE11, which doesn't support sticky */
        position: sticky; /* When week's events are scrolled, keep the day content fixed */
        top: 0;
        border-width: 1px 1px 0 0;
    }

    .cv-day-number {
        position: absolute;
        right: 0;
        pointer-events: none;
    }

    .cv-event {
        position: absolute;
        white-space: nowrap;
        overflow: hidden;
        background-color: #f7f7f7;
        border-width: 1px;
    }

    .cv-event.copy{
        opacity: 0.6;
        background-color: #fffaaf;
        border-color: #ffdf86;
    }

    /* Colors */

    .cv-header-days,
    .cv-header-day,
    .cv-weeks,
    .cv-week,
    .cv-day,
    .cv-event {
        border-style: solid;
        border-color: #ddd;
    }

    /* Event Times */
    .cv-event .endTime::before {
        content: " - ";
    }

    /* Internal Metrics */
    .cv-header-day,
    .cv-day-number,
    .cv-event {
        padding: 0.2em;
    }

    /* Allows emoji icons or labels (such as holidays) to be added more easily to specific dates by having the margin set already. */
    .cv-day-number::before {
        margin-right: 0.5em;
    }

    .cv-event.offset0 {
        left: 0;
    }

    .cv-event.offset1 {
        left: calc((100% / 7));
    }

    .cv-event.offset2 {
        left: calc((200% / 7));
    }

    .cv-event.offset3 {
        left: calc((300% / 7));
    }

    .cv-event.offset4 {
        left: calc((400% / 7));
    }

    .cv-event.offset5 {
        left: calc((500% / 7));
    }

    .cv-event.offset6 {
        left: calc((600% / 7));
    }

    /* Metrics for events spanning dates */

    .cv-event.span1 {
        width: calc((100% / 7) - 0.05em);
    }

    .cv-event.span2 {
        width: calc((200% / 7) - 0.05em);
    }

    .cv-event.span3 {
        width: calc((300% / 7) - 0.05em);
        text-align: center;
    }

    .cv-event.span4 {
        width: calc((400% / 7) - 0.05em);
        text-align: center;
    }

    .cv-event.span5 {
        width: calc((500% / 7) - 0.05em);
        text-align: center;
    }

    .cv-event.span6 {
        width: calc((600% / 7) - 0.05em);
        text-align: center;
    }

    .cv-event.span7 {
        width: calc((700% / 7) - 0.05em);
        text-align: center;
    }

    .cv-content{
        display: flex;
        .cv-event{
            position: absolute;
            margin-top: 1px;
            z-index: 2;
            border: 1px solid #fff;
            border-radius: 5px;
            /*white-space: inherit;*/
            &:hover{
                z-index: 4;
            }
        }
    }

    /* todo remove*/
    .theme-ocam .cv-content .cv-day.today {
        background-color: transparent;
    }

    .cv-weeks_day{
        .cv-week {
            overflow: visible;
        }
    }
    .theme-ocam .task-drag{
         .cv-day {
             z-index: 3;
         }
    }

    .cv-header-hour-all-day{
        height: 100px;
        border-bottom: 1px solid #ddd;
        padding-right: 5px;
    }

    .cv-header-hours{
        width: 60px;
        height: auto;
        border-left: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        background-color: #fafafa;
        border-top: 1px solid #ddd;
        padding-top: 20px;
        text-align: right;
    }

    .cv-header-hour{
        min-height: 100px;
        padding-top: 4px;
        padding-right: 5px;
    }

    .cv-hours{
        width: 100%;
    }

    .cv-hour{
        height: 100px;
        width: 100%;
        border-bottom: 1px solid #dddddd;
    }

    /* Hide scrollbars for the grid and the week */
    /*.cv-weeks::-webkit-scrollbar,
    .cv-week::-webkit-scrollbar {
        width: 0; !* remove scrollbar space *!
        background: transparent; !* optional: just make scrollbar invisible *!
    }*/
</style>
