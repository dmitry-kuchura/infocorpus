<?php

namespace app\components;

use yii\base\Component;

class Post extends Component
{

    public function getRaw($value = null)
    {
        $array = json_decode(file_get_contents('php://input'), true);

        if ($value) {
            return $array[$value];
        } else {
            return $array;
        }
    }

    public function checkRaw($value = null)
    {
        $array = json_decode(file_get_contents('php://input'), true);

        if (isset($array[$value])) {
            return true;
        } else {
            return false;
        }
    }
}