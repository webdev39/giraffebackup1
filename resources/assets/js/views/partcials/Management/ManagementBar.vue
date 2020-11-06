<template>
    <div class="bar">
        <div class="bar-value" :style="{width: percent + '%', backgroundColor: color }"></div>
    </div>
</template>

<script>
    export default {
        props:{
            percent:    { type: [String, Number] },
            color:      { type: String }
        }
    }
</script>