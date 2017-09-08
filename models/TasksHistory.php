<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tasks_history".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $task_id
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 * @property integer $updated_at
 */
class TasksHistory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'task_id', 'status', 'updated_at'], 'required'],
            [['user_id', 'task_id', 'status', 'updated_at'], 'integer'],
            [['longitude', 'latitude'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'task_id' => 'Task ID',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Полная история тревоги
     *
     * @param $id
     * @return array|null
     */
    public static function getFullHistory($id)
    {
        $history = [];

        $result = new Query();

        $result = $result->select([
            'tasks_history.longitude',
            'tasks_history.latitude',
            'tasks.user_id',
            'tasks.car_id',
            'tasks_history.updated_at',
        ])
            ->from('tasks')
            ->join('LEFT JOIN', 'tasks_history', 'tasks_history.task_id = tasks.id')
            ->where(['tasks.id' => $id])
            ->groupBy('tasks_history.longitude')
            ->orderBy('tasks_history.updated_at')
            ->all();

        foreach ($result as $obj) {
            $history['customer'][] = [
                'longitude' => $obj['longitude'],
                'latitude' => $obj['latitude'],
                'user_id' => $obj['user_id'],
                'car_id' => $obj['car_id'],
                'updated_at' => $obj['updated_at'],
            ];
        }

        /* @var $group CarHistory */
        $group = CarHistory::getCarHistory($id);

        foreach ($group as $obj) {
            $history['group'][] = [
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'car_id' => $obj->car_id,
                'updated_at' => $obj->updated_at,
            ];
        }

        return $history ? $history : null;
    }

    /**
     * Получение последней тревоги
     *
     * @return array|null
     */
    public static function getTasksList()
    {
        /* @var $model Tasks */
        $model = Tasks::find()->all();

        $array = [];

        foreach ($model as $obj) {
            $array[] = [
                'id' => $obj->id,
                'user' => $obj->user->username,
                'photo' => $obj->user->image ? Url::to('/images/small/' . $obj->image) : Url::to('/img/no-photo.png'),
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'location' => Map::getAddressAPI($obj->latitude, $obj->longitude),
                'status' => $obj->status,
                'created_at' => date('Y-m-d H:i:s', $obj->created_at),
                'updated_at' => date('Y-m-d H:i:s', $obj->updated_at),
            ];
        }

        return $array ? $array : null;
    }
}
