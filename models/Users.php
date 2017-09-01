<?php

namespace app\models;

use Yii;

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
 * @property string $password_hash
 * @property string $auth_key
 * @property string $hash
 * @property integer $v
 * @property integer $status
 * @property integer $role
 * @property integer $created_at
 * @property integer $updated_at
 */
class Users extends UserInterface
{
    public function rules()
    {
        return [
            [['uid', 'password'], 'required'],
            [['v', 'status', 'role', 'created_at', 'updated_at'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['email'], 'unique'],
            [['imei'], 'string', 'max' => 16],
            [['uid', 'username', 'skype', 'car_name', 'car_color', 'car_number'], 'string', 'max' => 50],
            [['email', 'password', 'phone', 'address', 'organization', 'location', 'password_hash', 'auth_key', 'hash'], 'string', 'max' => 150],
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
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'hash' => 'Hash',
            'v' => 'V',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function signUp()
    {

        $user = new Users();
        $user->uid = Yii::$app->security->generateRandomString();
        $user->username = $this->username;
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->status = $this->status;
        $user->role = $this->role;
        $user->password = md5($this->password);
        $user->hash = Yii::$app->security->generatePasswordHash($this->email . $this->password);
        $user->created_at = time();
        $user->updated_at = time();
        $user->generateAuthKey();

        if (!$user->validate()) {
            return null;
        }

        return $user->save() ? $user : null;
    }

    public function createCustomer()
    {
        $model = new Users();
        $model->uid = Yii::$app->security->generateRandomString();
        $model->username = $this->username;
        $model->phone = $this->phone;
        $model->imei = $this->imei;
        $model->email = $this->email;
        $model->skype = $this->skype;
        $model->address = $this->address;
        $model->organization = $this->organization;
        $model->location = $this->location;
        $model->car_name = $this->car_name;
        $model->car_color= $this->car_color;
        $model->car_number = $this->car_number;
        $model->password = md5($this->password);
        $model->status = $this->status;
        $model->role = $this->role;

        $model->hash = Yii::$app->security->generatePasswordHash($this->email . $this->password);
        $model->created_at = time();
        $model->updated_at = time();
        $model->generateAuthKey();

        if (!$model->validate()) {
            return null;
        }

        return $model->save() ? $model : null;
    }

    public function login()
    {
        $user = self::findByEmail($this->email);

        if ($this->validatePassword() && $user->role == 666) {
            Yii::$app->user->login($user, 3600 * 24 * 30);

            return true;
        } else {
            return false;
        }

    }

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
//            $result->save();
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
}
