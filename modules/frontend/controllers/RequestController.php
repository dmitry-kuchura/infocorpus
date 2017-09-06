<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\Recall;

/**
 * Class RequestController
 *
 * Содержит все action'sы которые отсносятся к редактированию пользователей
 *
 * @package app\modules\frontend\controllers
 */
class RequestController extends BaseController
{
    /**
     * Список запросов на перезвон
     *
     * @return array
     */
    public function actionRequestList()
    {
        if (Yii::$app->post->getRaw('id')) {
            /* @var $result Recall */
            $result = Recall::find()->all();

            foreach ($result as $obj) {
                $recall[] = [
                    'id' => $obj->id,
                ];
            }

            if (count($result)) {
                return [
                    'success' => true,
                    'data' => $result,
                ];
            } else {
                return [
                    'success' => false,
                ];
            }
        }
    }
}