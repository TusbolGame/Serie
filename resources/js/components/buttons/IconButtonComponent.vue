<template>
    <button :class="'cmn-button icon cmn-rippleable ' + buttonClass" :title="title"
            :data-type="type" :data-group="group" type="button"
            autocomplete="off">
        <div :class="'cmn-button-content' + icon"></div>
        <div class="cmn-button-ripple"></div>
    </button>

</template>

<script>
    export default {
        name: "IconButtonComponent",
        props: {
            buttonClass: {
                type: String,
                default: '',
            },
            title: {
                type: String,
                default: '',
            },
            type: {
                type: Number,
                default: null,
            },
            group: {
                type: Number,
                default: null,
            },
            icon: {
                type: String,
                default: '',
            },
        },
        methods: {
            bubbleClassToCoverSplash: function() {
                this.$emit('button-class-to-cover-splash', this.buttonClass);
            }
        },
    }
</script>

<style scoped>

</style>
