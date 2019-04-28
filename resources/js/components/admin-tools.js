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
