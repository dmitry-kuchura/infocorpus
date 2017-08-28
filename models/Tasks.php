<?php

namespace app\models;

use Yii;
use app\models\Users;
use app\models\Cars;

/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property integer $car_id
 * @property integer $user_id
 * @property integer $status
 * @property string $longitude
 * @property string $latitude
 *
 * @property Cars $car
 * @property Users $user
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'user_id', 'status'], 'integer'],
            [['user_id', 'longitude', 'latitude'], 'required'],
            [['longitude', 'latitude'], 'number'],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cars::className(), 'targetAttribute' => ['car_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_id' => 'Car ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Cars::className(), ['id' => 'car_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @param $user
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getLastTask($user)
    {
        return self::find()->where(['user_id' => $user])->orderBy('id DESC')->one();
    }
}
