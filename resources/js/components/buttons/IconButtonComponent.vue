<template>
    <button :class="'cmn-button icon cmn-rippleable ' + buttonClass"
            :title="title"
            :data-type="type"
            :data-group="group"
            type="button"
            autocomplete="off"
            @click="clickHandler"
    >
        <div :class="'cmn-button-content ' + icon"></div>
        <div class="cmn-button-ripple"></div>
    </button>
</template>

<script>
    import VueEventBus from '../VueEventBus'
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
                VueEventBus.$emit('buttonClassToCoverSplash', this.buttonClass);
            },
            clickedButton: function() {
                if (this.type !== 0) {
                    window.axios.get('/episode/action/add/' + this.group + '/' + this.type)
                        .then(({data}) => {
                            console.log(data);
                        }).catch((error) => {
                        console.log(error.response);
                    });
                }
            },
            clickHandler: function() {
                this.bubbleClassToCoverSplash();
                this.clickedButton();
            }
        },
    }
</script>
<style scoped>
</style>
