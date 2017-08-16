<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\httpclient\Exception;
use app\components\Logger;
use app\modules\frontend\models\Users;

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
     * @throws Exception
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
                    'login' => Yii::$app->user->identity->username,
                    'role' => Yii::$app->user->identity->role,
                    'success' => true,
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

    /**
     * Check AuthKey
     *
     * @return array
     */
    public function actionCheckAuth()
    {
        if (Yii::$app->request->get('key') == Yii::$app->user->identity->getAuthKey()) {
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
     * Reset password
     *
     * @return array
     * @throws Exception
     */
    public function actionResetPassword()
    {
        if (Yii::$app->request->get('email')) {
            if (Users::resetPassword(Yii::$app->request->get('email'))) {
                return ['success' => true];
            } else {
                return ['success' => false];
            }
        } else {
            throw new Exception('No email found!');
        }
    }

    /**
     * Markers for index map
     *
     * @return array
     */
    public function actionMap()
    {
        return [
            'success' => true,
            'result' => [
                'groups' => [
                    0 => [
                        'longitude' => 32.642713,
                        'latitude' => 46.671288,
                        'name' => 'Белка',
                        'status' => 2,
                        'type' => 'group'
                    ],
                    1 => [
                        'longitude' => 32.603832,
                        'latitude' => 46.655619,
                        'name' => 'Мангуст',
                        'status' => 1,
                        'type' => 'group'
                    ],
                    2 => [
                        'longitude' => 32.618337,
                        'latitude' => 46.632992,
                        'name' => 'Удав',
                        'status' => 2,
                        'type' => 'group'
                    ],
                    3 => [
                        'longitude' => 32.564006,
                        'latitude' => 46.652261,
                        'name' => 'Писец',
                        'status' => 3,
                        'type' => 'group'
                    ],
                    4 => [
                        'longitude' => 32.631297,
                        'latitude' => 46.646193,
                        'name' => 'Енот',
                        'status' => 1,
                        'type' => 'group'
                    ],
                ],
                'alerts' => [
                    0 => [
                        'longitude' => 32.630632,
                        'latitude' => 46.638230,
                        'name' => 'Петренко Николай Борисович',
                        'location' => 'м. Херсон, вул. Артилерійська, 14',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert'
                    ],
                    1 => [
                        'longitude' => 32.612513,
                        'latitude' => 46.637316,
                        'name' => 'Новиков Григорий Иванович',
                        'location' => 'м. Херсон, вул. Ярослава Мудрого, 12',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert'
                    ],
                    2 => [
                        'longitude' => 32.620988,
                        'latitude' => 46.628622,
                        'name' => 'Иванов Пётр Сергеевич',
                        'location' => 'м. Херсон, просп. Ушакова, 1А',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert'
                    ],
                ]
            ]
        ];
    }
}
