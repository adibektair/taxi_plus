<?php
namespace backend\controllers;
use backend\components\Helpers;
use backend\models\TaxiPark;
use Yii;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;
use backend\models\Services;
use backend\models\TaxiParkServices;

class ServicesController extends Controller
{
    public function actionIndex()
    {
        if(Yii::$app->request->isAjax){
            $tp = TaxiPark::findOne(['id' => Helpers::getMyTaxipark()]);
            if($tp->main == 0){
                $res = Services::find()->where('id in (1,2)')->all();
            }else{
                $res = Services::find()->all();
            }

            $rand = rand(12, 10000);
            $response['data'] = $res;
            $response['rand'] = $rand;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }


}

?>