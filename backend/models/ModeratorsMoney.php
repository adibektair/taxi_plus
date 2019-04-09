<?php

namespace backend\models;

use yii\db\ActiveRecord;


class ModeratorsMoney extends ActiveRecord
{

    public static function tableName() {
        return "moderators_money";
    }

    public function rules()
    {
        return [
            [['id', 'system_user_id', 'moderator_id', 'amount', 'created'], 'safe'],
        ];
    }
}
