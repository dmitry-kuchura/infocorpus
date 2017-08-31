<?php

namespace app\modules\frontend\controllers;

use app\models\Cars;
use app\models\Tasks;
use app\models\TasksHistory;
use Yii;
use yii\filters\Cors;
use yii\web\Response;
use yii\base\Exception;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
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
     * @param \yii\base\Action $action
     * @return bool
     * @throws Exception
     */
    public function beforeAction($action)
    {
        $actions = [
            'auth',
            'logout',
            'reset-password'
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
        /* @var $tasksData Tasks */
        $tasksData = Tasks::find()->where(['!=', 'status', 0])->all();

        $task = [];

        foreach ($tasksData as $obj) {
            $task[] = [
                'id' => $obj->id,
                'status' => 1,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'name' => $obj->user->username,
                'location' => 'м. Херсон, вул. Артилерійська, 14',
                'phone' => '+38(099)999-99-99',
                'type' => 'alert',
                'date' => date('d.m.Y в H:i', $obj->created_at),
            ];
        }

        /* @var $carsData Cars */
        $carsData = Cars::find()->where(['status' => 1])->all();

        $cars = [];

        foreach ($carsData as $obj) {
            $cars[] = [
                'id' => $obj->id,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'name' => $obj->name,
                'status' => $obj->status,
                'type' => 'group'
            ];
        }

        return [
            'success' => true,
            'result' => [
                'groups' => $cars,
                'alerts' => $task
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

            if ($model->signUp()) {
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
        $result = Users::find()->where(['=', 'role', 1])->all();

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

    /**
     * Создание клиента
     *
     * @return array
     */
    public function actionCreateCustomer()
    {
        $symbol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuwxyz0123456789';

        if (Yii::$app->post->getRaw()) {

            $data = Yii::$app->post->getRaw();

            $model = new Users();
            $model->username = $data['name'];
            $model->phone = $data['phone'];
            $model->imei = $data['imei'];
            $model->email = $data['email'];
            $model->skype = $data['skype'];
            $model->address = $data['location'];
            $model->organization = $data['company'];
            $model->location = $data['company-location'];
            $model->car_name = $data['car'];
            $model->car_color = $data['car-color'];
            $model->car_number = $data['car-number'];
            $model->password = substr(str_shuffle(str_repeat($symbol, 8)), 0, 10);
            $model->status = 1;
            $model->role = 0;

            if ($model->createCustomer()) {
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
     * Получение списка клиентов
     *
     * @return array
     */
    public function actionListCustomers()
    {
        /* @var $result Users */
        $result = Users::find()->where(['role' => 0])->all();

        foreach ($result as $obj) {
            $customers[] = [
                'id' => $obj->id,
                'name' => $obj->username,
                'phone' => $obj->phone,
                'imei' => $obj->imei,
                'identity' => $obj->auth_key,
                'location' => $obj->address,
                'status' => $obj->status,
            ];
        }

        return [
            'success' => true,
            'customers' => $customers
        ];
    }

    /**
     * Получение конкретного пользователя по ID
     *
     * @return static
     */
    public function actionGetUserData()
    {
        if (Yii::$app->post->getRaw('id')) {
            $data = Users::findOne(Yii::$app->post->getRaw('id'));

            return ['data' => $data];
        }
    }

    public function actionGroupCreate()
    {
        if (Yii::$app->post->getRaw()) {
            $date = Yii::$app->post->getRaw();

            $model = new Cars();

            $model->name = $date['name'];
            $model->status = 0;
            $model->aid = Yii::$app->security->generateRandomString();
            $model->token = Yii::$app->security->generateRandomString();
            $model->created_at = time();
            $model->updated_at = time();

            if ($model->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false,
                    'errors' => $model->getErrors()
                ];
            }

        }
    }

    public function actionGroupList()
    {
        /* @var $result Cars */
        $result = Cars::find()->all();

        foreach ($result as $obj) {
            $groups[] = [
                'id' => $obj->id,
                'name' => $obj->name,
                'status' => $obj->status,
                'available' => $obj->available,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
            ];
        }

        return [
            'success' => true,
            'groups' => $groups
        ];
    }

    public function actionGroupAlert()
    {
        if (Yii::$app->post->getRaw()) {
            $data = Yii::$app->post->getRaw();

            $model = Tasks::findOne($data['alert-id']);
            $model->car_id = $data['group-id'];
            $model->status = 2;
            $model->updated_at = time();

            $car = Cars::findOne($data['group-id']);
            $car->status = 2;

            $history = new TasksHistory();
            $history->user_id = $model->id;
            $history->task_id = $model->id;
            $history->status = 2;
            $history->longitude = $model->longitude;
            $history->latitude = $model->latitude;
            $history->updated_at = time();

            $history->save();

            if ($model->save() && $car->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    public function actionGroupChangeStatus()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $car = Cars::findOne(Yii::$app->post->getRaw('ID'));

            $car->status = Yii::$app->post->getRaw('status');

            if ($car->validate() && $car->save()) {
                return [
                    'success' => true,
                    'current' => $car->status
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        };
    }

    public function actionGroupChangeAllow()
    {
        if (Yii::$app->post->getRaw()) {
            $group = Cars::findOne(Yii::$app->post->getRaw('ID'));

            $group->available = $group->available == 1 ? 0 : 1;

            if ($group->validate() && $group->save()) {
                return [
                    'success' => true,
                    'current' => $group->available
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    public function actionGroupDelete()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $group = Cars::findOne(Yii::$app->post->getRaw('ID'));

            if ($group->delete()) {
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

    public function actionGroupCancel()
    {
        if (Yii::$app->post->getRaw()) {
            $model = Tasks::findOne(Yii::$app->post->getRaw('ID'));

            $model->status = 0;
            $model->updated_at = time();

            $history = new TasksHistory();
            $history->updated_at = time();
            $history->status = 0;
            $history->task_id = $model->id;
            $history->user_id = $model->user_id;

            if ($model->car_id) {
                $car = Cars::findOne($model->car_id);

                $car->status = 1;
                $car->save(false);
            }

            if ($model->save() && $history->save()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false
                ];
            }
        }
    }

    public function actionGroupSendMessage()
    {
    }
}