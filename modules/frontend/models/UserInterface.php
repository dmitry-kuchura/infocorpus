<?php

namespace app\modules\frontend\models;


use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class UserInterface extends ActiveRecord implements IdentityInterface
{

    /**
     * @inheritdoc
     */
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
        return static::findOne(['token' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
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
        /* @var $user Users */
        $user = static::findOne(['username' => $this->username]);

        if ($this->setPassword($this->password) == $user->password) {
            return Yii::$app->user->login($user, 3600*24*30);
        }
    }
}