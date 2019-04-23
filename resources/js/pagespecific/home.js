$(document).ready(function() {
    // Ripple handling
    $(document).on('click', 'button', function() {
        var button = $(this);
        var buttonType = parseInt(button.attr('data-type'));
        var buttonGroup = parseInt(button.attr('data-group'));

        switch (buttonGroup) {
            case 0:     // Admin tools
                switch (buttonType) {
                    case 0:     // Update all shows
                        var args = {
                            url: '/ajax/update/0',
                            method: 'GET'
                        };

                        ajaxHandler(args,
                            function(serverData) {
                                let adminTools = $('#admin-tools');
                                adminTools.append('<table class="table"></table>');
                                let table = adminTools.find('.table');
                                $.each(serverData.data, function(index, value) {
                                    table.append(addRow(value));
                                });

                                function addRow(data) {
                                    let row = $('<tr></tr>');
                                    let date = $('<td>' + data.airing_at + '</td>');
                                    let show = $('<td>' + data.show.name + '</td>');
                                    let code = $('<td>' + data.episode_code + '</td>');

                                    row.append(date);
                                    row.append(show);
                                    row.append(code);

                                    return row;
                                }
                            }, null
                        );
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    });
});
