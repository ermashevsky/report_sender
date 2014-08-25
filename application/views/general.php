<body>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span16">

                <div class="row-fluid">
                    <div class="span4">

                        <legend><i class="icon-upload"></i> Выберите файл</legend>

                        <input type="file" name="file_upload" id="file_upload" />
                        <div class = "alert alert-success fade in" data-alert="alert" style="display: none;"></div>

                    </div>
                    <div class="span8">

                        <legend><i class="icon-list"></i> Excel файлы</legend>
                        <div id="files"></div>


                    </div>
                </div>
            </div>

        </div>
        <div class="row-fluid">
            <div class="span8" id="excel_table_block">
                
                <legend><i class="icon-eye-open"></i> Просмотр Excel-файла</legend>
<div class="progress">
    <div class="bar six-sec-ease-in-out" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
</div>
                <div id="table_block" style="display:none;">

                    <table id="excel_table" class="table table-striped table-bordered table-condensed table-hover">

                    </table>
                </div>

            </div>
            <div class="span4" id="excel_table_block">
                
                <legend><i class="icon-user"></i> Список рассылки</legend>
                <ul>
                    <?php
                    $list = new General();
                    
                    foreach($list->getRecipientList() as $list_value):
                        if(is_null($list_value->email) ||$list_value->email==''){
                            echo '<li class="nav nav-list"><a data-toggle="modal" href="#form-content" class="list-group-item" onclick="getListItem('.$list_value->id.'); return false;"><span class="label label-important"><i class="icon-envelope"></i> '.$list_value->username.'</label></a></li>';
                        }else{
                            echo '<li class="nav nav-list"><a data-toggle="modal" href="#form-content" class="list-group-item" onclick="getListItem('.$list_value->id.'); return false;"><span class="label label-info">'.$list_value->username.'</span></a> <span class="label label-warning">'.$list_value->list.'</span></li>';
                        }
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="delete_dialog"></div>
    <!-- model content -->    
    <div id="form-content" class="modal hide fade in" style="display: none; ">
            <div class="modal-header">
                  <a class="close" data-dismiss="modal">×</a>
                  <h4>Карточка контакта</h4>
            </div>
        <div>
            <form class="contact">
            <fieldset>
                 <div class="modal-body">
                     <ul class="nav nav-list">
                <li class="nav-header">Краткое имя</li>
                <li><input class="input-xlarge" value="" type="text" name="username" id="username" readonly="true"></li>
                <li class="nav-header">Email</li>
                <li><input class="input-xlarge" value="" type="text" name="email" id="email"></li>
                <li class="nav-header">Список подписки</li>
                <li><input class="input-xlarge" value="" type="text" name="email_list" data-role="tagsinput" id="email_list"></li>
                <div class="alert alert-success" id="saveConfirmation" style="display:none; width: 235px;">
                    <strong>Внимание!</strong> Изменения сохранены.
                </div>
                </ul>
                     <input value="" type="hidden" name="id" id="id">
                </div>
            </fieldset>
            </form>
        </div>
         <div class="modal-footer">
             <button class="btn btn-mini" id="submit" onclick="saveList();return false;">Сохранить</button>
             <a href="#" class="btn btn-mini" data-dismiss="modal">Отменить</a>
          </div>
    </div>
    
</body>
<!--<footer>
        <p>Телекоммуникационная компания <a href="http://dialog64.ru" target="_blank">«Диалог»</a> 2013. | Телефон / факс: (8452) 740-740 E-mail: info@dialog64.ru
                </p>
</footer>-->
</html>