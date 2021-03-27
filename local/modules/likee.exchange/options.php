<?php
/**
 * Created by PhpStorm.
 * User: powermig29
 * Date: 12.02.15
 * Time: 15:06
 */

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\PersonTypeTable;
use Likee\Exchange\Config;

$sModuleID = 'likee.exchange';
Loader::includeModule($sModuleID);
Loader::includeModule('iblock');
Loader::includeModule('sale');

IncludeModuleLangFile(__FILE__);

\CUtil::InitJSCore(['jquery']);

$MOD_RIGHT = $APPLICATION->GetGroupRight($sModuleID);
if ($MOD_RIGHT >= 'Y' || $USER->IsAdmin()) {
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

    $arOptions = Config::get();

    if ($REQUEST_METHOD == 'POST' && $_POST['update']) {
        $arOptions['IBLOCK_ID'] = $_POST['IBLOCK_ID'];
        $arOptions['OFFERS_IBLOCK_ID'] = $_POST['OFFERS_IBLOCK_ID'];
        $arOptions['PATH'] = $_POST['PATH'];
        $arOptions['API'] = $_POST['API'];
        $arOptions['KEY'] = $_POST['KEY'];
        $arOptions['LOGIN'] = $_POST['LOGIN'];
        $arOptions['PASSWORD'] = $_POST['PASSWORD'];
        $arOptions['ACTIVE2'] = $_POST['ACTIVE2'] ?: false;
        $arOptions['KEY2'] = $_POST['KEY2'];
        $arOptions['API2'] = $_POST['API2'];
        $arOptions['LOGIN2'] = $_POST['LOGIN2'];
        $arOptions['PASSWORD2'] = $_POST['PASSWORD2'];
        Config::set($arOptions);
        LocalRedirect('/bitrix/admin/settings.php?lang=ru&mid=' . $sModuleID . '&mid_menu=1');
    }

    $arPersons = $arSubTabs = $aTabs = [];
    $rsPerson = PersonTypeTable::getList();
    while ($arPerson = $rsPerson->fetch()) {
        $arPersons[] = $arPerson;
        $arSubTabs[] = [
            'DIV' => 'person_' . $arPerson['ID'] . '_' . $arPerson['LID'],
            'TAB' => $arPerson['NAME'] . ' (' . $arPerson['LID'] . ')',
            'TITLE' => $arPerson['NAME'] . ' (' . $arPerson['LID'] . ')',
        ];
        $dbOrderProps = CSaleOrderProps::GetList(
            [],
            [
                'PERSON_TYPE_ID' => $arPerson['ID'],
            ],
            false,
            false,
            [
                'ID',
                'NAME',
            ]
        );
        while ($arOrderProps = $dbOrderProps->Fetch()) {
            $arProps[$arPerson['ID']][$arOrderProps['ID']] = $arOrderProps['NAME'];
        }
    }

    ?>

    <script>
        var props = <?=\CUtil::PhpToJSObject($arProps)?>;
    </script>

    <?
    $aTabs[] = [
        'DIV' => 'import',
        'TAB' => 'Настройки импорта',
        'TITLE' => 'Настройки импорта',
    ];

    $tabControl = new CAdminTabControl('tabControl', $aTabs);
    $tabControl->Begin();


    $arProperties = [
        'USER' => [
            'TITLE' => 'Пользователь',
            'PROPS' => [
                'NAME' => [
                    'TITLE' => 'Имя',
                ],
                'LAST_NAME' => [
                    'TITLE' => 'Фамилия',
                ],
                'SECOND_NAME' => [
                    'TITLE' => 'Отчество',
                ],
                'EMAIL' => [
                    'TITLE' => 'Email',
                ],
                'PHONE' => [
                    'TITLE' => 'Телефон',
                ],
            ],
        ],
        'DELIVERY' => [
            'TITLE' => 'Доставка',
            'PROPS' => [
                'REGION' => [
                    'TITLE' => 'Регион',
                ],
                'CITY' => [
                    'TITLE' => 'Фамилия',
                ],
                'STREET' => [
                    'TITLE' => 'Улица',
                ],
                'HOME' => [
                    'TITLE' => 'Дом',
                ],
                'HOUSING' => [
                    'TITLE' => 'Корпус',
                ],
                'PORCH' => [
                    'TITLE' => 'Подъезд',
                ],
                'FLOOR' => [
                    'TITLE' => 'Этаж',
                ],
                'FLAT' => [
                    'TITLE' => 'Квартира',
                ],
                'INTERCOM' => [
                    'TITLE' => 'Домофон',
                ],
            ],
        ],
    ];
    $arIblocks = [];

    $rsIblock = \CIBlock::GetList(['NAME' => 'ASC']);
    while ($arIblock = $rsIblock->Fetch()) {
        $arIblocks[] = $arIblock;
    }
    ?>
    <form method="POST"
          action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialchars($mid) ?>&lang=<? echo LANG ?>"
          name="ara">
        <?= bitrix_sessid_post(); ?>
        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2">Значения по умолчанию</td>
        </tr>
        <tr>
            <td valign="top" width="30%">Инфоблок товаров</td>
            <td valign="top" width="50%">
                <select name="IBLOCK_ID">
                    <option value="0"<? if ($arOptions['IBLOCK_ID'] == 0) :
                        ?> selected<?
                                     endif; ?>>
                        Выберите инфоблок
                    </option>
                    <? foreach ($arIblocks as $arIblock) : ?>
                        <option value="<?= $arIblock['ID'] ?>"<? if ($arOptions['IBLOCK_ID'] == $arIblock['ID']) :
                            ?> selected<?
                                       endif; ?>>
                            <?= $arIblock['NAME'] ?> [<?= $arIblock['ID'] ?>]
                        </option>
                    <? endforeach ?>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Инфоблок торговых предложений</td>
            <td valign="top" width="50%">
                <select name="OFFERS_IBLOCK_ID">
                    <option value="0"<? if ($arOptions['OFFERS_IBLOCK_ID'] == 0) :
                        ?> selected<?
                                     endif; ?>>
                        Выберите инфоблок
                    </option>
                    <? foreach ($arIblocks as $arIblock) : ?>
                        <option value="<?= $arIblock['ID'] ?>"<? if ($arOptions['OFFERS_IBLOCK_ID'] == $arIblock['ID']) :
                            ?> selected<?
                                       endif; ?>>
                            <?= $arIblock['NAME'] ?> [<?= $arIblock['ID'] ?>]
                        </option>
                    <? endforeach ?>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Путь к файлам выгрузки для FTP режима</td>
            <td valign="top" width="50%">
                <input type="text" name="PATH" value="<?= $arOptions['PATH'] ?>"/>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">Основная 1с</td>
        </tr>
        <tr>
            <td valign="top" width="30%">Ключ API</td>
            <td valign="top" width="50%">
                <input type="text" name="KEY" value="<?= $arOptions['KEY'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Адрес для отправки заказов</td>
            <td valign="top" width="50%">
                <input type="text" name="API" value="<?= $arOptions['API'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Пользователь</td>
            <td valign="top" width="50%">
                <input type="text" name="LOGIN" value="<?= $arOptions['LOGIN'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Пароль</td>
            <td valign="top" width="50%">
                <input type="text" name="PASSWORD" value="<?= $arOptions['PASSWORD'] ?>"/>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">Дополнительная 1с</td>
        </tr>
        <tr>
            <td valign="top" width="30%">Активность</td>
            <td valign="top" width="50%">
                <input type="checkbox" name="ACTIVE2" <?=($arOptions['ACTIVE2']) ? 'checked' : ''?>/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Ключ API</td>
            <td valign="top" width="50%">
                <input type="text" name="KEY2" value="<?= $arOptions['KEY2'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Адрес для отправки заказов</td>
            <td valign="top" width="50%">
                <input type="text" name="API2" value="<?= $arOptions['API2'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Пользователь</td>
            <td valign="top" width="50%">
                <input type="text" name="LOGIN2" value="<?= $arOptions['LOGIN2'] ?>"/>
            </td>
        </tr>
        <tr>
            <td valign="top" width="30%">Пароль</td>
            <td valign="top" width="50%">
                <input type="text" name="PASSWORD2" value="<?= $arOptions['PASSWORD2'] ?>"/>
            </td>
        </tr>
        <?
        $tabControl->Buttons();
        ?>
        <input type="submit" name="update" value="Сохранить">
        <? $tabControl->End(); ?>
    </form>
    <?
}
?>

<script>
    $(document).ready(function () {
        $('.type').change(function () {
            var td = $(this).closest('tr').find('td:last-child');
            var select = td.find('select');
            var input = td.find('input');
            var th = $(this);
            var person = th.data('person');

            select.html('');

            if (th.val() == 'USER') {
                select.show();
                input.hide();
            } else if (th.val() == 'ORDER') {
                select.show();
                input.hide();
            } else if (th.val() == 'PROPERTY') {
                select.show();
                input.hide();

                $.each(props[person], function (i, item) {
                    var option = $('<option/>');
                    option.val(i);
                    option.text(item);
                    select.append(option)
                })
            } else {
                select.hide();
                input.show();
            }
        });

        $('.type').change();

        setTimeout(function () {
            <? foreach ($arPersons as $arPerson) : ?>
                <? foreach ($arProperties as $code => $arTab) : ?>
                    <? foreach ($arTab['PROPS'] as $code => $arProp) : ?>
                        <? if ($arType[$code][$arPerson['ID']] == '') : ?>
            $('[name="VALUE2[<?= $code ?>][<?= $arPerson['ID'] ?>]"').val('<?=$arValues2[$code][$arPerson['ID']]?>');
                        <? else : ?>
            console.log($('[name="TYPE[<?= $code ?>][<?= $arPerson['ID'] ?>]"]').val());
            $('[name="VALUE1[<?= $code ?>][<?= $arPerson['ID'] ?>]"').val('<?=$arValues1[$code][$arPerson['ID']]?>');
                        <? endif ?>
                    <? endforeach; ?>
                <? endforeach; ?>
            <? endforeach; ?>

            $('.type').each(function () {
                var tr = $(this).closest('tr');
                var select = tr.find('.value1');
                var input = tr.find('.value2');

                if ($(this).val() == '') {
                    input()
                }
            })
        }, 100);
    });

</script>
