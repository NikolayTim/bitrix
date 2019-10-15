<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
ini_set("soap.wsdl_cache_enabled", "0");

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("DC_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["IBLOCK_DC"] = intval($arParams["IBLOCK_DC"]);
if(!isset($arParams["WEBSERVICE_URL"]) || strlen($arParams["WEBSERVICE_URL"]) <= 0)
{
	ShowError(GetMessage("DC_WEBSERVICE_URL_DEFINED"));
	return;
}

if($USER->IsAuthorized() && $this->StartResultCache(false, $USER->GetID()))
{
	// Выбираем все карты текущего пользователя
	$arResult["CARDS"] = Array();
	$arSelect = array("ID", "NAME", "PROPERTY_USER");
	$arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_DC"],
			  "ACTIVE_DATE" => "Y",
			  "ACTIVE"=>"Y",
			  "PROPERTY_USER" => $USER->GetID()
			 );
	$dbCards = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while($arCard = $dbCards->GetNext())
	{
		$arResult["CARDS"][$arCard["ID"]]["NUMBER"] = $arCard["NAME"];
	}

	if(count($arResult["CARDS"]) > 0)
	{

		// Подключаем веб-сервис
		$optionalConnection = Array();
		if(strlen($arParams["WEBSERVICE_LOGIN"]) > 0)
		{
			$optionalConnection['login'] = $arParams["WEBSERVICE_LOGIN"];
			$optionalConnection['password'] = $arParams["WEBSERVICE_PASSWORD"];
		}
		$optionalConnection['trace'] = 1;
		$client = new SoapClient($arParams["WEBSERVICE_URL"], $optionalConnection);

		// Получаем бонусы по каждой карте клиента
		foreach($arResult["CARDS"] as $keyDC => $numDC)
		{
			$params = Array();
			$params["NumberDC"] = $numDC["NUMBER"];
			$result = $client->GetBonus($params);
			$arResult["CARDS"][$keyDC]["BONUSES"] = $result->return;
		}
		$this->SetResultCacheKeys(array());
	}
	else
	{
		$this->AbortResultCache();
	}
	$this->IncludeComponentTemplate();
}
?>
