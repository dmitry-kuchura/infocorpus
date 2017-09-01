<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * Displays main page.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Display error page
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
