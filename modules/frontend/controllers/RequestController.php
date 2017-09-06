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
            $recall = [
                'id' => $obj->id,
                'user' => $obj->user->username,
                'status' => $obj->call_request,
                'time_created' => date('Y-m-d H:i:s', $obj->time),
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
}