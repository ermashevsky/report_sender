<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <title>Report Sender 1.0</title>

        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
        <script src="/assets/js/bootstrap-button.js"></script>
        <script src="/assets/js/bootstrap-fileupload.js"></script>
        <script src="/assets/js/bootstrap-notify.js"></script>
        <script src="/assets/js/jquery.uploadify.min.js"></script>
        <script src="/assets/js/bootbox.min.js"></script>
        <script src="/assets/js/bootstrap-progressbar.js"></script>
        <script src="/assets/js/bootstrap-tagsinput.js"></script>


        <link href="/assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="/assets/css/bootstrap-responsive.css" rel="stylesheet">
        <link href="/assets/css/bootstrap-button.css" rel="stylesheet">
        <link href="/assets/css/bootstrap-fileupload.css" rel="stylesheet">
        <link href="/assets/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/assets/css/uploadify.css" />
        <link rel="stylesheet" href="/assets/css/bootstrap-tagsinput.css">

        <style>
            #files { font-family: Verdana, sans-serif; font-size: 12px; }
            #files strong { font-size: 13px; }
            #files a { float: left; margin: 0 0 5px 10px; }
            #files ul { list-style: none; padding-left: 0; }
            #files li { width: 100%; font-size: 12px; padding: 5px; border-bottom: 1px solid #CCC; }
            .modal {
                width: 350px;
            }
            #table_block{
                display: block; 
                height:400px;
                width: 100%;
                overflow-y: scroll;
                overflow-x: hidden;
                margin-bottom: 15px;
            }
            .bootstrap-tagsinput{
                display: block;
                width: 270px;
            }
        </style>
    </head>
    <script>
        function getListItem(id) {
            $("#email_list").empty();
            $.post('<?= site_url('general/getListItem'); ?>', {'id': id},
            function(data) {
                $.each(data, function(i, val) {
                    $("#id").val(data[i].id);
                    $("#username").val(data[i].username);
                    $("#email").val(data[i].email);
                    //$("#email_list").tagsinput('add', data[i].list);
                    if(data[i].list === null){
                        $("#email_list").tagsinput('removeAll');
                    }else{
                        $("#email_list").tagsinput('removeAll');
                        $("#email_list").tagsinput('add', data[i].list);
                    }
                });
            }, 'json');
        }


        function saveList() {
            
            var id          = $("#id").val();
            var username    = $("#username").val();
            var email       = $("#email").val();
            var email_list  = $("#email_list").val();


            $.post('<?= site_url('general/saveListItem'); ?>', {'id': id, 'username':username, 'email':email, 'email_list':email_list},
            function(data) {

               $("#saveConfirmation").css('display','block');
               $("#saveConfirmation").alert();
               window.setTimeout(function() {
                   $("#saveConfirmation").alert('close');
                   $('#form-content').modal('hide');
                   window.location.reload();
               }, 2000);
            });
        }

        function delfile(path) {
            bootbox.dialog("<i class='icon-question-sign'></i> Вы действительно хотите удалить файл?", [{
                    "label": "Удалить",
                    "class": "btn-mini btn",
                    "callback": function() {
                        $.post('<?= site_url('general/deleteFromServer'); ?>', {'pathfile': path},
                        function(data) {
                            window.location.reload();
                        });
                    }
                },
                {
                    "label": "Отмена",
                    "class": "btn-mini btn",
                    "callback": function() {

                    }}]
                    );
        }


        function viewfile(path) {
            $('#excel_table').empty();
            $.post('<?= site_url('general/readXLS'); ?>', {'pathfile': path},
            function(data) {
                console.info(data);
                $('#excel_table').append('<thead><tr><th>№</th><th>A</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th><th>J</th></tr></thead>');
                $.each(data, function(i, val) {
                    $('#excel_table').append('<tr><td>' + i + '</td><td>' + data[i].A + '</td><td>' + data[i].C + '</td><td>' + data[i].D + '</td><td>' + data[i].E + '</td><td>' + data[i].F + '</td><td>' + data[i].G + '</td><td style="color:#' + data[i].K + '">' + data[i].H + '</td><td style="color:#' + data[i].B + '">' + data[i].I + '</td><td>' + data[i].J + '</td></tr>');
                });
                $('#table_block').css('display', 'block');
                $('#excel_table_block').append("<button class='btn btn-info pull-right' style='display: none;' id='send_mail' onclick=send_email('" + path + "')><i class='icon-envelope'></i> Рассылка</button>");
                $('#send_mail').css('display', 'block');

            }, "json");
        }
        function getPropertyCount(obj) {
            var count = 0,
                    key;

            for (key in obj) {
                if (obj.hasOwnProperty(key)) {
                    count++;
                }
            }

            return count;
        }

        function send_email(path) {
            var counter = 0;
            $.post('<?= site_url('general/readXLS'); ?>', {'pathfile': path},
            function(dataset) {

                var countAllObject = getPropertyCount(dataset);

                $.each(dataset, function(i, val) {
                    $.post('<?= site_url('general/send_email'); ?>', {'A': dataset[i].A, 'B': dataset[i].B, 'C': dataset[i].C, 'D': dataset[i].D, 'E': dataset[i].E, 'F': dataset[i].F,
                        'G': dataset[i].G, 'H': dataset[i].H, 'I': dataset[i].I, 'J': dataset[i].J,'K':dataset[i].K},
                    function(res) {
                        var current_perc = Math.round((((counter++) / countAllObject) * 100), 2);

                        $('.progress .bar').css('width', current_perc + '%').attr('aria-valuenow', current_perc);
                        $('.progress .bar').text(current_perc + '%');

                        if (current_perc === 100) {
                            createXlsFiles();
                        }
                        
                    }, 'json');

                });

            }, "json");
            $('.progress .bar').css('display', 'block');

        }

        function createXlsFiles() {
            $.post('<?= site_url('general/createXlsFiles'); ?>',
                    function(data) {
                        console.info(data);
                      if(data === 9){
                          console.info(data);
                      }
                    }, 'json');
        }


        function refresh_files()
        {
            $.post("general/find_all_files", {dir: '/home/denic/web/report_sender/uploads'}, function(data) {
                $("#files").html(data);
            });
        }

        $(function() {
            refresh_files();
            $('#file_upload').uploadify({
                'fileTypeExts': '*.xls; *.xlsx',
                'swf': '/assets/js/uploadify.swf',
                'uploader': 'general/uploadify',
                'buttonText': "Выберите файл",
                'displayData': 'percentage',
                'onUploadSuccess': function(file, data, response) {
                    $('.alert-success').append('Файл ' + file.name + ' успешно загружен.');
                    $('.alert-success').css("display", "block");
                    setTimeout(function() {
                        $('.alert-success').remove();
                        $('.alert-success').alert('close');
                        window.location.reload();
                    }, 5000);
                    refresh_files();
                }
                // Put your options here
            });
        });

        $("#email_list").tagsinput({
            itemValue: 'value',
            itemText: 'text'

        });

    </script>
    <div class="page-header">
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container"><!-- Collapsable nav bar -->
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <!-- Your site name for the upper left corner of the site -->
                    <a class="brand"><i class="icon-envelope"></i> Report Sender 1.0</a>
                    
                    <!-- Start of the nav bar content -->
                    <div class="nav-collapse"><!-- Other nav bar content -->
                        <!-- The drop down menu -->
                        <ul class="nav pull-right">
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>