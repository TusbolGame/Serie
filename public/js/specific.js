$(document).ready(function() {
    $('#admin-tools').on('click', 'button', function (e) {
        let button = $(this);
        let buttonType = parseInt(button.attr('data-type'));
        let buttonGroup = parseInt(button.attr('data-group'));

        if (buttonGroup == 0) {
            switch (buttonType) {
                case 0:         // Update all shows
                    // updateShows(0);
                    break;
                case 1:         // Search shows
                    // if ($('#showSearch').val().trim().length == 0) {
                    //     bootbox.alert("The show search query string is empty");
                    //     break;
                    // }
                    // var args = {
                    //     url: '/data/search/show/' + $('#showSearch').val().trim(),
                    //     method: 'GET'
                    // };
                    //
                    // ajaxHandler(args,
                    //     function(serverData) {
                    //         let searchContainer = $('#show-search-container');
                    //         adminTools.append('<table class="table"></table>');
                    //         let table = adminTools.find('.table');
                    //         $.each(serverData.data, function(index, value) {
                    //             table.append(addRow(value));
                    //         });
                    //
                    //         function addRow(data) {
                    //             let row = $('<tr></tr>');
                    //             let date = $('<td>' + data.airing_at + '</td>');
                    //             let show = $('<td>' + data.show.name + '</td>');
                    //             let code = $('<td>' + data.episode_code + '</td>');
                    //
                    //             row.append(date);
                    //             row.append(show);
                    //             row.append(code);
                    //
                    //             return row;
                    //         }
                    //     }, null
                    // );
                    break;
                default:
                    break;
            }
        }

        /**
         *
         * @param type: Type of update [0 = all shows]
         */
        function updateShows(type) {
            var url;
            switch (type) {
                case 0:         // Update all shows
                    url = '/data/update/0';
                    break;
                default:
                    break;
            }

            var args = {
                url: url,
                method: 'GET'
            };

            ajaxHandler(args,
                function (serverData) {
                    if (serverData.data != null) {
                        console.log(serverData.data)
                        $.each(serverData.data.show, function(index, value) {
                            console.log(value);
                        });
                    }
                }, null
            );
        }
    });
});

$(document).ready(function() {
    $('.episodes').on('click', '.card-episode .card-actions .cmn-button', function(e) {
        let button = $(this);
        let container = button.parents('.card-episode');
        let episode_id = container.attr('data-episode');
        let show_id = container.attr('data-show');
        let buttonType = parseInt(button.attr('data-type'));

        switch (buttonType) {
            case 0:             // Show more actions
                toggleEpisodeExpanded(container);
                break;
            case 1:             // Search for torrents
                break;
            case 2:             // Add magnet link
                bootbox.prompt({
                    title: "Insert the magnet link",
                    backdrop: true,
                    callback:  function(magnetlink) {
                        var pattern = /([a-fA-F0-9]{40})/g;
                        episodeCardMessageCallback(magnetlink, 1, pattern);
                    }
                });
                break;
            case 3:             // Convert Torrent
                break;
            case 4:             // Play Episode
                break;
            case 5:             // Mark as watched
                episodeMarkView(container, episode_id, 1);
                break;
            case 6:             // Check Torrent Status
                break;
            case 7:             // Remove Show
                removeUserShow(container, show_id);
                break;
            case 8:             // Add bookmark
                bootbox.prompt({
                    title: "Insert the bookmark: [(HH:)ii:ss]",
                    backdrop: true,
                    callback: function(bookmarkTime) {
                        var pattern = /([0-9]{2}:)?[0-9]{2}:[0-9]{2}/g;
                        episodeCardMessageCallback(bookmarkTime, 0, pattern);
                    }
                });
                break;
            case 9:             // Show more details
                break;
            case 10:             // Rate this episode
                break;
            default:
                break;
        }


        var args = {
            url: '/episode/action/add/' + buttonType,
            method: 'GET'
        };

        ajaxHandler(args,
            function() {

            }, null
        );

        // Footer
        function removeUserShow(container, show_id) {
            var args = {
                url: '/show/remove/' + show_id,
                method: 'GET'
            };

            ajaxHandler(args,
                function() {
                    container.fadeOut(ANIMATION_TIME, function() {
                        $(this).remove();
                    });
                }, null
            );
        }


        function episodeCardMessageCallback(input, type, pattern) {
            if (input === null || input.trim().length === 0) {
                console.log('The string provided is empty.');
            } else {
                var matches = input.trim().match(pattern);

                if (matches == null || matches.length < 1) {
                    switch (type) {
                        case 0:                 // Bookmark
                            console.log('The string provided is not a valid bookmark.');
                            break;
                        case 1:                 // Magnetlink
                            console.log('The string provided is not a valid magnet link.');
                            break;
                        default:
                            break;
                    }
                } else {
                    var url;
                    switch (type) {
                        case 0:
                            url = '/episode/bookmark/add/' + episode_id + '/' + matches[0];
                            break;
                        case 1:
                            url = 'http://localhost:5000/torrent/add/' + input;
                            break;
                        default:
                            break;
                    }
                    var args = {
                        url: url,
                        method: 'GET',
                        crossDomain: true,
                        contentType: '',
                        dataType: 'jsonp',
                        accepts: {},
                    };
                    // console.log('test');
                    // window.axios.get(url, {
                    //         headers: {
                    //             'Access-Control-Allow-Origin': '*',
                    //         }
                    //     })
                    //     .then(({data}) => {
                    //     }).catch((error) => {
                    //     console.log(error.response);
                    // });
                    ajaxHandler(args,
                        function (data) {
                            console.log(data);
                            window.axios.get('/torrent/add/' + data.infoHash.trim())
                                .then(({data}) => {
                                }).catch((error) => {
                                console.log(error.response);
                            });
                        }, null
                    );
                }
            }
        }
    });

    $('.episodes').on('click', '.card-episode .episode-poster-container', function(e) {
        let episodeContainer = $(this).parents('.card-episode');

        toggleEpisodeExpanded(episodeContainer);
    });

    function toggleEpisodeExpanded(episodeContainer) {
        episodeContainer.toggleClass('expanded');
    }
});

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
