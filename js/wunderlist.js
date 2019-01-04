jQuery(document).ready(function ($) {

    var check = 0;

    $('.combined').click(function(){
       check++;
    });
    window.onbeforeunload = function (e) {

        e.preventDefault();
        if(check != 0){
            if(confirm("Du hast noch nicht zur Wunderlist Synchronisiert, sicher das du die Seite Verlassen willst?")){
                return true;
            }
        }

    }



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
                    '<a id="upload-to-wunderlist" class="btn btn-primary tip animation" data-title="Zu Wunderlist übertragen"><i class="fa fa-upload"></i></a>' +
                    '<img src="https://s3-eu-central-1.amazonaws.com/herdzeitmedia/wp-content/uploads/2018/02/20091157/Herdzeit_quadratisch.jpg" ' +
                    'width="35px" height="35px" class="loader element" data-title="Von Wunderlist übertragen" style="display: none"> ' +
                    '<a id="download-from-wunderlist" class="btn btn-primary" style="margin-left: 15px">' +
                    '<i class="fa fa-download"></i></a>' +
                    '<img src="https://s3-eu-central-1.amazonaws.com/herdzeitmedia/wp-content/uploads/2018/02/20091157/Herdzeit_quadratisch.jpg"'  +
                    'width="35px" height="35px" class="loader2 element" style="display: none; margin-left: 15px">'

                );
                $('img').load(function(){
                    $('#download-from-wunderlist').click();
                });

            } else {
                $('#wunderlist-feedback').html(
                    '<a class="wunderlist-connect" href="'+ rsp.redirect + '" target="_blank">' +
                    '<i class="fa fa-connectdevelop"></i> verbinden</a>').css('cursor', 'pointer');
            }

        });

        $('.wunderlist-connect').live('click', function(){

            $('#wunderlist-feedback').html('warte auf Verbindung...');

            var refreshIntervalId =  setInterval(function () {

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
                                '<a id="upload-to-wunderlist" class="btn btn-primary"><i class="fa fa-upload"></i></a>' +
                                '<a id="download-from-wunderlist" class="btn btn-primary" style="margin-left: 15px">' +
                                '<i class="fa fa-download"></i></a>'
                            );
                            clearInterval(refreshIntervalId);


                        }

                    });

            }, 10000);


        });


        $('#upload-to-wunderlist').live('click', function () {

            check == 0;


            $('#upload-to-wunderlist').hide();
            $('.loader').show();

            var tasks = [];
            $('.combined').each(function () {
                tasks.push({
                    text: $(this).text(),
                    checked: $(this).hasClass('checked'),
                    wunderlist_id: $(this).data('wunderlist'),
                    herdzeit_id: $(this).data('id')
                });
            });

            $.post(
                ajaxurl,
                {
                    action: "upload_to_wunderlist",
                    hz_list: wunderlist.hz_list,
                    tasks: tasks
                },
                function(rsp){

                    $('#upload-to-wunderlist').show();
                    $('.loader').hide();

                    window.location.reload(false);
                    items = JSON.parse(rsp);
                    $(items).each(function(index, value){
                        $('.combined[data-id="' + value.hzerdzeit_id + '"]').data('wunderlist', value.wunderlist_id);
                    });

                }
            );
        });


    $('#download-from-wunderlist').live('click', function () {

        $('#download-from-wunderlist').hide();
        $('.loader2').show();

        $.post(
            ajaxurl,
            {
                action: "download_from_wunderlist",
                hz_list: wunderlist.hz_list,
            },
            function(rsp){
                console.log(rsp);
                items = JSON.parse(rsp);
                $(items).each(function (index, value) {

                    if (value.completed) {
                        $('.combined[data-wunderlist="' + value.id + '"]').addClass('checked').data('wunderlist', value.id);
                    } else {
                        $('.combined[data-wunderlist="' + value.id + '"]').removeClass('checked').data('wunderlist', value.id);
                    }
                });
                update_checked();
                $('.loader2').hide();
                $('#download-from-wunderlist').show();
            }
        );
    });





    $('#wunderlist-sync').live('click', function () {

        $('#wunderlist-feedback').text('verbinde...');


        $.post(
            ajaxurl,
            {
                action: "wunderlist_sync",
                hz_list: ''
            },
            function (rsp) {
                rsp = JSON.parse(rsp);

                if (!rsp.connected) {
                    $('#wunderlist-feedback').text('Bitte nochmal klicken');
                    var win = window.open(rsp.redirect);
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Bitte deaktiviere deinen Popup-Blocker!');
                    }
                } else {
                    begin_sync();
                    setInterval(function () {
                        begin_sync();
                    }, 60000);
                }
            });
    });

});
function begin_sync() {

    $('#wunderlist-feedback').text('Verbunden, schicke Daten...');
    $.post(
        ajaxurl,
        {
            action: "begin_wunderlist_sync",
            hz_list: wunderlist.hz_list

        },
        function (rsp) {
            rsp = JSON.parse(rsp);
            if (rsp.tasks.length == 0) {
                $('#wunderlist-feedback').text('sende die Liste...');
                send_tasks_to_wunderlist(rsp.wlist);
            } else {

                $('#wunderlist-feedback').text('synchronisiert ' + get_datetime());
                update_from_wunderlist(rsp.tasks);
            }
        });
}




function update_from_wunderlist(tasks) {

    $(tasks).each(function (index, value) {
        if (value.completed) {
            $('.combined[data-id="' + value.ing_id + '"]').addClass('checked').data('wunderlist', value.id);
        } else {
            $('.combined[data-id="' + value.ing_id + '"]').removeClass('checked').data('wunderlist', value.id);
        }

    });
    update_checked(null);
}










function send_tasks_to_wunderlist(wlist) {

    var tasks = [];
    $('.combined').each(function () {
        tasks.push({
            text: $(this).text(),
            checked: $(this).hasClass('checked'),
            herdzeitid: $(this).data()
        });
    });
    $.post(
        ajaxurl,
        {
            action: "wunderlist_add_tasks",
            listid: wlist,
            tasks: tasks
        },
        function (rsp) {

            $('#wunderlist-feedback').text('synchronisiert ' + get_datetime());
            console.log(rsp);
        }
    );

}
function get_datetime(){
    var currentdate = new Date();
    var datetime = currentdate.getDate() + "."
        + (currentdate.getMonth() + 1) + "."
        + currentdate.getFullYear() + " "
        + currentdate.getHours() + ":"
        + currentdate.getMinutes() + ":"
        + currentdate.getSeconds();
    return datetime;
}