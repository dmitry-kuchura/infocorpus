<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "car_history".
 *
 * @property integer $id
 * @property integer $car_id
 * @property integer $task_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 *
 * @property Cars $car
 * @property Tasks $task
 */
class CarHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'car_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'updated_at', 'longitude', 'latitude'], 'required'],
            [['car_id', 'task_id', 'created_at', 'updated_at', 'status'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cars::className(), 'targetAttribute' => ['car_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            'task_id' => 'Task ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
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
    public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }

    /**
     * Обновление координат машины
     *
     * @param $post array
     * @param $car integer
     * @return bool
     */
    public static function saveCarHistory($post, $car)
    {
        /* @var $task Tasks */
        $task = Tasks::find()->where(['car_id' => $car])->orderBy('id DESC')->one();

        $model = new CarHistory();

        $model->car_id = $car;
        $model->task_id = $task ? $task->id : null;
        $model->latitude = $post['latitude'];
        $model->longitude = $post['longitude'];
        $model->updated_at = time();
        $model->created_at = time();

        if ($model->validate() && $model->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Полная история машины
     *
     * @param $task
     * @return array|ActiveRecord[]
     */
    public static function getCarHistory($task)
    {
        return CarHistory::find()->where(['task_id' => $task])->all();
    }
}
