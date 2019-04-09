<?php


/**
 * Created by PhpStorm.
 * User: mint
 * Date: 11/30/18
 * Time: 8:46 AM
 */

namespace backend\controllers;
use backend\components\Helpers;
use backend\models\Orders;
use backend\models\SpecificOrders;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class FiltrationController extends Controller
{
    public function actionStatsOrders(){
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $date_cond = 'orders.created BETWEEN '. $start . ' AND ' . $end;
        $sp_cond = 'created BETWEEN "'. $_POST['start'] . '" AND "' . $_POST['end'] . '"';

        $cond = "orders.id IS NOT NULL";
        if(Helpers::getMyRole() == 3){
            $cond = Helpers::getCitiesCondition();
        }
        elseif(Helpers::getMyRole() == 5){
            $cond = 'orders.taxi_park_id = ' . Helpers::getMyTaxipark();
        }
        $ekonom1 = Orders::find()->where(['order_type' => 1])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere($date_cond)->count();
        $ekonom2 = Orders::find()->where(['order_type' => 1])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($date_cond)->andWhere($cond)->count();

        $komfort1 = Orders::find()->where(['order_type' => 2])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($date_cond)->andWhere($cond)->count();
        $komfort2 = Orders::find()->where(['order_type' => 2])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($date_cond)->andWhere($cond)->count();

        $kk1 = Orders::find()->where(['order_type' => 3])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere($date_cond)->count();
        $kk2 = Orders::find()->where(['order_type' => 3])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere($date_cond)->count();

        $lady1 = Orders::find()->where(['order_type' => 4])->andWhere(['status' => 5])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere($date_cond)->count();
        $lady2 = Orders::find()->where(['order_type' => 4])  ->andWhere(['or', ['status'=>0], ['deleted'=>1]])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($date_cond)->andWhere($cond)->count();

        $mejgorod = SpecificOrders::find()->where(['order_type_id' => 1])->andWhere($sp_cond)->count();
        $gruz = SpecificOrders::find()->where(['order_type_id' => 2])->andWhere($sp_cond)->count();
        $evak = SpecificOrders::find()->where(['order_type_id' => 3])->andWhere($sp_cond)->count();
        $inva = SpecificOrders::find()->where(['order_type_id' => 4])->andWhere($sp_cond)->count();

        $response['ek1'] = $ekonom1;
        $response['ek2'] = $ekonom2;
        $response['k1'] = $komfort1;
        $response['k2'] = $komfort2;
        $response['kk1'] = $kk1;
        $response['kk2'] = $kk2;
        $response['l1'] = $lady1;
        $response['l2'] = $lady2;

        $response['m'] = $mejgorod;
        $response['g'] = $gruz;
        $response['e'] = $evak;
        $response['i'] = $inva;

        $response['ek'] = $ekonom2 + $ekonom1;
        $response['k'] = $komfort2 + $komfort1;
        $response['kk'] = $kk2 + $kk1;
        $response['l'] = $lady2 + $lady1;


        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }



    public function actionOrders(){
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $date_cond = 'orders.created BETWEEN '. $start . ' AND ' . $end;
        $sp_cond = 'created BETWEEN "'. $_POST['start'] . '" AND "' . $_POST['end'] . '"';


        $cond= '';
        if(Yii::$app->session->get('profile_role') == 9){
            $cond = 'orders.id IS NOT null';
        }elseif (Yii::$app->session->get('profile_role') == 3){
            $me = \backend\models\SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
            $my_cities = \backend\models\SystemUsersCities::find()->where(['system_user_id' => $me->id])->all();
            $in = '';
            foreach ($my_cities as $k => $v){
                if($k == count($my_cities) - 1){
                    $in .= $v->city_id;
                }else{
                    $in .= $v->city_id . ', ';
                }
                $cond = 'cities.id in (' . $in . ')';
            }
        }elseif (Helpers::getMyRole() == 5){
            $cond = 'orders.taxi_park_id = ' . Helpers::getMyTaxipark();
        }

        $active_econom = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $active_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $active_kk = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $finished_econom = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $finished_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $finished_kk = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $active_lady = count(\backend\models\Orders::find()->where(['in', 'status', [1, 2, 3, 4]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $finished_lady = count(\backend\models\Orders::find()->where(['in', 'status', [5]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());

        $cancelled_econom = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 1])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $cancelled_comfort = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 2])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $cancelled_kk = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 3])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());
        $cancelled_lady = count(\backend\models\Orders::find()->where(['in', 'status', [0]])->andWhere(['order_type' => 4])->innerJoin('users', 'users.id = orders.user_id')->innerJoin('cities', 'cities.id = users.city_id')->andWhere($cond)->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())->andWhere($date_cond)->all());

        $mejgorod = count(\backend\models\SpecificOrders::find()->where(['order_type_id' => 1])->andWhere($sp_cond)->all());
        $gruz = count(\backend\models\SpecificOrders::find()->where(['order_type_id' => 2])->andWhere($sp_cond)->all());
        $evak = count(\backend\models\SpecificOrders::find()->where(['order_type_id' => 3])->andWhere($sp_cond)->all());
        $inva = count(\backend\models\SpecificOrders::find()->where(['order_type_id' => 4])->andWhere($sp_cond)->all());





        $response['ek1'] = $active_econom;
        $response['ek2'] = $finished_econom;
        $response['ek3'] = $cancelled_econom;
        $response['k1'] = $active_comfort;
        $response['k2'] = $finished_comfort;
        $response['k3'] = $cancelled_comfort;
        $response['kk1'] = $active_kk;
        $response['kk2'] = $finished_kk;
        $response['kk3'] = $cancelled_kk;
        $response['l1'] = $active_lady;
        $response['l2'] = $finished_lady;
        $response['l3'] = $cancelled_lady;

        $response['m'] = $mejgorod;
        $response['g'] = $gruz;
        $response['e'] = $evak;
        $response['i'] = $inva;

        $response['ek'] = $active_econom + $finished_econom + $cancelled_econom;
        $response['k'] = $active_comfort + $finished_comfort + $cancelled_comfort;
        $response['kk'] = $active_kk + $finished_kk + $cancelled_kk;
        $response['l'] = $active_lady + $finished_lady + $cancelled_lady;

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }





    public function actionTraffic(){
        $start = strtotime($_POST['start']);
        $end = strtotime($_POST['end']);
        $date_cond = 'date BETWEEN '. $start . ' AND ' . $end;

        $tp1 = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->andWhere(['reciever_user_id' => 111])->andWhere(['sender_tp_id' => 0])->andWhere($date_cond)->sum('amount');
        $driver = \backend\models\MonetsTraffic::find()->where(['type_id' => 1])->andWhere(['<>', 'reciever_user_id', 111])->andWhere(['sender_tp_id' => 0])->andWhere($date_cond)->sum('amount');
        $to_companies = \backend\models\MonetsTraffic::find()->where('reciever_company_id IS NOT NULL')->andWhere(['sender_tp_id' => \backend\components\Helpers::getMyTaxipark()])->andWhere($date_cond)->sum('amount');

        $response['t'] = $tp1 + 0;
        $response['d'] = $driver + 0;
        $response['c'] = $to_companies + 0;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }






}
