<?
use Bitrix\Main\EventManager;

class likee_exchange extends \CModule
{
    const MODULE_ID = 'likee.site';

    public $MODULE_ID = 'likee.exchange',
        $MODULE_VERSION,
        $MODULE_VERSION_DATE,
        $MODULE_NAME = 'Обмен с 1С',
        $PARTNER_NAME = 'Likee',
        $PARTNER_URI = 'http://likee.ru';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . 'version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = [])
    {
        return true;
    }

    function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallDB();
        $this->InstallEvents();
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();
    }

    public function InstallDB()
    {
        global $DB;
    $DB->Query('CREATE TABLE IF NOT EXISTS `b_likee_1c_order_queue` (
	`ID` INT NOT NULL AUTO_INCREMENT,
	  `ORDER_ID` INT NULL,
	  `STATUS` VARCHAR(2) NULL,
	  `ATTEMPTS` INT NULL,
	  `DATE_INSERT` DATETIME NULL,
	  `DATE_ATTEMPT` DATETIME NULL,
	  PRIMARY KEY (`ID`));');
    $DB->Query('CREATE TABLE IF NOT EXISTS `b_likee_1c_order_queue_error` (
	  `ID` INT NOT NULL AUTO_INCREMENT,
	  `QUEUE_ID` INT NULL,
	  `QUERY` TEXT NULL,
	  `ANSWER` TEXT NULL,
	  `DATE` DATETIME NULL,
	  PRIMARY KEY (`ID`));
	');
	$DB->Query('CREATE TABLE IF NOT EXISTS `b_likee_items_reserve_storage` (
	  `ID` INT NOT NULL AUTO_INCREMENT,
	  `PRODUCT_ID` INT NULL,
	  `STORAGE_ID` INT NULL,
	  `ORDER_ID` INT NULL,	  
	  `QUANTITY` FLOAT NULL,
	  `STATUS` VARCHAR(2) NULL,
	  PRIMARY KEY (`ID`));
	');
        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }


    function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'sale',
            'OnSaleOrderSaved',
            $this->MODULE_ID,
            'Likee\Exchange\Events',
            'orderSaveHandler'
        );
        return true;
    }

    function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'sale',
            'OnSaleOrderSaved',
            $this->MODULE_ID,
            'Likee\Exchange\Events',
            'orderSaveHandler'
        );
        return true;
    }
}
