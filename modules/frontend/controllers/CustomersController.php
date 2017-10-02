<?php

namespace app\modules\frontend\controllers;

use Yii;
use yii\helpers\Url;
use app\models\Users;

/**
 * Class CustomersController
 *
 * Содержит все action'sы которые отсносятся к редактированию пользователей
 *
 * @package app\modules\frontend\controllers
 */
class CustomersController extends BaseController
{
    /**
     * Создание клиента
     *
     * @return array
     */
    public function actionCustomerCreate()
    {
        if (Yii::$app->request->post()) {
            if (Users::createCustomer(Yii::$app->request->post())) {
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
     * Обновление данных клиента
     *
     * @return array
     */
    public function actionCustomerUpdate()
    {
        if (Yii::$app->request->post()) {
            if (Users::updateCustomer(Yii::$app->request->post())) {
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
     * Получение списка клиентов
     *
     * @return array
     */
    public function actionCustomerList()
    {
        $customers = Users::getListCustomers();

        if (count($customers)) {
            return [
                'success' => true,
                'customers' => $customers,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }
}