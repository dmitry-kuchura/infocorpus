<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\filters\Cors;
use yii\web\Response;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\httpclient\Exception;
use app\models\Users;
use app\components\Logger;

class FrontendController extends Controller
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
                    ]
                ]
            ],
        ], parent::behaviors());
    }

    /**
     * Ответ в формате JSON
     *
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $actions = ['auth', 'logout', 'reset-password', 'users-list', 'change-allow'];

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
                'auth_key' => Yii::$app->user->identity->getAuthKey()
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
            'success' => true
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
                    'message' => 'Не корректный email, пользователь не был найден!'
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
                'groups' => [
                    0 => [
                        'id' => 13,
                        'longitude' => 32.642713,
                        'latitude' => 46.671288,
                        'name' => 'Белка',
                        'status' => 2,
                        'type' => 'group'
                    ],
                    1 => [
                        'id' => 16,
                        'longitude' => 32.603832,
                        'latitude' => 46.655619,
                        'name' => 'Мангуст',
                        'status' => 1,
                        'type' => 'group'
                    ],
                    2 => [
                        'id' => 17,
                        'longitude' => 32.618337,
                        'latitude' => 46.632992,
                        'name' => 'Удав',
                        'status' => 2,
                        'type' => 'group'
                    ],
                    3 => [
                        'id' => 21,
                        'longitude' => 32.564006,
                        'latitude' => 46.652261,
                        'name' => 'Писец',
                        'status' => 3,
                        'type' => 'group'
                    ],
                    4 => [
                        'id' => 27,
                        'longitude' => 32.631297,
                        'latitude' => 46.646193,
                        'name' => 'Енот',
                        'status' => 1,
                        'type' => 'group'
                    ],
                ],
                'alerts' => [
                    0 => [
                        'id' => 15,
                        'status' => 1,
                        'longitude' => 32.630632,
                        'latitude' => 46.638230,
                        'name' => 'Петренко Николай Борисович',
                        'location' => 'м. Херсон, вул. Артилерійська, 14',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert',
                        'date' => date('d.m.Y в H:i'),
                    ],
                    1 => [
                        'id' => 17,
                        'status' => 2,
                        'longitude' => 32.612513,
                        'latitude' => 46.637316,
                        'name' => 'Новиков Григорий Иванович',
                        'location' => 'м. Херсон, вул. Ярослава Мудрого, 12',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert',
                        'date' => date('d.m.Y в H:i'),
                    ],
                    2 => [
                        'id' => 20,
                        'status' => 1,
                        'longitude' => 32.620988,
                        'latitude' => 46.628622,
                        'name' => 'Иванов Пётр Сергеевич',
                        'location' => 'м. Херсон, просп. Ушакова, 1А',
                        'phone' => '+38(099)999-99-99',
                        'type' => 'alert',
                        'date' => date('d.m.Y в H:i'),
                    ],
                ]
            ]
        ];
    }

    /**
     * Создание пользователя
     *
     * @return array
     */
    public function actionCreateUser()
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

            if ($model->validate() && $model->signUp()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $model->getErrors()
                ];
            }
        }
    }

    /**
     * Список пользователей
     *
     * @return array
     */
    public function actionUsersList()
    {
        /* @var $result Users */
        $result = Users::find()->where(['!=', 'role', 666])->all();

        foreach ($result as $obj) {
            $users[] = [
                'id' => $obj->id,
                'email' => $obj->email,
                'password' => $obj->password,
                'phone' => $obj->phone,
                'name' => $obj->username,
                'status' => $obj->status,
                'role' => $obj->role == 1 ? 'Администратор' : 'Пользователь',
            ];
        }

        return [
            'success' => true,
            'users' => $users
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
                    'current' => $user->status
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        };
    }

    /**
     * Удаление пользователя (насовсем)
     *
     * @return array
     */
    public function actionRemoveUser()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $user = Users::findOne(Yii::$app->post->getRaw('ID'));

            if ($user->delete()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        };
    }
}