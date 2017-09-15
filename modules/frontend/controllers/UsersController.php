<?php

namespace app\modules\frontend\controllers;

use app\models\Tasks;
use Yii;
use app\models\Users;

/**
 * Class UsersController
 *
 * Содержит все action'sы которые отсносятся к редактированию пользователей
 *
 * @package app\modules\frontend\controllers
 */
class UsersController extends BaseController
{
    /**
     * Создание пользователя
     *
     * @return array
     */
    public function actionUserCreate()
    {
        if (Yii::$app->post->getRaw()) {

            $data = Yii::$app->post->getRaw();

            $model = new Users();
            $model->username = $data['name'];
            $model->email = $data['email'];
            $model->phone = $data['phone'];
            $model->password = $data['password'];
            $model->status = 1;
            $model->role = $data['admin'] ? 666 : 1;

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

    /**
     * Обновление пользовательских данных
     *
     * @return array
     */
    public function actionUserUpdate()
    {
        if (Yii::$app->post->getRaw()) {
            $data = Yii::$app->post->getRaw();

            $model = Users::findOne($data['id']);
            $model->username = $data['name'];
            $model->email = $data['email'];
            $model->phone = $data['phone'];

            if ($model->save()) {
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

    /**
     * Список пользователей
     *
     * @return array
     */
    public function actionUserList()
    {
        /* @var $result Users */
        $result = Users::find()->where(['IN', 'role', [1, 666]])->all();

        $roles = Yii::$app->params['roles'];

        foreach ($result as $obj) {
            $users[] = [
                'id' => $obj->id,
                'email' => $obj->email,
                'password' => $obj->password,
                'phone' => $obj->phone,
                'name' => $obj->username,
                'status' => $obj->status,
                'role' => $roles[$obj->role],
            ];
        }

        return [
            'success' => true,
            'users' => $users,
        ];
    }

    /**
     * Удаление пользователя (насовсем)
     *
     * @return array
     */
    public function actionUserRemove()
    {
        if (Yii::$app->post->getRaw('ID')) {
            $user = Users::findOne(Yii::$app->post->getRaw('ID'));

            if ($user->role != 666 && Tasks::checkAlert($user->id) && $user->delete()) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
                ];
            }
        };
    }

    /**
     * Получение конкретного пользователя по ID
     *
     * @return array
     */
    public function actionUserGetData()
    {
        if (Yii::$app->post->getRaw('id')) {
            $data = Users::findOne(Yii::$app->post->getRaw('id'));

            return [
                'success' => true,
                'data' => $data,
            ];
        } else {
            return ['success' => false];
        }
    }
}