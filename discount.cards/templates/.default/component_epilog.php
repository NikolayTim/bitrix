<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
CJSCore::Init(array("jquery"));
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

// Проверка карты (AddDC)
if(array_key_exists("NumberDC", $_GET) && array_key_exists("Phone", $_GET) && 
			strlen($_GET["NumberDC"]) > 0 && strlen($_GET["Phone"]) > 0)
{	
	$numberCard = $_GET["NumberDC"];
	$phone = $_GET["Phone"];

	AddMessage2Log("numberCard = ".$numberCard." phone = ".$phone);
	
	$APPLICATION->RestartBuffer();

	// Проверяем в инфоблоке, нет ли там уже этой карты
	$arSel = Array("ID", "NAME");
	$arFil = Array("IBLOCK_ID" => $arParams["IBLOCK_DC"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "NAME" => $numberCard);
	$dbRs = CIBlockElement::GetList(Array(), $arFil, false, false, $arSel);
	if($arRs = $dbRs->GetNext())
	{	
		echo "#CARD#Карта: ".$numberCard." уже есть в базе!!!";
		die();
	}
	
	$optionalConnection = Array();
	if(strlen($arParams["WEBSERVICE_LOGIN"]) > 0)
	{
		$optionalConnection['login'] = $arParams["WEBSERVICE_LOGIN"];
		$optionalConnection['password'] = $arParams["WEBSERVICE_PASSWORD"];
	}
	$optionalConnection['trace'] = 1;
	$client = new SoapClient($arParams["WEBSERVICE_URL"], $optionalConnection);

	$params = Array();
	$params["DataAdd"] = array("NumberDC" => $numberCard, "Phone" => $phone);
	$result = $client->AddDC($params);
	$resCheckCard = $result->return;
	
	// Анкета заполнена на кассе, просто добавляем карту в инфоблок
	if(strval($resCheckCard) === "0")
	{
		$el = new CIBlockElement;
		$arProp = array();
		$arProp[34] = $USER->GetID();	
		$arLoadProductArray = Array("MODIFIED_BY" => $USER->GetID(),
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $arParams["IBLOCK_DC"],
			"PROPERTY_VALUES" => $arProp,
			"NAME" => $numberCard,
			"ACTIVE" => "Y",
		);
		if ($idCard = $el->Add($arLoadProductArray))
			echo "#CARD#Карта: " . $numberCard . " успешно добавлена! resCheckCard = ".$resCheckCard;
		else
			echo "#CARD#Ошибка: " . $el->LAST_ERROR . " при добавлении карты: " . $numberCard . "! resCheckCard = ".$resCheckCard;
	}
	elseif(strval($resCheckCard) === "1") // Требуется заполнение анкеты
	{
/*		
		echo '#ANKETA#<table><thead><tr><td colspan="2"><b>Анкета</b></td></tr></thead><tbody>';
		echo '<tr><td><b>Фамилия Имя Отчество:</b></td><td><input type="text" size="100" id="fio" value="'.$USER->GetFullName().'"></td></tr>';
		echo '<tr><td><b>Пол:</b></td><td><select><option>Мужской</option><option>Женский</option></select></td></tr>';
		echo '<tr><td><b>Дата рождения:</b></td><td><input type="date" size="100" id="birthdate"></td></tr>';
		echo '<tr><td><b>E-mail:</b></td><td><input type="email" size="100" id="email" value="'.$USER->GetEmail().'"></td></tr>';
		echo '<tr><td><b>Номер телефона:</b></td><td><input type="tel" size="100" id="phone" pattern="+7[0-9]{3}-[0-9]{2}-[0-9]{2}" value="+7"></td></tr></tbody></table>';
		echo '<input type="checkbox" id="spam" checked> <b>Я хочу получать информацию о скидках и акциях</b><br>';
		echo '<input type="checkbox" name="agree" id="agreement" checked> <b>Я согласен с условиями программы лояльности и условиями политики конфиденциальности</b>';

		echo '<p><input type="button" class="btn btn-primary" value="Отправить" onclick="SubmitAnketa()"></p><br>';		
*/		
		echo '#CARD#resCheckCard = '.$resCheckCard;
	}
	else // Возникла ошибка при вызове AddDC
	{
		echo "#CARD#Ошибка при проверке карты: ".$resCheckCard;
	}
	die();
}

// Запрос бонусов по карте
elseif(array_key_exists("NumberDC", $_GET) && strlen($_GET["NumberDC"]) > 0)
{	
	$numberCard = $_GET["NumberDC"];

	$optionalConnection = Array();
	if(strlen($arParams["WEBSERVICE_LOGIN"]) > 0)
	{
		$optionalConnection['login'] = $arParams["WEBSERVICE_LOGIN"];
		$optionalConnection['password'] = $arParams["WEBSERVICE_PASSWORD"];
	}
	$optionalConnection['trace'] = 1;
	$client = new SoapClient($arParams["WEBSERVICE_URL"], $optionalConnection);

	$params = Array();
	$params["NumberDC"] = $numberCard;
	$result = $client->GetBonus($params);
	$APPLICATION->RestartBuffer();
	echo $result->return;
	die();
}

?>
<script>
function SubmitAnketa()
{
    if($('#agreement').is(':checked') == false)
    {
        alert("Необходимо согласие с условиями программы лояльности и условиями политики конфиденциальности!");
        return false;
    }
    alert("Все ОК!");
}

function ViewCardBonus(data)
{
    alert(data);
}

function ViewCheckCardResult(data){
//    alert(data);
    if(data.indexOf("#CARD#") !== -1)
        $("#card").html(data.substr(6));
    else
        $("#anketa").html(data.substr(8));
}

function checkCard() {
//	alert("check_card");
    var numCard = $("#numcard").val();
    if(numCard == "undefined" || numCard == "")
    {
        alert("Введите штрихкод карты!");
        return false;
    }
/*	
    if(numCard.length !== 10)
    {
        alert("Некорректный штрихкод карты! Должно быть 10 символов!");
        return false;
    }
*/
    $.get(
        "<?=$APPLICATION->GetCurPage(false);?>",
        {'NumberDC': numCard, 'Phone': "9172320914"},
        ViewCheckCardResult
    );
}

$('#add_card').on('click', function() {
    var strHtml = '<p><b>Введите штрихкод карты:</b></p>' +
                  '<input type="text" size="10" id="numcard" value="6700000000515"> ' +
                  '<input type="button" class="btn btn-primary" value="Далее" onclick="checkCard()">';
//    alert(strHtml);
    $("#card").html(strHtml);
});

$('#view_bonus').on('click', function() {
//    alert("view_bonus");
    $.get(
        "<?=$APPLICATION->GetCurPage(false);?>",
        {NumberDC: "6700000000515"},
        ViewCardBonus
    );
});

</script>