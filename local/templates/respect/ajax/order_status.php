<? if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die();
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("iblock")) {
    $this->AbortResultCache();
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}
if (!CModule::IncludeModule("catalog")) {
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

//Проверяем, если в запросе пришел номер заказа и капча
if($_REQUEST['order_number'] && $_REQUEST['captcha_word'] && $_REQUEST['order_phone']){
    unset($strErrorMessage);

//  Работаю с капчей
    $captcha_code = $_POST["captcha_code"];
    $captcha_word = $_POST["captcha_word"];
    $cpt = new CCaptcha();
    if (strlen($captcha_code) > 0)
    {
        $captchaPass = COption::GetOptionString("main", "captcha_password", "");
        if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
            $strErrorMessage .= "Код с картинки введен с ошибкой";
    }
    else
    {
        if (!$cpt->CheckCode($captcha_word, $captcha_sid))
            $strErrorMessage .= "Введите код с картинки";
    }

        // Если капчу заполнили правильно, то выполняю запросы к БД
    if(!$strErrorMessage){
        //    Получаю все статусы из админки
        $statusResult = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
            'order' => array('STATUS.SORT'=>'ASC'),
            'filter' => array('STATUS.TYPE'=>'O','LID'=>LANGUAGE_ID),
            'select' => array('STATUS_ID','NAME','DESCRIPTION'),
        ));

        while($status=$statusResult->fetch()){
            $arResult['STATUS_LIST'][$status['STATUS_ID']] = $status['NAME'];
        }

        //    Получаю заказ по номеру(id)
        $arOrder = CSaleOrder::GetByID($_REQUEST['order_number']);

        $arOrder['DATE_INSERT'] = date("d.m.Y", strtotime($arOrder['DATE_INSERT']));
        //    Получаю номер телефона из заказа, т.к. номер телефона пользователя может отличаться от номера телефона в заказе
        $obProps = Bitrix\Sale\Internals\OrderPropsValueTable::getList(array('filter' => array('ORDER_ID' => $_REQUEST['order_number'])));
        while($prop = $obProps->Fetch()){
            $arResult['PROPS'][$prop['CODE']] = $prop;
        }

        //    Обрабатываю номера телефонов и достаю статус из массива
        $form_phone = substr(preg_replace('~[^0-9]+~','',$_REQUEST['order_phone']), -10);
        $order_phone = substr(preg_replace('~[^0-9]+~','',$arResult['PROPS']['PHONE']['VALUE']), -10);
        $order_status = $arResult['STATUS_LIST'][$arOrder['STATUS_ID']];

        //    Получение содержимого корзины
        //    $obBasket = \Bitrix\Sale\Basket::getList(array('filter' => array('ORDER_ID' => $_REQUEST['order_number'])));
        //    $count=1;$price=0;
        //    while($bItem = $obBasket->Fetch()){
        //        echo $count.'. '.$bItem['NAME'].' - '.stristr($bItem['QUANTITY'], '.', true);
        //        $price = $price+$bItem['BASE_PRICE'];
        //    }
        //    round($price);

        if (CModule::IncludeModule("sale"))
        {
            //  проверяю используется ли капча и реализую логику вывода сообщения
            if($_POST['use_captcha']==1){
                if (!$arOrder){
                    echo "Заказ с кодом ".$_REQUEST['order_number']." не найден";
                }else{
                    if($order_status){
                        if($form_phone == $order_phone){
                            echo 'Ваш заказ №'.$arResult['PROPS']['EMAIL']['ORDER_ID'].' от '.substr($arOrder['DATE_MARKED'],0,10).':<br /> Текущий статус заказа: <b>'.$order_status.'</b>';
                        }else{
                            echo 'Номер телефона не соответствует заказу';
                        }
                    }
                }
            }else{
                if (!$arOrder){
                    echo "Заказ с кодом ".$_REQUEST['order_number']." не найден";
                }else{
                    if($order_status){
                        if($form_phone == $order_phone){
                            echo 'Ваш заказ №'.$arResult['PROPS']['EMAIL']['ORDER_ID'].' от '.substr($arOrder['DATE_INSERT'],0,10).':<br /> Текущий статус заказа: <b>'.$order_status.'</b>';
                        }else{
                            echo 'Номер телефона не соответствует заказу';
                        }
                    }
                }
            }
        }
    }else{
        echo $strErrorMessage.'<br />';
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php"); ?>