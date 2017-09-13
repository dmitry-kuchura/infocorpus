<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\components\Logger;

class BaseController extends Controller
{
    /**
     * Application/JSON response
     *
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Сохранение логов
     *
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        $request = Yii::$app->post->getRaw();

        $request['Authorization-token'] = Yii::$app->request->headers->get('Authorization-token');

        Logger::saveLog($request, $action->id, $result);

        return $result;
    }
}