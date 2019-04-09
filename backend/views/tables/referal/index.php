<?php
use backend\models\TaxiPark;
use backend\models\Category;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/referal/form.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <?
                        $list = \backend\models\Services::find()->all();
                        foreach ($list as $k => $v){
                            ?>
                            <div class="col-md-12" style="outline: 0.5px solid black; margin-bottom: 2em; padding-bottom: 1em;">
                                <h5><?=$v->value?>:</h5>
                                <label class="text-semibold">Количество монет, снимаемых с водителя в случае, если у него открыта 6-часовая смена:</label>
                                <input name="six[<?=$v->id?>]" type="number" value="<?=$v->six_price?>" class="form-control">

                                <label class="text-semibold">Количество монет, снимаемых с водителя в случае, если у него открыта 12-часовая смена:</label>
                                <input name="twelve[<?=$v->id?>]" type="number" value="<?=$v->twelve_price?>" class="form-control">

                                <label class="text-semibold">Процент от процента с каждого заказа водителя, если у него не открыта смена:</label>
                                <input name="limitless[<?=$v->id?>]" type="number" value="<?=$v->limitless_percent?>" class="form-control">

                                <label class="text-semibold">Процент от бонуса для клиента(в процентах):</label>
                                <input name="percent[<?=$v->id?>]" type="number" value="<?=$v->referal_percent?>" class="form-control">
                                <br>
                                <span class="label label-info">Процент от бонуса для реферала равен 100% минус та цифра, которую Вы ввели выше</span>
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
