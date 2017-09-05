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
    public function actionCreateCustomer()
    {
        $symbol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuwxyz0123456789';

        if (Yii::$app->request->post()) {

            $data = Yii::$app->request->post();

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
            $model->image = Users::uploadPhoto();
            $model->status = 1;
            $model->role = 0;

            if ($model->createCustomer()) {
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
                'image' => is_file(HOST . Url::to('/images/small/' . $obj->image)) ? Url::to('/images/small/' . $obj->image) : Url::to('/img/no-photo.png'),
            ];
        }

        return [
            'success' => true,
            'customers' => $customers,
        ];
    }
}