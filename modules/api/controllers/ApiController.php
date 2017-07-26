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
        // Standard before action start
        $result = parent::beforeAction($action);

        // Data
        $this->action = $action->id;

        // Set ajax response format to JSON as default
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Send result of standard before action in the end
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
}
