<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\Recall;

/**
 * Class RequestController
 *
 * Содержит все action'sы которые отсносятся к редактированию пользователей
 *
 * @package app\modules\frontend\controllers
 */
class RequestController extends BaseController
{
    /**
     * Список запросов на перезвон
     *
     * @return array
     */
    public function actionList()
    {
        /* @var $result Recall */
        $result = Recall::find()->all();

        $recall = [];

        foreach ($result as $obj) {
            $recall[] = [
                'id' => $obj->id,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'user' => $obj->user->username,
                'phone' => $obj->user->phone,
                'status' => $obj->call_request,
                'time_created' => date('Y-m-d H:i:s', $obj->date / 1000),
                'alert_after' => $obj->call_security_after,
            ];
        }

        if ($result) {
            return [
                'success' => true,
                'data' => $recall,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Удаление на всякий случай
     *
     * @return array
     */
    public function actionDelete()
    {
        if (Yii::$app->post->getRaw('id')) {
            $model = Recall::findOne(Yii::$app->post->getRaw('id'));

            if ($model->delete()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                ];
            }
        }
    }

    /**
     * Изменение статуса запроса звонка
     *
     * @return array
     */
    public function actionStatus()
    {
        if (Yii::$app->post->getRaw('id')) {
            $model = Recall::findOne(Yii::$app->post->getRaw('id'));

            $model->call_request = $model->call_request == 1 ? 0 : 1;

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                    'current' => $model->call_request,
                ];
            } else {
                return [
                    'success' => false,
                ];
            }
        };
    }

    /**
     * Проверка на ближайший перезвон
     */
    public function actionCheckRecall()
    {
        /* @var $recall Recall */
        $recall = Recall::findCalls();

        $array = [];

        if (count($recall)) {
            foreach ($recall as $obj) {
                $array[] = [
                    'id' => $obj->id,
                    'date-recall' => date('Y-m-d H:i', $obj->date / 1000),
                    'recall-during' => date('H:i', mktime(0, 0, $obj->recall_after / 1000)),
                    'recall-every' => date('H:i', mktime(0, 0, $obj->call_security_after / 1000)),
                    'user' => $obj->user->username,
                    'userID' => $obj->user->id,
                    'status' => $obj->status,
                ];
            }

            $call = true;
        } else {
            $call = false;
        }

        if ($call) {
            return [
                'success' => true,
                'recalls' => $array,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Создание тревоги из перезвона
     *
     * @return array
     */
    public function actionCreateAlertRecall()
    {
        if (Yii::$app->post->getRaw('id')) {
            $model = Recall::findOne(Yii::$app->post->getRaw('id'));

            $alert = Recall::createAlert($model);

            if ($alert) {
                return [
                    'success' => true,
                    'isActive' => $alert ? true : false,
                    'identity' => $alert,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Not created',
                    ],
                ];
            }
        }
    }

    public function actionUpdateRecall()
    {
        if (Yii::$app->post->getRaw()) {
            $post = Yii::$app->post->getRaw();

            $model = Recall::findOne($post['id']);

//            $recallTime = $model->date *
        }
    }
}