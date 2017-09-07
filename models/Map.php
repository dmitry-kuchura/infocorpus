<?php

namespace app\models;

use yii\helpers\Url;

class Map
{
    /**
     * Получение списка тревог
     *
     * @return array|null
     */
    public static function getAlerts()
    {
        /* @var $tasksData Tasks */
        $tasksData = Tasks::find()->where(['!=', 'status', 0])->all();

        $task = [];

        foreach ($tasksData as $obj) {
            $task[] = [
                'id' => $obj->id,
                'status' => $obj->status,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'name' => $obj->user->username,
                'photo' => $obj->user->image ? Url::to('/images/small/' . $obj->image) : Url::to('/img/no-photo.png'),
                'location' => 'м. Херсон, вул. Артилерійська, 14',
                'phone' => $obj->user->phone,
                'type' => 'alert',
                'date' => date('d.m.Y в H:i', $obj->created_at),
            ];
        }

        return $task ? $task : null;
    }

    /**
     * Получение списка автомобилей
     *
     * @return array|null
     */
    public static function getGroups()
    {
        /* @var $carsData Cars */
        $carsData = Cars::find()->where(['available' => 1])->andWhere(['IN', 'status', [1, 2]])->all();

        $cars = [];

        foreach ($carsData as $obj) {
            $cars[] = [
                'id' => $obj->id,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'name' => $obj->name,
                'status' => $obj->status,
                'type' => 'group',
            ];
        }

        return $cars ? $cars : null;
    }
}