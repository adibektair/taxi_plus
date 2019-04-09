<?php
namespace backend\controllers;
use backend\components\Helpers;
use backend\models\CarModels;
use backend\models\SystemUsers;
use backend\models\SystemUsersCities;
use backend\models\TaxiPark;
use backend\models\UsersCars;
use Facebook\WebDriver\Remote\Service\DriverService;
use Yii;
use DateTime;
use yii\base\Model;
use yii\db\query;
use yii\web\Controller;
use backend\models\Users;
use backend\models\DriversServices;
use backend\models\Orders;
use backend\models\SavedAddresses;
use backend\models\MonetsTraffic;
use backend\models\UsersPrivileges;
use backend\models\TaxiParkPrivileges;
use backend\models\DriversFacilities;
use yii\web\Response;
use yii\web\User;

class ModeratorsController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->request->isAjax) {
            $id = $_POST['id'];
            if ($id != null) {
                $model = SystemUsers::find()->where(['id' => $id])->one();
            } else {
                $user = SystemUsers::find()->where(['email' => $_POST['Information']['email']])->one();
                if ($user != null) {
                    $response['message'] = "Пользователь с таким email уже зарегистрирован.";
                    $response['type'] = "error";
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $model = new SystemUsers();

            }

            $model->attributes = $_POST['Information'];
            $model->created = strtotime("now");
            $model->password = md5($_POST['Information']['password']);
            $model->role_id = 4;
            if(Helpers::getMyRole() == 9){
                $model->taxi_park_id = $_POST['taxi_park'];
                $tp = TaxiPark::findOne($_POST['taxi_park']);
            }else{
                $model->taxi_park_id = Helpers::getMyTaxipark();
                $tp = TaxiPark::findOne(Helpers::getMyTaxipark());
            }



            if ($model->save()) {
                $uc = SystemUsersCities::findOne($model->id);
                if($uc == null){
                    $uc = new SystemUsersCities();
                    $uc->system_user_id = $model->id;
                    $uc->created = strtotime('now');

                }
                $uc->city_id = $_POST['city_id'];
                $uc->save();
                if($id != null){
                    $response['message'] = "Модератор изменен";
                }else{
                    $response['message'] = "Модератор успешно добавлен";
                }

                $response['type'] = "success";

            } else {
                $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                $response['type'] = "error";
            }


            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }


    public function actionAdmins()
    {
        if (Yii::$app->request->isAjax) {
            $id = $_POST['id'];
            if ($id != null) {
                $model = SystemUsers::find()->where(['id' => $id])->one();
            } else {
                $user = SystemUsers::find()->where(['email' => $_POST['Information']['email']])->one();
                if ($user != null) {
                    $response['message'] = "Пользователь с таким email уже зарегистрирован.";
                    $response['type'] = "error";
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $model = new SystemUsers();
                $today = getdate();
                $model->created = strtotime("now");
                $model->password = md5($_POST['Information']['password']);

            }

            $model->attributes = $_POST['Information'];
            $model->last_edit = date("d/m/Y H:i:s", time());
            $model->role_id = 3;

            if ($model->save()) {
                if($id != null){
                    $response['message'] = "Администратор изменен";
                }else{
                    $response['message'] = "Администратор успешно добавлен";
                }

                $response['type'] = "success";

            } else {
                $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                $response['type'] = "error";
            }


            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }



    }





    public function actionDriver()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $_POST['id'];
            if($id != null){
                $model = Users::find()->where(['id' => $id])->one();

            }else{

                $model = new Users();
                $model->created = strtotime("now");
                $model->role_id = 2;
            }
            $me = \backend\models\SystemUsers::find()->where(['id' => Yii::$app->session->get("profile_id")])->one();
            $taxi_park = \backend\models\TaxiPark::find()->where(['id' => $me->taxi_park_id])->one();
            if($model->balance == $_POST['Information']['balance']){

            }else{

                if($model->balance > $_POST['Information']['balance']){
                    $response['message'] = "Внимание Вы не можете отнять монеты водителя";
                    $response['type'] = "error";
                    return $response;
                }else{
                    if($taxi_park->balance < ($taxi_park->balance - ($_POST['Information']['balance'] - $model->balance))){
                        $response['message'] = "Внимание у Вас недостаточно средств для пополнения баланса водителя";
                        $response['type'] = "error";
                        return $response;
                    }else{

                        $log = new MonetsTraffic();
                        $log->amount = $_POST['Information']['balance'] - $model->balance;
                        $log->reciever_user_id = $model->id;
                        $log->reciever_tp_id = $model->taxi_park_id;
                        $log->sender_user_id = Yii::$app->session->get("profile_id");
                        $log->sender_tp_id = Yii::$app->session->get("profile_tp");
                        $now = new DateTime();
                        $log->date = $now->getTimestamp();
                        $log->save();

                        $taxi_park->balance = $taxi_park->balance - ($_POST['Information']['balance'] - $model->balance);

                        $taxi_park->save();
                    }
                }

            }


            $model->taxi_park_id = Helpers::getMyTaxipark();
            $taxi_park = TaxiPark::findOne(Helpers::getMyTaxipark());
            $model->city_id = $taxi_park->city_id;
            $model->attributes = $_POST['Information'];
            $model->last_edit = date("d/m/Y H:i:s", time());
            $model->is_active = 1;


            if($model->save()) {

                $uc = new UsersCars();

                $uc->car_id = $_POST['car_id'];
                $uc->type = $_POST['type'];
                $uc->seats_number = $_POST['Information']['seats_number'];
                $uc->year = $_POST['Information']['year'];
                $uc->number = $_POST['Information']['number'];
                $uc->user_id = $model->id;
                $uc->save();

                if($id != null){
                    $response['message'] = "Водитель изменен";
                }else{
                    $response['message'] = "Водитель успешно добавлен";
                }

                $response['type'] = "success";

            } else {
                $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                $response['type'] = "error";
            }


            return $response;
        }



    }






    public function actionDispatcher()
    {
        if (Yii::$app->request->isAjax) {
            $id = $_POST['id'];
            if ($id != null) {
                $model = SystemUsers::find()->where(['id' => $id])->one();
            } else {
                $user = SystemUsers::find()->where(['email' => $_POST['Information']['email']])->one();
                if ($user != null) {
                    $response['message'] = "Пользователь с таким email уже зарегистрирован.";
                    $response['type'] = "error";
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $model = new SystemUsers();

            }

            $model->attributes = $_POST['Information'];
            if(Helpers::getMyRole() == 9){
                $model->taxi_park_id = $_POST['tp'];
            }else{
                $model->taxi_park_id = Helpers::getMyTaxipark();
            }

            $model->last_edit = date("d/m/Y H:i:s", time());
            $model->created = strtotime("now");
            $model->password = md5($_POST['Information']['password']);
            $model->role_id = 8;

            if ($model->save()) {
                if($id != null){
                    $response['message'] = "Диспетчер изменен";
                }else{
                    $response['message'] = "Диспетчер успешно добавлен";
                }

                $response['type'] = "success";

            } else {
                $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                $response['type'] = "error";
            }


            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }



    }



    public function actionCashier()
    {
        if (Yii::$app->request->isAjax) {
            $id = $_POST['id'];
            if ($id != null) {
                $model = Users::find()->where(['id' => $id])->one();
            } else {
                $user = Users::find()->where(['email' => $_POST['Information']['email']])->one();
                if ($user != null) {
                    $response['message'] = "Пользователь с таким email уже зарегистрирован.";
                    $response['type'] = "error";
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $model = new Users();

            }

            $model->attributes = $_POST['Information'];
            $model->last_edit = date("d/m/Y H:i:s", time());
            $today = getdate();
            $model->created = strtotime("now");
            $model->password = md5($_POST['Information']['password']);
            $model->role_id = 6;
            $model->taxi_park_id = 0;

            if ($model->save()) {
                if($id != null){
                    $response['message'] = "Кассир изменен";
                }else{
                    $response['message'] = "Кассир успешно добавлен";
                }

                $response['type'] = "success";

            } else {
                $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                $response['type'] = "error";
            }


            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }



    public function actionGetModels(){
        $models = CarModels::find()->where(['parent_id' => $_POST['id']])->all();

        $response['models'] = $models;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

}

?>