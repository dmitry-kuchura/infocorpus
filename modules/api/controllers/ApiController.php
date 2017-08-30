<?php

namespace app\modules\api\controllers;

use app\models\TasksHistory;
use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\models\Cars;
use app\models\Users;
use app\models\Tasks;
use app\models\Recall;
use app\components\Logger;

class ApiController extends Controller
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

        $request = Yii::$app->post->getRaw() ? Yii::$app->post->getRaw() : Yii::$app->request->post();

        Logger::saveLog($request, $action->id, $result);

        return $result;
    }

    /**
     * Авторизация
     *
     * @return array
     */
    public function actionLogin()
    {
        $user = Users::findByUid(Yii::$app->post->getRaw('uid'));

        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return [
                'success' => true,
                'token' => Yii::$app->user->identity->auth_key
            ];
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 200,
                    'message' => 'Ooooops. User not found!'
                ],
                'token' => null
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
                        'message' => 'User not found!'
                    ]
                ];
            }

            $data = Yii::$app->post->getRaw();

            $model = new Recall();

            $model->user_id = $user->id;
            $model->call_request = $data['call'] == true ? 1 : 0;
            $model->date = $data['date'];
            $model->time = $data['time'];
            $model->automatic_redial = $data['automaticRedial'] == true ? 1 : 0;
            $model->recall_after = $data['time'];
            $model->recall_during = $data['time'];
            $model->call_security_after = $data['time'];

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Wrong data!'
                    ]
                ];
            }
        } else {
            return [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => 'Wrong data!'
                ]
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

        if (Yii::$app->post->getRaw()) {

            $data = Yii::$app->post->getRaw();

            if (!$user) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 404,
                        'message' => 'User not found!'
                    ]
                ];
            }


            $task = new Tasks();
            $time = time();

            $task->user_id = $user->id;
            $task->longitude = $data['longitude'];
            $task->latitude = $data['latitude'];
            $task->status = 1;
            $task->created_at = $time;
            $task->updated_at = $time;

            $task->save();

            $model = new TasksHistory();

            $model->user_id = $user->id;
            $model->task_id = $task->id;
            $model->status = 1;
            $model->longitude = $data['longitude'];
            $model->latitude = $data['latitude'];
            $model->updated_at = $time;

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                    'identity' => $task->id,
                    'isActive' => $model->status == 1 ? true : false
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Not created!'
                    ]
                ];
            }
        }

        $last = Tasks::getLastTask($user);

        if ($last->status == 1) {
            return [
                'success' => true,
                'identity' => $last->id,
                'isActive' => $last->status == 1 ? true : false
            ];
        } else {
            return [
                'success' => true,
                'isActive' => false
            ];
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

        if (Yii::$app->post->getRaw()) {
            $data = Yii::$app->post->getRaw();
            $time = time();

            /* Обновление тривоги */
            $task = Tasks::findOne($data['identity']);

            if ($task->status == 1) {
                $task->longitude = $data['longitude'];
                $task->latitude = $data['latitude'];
                $task->updated_at = $time;

                $task->save();

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
                    'identity' => $task->id
                ];
            } else {
                return [
                    'success' => true,
                    'isActive' => false
                ];
            }

        } else {
            $task = Tasks::getLastTask($user);

            if ($task) {
                return [
                    'success' => true,
                    'isActive' => $task->status == 1 ? true : false
                ];
            }
        }
    }

    /**
     * Обновление статуса автомобиля
     *
     * @return array
     */
    public function actionStatus()
    {
        $car = Cars::findByToken(Yii::$app->request->headers->get('Authorization-token'));

        if (Yii::$app->post->getRaw()) {
            $data = Yii::$app->post->getRaw();

            if (!$car) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Car not found!'
                    ]
                ];
            }

            $model = Cars::findOne(['token' => Yii::$app->request->headers->get('Authorization-token')]);

            $model->longitude = $data['longitude'];
            $model->latitude = $data['latitude'];
            $model->status = $data['status'];
            $model->updated_at = time();
            $model->created_at = time();

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'Cars status was not updated!'
                    ]
                ];
            }
        } else {
            return [
                'success' => true,
                'status' => $car->status,
                'user' => [
                    'uid' => 'user_id',
                    'name' => 'name',
                    'phone' => 'phone',
                    'big_photo' => 'http =>//big_image.png',
                    'small_photo' => 'http =>//small_image.png',
                    'longitude' => 32.1111111,
                    'latitude' => 46.1111111
                ]
            ];
        }
    }

    /**
     * Сброс тревог к определенному статусу
     *
     * @return array
     */
    public function actionResetStatus()
    {
        $id = Yii::$app->request->get('id');

        $task = Tasks::findOne($id);

        $task->status = 0;
        $task->updated_at = time();

        $task->save(false);

        $model = new TasksHistory();

        $model->task_id = $task->id;
        $model->status = 0;
        $model->user_id = $task->user_id;
        $model->updated_at = time();

        $model->save(false);

        return ['success' => true];
    }
}