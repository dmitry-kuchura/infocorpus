<?php

namespace app\models;

use Yii;

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
class TasksHistory extends \yii\db\ActiveRecord
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
            [['user_id', 'task_id', 'longitude', 'latitude', 'status', 'updated_at'], 'required'],
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
}
