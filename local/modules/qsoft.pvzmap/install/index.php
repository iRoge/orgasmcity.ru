<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity\Base;
use Qsoft\Pvzmap\PVZTable;

IncludeModuleLangFile(__FILE__);

class qsoft_pvzmap extends CModule
{
    public $MODULE_ID = "qsoft.pvzmap";
    public $MODULE_NAME;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    private $TABLE_ENTITY = 'Qsoft\\Pvzmap\\PVZTable';

    public function __construct()
    {
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "14.02.2020";
        $this->MODULE_NAME = Loc::getMessage('QSOFT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('QSOFT_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'Qsoft';
        $this->PARTNER_URI = 'https://qsoft.ru/';
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallDB();
        return true;
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getConnection();

            if (!$connection->isTableExists(Base::getInstance($this->TABLE_ENTITY)->getDBTableName())
            ) {
                Base::getInstance($this->TABLE_ENTITY)->createDbTable();
            }

            PVZTable::add([
               'NAME' => 'CDEK',
               'CLASS_NAME' => 'CDEK',
               'ACTIVE' => 'Y'
            ]);

            PVZTable::add([
                'NAME' => 'IML',
                'CLASS_NAME' => 'IML',
                'ACTIVE' => 'Y'
            ]);
        }
        return false;
    }

    public function UnInstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            if (Application::getConnection()->isTableExists(Base::getInstance($this->TABLE_ENTITY)->getDBTableName())) {
                Application::getConnection()->dropTable(Base::getInstance($this->TABLE_ENTITY)->getDBTableName());
            }
        }
        return false;
    }
}
