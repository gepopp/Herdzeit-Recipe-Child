jQuery(document).ready(function ($) {
    $.post(
        ajaxurl,
        {
            action: "wunderlist_sync",
            hz_list: wunderlist.hz_list
        },
        function (rsp) {


            rsp = JSON.parse(rsp);
            if (rsp.connected) {
                $('#wunderlist_connection').val('1');
                $('#wunderlist-feedback').html(
                    'Deine Wunderlist wird beim speichern automatisch synchronisiert.'
                );
            } else {
                $('#wunderlist-feedback').html(
                    '<a class="wunderlist-connect" href="' + rsp.redirect + '" target="_blank">' +
                    '<i class="fa fa-connectdevelop"></i> Mit Wunderlist verbinden</a>').css('cursor', 'pointer');
            }

        });


    $('.wunderlist-connect').live('click', function () {

        $('#wunderlist-feedback').html('warte auf Verbindung...');

        var refreshIntervalId = setInterval(function () {

            $.post(
                ajaxurl,
                {
                    action: "wunderlist_sync",
                    hz_list: wunderlist.hz_list
                },
                function (rsp) {
                    rsp = JSON.parse(rsp);
                    if (rsp.connected) {
                        $('#wunderlist-feedback').html(
                            'Deine Wunderlist wird beim speichern automatisch synchronisiert.'
                        );
                        clearInterval(refreshIntervalId);


                    }

                });

        }, 10000);

    });
});