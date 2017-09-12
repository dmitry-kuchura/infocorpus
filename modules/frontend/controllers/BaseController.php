<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\filters\Cors;
use yii\web\Response;
use yii\base\Exception;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use app\models\Users;
use app\components\Logger;

class BaseController extends Controller
{
    /**
     * Настройка кроссдоманных аяксов CORS
     *
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Max-Age' => 86400,
                ],
                'actions' => [
                    'map' => [
                        'Access-Control-Allow-Credentials' => true,
                    ],
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * Авторизация пользователя
     *
     * @param \yii\base\Action $action
     * @return bool
     * @throws Exception
     */
    public function beforeAction($action)
    {
        $actions = [
            'auth',
            'logout',
            'reset-password',
            'customer-create',
            'customer-update',
            'check-recall',
        ];

        if (!in_array($action->id, $actions)) {
            $user = Users::findIdentityByAccessToken(Yii::$app->post->getRaw('key'));
            Yii::$app->user->login($user, 3600 * 24 * 30);
        }

        $result = parent::beforeAction($action);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Сохранение Лога с данными
     *
     * @param \yii\base\Action $action
     * @param mixed $result
     * @return mixed
     * @throws Exception
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        try {
            Logger::saveLog(Yii::$app->post->getRaw(), $action->id, $result);
        } catch (Exception $e) {
            throw new Exception('Log no save');
        }

        return $result;
    }
}