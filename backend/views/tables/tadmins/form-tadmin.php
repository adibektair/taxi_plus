<?php
use backend\models\TaxiPark;
use backend\models\Category;
?>
<!-- ENGINE -->
<script type="text/javascript" src="/profile/files/js/plugins/notifications/sweet_alert.min.js"></script>
<script type="text/javascript" src="/profile/files/js/pages/form_layouts.js"></script>

<!---LOCAL --->
<script type="text/javascript" src="/profile/files/js/mytables/tadmins/form.js"></script>
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
                            if($model == null){ ?>
                                <?=$this->render('/layouts/modal-components/_input', array('info' => array("Пароль", "password", "text", $model->password, "true")))?>
                            <?}
                        ?>

                        <?=$this->render('/layouts/modal-components/_input', array('info' => array("Телефон", "phone", "text", $model->phone, "true")))?>
                        <?
                        $su = \backend\models\SystemUsers::find()->where(['role_id' => 5])->all();
                        $array = "";
                        foreach ($su as $k => $v){
                            if($v->taxi_park_id != null){
                                $array .=  $v->taxi_park_id . ', ';
                            }

                        }
                        $array .= $su[0]->taxi_park_id;

                        if($array == '' OR $array == null){
                          $list = TaxiPark::find()->all();  
                        }else{
                            $list = TaxiPark::find()->where('id not in (' . $array . ')')->all();
                        }
                        //$list = TaxiPark::find()->where('id not in (' . $array . ')')->all();
                        if(\backend\components\Helpers::getMyRole() == 3){
                            $list = TaxiPark::find()->where('id not in (' . $array . ')')->andWhere('taxi_park.city_id in ('. \backend\components\Helpers::getCitiesString() .')')->all();
                        }
                        ?>
                        <label class = "text-semibold">Такопарк:</label>
                        <select name = "tpark" class="select" required ="required">
                            <option value="">Не выбран</option>
                            <? foreach ($list as $key => $value) { ?>
                                <option <? if ($value->id == $model->taxi_park_id) { ?>selected<? } ?> value="<?=$value->id?>"><?=$value->name?></option>
                            <? } ?>
                        </select>
                    </div>
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
