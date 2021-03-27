<?php

namespace Likee\Exchange;

use Bitrix\Main\Context;

class Request
{

    public $key = '123';
    public $secure, $format, $class, $version, $method;

    public function __construct($class, $version, $method)
    {
        $request = Context::getCurrent()->getRequest();

        $this->class = $class;
        $this->version = $version;
        $this->method = $method;
        $this->secure = $request->get('secure');
        $this->format = $request->get('format') ?: 'xml';
    }

    public function process()
    {
        if($this->access()) {

        }
    }

    /**
     * @return bool
     */
    protected function access()
    {
        $md5 = md5(date('Y.m.d') . $this->key);

        return $md5 == $this->secure;
    }
}