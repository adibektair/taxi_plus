<?php

namespace backend\models;

use yii\db\ActiveRecord;


class DriverAvatars extends ActiveRecord
{

    public static function tableName() {
        return "driver_avatars";
    }

    public function rules()
    {
        return [
            [['id', 'orders', 'stars', 'created'], 'safe'],
        ];
    }
}
