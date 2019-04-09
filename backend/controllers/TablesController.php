<?php
namespace backend\controllers;
use backend\models\CarModels;
use backend\models\Company;
use backend\models\Complaints;
use backend\models\SpecificOrders;
use backend\models\SystemUsers;
use backend\models\Orders;
use backend\models\MoneyRequest;
use backend\models\Queries;
use backend\models\SystemUsersCities;
use backend\models\TaxiPark;
use backend\models\Users;
use Yii;
use yii\log\Dispatcher;
use yii\web\Controller;
use yii\web\Response;
use backend\components\Helpers;
use backend\models\MonetsTraffic;

class TablesController extends Controller
{
    public function actionGettable()
    {
        if (Yii::$app->request->isAjax) {
            $table = $_POST['table'];
            $name = $_POST['name'];
            $other = (array)$_POST['other'];
            $config = Helpers::GetConfig($name, "select_fields");
            $draw = $_GET['draw'];      //Текущая страница
            $start = $_GET['start'];    //С какой записи
            $length = $_GET['length'];  //Количество записей на страницу
            $search = $_GET['search']['value'];  //Поиск
            $order = $_GET['order'][0]; //Сортировка


//            $query = null;
            $filtr = Yii::$app->session->get('filtr');

            /* -------------- ВНЕДРЕНИЕ */
            if (Yii::$app->session->get('profile_role') != 3) {

                if (Yii::$app->session->get('profile_role') == 5) {
                    if ($name == "taxi-parks") {
                        $query = "taxi_park.id = " . Yii::$app->session->get('profile_tp'); //Видят только дилеры
                    }
                }
            }

            $arr_date = array();


            if ($filtr[$name] != null) {
                $condition = null;
                foreach ($filtr[$name] as $key => $value) {
                    if (count($value) <= 1) {
                        if ($condition == null) {
                            $condition .= $table . "." . $key . " = '" . $value . "'";
                        } else {
                            $condition .= " AND " . $table . "." . $key . " = '" . $value . "'";
                        }
                    } else {
                        foreach ($value as $d => $date) {
                            if ($arr_date[$key]['start'] == null) {
                                $query .= " " . $table . "." . $key . " >= " . $date;
                                $arr_date[$key]['start'] = $date;
                            } else {
                                $query .= " AND " . $table . "." . $key . " <= " . $date;
                                $arr_date[$key]['end'] = $date;
                            }
                        }
                    }
                }
            }
            if ($other != null) {
                foreach ($other as $key => $value) {
                    $condition[$key] = $value;
                }
            }
            if ($condition == null) {
                $condition = $table . ".id IS NOT NULL";
            }

//            print_r('q' . $query . ' c ' . $condition); die();
            if ($name == "moderators") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`id`,
                       `name`,
                         `phone`,
                         `last_edit`,
                         `created`,
                         `email`
                        '
                    )
                    ->from($table)
                    ->where(['role_id' => 4])
                    ->all();
            } else if ($name == "admins") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`id`,
                         `name`,
                         `phone`,
                         `last_edit`,
                         `created`,
                         `email`'
                    )
                    ->from($table)
                    ->andWhere($query)
                    ->where(['role_id' => 3])
                    ->all();
            }
            else if ($name == "cadmins") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`users`.`id`,
                         `users`.`name`,
                         `users`.`phone`,
                         `company`.`name` AS cname,
                         `users`.`created`'
                    )
                    ->from($table)
                    ->andWhere($query)
                    ->where(['role_id' => 7])
                    ->innerJoin('company', 'users.company_id = company.id')
                    ->all();
            }
            else if ($name == "users") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`id`,
                         `name`,
                         `phone`,
                         `last_edit`,
                         `created`,
                         `email`,
                         `is_active`'
                    )
                    ->from($table)
                    ->where(['role_id' => 1])
                    ->andWhere($condition)
                    ->all();
            }
            else if ($name == "coworkers") {
                $model = (new \yii\db\Query())
                    ->select('`id`,
                         `name`,
                         `phone`,
                         `last_edit`,
                         `created`,
                         `is_active`,
                         `company_id`'
                    )
                    ->from($table)
                    ->where(['role_id' => 1])
                    ->andWhere(['company_id' => Helpers::getMyCompany()])
                    ->andWhere($condition)
                    ->all();
            }
            else if ($name == "aworkers") { //Producty
                $myId = Yii::$app->session->get('profile_id');
                $me = Users::find()->where(['id' => $myId])->one();
                $model = (new \yii\db\Query())
                    ->select('`id`,
                         `name`,
                         `phone`,
                         `last_edit`,
                         `created`,
                         `email`,
                         `is_active`,
                         `company_id`'
                    )
                    ->from($table)
                    ->where(['role_id' => 1])
                    ->andWhere(['company_id' => NULL])
                    ->andWhere($condition)
                    ->all();
            }

            else if ($name == "dispatchers") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`users`.`id`,
                         `users`.`name`,
                         `users`.`phone`,
   
                         `taxi_park`.`name` AS tname,
                         `users`.`created`'
                    )
                    ->from($table)
                    ->where(['role_id' => 8])
                    ->andWhere($condition)
                    ->innerJoin('taxi_park', '`taxi_park`.`id` = `users`.`taxi_park_id`')
                    ->all();
            }

            else if ($name == "tadmins") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`users`.`id`,
                         `users`.`name`,
                         `users`.`phone`,
                         `users`.`last_edit`,
                         `users`.`created`,
                         `users`.`email`,
                         `taxi_park`.`name` AS tname'
                    )
                    ->from($table)
                    ->where(['role_id' => 5])
                    //   ->andWhere($condition)
                    ->innerJoin('taxi_park', '`taxi_park`.`id` = `users`.`taxi_park_id`')
                    ->all();
            } else if ($name == "cashiers") { //Producty

                $model = (new \yii\db\Query())
                    ->select('`users`.`id`,
                         `users`.`name`,
                         `users`.`phone`,
                         `users`.`last_edit`,
                         `users`.`created`,
                         `users`.`email`,
                         `taxi_park`.`name` AS tname'
                    )
                    ->from($table)
                    ->where(['role_id' => 6])
                    ->innerJoin('taxi_park', '`taxi_park`.`id` = `users`.`taxi_park_id`')
                    ->all();
            } else if ($name == "taxi-parks" OR $name == "cashier") {


                $model = (new \yii\db\Query())
                    ->select('`taxi_park`.`id`,
                         `taxi_park`.`name`,
                         `taxi_park`.`balance`,
                         `cities`.`cname`,
                         `working_types`.`description`'
                    )
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->innerJoin('cities', '`taxi_park`.`city_id` = `cities`.`id`')
                    ->innerJoin('working_types', '`working_types`.`id` = `taxi_park`.`type`')
                    ->all();
            } else if ($name == "drivers") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`users`.`id`,
                         `users`.`name`,
                         `users`.`phone`,
                         `users`.`created`,
                         `users`.`email`,
                         `users`.`is_active`,
                         `users`.`balance`,
                         `taxi_park`.`name` AS tname'
                    )
                    ->from($table)
                    ->where(['role_id' => 2])


                    ->innerJoin('taxi_park', '`taxi_park`.`id` = `users`.`taxi_park_id`')
                    ->all();
            } else if ($name == "traffic") { //Producty
                $model = (new \yii\db\Query())
                    ->select('`monets_traffic`.`id`,
                         `u1`.`name` AS sname,
                         `u2`.`name` AS rname,
                         `tp1`.`name` AS tps,
                         `tp2`.`name` AS tpr,
                         `monets_traffic`.`date`,
                         `monets_traffic`.`sender_user_id`,
                         `monets_traffic`.`sender_tp_id`,
                         `monets_traffic`.`reciever_tp_id`,
                         `monets_traffic`.`reciever_user_id`,
                         `monets_traffic`.`amount`,
                         `monets_traffic`.`process`'
                    )
                    ->from($table)
                    ->where($query)
                    ->innerJoin('users u1', '`u1`.`id` = `monets_traffic`.`sender_user_id`')
                    ->innerJoin('users u2', '`u2`.`id` = `monets_traffic`.`reciever_user_id`')
                    ->innerJoin('taxi_park tp1', '`tp1`.`id` = `monets_traffic`.`sender_tp_id`')
                    ->innerJoin('taxi_park tp2', '`tp2`.`id` = `monets_traffic`.`reciever_tp_id`')
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "companies") { //Producty

                $companies = Company::find()->all();
                $ar = [];
                foreach ($companies as $key => $value){
                    $arr['id'] = $value->id;
                    $arr['name'] = $value->name;
                    $arr['balance'] = $value->balance;
                    $arr['created'] = $value->created;
                    $user = Users::find()->where(['role_id' => 7])->andWhere(['company_id' => $value->id])->one();
                    $arr['username'] = $user->name;
                    array_push($ar, $arr);
                }

//                $model = (new \yii\db\Query())
//                    ->select('`company`.`id`,
//                         `company`.`name`,
//                         `company`.`balance`,
//                         `company`.`created`,
//                         `users`.`name`'
//                    )
//                    ->from($table)
//                    ->innerJoin('users', 'users.company_id = company.')
//                    ->all();
                $model = $ar;

            }
            else {

                $model = (new \yii\db\Query())
                    ->select($config)
                    ->from($table)
                    ->andWhere($condition)
                    ->andWhere($query)
                    ->all();
            }
            $data['data'] = array_map('array_values', $model);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        }
    }

    public function actionFiltr()
    {
        if (Yii::$app->request->isAjax) {
            $page = $_POST['page'];
            $field = $_POST['field'];
            $value = $_POST['value'];

            $array = Yii::$app->session->get('filtr');
            if ($value == "all") {
                unset($array[$page][$field]);
            } else {
                $array[$page][$field] = $value;
            }
            if (count($array[$page]) <= 0) {
                unset($array[$page]);
            }
            Yii::$app->session->set('filtr', $array);
        }
    }

    public function actionFiltrdate()
    {
        if (Yii::$app->request->isAjax) {
            $page = $_POST['page'];
            $field = $_POST['field'];
            $start = $_POST['start'];
            $end = $_POST['end'];

            $array = Yii::$app->session->get('filtr');
            $array[$page][$field] = array("start" => strtotime($start), "end" => strtotime($end));
            if (count($array[$page]) <= 0) {
                unset($array[$page]);
            }
            Yii::$app->session->set('filtr', $array);
        }
    }

    public function actionDelfiltr()
    {
        if (Yii::$app->request->isAjax) {
            $page = $_POST['page'];
            $field = $_POST['field'];

            $array = Yii::$app->session->get('filtr');
            unset($array[$page][$field]);
            if (count($array[$page]) <= 0) {
                unset($array[$page]);
            }
            Yii::$app->session->set('filtr', $array);
        }
    }

    public function actionSavestate()
    {

        $response = array();
        foreach ($_POST as $key => $value) {
            if ($key == "time" OR $key == "start" OR $key == "length") {
                $response[$key] = intval($value);
            } else {
                $response[$key] = $value;
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->session->set('profile_state', array('products' => $_POST));
        return $response;
    }

    public function actionGetstate()
    {
        $page = 'monets_traffic';

        $state = Yii::$app->session->get('profile_state');
        if ($state[$page] == null) {
            $time = time() * 1000;
            $state[$page] = array(
                'time' => intval($time),
                'start' => 0,
                'length' => 10,
                'order' => array(
                    '0' => array(
                        '0' => 1,
                        '1' => 'asc'
                    ),
                ),
            );
            Yii::$app->session->set('profile_state', $state);
        }
        $state = Yii::$app->session->get('profile_state');
        $response = array();
        foreach ($state['monets_traffic'] as $key => $value) {
            if ($key == "time" OR $key == "start" OR $key == "length") {
                $response[$key] = intval($value);
            } else {
                $response[$key] = $value;
            }
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionGetNewTable()
    {
        if (Yii::$app->request->isAjax) {
            $name = $_GET['name'];
            $table = $_GET['table'];
            $id = $_GET['id'];
            $draw = $_GET['draw'];      //Текущая страница
            $start = $_GET['start'];    //С какой записи
            $length = $_GET['length'];  //Количество записей на страницу
            $search = $_GET['search']['value'];  //Поиск
            $order = $_GET['order'][0]; //Сортировка

            $config = Helpers::GetConfig($name, "select_fields");
            $search_config = Helpers::GetConfig($name, "search_fields");
            $filtr = Yii::$app->session->get('filtr');

            $other = (array)$_POST['other'];
            $query = null;
            $condition = null;
            $search_condition = $table . '.id != -1';

            if ($order['dir'] == "asc") {
                $sort = SORT_ASC;
            } else {
                $sort = SORT_DESC;
            }

            $arr_date = array();


            /* -------------- ВНЕДРЕНИЕ */
            if (Yii::$app->session->get('profile_role') != 3) {
                if ($name == "orders") {
                    if (Yii::$app->session->get('profile_role') == 5) {
                        $query = $table . ".taxi_park_id = " . Yii::$app->session->get('profile_tp');
                    } else if (Yii::$app->session->get('profile_role') == 7) {
                        $query = $table . ".company_id = " . Yii::$app->session->get('company_id');
                    }
                }
            }

            if ($query == null) {
                $query = $table . ".id != -1";
            }



            if ($filtr[$name] != null) {
                foreach ($filtr[$name] as $key => $value) {
                    if (count($value) <= 1) {
                        if ($condition == null) {
                            $condition .= $table . "." . $key . " = '" . $value . "'";
                        } else {
                            $condition .= " AND " . $table . "." . $key . " = '" . $value . "'";
                        }
                    } else {
                        if($name == 'admins/moderators'){
                            foreach ($value as $d => $date) {
                                if ($arr_date[$key]['start'] == null) {
                                    $query .= " AND " . 'monets_traffic.date' . " >= " . $date;
                                    $arr_date[$key]['start'] = $date;
                                } else {
                                    $query .= " AND " . 'monets_traffic.date' . " <= " . $date;
                                    $arr_date[$key]['end'] = $date;
                                }
                            }
                            foreach ($value as $d => $date) {
                                if ($arr_date[$key]['start'] == null) {
                                    $query .= " AND " . 'moderators_money.created' . " >= " . $date;
                                    $arr_date[$key]['start'] = $date;
                                } else {
                                    $query .= " AND " . 'moderators_money.created' . " <= " . $date;
                                    $arr_date[$key]['end'] = $date;
                                }
                            }
                        }else{
                            foreach ($value as $d => $date) {
                                if ($arr_date[$key]['start'] == null) {
                                    $query .= " AND " . $key . " >= " . $date;
                                    $arr_date[$key]['start'] = $date;
                                } else {
                                    $query .= " AND " . $key . " <= " . $date;
                                    $arr_date[$key]['end'] = $date;
                                }
                            }
                        }

                    }
                }
            }


            if ($other != null) {
                foreach ($other as $key => $value) {
                    if ($condition == null) {
                        $condition .= $table . "." . $key . " = '" . $value . "'";
                    } else {
                        $condition .= " AND " . $table . "." . $key . " = '" . $value . "'";
                    }
                }
            }

            if ($search != null AND $search_config != null) {
                $search_condition = null;
                foreach ($search_config as $value) {
                    if ($search_condition == null) {
                        $search_condition .=   $value . " LIKE '%" . $search . "%'";
                    } else {
                        $search_condition .= " OR " .  $value . " LIKE '%" . $search . "%'";
                    }
                }
            }
            if ($condition == null) {
                $condition = $table . ".id IS NOT NULL";
            }




            if ($name == "traffic") { //Producty
                $recordsTotal = MonetsTraffic::find()->andWhere($query)->count();
                $recordsFiltered = MonetsTraffic::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('`monets_traffic`.`id`,
                         `u1`.`name` AS sname,
                         `u2`.`name` AS rname,
                         `tp1`.`name` AS tps,
                         `tp2`.`name` AS tpr,
                         `monets_traffic`.`date`,
                         `monets_traffic`.`sender_user_id`,
                         `monets_traffic`.`sender_tp_id`,
                         `monets_traffic`.`reciever_tp_id`,
                         `monets_traffic`.`reciever_user_id`,
                         `monets_traffic`.`amount`,
                         `monets_traffic`.`process`'
                    )
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->innerJoin('users u1', '`u1`.`id` = `monets_traffic`.`sender_user_id`')
                    ->innerJoin('users u2', '`u2`.`id` = `monets_traffic`.`reciever_user_id`')
                    ->innerJoin('taxi_park tp1', '`tp1`.`id` = `monets_traffic`.`sender_tp_id`')
                    ->innerJoin('taxi_park tp2', '`tp2`.`id` = `monets_traffic`.`reciever_tp_id`')
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }

            else if ($name == "complaints") { //Producty
                $recordsTotal = Complaints::find()->andWhere($query)->count();
                $recordsFiltered = Complaints::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('complaints.id, users.name as author, users.phone, complaints.text, driver.name as for, complaints.created, driver.phone as dphone')
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->innerJoin('users', 'users.id = complaints.user_id')
                    ->innerJoin('orders', 'orders.id = complaints.order_id')
                    ->innerJoin('users driver', 'driver.id = orders.driver_id')
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }

            else if ($name == "requests") { //Producty
                $recordsTotal = MoneyRequest::find()->andWhere($query)->andWhere('money_requests.deleted = 0')->count();
                $recordsFiltered = MoneyRequest::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->andWhere('money_requests.deleted = 0')->count();
                $model = (new \yii\db\Query())
                    ->select('money_requests.id, users.name, users.phone, users.balance, taxi_park.name as taxi_park, money_requests.amount, money_requests.created, money_requests.card_number')
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere('money_requests.deleted = 0')
                    ->innerJoin('users', 'users.id = money_requests.user_id')
                    ->innerJoin('taxi_park', 'taxi_park.id = users.taxi_park_id')
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }

            else if ($name == "dispatchers_orders") { //Producty
                $recordsTotal = Orders::find()->andWhere($query)->andWhere(['orders.dispatcher_id' => Yii::$app->session->get('profile_id')])->count();
                $recordsFiltered = Orders::find()->andWhere($condition)->andWhere(['orders.dispatcher_id' => Yii::$app->session->get('profile_id')])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('orders.id, orders.price, orders.created, driver.phone as dphone, driver.name as dname, users.phone, orders.status')
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere(['orders.dispatcher_id' => Yii::$app->session->get('profile_id')])
                    ->innerJoin('users', 'users.id = orders.user_id')
                    ->leftJoin('users driver', 'driver.id = orders.driver_id')
                    ->limit($length)
                    ->offset($start)
                    ->orderBy(['id' => SORT_DESC])
                    ->all();
            }

            else if ($name == "orders/orders-list") {

                $recordsTotal = Orders::find()->andWhere($query)->andWhere(['order_type' => $_GET['id']])->count();
                $recordsFiltered = Orders::find()->andWhere($condition)->andWhere(['order_type' => $_GET['id']])->andWhere($query)->count();
                if(Yii::$app->session->get('profile_role') == 9){
                    $model = (new \yii\db\Query())
                        ->select('`orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `orders`.`price`,
                         `orders`.`status`,
                         `taxi_park`.`name` as tname,
                         `orders`.`created`'
                        )
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->andWhere(['order_type' => $_GET['id']])
                        ->innerJoin('users', 'users.id = orders.user_id')
                        ->innerJoin('taxi_park', 'taxi_park.id = orders.taxi_park_id')
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }elseif (Yii::$app->session->get('profile_role') == 3){

                    $cond = getCitiesCondition();
                    $model = (new \yii\db\Query())
                        ->select('`orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `orders`.`price`,
                         `orders`.`status`,
                         `taxi_park`.`name` as tname,
                         `orders`.`created`'
                        )
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['order_type' => $_GET['id']])
                        ->innerJoin('users', 'users.id = orders.user_id')
                        ->innerJoin('taxi_park', 'taxi_park.id = orders.taxi_park_id')
                        ->innerJoin('cities', 'cities.id = users.city_id')
                        ->andWhere($cond)
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }elseif (getMyRole() == 4){
                    $model = (new \yii\db\Query())
                        ->select('`orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `orders`.`price`,
                         `orders`.`status`,
                         `taxi_park`.`name` as tname,
                         `orders`.`created`'
                        )
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['order_type' => $_GET['id']])
                        ->innerJoin('users', 'users.id = orders.user_id')
                        ->innerJoin('taxi_park', 'taxi_park.id = orders.taxi_park_id')
                        ->innerJoin('cities', 'cities.id = users.city_id')
                        ->andWhere('orders.taxi_park_id = '. Helpers::getMyTaxipark())
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }elseif (getMyRole() == 7){
                    $model = (new \yii\db\Query())
                        ->select('`orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `orders`.`price`,
                         `orders`.`status`,
                         `taxi_park`.`name` as tname,
                         `orders`.`created`'
                        )
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['order_type' => $_GET['id']])
                        ->innerJoin('users', 'users.id = orders.user_id')
                        ->innerJoin('taxi_park', 'taxi_park.id = orders.taxi_park_id')
                        ->innerJoin('cities', 'cities.id = users.city_id')
                        ->andWhere('orders.company_id = '. Helpers::getMyCompany())
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }
                elseif (getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('`orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `orders`.`price`,
                         `orders`.`status`,
                         `taxi_park`.`name` as tname,
                         `orders`.`created`'
                        )
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['order_type' => $_GET['id']])
                        ->innerJoin('users', 'users.id = orders.user_id')
                        ->innerJoin('taxi_park', 'taxi_park.id = orders.taxi_park_id')
                        ->innerJoin('cities', 'cities.id = users.city_id')
                        ->andWhere('orders.taxi_park_id= '. Helpers::getMyTaxipark())
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }

            }
            else if ($name == "specific_orders") {
                $recordsTotal = SpecificOrders::find()->andWhere($query)->andWhere(['order_type_id' => $_GET['id']])->count();
                $recordsFiltered = SpecificOrders::find()->andWhere($condition)->andWhere(['order_type_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('`specific_orders`.`id`,
                         `users`.`name` as uname,
                         `users`.`phone`,
                         `specific_orders`.`price`,
                         `specific_orders`.`created`,
                         `start`.`cname` as a,
                         `end`.`cname` as b,
                         `specific_orders`.`from_string`,
                         `specific_orders`.`to_string`'
                    )
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere(['order_type_id' => $_GET['id']])
                    ->leftJoin('users', 'users.id = specific_orders.driver_id')
                    ->leftJoin('cities as start', 'start.id = specific_orders.start_id')
                    ->leftJoin('cities as end', 'end.id = specific_orders.destination_id')
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "messages") {
                $recordsTotal = 0;//SpecificOrders::find()->andWhere($query)->andWhere(['order_type_id' => $_GET['id']])->count();
                $recordsFiltered = 0;//SpecificOrders::find()->andWhere($condition)->andWhere(['order_type_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('messages.*, m2.read, u.first_name, u.last_name')
                    ->from('messages')
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere($search_condition)
                    ->innerJoin('message_recievers m2', 'messages.id = m2.message_id')
                    ->innerJoin('system_users u', 'u.id = messages.sender_id')
                    ->andWhere(['m2.reciever_id' => Yii::$app->session->get('profile_id')])
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "regions") {
                $recordsTotal = 0;//SpecificOrders::find()->andWhere($query)->andWhere(['order_type_id' => $_GET['id']])->count();
                $recordsFiltered = 0;//SpecificOrders::find()->andWhere($condition)->andWhere(['order_type_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('regions.*, count(distinct c.id) as amount')
                    ->from($table)
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere($search_condition)
                    ->leftJoin('cities c', 'regions.id = c.region_id')
                    ->limit($length)
                    ->offset($start)
                    ->groupBy('regions.id')
                    ->all();
            }
            else if ($name == "cities") {
                $recordsTotal = 0;//SpecificOrders::find()->andWhere($query)->andWhere(['order_type_id' => $_GET['id']])->count();
                $recordsFiltered = 0;//SpecificOrders::find()->andWhere($condition)->andWhere(['order_type_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('*')
                    ->from($table)
                    ->where($query)
                    ->andWhere(['region_id' => $_GET['id']])
                    ->andWhere($condition)
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "cars") {
                $recordsTotal = CarModels::find()->andWhere($query)->andWhere(['parent_id' => -1])->count();
                $recordsFiltered = CarModels::find()->andWhere($condition)->andWhere(['parent_id' => -1])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('car_models.id, car_models.model, count(model.id) as amount')
                    ->from("car_models")
                    ->where($query)
                    ->leftJoin('car_models model', 'model.parent_id = car_models.id')
                    ->andWhere($condition)
                    ->andWhere($search_condition)
                    ->andWhere('car_models.parent_id = -1')
                    ->limit($length)
                    ->offset($start)
                    ->groupBy('car_models.id')
                    ->all();
            }
            else if ($name == "cars/submodels") {
                $recordsTotal = CarModels::find()->andWhere($query)->andWhere(['parent_id' => $_GET['id']])->count();
                $recordsFiltered = CarModels::find()->andWhere($condition)->andWhere(['parent_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('*')
                    ->from("car_models")
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere($search_condition)
                    ->andWhere('car_models.parent_id = ' . $_GET['id'])
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "sent_messages") {
                $recordsTotal = 0;//SpecificOrders::find()->andWhere($query)->andWhere(['order_type_id' => $_GET['id']])->count();
                $recordsFiltered = 0;//SpecificOrders::find()->andWhere($condition)->andWhere(['order_type_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('messages.*, u.first_name, u.last_name')
                    ->from('messages')
                    ->where($query)
                    ->andWhere($search_condition)
                    ->andWhere($condition)
                    ->innerJoin('system_users u', 'u.id = messages.sender_id')
                    ->andWhere(['messages.sender_id' => Yii::$app->session->get('profile_id')])
                    ->limit($length)
                    ->offset($start)
                    ->groupBy(['messages.id'])
                    ->all();
            }
            else if ($name == "clients") {
                $cond = 'users.id IS NOT NULL';
                if(Helpers::getMyRole() == 4 OR Helpers::getMyRole() == 5){
                    $cond = 'users.taxi_park_id = '. Helpers::getMyTaxipark();
                }
                $recordsTotal = Users::find()->andWhere($query)->andWhere(['role_id' => 1])->count();
                $recordsFiltered = Users::find()->andWhere($condition)->andWhere(['role_id' => 1])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('users.id, users.name, users.phone, g.gender, users.created, users.balance, count(distinct parent.id) as referals, c.cname as city')
                    ->from('users')
                    ->where($query)
                    ->andWhere($condition)
                    ->andWhere($cond)
                    ->andWhere(['users.role_id' => 1])
                    ->leftJoin('genders g', 'users.gender_id = g.id')
                    ->leftJoin('users parent', 'users.id = parent.parent_id')
                    ->innerJoin('cities c', 'users.city_id = c.id')
                    ->limit($length)
                    ->offset($start)
                    ->groupBy('users.id')
                    ->all();
                $recordsTotal = count($model);// Users::find()->andWhere($query)->andWhere(['role_id' => 1])->count();
                $recordsFiltered = count($model);// Users::find()->andWhere($condition)->andWhere(['role_id' => 1])->andWhere($query)->andWhere($search_condition)->count();

            }
            else if ($name == "drivers") {
                $recordsTotal = Users::find()->andWhere($query)->andWhere(['role_id' => 2])->count();
                $recordsFiltered = Users::find()->andWhere($condition)->andWhere(['role_id' => 2])->andWhere($query)->andWhere($search_condition)->count();
                if(getMyRole() == 9){
                    $model = (new \yii\db\Query())
                        ->select('users.*, g.gender, users_cars.car_id, model.model, submodel.model as submodel, park.name as tp, users_cars.number, c.cname as city')
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['users.role_id' => 2])
                        ->leftJoin('genders g', 'users.gender_id = g.id')
                        ->leftJoin('users_cars', 'users.id = users_cars.user_id')
                        ->leftJoin('car_models submodel', 'submodel.id = users_cars.car_id')
                        ->leftJoin('car_models model', 'submodel.parent_id = model.id')
                        ->leftJoin('taxi_park park', 'users.taxi_park_id = park.id')
                        ->leftJoin('cities c', 'users.city_id = c.id')
                        ->andWhere($search_condition)
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }elseif (getMyRole() == 4 OR getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('users.*, g.gender, users_cars.car_id, model.model, submodel.model as submodel, park.name as tp, users_cars.number, c.cname as city')
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere(['users.role_id' => 2])
                        ->leftJoin('genders g', 'users.gender_id = g.id')
                        ->leftJoin('users_cars', 'users.id = users_cars.user_id')
                        ->leftJoin('car_models submodel', 'submodel.id = users_cars.car_id')
                        ->leftJoin('car_models model', 'submodel.parent_id = model.id')
                        ->leftJoin('taxi_park park', 'users.taxi_park_id = park.id')
                        ->leftJoin('cities c', 'users.city_id = c.id')
                        ->andWhere('park.id = ' . Helpers::getMyTaxipark())
                        ->andWhere($search_condition)
                        ->limit($length)
                        ->offset($start)
                        ->all();
                }
            }
            else if ($name == "taxi-parks") {
                $recordsTotal = TaxiPark::find()->andWhere($query)->count();
                $recordsFiltered = TaxiPark::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
//                if(Yii::$app->session->get('profile_role') == 9){
//
//                }else if(Yii::$app->session->get('profile_role') == 3){
//                    $cond = getCitiesCondition();
//                    $model = (new \yii\db\Query())
//                        ->select('taxi_park.*, cities.cname as city')
//                        ->from($table)
//                        ->where($query)
//                        ->andWhere($condition)
//                        ->andWhere($search_condition)
//                        ->leftJoin('cities', 'cities.id = taxi_park.city_id')
//                        ->andWhere($cond)
//                        ->limit($length)
//                        ->offset($start)
//                        ->all();
//                }
                if(Helpers::getMyRole() == 3){
                    $model = (new \yii\db\Query())
                        ->select('taxi_park.*, cities.cname as city')
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->andWhere(Helpers::getCitiesCondition())
                        ->leftJoin('cities', 'cities.id = taxi_park.city_id')
                        ->limit($length)
                        ->offset($start)
                        ->all();

                }else{
                    $model = (new \yii\db\Query())
                        ->select('taxi_park.*, cities.cname as city')
                        ->from($table)
                        ->where($query)
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->leftJoin('cities', 'cities.id = taxi_park.city_id')
                        ->limit($length)
                        ->offset($start)
                        ->all();

                }

            }

            else if ($name == "admins") {
                $sql = "SELECT system_users.id, system_users.first_name,
                                                                   system_users.last_name,
                                                                   system_users.last_edit,
                                                                   system_users.phone, 
                                                                   system_users.email,
                                                                   group_concat( distinct  c.cname) as cities,
                                                                   count(distinct driver.id) as drivers,
                                                                   count(distinct client.id) as clients,
                                                                   countOfModerators as moderators
                                                            from system_users
                                                                    inner join system_users_cities suc on system_users.id = suc.system_user_id
                                                                    inner join cities c on suc.city_id = c.id
                                                                    left join users driver on driver.city_id = c.id and driver.role_id = 2
                                                                    left join users client on client.city_id = c.id and client.role_id = 1
                                                                    left join (select city.city_id,count(moder.id) countOfModerators
                                                                               from system_users moder
                                                                                  inner join system_users_cities city
                                                                                        on moder.id = city.system_user_id
                                                                               where moder.role_id = 4
                                                                               group by city.city_id) m
                                                                     on m.city_id=suc.city_id
                                                            where system_users.role_id = 3  and ". $search_condition ."
                                                            group by system_users.id,countOfModerators;";
//                $model = (new \yii\db\Query())
//                    ->select('su.id, su.first_name, su.last_name, su.last_edit, su.phone, su.email, group_concat(distinct  c.cname) as cities, count(distinct driver.id) as drivers, count(distinct client.id) as clients count(distinct m.city_id) as moderators')
//                    ->from("system_users")
//                    ->where($query)
//                    ->andWhere($condition)
//                    ->innerJoin('system_users_cities suc', 'su.id = suc.system_user_id')
//                    ->innerJoin('cities c', 'su.id = suc.system_user_id')
//                    ->leftJoin('users driver', 'driver.city_id = c.id and driver.role_id = 2')
//                    ->leftJoin('users client', 'client.city_id = c.id and client.role_id = 1')
//                    ->leftJoin('(select city.city_id from system_users moder inner join system_users_cities city on moder.id = city.system_user_id where moder.role_id = 4) m', 'm.city_id=suc.city_id')
//                    ->andWhere(['su.role_id' => 3])
//                    ->limit($length)
//                    ->offset($start)
//                    ->groupBy('su.id')
//                    ->all();


                $recordsTotal = SystemUsers::find()->where(['role_id' => 3])->andWhere($query)->count();
                $recordsFiltered = SystemUsers::find()->where(['role_id' => 3])->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);

                $model = $command->queryAll();

            }
            else if ($name == "tadmins") {

                if(getMyRole() == 9){
                    $sql = "select system_users.*, park.name as park, count(distinct driver.id) as drivers, count(distinct clients.id) as clients, count(distinct u.id) as moderators, c.cname as city
                                                                                            from system_users 
                                                                                              inner join taxi_park park on system_users.taxi_park_id = park.id
                                                                                              left join users driver on park.id = driver.taxi_park_id and driver.role_id = 2
                                                                                              left join users clients on park.id = clients.taxi_park_id and clients.role_id = 1
                                                                                              left join system_users u on park.id = u.taxi_park_id and u.role_id = 4
                                                                                              inner join cities c on park.city_id = c.id
                                                                                            where system_users.role_id=5
                                                                                            and " . $search_condition . "
                                                                                            group by system_users.id;";

                }elseif (getMyRole() == 3){
                    $sql = "select system_users.*, park.name as park, count(distinct driver.id) as drivers, count(distinct clients.id) as clients, count(distinct u.id) as moderators, c.cname as city
                                                                                            from system_users
                                                                                              inner join taxi_park park on system_users.taxi_park_id = park.id
                                                                                              left join users driver on park.id = driver.taxi_park_id and driver.role_id = 2
                                                                                              left join users clients on park.id = clients.taxi_park_id and clients.role_id = 1
                                                                                              left join system_users u on park.id = u.taxi_park_id and u.role_id = 4
                                                                                              inner join cities c on park.city_id = c.id
                                                                                            where system_users.role_id=5
                                                                                            and c.id in (" . getCitiesString() . ")
                                                                                            and " . $search_condition . "
                                                                                            group by system_users.id;";

                }

                $recordsTotal = SystemUsers::find()->where(['role_id' => 5])->andWhere($query)->count();
                $recordsFiltered = SystemUsers::find()->where(['role_id' => 5])->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);
                $model = $command->queryAll();

            }
            else if ($name == "companies") {
                if(getMyRole() == 9){
                    $sql = "select company.*, u.first_name, u.last_name, c2.cname
                            from company
                            left join system_users u on company.id = u.company_id and u.role_id = 7
                            inner join cities c2 on company.city_id = c2.id
                              and " . $search_condition . " 
                            group by company.id, u.id;";
                }elseif(getMyRole() == 3){
                    $sql = "select company.*, u.first_name, u.last_name, c2.cname
                    from company 
                    left join system_users u on company.id = u.company_id and u.role_id = 7
                    inner join cities c2 on company.city_id = c2.id
                    and c2.id in (" . getCitiesString() . ")
                      and " . $search_condition . " 
                    group by company.id, u.id;";
                }

                $recordsTotal = Company::find()->andWhere($query)->count();
                $recordsFiltered = Company::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);
                $model = $command->queryAll();

            }
            else if ($name == "cadmins") {
                if(getMyRole() == 9){
                    $sql = "select admins.*, c.balance, c.name as company, c2.cname as city, count(distinct u.id) as clients
                            from system_users admins
                            inner join company c on admins.company_id = c.id
                            inner join cities c2 on c.city_id = c2.id
                            left join users u on c.id = u.company_id and u.role_id = 1
                            where admins.role_id=7
                            group by admins.id;";
                }elseif(getMyRole() == 3){
                    $sql = "select admins.*, c.balance, c.name as company, c2.cname as city, count(distinct u.id) as clients
                            from system_users admins
                            inner join company c on admins.company_id = c.id
                            inner join cities c2 on c.city_id = c2.id
                            left join users u on c.id = u.company_id and u.role_id = 1
                            where admins.role_id=7
                            and c2.id in (". getCitiesString() .")
                            group by admins.id;";
                }

                $recordsTotal = SystemUsers::find()->where(['role_id' => 7])->andWhere($query)->count();
                $recordsFiltered = SystemUsers::find()->where(['role_id' => 7])->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);
                $model = $command->queryAll();

            }
            else if ($name == "stats-referals/stat") {

                $sql = "select users.*, sum(monets_traffic.amount) as amount, cities.cname as city
                        from users 
                        LEFT JOIN monets_traffic on monets_traffic.reciever_user_id = ".$_GET['id']." and monets_traffic.type_id = 4 and monets_traffic.thanks_to = users.id
                        LEFT JOIN  cities on users.city_id = cities.id
                        where users.parent_id = ".$_GET['id']."  AND ". $query ."
                        GROUP BY users.id";

                $recordsTotal = Users::find()->where(['parent_id' => $_GET['id']])->count();
                $recordsFiltered = Users::find()->where(['parent_id' => $_GET['id']])->andWhere($condition)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);
                $model = $command->queryAll();

            }

            else if ($name == "admins/moderators") {


                if(Helpers::getMyRole() == 3){
                    $sql = "SELECT system_users.id, system_users.first_name, system_users.last_name, system_users.last_edit, system_users.phone, system_users.email as email, taxi_park.name as taxipark,
                               group_concat(distinct c.cname) as cities,
                               count(distinct driver.id)      as drivers,
                               count(distinct client.id)      as clients,
                               IFNULL(sum(distinct moderators_money.amount), 0) as sum,
                               IFNULL(sum(distinct monets_traffic.amount), 0) as sum2
                        from system_users
                               inner join system_users_cities suc on system_users.id = suc.system_user_id
                               inner join cities c on suc.city_id = c.id
                               inner join taxi_park on taxi_park.id = system_users.taxi_park_id
                               left join users driver on driver.city_id = c.id and driver.role_id = 2
                               left join users client on client.city_id = c.id and client.role_id = 1
                               left join moderators_money on moderators_money.moderator_id = system_users.id
                               left join monets_traffic on monets_traffic.sender_user_id = system_users.id
                        where system_users.role_id = 4
                        and system_users.deleted = 0
                        and c.id in  ( ". Helpers::getCitiesString() ." )
                        and " . $search_condition ." 
                        and " . $query ." 
                        group by system_users.id;";

                }else{
                    $sql = "SELECT system_users.id, system_users.first_name, system_users.last_name, system_users.last_edit, system_users.phone, system_users.email as email, taxi_park.name as taxipark,
                               group_concat(distinct c.cname) as cities,
                               count(distinct driver.id)      as drivers,
                               count(distinct client.id)      as clients,
                               IFNULL(sum(distinct moderators_money.amount), 0) as sum,
                               IFNULL(sum(distinct monets_traffic.amount), 0) as sum2
                        from system_users
                               inner join taxi_park on taxi_park.id = system_users.taxi_park_id
                               inner join system_users_cities suc on system_users.id = suc.system_user_id
                               inner join cities c on suc.city_id = c.id
                               left join users driver on driver.city_id = c.id and driver.role_id = 2
                               left join users client on client.city_id = c.id and client.role_id = 1
                               left join moderators_money on moderators_money.moderator_id = system_users.id
                               left join monets_traffic on monets_traffic.sender_user_id = system_users.id
                        where system_users.role_id = 4
                        and system_users.deleted = 0
                        and c.id in  ( ". $_GET['ids'] ." )
                        and " . $search_condition ." 
                        and " . $query ." 
                        group by system_users.id;";
                }

                $recordsTotal = SystemUsers::find()->where(['role_id' => 4])->count();
                $recordsFiltered = SystemUsers::find()->where(['role_id' => 4])->andWhere($condition)->andWhere($search_condition)->count();
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);

                $model = $command->queryAll();

            }elseif($name == "tp_moderators"){
                $recordsTotal = SystemUsers::find()->where(['role_id' => 4])->andWhere($query)->andWhere(['taxi_park_id' => Helpers::getMyTaxipark()])->count();
                $recordsFiltered = SystemUsers::find()->where(['role_id' => 4])->andWhere($condition)->andWhere($query)->andWhere($search_condition)->andWhere(['taxi_park_id' => Helpers::getMyTaxipark()])->count();

                $sql = "SELECT system_users.id, system_users.first_name, system_users.last_name, system_users.last_edit, system_users.phone, system_users.email,
                               group_concat(distinct c.cname) as cities,
                               count(distinct driver.id)      as drivers,
                               count(distinct client.id)      as clients,
                               IFNULL(sum(distinct monets_traffic.amount), 0) as sum,
                               IFNULL(sum(distinct moderators_money.amount), 0) as sum2
                        from system_users
                               inner join system_users_cities suc on system_users.id = suc.system_user_id
                               inner join cities c on suc.city_id = c.id
                               left join users driver on driver.city_id = c.id and driver.role_id = 2 and driver.taxi_park_id = system_users.taxi_park_id 
                               left join users client on client.city_id = c.id and client.role_id = 1 and client.taxi_park_id = system_users.taxi_park_id
                               left join monets_traffic on monets_traffic.sender_user_id = system_users.id
                               left join moderators_money on moderators_money.moderator_id = system_users.id

                        where system_users.role_id = 4
                        and system_users.taxi_park_id = " . Helpers::getMyTaxipark() ."
                        and ". $search_condition ."  
                        group by system_users.id;";

                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);

                $model = $command->queryAll();

            }

            else if ($name == "stats-drivers") {
                $recordsTotal = Users::find()->where(['role_id' => 2])->andWhere($query)->count();
                $recordsFiltered = Users::find()->where(['role_id' => 2])->andWhere($condition)->andWhere($query)->count();
                if(getMyRole() == 9){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.is_active as active, genders.gender, users.name, users.rating, users.phone, tp.name as tp, users.created, users.balance, cities.cname as city, submodel.model submodel, car.number, model.model')
                        ->from('users')
                        ->innerJoin('cities', 'users.city_id = cities.id')
                        ->leftJoin('users_cars car', 'users.id = car.user_id')
                        ->leftJoin('car_models submodel', 'car.car_id = submodel.id')
                        ->innerJoin('car_models model', 'submodel.parent_id = model.id')
                        ->innerJoin('taxi_park tp', 'tp.id= users.taxi_park_id')
                        ->leftJoin('genders', 'users.gender_id = genders.id')
                        ->where($query)
                        ->andWhere(['users.role_id' => 2])
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->limit($length)
                        ->offset($start)
                        ->groupBy(['users.id', 'car.id'])
                        ->all();
                }elseif (getMyRole() == 3){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.is_active as active, tp.name as tp, genders.gender, users.name, users.rating, users.phone, users.created, users.balance, cities.cname as city, submodel.model submodel, car.number, model.model')
                        ->from('users')
                        ->innerJoin('cities', 'users.city_id = cities.id')
                        ->innerJoin('taxi_park tp', 'tp.id= users.taxi_park_id')

                        ->leftJoin('users_cars car', 'users.id = car.user_id')
                        ->leftJoin('car_models submodel', 'car.car_id = submodel.id')
                        ->innerJoin('car_models model', 'submodel.parent_id = model.id')
                        ->leftJoin('genders', 'users.gender_id = genders.id')
                        ->where($query)
                        ->andWhere(['users.role_id' => 2])
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->andWhere(getCitiesCondition())
                        ->limit($length)
                        ->offset($start)
                        ->groupBy(['users.id', 'car.id'])
                        ->all();

                }elseif (getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.is_active as active, tp.name as tp, genders.gender, users.name, users.rating, users.phone, users.created, users.balance, cities.cname as city, submodel.model submodel, car.number, model.model')
                        ->from('users')
                        ->innerJoin('cities', 'users.city_id = cities.id')
                        ->leftJoin('users_cars car', 'users.id = car.user_id')
                        ->innerJoin('taxi_park tp', 'tp.id= users.taxi_park_id')

                        ->leftJoin('car_models submodel', 'car.car_id = submodel.id')
                        ->innerJoin('car_models model', 'submodel.parent_id = model.id')
                        ->leftJoin('genders', 'users.gender_id = genders.id')
                        ->where($query)
                        ->andWhere($search_condition)
                        ->andWhere(['users.role_id' => 2])
                        ->andWhere($condition)
                        ->andWhere('users.taxi_park_id = ' . Helpers::getMyTaxipark())
                        ->limit($length)
                        ->offset($start)
                        ->groupBy(['users.id', 'car.id'])
                        ->all();
                }

            }
            else if ($name == "stats-drivers/driver_stat") {

                $sql = "select users.name,
                           users.id as id,
                           c.cname as city,
                           park.name as taxipark,
                           sum(case when monets_traffic.type_id = 5 or monets_traffic.type_id=6 then monets_traffic.amount else 0 end) as orders,
                           sum(case when monets_traffic.type_id = 4 then monets_traffic.amount else 0 end) as bonus_income,
                           sum(case when monets_traffic.type_id = 7 then monets_traffic.amount else 0 end) as kk,
                           sum(case when outcome.type_id = 2 or outcome.type_id = 3 then monets_traffic.amount else 0 end) as taxiplus,
                           sum(case when outcome.reciever_tp_id = users.taxi_park_id then monets_traffic.amount else 0 end) as taxipark_monets,
                           sum(case when outcome.type_id = 4 then outcome.amount else 0 end) as bonus,
                           sum(outcome.amount) as spent_money,
                           count(specific_orders.id) + count(orders.id) as orders_count,
                           IFNULL(sum(orders.price), 0)  as summa
                    from users
                      inner join taxi_park park on users.taxi_park_id = park.id
                      inner join cities c on users.city_id = c.id
                      left join monets_traffic on monets_traffic.reciever_user_id = users.id
                      left join monets_traffic outcome on outcome.sender_user_id = users.id
                      left join log_types on monets_traffic.type_id = log_types.id
                      left join orders on orders.driver_id = users.id
                      left join specific_orders on users.id = specific_orders.driver_id
                    where users.id = " . $_GET['id'] ."
                     and ". $query ." 
                     and " . $condition . " 
                    group by users.id;";

                $recordsTotal = 1;
                $recordsFiltered = 1;
                $connection = Yii::$app->getDb();
                $command = $connection->createCommand($sql);
                $model = $command->queryAll();

            }
            else if ($name == "stats-referals") {
                if(getMyRole() == 9){
                    $model = (new \yii\db\Query())
                        ->select('parent.id, parent.phone, parent.name, count(distinct users.id) referals, c.cname as city')
                        ->from('users')
                        ->innerJoin('users parent', 'parent.id=users.parent_id')
                        ->leftJoin('cities c', 'parent.city_id = c.id')
//                        ->leftJoin('monets_traffic mt', 'mt.reciever_user_id = parent.id')
//                        ->andWhere(['mt.type_id' => 4])
                        ->limit($length)
                        ->andWhere($query)
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->offset($start)
                        ->groupBy('parent.id')
                        ->all();
                }elseif(getMyRole() == 3){
                    $model = (new \yii\db\Query())
                        ->select('parent.id, parent.phone, parent.name, count(distinct users.id) referals, c.cname as city, sum(distinct mt.amount) as bonuses')
                        ->from('users')
                        ->innerJoin('users parent', 'parent.id=users.parent_id')
                        ->innerJoin('cities c', 'parent.city_id = c.id')
                        ->leftJoin('monets_traffic mt', 'mt.reciever_user_id = parent.id')
//                        ->andWhere(['mt.type_id' => 4])
                        ->andWhere('c.id in (' . getCitiesString() .')')
                        ->limit($length)
                        ->andWhere($query)
                        ->andWhere($condition)
                        ->andWhere($search_condition)
                        ->offset($start)
                        ->groupBy('parent.id')
                        ->all();
                }elseif(getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('parent.id, parent.phone, parent.name, count(distinct users.id) referals, c.cname as city, sum(distinct mt.amount) as bonuses')
                        ->from('users')
                        ->innerJoin('users parent', 'parent.id = users.parent_id')
                        ->leftJoin('cities c', 'parent.city_id = c.id')
                        ->leftJoin('monets_traffic mt', 'mt.reciever_user_id = parent.id')
//                        ->andWhere(['mt.type_id' => 4])
                        ->andWhere('parent.taxi_park_id = '. Helpers::getMyTaxipark())
                        ->limit($length)
                        ->andWhere($query)
                        ->andWhere($search_condition)
                        ->andWhere($condition)
                        ->offset($start)
                        ->groupBy('parent.id')
                        ->all();
                }

                $recordsTotal = count($model);
                $recordsFiltered = count($model);

            }

            else if ($name == "stats-clients") {
                $recordsTotal = Users::find()->andWhere($query)->count();
                $recordsFiltered = Users::find()->andWhere($condition)->andWhere($query)->count();
                if(getMyRole() == 9){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.name, users.phone, c.cname as city, users.created, count(distinct child.id) as referals, users.balance')
                        ->from('users')
                        ->leftJoin('users child', 'child.parent_id = users.id')
                        ->innerJoin('cities c', 'users.city_id = c.id')
                        ->where(['users.role_id' => 1])
                        ->limit($length)
                        ->andWhere($condition)
                        ->andWhere($query)
                        ->andWhere($search_condition)
                        ->offset($start)
                        ->groupBy('users.id')
                        ->all();
                }elseif (getMyRole() == 3){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.name, users.phone, c.cname as city, users.created, count(distinct child.id) as referals, users.balance')
                        ->from('users')
                        ->leftJoin('users child', 'child.parent_id = users.id')
                        ->innerJoin('cities c', 'users.city_id = c.id')
                        ->where(['users.role_id' => 1])
                        ->andWhere('c.id in ('.getCitiesString().')')
                        ->limit($length)
                        ->andWhere($search_condition)
                        ->offset($start)
                        ->groupBy('users.id')
                        ->all();

                }elseif (getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('users.id, users.name, users.phone, c.cname as city, users.created, count(distinct child.id) as referals, users.balance')
                        ->from('users')
                        ->leftJoin('users child', 'child.parent_id = users.id')
                        ->innerJoin('cities c', 'users.city_id = c.id')
                        ->where(['users.role_id' => 1])
                        ->andWhere(['users.taxi_park_id' => Helpers::getMyTaxipark()])
                        ->limit($length)
                        ->andWhere($search_condition)
                        ->offset($start)
                        ->groupBy('users.id')
                        ->all();

                }

            }

            else if ($name == "stats_clients/client-stat") {
                $recordsTotal = Users::find()->andWhere($query)->count();
                $recordsFiltered = Users::find()->andWhere($condition)->andWhere($query)->count();
                $model = (new \yii\db\Query())
                    ->select('users.id, count(o.id) as orders,
                                       sum(case when o.payment_type = 1 then 1 else 0 end ) as nal,
                                       sum(case when o.payment_type = 2 then 1 else 0 end ) as beznal,
                                       sum(case when o.payment_type = 3 then 1 else 0 end ) as bonus')
                    ->from('users')
                    ->innerJoin('orders o', 'o.user_id = users.id')
                    ->where(['users.id' => $_GET['id']])
                    ->andWhere($condition)
                    ->andWhere($query)
                    ->andWhere($search_condition)
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "dispatchers") {
                $recordsTotal = SystemUsers::find()->andWhere($query)->andWhere(['role_id' => 8])->count();
                $recordsFiltered = SystemUsers::find()->andWhere($condition)->andWhere(['role_id' => 8])->andWhere($query)->andWhere($search_condition)->count();
                if(Helpers::getMyRole() == 9){
                    $model = (new \yii\db\Query())
                        ->select('su.*, count(distinct  o.id) as orders, count(case when o.status = 0 then 1 end ) as cancelled')
                        ->from('system_users su')
                        ->leftJoin('orders o', 'su.id = o.dispatcher_id')
                        ->where(['su.role_id' => 8])
                        ->andWhere('su.deleted = false')
                        ->limit($length)
                        ->offset($start)
                        ->groupBy('su.id')
                        ->all();
                }else if(Helpers::getMyRole() == 3){
                    $model = (new \yii\db\Query())
                        ->select('su.*, count(distinct  o.id) as orders, count(case when o.status = 0 then 1 end ) as cancelled')
                        ->from('system_users su')
                        ->leftJoin('orders o', 'su.id = o.dispatcher_id')
                        ->where(['su.role_id' => 8])
                        ->andWhere('su.city_id in('. Helpers::getCitiesString() .')')
                        ->andWhere('su.deleted = false')
                        ->limit($length)
                        ->offset($start)
                        ->groupBy('su.id')
                        ->all();
                }else if(Helpers::getMyRole() == 5){
                    $model = (new \yii\db\Query())
                        ->select('su.*, count(distinct  o.id) as orders, count(case when o.status = 0 then 1 end ) as cancelled')
                        ->from('system_users su')
                        ->leftJoin('orders o', 'su.id = o.dispatcher_id')
                        ->where(['su.role_id' => 8])
                        ->andWhere('su.taxi_park_id = ' . Helpers::getMyTaxipark())
                        ->andWhere('su.deleted = false')

                        ->limit($length)
                        ->offset($start)
                        ->groupBy('su.id')
                        ->all();
                }

            }
            else if ($name == "dispatchers_orders") {
                $recordsTotal = Orders::find()->andWhere($query)->andWhere(['dispatcher_id' => $_GET['id']])->count();
                $recordsFiltered = Orders::find()->andWhere($condition)->andWhere(['dispatcher_id' => $_GET['id']])->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('orders.*, u.id as uid, u.phone')
                    ->from('orders')
                    ->innerJoin('system_users su', 'orders.dispatcher_id = su.id')
                    ->innerJoin('users u', 'orders.user_id = u.id')
                    ->where(['orders.dispatcher_id' => $_GET['id']])
                    ->andWhere('su.deleted = 0')
                    ->andWhere($query)
                    ->andWhere($condition)
                    ->limit($length)
                    ->offset($start)
                    ->groupBy('orders.id')
                    ->all();
            }

            else if ($name == "taxi_park") {
                $recordsTotal = TaxiPark::find()->andWhere($query)->count();
                $recordsFiltered = TaxiPark::find()->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                if(getMyRole() == 9){
                    $cond = 'tp.id IS NOT NULL';
                }elseif (getMyRole() == 3){
                    $cond = 'c.id in ('. getCitiesString() .')';
                }
                $model = (new \yii\db\Query())
                    ->select('tp.*, c.cname as city,  (case when tp.type < 15 then 1 else 2 end) as typen')
                    ->from('taxi_park tp')
                    ->innerJoin('cities c', 'tp.city_id = c.id')
                    ->andWhere($cond)
                    ->andWhere($condition)
                    ->andWhere($query)
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "stats-tp") {
                $recordsTotal = TaxiPark::find()->andWhere($query)->count();
                $recordsFiltered = TaxiPark::find()->andWhere($condition)->andWhere($query)->count();
                if(getMyRole() == 9){
                    $cond = 'taxi_park.id IS NOT NULL';
                }elseif (getMyRole() == 3){
                    $cond = 'c.id in ('. getCitiesString() .')';
                }
                $model = (new \yii\db\Query())
                    ->select('taxi_park.*, c.cname as city,  (case when taxi_park.type < 15 then 1 else 2 end) as typen')
                    ->from('taxi_park')
                    ->innerJoin('cities c', 'taxi_park.city_id = c.id')
                    ->andWhere($cond)
                    ->andWhere($condition)
                    ->andWhere($search_condition)
                    ->andWhere($query)
                    ->limit($length)
                    ->offset($start)
                    ->all();
            }
            else if ($name == "stats-tp/tp-stat") {

                $model = (new \yii\db\Query())
                    ->select('taxi_park.*, c.cname as city,
                                       sum(case when taxi_park.type < 15 and income.type_id = 2 then income.amount end)  as income_1,
                                       sum(case when taxi_park.type < 15 and outcome.type_id = 1 then outcome.amount end)  as outcome_1,
                                       sum(case when taxi_park.type > 14 and income.type_id = 2 then income.amount end)  as income_pay,
                                       sum(case when taxi_park.type > 14 and income.type_id = 4 then income.amount end)  as income_bonus,
                                       sum(case when taxi_park.type > 14 and income.type_id = 7 then income.amount end)  as income_kk,
                                       sum(case when taxi_park.type > 14 and outcome.type_id = 2 then outcome.amount end) as outcome_tp,
                                       sum(case when taxi_park.type > 14 and outcome.type_id = 4 then outcome.amount end) as outcome_bonus')
                    ->from('taxi_park')
                    ->innerJoin('cities c', 'taxi_park.city_id = c.id')
                    ->leftJoin('monets_traffic income', ' taxi_park.id = income.reciever_tp_id ')
                    ->leftJoin('monets_traffic outcome',  ' taxi_park.id = outcome.sender_tp_id ')
                    ->where(['taxi_park.id' => $_GET['id']])
                    ->andWhere($query)
                    ->andWhere($condition)
                    ->limit($length)
                    ->offset($start)
                    ->all();

                $recordsTotal = 1;
                $recordsFiltered = 1;

            }
            else if ($name == "stat_company") {
                $recordsTotal = Users::find()->where(['company_id' => $_GET['id']])->andWhere($query)->count();
                $recordsFiltered = Users::find()->where(['company_id' => $_GET['id']])->andWhere($condition)->andWhere($query)->andWhere($search_condition)->count();
                $model = (new \yii\db\Query())
                    ->select('users.name, users.id, users.phone, users.balance, sum(distinct m.amount) as monets')
                    ->from('users')
                    ->leftJoin('monets_traffic m', 'users.id = m.thanks_to')
                    ->where(['users.company_id' => $_GET['id']])
                    ->andWhere(['m.type_id' => 7])
                    ->andWhere($query)
                    ->andWhere($condition)
                    ->limit($length)
                    ->offset($start)
                    ->groupBy('users.id')
                    ->all();
            }


            $array['draw'] = $draw;
            $array['recordsTotal'] = $recordsTotal;
            $array['recordsFiltered'] = $recordsFiltered;
            $array['data'] = $model;

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $array;
        }
    }
}
function getCitiesCondition(){
    $me = SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
    $my_cities = SystemUsersCities::find()->where(['system_user_id' => $me->id])->all();
    $in = '';
    foreach ($my_cities as $k => $v){
        if($k == count($my_cities) - 1){
            $in .= $v->city_id;
        }else{
            $in .= $v->city_id . ', ';
        }
    }
    $cond = 'cities.id in (' . $in . ')';
    return $cond;
}
function getCitiesString(){
    $me = SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
    $my_cities = SystemUsersCities::find()->where(['system_user_id' => $me->id])->all();
    $in = '';
    foreach ($my_cities as $k => $v){
        if($k == count($my_cities) - 1){
            $in .= $v->city_id;
        }else{
            $in .= $v->city_id . ', ';
        }
        //        $cond = 'cities.id in (' . $in . ')';
    }
    return $in;
}
function getMyRole(){
    return Yii::$app->session->get('profile_role');
}
?>
