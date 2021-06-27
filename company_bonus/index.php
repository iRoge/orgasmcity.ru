<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Бонусная программа");
$APPLICATION->SetTitle("Бонусная программа");
$APPLICATION->SetAdditionalCss("/local/templates/respect/css/application.css");
?>
<div style="margin-bottom: 30px">
    <span style="font-size: 14pt;">
        Вы имеете возможность снизить цену благодаря нашей бонусной программе. <br>
        Регестрируйтесь на сайте и получайте постоянную скидку за осуществление суммарных покупок на определенную сумму:<br>
        <b>
            <ul style="list-style-type: none;">
                <li>
                    10000 рублей = <span style="color: green">3%</span> постоянная скидка
                </li>
                <li>
                    20000 рублей = <span style="color: green">6%</span> постоянная скидка
                </li>
                <li>
                    30000 рублей = <span style="color: green">10%</span> постоянная скидка
                </li>
            </ul>
        </b>
        Вашу текущую общую сумму покупок и размер скидки вы можете посмотреть в личном кабинете.
    </span>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
