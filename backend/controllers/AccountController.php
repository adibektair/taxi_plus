<?php
namespace backend\controllers;

use backend\models\DriverAvatars;
use backend\models\Message;
use backend\models\Payment;
use backend\models\Cities;
use backend\models\Company;
use backend\models\MonetsTraffic;
use backend\models\OrderSettings;
use backend\models\MoneyRequest;
use backend\models\SystemUsers;
use backend\models\UsersAvatars;
use backend\models\Log;

use DateTime;
use phpDocumentor\Reflection\Types\Integer;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use backend\models\Users;
use backend\models\OrderTypes;
use backend\models\SpecificOrders;
use backend\models\Region;
use backend\models\CarModels;
use backend\models\Orders;
use backend\models\SavedAddresses;
use backend\models\Sessions;
use backend\models\Facilities;
use backend\models\Services;
use backend\models\PossibleDrivers;
use backend\models\DriversServices;
use backend\models\UsersCars;
use backend\models\DriversRatings;
use backend\models\Complaints;
use backend\models\RejectedOrders;
use yii\web\Response;
use backend\components\Helpers;
use backend\models\TaxiPark;
use backend\models\TaxiParkPrivileges;
use backend\models\TempPass;
use backend\models\DriversFacilities;
use backend\models\TaxiParkServices;
use backend\models\DriversAccess;
use backend\models\IntercityPricelist;
use backend\models\UsersPrivileges;
use backend\models\Privileges;
use backend\models\IntercityOrder;
use backend\models\IntercityOrdersClient;
use yii\rest\ActiveController;
use yii\web\User;


class AccountController extends Controller
{

    

    public function actionKassa(){
        header("Content-type: text/xml; charset=utf-8");
        $action = $_GET['action'];
        $amount = $_GET['amount'];
        $receipt = $_GET['receipt'];
        $date = $_GET['date'];
        $number = $_GET['number'];
        $success = 0;
        $comment = "Успешно";
        $user = Users::findOne(['phone' => $number]);


        if($action == 'check'){

            if($user == null){
                $success = 2;
                $comment = "Абонента не существует";
            }else{
                $success = 0;
            }
            $response = "<?xml version=”1.0” encoding=”utf-8”?>
                            <response>
                            <code>". $success ."</code>
                            <message>". $comment ."</message>
                          </response>";
            echo  $response;
            return;

        }elseif ($action == 'payment'){
            if($amount == null){
                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <code>3</code>
                             <message>sum is missing</message>
                            </response>";
                echo  $response;
                return;
            }
            if($user != null){
                $user->balance += $amount;
                $user->save();
                $success = 0;
                $response = "<?xml version=”1.0” encoding=”utf-8”?>
                            <response>
                            <code>". $success ."</code>
                            <message>". $comment ."</message>
                          </response>";

                echo  $response;
                return;
            }else{
                $comment = "Абонента не существует";

                $response = "<?xml version=”1.0” encoding=”utf-8”?>
                            <response>
                            <code>". 2 ."</code>
                            <message>". $comment ."</message>
                          </response>";

                echo  $response;
                return;
            }
        }
    }


    public function actionKazpost(){
        header("Content-type: text/xml; charset=utf-8");
        $command = $_GET['command'];
        $sum = $_GET['sum'];
        $id = $_GET['txn_id'];
        $account = $_GET['account'];
        $success = 0;
        $user = Users::findOne(['phone' => $account]);
        if($user != null){
            $success = 1;
        }

        if($command == 'check'){
            $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                     <response>
                         <result>". $success ."</result>
                         <comment></comment>
                     </response>";

        }elseif ($command == 'pay'){
            if($sum == null){
                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <result>0</result>
                             <comment>sum is missing</comment>
                            </response>";
                echo  $response;
                return;
            }
            if($user != null){
                $user->balance += $sum;
                $user->save();
                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <sum>".$sum."</sum>
                             <result>". $success ."</result>
                             <comment>success</comment>
                            </response>";
                echo  $response;
                return;
            }else{

                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <sum>".$sum."</sum>
                             <result>". $success ."</result>
                             <comment>user not found</comment>
                            </response>";
                echo  $response;
                return;
            }
        }

        echo $response;
    }



    public function actionQiwi(){
        header("Content-type: text/xml; charset=utf-8");

        $command = $_GET['command'];
        $sum = $_GET['sum'];
        $id = $_GET['txn_id'];
        $account = $_GET['account'];
        $success = 0;
        $user = Users::findOne(['phone' => $account]);
        if(!isset($user)){
            $success = 5;
        }
        if($command == 'check'){

            $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                     <response>
                         <osmp_txn_id>". $id ."</osmp_txn_id>
                         <result>". $success ."</result>
                         <comment></comment>
                     </response>";

        }elseif ($command == 'pay'){
            if($user != null){
                $user->balance += $sum;
                $user->save();
                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <osmp_txn_id>". $id ."</osmp_txn_id>
                             <prv_txn>2016</prv_txn>
                             <sum>".$sum."</sum>
                             <result>". $success ."</result>
                             <comment>OK</comment>
                            </response>";
                echo  $response;
                return;
            }else{

                $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <osmp_txn_id>". $id ."</osmp_txn_id>
                             <prv_txn>2016</prv_txn>
                             <sum>".$sum."</sum>
                             <result>". $success ."</result>
                             <comment>OK</comment>
                            </response>";
                echo  $response;
                return;
            }

            $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                            <response>
                             <osmp_txn_id>". $id ."</osmp_txn_id>
                             <prv_txn>2016</prv_txn>
                             <sum>".$sum."</sum>
                             <result>". $success ."</result>
                             <comment>OK</comment>
                            </response>";
        }

        echo $response;
    }


    public function actionGetAmount(){
        $token = $_POST['token'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->role_id == 1){
            $drivers_count = Users::find()->where(['role_id' => 2])->andWhere(['city_id' => $user->city_id])->count();
            $ladies_count = Users::find()->where(['role_id' => 2])->andWhere(['gender_id' => 1])->andWhere(['city_id' => $user->city_id])->count();
            $inva_count = UsersCars::find()->where(['type' => 4])->innerJoin('users', 'users.id = users_cars.user_id')->andWhere(['users.city_id' => $user->city_id])->count();
            $gruzo_count = UsersCars::find()->where(['type' => 2])->innerJoin('users', 'users.id = users_cars.user_id')->andWhere(['users.city_id' => $user->city_id])->count();
            $evak_count = UsersCars::find()->where(['type' => 3])->innerJoin('users', 'users.id = users_cars.user_id')->andWhere(['users.city_id' => $user->city_id])->count();
            $mej_count = SpecificOrders::find()->where(['order_type_id' => 1])->andWhere('driver_id is not null')->count();

            $response["state"] = 'success';
            $response["taxi"] = $drivers_count;
            $response["lady"] = $ladies_count;
            $response["inva"] = $inva_count;
            $response["gruzotaxi"] = $gruzo_count;
            $response["ekavuator"] = $evak_count;
            $response["mejgorod"] = $mej_count;

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            $taxi_count = Orders::find()->innerJoin('users', 'users.id = orders.user_id')->andWhere(['users.city_id' => $user->city_id])->andWhere(['orders.deleted' => 0])->andWhere(['orders.status' => 1])->andWhere('orders.driver_id is NULL')->count();
            $mej_count = SpecificOrders::find()->where(['order_type_id' => 1])->andWhere(['driver_id' => null])->count();
            $gruzo_count = SpecificOrders::find()->where(['order_type_id' => 2])->andWhere(['driver_id' => null])->count();
            $evak_count = SpecificOrders::find()->where(['order_type_id' => 3])->andWhere(['driver_id' => null])->count();
            $inva_count = SpecificOrders::find()->where(['order_type_id' => 4])->andWhere(['driver_id' => null])->count();
            $response["state"] = 'success';
            $response["taxi"] = $taxi_count;
            $response["inva"] = $inva_count;
            $response["gruzotaxi"] = $gruzo_count;
            $response["ekavuator"] = $evak_count;
            $response["mejgorod"] = $mej_count;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }
    }

    public function actionMoneyRequest(){
        $token = $_POST['token'];
        $amount = $_POST['amount'];
        $card_number = $_POST['card_number'];
        if($amount < 40000){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'fail';
            $response["message"] = '40000 is minimum';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->balance >= $amount){

            $model = new MoneyRequest();
            $model->card_number = $card_number;
            $model->user_id = $user->id;
            $model->amount = $amount;
            $model->created = strtotime('now');
            if($model->save()){
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'success';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'fail';
                $response["message"] = 'Unable to save model';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }

        }
        else{
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionGetNews(){
        $token = $_POST['token'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $cars = (new \yii\db\Query())
            ->select('messages.*')
            ->from('messages')
            ->where(['role_id' => $user->role_id])
            ->andWhere(['taxi_park_id' => $user->taxi_park_id])
            ->all();
        Yii::$app->response->statusCode = 200;
        $response["messages"] = $cars;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionGetMessage(){
        $id = $_POST['id'];
        $model = Message::findOne(['id' => $id]);
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["message"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionGetMyBalance(){
        $token = $_POST['token'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->role_id == 2){
            $monets = MonetsTraffic::find()->where(['type_id' => 5])->andWhere(['reciever_user_id' => $user->id])->sum('amount');
            Yii::$app->response->statusCode = 200;
            if($monets > $user->balance){
                $monets = $user->balance;
            }

            $response["orders_monets"] = intval($monets);
            $response["added_monets"] = $user->balance - $monets;
            $response["balance"] = $user->balance;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{
            $monets = MonetsTraffic::find()->where(['type_id' => 4])->andWhere(['reciever_user_id' => $user->id])->sum('amount');
            $response['monets'] = intval($monets);
            $response['balance'] = $user->balance;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }


    public function actionMessage(){
        // token phone text
        $token = $_POST['token'];
        $phone = $_POST['phone'];
        $text = $_POST['text'];

        $user = Users::findOne(['phone' => $phone]);
        $author = Users::findOne(['token' => $token]);
        $data = array("author" => $author->name, "text" => $text, "type" => 2);
        if($user->platform == 1){
            $data1 = array('to' => $user->push_id,
                'notification' => $data);
        }else{
            $data1 = array('to' => $user->push_id,
                "data" => $data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);


    }


    public function actionBalance(){
        $u = Users::findOne(['name' => $_POST['name']]);
        $u->balance = 10000;
        $u->save();
    }
    public function CreateOrder($price, $order_id){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <TKKPG>
                    <Request>
                        <Operation>CreateOrder</Operation>
                        <Language>RU</Language>
                        <Order>
                            <OrderType>Purchase</OrderType>
                            <Merchant>HALAL15000695</Merchant>
                            <Amount>' . $price . '</Amount>
                            <Currency>398</Currency>
                            <Description>Оплата поездки в TAXI PLUS</Description>
                            <ApproveURL>/testshopPageReturn.jsp</ApproveURL>
                            <CancelURL>/testshopPageReturn.jsp</CancelURL>
                            <DeclineURL>/testshopPageReturn.jsp</DeclineURL>
                            <AddParams>
                                <FA-DATA>Phone=22211444</FA-DATA>
                                <OrderExpirationPeriod>30</OrderExpirationPeriod>
                            </AddParams>
                            <Fee></Fee>
                        </Order>
                    </Request>
                </TKKPG>';
        //  87774401460 eldos fortebank
        $url = 'https://epaypost.fortebank.com/Exec';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $status = $array['Response']['Status'];
        if($status == 00){
            $orderid = $array['Response']['Order']['OrderID'];
            $sessionid = $array['Response']['Order']['SessionID'];
            $url = $array['Response']['Order']['URL'];
            $payment = new Payment();
            $payment->order_id = $order_id;
            $payment->payment_id = $orderid;
            $payment->session_id = $sessionid;
            $payment->save();
            $response["state"] = 'success';
            $response["order_id"] = $order_id;
            $response["url"] = $url . '?SESSIONID='.$sessionid.'&ORDERID='.$orderid;
            ignore_user_abort(true);
            set_time_limit(0);

            ob_start();

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            Yii::$app->response->send();
            $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
            header($serverProtocol . ' 200 OK');
            // Disable compression (in case content length is compressed).
            header('Content-Encoding: none');
            header('Content-Length: ' . ob_get_length());

            // Close the connection.
            header('Connection: close');

            ob_end_flush();
            ob_flush();
            flush();

        }else{
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'Online payments unavailable';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }


    }

    public function GetOrderStatus($orderid, $sessionid){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <TKKPG>
                        <Request>
                            
                            <Operation>GetOrderStatus</Operation>
                            <Language>RU</Language>
                            <Order>
                                <Merchant>HALAL15000695</Merchant>
                                <OrderID>' . $orderid .'</OrderID>
                            </Order>
                            <SessionID>' . $sessionid . '</SessionID>
                        </Request>
                    </TKKPG>';

        $url = 'https://epaypost.fortebank.com/Exec';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);
        curl_close($ch);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        $status = $array['Response']['Order']['OrderStatus'];
        if($status != 'APPROVED'){
            return false;
        }else{
            return true;
        }


    }





    public function actionChangeRole(){
        $token = $_POST['token'];
        $role = $_POST['role_id'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'unauthorized';

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($role == 2){
            $cars = (new \yii\db\Query())
                ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
                ->from('users_cars')
                ->where(['user_id' => $user->id])
                ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
                ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
                ->all();
            if(count($cars) < 1){
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'fail';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;

            }
        }

        $user->role_id = $role;
        if($user->save()){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["cars"] = $cars;

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{
            Yii::$app->response->statusCode = 501;
            $response["state"] = 'fail';
            $response["message"] = 'hz wws';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }


    public function actionGetRegions(){

        $model = (new \yii\db\Query())
            ->select('cities.cname, cities.id, cities.region_id, regions.name')
            ->from('cities')
            ->innerJoin('regions', 'regions.id = cities.region_id')
            ->all();

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["cities"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionSetCity(){
        $token = $_POST['token'];
        $id = $_POST['city_id'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $user->city_id = $id;
        if($user->save()){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{
            Yii::$app->response->statusCode = 501;
            $response["state"] = 'fail';
            $response["message"] = 'hz wws';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionDriverSignUp()
    {
        $json = json_decode(file_get_contents("php://input"), true);
        $model = Users::find()->where(['token' => $json['token']])->one();
        $model->role_id = 2;
        $model->gender_id = $json['gender_id'];
        $car = new UsersCars();
        $car->number = $json['car_number'];
        if($model->taxi_park_id == null){
            $model->taxi_park_id = 0;
        }
        if($model->year_of_birth == null){
            $model->year_of_birth = $json['year_of_birth'];
        }
        $model->gender_id = $json['gender_id'];
        $car->year = $json['car_year'];
        $car->car_id = $json['car_model'];
        $car->user_id = $model->id;

        $car->seats_number = $json['seats_number'];
        if($json['tonns'] != null){
            $car->tonns = $json['tonns'];
        }
        $car->type = $json['type'];

        if($json['tonns'] != null){
            $car->tonns = $json['tonns'];
        }
        if($json['body'] != null){
            $car->body = $json['body'];
        }
        $car->save();

        $cars = (new \yii\db\Query())
            ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
            ->from('users_cars')
            ->where(['user_id' => $model->id])
            ->andWhere('users_cars.id = ' . $car->id)
            ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
            ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
            ->one();

        if($model->save()){
            $ds = new DriversServices();
            $ds-> driver_id = $model->id;
            if($json['car_year'] > 2012){
                $ds->service_id = 2;
            }else{
                $ds->service_id = 1;
            }
            $ds->save();
            if($json['facilities'] != null){
                foreach ($json['facilities'] as $v){
                    $df = new DriversFacilities();
                    $df->driver_id = $model->id;
                    $df->facility_id = $v;
                    $df->save();
                }
            }
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["car"] = $cars;

            $response["message"] = 'successfuly registered1';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{

            Yii::$app->response->statusCode = 200;
            $response["state"] = 'error';
            $response["message"] = 'che to ne to';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }


    public function actionGetFacilities(){
        $all = Facilities::find()->all();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["Facilities"] = $all;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetTaxiParks(){
        $parks = TaxiPark::find()->all();
        $response["taxi_parks"] = $parks;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionSendSms(){
        $auth_key = Yii::$app->security->generateRandomString();
        $phone = $_POST['phone'];
        (new Query)
            ->createCommand()
            ->delete('temporary_passwords', ['phone' => $phone])
            ->execute();

        $x = 3; // Amount of digits
        $min = pow(10,$x);
        $max = pow(10,$x+1)-1;
        $value = rand($min, $max);
        $code_model = new TempPass();
        $code_model->phone = $phone;
        $code_model->code = $value;
        $code_model->save();

        $data = array
        (
            'recipient' => $phone,
            'text' => 'Код для авторизации в Taxi Plus: ' . $value,
            'apiKey' => 'kz17291378da58235313e30f6a06f0256460cdf28c5b57e8fb59642f35c102216d68f6'
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/x-www-form-urlencoded'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://api.mobizon.kz/service/Message/SendSMSMessage/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
//        echo $data;

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
//        return $server_output;
//        echo $server_output;
        return $response;
    }

    public function actionVerifyCode(){
        $auth_key = Yii::$app->security->generateRandomString();
        $json = json_decode(file_get_contents("php://input"), true);

        $phone = $json['phone'];
        $code = $json['code'];
        $user_id = $json['user_id'];

        $check = TempPass::find()->where(['phone' => $phone])->andWhere(['code' => $code])->one();
        if($check != null){

            $user = Users::find()->where(['phone' => $phone])->one();
            if($user != null){
                Yii::$app->response->statusCode = 200;
                $user->token = $auth_key;
                $user->parent_id = $user_id;
//                $user->taxi_park_id =
                if($user_id != NULL AND isset($user_id)){
                    $parent = Users::findOne($user_id);
                    $user->taxi_park_id = $parent->taxi_park_id;
                }

                $user->save();
                (new Query)
                    ->createCommand()
                    ->insert('token_change', ['user_id' => $user->id, 'comment' => 'actionVerifyCode'])
                    ->execute();
                if($user->role_id == 2){

                    $cars = (new \yii\db\Query())
                        ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
                        ->from('users_cars')
                        ->where(['user_id' => $user->id])
                        ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
                        ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
                        ->all();
                    $response['cars'] = $cars;
                }
                $response["state"] = 'success';
                $response["type"] = $user->role_id;
                $response["user"] = $user;
                $city = Cities::find()->where(['id' => $user->city_id])->one();
                $response["city"] = $city;

                $response["token"] = $auth_key;
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                $user = new Users();
                $user->phone = $phone;
                $user->role_id = 1;
                $user->token = $auth_key;
                $user->parent_id = $user_id;
                if($user_id != NULL AND isset($user_id)){
                    $parent = Users::findOne($user_id);
                    $user->taxi_park_id = $parent->taxi_park_id;
                }
                $user->created = strtotime("now");
                $user->save();
                (new Query)
                    ->createCommand()
                    ->insert('token_change', ['user_id' => $user->id, 'comment' => 'actionVerifyCode'])
                    ->execute();
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'success';
                $response["type"] = 0;
                $response["message"] = 'user not found';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'invalid code or number';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }




    }

    public function actionLogout()
    {
        $token = $_POST['token'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $user->push_id = null;
        $user->save();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionHowManyChats()
    {
        $token = $_POST['token'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->role_id != 2){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'not driver';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }



        if($user->taxi_park_id == 0){
            $model_sh = (new \yii\db\Query())
                ->select('users.name, orders.order_type, orders.created, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.price')
                ->from('orders')
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                //      ->where(['orders.taxi_park_id' => 0])
                ->andWhere(['orders.status' => 1])
                ->andWhere(['orders.deleted' => 0])
                ->andWhere(['users.city_id' => $user->city_id])
                ->all();

            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["show_chat"] = false;
            $response["amount_shared"] = count($model_sh);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        else{
            $model = (new \yii\db\Query())
                ->select('users.name, orders.order_type, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.created, orders.price')
                ->from('orders')
                ->where(['orders.taxi_park_id' => $user->taxi_park_id])
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                ->andWhere(['orders.status' => 1])
                ->andWhere('orders.order_type <> 4')
                ->andWhere(['orders.deleted' => 0])
                ->all();

            $model_sh = (new \yii\db\Query())
                ->select('users.name, orders.order_type, orders.created, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.price')
                ->from('orders')
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                //      ->where(['orders.taxi_park_id' => 0])
                ->andWhere(['orders.status' => 1])
                ->andWhere(['orders.deleted' => 0])
//                ->andWhere(['users.city_id' => $driver->city_id])
                ->all();

            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["show_chat"] = true;
            $response["amount_own"] = count($model);
            $response["amount_shared"] = count($model_sh);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }
    public function actionSignUp(){
        $auth_key = Yii::$app->security->generateRandomString();
        $phone = $_POST['phone'];
        $name = $_POST['name'];
        $city_id = $_POST['city_id'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        $findme   = 'Optional';
        $pos = strpos($name, $findme);
        if($pos == true){
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'Optional !!!';
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;
        }

        if($phone == null OR $name == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'name or phone is missing';
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;
        }
        $user = Users::find()->where(['phone' => $phone])->one();
        if($user == null){
            $user = new Users();
        }
        $user->role_id = 1;
        $user->name = $name;
        $user->phone = $phone;
        $user->created = strtotime("now");
        $user->last_edit = date("d/m/Y H:i:s", time());
        $user->is_active = 1;
        $user->city_id= $city_id;
        $user->latitude = $latitude;
        $user->longitude = $longitude;
        $user->token = $auth_key;

        if($user->save()){

            (new Query)
                ->createCommand()
                ->insert('token_change', ['user_id' => $user->id, 'comment' => 'sign-up'])
                ->execute();

            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["message"] = 'Authorized';
            $response["token"] = $auth_key;
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;

        }else{
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'Occured some errors, ask Tair';
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;
        }
    }

    // ORDER BEGIN
    public function getPrice($token, $long_a, $lat_a, $long_b, $lat_b, $type){

        $distance = $this->haversineGreatCircleDistance($lat_a, $long_a, $lat_b, $long_b);

        $user = Users::find()->where(['token' => $token])->one();
        $total = 0;
        if($user == null){
            $user = SystemUsers::findOne(['token' => $token]);
            if($user == null){
                Yii::$app->response->statusCode = 401;
                $response["state"] = 'fail';
                $response["message"] = 'Invalid token';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                goto cont_order;
            }

        }else{
            cont_order:
            $tp = $user->taxi_park_id;
            $arr = array();

            $all_services = Services::find()->where(['id' => $type])->all();


            foreach ($all_services as $k => $v){
                $obj = array();
                $obj['service'] = $v->value;
                $current_service = TaxiParkServices::find()->where(['service_id' => $v->id])->andWhere(['taxi_park_id' => $tp])->orderBy(['meters'=>SORT_ASC])->all();
                if($current_service != null){
                    if(count($current_service) > 1){
                        foreach ($current_service as $key => $value){
                            if($value->meters > $distance){
                                $obj['price'] = $value->tenge;
                                $total = $value->tenge;
                                break;
                            }
                        }
                        if(count($obj) < 1){
                            $last = end($current_service);
                            $meters = $distance - $last->meters;
                            $total = (($meters/1000) * $last->km_price) + $last->tenge;
                            $obj['price'] = $total;


                        }

                    }else{
                        $cur = reset($current_service);
                        $price = (($distance / 1000) * $cur->km_price) + $cur->call_price;
                        $obj['price'] = $price;
                        $total = $price;
                    }
                }else{
                    $obj['price'] = null;
                }
                array_push($arr, $obj);
            }


            return $total;


        }

    }

    public function actionGetPrice(){
        $token = $_POST['token'];
        $long_a = $_POST['longitude_a'];
        $lat_a = $_POST['latitude_a'];
        $long_b = $_POST['longitude_b'];
        $lat_b = $_POST['latitude_b'];
        $type = $_POST['type'];

        $distance = $this->haversineGreatCircleDistance($lat_a, $long_a, $lat_b, $long_b);



        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            $user = SystemUsers::find()->where(['token' => $token])->one();
            if($user == null){
                Yii::$app->response->statusCode = 401;
                $response["state"] = 'fail';
                $response["message"] = 'Invalid token';
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else{
                goto continue_order;
            }
        }else{
            continue_order:
            $tp = $user->taxi_park_id;
            $arr = array();
            if($type == 2){
                $all_services = Services::find()->where(['id' => 4])->all();
            }else if($type == 4){
                $all_services = Services::find()->where(['id' => 6])->all();
            }else if($type == 5){
                $all_services = Services::find()->where(['id' => 5])->all();
            }
            else{
//                if($user->company_id != null){
                    $all_services = Services::find()->where(['id' => ['1', '2', '3']])->all();
//                }else{
//                    $all_services = Services::find()->where(['id' => ['1', '2']])->all();
//                }

            }

            foreach ($all_services as $k => $v){
                $obj = array();
                if($v->id == 3){
                    if($user->company_id != null){
                        $obj['service_name'] = 'Корпоративный клиент';
                        $obj['available'] = true;
                    }else{
                        $obj['service_name'] = $v->value;
                        $obj['available'] = false;

                    }

                }else{
                    $obj['available'] = true;
                    $obj['service_name'] = $v->value;
                }
                $obj['service_id'] = $v->id;
                $obj['img'] = 'http://185.236.130.126/profile/uploads/icons/' . $v->icon;
                $obj['img1'] = 'http://185.236.130.126/profile/uploads/icons/' . $v->icon1;
                $current_service = TaxiParkServices::find()->where(['service_id' => $v->id])->andWhere(['taxi_park_id' => $tp])->orderBy(['meters'=>SORT_ASC])->all();
                Yii::$app->response->format = Response::FORMAT_JSON;

//
//                $res['p'] = $distance;
//                $res['s'] = $current_service;
//                return $res;

                if($current_service != null){
                    if(count($current_service) > 1){
                        foreach ($current_service as $key => $value){
                            if($value->meters > $distance){
                                $obj['price'] = $value->tenge;

                                break;
                            }
                        }
                        if(count($obj) < 1){
                            $last = end($current_service);
                            $meters = $distance - $last->meters;
                            $total = (($meters/1000) * $last->km_price) + $last->tenge;
                            $obj['price'] = round($total);
                        }

                    }else{
                        $cur = reset($current_service);
                        $price = (($distance / 1000) * $cur->km_price) + $cur->call_price;
                        $obj['price'] = round($price);
                    }
                }else{
                    $obj['price'] = 0;
                }
                array_push($arr, $obj);
            }

            Yii::$app->response->statusCode = 200;
            $response["price_list"] = $arr;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function sendSilentToDrivers($order, $drivers, $user, $comment){
        foreach ($drivers as $key => $value){

            $not = array("type" => 1, "order_id" => $order);

            if($value->platform == 1){
                $data = array('to' => $value->push_id,
                    'data' => $not, "content_available" => true);

            }else{
                $data = array('to' => $value->push_id,
                    'data' => $not);

            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type: application/json',
                'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
            ));
            curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $log = new Log();
            $log->response = $server_output;
            $log->comment = 'определение локации водителя to: ' . $value->name;
            $log->save();
        }

//        $this->sendPushToDriver($order, $user, 'Водители получили вашу заявку', 'Да');


    }

    public function sendPushToDriver($order, $user, $title, $text){

        $order_model = Orders::find()->where(['id' => $order])->one();
        $lat_a = $order_model->from_latitude;
        $long_a = $order_model->from_longitude;
        $lat_b = $order_model->to_latitude;
        $long_b = $order_model->to_longitude;
        $data = array("type" => 101, "order_id" => $order, "lat_a" => $lat_a, "lat_b" => $lat_b, "long_a" => $long_a, "long_b" => $long_b);

//        $not = array( "title" => $title, "body" => $text);
        if($user->platform == 1){
            $data1 = array('to' => $user->push_id,
//            'notification' => $not,
                "data" => $data, "content_available" => true);
        }else{
            $data1 = array('to' => $user->push_id,
//            'notification' => $not,
                "data" => $data);

        }




        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        $log = new Log();
        $log->response = $server_output;
        $log->comment = '101 code: to ' . $user->name;
        $log->save();
    }

    public function congratulateDriver($driver, $order){

        $data = array("type" => 301, "order_id" => $order->id);

        if($driver->platform == 1){
            $data1 = array('to' => $driver->push_id,
                "data" => $data,
                "content_available" => true
            );
        }else{
            $data1 = array('to' => $driver->push_id,
                "data" => $data
            );
        }



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $log = new Log();
        $log->response = $server_output;
        $log->comment = 'congrat to: ' . $driver->name;
        $log->save();
    }



    public function actionAutodelete(){
        $now = strtotime('now') - 3600;
        $orders = Orders::find()->where('created < ' . $now )->andWhere(['deleted' => 0])->all();
        foreach ($orders as $order){
            $order->deleted = 1;
            $order->save();
        }
    }




    public function actionChangeCity(){
        $token = $_POST['token'];
        $city_id = $_POST['city_id'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $user = Users::findOne(['token' => $token]);
        if($user != null){
            $user->latitude = $latitude;
            $user->longitude = $longitude;
            $user->city_id = $city_id;
            $user->save();
            Yii::$app->response->statusCode = 200;
            $response["state"] = "success";
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = "unauthorized";
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionMakeOrder(){

        $token = $_POST['token'];
        $to_long = $_POST['longitude_b'];
        $to_lat = $_POST['latitude_b'];
        $from_long = $_POST['longitude_a'];
        $from_lat = $_POST['latitude_a'];
        $type = $_POST['service_id'];
        $comment = $_POST['comment'];
        $date = $_POST['date'];
        $pay = $_POST['payment_type'];

        if($type == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = "fail";
            $response["state"] = "Type missing";
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($token != null AND $to_lat != null AND $to_long != null AND $from_lat != null AND $from_long != null){
            $user = Users::find()->where(['token' => $token])->one();
            $users_distance = $this->haversineGreatCircleDistance($user->latitude, $user->longitude, $from_lat, $from_long);
            if(!$_POST['dispatcher']){
                if(($users_distance/1000) > 100){
                    $response["state"] = 'city';
                    Yii::$app->response->statusCode = 200;
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
            }

            if($_POST['phone'] != null){
                $user = Users::findOne(['phone' => $_POST['phone']]);
                if(!$_POST['dispatcher']) {
                    $current_orders = Orders::find()->where(['user_id' => $user->id])->andWhere(['status' => [1,2,3,4]])->all();
                    if($current_orders != null){
                        Yii::$app->response->statusCode = 200;
                        $response["state"] = "fail";
                        $response["state"] = "у вас есть текущий заказ";
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;
                    }
                }

                if($user == null){
                    $disp = true;
                    $user = new Users();
                    $user->phone = $_POST['phone'];
                    $user->role_id = 1;
                    $user->city_id = Helpers::getMyTpCity();
                    $user->is_active = 1;
                    $user->balance = 0;
                    $user->taxi_park_id = Helpers::getMyTaxipark();
                    $user->save();
                }
            }

            if($user != null){
                if($user->is_active != 1){
                    Yii::$app->response->statusCode = 400;
                    $response["state"] = "fail";
                    $response["state"] = "Blocked user";
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                if($pay == 3){
                    // TODO: доделать оплату бонусами
                }
                $order = new Orders();
                $order->comment = $comment;
                if($disp){
                    $order->dispatcher_id = Yii::$app->session->get('profile_id');
                }

                if($type == 5){
                    $price = $this->GetTrezvyPrice($from_lat, $from_long, $to_lat, $to_long);
                    $order->comment = $_POST['kpp'] . " " . $_POST['comment'];
                }else{
                    $price = $this->getPrice($token, $from_long, $from_lat, $to_long, $to_lat, $type);
                }
                if($pay == 3){
                    // TODO: доделать оплату бонусами
                    if($user->balance < $price){
                        Yii::$app->response->statusCode = 200;
                        $response["state"] = "fail";
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;
                    }
                }
                if($type == 3){
                    $company = Company::findOne($user->company_id);
                    if($company->balance < $price){
                        Yii::$app->response->statusCode = 200;
                        $response["state"] = "fail";
                        $response["message"] = "не достатоно баланса";
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;

                    }
                }

                $order->user_id = $user->id;
                $order->from_latitude = $from_lat;
                $order->from_longitude = $from_long;
                $order->to_latitude = $to_lat;
                $order->to_longitude = $to_long;
                $order->order_type = $type;
                $order->price = $price;
                if($pay != null){
                    $order->payment_type = $pay;
                }else{
                    $order->payment_type = 1;
                }

                $order->status = 1;
                $order->created = strtotime('now');
                $order->taxi_park_id = $user->taxi_park_id;
                if($type == 3 AND $user->company_id != null){
                    $order->company_id = $user->company_id;
                }

                if($date != null){
                    $order->date = $date;
                }else{
                    $order->date = strtotime('now');
                }
                $today = new DateTime();


                if($order->save()){
                    // ask drivers
                    if($type == 4){
                        $all_drivers = Users::find()->where(['taxi_park_id' => $order->taxi_park_id])->andWhere(['role_id' => 2])->andWhere(['gender_id' => 1])->all();
                    }else{
                        $all_drivers = Users::find()->where(['taxi_park_id' => $order->taxi_park_id])->andWhere(['role_id' => 2])->all();
                    }
                    $this->sendSilentToDrivers($order->id, $all_drivers, $user, "Первоначальный пуш для определения дистанции");

                    Yii::$app->response->statusCode = 200;
                    $response["state"] = 'success';
                    $response["message"] = $order->id;
                    if($pay == 2){
                        $this->CreateOrder(1, $order->id);
                    }else{
                        ignore_user_abort(true);
                        set_time_limit(0);

                        ob_start();

                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        Yii::$app->response->send();
                        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
                        header($serverProtocol . ' 200 OK');
                        // Disable compression (in case content length is compressed).
                        header('Content-Encoding: none');
                        header('Content-Length: ' . ob_get_length());

                        // Close the connection.
                        header('Connection: close');

                        ob_end_flush();
                        ob_flush();
                        flush();

                    }
                    $settings = OrderSettings::findOne(1);

                    sleep($settings->seconds);

                    // check if there some drivers


                    $updated_order = Orders::find()->where(['id' => $order->id])->one();
                    if($updated_order->driver_id == null){
                        $updated_order->is_common = 1;
                        $updated_order->save();
                        // send push to all drivers
                        $today = new DateTime();
                        $time = $today->getTimestamp();
                        $active_drivers = Sessions::find()->where(['>', 'end', $time])->all();
                        $ids = [];
                        foreach ($active_drivers as $k => $v){
                            array_push($ids, $v->user_id);
                        }
                        $all_drivers = Users::find()->where(['role_id' => 2])->andWhere(['not', ['push_id' => null]])->andWhere(['id' => $ids])->andWhere(['service_id' => $type])->all();
                        $this->sendSilentToDrivers($updated_order->id, $all_drivers, $user, "вторичный пуш для определения дистанции");



                    }

                }else{
                    Yii::$app->response->statusCode = 401;

                    $response["state"] = 'fail';
                    $response["message"] = 'hzz';
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $response;
                }

            }else{

                Yii::$app->response->statusCode = 401;

                $response["state"] = 'fail';
                $response["message"] = 'Invalid token, user not found';
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $response;
            }

        }else{
            Yii::$app->response->statusCode = 401;

            $response["state"] = 'fail';
            $response["message"] = 'hzz2';
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;
        }


    }

    public function actionCheckLocation(){
        $my_long = $_POST['longitude'];
        $my_lat = $_POST['latitude'];
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];

        $order = Orders::find()->where(['id' => $order_id])->one();
        $user = Users::find()->where(['token' => $token])->one();
        $client = Users::find()->where(['id' => $order->user_id])->one();
        if($order->status == 1){

            $distance = $this->haversineGreatCircleDistance($my_lat, $my_long, $order->from_latitude, $order->from_longitude);
            if($order->is_common == 1){
                $this->sendPushToDriver($order_id, $user, 'Новый Заказ', 'На расстоянии ' . $distance . 'м. от Вас');
            }else{
                $settings = OrderSettings::findOne(1);

                if($distance < $settings->meters){
                    $this->sendPushToDriver($order_id, $user, 'Новый Заказ', 'На расстоянии ' . $distance . 'м. от Вас');
                }
            }

        }else{
            $not = array("type" => 601, "lat" => $my_lat, "long" => $my_long);

            if($client->platform == 1){
                $data = array('to' => $client->push_id, 'data' => $not, "content_available" => 1);

            }else{
                $data = array('to' => $client->push_id, 'data' => $not);
            }


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-type: application/json',
                'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
            ));
            curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                json_encode($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);


        }

        Yii::$app->response->statusCode = 200;

        $response["state"] = $order->id;
//        $response["dis"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function sendClient($driver, $order_id){

        $order = Orders::find()->where(['id' => $order_id])->one();
        $client = Users::find()->where(['id' =>$order->user_id])->one();
        $data = array("type" => 201, "driver_id" => $driver->id, "order_id" => $order_id);
        if($client->platform == 1){

            $data1 = array('to' => $client->push_id,
                "data" => $data, "content_available" => true);

        }else if($client->platform == 0){


            $data1 = array('to' => $client->push_id,
                "data" => $data);
        }




        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        $log = new Log();
        $log->response = $server_output;
        $log->comment = '201 : ' . $client->name;
        $log->save();
    }

    public function actionAcceptOrder(){

        $token = $_POST['token'];
        $order_id = $_POST['order_id'];

        $order = Orders::find()->where(['id' => $order_id])->andWhere(['driver_id' => null])->one();
        $driver = Users::find()->where(['token' => $token])->one();

        $tp = TaxiPark::find()->where(['id' => $driver->taxi_park_id])->one();
        $tps = TaxiParkServices::find()->where(['service_id' => $order->order_type])->andWhere(['taxi_park_id' => $tp->id])->one();
        $percent = $tps->commision_percent;

        $active_driver = Sessions::find()->where(['>', 'end', strtotime('now')])->andWhere(['user_id' => $driver->id])->one();

        if($driver != null){
                if($driver->role_id == '2'){
                    if($percent != null){
                        // send push
                        $model = new PossibleDrivers();
                        $model->driver_id = $driver->id;
                        $model->order_id = $order_id;
                        $model->save();
                        $tp = TaxiPark::findOne($driver->taxi_park_id);
                        if($tp->type < 15){
                            if($active_driver != null){
                                $this->acceptDriver($driver, $order);

                            }else{
                                if($driver->balance < 10){

                                    Yii::$app->response->statusCode = 200;
                                    $response["state"] = 'balance';
                                    $response["message"] = 'Пополните баланс';
                                    Yii::$app->response->format = Response::FORMAT_JSON;

                                    return $response;
                                }
                            }

                        }else{
                            if($tp->balance < 10){

                                Yii::$app->response->statusCode = 200;
                                $response["state"] = 'balance';
                                $response["message"] = 'Пополните баланс';
                                Yii::$app->response->format = Response::FORMAT_JSON;

                                return $response;
                            }
                        }

                        $this->acceptDriver($driver, $order);

                    }else{
                        $this->acceptDriver($driver, $order);

                    }



                }else{
                    Yii::$app->response->statusCode = 400;

                    $response["state"] = 'fail';
                    $response["message"] = 'You cannot accept that order';
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $response;
                }
            }else{
                Yii::$app->response->statusCode = 401;

                $response["state"] = 'fail';
                $response["message"] = 'Invalid token, user not found';
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $response;
            }
//        }else{
//            Yii::$app->response->statusCode = 400;
//
//            $response["state"] = 'fail';
//            $response["message"] = 'That order is not available';
//            Yii::$app->response->format = Response::FORMAT_JSON;
//
//            return $response;
//        }

    }

    function acceptDriver($driver, $order){
        $client = Users::find()->where(['id' => $order->user_id])->one();
        $order->driver_id = $driver->id;
        $order->status = 2;
        $order->save();
        $this->congratulateDriver($driver, $order);
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
//        $response["order"] = $order;
//        $response["driver"] = $driver;

        $cars = (new \yii\db\Query())
            ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
            ->from('users_cars')
            ->where(['user_id' => $driver->id])
            ->andWhere(['users_cars.type' => 1])
            ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
            ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
            ->one();

        $users_car = UsersCars::find()->where(['user_id' => $driver->id])->andWhere(['type' => 1])->one();
        $submodel = CarModels::find()->where(['id' => $users_car->car_id])->one();
        $model = CarModels::find()->where(['id' => $submodel->parent_id])->one();
        $car = $model->model . " " . $submodel->model;

        $response["car"] = $cars;
        Yii::$app->response->format = Response::FORMAT_JSON;
        ignore_user_abort(true);
        set_time_limit(0);

        ob_start();

        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $response;
        Yii::$app->response->send();
        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocol . ' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: ' . ob_get_length());

        // Close the connection.
        header('Connection: close');

        ob_end_flush();
        ob_flush();
        flush();
        $this->sendClient($driver, $order->id);
        $this->updateLocation($driver, $client, $order->id);
    }

    public function actionGetOrderInfo(){
        $id = $_POST['order_id'];
        $order = Orders::find()->where(['id' => $id])->one();
        $user = Users::find()->where(['id' => $order->user_id])->one();
        $dispatcher = SystemUsers::findOne($order->dispatcher_id);
        $driver = Users::find()->where(['id' => $order->driver_id])->one();

        Yii::$app->response->statusCode = 200;

        $response["state"] = 'success';
        $response["order"] = $order;
        $response["client"] = $user;
        $response["dispatcher"] = $dispatcher;

        $response["driver"] = $driver;
        $cars = (new \yii\db\Query())
            ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
            ->from('users_cars')
            ->where(['user_id' => $driver->id])
            ->andWhere(['users_cars.type' => 1])
            ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
            ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
            ->all();
        $users_car = UsersCars::find()->where(['user_id' => $driver->id])->andWhere(['type' => 1])->one();
        $submodel = CarModels::find()->where(['id' => $users_car->car_id])->one();
        $model = CarModels::find()->where(['id' => $submodel->parent_id])->one();
        $car = $model->model . " " . $submodel->model;
        $response["car"] = $cars;
        if($driver != null){
        $response["stars"] = $this->getDriversAvatar($driver->id);
        $avatar = UsersAvatars::find()->where(['user_id' => $driver->id])->one();
        $response["avatar"] = $avatar->path;
        $response["rating"] = $this->getDriverRating($driver->id);
    
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    public function actionGetDriverInfo(){
        $id = $_POST['driver_id'];
        $driver = Users::find()->where(['id' => $id])->one();
        Yii::$app->response->statusCode = 200;

        $response["state"] = 'success';
        $response["driver"] = $driver;
        $users_car = UsersCars::find()->where(['user_id' => $driver->id])->andWhere(['type' => 1])->one();
        $submodel = CarModels::find()->where(['id' => $users_car->car_id])->one();
        $model = CarModels::find()->where(['id' => $submodel->parent_id])->one();
        $car = $model->model . " " . $submodel->model;
        $response["car"] = $car;
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    public function actionDriverCame(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $order = Orders::find()->where(['id' => $order_id])->one();
        $client = Users::find()->where(['id' => $order->user_id])->one();
        $order->status = 3;
        $order->save();
        $data = array("type" => 401);

        if($client->platform == 1){
            $data1 = array('to' => $client->push_id,
                "data" => $data, "content_available" => true);
        }else{
            $data1 = array('to' => $client->push_id,
                "data" => $data);

        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGo(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $order = Orders::find()->where(['id' => $order_id])->one();
        $client = Users::find()->where(['id' => $order->user_id])->one();
        $driver = Users::find()->where(['token' => $token])->one();
        $order->status = 4;
//        if($order->payment_type == 2){
//            $payment = Payment::find()->where(['order_id' => $order_id])->one();
//            if($this->GetOrderStatus($payment->payment_id, $payment->session_id)){
//                $payment->status = 1;
//            }else{
//                $order->payment_type = 1;
//                $payment->status = 0;
//                $this->PaymentFailed($driver, $client);
//            }
//        }
//        $payment->save();
        $order->save();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function PaymentFailed($driver, $client){
        $data = array("type" => 0);
        if($client->platform == 1){
            $data1 = array('to' => $client->push_id,
                "data" => $data, "content_available" => true);
        }else{
            $data1 = array('to' => $client->push_id,
                "data" => $data);
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

        if($driver->platform == 1){
            $data1 = array('to' => $driver->push_id,
                "data" => $data, "content_available" => true);
        }else{
            $data1 = array('to' => $driver->push_id,
                "data" => $data);
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
    }

    function checkLoc($driver, $client,  $order_id){

        $not = array("type" => 1, "order_id" => $order_id);

        if($driver->platform == 1){
            $data = array('to' => $driver->push_id, 'data' => $not, "content_available" => 1);

        }else{
            $data = array('to' => $driver->push_id, 'data' => $not);

        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);

    }
    public function updateLocation($driver, $client,  $order_id){

        $status=TRUE;

        do {

            $this->checkLoc($driver, $client,  $order_id);
            // Call your function
            sleep(3);   //wait for 5 sec for next function call

            //you can set $status as FALSE if you want get out of this loop.
            $order = \api\models\Orders::find()->where(['id' => $order_id])->one();
            if($order->status == 5){
                $status = false;
            }

        } while($status==TRUE);
    }

    public function actionFinishOrder(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $driver = Users::find()->where(['token' => $token])->one();
        $order = Orders::find()->where(['id' => $order_id])->one();
        $client = Users::find()->where(['id' => $order->user_id])->one();
        $order->status = 5;
        $order->save();

        $data = array("type" => 501, "order_id" => $order_id);


        if($client->platform == 1){
            $data1 = array('to' => $client->push_id,
                "data" => $data, "content_available" => true);
        }else{
            $data1 = array('to' => $client->push_id,
                "data" => $data);
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: key=AIzaSyCzke3IVnyVWY3aFz9TcGZU2yVd4cctQvk'
        ));
        curl_setopt($ch, CURLOPT_URL,"https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            json_encode($data1));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
         $this->rasschet($client, $order, $driver);
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionRateDriver(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $value = $_POST['value'];
        $user = Users::find()->where(['token' => $token])->one();
        $order = Orders::find()->where(['user_id' => $user->id])->andWhere(['id' => $order_id])->andWhere(['is_rated' => 0])->one();
        if($order == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = $order_id;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

        $dr = new DriversRatings();
        $dr->driver_id = $order->driver_id;
        $dr->value = $value;
        $dr->save();

        $this->updateRating($order->driver_id);

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    // ORDER END
    public function actionSaveAddress(){
        $token = $_POST['token'];
        $address = $_POST['address'];
        $long = $_POST['longitude'];
        $lat = $_POST['latitude'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){
            $model = new SavedAddresses();
            $model->address = $address;
            $model->longitude = $long;
            $model->latitude = $lat;
            $model->user_id = $user->id;

            if($model->save()){
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'sussess';
                $response["message"] = 'Address saved';
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $response;
            }else{
                Yii::$app->response->statusCode = 400;
                $response["state"] = 'fail';
                $response["message"] = 'Some error happened';
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $response;
            }

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionGetOrders(){

        $token = $_POST['token'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $orders = Orders::find()->where(['user_id' => $user->id])->andWhere(['status' => 3])->all();
        if($orders != null){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["orders"] = $orders;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'There are no orders';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionGetActiveOrders(){

        $token = $_POST['token'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $orders = Orders::find()->where(['user_id' => $user->id])->andWhere(['status' => [1,2,3,4]])->all();
        if($orders != null){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["orders"] = $orders;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'There are no orders';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionGetAddresses(){

        $token = $_POST['token'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){
            $addresses = SavedAddresses::find()->where(['user_id' => $user->id])->all();
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["addresses"] = $addresses;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }




    }

    public function actionGetBonuses(){

        $token = $_POST['token'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["balance"] = $user->balance;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionGetProfileInfo(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Incorrect token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionSetPushId(){
        $token = $_POST['token'];
        $push_id = $_POST['push_id'];
        if($token == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'fail';
            $response["message"] = 'Requered fields are missing: token or push_id';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){
            if($push_id != null){
                $user->push_id = $push_id;
                $user->save();
            }
            if($_POST['platform'] != null){
                $user->platform = $_POST['platform'];
                $user->save();
            }

            $today = new DateTime();
            $time = $today->getTimestamp();
            $session = Sessions::find()->where(['user_id' => $user->id])->andWhere(['>', 'end', $time])->one();
            if($session != null){
                $type = 1;
            }
            else{
                $type = 0;
            }

            $token = $_POST['token'];
            $user = Users::find()->where(['token' => $token])->one();
            if($user->role_id == 1){
                $active_order = Orders::find()->where(['user_id' => $user->id])->andWhere(['status' => [1, 2, 3, 4]])->all();

            }else{
                $active_order = Orders::find()->where(['driver_id' => $user->id])->andWhere(['status' => [1, 2, 3, 4]])->all();
            }
            if(count($active_order ) < 1){
                $status = 0;
            }else{
                $status = 1;
            }

            if($user->role_id == 2){


                $response["stars"] = $this->getDriversAvatar($user->id);

                $response["rating"] = $this->getDriverRating($user->id);

            }
            $avatar = UsersAvatars::find()->where(['user_id' => $user->id])->one();
            $response["avatar"] = $avatar->path;

            Yii::$app->response->statusCode = 200;

            $response["state"] = 'success';
            $response["status"] = $status;
            $response["active_orders"] = $active_order;
            $response["is_active"] = $user->is_active;
            $response["is_session_opened"] = $type;
            $response["balance"] = $user->balance;
            if($user->city_id == null){
                $response["has_city"] = 0;
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Invalid token';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }


    }

    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function actionSetpassword()
    {
        if (Yii::$app->request->isAjax) {
            if (Helpers::CheckAuth("check", null)) {
                $check_password = SystemUsers::find()->where(['id' => Yii::$app->session->get('profile_id'), 'password' => md5($_POST['password'])])->one();
                if ($check_password != null) {
                    $check_password->password = md5($_POST['newpass']);
                    $check_password->created = strtotime('now');
                    if ($check_password->save()) {
                        $response['message'] = "Пароль успешно изменен, требуется перезайти.";
                        $response['type'] = "success";
                    } else {

                        $response['message'] = $check_password->errors;
                        $response['type'] = "error";
                    }
                } else {
                    $response['message'] = "Текущий пароль неверный.";
                    $response['type'] = "warning";
                }
            } else {
                $response['message'] = "Сессия устарела, перезайдите.";
                $response['type'] = "information";
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionGetSessionPrice(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user != null){
            $drivers_service = DriversServices::find()->where(['driver_id' => $user->id])->one();
            $service = TaxiParkServices::find()->where(['taxi_park_id' => $user->taxi_park_id])->andWhere(['service_id' => $drivers_service->service_id])->one();
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["six_hours_price"] = $service->session_price;
            $response["unlim_price"] = $service->session_price_unlim;

        }else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Invalid token';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionSetinformation()
    {
        if (Yii::$app->request->isAjax) {
            if (Helpers::CheckAuth("check", null)) {
                $profile = SystemUsers::find()->where(['id' => Yii::$app->session->get('profile_id')])->one();
                if ($profile != null) {
                    $profile->first_name = $_POST['Information']['first_name'];
                    $profile->last_name = $_POST['Information']['last_name'];

                    if ($profile->save()) {
                        $response['message'] = "Данные успешно сохранены.";
                        $response['type'] = "success";
                        $response['last_edit'] = "Последнее изменение: ".$profile->last_edit;

                    } else {

                        $response['message'] = "Неизвестная ошибка, попробуйте позже.";
                        $response['type'] = "error";
                    }
                }
            } else {
                $response['message'] = "Сессия устарела, перезайдите.";
                $response['type'] = "information";
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    public function actionSetStatus(){
        $id = $_POST['id'];
        $status = $_POST['status'];
        $user = Users::find()->where(['id' => $id])->one();
        if($user != null){
            $user->is_active = $status;
            $user->save();
        }

    }

    public function actionGetCarModels(){

        $user = CarModels::find()->where(['parent_id' => -1])->all();
        if($user != null){
            $response['models'] = $user;
            $response['type'] = "success";
        }
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetCarSubmodels(){

        $user = CarModels::find()->where(['parent_id' => $_GET['id']])->all();
        if($user != null){
            $response['models'] = $user;
            $response['type'] = "success";
        }
        Yii::$app->response->statusCode = 200;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionGetReferalLink(){

        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            $response['state'] = "unauthorized";
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $link1 = "http://taxiplus.cf:443?auth_key=".$user->id;
        $response['state'] = "success";
        $response['link'] = $link1;
        $response['link2'] = urlencode($link1);

        Yii::$app->response->statusCode = 200;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    // logirovanie
    public function actionStartSession(){

        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = $_POST['token'];
        $lim = $_POST['duration'];
        $user = Users::find()->where(['token' => $token])->one();
        $users_service = DriversServices::findOne(['driver_id' => $user->id]);
        if($user != null){
            $service = TaxiParkServices::find()->where(['taxi_park_id' => $user->taxi_park_id])->andWhere(['service_id' => $users_service->service_id])->one();
            if($lim == null){
                $price = $service->session_price;
            }else{
                $price = $service->session_price_unlim;
            }
            $session = new Sessions();
            $session->user_id = $user->id;
            $time = strtotime("now");
            $session->start = $time;
            if($lim  == null){
                $session->end = $time + (60 * 60 * 6);
            }else{
                $session->end = $time + (60 * 60 * 12);
            }
            if($user->balance >= $price ){
                if($session->save()){
                    $log = new MonetsTraffic();
                    $log->sender_user_id = $user->id;
                    $log->sender_tp_id = $user->taxi_park_id;
                    $log->reciever_user_id = 111;
                    $log->reciever_tp_id = 0;
                    $log->date = $time;
                    $log->amount = $price;
                    $log->type_id = 3;
                    $log->save();
                    $user->balance = $user->balance - $price;
                    $user->save();

                    Yii::$app->response->statusCode = 200;
                    $response["state"] = 'success';
                    $response["message"] = 'Сессия успешно открыта';
                    return $response;
                }
            }else{
                Yii::$app->response->statusCode = 400;
                $response["state"] = 'fail';
                return $response;
            }
        }
        else{
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            $response["message"] = 'Invalid token';
            return $response;
        }
    }

    public function actionGetMySession(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'fail';
            return $response;
        }
        $today = new DateTime();
        $time = $today->getTimestamp();
        $session = Sessions::find()->where(['user_id' => $user->id])->andWhere(['>', 'end', $time])->one();
        if($session != null){
            $type = 1;
        }
        else{
            $type = 0;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["is_active"] = $type;
        $response["balance"] = $user->balance;
        $response["active"] = $user->is_active;

        return $response;
    }


    public function actionCheckStatus(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user->is_active == 1){

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            return $response;
        }else{

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'not confirmed';
            return $response;
        }

    }



    public function actionCloseSession(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        (new Query)
            ->createCommand()
            ->delete('sessions', ['user_id' => $user->id])
            ->execute();
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        return $response;
    }




    public function actionGetOwnOrders(){
        $token = $_POST['token'];
        $driver = Users::find()->where(['token' => $token])->one();
        $rejected = RejectedOrders::find()->where(['user_id' => $driver->id])->all();
        $ids = [];
        foreach ($rejected as $k => $v){
            array_push($ids, $v->order_id);
        }
        Yii::$app->response->statusCode = 200;
        if($driver->gender_id == 1){
            $model = (new \yii\db\Query())
                ->select('users.name, orders.order_type, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.created, orders.price')
                ->from('orders')
                ->where(['orders.taxi_park_id' => $driver->taxi_park_id])
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                ->where(['orders.is_common' => null])
                ->andWhere(['orders.status' => 1])
                ->andWhere(['NOT IN', 'orders.id', $ids])
                ->andWhere(['orders.deleted' => 0])
                ->andWhere(['users.city_id' => $driver->city_id])
                ->all();
        }else{
            $model = (new \yii\db\Query())
                ->select('users.name, orders.order_type, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.created, orders.price')
                ->from('orders')
                ->where(['orders.taxi_park_id' => $driver->taxi_park_id])
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                ->where(['orders.is_common' => null])
                ->andWhere(['orders.status' => 1])
                ->andWhere(['NOT IN', 'orders.id', $ids])
                ->andWhere('orders.order_type <> 4')
                ->andWhere(['orders.deleted' => 0])
                ->all();

        }


        $response["state"] = 'success';
        $response["orders"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    public function actionCancelOrder(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $user = Users::find()->where(['token' => $token])->one();
        $order = Orders::find()->where(['id' => $order_id])->one();
        if($order == null){
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'order not found';
            return $response;
        }

        if($order->status == 1 OR $order->status == 2){
            $order->status = 0;
            $order->save();

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            return $response;
        }else{

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'Too late for cancelling order';
            return $response;
        }

    }
    public function actionGetSharedOrders(){
        $token = $_POST['token'];
        $driver = Users::find()->where(['token' => $token])->one();
        $rejected = RejectedOrders::find()->where(['user_id' => $driver->id])->all();
        $ids = [];
        foreach ($rejected as $k => $v){
            array_push($ids, $v->order_id);
        }
        if($driver->gender_id == 1){
            $model = (new \yii\db\Query())
                ->select('users.name, orders.order_type, orders.created, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.price')
                ->from('orders')
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                ->where(['orders.is_common' => 1])
                ->andWhere(['orders.status' => 1])
                ->andWhere(['orders.deleted' => 0])
                ->andWhere(['NOT IN', 'orders.id', $ids])
                ->andWhere(['users.city_id' => $driver->city_id])
                ->all();
        }else{
            $model = (new \yii\db\Query())
                ->select('users.name, orders.order_type, orders.created, (case when `orders`.`dispatcher_id` IS NOT NULL then `system_users`.`phone` else `users`.`phone` end) phone, orders.from_longitude, orders.to_longitude, orders.to_latitude, orders.from_latitude, orders.id, orders.price')
                ->from('orders')
                ->innerJoin('users', 'users.id = orders.user_id')
                ->leftJoin('system_users', 'system_users.id = orders.dispatcher_id')
                ->where(['orders.is_common' => 1])
                ->andWhere(['orders.status' => 1])
                ->andWhere('orders.order_type <> 4')
                ->andWhere(['orders.deleted' => 0])
                ->andWhere(['NOT IN', 'orders.id', $ids])
                ->andWhere(['users.city_id' => $driver->city_id])
                ->all();
        }

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["orders"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionAddComplaint(){
        $token = $_POST['token'];
        $text = $_POST['text'];
        $order_id = $_POST['order_id'];

        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $model = new Complaints();
        $model->text = $text;
        $model->user_id = $user->id;
        $model->order_id = $order_id;
        $model->save();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["orders"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionRejectOrder(){
        $token = $_POST['token'];
        $order_id = $_POST['order_id'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 400;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $model = new RejectedOrders();
        $model->order_id = $order_id;
        $model->user_id = $user->id;
        $model->save();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["orders"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetAllOrders(){
        $token = $_POST['token'];
        $date = $_POST['date'];
        $user = Users::find()->where(['token' => $token])->one();

        if($user == null){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($date != null){
            $beginOfDay = strtotime("midnight", $date );
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
            if($user->role_id == 1){
                $orders = Orders::find()->where(['BETWEEN', 'created', $beginOfDay, $endOfDay])->andWhere(['user_id' => $user->id])->andWhere(['status' => [5, 1]])->all();
            }else{
                $orders = Orders::find()->where(['BETWEEN', 'created', $beginOfDay, $endOfDay])->andWhere(['driver_id' => $user->id])->andWhere(['status' => [5, 1]])->all();
            }
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["orders"] = $orders;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }


    }


    public function actionGetBalanceHistory(){
        $token = $_POST['token'];
        $date = $_POST['date'];
        $user = Users::find()->where(['token' => $token])->one();
        if($user == null){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($date != null){
            $beginOfDay = strtotime("midnight", $date);
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
            $sum = 0;
            if($user->role_id == 2){
                $count = 0;
                $orders = Orders::find()->where(['BETWEEN', 'created', $beginOfDay, $endOfDay])->andWhere(['driver_id' => $user->id])->all();
                foreach ($orders as $k => $v){
                    $sum += $v->price;
                    $count += 1;
                }
                $response["amount"] = $count;

            }else{
                $traffic = MonetsTraffic::find()->where(['BETWEEN', 'date', $beginOfDay, $endOfDay])->andWhere(['reciever_user_id' => $user->id])->all();
                foreach ($traffic as $k => $v){
                    $sum += $v->amount;
                }

            }
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'success';
            $response["sum"] = $sum;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionGetUser(){
        $token = $_POST['token'];
        $user = Users::find()->where(['token' => $token])->one();
        $fac = DriversFacilities::find()->where(['driver_id' => $user->id])->all();
        $f = [];
        foreach ($fac as $k => $v){
            array_push($f, $v->facility_id);
        }
        $users_car = UsersCars::find()->where(['user_id' => $user->id])->andWhere(['type' => 1])->one();
        if($user->role_id == 2){

            $cars = (new \yii\db\Query())
                ->select('users_cars.id, users_cars.car_id, users_cars.seats_number, users_cars.tonns, users_cars.body, users_cars.number, users_cars.year, users_cars.type, mod.model as model, sub.model as submodel')
                ->from('users_cars')
                ->where(['user_id' => $user->id])
                ->innerJoin('car_models as mod', 'users_cars.car_id = mod.id')
                ->innerJoin('car_models as sub', 'sub.id = mod.parent_id')
                ->all();
        }
        Yii::$app->response->statusCode = 200;
        $tp = TaxiPark::find()->where(['id' => $user->taxi_park_id])->one();
        $city = Cities::find()->where(['id' => $user->city_id])->one();
        $response["taxi_park"] = $tp->name;
        $response["user"] = $user;
        $response["facilities"] = $f;
        $response['cars'] = $cars;
        $response['city'] = $city;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionUploadAvatar(){


        $currentDir = getcwd();
        $uploadDirectory = "/../web/uploads/";//avatars/";

        $fileExtensions = ['jpeg','jpg','png']; // Get all the file extensions

        $fileName = $_FILES['myfile']['name'];
        $fileSize = $_FILES['myfile']['size'];
        $fileTmpName  = $_FILES['myfile']['tmp_name'];
        $fileType = $_FILES['myfile']['type'];
        $fileExtension = strtolower(end(explode('.',$fileName)));

        $uploadPath = $currentDir . $uploadDirectory . basename($fileName);

        if (isset($_POST['token'])) {

            $user = Users::find()->where(['token' => $_POST['token']])->one();
            if($user == null) {
                Yii::$app->response->statusCode = 401;
                $response["state"] = 'unauthorized';

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }

            if (! in_array($fileExtension,$fileExtensions)) {
                $error = "This file extension is not allowed. Please upload a JPEG or PNG file";
                $response["state"] = $error;
                Yii::$app->response->statusCode = 400;
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }

            if (empty($errors)) {
                $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

                if ($didUpload) {
                    $response["state"] = 'success';
                    $response["path"] = 'http://194.87.146.89/profile/uploads/' . basename($fileName);
                    $users_avatars = UsersAvatars::find()->where(['user_id' => $user->id])->one();
                    if($users_avatars == null){
                        $users_avatars = new UsersAvatars();
                    }
                    $users_avatars->user_id = $user->id;
                    $users_avatars->path = basename($fileName);
                    $users_avatars->save();
                    Yii::$app->response->statusCode = 200;
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                } else {
                    $error = "An error occurred somewhere. Try again or contact the admin";
                    $response["state"] = $error;

                    Yii::$app->response->statusCode = 200;
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
            }
        }else{
            $error = "Missing file";
            $response["state"] = 'unauthorized';
//            $response["path"] = 'http://185.236.130.126/profile/uploads/avatars/' . basename($fileName);
            Yii::$app->response->statusCode = 401;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

    }

    public function actionAddRecomendation(){
        $token = $_POST['token'];
        $text = $_POST['text'];
        $rating = $_POST['rating'];

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionAddSpecificOrder(){
        $token = $_POST['token'];
        $type = $_POST['type'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->role_id == 1){
            if($type == 1){
                SpecificOrders::addIntercityOrder($token, $_POST['seats_number'], $_POST['start_id'], $_POST['end_id'], $_POST['price'], $_POST['date']);
            }else{
                SpecificOrders::addOtherOrder($token, $_POST['comment'], $_POST['start_string'], $_POST['end_string'], $_POST['price'], $_POST['date'], $type);
            }
        }else{
            $access = DriversAccess::find()->where(['driver_id' => $user->id])->andWhere(['>', 'until', strtotime('now')])->andWhere(['order_type_id' => $type])->andWhere(['publish' => 1])->one();
            if($access != null){
                $order = new SpecificOrders();
                $order->driver_id = $user->id;
                $order->order_type_id = $type;
                $order->comment = $_POST['comment'];
                $order->date = $_POST['date'];
                $order->price = $_POST['price'];
                if($type == 1){
                    $order->start_id = $_POST['start_id'];
                    $order->destination_id = $_POST['end_id'];
                }else{
                    $order->from_string = $_POST['start_string'];
                    $order->to_string = $_POST['end_string'];
                }
                $order->save();
            }else{
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'do not have access';
                $response['price'] = OrderTypes::find()->where(['id' => $type])->one();

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
        }
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetAccessPrice(){
        $list = OrderTypes::find()->all();
        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["types"] = $list;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }
    //TODO: Add monets traffic
    public function actionBuyAccess(){
        $token = $_POST['token'];
        $type = $_POST['type'];
        $publish = $_POST['publish'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $order_type = OrderTypes::find()->where(['id' => $type])->one();
        if($user->balance < $order_type->hour_price){
            Yii::$app->response->statusCode = 200;
            $response["state"] = 'Your balance not enought';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        $drivers_access = new DriversAccess();
        if($publish == 1){
            $drivers_access->publish = 1;
        }else{
            $drivers_access->publish = 0;
        }
        $drivers_access->driver_id = $user->id;
        $drivers_access->order_type_id = $type;
        $drivers_access->until = strtotime('now') + 3600;
        $drivers_access->save();

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetSpecificChats(){
        $token = $_POST['token'];
        $type = $_POST['type'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($type == 1){
            if($user->role_id == 1){
                $model = (new \yii\db\Query())
                    ->select('specific_orders.*, Cs.cname as start, Ce.cname as end, Cs.id as start_id, Ce.id as end_id')
                    ->from('specific_orders')
                    ->where(['not', ['specific_orders.driver_id' => null]])
                    ->andWhere(['start_id' => $user->city_id])
                    ->andWhere(['order_type_id' => $type])
                    ->innerJoin('cities as Cs', 'Cs.id = specific_orders.start_id')
                    ->innerJoin('cities as Ce', 'Ce.id = specific_orders.destination_id')
                    ->all();

            } else {
                $model = (new \yii\db\Query())
                    ->select('specific_orders.id, Cs.cname as start, Ce.cname as end, Cs.id as start_id, Ce.id as end_id')
                    ->from('specific_orders')
                    ->where(['driver_id' => null])
                    ->andWhere(['order_type_id' => $type])
                    ->andWhere(['start_id' => $user->city_id])
                    ->innerJoin('cities as Cs', 'Cs.id = specific_orders.start_id')
                    ->innerJoin('cities as Ce', 'Ce.id = specific_orders.destination_id')
                    ->groupBy('start, end, start_id, end_id')
                    ->having('specific_orders.id = min(specific_orders.id)')
                    ->all();
            }
        }else {
            if($user->role_id == 1){
                $model = (new \yii\db\Query())
                    ->select('specific_orders.*, users.*')
                    ->from('specific_orders')
                    ->where(['not', ['driver_id' => null]])
                    ->andWhere(["order_type_id" => $type])
                    ->innerJoin('users', 'users.id = specific_orders.driver_id')
                    ->all();
//                $model = )->innerJoin('users', 'users.id = specific_orders.driver_id')->all();
            }else{
                $access = DriversAccess::find()->where(['driver_id' => $user->id])->andWhere(['>', 'until', strtotime('now')])->andWhere(['order_type_id' => $type])->andWhere(['publish' => 0])->one();
                if($access != null){
                    $model = (new \yii\db\Query())
                        ->select('specific_orders.*, u.name, u.phone')
                        ->from('specific_orders')
                        ->where(['driver_id' => null])
                        ->andWhere(['order_type_id' => $type])
                        ->innerJoin('specific_orders_clients as clients', 'clients.order_id = specific_orders.id')
                        ->innerJoin('users as u', 'u.id = clients.client_id')
                        ->all();
                }else{
                    Yii::$app->response->statusCode = 200;
                    $response["state"] = 'do not have access';
                    $response['price'] = OrderTypes::find()->where(['id' => $type])->one();

                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
            }
        }

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["chats"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetMejdugorodniyChat(){
        $token = $_POST['token'];
        $start = $_POST['start_id'];
        $end = $_POST['end_id'];
        $user = Users::findOneUser($token);
        if($user == null){
            Yii::$app->response->statusCode = 401;
            $response["state"] = 'unauthorized';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
        if($user->role_id == 2){
            $access = DriversAccess::find()->where(['driver_id' => $user->id])->andWhere(['>', 'until', strtotime('now')])->andWhere(['order_type_id' => 1])->andWhere(['publish' => 0])->one();
            if($access != null){
                $model = (new \yii\db\Query())
                    ->select('specific_orders.*, cs.cname as start, ce.cname as end, u.name, u.phone, clients.seats_number')
                    ->from('specific_orders')
                    ->where(['driver_id' => null])
                    ->andWhere(['order_type_id' => 1])
                    ->andWhere(['start_id' => $start])
                    ->andWhere(['destination_id' => $end])
                    ->innerJoin('specific_orders_clients as clients', 'clients.order_id = specific_orders.id')
                    ->innerJoin('users as u', 'u.id = clients.client_id')
                    ->innerJoin('cities as cs', 'cs.id = specific_orders.start_id')
                    ->innerJoin('cities as ce', 'ce.id = specific_orders.destination_id')
                    ->all();
            }else{
                Yii::$app->response->statusCode = 200;
                $response["state"] = 'do not have access';
                $response['price'] = OrderTypes::find()->where(['id' => 1])->one();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
        }else{
            $model = (new \yii\db\Query())
                ->select('specific_orders.*, cs.cname as start, ce.cname as end, u.name, u.phone, users_cars.seats_number, cmod.model as model, csub.model as submodel')
                ->from('specific_orders')
                ->where(['not', ['specific_orders.driver_id' => null]])
                ->andWhere(['specific_orders.order_type_id' => 1])
                ->andWhere(['specific_orders.start_id' => $start])
                ->andWhere(['specific_orders.destination_id' => $end])
                ->innerJoin('users as u', 'u.id = specific_orders.driver_id')
                ->innerJoin('users_cars', 'users_cars.user_id = u.id')
                ->innerJoin('car_models as cmod', 'cmod.id = users_cars.car_id')
                ->innerJoin('car_models as csub', 'csub.id = cmod.parent_id')
                ->innerJoin('cities as cs', 'cs.id = specific_orders.start_id')
                ->innerJoin('cities as ce', 'ce.id = specific_orders.destination_id')
                ->all();
        }

        Yii::$app->response->statusCode = 200;
        $response["state"] = 'success';
        $response["orders"] = $model;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    function updateRating($driver_id){
        $all_ratings = DriversRatings::find()->where(['driver_id' => $driver_id])->all();
        $count = count($all_ratings);
        $sum = 0;
        foreach ($all_ratings as $k => $v){
            $sum += $v->value;
        }
        $average = $sum / $count;
        $driver = Users::find()->where(['id' => $driver_id])->  one();
        $driver->rating = $average;
        $driver->save();
    }


    public function actionGetTrezvyPrice(){
        $latitude_a = $_POST['latitude_a'];
        $longitude_a = $_POST['longitude_a'];
        $latitude_b = $_POST['latitude_b'];
        $longitude_b = $_POST['longitude_b'];
        $token = $_POST['token'];
        $distance = $this->haversineGreatCircleDistance($latitude_a, $longitude_a, $latitude_b, $longitude_b);
        Yii::$app->response->statusCode = 200;
        $my_main_taxipark = TaxiPark::findOne(['main' => 1]);
        $services = TaxiParkServices::find()->where(['service_id' => 5, 'taxi_park_id' => $my_main_taxipark->id])->orderBy(['meters'=>SORT_DESC])->all();
        $price = 0;
        if($services > 1){
            foreach ($services as $k => $v){
                if($distance < $v->meters){
                    $price = $v->tenge;
                }
            }
            if($price == 0){
                $price = $services[count($services) - 1]->tenge;
                $left_kms = ($distance - $services[count($services) - 1]->meters) / 1000;
                $price += $left_kms * $services[count($services) - 1]->km_price;
            }
        }else{

            $price = (($distance / 1000) * $services[0]->km_price) + $services[0]->call_price;
        }

        $response["state"] = 'success';
        $response["price"] = floor($price);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function GetTrezvyPrice($latitude_a, $longitude_a, $latitude_b, $longitude_b){

        $distance = $this->haversineGreatCircleDistance($latitude_a, $longitude_a, $latitude_b, $longitude_b);
        Yii::$app->response->statusCode = 200;
        $my_main_taxipark = TaxiPark::findOne(['main' => 1]);
        $services = TaxiParkServices::find()->where(['service_id' => 5, 'taxi_park_id' => $my_main_taxipark->id])->orderBy(['meters'=>SORT_DESC])->all();
        $price = 0;
        if($services > 1){
            foreach ($services as $k => $v){
                if($distance < $v->meters){
                    $price = $v->tenge;
                }
            }
            if($price == 0){
                $price = $services[count($services) - 1]->tenge;
                $left_kms = ($distance - $services[count($services) - 1]->meters) / 1000;
                $price += $left_kms * $services[count($services) - 1]->km_price;
            }
        }else{

            $price = (($distance / 1000) * $services[0]->km_price) + $services[0]->call_price;
        }
        return floor($price);
    }

    // log
    function rasschet($client, $order, $driver){
        // check if driver have open session
        $active_driver = Sessions::find()->where(['>', 'end', strtotime('now')])->andWhere(['user_id' => $driver->id])->one();
        $service = Services::find()->where(['id' => $order->order_type])->one();

        // korp klient
        if($order->order_type == 3){
            $company = Company::find()->where(['id' => $client->company_id])->one();
            $company->balance -= $order->price;
            $company->save();
            $traffic = new MonetsTraffic();
            $traffic->sender_company_id = $client->company_id;
//            $tp_of_company = TaxiPark::find()->where(['city_id'=>$company->city_id])->andWhere(['main' => 1])->one();
            // TODO: MAIN TAXI PARK.
            $traffic->thanks_to = $client->id;
            $traffic->reciever_tp_id = $driver->taxi_park_id;
            $traffic->reciever_user_id = $driver->id;
            $traffic->amount = $order->price;
            $traffic->date = strtotime("now");
            $traffic->type_id = 7;
            $traffic->save();
            $driver->balance += $order->price;
            $driver->save();


        }

        if($order->payment_type == 3){
            $client->balance -= $order->price;
            $client->save();
            $driver->balance += $order->price;
            $driver->save();
            $traffic = new MonetsTraffic();
            $traffic->sender_user_id = $client->id;
            $traffic->sender_tp_id = $client->taxi_park_id;
            $traffic->reciever_user_id = $driver->id;
            $traffic->reciever_tp_id = $driver->taxi_park_id;
            $traffic->amount = $order->price;
            $traffic->date = strtotime("now");
            $traffic->type_id = 5;
            $traffic->save();
        }
        // zakaz obwego chata
        if($order->is_common == 1){

            $tps = TaxiParkServices::find()->where(['taxi_park_id' => 0])->andWhere(['service_id' => $order->order_type])->one();
            $percent = $tps->commision_percent;
            // esli u drivera ne otkryta smena
            if(!isset($active_driver)){
                $traffic = new MonetsTraffic();
                $traffic->sender_user_id = $driver->id;
                $traffic->sender_tp_id = $driver->taxi_park_id;
                $traffic->reciever_user_id = 111;
                $traffic->reciever_tp_id = 0;
                $traffic->amount = $driver->balance - ($driver->balance - ($order->price * ($percent / 100)));
                $traffic->date = strtotime("now");
                $traffic->type_id = 2;
                $traffic->save();
                $driver->balance =  $driver->balance - ($order->price * ($percent / 100));
                $driver->save();
            }
            // bonus referalu so scheta drivera
            if($client->parent_id != null){
                $referal = Users::find()->where(['id' => $client->parent_id])->one();
                $bonus = Services::find()->where(['id' => $order->order_type])->one();
                $referal_bonus = ($bonus->referal_percent / 100) * $bonus->referal_price;
                $referal->balance += $referal_bonus;
                $client->balance += $bonus->referal_price - $referal_bonus;

                $traffic1 = new MonetsTraffic();
                $traffic1->sender_user_id = $driver->id;
                $traffic1->sender_tp_id = $driver->taxi_park_id;
                $traffic1->reciever_user_id = $client->id;
                $traffic1->reciever_tp_id = $client->taxi_park_id;
                $traffic1->amount = $bonus->referal_price - $referal_bonus;
                $traffic1->date = strtotime("now");
                $traffic1->type_id = 4;
                $traffic1->save();

                $traffic = new MonetsTraffic();
                $traffic->sender_user_id = $driver->id;
                $traffic->sender_tp_id = $driver->taxi_park_id;
                $traffic->reciever_user_id = $referal->id;
                $traffic->thanks_to = $client->id;
                $traffic->reciever_tp_id = $referal->taxi_park_id;
                $traffic->amount = $referal_bonus;
                $traffic->date = strtotime("now");
                $traffic->type_id = 4;
                $traffic->save();
                $driver->balance -= $bonus->referal_price;
                $driver->save();
                $referal->save();
            }
        }
        else{
            $tp = TaxiPark::find()->where(['id' => $driver->taxi_park_id])->one();
            // esli monety snimautsya so scheta voditelya
            if($tp->type < 15){
                $tps = TaxiParkServices::find()->where(['taxi_park_id' => $tp->id])->andWhere(['service_id' => $order->order_type])->one();
                $percent = $tps->commision_percent;
                // proverim smenu
                if(!isset($active_driver)){
                    $driver->balance = $driver->balance - ($order->price * ($percent / 100));
                    $driver->save();
                    $traffic = new MonetsTraffic();
                    $traffic->sender_user_id = $driver->id;
                    $traffic->sender_tp_id = 0;
                    $traffic->reciever_user_id = 111;
                    $traffic->reciever_tp_id = $driver->taxi_park_id;
                    $traffic->amount = $driver->balance - ($driver->balance - ($order->price * ($percent / 100)));
                    $traffic->date = strtotime("now");
                    $traffic->type_id = 2;
                    $traffic->save();
                }

                // referal
                if($client->parent_id != null){
                    $referal = Users::find()->where(['id' => $client->parent_id])->one();
                    $bonus = Services::find()->where(['id' => $order->order_type])->one();
                    $referal_bonus = ($bonus->referal_percent / 100) * $bonus->referal_price;
                    $referal->balance += $referal_bonus;
                    $client->balance += $bonus->referal_price - $referal_bonus;

                    $traffic1 = new MonetsTraffic();
                    $traffic1->sender_user_id = $driver->id;
                    $traffic1->sender_tp_id = $driver->taxi_park_id;
                    $traffic1->reciever_user_id = $client->id;
                    $traffic1->reciever_tp_id = $client->taxi_park_id;
                    $traffic1->amount = $bonus->referal_price - $referal_bonus;
                    $traffic1->date = strtotime("now");
                    $traffic1->type_id = 4;
                    $traffic1->save();

                    $traffic = new MonetsTraffic();
                    $traffic->sender_user_id = $driver->id;
                    $traffic->sender_tp_id = $driver->taxi_park_id;
                    $traffic->reciever_user_id = $referal->id;
                    $traffic->thanks_to = $client->id;

                    $traffic->reciever_tp_id = $referal->taxi_park_id;
                    $traffic->amount = $referal_bonus;
                    $traffic->date = strtotime("now");
                    $traffic->type_id = 4;
                    $traffic->save();
                    $driver->balance -= $bonus->referal_price;
                    $driver->save();
                    $referal->save();
                }
            }else {
                if($client->parent_id != null){
//                    $referal = Users::find()->where(['id' => $client->parent_id])->one();
                    $bonus = Services::find()->where(['id' => $order->order_type])->one();
                    $client->balance += $bonus->referal_price;

                    $traffic = new MonetsTraffic();
                    $traffic->sender_user_id = $driver->id;
                    $traffic->sender_tp_id = $driver->taxi_park_id;
                    $traffic->reciever_user_id = $client->id;
                    $traffic->reciever_tp_id = $client->taxi_park_id;
                    $traffic->amount = $bonus->referal_price;
                    $traffic->date = strtotime("now");
                    $traffic->type_id = 4;
                    $traffic->save();
                    $driver->balance -= $bonus->referal_price;
                    $driver->save();
                    $client->save();
                }
            }

        }
    }


    public function getDriversAvatar($driver_id){
        $driver = Users::findOne(['id' => $driver_id]);
        $query = "select * from orders where last_edit BETWEEN concat(date(now() - INTERVAL 1 day), '9:00:00') and concat(date(now() - INTERVAL 1 day), ' 21:00:00') and driver_id = " . $driver_id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($query);
        $orders = $command->queryAll();

        /*
        Водители имеет три уровня классности
        а) Любитель – до 14 заказов в течение 12 часов, 7 заказов в 6 часов;
        б) Мастер – до 22 заказов  в течение 12 ч., 11 заказов в 6 часов;
        B) Профи – свыше 22 заказов в 12 и свыше 11 заказов в 6 ч.
        И рейтинги водителей - где большое количество рейтинга например 4-х звездочки, то это будет светится.
        */

        $stars = 0;
        if(count($orders) < 15){
            $stars = 1;
        }elseif (count($orders) < 22){
            $stars = 2;
        }else{
            $stars = 3;
        }
        return $stars;
    }

    public function getDriverRating($driver_id){
        $query = "select * from drivers_ratings where created BETWEEN concat(date(now() - INTERVAL 1 day), '9:00:00') and concat(date(now() - INTERVAL 1 day), ' 21:00:00') and driver_id = " . $driver_id;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($query);
        $rates = $command->queryAll();
        $total = 0;
        foreach ($rates as $k => $v){
            $total += $v->value;
        }
        return $total / count($rates) + 100 - 100;
    }

}
