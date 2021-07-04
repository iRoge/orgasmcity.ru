<?php

use Qsoft\Helpers\BonusSystem;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Личный кабинет');

global $USER;
$bonusHelper = new BonusSystem($USER->GetID());
$bonusHelper->recalcUserBonus();
?>
<div style="margin-bottom: 30px">
<span style="font-size: 14pt;">
    Общая сумма оплаченных заказов = <b><?=$bonusHelper->getUsersPaidOrdersSum()?> рублей</b><br>
    Ваша скидка = <b><?=$bonusHelper->getCurrentBonus()?>%</b> <br>
    Узнать про бонусную программу и возможность снизить цену вы можете на <a href="/company_bonus/">странице</a>
</span>
</div>


<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
