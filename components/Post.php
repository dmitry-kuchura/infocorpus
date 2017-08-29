<?php

namespace app\components;

use yii\base\Component;

class Post extends Component
{
    public function getRaw()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}