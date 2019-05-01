// var showSearch = new Vue({
//     el: '#show-search-container',
//     data: {
//         showSearch:'',
//         results:[],
//         noResults:false,
//         searching:false
//     },
//     methods: {
//         searchShow: function() {
//             this.searching = true;
//             fetch('/data/search/show/${this.term}')
//                 .then(res => res.json())
//                 .then(res => {
//                     this.searching = false;
//                     this.results = res.data;
//                     this.noResults = this.results.length === 0;
//                 });
//         }
//     },
// });
