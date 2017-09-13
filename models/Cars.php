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
 * @property integer $available
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
            [['client_id', 'cid', 'status', 'available', 'updated_at', 'created_at'], 'integer'],
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
            'available' => 'Available',
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

    /**
     * Созданеи группы ОБР
     *
     * @param $data
     * @return bool
     */
    public static function createCar($data)
    {
        $model = new Cars();

        $model->name = $data['name'];
        $model->status = 0;
        $model->aid = Yii::$app->security->generateRandomString();
        $model->token = Yii::$app->security->generateRandomString();
        $model->created_at = time();
        $model->updated_at = time();

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    public static function updateCar($token, $data)
    {
        $model = Cars::findOne(['token' => $token]);

        $model->longitude = $data['longitude'];
        $model->latitude = $data['latitude'];
        $model->updated_at = time();

        if ($model->validate() && $model->save()) {
            return true;
        } else {
            return false;
        }
    }

    public static function updateCarStatus($token, $data)
    {
        $model = Cars::findOne(['token' => $token]);

        $model->status = $data['status'];
        $model->updated_at = time();

        if ($model->validate() && $model->save()) {
            return true;
        } else {
            return false;
        }
    }
}
