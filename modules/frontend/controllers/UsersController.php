<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\Tasks;
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
            if (Users::signUp(Yii::$app->post->getRaw())) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
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
            if (Users::updateUserData(Yii::$app->post->getRaw())) {
                return [
                    'success' => true,
                ];
            } else {
                return [
                    'success' => false,
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
        $users = Users::getUsersList();

        if (count($users)) {
            return [
                'success' => true,
                'users' => $users,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
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