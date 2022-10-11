<?
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;

use Bitrix\Main\ModuleManager;

use Bitrix\Main\Config\Option;

use Bitrix\Main\EventManager;

use Bitrix\Main\Application;

use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

Class braind_photoload extends CModule
{
	var $MODULE_ID = "braind.photoload";

	var $MODULE_VERSION;

	var $MODULE_VERSION_DATE;

	var $MODULE_NAME;

	var $MODULE_DESCRIPTION;

	var $PARTNER_NAME;

	var $PARTNER_URI;

	public function braind_photoload()
	{

		$arModuleVersion = [];

		include_once(__DIR__.'/version.php');


		$this->MODULE_VERSION = $arModuleVersion["VERSION"];

		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = Loc::getMessage("BPL_MODULE_NAME");

		$this->MODULE_DESCRIPTION = Loc::getMessage("BPL_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = Loc::getMessage("BPL_PARTNER_NAME");

		$this->PARTNER_URI = Loc::getMessage("BPL_PARTNER_URI");

	}

	public function InstallEvents()

	{

		EventManager::getInstance()->registerEventHandlerCompatible(
			"main", "OnAdminContextMenuShow",
			$this->MODULE_ID, "\\Braind\\Photoload\\EventHandler", "PhotoImportAdminContextMenuShow",
			10000
		);

		return true;

	}



	public function UnInstallEvents()

	{

		EventManager::getInstance()->unRegisterEventHandler(
			"main", "OnAdminContextMenuShow",
			$this->MODULE_ID, "\\Braind\\Photoload\\EventHandler", "PhotoImportAdminContextMenuShow"
		);

		return true;

	}


/*
	public function InstallFiles()

	{

		//CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true, true);

		return true;

	}


	public function UnInstallFiles()

	{

		//DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/photoload_script.php");

		return true;

	}
*/

	public function UnInstallDB(){

		Option::delete($this->MODULE_ID);

		return false;
	}

	public function DoInstall()

	{

		global $APPLICATION;



		if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {

			ModuleManager::registerModule($this->MODULE_ID);

			$this->InstallDB();

			$this->InstallEvents();

			$this->InstallFiles();



			$APPLICATION->IncludeAdminFile(Loc::getMessage("BPL_INSTALL_TITLE")." \"".

				Loc::getMessage("BPL_MODULE_NAME")."\"", __DIR__."/step.php");

		}

		else {

			$APPLICATION->ThrowException(Loc::getMessage("BPL_INSTALL_ERROR_VERSION"));

		}

	}

	function DoUninstall()
	{

		global $APPLICATION;


		$this->UnInstallFiles();

		$this->UnInstallEvents();

		$this->UnInstallDB();

		ModuleManager::unRegisterModule($this->MODULE_ID);

		COption::RemoveOption($this->MODULE_ID);

		$APPLICATION->IncludeAdminFile(Loc::getMessage("BPL_UNINSTALL_TITLE")." \"".

			Loc::getMessage("BPL_MODULE_NAME")."\"", __DIR__."/unstep.php");

	}
}
?>
