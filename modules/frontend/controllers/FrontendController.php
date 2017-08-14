<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\modules\frontend\models\Users;
use yii\web\Controller;

class FrontendController extends Controller
{
    public function actionAuth()
    {
        if (Yii::$app->request->get()) {

            $data = Yii::$app->request->get();

            $model = new Users();

            $model->username = $data['login'];
            $model->password = $data['password'];

            $model->validatePassword();

            var_dump(Yii::$app->user->id);
            die;
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
