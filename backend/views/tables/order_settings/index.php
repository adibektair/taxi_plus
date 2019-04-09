<?php
use backend\models\TaxiPark;
use backend\models\OrderSettings;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/order_settings/form.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <?
                        $model = OrderSettings::findOne(1);
                        ?>
                        <label class="text-semibold">Радиус охвата водителей при добавлении нового заказа в метрах</label>
                        <input name="meters" type="number" class="form-control" value="<?=$model->meters?>">
                        <br><br>
                        <label class="text-semibold">Время перехода заказа в общий чат в секундах</label>
                        <input name="seconds" type="number" class="form-control" value="<?=$model->seconds?>">

                        <label class="text-semibold">Год выпуска, начиная с которого автомобиль относится к классу комфорт</label>
                        <input name="year" type="number" class="form-control" value="<?=$model->year?>">

                    </div>
                    <div  class = "col-md-12 pt-15">
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Сохранить <i class="icon-check position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
