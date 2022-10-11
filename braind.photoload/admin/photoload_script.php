<?

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_before.php");


use Bitrix\Main\Localization\Loc;

use Bitrix\Main\Config\Option;



Loc::loadMessages(__FILE__);

$module_id = pathinfo(dirname(__DIR__))["basename"];
$iblock_id = Option::get($module_id, "iblock_id", 1);

$userRights = $APPLICATION->GetUserRight($module_id);

if ($userRights <= "D") $APPLICATION->AuthForm(Loc::getMessage("BPL_MODULE_ACCESS_DENIED"));
if (($userRights >= "W") || ($userRights == "MS")):

    require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_after.php");
    //require_once($_SERVER["DOCUMENT_ROOT"] . "путь к скрипту");

    if ($_SERVER["REQUEST_METHOD"] == 'GET') {
        $section_id = $_GET["section_id"]; //ID раздела (альбома)
    }

    //$directory = $_SERVER["DOCUMENT_ROOT"].'/upload/import-photos';
    $directory = $_SERVER["DOCUMENT_ROOT"]. Option::get($module_id, "import_folder", "/upload/import-photos");



    $photos = array_diff(scandir($directory), array('..', '.'));

    if (count($photos) > 0):
?>
        <div class="adm-info-message-wrap adm-info-message-gray">
            <div class="adm-info-message">
<?
        CModule::IncludeModule("iblock");

        $i = 0;

        foreach ($photos as $photo)
        {
            $photoName = substr($photo,0,strpos($photo,'.'));

            $el = new CIBlockElement;
            $arFields = array(
                "IBLOCK_ID" => $iblock_id,
                "IBLOCK_SECTION_ID" => $section_id,
                "NAME" => $photoName,
                "ACTIVE" => "Y",
                "ACTIVE_FROM" => ConvertTimeStamp(time(), 'FULL'),
                "DETAIL_PICTURE" => CFile::MakeFileArray($directory.'/'.$photo),
            );

            //pre($arFields);

            if($NEW_ID = $el->Add($arFields,false,true,true))
            {

                echo Loc::getMessage("BPL_SCRIPT_PHOTO_ADDED") . $NEW_ID . ' - ' . $arFields["NAME"] . '<br>';
                $i++;

                if (Option::get($module_id, "delete_after", "N") == "Y") {
                    unlink($directory.'/'.$photo);
                    echo '<br><p style="color:lightcoral;">' . $photo . Loc::getMessage("BPL_SCRIPT_FILE_DELETED") . Option::get($module_id, "import_folder", "/upload/import-photos"). '</p>';
                }

            }
            else
            {
                echo "Error: " . $el->LAST_ERROR;
            }
        }
    ?>
            </div>
        </div>
        <div class="adm-info-message-wrap adm-info-message-green">

            <div class="adm-info-message">

                <div class="adm-info-message-title"><?=$i . Loc::getMessage("BPL_SCRIPT_SUCCESS_COUNT")?></div>

                <div class="adm-info-message-icon"></div>

            </div>

        </div>

        <div class="adm-detail-toolbar">
            <span style="position:absolute;"></span>
            <a href="/bitrix/admin/iblock_element_admin.php?IBLOCK_ID=<?=$iblock_id?>&type=gallerys&find_section_section=<?=htmlspecialcharsbx($section_id);?>" class="adm-detail-toolbar-btn" title="<?=Loc::getMessage("BPL_SCRIPT_LINK_BACK_TO_PHOTOS")?>" id="btn_list"><span class="adm-detail-toolbar-btn-l"></span><span class="adm-detail-toolbar-btn-text"><?=Loc::getMessage("BPL_SCRIPT_LINK_BACK_TO_PHOTOS")?></span><span class="adm-detail-toolbar-btn-r"></span></a>
        </div>

    <?
    else:?>
        <div class="adm-info-message-wrap adm-info-message-red">

            <div class="adm-info-message">

                <div class="adm-info-message-title">ОШИБКА:</div>

                В директории <b><?=Option::get($module_id, "import_folder", "/upload/import-photos")?></b> нет файлов.

                <div class="adm-info-message-icon"></div>

            </div>

        </div>

        <div class="adm-detail-toolbar">
            <span style="position:absolute;"></span>
            <a href="/bitrix/admin/iblock_section_admin.php?IBLOCK_ID=<?=$iblock_id?>&type=gallerys&find_section_section=<?=htmlspecialcharsbx($section_id);?>&SECTION_ID=<?=htmlspecialcharsbx($section_id);?>" class="adm-detail-toolbar-btn" title="<?=Loc::getMessage("BPL_SCRIPT_LINK_BACK_TO_ALBUM")?>" id="btn_list"><span class="adm-detail-toolbar-btn-l"></span><span class="adm-detail-toolbar-btn-text"><?=Loc::getMessage("BPL_SCRIPT_LINK_BACK_TO_ALBUM")?></span><span class="adm-detail-toolbar-btn-r"></span></a>
        </div>

    <?endif; //count($photos)?>
<?


    require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
else:
    $APPLICATION->AuthForm(Loc::getMessage("BPL_MODULE_ACCESS_DENIED"));
endif;