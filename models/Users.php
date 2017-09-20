<?php

namespace app\models;

use Yii;
use app\components\File;
use app\components\Image;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $uid
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $imei
 * @property string $skype
 * @property string $address
 * @property string $organization
 * @property string $location
 * @property string $car_name
 * @property string $car_color
 * @property string $car_number
 * @property string $longitude
 * @property string $latitude
 * @property string $auth_key
 * @property string $hash
 * @property integer $v
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $image
 */
class Users extends UserInterface
{
    public function rules()
    {
        return [
            [['uid', 'password'], 'required'],
            [['v', 'status', 'role', 'created_at', 'updated_at'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['phone'], 'unique'],
            [['imei'], 'string', 'max' => 16],
            [['uid', 'username', 'skype', 'car_name', 'car_color', 'car_number'], 'string', 'max' => 50],
            [['email', 'image', 'password', 'phone', 'address', 'organization', 'location', 'auth_key', 'hash'], 'string', 'max' => 150],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'uid' => 'Uid',
            'email' => 'Email',
            'phone' => 'Phone',
            'username' => 'Username',
            'password' => 'Password',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'short_name' => 'Short Name',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'token' => 'Token',
            'auth_key' => 'Auth Key',
            'hash' => 'Hash',
            'v' => 'V',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Регистрация пользователя
     *
     * @param $data
     * @return Users|null
     */
    public static function signUp($data)
    {
        $user = new Users();

        $user->uid = Yii::$app->security->generateRandomString();
        $user->username = $data['name'];
        $user->phone = $data['phone'];
        $user->email = $data['email'];
        $user->status = 1;
        $user->role = $data['admin'] ? 666 : 1;
        $user->password = md5($data['password']);
        $user->hash = Yii::$app->security->generatePasswordHash($data['email'] . $data['password']);
        $user->created_at = time();
        $user->updated_at = time();
        $user->generateAuthKey();

        if (!$user->validate()) {
            return null;
        }

        return $user->save() ? $user : null;
    }

    /**
     * Редактирование данных
     *
     * @param $data
     * @return bool
     */
    public static function updateUserData($data)
    {
        $model = Users::findOne($data['id']);
        $model->username = $data['name'];
        $model->email = $data['email'];
        $model->phone = $data['phone'];

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Полный список пользователей
     *
     * @return array
     */
    public static function getUsersList()
    {
        /* @var $result Users */
        $result = Users::find()->where(['IN', 'role', [1, 666]])->all();

        $users = [];

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

        return $users;
    }

    /**
     * Создание клиента
     *
     * @param $data
     * @return Users|null
     */
    public static function createCustomer($data)
    {
        $symbol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuwxyz0123456789';

        $model = new Users();
        $model->uid = substr(str_shuffle(str_repeat($symbol, 8)), 0, 10);
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
        $model->password = md5(substr(str_shuffle(str_repeat($symbol, 8)), 0, 10));
        $model->status = 1;
        $model->role = 0;
        $model->image = Users::uploadPhoto();

        $model->hash = Yii::$app->security->generatePasswordHash($data['email'] . $model->password);
        $model->created_at = time();
        $model->updated_at = time();
        $model->generateAuthKey();

        if (!$model->validate()) {
            return null;
        }

        return $model->save() ? $model : null;
    }

    /**
     * Обновление клиента
     *
     * @param $data
     * @return bool
     */
    public static function updateCustomer($data)
    {
        $model = Users::findOne($data['id']);
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
        if (Users::uploadPhoto()) {
            $model->image = Users::uploadPhoto();
        }

        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение списка клиентов
     *
     * @return array
     */
    public static function getListCustomers()
    {
        /* @var $result Users */
        $result = Users::find()->where(['role' => 0])->all();

        $customers = [];

        foreach ($result as $obj) {
            $customers[] = [
                'id' => $obj->id,
                'name' => $obj->username,
                'phone' => $obj->phone,
                'imei' => $obj->imei,
                'identity' => $obj->uid,
                'location' => $obj->address,
                'status' => $obj->status,
                'image' => $obj->image ? Url::to('/images/small/' . $obj->image) : Url::to('/img/no-photo.png'),
            ];
        }

        return $customers;
    }

    /**
     * Авторизация
     *
     * @return bool
     */
    public function login()
    {
        $user = self::findByEmail($this->email);

        if ($this->validatePassword() && in_array($user->role, [666, 1])) {
            Yii::$app->user->login($user, 3600 * 24 * 30);

            return true;
        } else {
            return false;
        }

    }

    /**
     * Сброс пароля
     *
     * @param $email
     * @return bool|string
     */
    public static function resetPassword($email)
    {
        $result = self::findByEmail($email);
        if (!$result) {
            return false;
        }

        $symbol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuwxyz0123456789';
        $password = substr(str_shuffle(str_repeat($symbol, 8)), 0, 10);

        $result->password = md5($password);
        $result->hash = Yii::$app->security->generatePasswordHash($result->email . $password);
        $result->updated_at = time();
        $result->generateAuthKey();

        if ($result->validate()) {
            $result->save();
            Yii::$app->mailer->compose()
                ->setFrom('kuchura.d.wezom@domain.com')
                ->setTo($email)
                ->setSubject('Смена пароля')
                ->setHtmlBody('Ваш новый пароль: <b>' . $password . '</b>')
                ->send();

            return $password;
        } else {
            return false;
        }

    }

    /**
     * Загрузка и Crop фото
     *
     * @return bool
     */
    public static function uploadPhoto()
    {
        $file = UploadedFile::getInstanceByName('photo');

        if (!isset($file)) {
            return null;
        }

        $config = Yii::$app->params['photo'];

        $filename = md5($file->name . '_' . time()) . '.' . $file->extension;

        foreach ($config as $one) {
            $path = 'images/' . $one['path'];
            $name = 'images/' . $one['path'] . '/' . $filename;
            File::createFolder($path);

            if ($one['resize']) {
                $image = new Image();
                $image->load($file->tempName);
                $image->resizeToWidth($one['width']);
                $image->save($name);
            } else {
                $image = new Image();
                $image->load($file->tempName);
                $image->save($name);
            }
        }

        return $filename;
    }
}
