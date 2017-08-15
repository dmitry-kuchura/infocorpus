<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Users;
use yii\web\Controller;
use yii\web\Response;

class FrontendController extends Controller
{

    public function beforeAction($action)
    {
        $result = parent::beforeAction($action);

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $result;
    }

    public function actionAuth()
    {
        if (Yii::$app->request->get()) {

            $data = Yii::$app->request->get();

            $model = new Users();

            $model->username = $data['login'];
            $model->password = $data['password'];

            if ($model->login()) {
                return ['auth' => Yii::$app->user->identity->getAuthKey()];
            } else {
                return ['auth' => false];
            }
        }

    }

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
