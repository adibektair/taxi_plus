<?php

namespace backend\models;

use yii\db\ActiveRecord;


class OrderSettings extends ActiveRecord
{

    public static function tableName() {
        return "order_settings";
    }

    public function rules()
    {
        return [
            [['id', 'meters', 'seconds'], 'safe'],
        ];
    }
}
