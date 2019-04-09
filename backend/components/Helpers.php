<?php
    namespace backend\components;
    use backend\models\Roles;
use backend\models\SystemUsers;
use backend\models\SystemUsersCities;
use backend\models\TaxiPark;
use backend\models\Users;
use Yii;

    class Helpers {
        public static function GetTaxiParkName(){
            $taxi_park = TaxiPark::findOne(['id' => self::getMyTaxipark()]);
            return $taxi_park->name;
        }
        public static function GetMyRoleWord(){
            $role = Yii::$app->session->get('profile_role');
            $model = Roles::findOne(['id' => $role]);
            return $model->name;
        }
        public static function GeneratePassword() {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $password = substr( str_shuffle( $chars ), 0, 8 );
            return $password;
        }
        public static function getMyCompany(){
            $me = SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
            return $me->company_id;
        }
        public static function getMyTpCity(){
            $me = SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
            $tp = TaxiPark::findOne(['id' => $me->taxi_park_id]);
            return $tp->city_id;
        }
        public static function getCitiesCondition(){
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
        public static function getMyTaxipark(){
            $me = SystemUsers::findOne(['id' => Yii::$app->session->get('profile_id')]);
            return $me->taxi_park_id;
        }
        public static function getCitiesString(){
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
        public static function getMyRole(){
            return Yii::$app->session->get('profile_role');
        }
        public static function CheckAuth($type, $link) {
            if (Yii::$app->session->get('profile_auth') == "OK" AND Yii::$app->session->get('profile_ip') == $_SERVER['REMOTE_ADDR']) {
                $auth = true;
            } else {
                $auth = false;
            }
            if ($type == "redirect") {
                if ($auth == true) {
                    return Yii::$app->response->redirect($link);
                }
            } else if ($type == "no-redirect") {
                if ($auth == false) {
                    return Yii::$app->response->redirect("/profile/authentication/");
                }
            } else if ($type == "check") {
                return $auth;
            }
        }

        public static function SetBack($page) {
            $backs = Yii::$app->session->get('navigation_back');
            $backs[] = $page;
            return $backs;
        }

        public static function GetConfig($table, $type) {
            $array = null;
//            print_r($table);
//            echo  $table . '     ';
//            die();


            switch ($table) {
                case "orders/orders-list":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['users.phone'],
                        'filtr' => array (
                            'orders.created' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "stats-referals/stat":
                    $array = array (
                        'select_fields' => ['id', 'name'],
                        'search_fields' => ['users.name'],
                        'filtr' => array (
                            'monets_traffic.date' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "drivers":
                    $array = array (
                        'search_fields' => ['users.name'],
                    );
                    break;
                case "requests":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['users.name'],
                        'filtr' => array (
                            'money_requests.created' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "dispatchers_orders":
                    $array = array (
                        'filtr' => array (
                            'orders.created' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "admins":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['system_users.first_name', 'system_users.last_name'],
                    );
                    break;
                case "admins/moderators":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['system_users.first_name', 'system_users.last_name'],
                        'filtr' => array (
                            'created' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            ),
                            
                        )
                    );
                    break;
                case "tadmins":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['system_users.first_name', 'system_users.last_name'],
                    );
                    break;
                case "companies":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['company.name'],
                    );
                    break;
                case "stats-referals":
                    $array = array (
                        'select_fields' => ['id', 'first_name', 'last_name'],
                        'search_fields' => ['parent.name'],
                        'filtr' => array (
                            'mt.date' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "stats-drivers":
                    $array = array (
                        'select_fields' => ['id', 'name', 'phone'],
                        'search_fields' => ['users.name', 'users.phone'],
                    );
                    break;

                case "stats-drivers/driver-stat":
                    $array = array (
                        'select_fields' => ['id', 'name', 'phone'],
                        'search_fields' => ['users.name', 'users.phone'],
                        'filtr' => array (
                            'monets_traffic.date' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "stats-clients":
                    $array = array (
                        'select_fields' => ['id', 'name', 'phone'],
                        'search_fields' => ['users.name', 'users.phone'],
                    );
                    break;
                case "stats-clients/client-stat":
                    $array = array (
                        'select_fields' => ['id', 'name', 'phone'],
                        'search_fields' => ['users.name', 'users.phone'],
                        'filtr' => array (
                            'o.created' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;
                case "stats-tp":
                    $array = array (
                        'select_fields' => ['id', 'name', 'phone'],
                        'search_fields' => ['taxi_park.name', 'taxi_park.company_name'],
                    );
                    break;
                case "taxi-parks":
                    $array = array (
                        'select_fields' => ['taxi_park.id', 'taxi_park.name'],
                        'search_fields' => ['taxi_park.name', 'taxi_park.company_name'],
                    );
                    break;
                case "messages":
                    $array = array (
                        'select_fields' => ['messages.title', 'messages.text'],
                        'search_fields' => ['messages.title', 'messages.text'],
                    );
                    break;
                case "sent_messages":
                    $array = array (
                        'select_fields' => ['messages.title', 'messages.text'],
                        'search_fields' => ['messages.title', 'messages.text'],
                    );
                    break;
//
                case "regions":
                    $array = array (
                        'select_fields' => ['regions.name'],
                        'search_fields' => ['regions.name'],
                    );
                    break;
                case "cars":
                    $array = array (
                        'select_fields' => ['car_models.model'],
                        'search_fields' => ['car_models.model'],
                    );
                    break;
                case "cars/submodels":
                    $array = array (
                        'select_fields' => ['car_models.model'],
                        'search_fields' => ['car_models.model'],
                    );
                    break;
//
                case "tp-stat":
                    $array = array (
                        'select_fields' => ['id', 'name', 'created'],
                        'filtr' => array (
                            'date' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;

                case "stats-tp/tp-stat":
                    $array = array (
                        'select_fields' => ['id', 'name', 'created'],
                        'filtr' => array (
                            'outcome.date' => array (
                                'label' => 'Дата',
                                'type' => 'date',
                                'icon' => 'icon-calendar'
                            )
                        ),
                    );
                    break;

                default:
                    $array = null;
                    break;
            }
            return $array[$type];
        }

        public static function GetRangeAccess($roles) {
            return true;
        }

        public static function GetPageAccess($page) {
 
            return true;
        }
        public static function GetTaxiParks(){
            $tp = TaxiPark::find()->all();
            $array = array();
            foreach ($tp as $value) {
                $array[$value->id] = $value->name;
            }
             return $array;
        }

        public static function GetUser(){
            $tp = Users::find()->all();
            $array = array();
            foreach ($tp as $value) {
                $array[$value->id] = $value->name;
            }
            return $array;
        }

        public static function GetTransliterate($s) {
            $s = (string) $s;
            $s = strip_tags($s);
            $s = str_replace(array("\n", "\r"), " ", $s);
            $s = preg_replace("/\s+/", ' ', $s);
            $s = trim($s);
            $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
            $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
            $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
            $s = str_replace(" ", "-", $s);
            return $s;
        }
    }
?>
