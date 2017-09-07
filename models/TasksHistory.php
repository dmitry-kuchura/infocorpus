<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

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
            $history[] = [
                'longitude' => $obj['longitude'],
                'latitude' => $obj['latitude'],
                'user_id' => $obj['user_id'],
                'car_id' => $obj['car_id'],
                'updated_at' => $obj['updated_at'],
            ];
        }

        return $history ? $history : null;
    }
}
