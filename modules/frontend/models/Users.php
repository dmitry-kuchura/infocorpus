<?php

namespace app\modules\frontend\models;

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
            [['client_id', 'uid', 'email', 'username', 'password', 'first_name', 'last_name', 'short_name'], 'required'],
            [['client_id', 'v', 'status', 'created_at', 'updated_at', 'role'], 'integer'],
            [['longitude', 'latitude'], 'number'],
            [['uid', 'username'], 'string', 'max' => 50],
            [['email', 'password', 'first_name', 'last_name', 'short_name', 'token', 'password_hash', 'auth_key', 'hash'], 'string', 'max' => 150],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'uid' => 'Uid',
            'email' => 'Email',
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
        $user->uid = $this->uid;
        $user->username = $this->username;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->short_name = $this->short_name;
        $user->client_id = $this->client_id;
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
        $user = $this->getUser();

        if ($this->validatePassword()) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
            
            return Yii::$app->user->identity->auth_key;
        }

    }
}
