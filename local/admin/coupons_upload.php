<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Загрузка промокодов');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Sale\Internals\DiscountTable;
use Bitrix\Main\Type\DateTime as BitrixDate;

if (!empty($_POST)) {
    try {
        if (!Loader::includeModule('sale')) {
            throw new Exception('Не удалось подключить модуль "Интернет-магазин".');
        }

        $discountList = DiscountTable::getList([
            'select' => ['ID', 'NAME'],
        ]);

        $discountTypes = [];
        while ($discount = $discountList->fetch()) {
            $discountTypes[$discount['ID']] = $discount['NAME'];
        }

        $couponTypes = [
            'SINGLE-USE' => DiscountCouponTable::TYPE_ONE_ORDER,
            'REUSABLE' => DiscountCouponTable::TYPE_MULTI_ORDER,
            'SINGLE-BASKET-ITEM' => DiscountCouponTable::TYPE_BASKET_ROW,
        ];

        if ($_POST['CHECK']) {
            $enteredCoupons = array_filter(
                array_map('trim', explode(',', $_POST['COUPONS'])),
                function ($coupon) {
                    return !empty($coupon);
                }
            );

            if (empty($enteredCoupons)) {
                throw new Exception('Введите купоны в поле ниже.');
            }

            $couponList = DiscountCouponTable::getList([
                'filter' => ['=COUPON' => $enteredCoupons],
                'select' => ['COUPON'],
            ]);

            $existingCoupons = [];
            while ($coupon = $couponList->fetch()) {
                $existingCoupons[] = $coupon['COUPON'];
            }

            $newCoupons = empty($existingCoupons) ? $enteredCoupons : array_udiff($enteredCoupons, $existingCoupons, 'strcasecmp');
            if (empty($newCoupons)) {
                throw new Exception('Введенные промокоды уже существуют.');
            }
        } elseif ($_POST['UPLOAD']) {
            $newCoupons = array_map('trim', explode(',', $_POST['COUPONS']));

            if (empty($newCoupons)) {
                throw new Exception('Нет промокодов, готовых к загрузке.');
            }

            $data = [];

            $discountID = trim($_POST['DISCOUNT_TYPE']);
            if (empty($discountID)) {
                throw new Exception('Не выбрано правило работы корзины.');
            }
            $data['DISCOUNT_ID'] = $discountID;

            $type = trim($_POST['COUPON_TYPE']);
            if (empty($type)) {
                throw new Exception('Не выбран тип промокода.');
            }
            $data['TYPE'] = $type;

            $hasPeriod = (bool) $_POST['ACTIVITY_PERIOD']['FLAG'];
            if ($hasPeriod) {
                $periodStart = trim($_POST['ACTIVITY_PERIOD']['START']);
                $periodEnd = trim($_POST['ACTIVITY_PERIOD']['END']);

                if (empty($periodStart) || empty($periodEnd)) {
                    throw new Exception('Не выбран период активности промокода.');
                }
                $data['ACTIVE_FROM'] = BitrixDate::createFromPhp(new DateTime($periodStart));
                $data['ACTIVE_TO'] = BitrixDate::createFromPhp(new DateTime($periodEnd));
            }

            foreach ($newCoupons as $coupon) {
                $data['COUPON'] = $coupon;
                $result = DiscountCouponTable::add($data);

                if (!$result->isSuccess()) {
                    $fails[$coupon] = $coupon . ' (' . implode('; ', $result->getErrorMessages()) . ')';
                }
            }

            $uploadedCoupons = empty($fails) ? $newCoupons : array_diff($newCoupons, array_keys($fails));

            if (!empty($fails)) {
                throw new Exception('Не удалось загрузить промокод(ы) ' . implode(', ', $fails) . '.');
            }

            unset($newCoupons);
            $message = [
                'MESSAGE' => 'Все промокоды загружены.',
                'TYPE' => 'OK',
            ];
        }
    } catch (Exception $exception) {
        $message = [
            'MESSAGE' => $exception->getMessage(),
            'TYPE' => 'ERROR',
        ];
    }
}

?>

<?php
if (!empty($message)) {
    CAdminMessage::ShowMessage($message);
}
?>

<form method="POST">
    <table style="width: 1000px">
    <?php if (!empty($existingCoupons)) : ?>
        <tr>
            <td colspan="4">
                <span style="color: red; font-size: 16px;">Уже существуют:</span>
                <ul>
                <?php foreach ($existingCoupons as $coupon) : ?>
                    <li><?= $coupon; ?></li>
                <?php endforeach; ?>
                </ul>
                <br>
            </td>
        </tr>
    <?php endif;?>

    <?php if (empty($newCoupons)) : ?>
        <tr>
            <td>
                <textarea name="COUPONS" placeholder="Введите промокоды через запятую" rows="5" style="width: 50%"><?= implode(', ', $uploadedCoupons); ?></textarea>
            </td>
        </tr>

        <tr>
            <td>
                <input type="submit" name="CHECK" value="Проверить" style="width: 25%">
                <br><br>
            </td>
        </tr>
    <?php else : ?>
        <tr style="width: 100%">
            <td style="width: 25%">
                <textarea name="COUPONS" rows="5" style="width: 90%" readonly><?= implode(', ', $newCoupons); ?></textarea>
            </td>

            <td style="width: 25%">
                <select name="DISCOUNT_TYPE" style="width: 100%">
                    <option disabled selected>Правило работы корзины</option>
                    <?php foreach ($discountTypes as $id => $name) : ?>
                        <option value="<?= $id; ?>" <?= $id == $discountID ? ' selected' : ''; ?>><?= $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td style="width: 25%">
                <select name="COUPON_TYPE" style="width: 100%">
                    <option disabled selected>Тип купона</option>
                    <option value="<?= $couponTypes['SINGLE-USE']; ?>" <?= $couponTypes['SINGLE-USE'] == $type ? ' selected' : ''; ?>>На один заказ</option>
                    <option value="<?= $couponTypes['REUSABLE']; ?>" <?= $couponTypes['REUSABLE'] == $type ? ' selected' : ''; ?>>Многоразовый</option>
                    <option value="<?= $couponTypes['SINGLE-BASKET-ITEM']; ?>" <?= $couponTypes['REUSABLE'] == $type ? ' selected' : ''; ?>>На одну позицию заказа</option>
                </select>
            </td>

            <td style="width: 25%">
                <p><input name="ACTIVITY_PERIOD[FLAG]" type="radio" value="0" checked>Неограниченно</p>
                <span>
                    <input name="ACTIVITY_PERIOD[FLAG]" type="radio" value="1">Период активности
                    <input name="ACTIVITY_PERIOD[START]" type="date">
                    <input name="ACTIVITY_PERIOD[END]" type="date">
                </span>
            </td>
        </tr>

        <tr style="width: 100%">
            <td colspan="4">
                <input type="submit" name="UPLOAD" value="Загрузить" style="width: 25%">
            </td>
        </tr>
    <?php endif; ?>
    </table>
</form>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
