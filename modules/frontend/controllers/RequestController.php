<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\Users;

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
     * Создание пользователя
     *
     * @return array
     */
    public function actionRequest()
    {
        if (Yii::$app->post->getRaw()) {

            $data = Yii::$app->post->getRaw();

            $model = new Users();
            $model->username = $data['name'];
            $model->email = $data['email'];
            $model->phone = $data['phone'];
            $model->password = $data['password'];
            $model->status = 1;
            $model->role = 1;

            if ($model->signUp()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $model->getErrors(),
                ];
            }
        }
    }
}