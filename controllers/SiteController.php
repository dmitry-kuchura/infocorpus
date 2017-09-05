<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * Отображение главной страницы
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Отображение страницы 404 в формате JSON
     *
     * @return array
     */
    public function actionError()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'success' => false,
            'error' => [
                'status' => 404,
                'message' => 'Page not found!'
            ]
        ];
    }
}
