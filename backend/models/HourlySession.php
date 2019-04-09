<?php

namespace backend\models;

use yii\db\ActiveRecord;


class HourlySession extends ActiveRecord
{

    public static function tableName() {
        return "hourly_sessions";
    }

}
