<?php

namespace app\modules\api\controllers;

use app\models\Cars;
use app\models\Tasks;
use Yii;
use http\Exception;
use yii\web\Response;
use yii\web\Controller;
use app\models\Recall;
use app\modules\frontend\models\Users;
use app\components\Logger;
use yii\web\User;

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

        Logger::saveLog(Yii::$app->request->post(), $action->id, $result);

        return $result;
    }

    /**
     * Авторизация
     *
     * @return array
     */
    public function actionLogin()
    {
        $user = Users::findByUid(Yii::$app->request->post('uid'));

        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            return [
                'success' => true,
                'error' => [
                    'code' => 200,
                    'message' => 'User found!'
                ],
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
        if (Yii::$app->request->post()) {

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

            $data = Yii::$app->request->post();

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
                    'error' => [
                        'code' => 200,
                        'message' => 'Recall was save!'
                    ]
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
     * Простановка таска и запрос статуса тревоги
     *
     * @return array
     */
    public function actionAlert()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->request->headers->get('Authorization-token'));

        if (Yii::$app->request->post()) {

            $data = Yii::$app->request->post();

            if (!$user) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 500,
                        'message' => 'User not found!'
                    ]
                ];
            }

            $model = new Tasks();

            $model->user_id = $user->id;
            $model->status = 1;
            $model->longitude = $data['longitude'];
            $model->latitude = $data['latitude'];

            if ($model->validate() && $model->save()) {
                return [
                    'success' => true,
                    'error' => [
                        'code' => 200,
                        'message' => 'Alarm was created!'
                    ],
                ];
            }
        } else {
            $task = Tasks::getLastTask($user);

            if ($task) {
                return [
                    'success' => true,
                    'error' => [
                        'code' => 200,
                        'message' => 'Getting status'
                    ],
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

        if (Yii::$app->request->post()) {
            $data = Yii::$app->request->post();

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
                    'error' => [
                        'code' => 200,
                        'message' => 'Cars was updated!'
                    ]
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
                'error' => [
                    'code' => 200,
                    'message' => 'Getting status!'
                ],
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
}
