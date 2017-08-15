<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\Logger;

class ApiController extends Controller
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
     * Save log
     *
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        Logger::saveLog(Yii::$app->request->get(), $action->id, $result);

        return $result;
    }

    public function actionLogin()
    {
        return ['auth' => false];
    }

    public function actionChangeClientStatus()
    {
        return [
            'aid' => 'some_string',
            'status' => true
        ];
    }

    public function actionChangeGroupStatus()
    {
        return [
            'uid' => 'user_id',
            'name' => 'name',
            'phone' => 'phone',
            'big_photo' => 'http://big_image.png',
            'small_photo' => 'http://small_image.png',
            'longitude' => 32.1111111,
            'latitude' => 46.1111111,
        ];
    }
}
