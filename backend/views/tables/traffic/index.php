<!--<script type="text/javascript" src="/profile/files/js/mytables/traffic/index.js"></script>-->

<?=$this->render("/layouts/header/_header")?>


<div  class="navbar navbar-default navbar-xs navbar-component" style = "margin-right: 2em; margin-left: 2em; margin-bottom: 2em;">
    <div style="padding-top: 1em; padding-bottom: 1em; margin-left: 1em;" class="navbar-collapse collapse" id="navbar-filter">
        <a href="#" style="color: #434343" class="daterange-picker filtr-toggle dropdown-toggle"><i class="icon-calendar" position-left"></i> Фильтровать по дате <span class="caret"></span></a>
    </div>
</div>


<?
if(\backend\components\Helpers::getMyRole() == 9){
    $tp1 = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->andWhere(['reciever_user_id' => 111])->andWhere(['sender_tp_id' => 0])->sum('amount');
    $driver = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->andWhere(['<>', 'reciever_user_id', 111])->andWhere(['sender_tp_id' => 0])->sum('amount') + 0;
    $companies = \backend\models\Company::find()->sum('balance') + 0;
    $to_companies = \backend\models\MonetsTraffic::find()->where('reciever_company_id IS NOT NULL')->andWhere(['sender_tp_id' => \backend\components\Helpers::getMyTaxipark()])->sum('amount') + 0;
    $drivers = \backend\models\Users::find()->where(['role_id' => 2])->sum('balance');
    $users = \backend\models\Users::find()->where(['role_id' => 1])->sum('balance');
    $tps = \backend\models\TaxiPark::find()->where(['main' => 0])->sum('balance') + 0;
}
elseif (\backend\components\Helpers::getMyRole() == 5){
    $tp1 = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->innerJoin('taxi_park', 'taxi_park.id = monets_traffic.reciever_tp_id')->andWhere('taxi_park.id = ' . \backend\components\Helpers::getMyTaxipark())->andWhere(['reciever_user_id' => 111])->andWhere(['sender_tp_id' => 0])->sum('amount') + 0;
    $driver = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->innerJoin('users', 'users.id = monets_traffic.reciever_user_id')->andWhere('users.city_id = ' . \backend\components\Helpers::getMyTaxipark())->andWhere(['<>', 'reciever_user_id', 111])->andWhere(['sender_tp_id' => 0])->sum('amount') + 0;
    $companies = \backend\models\Company::find()->where('city_id in (4)')->sum('balance') + 0;
    $to_companies = \backend\models\MonetsTraffic::find()->where('reciever_company_id IS NOT NULL')->innerJoin('company', 'company.id = monets_traffic.reciever_company_id')->andWhere('company.city_id = 4')->andWhere(['sender_tp_id' => \backend\components\Helpers::getMyTaxipark()])->sum('amount') + 0;
    $drivers = \backend\models\Users::find()->where(['role_id' => 2])->where('taxi_park_id = ' . \backend\components\Helpers::getMyTaxipark())->sum('balance') + 0;
    $users = \backend\models\Users::find()->where(['role_id' => 1])->where('taxi_park_id = ' . \backend\components\Helpers::getMyTaxipark())->sum('balance') + 0;
    $tps = \backend\models\TaxiPark::find()->where(['main' => 0])->where('city_id in (4)')->sum('balance') + 0;
}
else{
    $tp1 = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->innerJoin('taxi_park', 'taxi_park.id = monets_traffic.reciever_tp_id')->andWhere('taxi_park.city_id in (' . \backend\components\Helpers::getCitiesString() .')')->andWhere(['reciever_user_id' => 111])->andWhere(['sender_tp_id' => 0])->sum('amount') + 0;
    $driver = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->innerJoin('users', 'users.id = monets_traffic.reciever_user_id')->andWhere('users.city_id in (' . \backend\components\Helpers::getCitiesString() .')')->andWhere(['<>', 'reciever_user_id', 111])->andWhere(['sender_tp_id' => 0])->sum('amount') + 0;
    $companies = \backend\models\Company::find()->where('city_id in (' . \backend\components\Helpers::getCitiesString() .')')->sum('balance') + 0;
    $to_companies = \backend\models\MonetsTraffic::find()->where('reciever_company_id IS NOT NULL')->innerJoin('company', 'company.id = monets_traffic.reciever_company_id')->andWhere('company.city_id IN (' . \backend\components\Helpers::getCitiesString() .')')->andWhere(['sender_tp_id' => \backend\components\Helpers::getMyTaxipark()])->sum('amount') + 0;
    $drivers = \backend\models\Users::find()->where(['role_id' => 2])->where('city_id in (' . \backend\components\Helpers::getCitiesString() .')')->sum('balance') + 0;
    $users = \backend\models\Users::find()->where(['role_id' => 1])->where('city_id in (' . \backend\components\Helpers::getCitiesString() .')')->sum('balance') + 0;
    $tps = \backend\models\TaxiPark::find()->where(['main' => 0])->where('city_id in (' . \backend\components\Helpers::getCitiesString() .')')->sum('balance') + 0;
}
?>
<div class="content">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-body">
                    <?=$this->render('/layouts/header/_filter', array('page' => $page))?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th <?if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){?>colspan="3"<?}elseif(\backend\components\Helpers::getMyRole() == 5){ ?>colspan="1"<? }else{?>colspan="2"<?}?>>Выданные монеты</th>
                            <th <?if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){?>colspan="4"<?}elseif(\backend\components\Helpers::getMyRole() == 5){ ?>colspan="2"<? }else{?>colspan="3"<?}?>>Возвратные монеты</th>
                            <th>Сальдо</th>
                        </tr>
                        <tr>
                            <td></td>
                            <?
                            if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){
                                ?>
                                <td>Таксопаркам</td>
                                <?
                            }
                            ?>
                            <td>Водителям</td>
                            <?
                            if(\backend\components\Helpers::getMyRole() != 5 ){
                                ?>
                                <td>Компаниям</td>
                                <?
                            }
                            ?>
                            <?
                            if(\backend\components\Helpers::getMyRole() != 5 ){
                                ?>
                                <td>Монеты компаний</td>
                                <?
                            }
                            ?>
                            <td>Монеты водителей</td>
                            <td>Монеты клиентов</td>
                            <?
                            if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){
                            ?>
                                <td>Монеты таксопарков</td>
                                <?
                            }
                            ?>

                            <td></td>
                        </tr>
                        <tr>
                            <td>Итого</td>
                            <?
                            if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){
                                ?>
                                <td id="t"><?=$tp1 + 0?></td>
                                <?
                            }
                            ?>
                            <td id="d"><?=$driver?></td>
                            <?
                            if(\backend\components\Helpers::getMyRole() != 5 ){
                                ?>
                                <td id="c"><?=$to_companies?></td>
                                <td><?=$companies?></td>

                                <?
                            }
                            ?>
                            <td><?=$drivers?></td>
                            <td><?=$users?></td>
                            <?
                            if(\backend\components\Helpers::getMyRole() == 9 OR \backend\components\Helpers::getMyRole() == 3){
                                ?>
                                <td><?=$tps + 0?></td>
                                <?
                            }
                            ?>
                            <?
                            if(\backend\components\Helpers::getMyRole() != 9 AND \backend\components\Helpers::getMyRole() != 3){
                                ?>
                                <td id="saldo"><?=($driver) - ($drivers + $users)?></td>
                                <?
                            }else{
                                ?>
                                <td id="saldo"><?=($tp1 + $driver + $to_companies) - ($companies + $drivers + $users + $tps)?></td>

                                <?
                            }
                            ?>

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
                    url: "/profile/filtration/traffic/",
                    data:{start:start.format('DD.MM.YYYY HH:MM'), end:end.format('DD.MM.YYYY HH:MM')},
                    success: function(data) {

                        document.getElementById('t').innerText = data.t;
                        document.getElementById('d').innerText = data.d;
                        document.getElementById('c').innerText = data.c;

                        var saldo = 0;
                        saldo = data.t + data.d + data.c - (<?=$companies + $drivers + $users + $tps?>);
                        <?
                        if(\backend\components\Helpers::getMyRole() != 9 AND \backend\components\Helpers::getMyRole() != 3){
                        ?>
                        saldo = data.d  - (<?= $drivers + $users?>);

                            <?
                        }else{
                            ?>
                        saldo = data.t + data.d + data.c - (<?=$companies + $drivers + $users + $tps?>);

                        <?
                        }
                        ?>

                        document.getElementById('saldo').innerText = saldo;

                    },
                }).fail(function (xhr) {
                    console.log(xhr. responseText);
                });
            },
        );

    });
</script>