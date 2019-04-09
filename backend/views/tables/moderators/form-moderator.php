<?php
use backend\models\News;
use backend\models\Category;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/moderators/form.js"></script>
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

                            <?=$this->render('/layouts/modal-components/_input', array('info' => array("Имя", "first_name", "text", $model->first_name, "true")))?>
                            <?=$this->render('/layouts/modal-components/_input', array('info' => array("Фамилия", "last_name", "text", $model->last_name, "true")))?>
                            <?=$this->render('/layouts/modal-components/_input', array('info' => array("email", "email", "text", $model->email, "true")))?>

                        <?
                            if($model->id == null){ ?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Пароль", "password", "text", $model->password, "true")))?>
                            <?}
                        ?>


                        <?=$this->render('/layouts/modal-components/_input', array('info' => array("Телефон", "phone", "text", $model->phone, "true")))?>

                    </div>

                <?
                if($model->id == null){
                    if(Yii::$app->session->get('last_moder_city_id') != null){
                        $list = \backend\models\TaxiPark::find()->andWhere('city_id in ('. Yii::$app->session->get('last_moder_city_id') .')')->all();
                        if(\backend\components\Helpers::getMyRole() == 3){
                            $list = \backend\models\TaxiPark::find()->innerJoin('cities', 'cities.id = taxi_park.city_id')->andWhere(\backend\components\Helpers::getCitiesCondition())->all();
                        }
                    }else{
                        $list = \backend\models\TaxiPark::find()->all();
                    }

                }else{
                    $list = \backend\models\TaxiPark::find()->all();

                }


                ?>
                    <?
                    if(\backend\components\Helpers::getMyRole() == 9){
                        ?>
                        <div class="col-md-6" style="padding-top: 2em; padding-bottom: 2em;">
                            <label class = "text-semibold">Таксопарк:</label>
                            <select id="model" name = "taxi_park" class="select" required ="required">
                                <option value="">Не выбран</option>
                                <? foreach ($list as $key => $value) { ?>
                                    <option <?if($model->taxi_park_id == $value->id){?>selected<?}?> value="<?=$value->id?>"><?=$value->name?></option>
                                <? } ?>
                            </select>
                        </div>

                        <?
                    }
                    ?>




                    <?
                    $cities = \backend\models\Cities::find()->all();
                    if(\backend\components\Helpers::getMyRole() == 3){
                        $cities = \backend\models\Cities::find()->where('id in ('. \backend\components\Helpers::getCitiesString() .')')->all();
                    }
                    $my_city = \backend\models\SystemUsersCities::findOne($model->id);
                    $my_city = $my_city->city_id;
                        ?>
                        <div class="col-md-6" style="padding-top: 2em; padding-bottom: 2em;">
                            <label class = "text-semibold">Город:</label>
                            <select name = "city_id" class="select" required ="required">
                                <option value="">Не выбран</option>
                                <? foreach ($cities as $key => $value) { ?>
                                    <option <?if($my_city == $value->id){?>selected<?}?> value="<?=$value->id?>"><?=$value->cname?></option>
                                <? } ?>
                            </select>
                        </div>

                        <?

                    ?>

                    <div class = "col-md-12">
                        <div class="text-right">
                            <a href = "<?=Yii::$app->request->cookies['back']?>" class="cs-link btn btn-default">Отмена <i class="icon-x position-right"></i></a>
                            <? if ($model->id != null) { ?>
<!--                                <a href = "#delete" data-id = "--><?//=$model->id?><!--" data-table = "system_users" data-redirect = "moderators" class="delete btn btn-danger">Удалить <i class="icon-trash-alt position-right"></i></a>-->
                            <? } ?>
                            <button type="submit" class="btn btn-primary">Сохранить <i class="icon-check position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</div>
