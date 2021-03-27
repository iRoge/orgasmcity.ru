<?php

use Likee\Exchange\Process;

class Likee1CExchangeComponent extends CBitrixComponent
{
    /** @var Process $process */
    public $process;

    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $arDefaultUrlTemplates404 = [
            'error' => ''
        ];

        $arUrlTemplates = CComponentEngine::makeComponentUrlTemplates(
            $arDefaultUrlTemplates404,
            $this->arParams['SEF_URL_TEMPLATES']
        );

        $engine = new CComponentEngine($this);
        $engine->guessComponentPath(
            $this->arParams['SEF_FOLDER'],
            $arUrlTemplates,
            $arVariables
        );
		ob_start();
		var_dump($arVariables);
		$output = ob_get_contents();
		ob_end_clean();
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/DNG_LOG_1.txt', $output);
		
        $this->process = new Process(
            $arVariables['CLASS'],
            $arVariables['VERSION'],
            $arVariables['METHOD']
        );

        $this->process->process();
        while (ob_get_level()) ob_end_clean();
        $this->includeComponentTemplate();
    }
}