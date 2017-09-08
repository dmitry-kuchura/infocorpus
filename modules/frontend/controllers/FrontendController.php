<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\base\Exception;
use app\models\Map;
use app\models\Users;

class FrontendController extends BaseController
{
    /**
     * Авторизация пользователя
     *
     * @return array
     * @throws Exception
     */
    public function actionAuth()
    {
        if (Yii::$app->user->isGuest) {
            $data = Yii::$app->post->getRaw();

            $model = new Users();

            $model->email = $data['login'];
            $model->password = $data['password'];

            if ($model->login()) {
                return [
                    'auth_key' => Yii::$app->user->identity->getAuthKey(),
                    'login' => Yii::$app->user->identity->username,
                    'role' => Yii::$app->user->identity->role,
                    'success' => true,
                ];
            } else {
                return [
                    'auth' => 'Incorrect password or email!',
                    'success' => false,
                ];
            }
        } else {
            return [
                'auth' => 'Already auth!',
                'success' => false,
                'auth_key' => Yii::$app->user->identity->getAuthKey(),
            ];
        }
    }

    /**
     * Разлогин пользователя в системе
     *
     * @return array
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return [
            'auth' => 'Go home!',
            'success' => true,
        ];
    }

    /**
     * Проверка существует ли еще пользователь в системе или нет React API
     *
     * @return array
     */
    public function actionCheckAuth()
    {
        if (Yii::$app->post->getRaw('key') == Yii::$app->user->identity->getAuthKey()) {
            return [
                'auth_key' => Yii::$app->user->identity->getAuthKey(),
                'login' => Yii::$app->user->identity->username,
                'role' => Yii::$app->user->identity->role,
                'success' => true,
            ];
        } else {
            return ['success' => false];
        }
    }

    /**
     * Сброс пароля, отправка на Email
     *
     * @return array
     * @throws Exception
     */
    public function actionResetPassword()
    {
        if (Yii::$app->post->getRaw('email')) {
            if (Users::resetPassword(Yii::$app->post->getRaw('email'))) {
                return ['success' => true];
            } else {
                return [
                    'success' => false,
                    'message' => 'Не корректный email, пользователь не был найден!',
                ];
            }
        } else {
            throw new Exception('No email found!');
        }
    }

    /**
     * Маркеры на карте группы и пользователи
     *
     * @return array
     */
    public function actionMap()
    {
        return [
            'success' => true,
            'result' => [
                'groups' => Map::getGroups(),
                'alerts' => Map::getAlerts(),
            ],
        ];
    }

    /**
     * Изменение текущего статуса путем получения
     *
     * @return array
     */
    public function actionChangeAllow()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $user = Users::findOne(Yii::$app->post->getRaw('ID'));

            $user->status = $user->status == 1 ? 0 : 1;

            if ($user->validate() && $user->save()) {
                return [
                    'success' => true,
                    'current' => $user->status,
                ];
            } else {
                return [
                    'success' => false,
                ];
            }
        };
    }
}