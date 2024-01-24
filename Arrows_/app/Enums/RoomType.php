<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RoomType extends Enum
{
    const MY_MESSAGE = 1;
    const DIRECT_MESSAGE = 2;
    const GROUP_MESSAGE = 3;
}