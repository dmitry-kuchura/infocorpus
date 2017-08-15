<?php

namespace app\components;

use Yii;
use app\models\Logs;

class Logger
{

    /**
     * Save a Log data
     *
     * @param $request
     * @param $action
     * @param $result
     */
    public static function saveLog($request, $action, $result)
    {
        $ip = Yii::$app->request->getUserIP();
        $agent = Yii::$app->request->getUserAgent();

        $request = count($request) ? json_encode($request) : null;

        $model = new Logs();

        $model->ip = $ip;
        $model->action = $action;
        $model->request = $request;
        $model->created_at = time();
        $model->updated_at = time();
        $model->agent = $agent;
        $model->response = serialize($result);

        if ($model->validate()) {
            $model->save();
        }
    }
}