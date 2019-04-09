<!--<script type="text/javascript" src="/profile/files/js/mytables/orders/index.js"></script>-->

<?//=$this->render("/layouts/header/_header")?>
<script type="text/javascript" src="/profile/files/js/mytables/filtr.js"></script>
<?
use backend\components\Helpers;
?>


<?=$this->render("/layouts/header/_header")?>


<div  class="navbar navbar-default navbar-xs navbar-component" style = "margin-right: 2em; margin-left: 2em; margin-bottom: 2em;">
    <div style="padding-top: 1em; padding-bottom: 1em; margin-left: 1em;" class="navbar-collapse collapse" id="navbar-filter">
        <a href="#" style="color: #434343" class="daterange-picker filtr-toggle dropdown-toggle"><i class="icon-calendar" position-left"></i> Фильтровать по дате <span class="caret"></span></a>
    </div>
</div>



<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?
                    $cond= '';
                    if(Yii::$app->session->get('profile_role') == 9){
                        $cond = 'orders.id IS NOT null';
                    }elseif (Yii::$app->session->get('profile_role') == 3){
                        $me = \backend\models\SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
                        $my_cities = \backend\models\SystemUsersCities::find()->where(['system_user_id' => $me->id])->all();
                        $in = '';
                        foreach ($my_cities as $k => $v){
                            if($k == count($my_cities) - 1){
                                $in .= $v->city_id;
                            }else{
                                $in .= $v->city_id . ', ';
                            }
                            $cond = 'cities.id in (' . $in . ')';
                        }
                    }elseif (Helpers::getMyRole() == 5){
                        $cond = 'orders.taxi_park_id = ' . Helpers::getMyTaxipark();
                    }

                    $active_econom = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $active_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $active_kk = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $finished_econom = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $finished_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $finished_kk = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $active_lady = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $finished_lady = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());

                    $cancelled_econom = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $cancelled_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $cancelled_kk = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());
                    $cancelled_lady = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->all());

                    $mejgorod = count(\backend\models\SpecificOrders::findAll(['order_type_id' => 1]));
                    $gruz = count(\backend\models\SpecificOrders::findAll(['order_type_id' => 2]));
                    $evak = count(\backend\models\SpecificOrders::findAll(['order_type_id' => 3]));
                    $inva = count(\backend\models\SpecificOrders::findAll(['order_type_id' => 4]));
                    ?>
<!--                    --><?//=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>П/П</th>
                                <th>Эконом</th>
                                <th>Комфорт</th>
                                <th>КК</th>
                                <th>Леди такси</th>
                                <?
                                if(Helpers::getMyRole() != 5 AND Helpers::getMyRole() != 3){
                                    ?>

                                    <th>Межгород</th>
                                    <th>Грузотакси</th>
                                    <th>Инватакси</th>
                                    <th>Эвакуатор</th>

                                    <?
                                }
                                ?>

                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>Активные</th>
                            <th id="ek1"><?=$active_econom?></th>
                            <th id="k1"><?=$active_comfort?></th>
                            <th id="kk1"><?=$active_kk?></th>
                            <th id="l1"><?=$active_lady?></th>
                        </tr>
                        <tr>
                            <th>Завершенные</th>
                            <th id="ek2"><?=$finished_econom?></th>
                            <th id="k2"><?=$finished_comfort?></th>
                            <th id="kk2"><?=$finished_kk?></th>
                            <th id="l2"><?=$finished_lady?></th>

                        </tr>
                        <tr>
                            <th>Отмененные</th>
                            <th id="ek3"><?=$cancelled_econom?></th>
                            <th id="k3"><?=$cancelled_comfort?></th>
                            <th id="kk3"><?=$cancelled_kk?></th>
                            <th id="l3"><?=$cancelled_lady?></th>

                        </tr>
                        <tr>
                            <th>Итого</th>
                            <th id="ek"><?=$cancelled_econom + $active_econom + $finished_econom?></th>
                            <th id="k"><?=$cancelled_comfort + $active_comfort + $finished_comfort?></th>
                            <th id="kk"><?=$cancelled_kk + $active_kk + $finished_kk?></th>
                            <th id="l"><?=$cancelled_lady + $active_lady + $finished_lady?></th>
                            <?
                            if(Helpers::getMyRole() != 5 AND Helpers::getMyRole() != 3){
                                ?>
                                <th id="m"><?=$mejgorod?></th>
                                <th id="g"><?=$gruz?></th>
                                <th id="i"><?=$inva?></th>
                                <th id="e"><?=$evak?></th>
                                <?
                            }
                            ?>

                        </tr>
                        <tr>
                            <th></th>
                            <th><a data-id=1 data-info="Эконом" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                            <th><a data-id=2 data-info="Комфорт" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                            <th><a data-id=3 data-info="Корпоративный клиент" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                            <th><a data-id=4 data-info="Леди-такси" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                            <?
                            if(Helpers::getMyRole() != 5 AND Helpers::getMyRole() != 3){
                                ?>
                                <th><a data-id=1 data-info="Межгород" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                                <th><a data-id=2 data-info="Грузотакси" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                                <th><a data-id=4 data-info="Инватакси" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>
                                <th><a data-id=3 data-info="Эвакуатор" class="action-link" href="orders/orders-list"><button class="btn btn-success" type="button">Просмотр</button></a></th>

                                <?
                            }
                            ?>
                                                    </tr>

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
        $('.daterange-picker').daterangepicker(
            {
                startDate: <? if ($array_filtr[$page][$global_key]['start'] != null) { echo '"'.date("d/m/y", $array_filtr[$page][$global_key]['start']).'"'; } else { ?>moment().subtract(29, 'days')<? } ?>,
                endDate: <? if ($array_filtr[$page][$global_key]['end'] != null) { echo '"'.date("d/m/y", $array_filtr[$page][$global_key]['end']).'"'; } else { ?>moment()<? } ?>,
                dateLimit: { days: 120 },
                ranges: {
                    'Сегодня': [moment(), moment()],
                    'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                    'Прошедший месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Вперед',
                    cancelLabel: 'Отмена',
                    startLabel: 'Начальная дата',
                    endLabel: 'Конечная дата',
                    customRangeLabel: 'Выбрать дату',
                    daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт','Сб'],
                    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                    firstDay: 1
                },
                opens: 'right',
                applyClass: 'btn-small bg-primary',
                cancelClass: 'btn-small btn-default'
            },
            function(start, end) {
                $.ajax({
                    type: "POST",
                    url: "/profile/filtration/orders/",
                    data:{start:start.format('DD.MM.YYYY HH:MM'), end:end.format('DD.MM.YYYY HH:MM')},
                    success: function(data) {

                        document.getElementById('ek1').innerText = data.ek1;
                        document.getElementById('ek2').innerText = data.ek2;
                        document.getElementById('ek3').innerText = data.ek3;

                        document.getElementById('k1').innerText = data.k1;
                        document.getElementById('k2').innerText = data.k2;
                        document.getElementById('k3').innerText = data.k3;

                        document.getElementById('kk1').innerText = data.kk1;
                        document.getElementById('kk2').innerText = data.kk2;
                        document.getElementById('kk3').innerText = data.kk3;

                        document.getElementById('l1').innerText = data.l1;
                        document.getElementById('l2').innerText = data.l2;
                        document.getElementById('l3').innerText = data.l2;


                        document.getElementById('ek').innerText = data.ek;
                        document.getElementById('k').innerText = data.k;
                        document.getElementById('kk').innerText = data.kk;
                        document.getElementById('l').innerText = data.l;
                        document.getElementById('m').innerText = data.m;
                        document.getElementById('g').innerText = data.g;
                        document.getElementById('i').innerText = data.i;
                        document.getElementById('e').innerText = data.e;

                    },
                }).fail(function (xhr) {
                    console.log(xhr. responseText);
                });
            },
        );

    });
</script>