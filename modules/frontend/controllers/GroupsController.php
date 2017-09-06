<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\Cars;
use app\models\Tasks;
use app\models\TasksHistory;

/**
 * Class GroupsController
 *
 * Содержит все action'sы которые отсносятся к редактированию групп
 *
 * @package app\modules\frontend\controllers
 */
class GroupsController extends BaseController
{
    /**
     * Создание группы
     *
     * @return array
     */
    public function actionGroupCreate()
    {
        if (Yii::$app->post->getRaw()) {
            $date = Yii::$app->post->getRaw();

            $model = new Cars();

            $model->name = $date['name'];
            $model->status = 0;
            $model->aid = Yii::$app->security->generateRandomString();
            $model->token = Yii::$app->security->generateRandomString();
            $model->created_at = time();
            $model->updated_at = time();

            if ($model->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $model->getErrors()
                ];
            }

        }
    }

    /**
     * Получение списка всех групп
     *
     * @return array
     */
    public function actionGroupList()
    {
        /* @var $result Cars */
        $result = Cars::find()->all();

        foreach ($result as $obj) {
            $groups[] = [
                'id' => $obj->id,
                'name' => $obj->name,
                'status' => $obj->status,
                'available' => $obj->available,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
            ];
        }

        return [
            'success' => true,
            'groups' => $groups
        ];
    }

    /**
     * Простановака или закрепления группы за конкретной тревогой
     *
     * @return array
     */
    public function actionGroupAlert()
    {
        if (Yii::$app->post->getRaw()) {
            $data = Yii::$app->post->getRaw();

            $model = Tasks::findOne($data['alert-id']);
            $model->car_id = $data['group-id'];
            $model->status = 2;
            $model->updated_at = time();

            $car = Cars::findOne($data['group-id']);
            $car->status = 2;

            $history = new TasksHistory();
            $history->user_id = $model->id;
            $history->task_id = $model->id;
            $history->status = 2;
            $history->longitude = $model->longitude;
            $history->latitude = $model->latitude;
            $history->updated_at = time();

            $history->save();

            if ($model->save(false) && $car->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    /**
     * Изменение статуса группы:
     *
     * 0 - В гараже
     * 1 - В патруле
     * 2 - На вызове
     *
     * @return array
     */
    public function actionGroupChangeStatus()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $car = Cars::findOne(Yii::$app->post->getRaw('ID'));

            $car->status = Yii::$app->post->getRaw('status');

            if ($car->validate() && $car->save()) {
                return [
                    'success' => true,
                    'current' => $car->status
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        };
    }

    /**
     * Простановка статуса учетной записи группы
     *
     * @return array
     */
    public function actionGroupChangeAllow()
    {
        if (Yii::$app->post->getRaw()) {
            $group = Cars::findOne(Yii::$app->post->getRaw('ID'));

            $group->available = $group->available == 1 ? 0 : 1;

            if ($group->validate() && $group->save()) {
                return [
                    'success' => true,
                    'current' => $group->available
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    /**
     * Удаление группы
     *
     * @return array
     */
    public function actionGroupDelete()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $group = Cars::findOne(Yii::$app->post->getRaw('ID'));

            if ($group->delete()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        };
    }

    /**
     * Снятие/отмена группы с текущей тревоги
     *
     * @return array
     */
    public function actionGroupCancel()
    {
        if (Yii::$app->post->getRaw()) {
            $model = Tasks::findOne(Yii::$app->post->getRaw('ID'));

            $model->status = 0;
            $model->updated_at = time();

            $history = new TasksHistory();
            $history->updated_at = time();
            $history->status = 0;
            $history->task_id = $model->id;
            $history->user_id = $model->user_id;

            if ($model->car_id) {
                $car = Cars::findOne($model->car_id);

                $car->status = 1;
                $car->save(false);
            }

            if ($model->save(false) && $history->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    /**
     * Отправка сообщения в группу [Будет в следующем релизе]
     */
    public function actionGroupSendMessage()
    {

    }

    public function actionGroupGetData()
    {
        if (Yii::$app->post->getRaw('id')) {
            $data = Cars::findOne(Yii::$app->post->getRaw('id'));

            return [
                'success' => true,
                'data' => $data
            ];
        } else {
            return ['success' => false];
        }
    }
}