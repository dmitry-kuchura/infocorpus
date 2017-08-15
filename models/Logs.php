<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $ip
 * @property string $request
 * @property string $response
 * @property string $action
 * @property string $agent
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'ip', 'request', 'action'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['response', 'agent'], 'string'],
            [['ip'], 'string', 'max' => 39],
            [['request', 'action'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'ip' => 'Ip',
            'request' => 'Request',
            'response' => 'Response',
            'action' => 'Action',
        ];
    }
}
