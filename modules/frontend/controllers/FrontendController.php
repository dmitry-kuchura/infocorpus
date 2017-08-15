<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\httpclient\Exception;
use yii\web\Response;
use yii\web\Controller;
use app\components\Logger;
use app\modules\frontend\models\Users;
use yii\web\NotFoundHttpException;

class FrontendController extends Controller
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

    /**
     * Login to FRONTEND
     *
     * @return array
     */
    public function actionAuth()
    {
        if (Yii::$app->user->isGuest) {
            $data = Yii::$app->request->get();

            $model = new Users();

            $model->username = $data['login'];
            $model->password = $data['password'];

            if ($model->login()) {
                return [
                    'auth_key' => Yii::$app->user->identity->getAuthKey(),
                    'success' => true
                ];
            } else {
                throw new Exception("Incorrect password!");
            }
        } else {
            return [
                'auth' => 'Already auth!',
                'success' => false,
                'auth_key' => Yii::$app->user->identity->getAuthKey()
            ];
        }
    }

    /**
     * Logout from FRONTEND
     *
     * @return array
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return [
            'auth' => 'Go home!',
            'success' => true
        ];
    }

    /**
     * Registered SuperUser
     */
    public function actionRegistration()
    {
        if (Yii::$app->request->get()) {

            $data = Yii::$app->request->get();

            $model = new Users();
            $model->username = $data['login'];
            $model->password = $data['password'];
            $model->uid = $data['uid'];
            $model->email = $data['email'];
            $model->first_name = $data['first_name'];
            $model->last_name = $data['last_name'];
            $model->short_name = $data['short_name'];
            $model->client_id = $data['client_id'];

            $model->signUp();
        }
    }
}
