<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Sale\Internals\DiscountTable;

define('COUNT_DELETING', 1000);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
Extension::load("ui.progressbar");
CUtil::InitJSCore(array('ajax', 'popup', 'jquery'));
$APPLICATION->SetTitle('Удаление промокодов');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

try {
    if (!Loader::includeModule('sale')) {
        throw new Exception('Не удалось подключить модуль "Интернет-магазин".');
    }
} catch (Exception $exception) {
    $error = [
        'MESSAGE' => $exception->getMessage(),
        'TYPE' => 'ERROR',
    ];
    CAdminMessage::ShowMessage($error);
}

//Собираем список правил работы с корзиной
$discountList = DiscountTable::getList([
    'select' => ['ID', 'NAME'],
]);

$discountTypes = [];
while ($discount = $discountList->fetch()) {
    $discountTypes[$discount['ID']] = $discount['NAME'];
}


if (count($_REQUEST) > 1) {
    try {
        $deleted_all = 'Y';
        if (!empty($_REQUEST['deleted_all'])) {
            $deleted_all = $_REQUEST['deleted_all'];
        }

        $date_start = date('d_m_Y_H_i_s');
        if (!empty($_REQUEST['date_start'])) {
            $date_start = $_REQUEST['date_start'];
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/deleted_coupons_' . $date_start . '.txt';
        } else {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/deleted_coupons_' . $date_start . '.txt';
            file_put_contents($filePath, 'Начало удаления: ' . date('d.m.Y H:i:s') . PHP_EOL, FILE_APPEND);
            file_put_contents(
                $filePath,
                'ID пользователя запустившего удаление - ' . $USER->GetID() . PHP_EOL,
                FILE_APPEND
            );
            file_put_contents(
                $filePath,
                'IP с которого запустили удаление - ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL,
                FILE_APPEND
            );
        }


        $deleted = 0;
        if (!empty($_REQUEST['deleted'])) {
            $deleted = $_REQUEST['deleted'];
        }

        $step = 0;
        if (!empty($_REQUEST['step'])) {
            $step = $_REQUEST['step'];
        }


        if (!empty($_REQUEST['FROM'])) {
            $from = (new DateTime($_REQUEST['FROM']))->format('d.m.Y H:i:s');
        } else {
            $message[] = [
                'MESSAGE' => 'Укажите дату От',
                'TYPE' => 'ERROR',
            ];
        }

        if (!empty($_REQUEST['TO'])) {
            $to = (new DateTime($_REQUEST['TO']))->add(new DateInterval('PT23H59M59S'))->format('d.m.Y H:i:s');
        } else {
            $message[] = [
                'MESSAGE' => 'Укажите дату До',
                'TYPE' => 'ERROR',
            ];
        }

        if (!empty($_REQUEST['DISCOUNT_TYPE'])) {
            $discount_id = $_REQUEST['DISCOUNT_TYPE'];
        } else {
            $message[] = [
                'MESSAGE' => 'Укажите правило работы с корзиной',
                'TYPE' => 'ERROR',
            ];
        }

        if (!empty($from) && !empty($to) && !empty($discount_id)) {
            $count = $error_count = 0;
            file_put_contents($filePath, 'Диапазон удаления: от ' . $from . ' до ' . $to . PHP_EOL, FILE_APPEND);
            file_put_contents($filePath, 'Правило работы с корзиной: ' . $discount_id . PHP_EOL, FILE_APPEND);
            $couponsList = DiscountCouponTable::getList([
                'select' => ['ID', 'COUPON', 'TYPE', 'TIMESTAMP_X', 'DATE_CREATE'],
                'filter' => [
                    '>DATE_CREATE' => $from,
                    '<DATE_CREATE' => $to,
                    'DISCOUNT_ID' => $discount_id,
                    'ACTIVE' => 'Y',
                    [
                        'LOGIC' => 'OR',
                        array(
                            '@TYPE' => [1,2],
                        ),
                        array(
                            '=TYPE' => 4,
                            'USE_COUNT' => 0
                        )
                    ]

                ]
            ]);


            if (empty($_REQUEST['all'])) {
                $all = $couponsList->getSelectedRowsCount();
            } else {
                $all = $_REQUEST['all'];
            }

            if ($_REQUEST['AJAX'] == 'Y') {
                $APPLICATION->RestartBuffer();
                echo $all;
                die;
            } else {
                while ($arCoupon = $couponsList->fetch()) {
                    $result = DiscountCouponTable::delete($arCoupon['ID']);
                    if ($result->isSuccess()) {
                        $count++;
                        $string = 'Удалено: '
                            . ' ID - ' . $arCoupon['ID']
                            . ' Название - ' . $arCoupon['COUPON']
                            . ' Тип - ' . $arCoupon['TYPE']
                            . ' Дата последнего изменения - ' . $arCoupon['TIMESTAMP_X']
                            . ' Дата создания - ' . $arCoupon['DATE_CREATE'];
                        file_put_contents($filePath, $string . PHP_EOL, FILE_APPEND);
                    } else {
                        $error_count++;
                        $string = 'Возникла ошибка: '
                            . ' ID - ' . $arCoupon['ID']
                            . ' Название - ' . $arCoupon['COUPON']
                            . ' Тип - ' . $arCoupon['TYPE']
                            . ' Дата последнего изменения - ' . $arCoupon['TIMESTAMP_X']
                            . ' Дата создания - ' . $arCoupon['DATE_CREATE'];
                        file_put_contents($filePath, $string . PHP_EOL, FILE_APPEND);
                        foreach ($result->getErrorMessages() as $errorMessage) {
                            $message[] = [
                                'MESSAGE' => $errorMessage,
                                'TYPE' => 'ERROR',
                            ];
                        }
                    }

                    if ($count >= COUNT_DELETING) {
                        break; //Чтобы ограничить время исполнения скрипта
                    }
                }

                $deleted += $count;
                $persent = round($deleted / $all * 100);
                if ($persent > 100) {
                    $persent = 100;
                }
                if ($persent < 0) {
                    $persent = 0;
                }
                $step++;

                $deleted_all = ($all > $deleted) ? 'N' : 'Y';

                if ($count == 0) {
                    $deleted_all = 'Y';
                    $persent = 100;
                    $deleted = $all;
                }

                $string = 'Шаг - ' . $step
                    . 'Всего - ' . $all
                    . 'Удалено - ' . $deleted
                    . 'Процент удаления - ' . $persent;

                file_put_contents($filePath, $string . PHP_EOL, FILE_APPEND);

                $strParams = 'all=' . $all
                    . '&deleted=' . $deleted
                    . '&deleted_all=' . $deleted_all
                    . '&step=' . $step
                    . '&date_start=' . $date_start
                    . '&FROM=' . $_REQUEST['FROM']
                    . '&TO=' . $_REQUEST['TO']
                    . '&DISCOUNT_TYPE=' . $_REQUEST['DISCOUNT_TYPE'];
            }
        }
    } catch (Exception $exception) {
        $message[] = [
            'MESSAGE' => $exception->getMessage(),
            'TYPE' => 'ERROR',
        ];
    }

    if (!empty($message)) {
        file_put_contents($filePath, 'Возникшие ошибки:' . PHP_EOL, FILE_APPEND);
        foreach ($message as $arMess) {
            file_put_contents($filePath, $arMess['MESSAGE'] . PHP_EOL, FILE_APPEND);
            CAdminMessage::ShowMessage($arMess);
        }
    }

    if (isset($count)) {
        file_put_contents($filePath, 'Удалено ' . $count . ' записей' . PHP_EOL, FILE_APPEND);
        CAdminMessage::ShowMessage(['MESSAGE' => 'Удалено ' . $count . ' записей', 'TYPE' => 'OK']);
    }

    if ($deleted_all == 'Y') {
        file_put_contents($filePath, 'Конец удаления: ' . date('d.m.Y H:i:s') . PHP_EOL . PHP_EOL, FILE_APPEND);
    }
}

CAdminMessage::ShowMessage([
    'MESSAGE' => 'Выберите диапазон дат и правило работы с корзиной для удаления',
    'TYPE' => 'OK'
]);
?>

<? if (!empty($deleted) && !empty($all)) : ?>
    <div class="ui-progressbar ui-progressbar-lg ui-progressbar-column">
        <div class="ui-progressbar-text-before">Выполняется удаление</div>
        <div class="ui-progressbar-track">
            <div class="ui-progressbar-bar" style="width:<?= $persent ?>%;"></div>
        </div>
        <div class="ui-progressbar-text-after"><?= $deleted ?> из <?= $all ?></div>
    </div>
<? endif ?>

<? if ($deleted_all == 'N') : ?>
    Если страница не обновляется автоматически, нажмите на ссылку
    <a href="<? echo $APPLICATION->GetCurPage(); ?>?lang=<? echo LANG ?>&<? echo $strParams ?>">Следующий шаг</a><br>

    <script type="text/javascript">
        function DoNext() {
            window.location = "<?echo $APPLICATION->GetCurPage(); ?>?lang=<?echo LANG ?>&<?echo $strParams ?>";
        }

        setTimeout('DoNext()', 2000);
    </script>
<? endif; ?>

<form method="POST" id="delete_form">
    <table>
        <td style="width: 8%">
            <input type="date" name="FROM">
            -
            <input type="date" name="TO">
        </td>


        <td style="width: 25%">
            <select name="DISCOUNT_TYPE">
                <option disabled selected>Правило работы корзины</option>
                <?php foreach ($discountTypes as $id => $name) : ?>
                    <option value="<?= $id; ?>" <?= $id == $discountID ? ' selected' : ''; ?>><?= '[' . $id . '] ' . $name; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        </tr>
        <tr>
            <td colspan=4>
                <input type="button" name="DELETE" id="confirm_delete_button" value="Удалить" style="width: 25%">
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    BX.ready(function () {
        $('#confirm_delete_button').click(function () {
            //Собираем объект и добавляем к нему переменную ajax
            let data = $('#delete_form').serialize();
            data += '&AJAX=Y';

            console.log(data);

            $.ajax({
                type: "POST",
                url: "<?=$APPLICATION->GetCurPage()?>",
                data: data,
                success: function (data) {
                    $('ajax_answer').html('Будет удалено: ' + data + 'купонов');
                    var addAnswer = new BX.PopupWindow("my_answer", null, {
                        content: 'Будет удалено: ' + data + ' купонов. Продолжить?',
                        closeIcon: {right: "20px", top: "10px"},
                        titleBar: {
                            content: BX.create("span", {
                                html: '<b>Подтверждение удаления</b>',
                                'props': {'className': 'access-title-bar'}
                            })
                        },
                        zIndex: 0,
                        offsetLeft: 0,
                        offsetTop: 0,
                        draggable: {restrict: false},
                        buttons: [
                            new BX.PopupWindowButton({
                                text: "Удалить",
                                className: "popup-window-button-accept",
                                events: {
                                    click: function () {
                                        $('#delete_form').submit();
                                    }
                                }
                            }),
                            new BX.PopupWindowButton({
                                text: "Отменить",
                                className: "webform-button-link-cancel",
                                events: {
                                    click: function () {
                                        this.popupWindow.close(); // закрытие окна
                                    }
                                }
                            })
                        ]
                    });
                    addAnswer.show(); // появление окна
                }
            });
        });
    });
</script>