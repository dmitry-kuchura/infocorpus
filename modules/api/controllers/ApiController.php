<?php

namespace app\modules\api\controllers;

use Yii;
use app\models\Cars;
use app\models\Users;
use app\models\Tasks;
use app\models\Recall;
use app\models\Messages;
use app\models\CarHistory;
use app\models\TasksHistory;

class ApiController extends BaseController
{
    /**
     * Авторизация
     *
     * @return array
     */
    public function actionLogin()
    {
        $user = Users::findByUid(Yii::$app->post->getRaw('uid'));

        if ($user && Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return [
                'success' => true,
                'token' => Yii::$app->user->identity->auth_key,
            ];
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 404,
                    'message' => 'Ooooops. User not found!',
                ],
                'token' => null,
            ];
        }
    }

    /**
     * Авторизация группы
     *
     * @return array
     */
    public function actionLoginGroup()
    {
        $car = Cars::findOne(['name' => Yii::$app->post->getRaw('uid'), 'available' => 1]);

        if ($car) {
            return [
                'success' => true,
                'token' => $car->token,
            ];
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 404,
                    'message' => 'Ooooops. Car not found!',
                ],
                'token' => null,
            ];
        }
    }

    /**
     * Заказ звонка
     *
     * @return array
     */
    public function actionRecall()
    {
        if (Yii::$app->post->getRaw()) {

            $user = Users::findIdentityByAccessToken(Yii::$app->request->headers->get('Authorization-token'));

            if (!$user) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'User not found!',
                    ],
                ];
            }

            $data = Yii::$app->post->getRaw();

            $result = Recall::findOne(['user_id' => $user->id]);

            if ($result) {
                $model = $result;

                $model->call_request = $data['call'] == true ? 1 : 0;
                $model->date = $data['date'];
                $model->time = $data['time'];
                $model->automatic_redial = $data['automaticRedial'] == true ? 1 : 0;
                $model->recall_after = $data['recallAfter'];
                $model->recall_during = $data['recallDuring'];
                $model->call_security_after = $data['callSecurityAfter'];
                $model->task_id = null;
                $model->status = 0;
            } else {
                $model = new Recall();

                $model->user_id = $user->id;
                $model->call_request = $data['call'] == true ? 1 : 0;
                $model->date = $data['date'];
                $model->time = $data['time'];
                $model->automatic_redial = $data['automaticRedial'] == true ? 1 : 0;
                $model->recall_after = $data['recallDuring'];
                $model->recall_during = $data['recallDuring'];
                $model->call_security_after = $data['callSecurityAfter'];
                $model->task_id = null;
                $model->status = 0;
            }

            if ($model->save(false)) {
                return [
                    'success' => true,
                    'callId' => $model->id,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Wrong data!',
                    ],
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => 'Wrong data!',
                ],
            ];
        }
    }

    /**
     * Обновление координат пользователя
     *
     * @return array
     */
    public function actionUpdateCall()
    {
        if (Yii::$app->post->getRaw() && Recall::checkRecall(Yii::$app->post->getRaw())) {
            $post = Yii::$app->post->getRaw();

            $alert = Recall::checkRecallAlert($post);

            $recall = Recall::updateCoordinate($post);
            if ($alert) {
                $recallAlert = Recall::updateAlert($post, $recall);
            } else {
                $recallAlert = null;
            }

            if ($recall) {
                return [
                    'success' => true,
                    'isActive' => $recallAlert && $recallAlert->status == 1 ? true : false,
                    'identity' => $alert,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Not updated!',
                    ],
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Recall not created!',
            ];
        }
    }

    /**
     * Проверка тревоги
     *
     * @return array
     */
    public function actionCheckAlert()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->request->headers->get('Authorization-token'));

        if (!$user) {
            return [
                'success' => false,
                'error' => [
                    'code' => 404,
                    'message' => 'User not found!',
                ],
            ];
        }

        /* @var $last Tasks */
        $last = Tasks::getLastTask($user);

        if ($last && $last->status == 1 || $last->status == 2) {
            return [
                'success' => true,
                'identity' => $last->id,
                'isActive' => $last->status == 1 ? true : false,
            ];
        } else {
            return [
                'success' => true,
                'isActive' => false,
            ];
        }
    }

    /**
     * Запуск тревоги
     *
     * @return array
     */
    public function actionAlert()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->request->headers->get('Authorization-token'));

        if (!$user) {
            return [
                'success' => false,
                'error' => [
                    'code' => 404,
                    'message' => 'User not found!',
                ],
            ];
        }

        /* @var $last Tasks */
        $last = Tasks::getLastTask($user);

        if ($last && $last->status == 1) {
            return [
                'success' => true,
                'identity' => $last->id,
                'isActive' => $last->status == 1 ? true : false,
            ];
        } else {
            $task = new Tasks();
            $time = time();

            $task->user_id = $user->id;
            $task->status = 1;
            $task->created_at = $time;
            $task->updated_at = $time;

            $task->save();

            $model = new TasksHistory();

            $model->user_id = $user->id;
            $model->task_id = $task->id;
            $model->status = 1;
            $model->updated_at = $time;

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                    'identity' => $task->id,
                    'isActive' => $model->status == 1 ? true : false,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Not created!',
                    ],
                ];
            }
        }
    }

    /**
     * Обновление координат тревоги
     *
     * @return array
     */
    public function actionUpdateAlert()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->request->headers->get('Authorization-token'));

        if (Yii::$app->post->getRaw() && $user) {
            $data = Yii::$app->post->getRaw();
            $time = time();

            $user->longitude = $data['longitude'];
            $user->latitude = $data['latitude'];
            $user->save(false);

            /* Обновление тривоги */
            $task = Tasks::findOne($data['identity']);

            if ($task->status == 1 || $task->status == 2) {
                $task->longitude = $data['longitude'];
                $task->latitude = $data['latitude'];
                $task->updated_at = $time;

                $task->save(false);

                /* Запись истории тривоги */
                $model = new TasksHistory();

                $model->user_id = $user->id;
                $model->task_id = $task->id;
                $model->status = $task->status;
                $model->longitude = $data['longitude'];
                $model->latitude = $data['latitude'];
                $model->updated_at = $time;

                $model->save();

                return [
                    'success' => true,
                    'isActive' => true,
                    'identity' => $task->id,
                ];
            } else {
                return [
                    'success' => true,
                    'isActive' => false,
                ];
            }
        } else {
            $task = Tasks::getLastTask($user);

            if ($task) {
                return [
                    'success' => true,
                    'identity' => $task->id,
                    'isActive' => $task->status == 1 ? true : false,
                ];
            }
        }
    }

    /**
     * Обновление статуса автомобиля
     *
     * @return array
     */
    public function actionStatusChange()
    {
        $car = Cars::findByToken(Yii::$app->request->headers->get('Authorization-token'));

        if (Yii::$app->post->getRaw()) {
            if (!$car) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Car not found!',
                    ],
                ];
            }

            $task = Tasks::getTaskForCar($car);

            $model = Cars::updateCarStatus(Yii::$app->request->headers->get('Authorization-token'), Yii::$app->post->getRaw());

            $car = Cars::findByToken(Yii::$app->request->headers->get('Authorization-token'));

            if ($model) {
                return [
                    'success' => true,
                    'status' => $car->status,
                    'message' => $this->getMessage($car->id),
                    'user' => $task,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Cars status was not updated!',
                    ],
                ];
            }
        }
    }

    /**
     * Обновление координатов автомобиля
     *
     * @return array
     */
    public function actionStatusUpdate()
    {
        $car = Cars::findByToken(Yii::$app->request->headers->get('Authorization-token'));

        if (Yii::$app->post->getRaw()) {
            if (!$car) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Car not found!',
                    ],
                ];
            }

            $task = Tasks::getTaskForCar($car);

            $model = Cars::updateCar(Yii::$app->request->headers->get('Authorization-token'), Yii::$app->post->getRaw());

            CarHistory::saveCarHistory(Yii::$app->post->getRaw(), $car->id);

            if ($model) {
                return [
                    'success' => true,
                    'status' => $car->status,
                    'message' => $this->getMessage($car->id),
                    'user' => $task,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Cars status was not updated!',
                    ],
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => 'Cars status was not updated!',
                ],
            ];
        }
    }

    /**
     * Получение последнего мессаджа
     *
     * @param $car
     * @return string
     */
    function getMessage($car)
    {
        /* @var $message Messages */
        $message = Messages::find()->where(['readed' => 0, 'car_id' => $car])->orderBy('id DESC')->one();
        $text = '';

        if ($message) {
            $text = $message->text;

            $message->readed = 1;
            $message->updated_at = time();

            $message->save();
        }

        return $text;
    }
}