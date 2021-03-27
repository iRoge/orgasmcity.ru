<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class Hello extends CBitrixComponent{

	public function executeComponent(){

		/*$request = Context::getCurrent()->getRequest();
	
		if(!$request->isAjaxRequest()){
	
			$server = Context::getCurrent()->getServer();
	
			if(strpos($server->getRequestUri(), "PAGEN_") !== false){
	
				LocalRedirect("/404.php", "404 Not Found");
			}
		}
	
$this->initEvents();*/
	
		$this->includeComponentTemplate();
	
		return false;
	}
}

?>