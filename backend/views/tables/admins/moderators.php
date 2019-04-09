<?=$this->render("/layouts/header/_header", array("model" => null))?>
<!--<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>-->
<!--<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>-->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>

<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?if($admin != null){
                        ?>
                        <h2 >Администратор <?=$admin->first_name . ' ' . $admin->last_name ?></h2>
                        <?
                        Yii::$app->session->set('last_moder_city_id', $model);
                        Yii::$app->session->set('last_admin_id', $admin->id);
                        ?>
                        <?
                    }else{
                        Yii::$app->session->set('last_admin_id', null);
                    }?>
                    <?=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Модератор</th>
                                <th>Регион</th>
                                <th>email</th>
                                <th>Таксопарк</th>
                                <th>Количество водителей</th>
                                <th>Количество клиентов</th>
                                <th>Монеты пополненные вручную</th>
                                <th>Сумма сданная в кассу</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var ids = "<?= $model?>";
        var token = $('meta[name=csrf-token]').attr("content");
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            responsive: true,
            dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                "emptyTable":       "Данные отсутствуют.",
                "info":             "Показано с _START_ по _END_, всего: _TOTAL_",
                "infoEmpty":        "Показано 0 из 0, всего 0",
                "infoFiltered":     "(отфильтровано из _MAX_)",
                "infoPostFix":      "",
                "lengthMenu":       "<span>Показано:</span> _MENU_",
                "loadingRecords":   "Загрузка...",
                "processing":       "Загрузка...",
                "search":           "<span>Поиск:</span> _INPUT_",
                "searchPlaceholder": 'Введите ключевые слова...',
                "zeroRecords":      "Данные отсутствуют.",
                "paginate": {
                    "first":        "Первая",
                    "previous":     "&larr;",
                    "next":         "&rarr;",
                    "last":         "Последняя"
                },
                "aria": {
                    "sortAscending":    ": activate to sort column ascending",
                    "sortDescending":   ": activate to sort column descending"
                },
                "decimal":          "",
                "thousands":        ","
            },


            drawCallback: function () {
                $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
            },
            preDrawCallback: function() {
                $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
            }
        });
        $('.table').DataTable({
            "processing": true,
            "serverSide": true,

            "ajax":{
                url :"/profile/tables/get-new-table/",
                type: "GET",
                data: {"_csrf-backend":token, table:"system_users", name:"admins/moderators", ids:ids}
            },
            "stateSave": true,
            "stateSaveCallback": function (settings, data) { //Сохраняем таблицу (Страница, Сортировка, Количество записей и т.д)
                $.ajax({
                    url: "/profile/tables/savestate/",
                    dataType: "json",
                    type: "POST",
                    data: data,
                    success: function (response) {
                        console.log('save>>>', response);
                    }
                });
            },
            "stateLoadCallback": function (settings, callback) { //Загружаем сохраненные настройки таблицы
                $.ajax( {
                    url: '/profile/tables/getstate/',
                    async: false,
                    dataType: 'json',
                    success: function (json) {
                        console.log('load>>>', json);
                        callback(json);
                    }
                } );
            },
            aoColumns: [
                {"mData": {},
                    "mRender": function (data, type, row) {
                        var name = data.first_name + ' ' + data.last_name;
                        return '<button class="btn btn-primary add" data-name="'+ name + '" data-id="'+ data.id  +'" type="button">Сдал сумму в кассу</button>';
                    }
                },
                <?
                if(\backend\components\Helpers::getMyRole() == 9){
                ?>

                {"mData": {},
                    "mRender": function (data, type, row) {
                        var name = data.first_name + ' ' + data.last_name;
                        return '<button class="rmv btn btn-danger" data-name="'+ name + '" data-id="'+ data.id  +'" type="button">Удалить</button>';
                    }
                },

                <?
                }
                ?>

                {"mData": {},
                    "mRender": function (data, type, row) {
                        return '<span style="float:left;">' + data.id + '</span><span style="float:right; margin-top: 2px;"><ul class="icons-list" style="float:left;"><li><a class="action-link" data-id="'+ data.id +'" title = "Редактировать" href="moderators/form-moderator"><i style="font-size:0.9em; margin-top:2px;" class="icon-pencil"></i></a></li></ul></span>';

                        return '<label class="text-semibold">'+ data.id + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {
                        return '<label class="text-semibold">'+ data.first_name + ' ' + data.last_name +'</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.cities + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.email + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.taxipark + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.drivers + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.clients + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.sum2 + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.sum + '</label>';
                    }
                },



            ]
        });

        $('.table').on( 'draw.dt', function () {
            $(".rmv").on('click', function () {

                var th = $(this);
                swal({
                        title: "Вы уверены, что хотите удалить " + th.data("name") + " из системы?" ,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#2196F3",
                        confirmButtonText: "Подтверждаю",
                        cancelButtonText: "Отмена",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            th.hide();
                            $.ajax({
                                type: "POST",
                                url: "/profile/site/remove-user/",
                                data: {"_csrf-backend":token, id: th.data("id")},
                                success: function (data) {
                                    swal({
                                        title: "В течении нескольких минут пользователь будет удален из системы" ,
                                        type: "success",
                                        text: ""
                                    });
                                },
                            });
                        }
                    });

            });
        } );


        $('.table').on( 'draw.dt', function () {
            $(".add").on('click', function () {

                var th = $(this);
                console.log(th.data("id"));
                swal({
                    text: 'Сколько денег сдал ' + th.data("name") + ' в кассу (в тенге)',
                    content: "input",
                    button: {
                        text: "Пополнить",
                        closeModal: false,
                    },
                })
                    .then(name => {
                        if (!name) throw null;

                        return fetch(`site/moderator/?amount=${name}&id=${th.data("id")}`);
                    })
                    .then(results => {
                        return results.json();
                    })
                    .then(json => {
                        const movie = json.type;
                        if(movie === 'success'){
                            swal({
                                title: "Успешно",
                                text: ''
                            });
                            $('#admins').trigger('click');
                        }


                    })
                    .catch(err => {
                        if (err) {
                            swal("Oh noes!", "The AJAX request failed!", "error");
                        } else {
                            swal.stopLoading();
                            swal.close();
                        }
                    });

            });
        } );

        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto'
        });
        function timeConverter(UNIX_timestamp){
            var a = new Date(UNIX_timestamp * 1000);
            var months = ['Янв','Фев','Мар','Aпр','Maй','Июн','Июл','Авс','Сен','Окт','Ноя','Дек'];
            var year = a.getFullYear();
            var month = months[a.getMonth()];
            var date = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes();

            var sec = a.getSeconds();
            var minutes = '';
            if(min < 10){
                minutes = '0' + min;
            }else{
                minutes = min;
            }
            var hrs = '';
            if(hour < 10){
                hrs = '0' + hour;
            }else{
                hrs = hour;
            }
            var time = date + ' ' + month + ' ' + year + ', ' + hrs + ':' + minutes;
            return time;
        }
    });

</script>