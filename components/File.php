<?php

namespace app\components;

class File
{
    /**
     * Создание папки если таковой нет
     *
     * @param $path
     * @param int $mode
     * @return bool
     */
    public static function createFolder($path, $mode = 0777)
    {
        if (is_dir($path)) {
            return true;
        }
        if (!mkdir($path, $mode, true)) {
            return false;
        }
        @chmod($path, $mode);
        return true;
    }
}