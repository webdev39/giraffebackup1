<template>
    <div class="modal-time-log">
        <div class="modal-time-log__list">
            <div class="modal-time-log__item">
                <div class="modal-time-log__content">
                    <span class="modal-time-log__addon">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                    </span>
                    <input type="number" step="1" min="0" max="9999" class="modal-time-log__input" v-model="innerHours"/>
                    <span class="modal-time-log__time-type">H</span>
                </div>
            </div>
            <div class="modal-time-log__item">
                <div class="modal-time-log__content">
                    <span class="modal-time-log__addon">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                    </span>

                    <input type="number" step="1" min="0" max="60" class="modal-time-log__input" v-model="innerMinutes"/>
                    <span class="modal-time-log__time-type">M</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "logged-time",
        props: {
            hours: Number,
            minutes: Number,
        },
        computed: {
            innerHours: {
                get() {
                    return Number(this.hours);
                },
                set(value) {
                    this.$emit('update:hours', Number(value));
                }
            },
            innerMinutes: {
                get() {
                    return Number(this.minutes);
                },
                set(value) {
                    const hours    = Number(this.hours) + Math.floor(Number(value) / 60);
                    const minutes  = Number(value) % 60;

                    this.$emit('update:hours', Number(hours));
                    this.$emit('update:minutes', Number(minutes));
                }
            },
        }
    }
</script>

<style lang="scss">
    .modal-time-log {
        position: relative;
        top: 0;
        left:0;
        border: none;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
    }
</style>
