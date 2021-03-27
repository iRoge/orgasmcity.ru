<?php
IncludeModuleLangFile(__FILE__);
Class likee_admin extends CModule
{
    var $MODULE_ID = "likee.admin";

    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    var $MODULE_GROUP_RIGHTS = "Y";

    function LIKEE_ADMIN() {
        $arModuleVersion = array();

        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("LIKEE_ADMIN_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("LIKEE_ADMIN_MODULE_DESCRIPTION");
    }

    function DoInstall() {
        RegisterModule($this->MODULE_ID);
        CopyDirFiles(__DIR__ . "/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
    }

    function DoUninstall() {
        //launch upgrade when reinstalled module
        DeleteDirFiles(__DIR__ . "/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
        UnRegisterModule($this->MODULE_ID);
    }

    function GetModuleRightList(){
        $arr = array(
            "reference_id" => array("D","W"),
            "reference" => array(
                "[D] ".GetMessage("LIKEE_ADMIN_RIGHT_D"),
                "[W] ".GetMessage("LIKEE_ADMIN_RIGHT_W"))
        );
        return $arr;
    }
}
