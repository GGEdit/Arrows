<?php

namespace App\Consts;

class MessageType
{
    const DEFAULT = 1;
    const SYSTEM_MESSAGE = 2;

    const NAMES = [
        self::DEFAULT => 'デフォルト',
        self::SYSTEM_MESSAGE => 'システムメッセージ',
    ];
}