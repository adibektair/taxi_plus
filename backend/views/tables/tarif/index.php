<?php
use backend\models\TaxiPark;
use backend\models\Category;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/tarif/form.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <?
                        $list = \backend\models\OrderTypes::find()->all();
                        foreach ($list as $k => $v){
                            ?>
                            <div class="col-md-12" style="outline: 0.5px solid black; margin-bottom: 2em; padding-bottom: 1em;">
                                <h5><?=$v->type?>:</h5>
                                <label class="text-semibold">Цена для водителя на доступ к заказам клиентов на 1 час (в монетах):</label>
                                <input name="hour[<?=$v->id?>]" type="number" value="<?=$v->hour_price?>" class="form-control">
                                <label class="text-semibold">Цена для водителя на публикацию собственного заказа в чате клиентов(в монетах):</label>
                                <input name="publish[<?=$v->id?>]" type="number" value="<?=$v->publish_price?>" class="form-control">

                            </div>

                            <?
                        }

                        ?>
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
