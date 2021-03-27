<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var Likee1CExchangeComponent $component */

foreach ((array)$component->process->getHeaders() as $header) {
    header($header);
}

echo $component->process->getResult();