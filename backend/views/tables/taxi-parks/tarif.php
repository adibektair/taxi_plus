<?php
use backend\models\TaxiPark;
use backend\models\TaxiParkServices;
use backend\models\Services;

?>
<!-- ENGINE -->
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/tarif/forms.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <input name="id" type="hidden" class="form-control" value = "<?=$info?>">
                    <input name="service_id" type="hidden" class="form-control" value = "<?=$model->service_id?>">

                    <input name="_csrf-backend" type="hidden" class="form-control" value = "<?=Yii::$app->getRequest()->getCsrfToken()?>">


                    <?php
                        $tp = TaxiPark::findOne($info);
                        ?>
                        <h2>Таксопарк: <?= $tp->name?></h2>
                        <?
                        if($model){

                            $service = Services::findOne($model->service_id);
                            ?>
                            <h3>Редактировать тариф: <?= $service->value?></h3>
                            <?
                        }else{
                            ?>
                            <h3>Добавить тариф</h3>
                            <?
                            $arr = TaxiParkServices::find()->where('taxi_park_id = '. $tp->id)->all();
                            $massiv = '';
                            foreach ($arr as $k => $v){
                                $massiv .= $v->service_id . ', ';
                            }

                            if ($tp->main != 1){
                                $massiv .= "3, 4, 5, ";
                            }
                            $massiv.= 0;
                            $services = Services::find()->where('id not in (' . $massiv .')')->all();

                            ?>
                                <label class="text-semibold">Тариф:</label>
                                <select name = "service_id" class="select" required ="required">
                                    <?
                                    ?>
                                    <option>Не выбрано</option>
                                    <?
                                    foreach ($services as $key => $value){

                                    ?>
                                        <option value="<?=$value->id?>"><?=$value->value?></option>
                                        <?
                                    }

                                    ?>
                                </select>
                            </div>
                            <?
                        }
                    ?>

                    <label class="text-semibold">Комиссия для водителя в процентах:</label>
                    <input type="number" class="form-control" name="percent" value="<?=$model->commision_percent?>">

                    <label class="text-semibold">Стоимость открытия смены на 6 часов:</label>
                    <input type="number" class="form-control" name="session_price" value="<?=$model->session_price?>">


                    <label class="text-semibold">Стоимость открытия смены на 12 часов:</label>
                    <input type="number" class="form-control" name="session_price_unlim" value="<?=$model->session_price_unlim?>">


                    <div class="col-md-12">
                        <label class="text-semibold">Тип:</label>
                        <select onchange="changedState()" name = "type" id="select" class="select" required ="required">
                            <option <? if ($model->call_price) { ?>selected<? } ?> value="1">Радиальный</option>
                            <option <? if ($model->call_price or $model == null) { ?>selected<? } ?> value="0">Километражный</option>

                        </select>
                    </div>

                    <div id="mapDiv"></div>
                    <?
                    if($model){
                        if($model->call_price){
                            ?>
                            <div id="dynamic" class="col-md-12">
                                <label class="text-semibold">Цена за вызов такси:</label>
                                <input type="number" class="form-control" name="call_price" value="<?=$model->call_price?>">

                                <label class="text-semibold">Цена за километр:</label>
                                <input type="number" class="form-control" name="km_price" value="<?=$model->km_price?>">

                            </div>
                            <?
                        }else{
                            $array = TaxiParkServices::find()->where(['taxi_park_id' => $model->taxi_park_id])->andWhere(['service_id' => $model->service_id])->orderBy(['meters' => SORT_ASC])->all();
                            ?>

                            <div id="dynamic" class="col-md-12">
                                <label class="text-semibold">Цена за километр, если точка Б выйдет за рамки кругов:</label>
                                <input type="number" class="form-control" name="km_price" value="<?=$model->km_price?>">
                                <?
                            foreach ($array as $key => $value){

                                $rand = rand(0, 10000000);
                                ?>

                                    <div class="col-md-6">
                                        <label class="text-semibold">Ширина круга:</label>
                                        <input type="number" class="form-control" name="meters[<?=$rand?>]" value="<?=$value->meters?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-semibold">Цена внутри данного круга:</label>
                                        <input type="number" class="form-control" name="tenge[<?=$rand?>]" value="<?=$value->tenge?>">
                                    </div>

                                <button type="button" class="btn btn-primary" style=" position: absolute; right: 0; bottom: 0;" onclick="addCircle()"> Добавить круг </button>
                                <?
                            }
                            ?>
                            </div>
                            <?
                        }
                    }else{
                        ?>

                        <div id="dynamic" class="col-md-12">
                            <label class="text-semibold">Цена за вызов такси:</label>
                            <input type="number" class="form-control" name="call_price" value="<?=$model->call_price?>">

                            <label class="text-semibold">Цена за километр:</label>
                            <input type="number" class="form-control" name="km_price" value="<?=$model->km_price?>">

                        </div>

                        <?
                    }
                    ?>



                    <div  class = "col-md-12 pt-15">
                        <div class="text-right">
                            <a href = "<?=Yii::$app->request->cookies['back']?>" class="cs-link btn btn-default">Отмена <i class="icon-x position-right"></i></a>
                            <? if ($model->id != null) { ?>
                                <a href = "#delete" data-id = "<?=$model->id?>" data-table = "system_users" data-redirect = "tadmins" class="delete btn btn-danger">Удалить <i class="icon-trash-alt position-right"></i></a>
                            <? } ?>
                            <button type="submit" class="btn btn-primary">Сохранить <i class="icon-check position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>


<script>
    function changedState() {
        var e = document.getElementById("select");
        var value = e.options[e.selectedIndex].value;
        console.log(value);
        var dynamicDiv = document.getElementById('dynamic');
        dynamicDiv.innerHTML = '';
        var newDiv = document.createElement('div');
        if(value == 0){
            var div1 = document.createElement('div');
            div1.classList.toggle('col-md-6');
            var div2 = document.createElement('div');
            div2.classList.toggle('col-md-6')
            var input1 = document.createElement('input');
            input1.classList.toggle('form-control');
            input1.name = 'call_price';
            var input2 = document.createElement('input');
            input2.classList.toggle('form-control');
            input2.name = 'km_price';
            var label1 = document.createElement('label');
            label1.classList.toggle('text-semibold');
            label1.innerText = 'Цена за вызов такси:';
            var label2 = document.createElement('label');
            label2.classList.toggle('text-semibold');
            label2.innerText = 'Цена за километр:';
            div1.appendChild(label1);
            div1.appendChild(input1)
            div2.appendChild(label2);
            div2.appendChild(input2);
            newDiv.appendChild(div1);
            newDiv.appendChild(div2);
        }else{


            // var geolocation = ymaps.geolocation,
            //     myMap = new ymaps.Map("mapDiv", {
            //         center: [43.24, 76.92],
            //         zoom: 9,
            //         controls: []
            //     });
            // var myCircle = new ymaps.Circle([
            //     [43.24, 76.92],
            //     10000
            // ], {}, {
            //     fillColor: "#DB709377",
            //     strokeColor: "#990066",
            //     strokeOpacity: 0.8,
            //     strokeWidth: 5
            // });
            // myMap.geoObjects.add(myCircle);
            // myCircle.editor.startEditing();
            // geolocation.get({
            //     provider: 'yandex',
            //     mapStateAutoApply: true
            // }).then(function (result) {
            //     // result.geoObjects.options.set('preset', 'islands#redCircleIcon');
            //     // result.geoObjects.get(0).properties.set({
            //     //     balloonContentBody: 'Мое местоположение'
            //     // });
            //     // myMap.geoObjects.add(result.geoObjects);
            //
            // });
            //
            // myCircle.events.add('geometrychange', function () {
            //     var i = document.getElementById('metersLabel');
            //     i.innerText = 'Метры: ' + myCircle.geometry.getRadius();
            // });


            // km price
            var input = document.createElement('input');
            input.classList.toggle('form-control');
            input.name = 'km_price';
            var label = document.createElement('label');
            label.classList.toggle('text-semibold');
            label.innerText = 'Цена за километр, если точка Б выйдет за рамки кругов:';

            var div1 = document.createElement('div');
            div1.classList.toggle('col-md-6');
            var div2 = document.createElement('div');
            div2.classList.toggle('col-md-6');
            var input1 = document.createElement('input');
            input1.classList.toggle('form-control');
            input1.name = 'meters[123123]';
            var input2 = document.createElement('input');
            input2.classList.toggle('form-control');
            input2.name = 'tenge[123123]';
            var label1 = document.createElement('label');
            label1.classList.toggle('text-semibold');
            label1.innerText = 'Ширина круга:';
            var label2 = document.createElement('label');
            label2.classList.toggle('text-semibold');
            label2.innerText = 'Цена внутри данного круга:';


            div1.appendChild(label1);
            div1.appendChild(input1);
            div2.appendChild(label2);
            div2.appendChild(input2);
            newDiv.appendChild(label);
            newDiv.appendChild(input);
            newDiv.appendChild(div1);
            newDiv.appendChild(div2);

            var a = '<button type="button" class="btn btn-primary" style="position: absolute; right: 0; bottom: 0;" onclick="addCircle()"> Добавить круг </button>';
            // var buttonDiv = document.createElement('div');
            // buttonDiv.classList.toggle('col-md-6');
            // button.setAttribute("style", "");
            // newDiv.appendChild(buttonDiv);
        }
        dynamicDiv.appendChild(newDiv);
        if(value == 1){
            newDiv.insertAdjacentHTML('beforeend', a);
        }
    }
    function addCircle() {
        var random = Math.random() * (100000000 - 0) + 1;
        var dynamicDiv = document.getElementById('dynamic');
        var newDiv = document.createElement('div');
        var div1 = document.createElement('div');
        div1.classList.toggle('col-md-6');
        var div2 = document.createElement('div');
        div2.classList.toggle('col-md-6');

        var input1 = document.createElement('input');
        input1.classList.toggle('form-control');
        input1.name = 'meters[' + random + ']';
        var input2 = document.createElement('input');
        input2.classList.toggle('form-control');
        input2.name = 'tenge[' + random +']';
        var label1 = document.createElement('label');
        label1.classList.toggle('text-semibold');
        label1.innerText = 'Ширина круга:';
        var label2 = document.createElement('label');
        label2.classList.toggle('text-semibold');
        label2.innerText = 'Цена внутри данного круга:';
        div1.appendChild(label1);
        div1.appendChild(input1);
        div2.appendChild(label2);
        div2.appendChild(input2);
        newDiv.appendChild(div1);
        newDiv.appendChild(div2);
        dynamicDiv.appendChild(newDiv);
    }
</script>