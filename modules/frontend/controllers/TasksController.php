<?php

namespace app\modules\frontend\controllers;

use Yii;
use app\models\TasksHistory;

/**
 * Class TasksController
 *
 * Содержит все action'sы которые отсносятся к редактированию групп
 *
 * @package app\modules\frontend\controllers
 */
class TasksController extends BaseController
{
    /**
     * Список всех тревог
     *
     * @return array
     */
    public function actionTaskList()
    {
        $result = TasksHistory::getTasksList();

        if (count($result)) {
            return [
                'success' => true,
                'result' => $result,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }

    /**
     * Получение полной истории маршрута
     *
     * @return array
     */
    public function actionTaskHistory()
    {
        $history = TasksHistory::getFullHistory(Yii::$app->post->getRaw('id'));

        if (count($history)) {
            return [
                'success' => true,
                'history' => $history,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }
}