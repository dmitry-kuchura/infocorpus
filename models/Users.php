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
 * @property string $phone
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $short_name
 * @property string $longitude
 * @property string $latitude
 * @property string $token
 * @property string $password_hash
 * @property string $auth_key
 * @property string $hash
 * @property integer $v
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Users extends UserInterface
{
    public function rules()
    {
        return [
            [['email', 'username', 'password',], 'required'],
            [['client_id', 'v', 'status', 'created_at', 'updated_at', 'role'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['email'], 'unique'],
            [['uid', 'username'], 'string', 'max' => 50],
            [['email', 'password', 'phone', 'first_name', 'last_name', 'short_name', 'token', 'password_hash', 'auth_key', 'hash'], 'string', 'max' => 150],
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
        if (!$this->validate()) {
            return null;
        }

        $user = new Users();
        $user->uid = Yii::$app->security->generateRandomString();
        $user->username = $this->username;
        $user->short_name = $this->short_name;
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->password = md5($this->password);
        $user->hash = Yii::$app->security->generatePasswordHash($this->email . $this->password);
        $user->created_at = time();
        $user->updated_at = time();
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }

    public function login()
    {
        $user = self::findByEmail($this->email);

        if ($this->validatePassword()) {
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
