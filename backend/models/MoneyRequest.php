<?php

namespace backend\models;

use yii\db\ActiveRecord;


class MoneyRequest extends ActiveRecord
{

    public static function tableName() {
        return "money_requests";
    }

    public function rules()
    {
        return [
            [['id', 'user_id', 'amount', 'created', 'deleted'], 'safe'],
        ];
    }
}
