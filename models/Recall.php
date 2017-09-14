<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

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
 * @property integer $task_id
 * @property string $longitude
 * @property string $latitude
 * @property integer $status
 *
 * @property Users $user
 * @property Tasks $task
 */
class Recall extends ActiveRecord
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
            [['date', 'time', 'automatic_redial', 'recall_after', 'recall_during', 'call_security_after', 'call_request', 'user_id', 'status'], 'required'],
            [['date', 'time', 'automatic_redial', 'recall_after', 'recall_during', 'call_security_after', 'call_request', 'user_id', 'task_id', 'status'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'date' => 'Date',
            'time' => 'Time',
            'automatic_redial' => 'Automatic Redial',
            'recall_after' => 'Recall After',
            'recall_during' => 'Recall During',
            'call_security_after' => 'Call Security After',
            'call_request' => 'Call Request',
            'user_id' => 'User ID',
            'task_id' => 'Task ID',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }

    /**
     * Поиск ближайших перезвонов
     *
     * @return array|ActiveRecord[]
     */
    public static function findCalls()
    {
        $time = time() * 1000 + 270000;
        return Recall::find()->where(['<=', 'date', $time])->andWhere(['status' => 0])->groupBy('user_id')->all();
    }

    /**
     * Создание тревози с перезвона
     *
     * @param $post
     * @return bool
     */
    public static function createAlert($post)
    {
        $time = time();

        $task = new Tasks();
        $task->created_at = $time;
        $task->updated_at = $time;
        $task->status = 1;
        $task->user_id = $post->user_id;
        $task->longitude = $post->longitude;
        $task->latitude = $post->latitude;
        $task->save();

        $history = new TasksHistory();
        $history->updated_at = $time;
        $history->task_id = $task->id;
        $history->user_id = $post->user_id;
        $history->longitude = $post->longitude;
        $history->latitude = $post->latitude;
        $history->status = 1;

        $recall = Recall::findOne($post->id);
        $recall->task_id = $task->id;

        $recall->save();


        if ($history->save()) {
            return $task->id;
        } else {
            return false;
        }
    }

    /**
     * Проверка на существование тревогис перезвона
     *
     * @param $post
     * @return int
     */
    public static function checkRecallAlert($post)
    {
        $recall = Recall::findOne($post['id']);

        if ($recall && $recall->task_id != null) {
            return $recall->task_id;
        } else {
            return false;
        }
    }

    /**
     * Обновление координат тревоги
     *
     * @param $post
     * @param $task
     * @return bool|static
     */
    public static function updateAlert($post, $task)
    {
        $time = time();

        $task = Tasks::findOne($task);

        $task->latitude = $post['latitude'];
        $task->longitude = $post['longitude'];
        $task->updated_at = $time;

        $history = new TasksHistory();

        $history->task_id = $task->id;
        $history->user_id = $task->user_id;
        $history->status = $task->status;
        $history->latitude = $post['latitude'];
        $history->longitude = $post['longitude'];
        $history->updated_at = $time;

        $user = Users::findOne($task->user_id);
        $user->longitude = $post['latitude'];
        $user->latitude = $post['latitude'];

        $user->save();

        $recall = Recall::findOne($post['id']);
        $recall->latitude = $post['latitude'];
        $recall->longitude = $post['longitude'];

        $recall->save(false);

        if ($task->save(false) && $history->save()) {
            return $task;
        } else {
            return false;
        }
    }
}