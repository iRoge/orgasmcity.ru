<?
use Qsoft\Helpers\SubscribeManager;
define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Отмена подписки");

$subscriberID = $_GET['id'];
$email = $_GET['email'];
$check = $_GET['check'];
$subscriber = null;
if ($subscriberID) {
    $subscriber = SubscribeManager::getSubscriber($subscriberID);
}
if ($check == 1 && $subscriber && $email == $subscriber['PROPERTY_EMAIL_VALUE']) {
    SubscribeManager::updateSubscriber($subscriberID, false, false);
    ?>
    <div class="page-massage page__message-order">
        Вы успешно отменили подписку.
    </div>
<?php
} elseif ($subscriber && $email && $email === $subscriber['PROPERTY_EMAIL_VALUE']) {
    ?>
    <div class="page-massage page__message-order">
        <h2>Отмена подписки для <?=$email?></h2>
        <button class="bttn">
            <a style="color: white; text-decoration: none" href="/unsubscribe/?check=1&email=<?=$email?>&id=<?=$subscriberID?>">
                Отменить подписку
            </a>
        </button>
    </div>
<?php
} else {
    Functions::abort404();
}
?>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
