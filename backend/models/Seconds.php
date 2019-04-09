<?php

namespace backend\models;

use yii\db\ActiveRecord;


class Seconds extends ActiveRecord
{

    public static function tableName() {
        return "seconds";
    }

    public function rules()
    {
        return [
            [['id', 'seconds'], 'safe'],
        ];
    }
}
