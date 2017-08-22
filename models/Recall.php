<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recall".
 *
 * @property integer $id
 * @property integer $date
 * @property integer $time
 * @property integer $automatic_redial
 * @property integer $recall_after
 * @property integer $recall_during
 * @property integer $call_security_after
 * @property integer $call_request
 * @property integer $user_id
 */
class Recall extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recall';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'time', 'automatic_redial', 'recall_after', 'recall_during', 'call_security_after', 'call_request'], 'required'],
            [['date', 'time', 'automatic_redial', 'recall_after', 'recall_during', 'call_security_after', 'call_request', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'time' => 'Time',
            'automatic_redial' => 'Automatic Redial',
            'recall_after' => 'Recall After',
            'recall_during' => 'Recall During',
            'call_security_after' => 'Call Security After',
            'call_request' => 'Call Request',
        ];
    }
}