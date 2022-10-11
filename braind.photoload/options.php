<?php
use Bitrix\Main\Localization\Loc;

use Bitrix\Main\HttpApplication;

use Bitrix\Main\Loader;

use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

$RIGHT = $APPLICATION->GetGroupRight($module_id);

if($RIGHT >= "R") :

    Loader::includeModule($module_id);

    Loader::includeModule("iblock");


    $optionsSelect = [];

    $dbResult = CIBlock::GetList();

    while ($arItem = $dbResult->GetNext()) {

        $optionsSelect[$arItem["ID"]] = $arItem["NAME"];

    }

    $sectionsSelect = [];

    $dbResult = CIBlockSection::GetList([],["IBLOCK_ID" => Option::get($module_id, "iblock_id_goods")],false,['ID', 'NAME']);

    while ($arItem = $dbResult->GetNext())  {

        $sectionsSelect[$arItem['ID']] = $arItem['NAME'];

    }

    $optionsEdit1 = [

        Loc::getMessage("BPL_OPTIONS_TAB_EDIT_IBLOCK_NAME"),

        [
            "iblock_id",
            Loc::getMessage("BPL_OPTIONS_GALLERY_IBLOCK_ID_NAME"),
            1,
            ["selectbox", $optionsSelect]
        ],
/*
        [
            "iblock_id_sect",
            Loc::getMessage("BPL_OPTIONS_GALLERY_SECTION_ID_NAME"),
            1,
            ["selectbox", $sectionsSelect]
        ],
*/
        [
            "import_folder",
            Loc::getMessage("BPL_OPTIONS_IMPORT_FOLDER"),
            "/upload/import-photos/",
            [text,20]
        ],

        [
            "delete_after",
            Loc::getMessage("BPL_OPTIONS_DELETE_AFTER"),
            "N",
            [checkbox]
        ]

    ];

    $arTabs = [

        [
            "DIV" => "edit1",
            "TAB" => Loc::getMessage("BPL_OPTIONS_TAB_SHORT_NAME"),
            "TITLE" => Loc::getMessage("BPL_OPTIONS_TAB_NAME"),
            "OPTIONS" => $optionsEdit1
        ]
    ];

    $tabControl = new CAdminTabControl(
        "tabControl",
        $arTabs
    );


//Обработчик формы параметров модуля
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($_REQUEST['save']) > 0 && check_bitrix_sessid())

    {

        foreach ($arTabs as $arTab)

        {

            if (isset($arTab["OPTIONS"]))

                __AdmSettingsSaveOptions($module_id, $arTab['OPTIONS']);

        }

        ob_start();

        $Update = $_REQUEST['save'];

        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');

        ob_end_clean();



        LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid_menu=1&mid=' . urlencode($module_id) .

            '&tabControl_active_tab=' . urlencode($_REQUEST['tabControl_active_tab']));

    }

    ?>

    <form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

        <? $tabControl->Begin();

        foreach($arTabs as $arTab)
        {

            if ($arTab["OPTIONS"]) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($module_id, $arTab["OPTIONS"]);
            }

        }

        $tabControl->Buttons(['btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false]);

        ?>

        <?= bitrix_sessid_post(); ?>

        <? $tabControl->End(); ?>

    </form>

<?endif;?>