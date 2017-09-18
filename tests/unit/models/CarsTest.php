<?php

namespace tests\models;

use Codeception\Test\Unit;
use app\models\Cars;
use Yii;

class CarsTest extends Unit
{
    public function testFindCarById()
    {
        expect_that($car = Cars::findOne(2));
        expect($car->name)->equals('Wezom');

        expect_not(Cars::findOne(999));
    }

    public function testFindUserByAccessToken()
    {
        expect_that($car = Cars::findByToken('jai5dgT5Jt9FAJzOo9TXRgmhgVlrNSx1'));
        expect($car->name)->equals('Wezom');

        expect_not(Cars::findByToken('non-existing'));
    }

    public function testCreateGroup()
    {
        $model = new Cars();
        $model->name = 'UnitTest-001';
        $model->created_at = time();
        $model->updated_at = time();
        $model->token = Yii::$app->security->generateRandomString();
        $model->aid = Yii::$app->security->generateRandomString();

//        expect($model->save())->equals(true);

//        expect_not(!$model->save());
    }
}
