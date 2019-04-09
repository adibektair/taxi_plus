    <!--<script type="text/javascript" src="/profile/files/js/mytables/orders/index.js"></script>-->
<?php
use backend\models\Orders;
use backend\models\SpecificOrders;
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
                <div class="panel-body" >
<?
$cond = "orders.id IS NOT NULL";
if(Helpers::getMyRole() == 3){
        $cond = Helpers::getCitiesCondition();
}
elseif(Helpers::getMyRole() == 5){
    $cond = 'orders.taxi_park_id = ' . Helpers::getMyTaxipark();
}
$ekonom1 = Orders::find()->where(['order_type' => 1])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();
$ekonom2 = Orders::find()->where(['order_type' => 1])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();

$komfort1 = Orders::find()->where(['order_type' => 2])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();
$komfort2 = Orders::find()->where(['order_type' => 2])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();

$kk1 = Orders::find()->where(['order_type' => 3])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();
$kk2 = Orders::find()->where(['order_type' => 3])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();

$lady1 = Orders::find()->where(['order_type' => 4])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();
$lady2 = Orders::find()->where(['order_type' => 4])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->count();

$mejgorod = SpecificOrders::find()->where(['order_type_id' => 1])->count();
$gruz = SpecificOrders::find()->where(['order_type_id' => 2])->count();
$evak = SpecificOrders::find()->where(['order_type_id' => 3])->count();
$inva = SpecificOrders::find()->where(['order_type_id' => 4])->count();
?>
                    <TABLE class="table table-bordered" style="padding-right: 1em; padding-left: 1em;">
                        <TR>
                            <td></td>
                            <TH colspan="2">Эконом</TH>
                            <TH colspan="2">Комфорт</TH>
                            <TH colspan="2">Корп. клиенты</TH>
                            <TH colspan="2">Леди такси</TH>
                            <?
                            if(Helpers::getMyRole() != 5){
                            ?>
                                <TH colspan="1">Межгород</TH>
                                <TH colspan="1">Грузотакси</TH>
                                <TH colspan="1">Инватакси</TH>
                                <TH colspan="1">Эвакуатор</TH>
                            <?
                            }
                            ?>

                        </TR>
                        <TR>
                            <td></td>
                            <TH>Отработанные</TH>
                            <TH>Не отработанные</TH>
                            <TH>Отработанные</TH>
                            <TH>Не отработанные</TH>
                            <TH>Отработанные</TH>
                            <TH>Не отработанные</TH>
                            <TH>Отработанные</TH>
                            <TH>Не отработанные</TH>
                            <?
                            if(Helpers::getMyRole() != 5){
                                ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?
                            }
                            ?>

                        </TR>
                        <TR>
                            <td></td>
                            <TD id="ek1"><?=$ekonom1 ?></TD>
                            <TD id="ek2"><?=$ekonom2?></TD>
                            <TD id="k1"><?=$komfort1?></TD>
                            <TD id="k2"><?=$komfort2?></TD>
                            <TD id="kk1"><?=$kk1?></TD>
                            <TD id="kk2"><?=$kk2?></TD>
                            <TD id="l1"><?=$lady1?></TD>
                            <TD id="l2"><?=$lady2?></TD>
                            <?
                            if(Helpers::getMyRole() != 5){
                                ?>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <?
                            }
                            ?>

                        </TR>
                        <TR>
                            <td>Итого</td>
                            <TH id="ek" colspan="2"><?=$ekonom2 + $ekonom1?></TH>
                            <TH id="k" colspan="2"><?=$komfort2 + $komfort1?></TH>
                            <TH id="kk" colspan="2"><?= $kk2 + $kk1?></TH>
                            <TH id="l" colspan="2"><?= $lady2 + $lady1?></TH>
                            <?
                            if(Helpers::getMyRole() != 5){
                                ?>
                                <TH id="m" colspan="1"><?=$mejgorod?></TH>
                                <TH id="g" colspan="1"><?=$gruz?></TH>
                                <TH id="i" colspan="1"><?=$inva?></TH>
                                <TH id="e" colspan="1"><?=$evak?></TH>
                                <?
                            }
                            ?>

                        </TR>


                    </TABLE>
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
                        url: "/profile/filtration/stats-orders/",
                        data:{start:start.format('DD.MM.YYYY HH:MM'), end:end.format('DD.MM.YYYY HH:MM')},
                        success: function(data) {

                            document.getElementById('ek1').innerText = data.ek1;
                            document.getElementById('ek2').innerText = data.ek2;
                            document.getElementById('k1').innerText = data.k1;
                            document.getElementById('k2').innerText = data.k2;
                            document.getElementById('kk1').innerText = data.kk1;
                            document.getElementById('kk2').innerText = data.kk2;
                            document.getElementById('l1').innerText = data.l1;
                            document.getElementById('l2').innerText = data.l2;

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