<?php
use backend\models\Orders;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<!--<script type="text/javascript" src="/profile/files/js/mytables/add_order/form.js"></script>-->
<!------->
<?=$this->render("/layouts/header/_header", array("model" => $model))?>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div id="map" style="padding-left: 1em; padding-right: 1em; padding-bottom: 1em; padding-top: 1em; height: 600px "></div>

                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" >
    var token = '<?=Yii::$app->session->get('token')?>';
    var laa = null;
    var lab = null;
    var loa = null;
    var lob = null;

    setTimeout(function(){
        ymaps.ready(init);

        function init() {
            var geolocation = ymaps.geolocation,
                myMap = new ymaps.Map('map', {
                    center: [55, 34],
                    zoom: 9,
                    controls: []
                });

            geolocation.get({
                provider: 'yandex',
                mapStateAutoApply: true
            }).then(function (result) {
                result.geoObjects.options.set('preset', 'islands#redCircleIcon');
                result.geoObjects.get(0).properties.set({
                    balloonContentBody: 'Мое местоположение'
                });
                myMap.geoObjects.add(result.geoObjects);
            });


            var DELIVERY_TARIFF = 20,
                // Минимальная стоимость.
                MINIMUM_COST = 500;
                // myMap = new ymaps.Map('map', {
                //     center: [60.906882, 30.067233],
                //     zoom: 9,
                //     controls: []
                // }),
                // Создадим панель маршрутизации.
               var routePanelControl = new ymaps.control.RoutePanel({
                    options: {
                        showHeader: true,
                        title: 'Добавить заказ',
                        visible: true
                    }
                }),
                zoomControl = new ymaps.control.ZoomControl({
                    options: {
                        size: 'big',
                        float: 'none',
                        position: {
                            bottom: 145,
                            right: 10
                        }
                    }
                });




            // Пользователь сможет построить только автомобильный маршрут.
            routePanelControl.routePanel.options.set({
                types: {auto: true}
            });



            myMap.controls.add(routePanelControl).add(zoomControl);

            // Получим ссылку на маршрут.
            routePanelControl.routePanel.getRouteAsync().then(function (route) {

                // Зададим максимально допустимое число маршрутов, возвращаемых мультимаршрутизатором.
                route.model.setParams({results: 1}, true);

                // Повесим обработчик на событие построения маршрута.
                route.model.events.add('requestsuccess', function () {
                    console.log('aa');
                    var activeRoute = route.getActiveRoute();

                    if (activeRoute) {
                        laa = route.model.properties._data.waypoints[0].coordinates[1];
                        lab = route.model.properties._data.waypoints[1].coordinates[1];
                        loa = route.model.properties._data.waypoints[0].coordinates[0];
                        lob = route.model.properties._data.waypoints[1].coordinates[0];
                        // var token = //Yii::$app->session->get('token');
                        var result = null;
                        $.ajax({
                            dataType: "json",
                            async: false,
                            type: "POST",
                            global: false,
                            url: "/profile/account/get-price/",
                            data: {token: token, longitude_a: loa, latitude_a: laa,
                                longitude_b: lob, latitude_b: lab, type: 1},
                            success: function (data) {
                                console.log(data);
                                var price = data.price_list[0].price;
                                var length = route.getActiveRoute().properties.get("distance");
                                balloonContentLayout = ymaps.templateLayoutFactory.createClass(
                                    '<span>Расстояние: ' + length.text + '.</span><br/>' +
                                    '<span style="font-weight: bold; font-style: italic"> Стоимость: ' + price + ' tg. </span>' + '<br>' +
                                    '<label class="text-semibold">Введите сотовый номер телефона клиента (пример: 77005554797)</label>' + '<br>' +
                                    '<input class="form-control" name="phone" id="phone" placeholder="77005554797">' + '<br>' +
                                    '<button type="button" onclick="makeOrder(laa, loa, lab, lob, token)" class="btn btn-primary"> Oформить заказ </button>');
                                // Зададим этот макет для содержимого балуна.
                                route.options.set('routeBalloonContentLayout', balloonContentLayout);
                                // Откроем балун.
                                activeRoute.balloon.open();
//
                            }
                        });

                        // Получим протяженность маршрута.
                        var length = route.getActiveRoute().properties.get("distance");

                    }
                });

            });

        }




    }, 2000);

    function makeOrder(laa, loa, lab, lob, tok) {
        var phone = document.getElementById('phone').value;
        if(phone == "" || phone == null){
            alert('Введите номер телефона')
        }
        $.ajax({
            dataType: "json",
            type: "POST",
            url: "/profile/account/make-order/",
            data: {token: tok, longitude_a: loa, latitude_a: laa,
                longitude_b: lob, latitude_b: lab, service_id: 1, phone: phone, dispatcher: true},
            success: function (data) {
                console.log(data);
                if(data.state == 'success'){
                    swal({
                        title: 'Заказ успешно добавлен!',
                        timer: 900,
                        type: 'success',
                        showConfirmButton: false
                    });
                    // $('#add_order').trigger('click');
                    // location.reload();
                    laa = null;
                    loa = null;
                    lob = null;
                    lab = null;
                    // init();

                }else{
                    alert(data.state)
                }

            },
            default: function (data) {
                console.log(data);
            }
        });
    }

</script>