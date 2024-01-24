<?php

namespace App\Consts;

class RoomType
{
    const MY_MESSAGE = 1;
    const DIRECT_MESSAGE = 2;
    const GROUP_MESSAGE = 3;

    const NAMES = [
        self::MY_MESSAGE => 'マイルーム',
        self::DIRECT_MESSAGE => 'ダイレクトメッセージ',
        self::GROUP_MESSAGE => 'グループ',
    ];
}