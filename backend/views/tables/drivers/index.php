<!--<script type="text/javascript" src="/profile/files/js/mytables/drivers/index.js"></script>-->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>

<?=$this->render("/layouts/header/_header")?>

<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table">
                        <thead>
                        <tr>
                            <?
                            if(\backend\components\Helpers::getMyRole() == 4) {
                            ?>
                                <th></th>
                                <?
                            }?>

                            <th>ID</th>
                            <th>Имя</th>
                            <th>Пол</th>
                            <th>Марка автомобиля</th>
                            <th>Гос. номер</th>
                            <th>Регион</th>
                            <th>Таксопарк</th>
                            <th>Баланс</th>
                            <th>Дата регистрации</th>
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
                data: {"_csrf-backend":token, table:"users", name:"drivers"}
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
                <?
                if(\backend\components\Helpers::getMyRole() == 4){
                ?>
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<button class="add btn btn-primary" data-name="'+ data.name +'" data-id="'+ data.id  +'" type="button">Пополнить счет</button>';
                    }
                },
                <?
                }
                ?>
                {"mData": {},
                    "mRender": function (data, type, row) {
                        // return '<span style="float:left;">' + data.id + '</span><span style="float:right; margin-top: 2px;"><ul class="icons-list" style="float:left;"><li><a class="action-link" data-id="'+ data.id +'" title = "Редактировать" href="admins/form-admin"><i style="font-size:0.9em; margin-top:2px;" class="icon-pencil"></i></a></li></ul></span>';
                        return '<label class="text-semibold">' + data.id + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.name + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {
                        return '<label class="text-semibold">'+ data.gender +'</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.model + ' ' + data.submodel + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.number + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.city + '</label>';
                    }
                },

                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.tp + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ data.balance + '</label>';
                    }
                },
                {"mData": {},
                    "mRender": function (data, type, row) {

                        return '<label class="text-semibold">'+ timeConverter(data.created) + '</label>';
                    }
                },


            ]
        });

        $('.table').on( 'draw.dt', function () {
            $(".add").on('click', function () {

                var th = $(this);
                console.log(th.data("id"));
                swal({
                    text: 'Введите количество монет для ' + th.data("name"),
                    content: "input",
                    button: {
                        text: "Пополнить",
                        closeModal: false,
                    },
                })
                    .then(name => {
                        if (!name) throw null;

                        return fetch(`site/driver-balance/?amount=${name}&id=${th.data("id")}`);
                    })
                    .then(results => {
                        return results.json();
                    })
                    .then(json => {
                        const movie = json.type;
                        if(movie === 'success'){
                            swal({
                                title: "Баланс успешно пополнен",
                                text: ''
                            });
                            $('#drivers').trigger('click');

                        }else{
                            swal({
                                title: "У Вашего таксопарка недостаточно баланса",
                                text: ''
                            });
                        }

                    })
                    .catch(err => {
                        if (err) {
                            swal({
                                title: "У Вашего таксопарка недостаточно баланса",
                                text: ''
                            });
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