<?

use Qsoft\Helpers\BonusSystem;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Бонусная программа");
$APPLICATION->SetTitle("Бонусная программа");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Информация о бонусной программе в городе оргазма. За каждый заказ вы будете получать накопительную скидку в процентах от стоимости будущих покупок");

$APPLICATION->SetAdditionalCss("/local/templates/respect/css/application.css");

?>
<div style="margin-bottom: 30px">
    <span style="font-size: 14pt;">
        Вы имеете возможность снизить цену благодаря нашей бонусной программе. <br>
        Регистрируйтесь на сайте и получайте постоянную скидку за осуществление суммарных покупок на определенную сумму:<br>
        <b>
            <ul style="list-style-type: none;">
                <?php foreach (BonusSystem::BONUSES as $minOrderSum => $discount) {?>
                    <li>
                        <?=$minOrderSum?> рублей = <span style="color: green"><?=$discount?>%</span> постоянная скидка
                    </li>
                <?php }?>
            </ul>
        </b>
        Вашу текущую общую сумму покупок и размер скидки вы можете посмотреть в <a href="/personal/bonuses/">личном кабинете</a>. <br>
        Все скидки автоматически суммируются и показываются в каталоге.
    </span>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
