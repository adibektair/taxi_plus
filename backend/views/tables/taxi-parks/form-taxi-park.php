<?php
use backend\models\WorkingTypes;
use backend\models\Cities;
use backend\models\TaxiParkServices;
use backend\models\Services;
use backend\models\RadialPricing;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<!---LOCAL --->
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
<script type="text/javascript" src="/profile/files/js/mytables/taxi-parks/form.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <input name="id" type="hidden" class="form-control" value = "<?=$model->id?>">
                        <input name="_csrf-backend" type="hidden" class="form-control" value = "<?=Yii::$app->getRequest()->getCsrfToken()?>">

                        <?
                            if(\backend\components\Helpers::getMyRole() == 9){
                                ?>
                                <div class="checkbox">
                                    <label><input name="main" type="checkbox" value="1" <?if($model->main){?>checked<?}?>>Филиал Taxi Plus</label>
                                </div>
                                <?
                            }
                        ?>
                        <?=$this->render('/layouts/modal-components/_input', array('info' => array("Название", "name", "text", $model->name, "true")))?>
<!--                        --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Компания", "company_name", "text", $model->company_name, "true")))?>
                        <label class="text-semibold">Название компании:</label>
                        <input value="<?=$model->company_name?>" type="text" name="company_name" class="form-control">

                        <?if(\backend\components\Helpers::getMyRole() == 9){
                        ?>
                            <?=$this->render('/layouts/modal-components/_input', array('info' => array("Баланс", "balance", "number", $model->balance, "true")))?>
                        <?
                        }?>


                        <?
                        $list = WorkingTypes::find()->all();
                        $cities = Cities::find()->all();
                        if(\backend\components\Helpers::getMyRole() == 3){
                            $cities = Cities::find()->where('id in ('.\backend\components\Helpers::getCitiesString().')')->all();
                        }
                        ?>


                        <label class = "text-semibold">Город:</label>
                        <select name = "city_id" class="select" required ="required">
                            <option value="">Не выбран</option>
                            <? foreach ($cities as $key => $value) { ?>
                                <option <? if ($value->id == $model->city_id) { ?>selected<? } ?> value="<?=$value->id?>"><?=$value->cname?></option>
                            <? } ?>
                        </select>

                        <label style="margin-top: 1em;" class = "text-semibold">Тип оплаты:</label>
                        <select id="type" <? if(Yii::$app->session->get("profile_role") != 9 AND Yii::$app->session->get("profile_role") != 3){?> disabled <?} ?> name = "payment" class="select" required ="required">
                            <option value="">Не выбран</option>
                            <? foreach ($list as $key => $value) { ?>
                                <option <? if ($value->id == $model->type) { ?>selected<? } ?> value="<?=$value->id?>"><?=$value->description?></option>
                            <? } ?>
                        </select>
                        <div id="add">
                            <?
                            if($model->type == 12 OR $model->type == 16){
                                ?>
                                <label class="text-semibold"></label>
                                <label class="text-semibold">Сумма:</label>
<!--                                --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Настройка для статистики фиксированной арендной платой ", "sum", "number", $model->sum, "true")))?>
                                <input type="number" name="sum" value="<?=$model->sum?>" class="form-control">
                                <?
                            }elseif ($model->type == 13 OR $model->type == 17){
                                ?>
<!--                                --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Доля таксопарка в процентах:", "dole_tp", "number", $model->dole_tp, "true")))?>
<!--                                --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Доля водителя в процентах:", "dole_driver", "number", $model->dole_driver, "true")))?>
                                <label class="text-semibold">Доля таксопарка в процентах:</label>
                                <input value="<?=$model->percent?>" type="number" name="dole_tp" class="form-control">
                                <label class="text-semibold">Доля водителя в процентах:</label>
                                <input value="<?=100 - $model->percent?>" type="number" name="dole_driver" class="form-control">
                                <?
                            }elseif ($model->type == 15 or $model->type == 18){
                                ?>
<!--                                --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Километры:", "km", "number", $model->km, "true")))?>
<!--                                --><?//=$this->render('/layouts/modal-components/_input', array('info' => array("Тенге:", "tg", "number", $model->tg, "true")))?>

                                <label class="text-semibold">Километры:</label>
                                <input value="<?=$model->km?>" type="number" name="km" class="form-control">

                                <label class="text-semibold">Тенге:</label>
                                <input value="<?=$model->tg?>" type="number" name="tg" class="form-control">
                                <?
                            }
                            ?>
                        </div>
                    </div>
                    <?
                        if(\backend\components\Helpers::getMyRole() == 5){
                            ?>
                            <div class="col-md-12">

                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Контактный email", "email", "email", $model->email, "true", "example@mail.kz")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Дата подписания контракта", "contract_date", "text", $model->contract_date, "true", "15.08.2018")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Дата окончания контракта", "contract_end", "text", $model->contract_end, "true", "\"15.08.2018\"")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Номер контракта", "contract_number", "text", $model->contract_number, "true", "123456")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Количество собственных автомобилей", "own_cars", "number", $model->own_cars, "true", "15")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Количество арендованных автомобилей", "rent_cars", "number", $model->rent_cars, "true", "10")))?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Количество смешанных автомобилей", "mixed_cars", "number", $model->mixed_cars, "true", "5")))?>

                            </div>

                            <?




                        }

                    ?>

                    <h5>Тарифы:</h5>
                    <?
                    if(\backend\components\Helpers::getMyTaxipark() == 0){
                        $all_services = Services::find()->all();
                    }else{
                        $all_services = Services::find()->where('id in (1,2)')->all();
                    }

                    $my_services = TaxiParkServices::find()->where(['taxi_park_id' => $model->id])->all();
                    ?>
                    <br><br>
                    <div class="col-md-12">
                        <?
                        $shown = array();

                        foreach ($my_services as $key => $value){
                            $currentService = Services::findOne($value->service_id);
                            if(in_array($value->service_id, $shown) == false){
                            ?>
                                <div class="col-md-12" style="border:0.5px solid lightslategray;">
                                <label class="text-semibold">Тариф: <?=$currentService->value?></label>
                                <br><br>
                                <label class="text-semibold">Тип: <? if($value->call_price){ echo "Километражный";}else{echo "Радиальный";}?></label>
                                <br>
                                <?
                                if($value->call_price){
                                    ?>
                                    <label class="text-semibold">Стоимость открытия смены на 6 часов:<?=$value->session_price?></label>
                                    <br><br>
                                    <label class="text-semibold">Стоимость открытия смены на 12 часов: <?=$value->session_price_unlim?></label>
                                    <br><br>
                                    <label class="text-semibold">Коммиссия для водителя в процентах: <?=$value->commision_percent?></label>
                                    <br><br>
                                    <label class="text-semibold">Цена за вызов в тенге: <?=$value->call_price?></label>
                                    <br><br>
                                    <label class="text-semibold">Цена за километр в тенге: <?=$value->km_price?></label>

                                    <?
                                }else{
                                    $array = TaxiParkServices::find()->where(['taxi_park_id' => $model->id])->andWhere(['service_id' => $value->service_id])->orderBy(['meters' => SORT_ASC])->all();
                                    ?>

                                    <label class="text-semibold">Цена за километр в тенге(в случае, если заказ окажется вне круга): <?=$value->km_price?></label>
                                    <br><br>
                                    <label class="text-semibold">Стоимость открытия смены на 6 часов: <?=$value->session_price?></label>
                                    <br><br>
                                    <label class="text-semibold">Стоимость открытия смены на 12 часов: <?=$value->session_price_unlim?></label>
                                    <br><br>
                                    <label class="text-semibold">Коммиссия для водителя в процентах: <?=$value->commision_percent?></label>
                                    <br><br>
                                    <?
                                    foreach ($array as $k => $v){
                                        ?>
                                        <label class="text-semibold">Ширина круга в метрах(диаметр): <?=$v->meters?></label>
                                        <label class="text-semibold">Цена внутри круга в тенге:<?=$v->tenge?></label>
                                        <br><br>
                                        <?
                                    }
                                }
                                ?>
                                    <br>
                                    <a data-id="<?=$model->id?>"  data-info="<?=$value->id?>" class="action-link" href="taxi-parks/tarif"><button class="btn btn-success" type="button">Редактировать</button></a>
                                </div>

                                <?
                                array_push($shown, $value->service_id);

                            }
                            ?>



                                <?
                            }

                        ?>


                    </div>





                    <div class = "col-md-12 mt-15">


                        <div class="text-left" style="padding-top: 2em">
                            <a data-id="<?=$model->id?>" class="action-link" href="taxi-parks/tarif"><button class="btn btn-success" type="button">Добавить тариф</button></a>
                        </div>




                        <div class="text-left" style="padding-top: 2em">
<!--                            <button class="btn btn-default" type="button" onclick="add()"> Добавить услугу</button>-->
                        </div>
                        <div class="text-right">
                            <a href = "<?=Yii::$app->request->cookies['back']?>" class="cs-link btn btn-default">Отмена <i class="icon-x position-right"></i></a>
                            <? if ($model->id != null) { ?>
                                <a href = "#delete" data-id = "<?=$model->id?>" data-table = "taxi_park" data-redirect = "taxi-parks" class="delete btn btn-danger">Удалить <i class="icon-trash-alt position-right"></i></a>
                            <? } ?>
                            <button  type="submit" class="btn btn-primary">Сохранить <i class="icon-check position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>

<script>


    $(document).ready(function() {
        $( "#type" ).change(function() {
            var id = document.getElementById('type').value;
            document.getElementById('add').innerText = '';
            switch(id) {
                case '12':
                    fix();
                    break;
                case '13':
                    dol();
                    break;
                case '14':
                    km();
                    break;
                case '16':
                    fix();
                    break;
                case '17':
                    dol();
                case '18':
                    km();
                default:
                    break;
            }
        });
        function fix() {
            var div = document.getElementById('add');
            var input = document.createElement('input');
            input.type = 'number';
            input.name = 'fix';
            input.classList.toggle('form-control');
            var label = document.createElement('label');
            label.classList.toggle('text-semibold');
            label.innerText = "Настройка для статистики фиксированной арендной платой \nСумма:";
            label.setAttribute('style', 'margin-top: 1em;');
            div.appendChild(label);
            div.appendChild(input);
        }
        function dol() {
            var div = document.getElementById('add');
            var input = document.createElement('input');
            input.type = 'number';
            input.name = 'dole_tp';
            input.classList.toggle('form-control');
            var label = document.createElement('label');
            label.classList.toggle('text-semibold');
            label.innerText = "Доля таксопарка в процентах:";
            label.setAttribute('style', 'margin-top: 1em;');

            var input1 = document.createElement('input');
            input1.type = 'number';
            input1.name = 'dole_driver';
            input1.classList.toggle('form-control');
            var label1 = document.createElement('label');
            label1.classList.toggle('text-semibold');
            label1.innerText = "Доля водителя в процентах:";
            label1.setAttribute('style', 'margin-top: 1em;');

            div.appendChild(label);
            div.appendChild(input);
            div.appendChild(label1);
            div.appendChild(input1);
        }

        function km() {
            var div = document.getElementById('add');
            var input = document.createElement('input');
            input.type = 'number';
            input.name = 'km';

            input.classList.toggle('form-control');
            var label = document.createElement('label');
            label.classList.toggle('text-semibold');
            label.innerText = "Километры:";
            label.setAttribute('style', 'margin-top: 1em;');

            var input1 = document.createElement('input');
            input1.type = 'number';
            input1.name = 'tg';
            input1.classList.toggle('form-control');
            var label1 = document.createElement('label');
            label1.classList.toggle('text-semibold');
            label1.innerText = "Тенге:";
            label1.setAttribute('style', 'margin-top: 1em;');

            div.appendChild(label);
            div.appendChild(input);
            div.appendChild(label1);
            div.appendChild(input1);
        }

    });
</script>


<script>


    $(document).ready(function() {
        $("[name='access']").bootstrapSwitch();
    });

    function deleteDiv(rand){
        document.getElementById('bigDiv' + rand).remove();
    }

    function appendCircle(rand){
        var random =  Math.floor(Math.random() * 30) + 1;
        var radiusDiv = document.createElement('div');
        radiusDiv.classList.toggle('col-md-6');
        var tengeDiv = document.createElement('div');
        tengeDiv.classList.toggle('col-md-6');

        var radiusLabel = document.createElement("label");
        radiusLabel.classList.toggle("text-semibold");
        radiusLabel.innerHTML = "Ширина круга в метрах(диаметр):";
        var tengeLabel = document.createElement("label");
        tengeLabel.classList.toggle("text-semibold");
        tengeLabel.innerHTML = "Цена внутри круга в тенге";

        var radiusInput = document.createElement("input");
        radiusInput.name = "meters[" + rand + "][" + random + "]";
        radiusInput.classList.toggle("form-control");
        radiusInput.required = true;
        radiusInput.type = 'number';
        radiusInput.placeholder = "500";
        var tengeInput = document.createElement("input");
        tengeInput.name = "tenge[" + rand + "][" + random + "]";
        tengeInput.classList.toggle("form-control");
        tengeInput.required = true;
        tengeInput.placeholder = "600";
        tengeInput.type = 'number';

        radiusDiv.appendChild(radiusLabel);
        radiusDiv.appendChild(radiusInput);
        tengeDiv.appendChild(tengeLabel);
        tengeDiv.appendChild(tengeInput);

        document.getElementById('radial' + rand).appendChild(radiusDiv);
        document.getElementById('radial' + rand).appendChild(tengeDiv);

    }

    function appendKm(rand) {

        var kilometerDiv = document.createElement('div');
        kilometerDiv.classList.toggle('col-md-12');
        kilometerDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em;");
        kilometerDiv.id = 'km' + rand;
        var callDiv = document.createElement('div');
        callDiv.classList.toggle('col-md-6');
        var kmDiv = document.createElement('div');
        kmDiv.classList.toggle('col-md-6');

        var callLabel = document.createElement("label");
        callLabel.classList.toggle("text-semibold");
        callLabel.innerHTML = "Цена за вызов в тенге:";
        var kmLabel = document.createElement("label");
        kmLabel.classList.toggle("text-semibold");
        kmLabel.innerHTML = "Цена за километр в тенге:";

        var callInput = document.createElement("input");
        callInput.name = "call[" + rand + "]";
        callInput.classList.toggle("form-control");
        callInput.required = true;
        callInput.type = 'number';
        callInput.placeholder = "150";
        var kmInput = document.createElement("input");
        kmInput.name = "km[" + rand + "]";
        kmInput.classList.toggle("form-control");
        kmInput.required = true;
        kmInput.type = 'number';
        kmInput.placeholder = "100";

        callDiv.appendChild(callLabel);
        callDiv.appendChild(callInput);
        kmDiv.appendChild(kmLabel);
        kmDiv.appendChild(kmInput);

        kilometerDiv.appendChild(callDiv);
        kilometerDiv.appendChild(kmDiv);

        var bigDiv = document.getElementById('bigDiv' + rand);
        bigDiv.appendChild(kilometerDiv);

        $('[name="access[' + rand + ']"]').bootstrapSwitch();
        $('[name="service[' + rand + ']"]').select2();

    }




    function appendRadial(rand) {


        var bigDiv = document.getElementById('bigDiv' + rand);
        var label = document.createElement('label');
        label.classList.toggle('text-semibold');
        label.innerText = 'Метры:';
        label.id = 'meters' + rand;
        bigDiv.appendChild(label);
        // bigDiv.appendChild(input);

        var geolocation = ymaps.geolocation,
            myMap = new ymaps.Map("bigDiv" + rand, {
                center: [43.24, 76.92],
                zoom: 10,
                controls: []
            });
        var myCircle = new ymaps.Circle([

            [43.24, 76.92],

            10000
        ], {}, {
            fillColor: "#DB709377",
            strokeColor: "#990066",
            strokeOpacity: 0.8,
            strokeWidth: 5
        });

        myMap.geoObjects.add(myCircle);

        myCircle.editor.startEditing();
        geolocation.get({
            provider: 'yandex',
            mapStateAutoApply: true
        }).then(function (result) {
            // result.geoObjects.options.set('preset', 'islands#redCircleIcon');
            // result.geoObjects.get(0).properties.set({
            //     balloonContentBody: 'Мое местоположение'
            // });
            // myMap.geoObjects.add(result.geoObjects);

        });
        // myCircle.geometry.setRadius(document.getElementById('meters').value);

        myCircle.events.add('geometrychange', function () {
            var i = document.getElementById('meters' + rand);
            i.innerText = 'Метры: ' + myCircle.geometry.getRadius();
        });



        var random =  Math.floor(Math.random() * 50000) + (Math.random() * 124) + (Math.random() * 110);
        var radialDiv = document.createElement('div');
        radialDiv.classList.toggle('col-md-12');
        radialDiv.id = 'radial' + rand;
        radialDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em;");
        var radiusDiv = document.createElement('div');
        radiusDiv.classList.toggle('col-md-3');
        var tengeDiv = document.createElement('div');
        tengeDiv.classList.toggle('col-md-3');
        var kmDiv = document.createElement('div');
        kmDiv.classList.toggle('col-md-3');
        var buttonDiv = document.createElement('div');
        buttonDiv.classList.toggle('col-md-3');



        var kmLabel = document.createElement("label");
        kmLabel.classList.toggle("text-semibold");
        kmLabel.innerHTML = "Цена за километр в тенге:";

        var kmInput = document.createElement("input");
        kmInput.name = "km[" + rand + "]";
        kmInput.classList.toggle("form-control");
        kmInput.required = true;
        kmInput.type = 'number';
        kmInput.placeholder = "100";


        var radiusLabel = document.createElement("label");
        radiusLabel.classList.toggle("text-semibold");
        radiusLabel.innerHTML = "Ширина круга в метрах(диаметр):";
        var tengeLabel = document.createElement("label");
        tengeLabel.classList.toggle("text-semibold");
        tengeLabel.innerHTML = "Цена внутри круга в тенге";

        var radiusInput = document.createElement("input");
        radiusInput.name = "meters[" + rand + "][" + random + "]";
        radiusInput.classList.toggle("form-control");
        radiusInput.required = true;
        radiusInput.type = 'number';
        radiusInput.placeholder = "500";
        var tengeInput = document.createElement("input");
        tengeInput.name = "tenge[" + rand + "][" + random + "]";
        tengeInput.classList.toggle("form-control");
        tengeInput.required = true;
        tengeInput.placeholder = "600";
        tengeInput.type = 'number';

        var a = '<button type="button" class="btn btn-primary" onclick="appendCircle(' + rand +')"> Добавить круг </button>';

        radiusDiv.appendChild(radiusLabel);
        radiusDiv.appendChild(radiusInput);
        tengeDiv.appendChild(tengeLabel);
        tengeDiv.appendChild(tengeInput);
        kmDiv.appendChild(kmLabel);
        kmDiv.appendChild(kmInput);

        radialDiv.appendChild(radiusDiv);
        radialDiv.appendChild(tengeDiv);
        radialDiv.appendChild(kmDiv);
        buttonDiv.classList.toggle('col-md-6');
        buttonDiv.insertAdjacentHTML('beforeend', a);
        buttonDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em;");
        radialDiv.appendChild(buttonDiv);

        var bigDiv = document.getElementById('bigDiv' + rand);
        bigDiv.appendChild(radialDiv);
    }

    function trya(id) {
        var switc = document.getElementById(id);
        var is_km = $('#' + id).bootstrapSwitch('state');
        var bigDiv = document.getElementById('bigDiv' + id);

        if(is_km){
            var radial = document.getElementById('radial' + id).remove();
            // bigDiv.removeChild(radial);
            appendKm(id);
        }else{
            var km = document.getElementById('km' + id).remove();
            // bigDiv.removeChild(km);
            appendRadial(id);

        }
    }
    var rands = [];


    function removeOptional(id) {
        var serviceid = document.getElementById('mySelect'+ id).value;
        console.log(serviceid);
        if(serviceid == 2 || serviceid == 1){

        }else{
            var div = document.getElementById('lim'+id);
            var div1 = document.getElementById('unlim'+id);
            if(div != null && div1 != null){
                var mainDiv = document.getElementById('sessionDiv'+id);
                mainDiv.removeChild(div);
                mainDiv.removeChild(div1);
            }

        }

    }
    function add() {
        $.ajax({url: "services/",
            success: function(result) {
                var array = result.data;
                var rand = result.rand;

                var sw = '<input id="' + rand + '" type="checkbox" name="access[' + rand + ']" checked data-on-color="success" data-off-color="danger" data-on-text="Километражный" data-off-text="Радиальный" onchange="trya('+ rand +')" class="switch" style="padding-top: 5em;">';
                var myDiv = document.getElementById("addings");

                var bigDiv = document.createElement('div');
                bigDiv.classList.toggle('col-md-12');
                bigDiv.id = 'bigDiv' + rand;
                bigDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em; margin-top: 2em; border:0.5px solid lightslategray;");

                var typeLabel = document.createElement('label');
                typeLabel.classList.toggle("text-semibold");
                typeLabel.innerText = 'Режим работы:';

                var selectList = document.createElement("select");
                selectList.classList.toggle("select");
                selectList.id = "mySelect" + rand;
                selectList.name = "service[" + rand + "]";
                for (var i = 0; i < array.length; i++) {
                    var option = document.createElement("option");
                    option.value = array[i].id;
                    option.text = array[i].value;
                    selectList.appendChild(option);
                }

                selectList.setAttribute('onchange', 'removeOptional('+ rand +')');
                var selectDiv = document.createElement('div');
//                selectDiv.id = rand;
                selectDiv.classList.toggle('col-md-12');
                selectDiv.appendChild(typeLabel);
                selectDiv.appendChild(selectList);
                var subSelectDiv = document.createElement('div');
                subSelectDiv.classList.toggle('col-md-6');
                subSelectDiv.insertAdjacentHTML('beforeend', sw);
                subSelectDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em;");

                selectDiv.appendChild(subSelectDiv);



                var sessionDiv = document.createElement('div');
                sessionDiv.classList.toggle('col-md-12');
                sessionDiv.id = 'sessionDiv' + rand;

                var limDiv = document.createElement('div');
                limDiv.classList.toggle('col-md-4');
                limDiv.id = 'lim' + rand;
                var limLabel = document.createElement("label");
                limLabel.classList.toggle("text-semibold");
                limLabel.innerHTML = "Стоимость открытия смены на 6 часов:";
                var limInput = document.createElement("input");
                limInput.name = "session_price[" + rand + "]";
                limInput.classList.toggle("form-control");
                limInput.placeholder = "1200";
                limInput.required = true;
                limInput.type = 'number';

                limDiv.appendChild(limLabel);
                limDiv.appendChild(limInput);

                var unlimDiv = document.createElement('div');
                unlimDiv.classList.toggle('col-md-4');
                unlimDiv.id = 'unlim' + rand;
                var unlimLabel = document.createElement("label");
                unlimLabel.classList.toggle("text-semibold");
                unlimLabel.innerHTML = "Стоимость открытия смены на 12 часов:";
                var unlimInput = document.createElement("input");
                unlimInput.name = "session_price_unlim[" + rand + "]";
                unlimInput.classList.toggle("form-control");
                unlimInput.placeholder = "2000";
                unlimInput.required = true;
                unlimInput.type = 'number';
                unlimDiv.appendChild(unlimLabel);
                unlimDiv.appendChild(unlimInput);

                var percentDiv = document.createElement('div');
                percentDiv.classList.toggle('col-md-4');
                var percentLabel = document.createElement("label");
                percentLabel.classList.toggle("text-semibold");
                percentLabel.innerHTML = "Коммиссия для водителя в процентах:";
                var percentInput = document.createElement("input");
                percentInput .name = "percent[" + rand + "]";
                percentInput .classList.toggle("form-control");
                percentInput .required = true;
                percentInput.placeholder = "8";
                percentInput .type = 'number';
                percentDiv.appendChild(percentLabel);
                percentDiv.appendChild(percentInput);



                sessionDiv.appendChild(limDiv);
                sessionDiv.appendChild(unlimDiv);
                sessionDiv.appendChild(percentDiv);

                var kilometerDiv = document.createElement('div');
                kilometerDiv.classList.toggle('col-md-12');
                kilometerDiv.setAttribute("style", "padding-top: 2em; padding-bottom: 2em;");
                kilometerDiv.id = 'km' + rand;
                var callDiv = document.createElement('div');
                callDiv.classList.toggle('col-md-6');
                var kmDiv = document.createElement('div');
                kmDiv.classList.toggle('col-md-6');

                var callLabel = document.createElement("label");
                callLabel.classList.toggle("text-semibold");
                callLabel.innerHTML = "Цена за вызов в тенге:";
                var kmLabel = document.createElement("label");
                kmLabel.classList.toggle("text-semibold");
                kmLabel.innerHTML = "Цена за километр в тенге:";

                var callInput = document.createElement("input");
                callInput.name = "call[" + rand + "]";
                callInput.classList.toggle("form-control");
                callInput.required = true;
                callInput.type = 'number';
                callInput.placeholder = "150";
                var kmInput = document.createElement("input");
                kmInput.name = "km[" + rand + "]";
                kmInput.classList.toggle("form-control");
                kmInput.required = true;
                kmInput.type = 'number';
                kmInput.placeholder = "100";

                callDiv.appendChild(callLabel);
                callDiv.appendChild(callInput);
                kmDiv.appendChild(kmLabel);
                kmDiv.appendChild(kmInput);


                kilometerDiv.appendChild(callDiv);
                kilometerDiv.appendChild(kmDiv);
//                kilometerDiv.appendChild(percentDiv);

                bigDiv.appendChild(selectDiv);
                bigDiv.appendChild(sessionDiv);
                bigDiv.appendChild(kilometerDiv);
                var deleteDiv = document.createElement('div');
                deleteDiv.classList.toggle('text-right');
                deleteDiv.setAttribute("style", "padding-top: 3em;");
                var a = '<button type="button" class="btn btn-danger" onclick="deleteDiv(' + rand +')">Удалить</button>';
                deleteDiv.insertAdjacentHTML('beforeend', a);
                bigDiv.appendChild(deleteDiv);
//                bigDiv.appendChild(radialDiv);
                myDiv.appendChild(bigDiv);
                $('[name="access[' + rand + ']"]').bootstrapSwitch();
                $('[name="service[' + rand + ']"]').select2();


            }
        });


    }


</script>