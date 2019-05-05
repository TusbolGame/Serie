<template>
    <div class="cmn-progress c2 my-2">
        <span :style="{width: width + '%'}"></span>
    </div>
</template>

<script>
    export default {
        name: "ProgressBarComponent",
        props: {
            width: {
                type: Number,
                default: 0,
            }
        },
    }
</script>

<style scoped>

</style>
