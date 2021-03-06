<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property integer $car_id
 * @property string $text
 * @property integer $readed
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Cars $car
 */
class Messages extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'text'], 'required'],
            [['car_id', 'readed', 'created_at', 'updated_at'], 'integer'],
            [['text'], 'string'],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cars::className(), 'targetAttribute' => ['car_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'car_id' => 'Car ID',
            'text' => 'Text',
            'readed' => 'Readed',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Поиск машины
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Cars::className(), ['id' => 'car_id']);
    }

    /**
     * Создание сообщение группе
     *
     * @param $data
     * @return bool
     */
    public static function createMessage($data)
    {
        $model = new Messages();

        $model->car_id = $data['car-id'];
        $model->text = $data['text'];
        $model->readed = 0;
        $model->created_at = time();
        $model->updated_at = time();

        if ($model->save(false)) {
            return true;
        } else {
            return false;
        }
    }
}
