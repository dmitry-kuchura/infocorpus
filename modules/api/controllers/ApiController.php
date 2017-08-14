<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\components\Logger;

/**
 * API controller for the `api` module
 */
class ApiController extends Controller
{

    public $action;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        $this->action = $action->id;

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;

    }

    /**
     * Save Log data
     * @return array
     */
    public function actionIndex()
    {
        Logger::saveLog(Yii::$app->request->get(), $this->action);
        return ['success' => true];
    }

    /**
     *
     */
    public function actionLogin()
    {

    }
}
