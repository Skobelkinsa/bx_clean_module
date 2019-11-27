<?php
/**
 * Created by PhpStorm.
 * User: semen
 * Date: 26.11.19
 * Time: 15:13
 */
use Bitrix\Main\Localization\Loc;
use    Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);
Loader::includeModule('iblock');

$arSites = array();
$dbItems = Bitrix\Main\SiteTable::getList(array(
    'order' => array('SORT' => 'DESC'),
    'select' => array('LID', 'NAME'),
    'filter' => array(),
));
while ($arSite = $dbItems->fetch())
{
    $arSites[$arSite["LID"]] = $arSite["NAME"];
}

$arIBlocks = array();
$dbItems = Bitrix\Iblock\IblockTable::getList(array(
    'order' => array('SORT' => 'DESC'),
    'select' => array('ID', 'NAME'),
    'filter' => array(),
));
while ($arIB = $dbItems->fetch())
{
    $arIBlocks[$arIB["ID"]] = $arIB["NAME"];
}

$aTabs = array(
    array(
        "DIV"       => "edit",
        "TAB"       => Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_COMMON"),
            array(
                "iblock",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_IBLOCK"),
                "left",
                array("selectbox", $arIBlocks)
            ),
            array(
                "site_of",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_SITE_DONER"),
                "left",
                array("selectbox", $arSites)
            ),
            array(
                "site_in",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_SITE"),
                "left",
                array("selectbox",$arSites)
            ),

            array(
                "switch_delete",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_DELETE"),
                "Y",
                array("checkbox")
            ),
            Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_REPLACE"),
            array(
                "from",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_REPLACE_FROM"),
                "",
                array("text", 50)
            ),
            array(
                "before",
                Loc::getMessage("UM_SITE_CONFIG_OPTIONS_TAB_REPLACE_BEFORE"),
                "",
                array("text", 50)
            )
        )
    )
);

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

$tabControl->Begin();
?>
    <form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

        <?
        foreach($aTabs as $aTab){

            if($aTab["OPTIONS"]){

                $tabControl->BeginNextTab();

                __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
            }
        }

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="<? echo(Loc::GetMessage("UM_SITE_CONFIG_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />

        <?
        echo(bitrix_sessid_post());
        ?>

    </form>
<?$tabControl->End();
