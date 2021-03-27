<?php

class ExchangeException extends Exception
{
    static $ERR_AUTHORIZE = 10;
    static $ERR_INCORRECT_STRUCTURE = 100;
    static $ERR_EMPTY_FIELD = 101;
    static $ERR_INCORRECT_LINK = 111;
    static $ERR_NO_ORDER = 112;
    static $ERR_ALREADY_WORK = 113;
}
