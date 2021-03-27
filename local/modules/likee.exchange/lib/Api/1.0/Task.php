<?php

namespace Likee\Exchange\Api\v10;

use Bitrix\Main\Context;

abstract class Task
{
    public function getParams()
    {
        $request = Context::getCurrent()->getRequest();

        return [
            'FORMAT' => $request->get('format') ?: 'xml',
            'SECURE' => $request->get('secure'),
        ];
    }
}