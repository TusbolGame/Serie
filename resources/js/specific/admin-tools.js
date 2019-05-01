$(document).ready(function() {
    $('#admin-tools').on('click', 'button', function (e) {
        let button = $(this);
        let buttonType = parseInt(button.attr('data-type'));
        let buttonGroup = parseInt(button.attr('data-group'));

        if (buttonGroup == 0) {
            switch (buttonType) {
                case 0:         // Update all shows
                    updateShows(0);
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
