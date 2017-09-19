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
                'group' => Cars::getCarName($obj->car_id),
                'id' => $obj->id,
                'status' => $obj->status,
                'longitude' => $obj->longitude,
                'latitude' => $obj->latitude,
                'name' => $obj->user->username,
                'photo' => $obj->user->image ? Url::to('/images/small/' . $obj->user->image) : Url::to('/img/no-photo.png'),
                'location' => $obj->latitude != null ? self::getAddressAPI($obj->latitude, $obj->longitude) : '',
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
                'location' => $obj->latitude != null ?  self::getAddressAPI($obj->latitude, $obj->longitude) : '',
                'name' => $obj->name,
                'status' => $obj->status,
                'type' => 'group',
            ];
        }

        return $cars ? $cars : null;
    }

    /**
     * Получение улицы от GOOGLE API
     *
     * @param $lat
     * @param $lng
     * @return null
     */
    public static function getAddressAPI($lat, $lng)
    {
        if ($lat != null && $lng != null) {
            $url = 'http://maps.googleapis.com/maps/api/geocode/json?language=ru&latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false';
            $json = @file_get_contents($url);
            $data = json_decode($json);
            $status = $data->status;
            if ($data) {
                if ($status == 'OK') {
                    return $data->results[0]->formatted_address;
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }
    }
}