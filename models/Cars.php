<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cars".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $cid
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 * @property string $name
 * @property string $token
 * @property string $aid
 *
 * // * @property Users[] $ids
 * // * @property Tasks[] $tasks
 */
class Cars extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'cid', 'status', 'updated_at', 'created_at'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['token', 'aid'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'cid' => 'Cid',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'token' => 'Token',
            'aid' => 'Aid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIds()
    {
        return $this->hasMany(Users::className(), ['client_id' => 'id'])->viaTable('clients', ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['car_id' => 'id']);
    }

    public static function findByToken($token)
    {
        return self::findOne(['token' => $token]);
    }
}
