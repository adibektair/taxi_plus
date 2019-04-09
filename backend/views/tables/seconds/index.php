<?php
use backend\models\News;
use backend\models\Category;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/seconds/form.js"></script>
<!------->

<?=$this->render("/layouts/header/_header", array("model" => $model))?>

<div class="content">
    <form id = "form">
        <div class="panel panel-flat">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <input name="_csrf-backend" type="hidden" class="form-control" value = "<?=Yii::$app->getRequest()->getCsrfToken()?>">

                        <?
                        $model = \backend\models\Seconds::findOne(['id' => 1]);
                        ?>
                        <label class="text-semibold">Время перехода заказа из ленты заказов одного таксопарка в общий чат (в секундах)</label>
                        <input type="number" name="seconds" class="form-control" value="<?=$model->seconds?>">
                    </div>


                    <div class = "col-md-12">
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Сохранить <i class="icon-check position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
