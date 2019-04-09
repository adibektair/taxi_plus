<?php

namespace backend\models;

use yii\db\ActiveRecord;


class HourlySession extends ActiveRecord
{
    public $id;
    public $user_id;
    public $start_point;
    public $finished;

    public static function tableName() {
        return "hourly_sessions";
    }

    public function rules()
    {
        return [
            [['user_id', 'start_point'], 'required'],
        ];
    }

}
