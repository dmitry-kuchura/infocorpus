<?php

namespace app\modules\frontend\models;


use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class UserInterface extends ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'users';
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findByUid($uid)
    {
        return static::findOne(['uid' => $uid]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return static::findOne(['username' => $this->username]);
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function setPassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function validatePassword()
    {
        $data = static::findOne(['username' => $this->username]);

        if ($data->password === md5($this->password)) {
            return true;
        }
    }
}