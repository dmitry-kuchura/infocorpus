<?php

namespace app\modules\frontend\controllers;

use app\models\Tasks;
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
        $result = Recall::getListRequest();

        if ($result) {
            return [
                'success' => true,
                'data' => $result,
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
                    'date-recall' => date('Y-m-d H:i', $obj->time / 1000),
                    'recall-during' => date('H:i', mktime(0, 0, $obj->recall_during / 1000)),
                    'recall-every' => date('H:i', mktime(0, 0, $obj->call_security_after / 1000)),
                    'user' => $obj->user->username,
                    'userID' => $obj->user->id,
                    'phone' => $obj->user->phone,
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

            $status = Recall::updateRecall($post['id']);

            if ($status) {
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
}