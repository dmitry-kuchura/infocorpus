<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property integer $car_id
 * @property integer $user_id
 * @property integer $status
 * @property string $longitude
 * @property string $latitude
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Cars $car
 * @property Users $user
 */
class Tasks extends ActiveRecord
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
            [['car_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['user_id'], 'required'],
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

    public static function getActiveTask($user)
    {
        $result = self::find()->where(['user_id' => $user])->andWhere(['status' => 1])->orderBy('id DESC')->one();

        if (count($result)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTaskForCar($car)
    {
        if ($car->status == 2) {
            /* @var $result Tasks */
            $result = Tasks::find()->where(['car_id' => $car])->orderBy('id DESC')->one();

            $data = [
                'uid' => $result->user->id,
                'name' => $result->user->username,
                'phone' => $result->user->phone,
                'big_photo' => $result->user->image ? Url::to('@web/images/big/' . $result->user->image, true) : Url::to('@web/img/no-photo.png', true),
                'small_photo' => $result->user->image ? Url::to('@web/images/small/' . $result->user->image, true) : Url::to('@web/img/no-photo.png', true),
                'longitude' => $result->longitude,
                'latitude' => $result->latitude,
            ];

            return $data;
        } else {
            return null;
        }
    }

    public static function checkAlert($id)
    {
        /* @var $result Tasks */
        $result = self::find()->where(['user_id' => $id])->orderBy('updated_at DESC')->one();

        if ($result->status == 0) {
            return true;
        } else {
            return false;
        }
    }
}
