<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DC_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_DC" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DC_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"WEBSERVICE_URL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DC_WEBSERVICE_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => 'http://92.255.198.206:35380/RTTest/ws/Upak_RF.1cws?wsdl',
		),
		"WEBSERVICE_LOGIN" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DC_WEBSERVICE_LOGIN"),
			"TYPE" => "STRING",
			"DEFAULT" => 'Web_Discount',
		),
		"WEBSERVICE_PASSWORD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("DC_WEBSERVICE_PASSWORD"),
			"TYPE" => "STRING",
			"DEFAULT" => 'bsdjkw3245dxsjq',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000000),
	),
);
?>
