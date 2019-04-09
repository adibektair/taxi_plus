<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>

<?


$me = \backend\models\SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
$myphone = $me->phone;
?>
<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table">
                        <thead>
                        <tr>
                                <th>ID заказа</th>
                                <th>Телефон клиента</th>
                                <th>Дата и время заказа</th>
                                <th>Цена</th>
                                <th>Статус заказа</th>
                                <th>Водитель</th>
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
    $(document).ready(function() {

        $(function() {
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
                    data: {"_csrf-backend":token, table:"orders", name:"dispatchers_orders"}
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

                            return '<label class="text-semibold">'+ data.id + '</label>';
                        }
                    },
                    {"mData": {},
                        "mRender": function (data, type, row) {
                            return '<label class="text-semibold">'+ data.phone + '</label>';
                        }
                    },

                    {"mData": {},
                        "mRender": function (data, type, row) {

                            return '<label class="text-semibold">'+ timeConverter(data.created) + '</label>';
                        }
                    },
                    {"mData": {},
                        "mRender": function (data, type, row) {

                            return '<label class="text-semibold">'+ data.price + ' тг.</label>';
                        }
                    },
                    {   "mData": {},
                        "mRender": function (data, type, row) {

                            switch (data.status) {
                                case '0':
                                    return '<label style="background-color: #880000" class="text-semibold"> Отменен </label>';

                                case '1':
                                    return '<label style="background-color: #99CC66" class="text-semibold"> В ожидании </label>';

                                case '2':
                                    return '<label style="background-color: #00CCFF" class="text-semibold"> Водитель в пути к клиенту </label>';

                                case '3':
                                    return '<label style="background-color: #00CCFF" class="text-semibold">Водитель ожидает клиента</label>';

                                case '4':
                                    return '<label style="background-color: #00CCFF" class="text-semibold"> В пути </label>';
                                default:
                                    return '<label style="background-color: #00aa00" class="text-semibold"> Завершен </label>';
                            }
                        }
                    },
                    {"mData": {},
                        "mRender": function (data, type, row) {
                            return '<label class="text-semibold">'+ data.dname +'</label>';
                        }
                    },
                    {"mData": {},
                        "mRender": function (data, type, row) {
                            if(data.dname != null){
                                return '<a data-id="'+data.dphone+'" data-info="<?=$myphone?>" class="action-link" href="dispatchers_orders/chat"><button class="btn btn-success" type="button">Чат с водителем</button></a>';
                            }else{
                                return '<p>Начать чат можно после того, как кто-нибудь примет заказ</p>'
                            }
                        }
                    },
                    {"mData": {},
                        "mRender": function (data, type, row) {
                        if (data.status == '1' || data.status == '2'){
                            return '<button id="cancel" data-id="'+ data.id +'" class="btn btn-danger" type="button">Отменить заказ</button>';
                        } else{
                            return '<label class="text-semibold">Заказ не может быть отменен</label>';

                        }

                        }
                    }
                    //

                ],
            });

            $('.table').on( 'draw.dt', function () {
                $("#cancel").on('click', function () {

                    var th = $(this);
                    console.log(th.data("id"));
                    $.ajax({
                        type: "POST",
                        url: "/profile/account/cancel-order/",
                        data: {"_csrf-backend":token, order_id: th.data("id")},
                        success: function (data) {
                            swal({
                                title: "Успешно",
                                text: "Заказ отменен",
                                dangerMode: true
                            });
                            $('#dispatchers_orders').trigger('click');
                        }, fail: function () {
                            swal({
                                title: "Ошибка",
                                text: "Заказ не может быть отменен",
                                dangerMode: false
                            });
                            $('#dispatchers_orders').trigger('click');
                        }
                    });
                    // swal({
                    //     title: "Are you sure ??",
                    //     text: message,
                    //     icon: "warning",
                    //     buttons: true,
                    //     dangerMode: true,
                    // })
                    //     .then((willDelete) => {
                    //         if (willDelete) {
                    //             $.ajax({
                    //                 type: "POST",
                    //                 url: "/profile/account/cancel-order/",
                    //                 data: {"_csrf-backend":token, id: th.data("id")},
                    //                 success: function (data) {
                    //                     $('#dispatchers_orders').trigger('click');
                    //                 }
                    //             });
                    //         } else {
                    //             swal("Your imaginary file is safe!");
                    //         }
                    //     });


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
    });

</script>