<?
class likee_site extends CModule
{
    const MODULE_ID = 'likee.site';

    public $MODULE_ID = 'likee.site',
        $MODULE_VERSION,
        $MODULE_VERSION_DATE,
        $MODULE_NAME = 'Cайт respect',
        $PARTNER_NAME = 'Likee',
        $PARTNER_URI = 'http://likee.ru';

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . 'version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    function InstallFiles($arParams = array())
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
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);

        $this->UnInstallFiles();
    }
}