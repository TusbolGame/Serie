<template>
    <div class="col-12">
        <div class="row no-gutters mb-1 d-flex justify-content-between">
            <div class="col-2 text-black-50 font-weight-light" v-bind:title="'Airing on the '+displayDate(airing_at, 'DD/MM/YYYY')+' at '+displayDate(airing_at, 'HH:mm')">
                {{displayDate(airing_at, 'DD/MM')}}
            </div>
            <div class="col-8">
                <a v-bind:href="'/episode/' + uuid" class="text-dark">{{show_name}}</a>
            </div>
            <div>
                {{episode_code}}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowUpdateResultComponent",
        props: {
            show_name: {
                type: String,
            },
            uuid: {
                type: String,
            },
            api_link: {
                type: String,
            },
            airing_at: {
                type: String,
            },
            episode_code: {
                type: String,
            },
            summary: {
                type: String,
            },
        },
        data: function() {
            return {}
        },
        methods: {
            displayDate: function(date, format) {
                return moment(date).format(format);
            },
        },
    }
</script>

<style scoped>

</style>
