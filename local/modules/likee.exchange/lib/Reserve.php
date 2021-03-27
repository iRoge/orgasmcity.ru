<?

namespace Likee\Exchange;

class Reserve
{
    const STATUS_RESERVE = 'R';
    const STATUS_PURCHASE = 'P';

    function __construct()
    {

    }

    /*
    * Метод добавляет товар в резерв для резервирования. Резерв требует точного указания склада.
    */
    public static function addItemToReserve($productId, $storageId, $quantity, $orderId)
    {

        $productId = (int)$productId;
        $storageId = (int)$storageId;
        $quantity = (float)$quantity;

        if ($productId <= 0 || $storageId <= 0)
            return false;

        if ($quantity <= 0)
            $quantity = 1;

        $data = [
            'PRODUCT_ID' => (int)$productId,
            'STORAGE_ID' => (int)$storageId,
            'QUANTITY' => $quantity,
            'STATUS' => self::STATUS_RESERVE,
            'ORDER_ID' => (int)$orderId,
        ];

        return \Likee\Exchange\Tables\ReserveStorageTable::add($data);
    }

    /*
    * Метод добавляет товар в резерв для покупки. Покупка не требует точного указания склада, товар может быть приобретен в любом.
    * При выгрузке может передаться ID склада.
    */
    public static function addItemToPurchase($productId, $storageId, $quantity, $orderId)
    {

        $productId = (int)$productId;
        $storageId = (int)$storageId;
        $quantity = (float)$quantity;

        if ($productId <= 0)
            return false;

        if ($quantity <= 0)
            $quantity = 1;


        $data = [
            'PRODUCT_ID' => (int)$productId,
            'STORAGE_ID' => (int)$storageId,
            'QUANTITY' => $quantity,
            'STATUS' => self::STATUS_PURCHASE,
            'ORDER_ID' => (int)$orderId,
        ];

        return \Likee\Exchange\Tables\ReserveStorageTable::add($data);
    }

    /*
    * Метод получает количество зарезервированного товара. Если передать склад - то только по нему, иначе - все резервы вообще.
    */

    public static function getItemReservedCount($productId, $storageId)
    {

        $productId = (int)$productId;
        $storageId = (int)$storageId;
        if ($productId <= 0)
            return 0;

        $filter = [
            'PRODUCT_ID' => $productId,
            'STATUS' => self::STATUS_RESERVE
        ];

        if ($storageId > 0)
            $filter['STORAGE_ID'] = $storageId;

        $quantity = 0;
        $dbItems = \Likee\Exchange\Tables\ReserveStorageTable::getList(['filter' => $filter, 'select' => ['ID', 'QUANTITY']]);

        while ($arItem = $dbItems->fetch()) {
            $quantity += (float)$arItem['QUANTITY'];
        }

        return $quantity;
    }
    public static function getItemReservedCount2($productId)
    {
        $productId = (int)$productId;
        if ($productId <= 0)
            return 0;

        $filter = [
            'PRODUCT_ID' => $productId,
            'STATUS' => self::STATUS_RESERVE
        ];

        $quantity = 0;
        $dbItems = \Likee\Exchange\Tables\ReserveStorageTable::getList(['filter' => $filter, 'select' => ['ID', 'QUANTITY']]);

        while ($arItem = $dbItems->fetch()) {
            $quantity += (float)$arItem['QUANTITY'];
        }

        return $quantity;
    }

    /*
    * Количество купленных товаров без учета склада. $storageId не используется - задел на возможные доработки.
    * Предполагается, что наличие товара на конкретном складе не важно.
    */
    public static function getItemBoughtCount($productId, $storageId = null)
    {
        $productId = (int)$productId;
        $storageId = (int)$storageId;
        if ($productId <= 0)
            return false;

        $quantity = 0;

        $filter = ['PRODUCT_ID' => $productId, 'STATUS' => self::STATUS_PURCHASE];
        if ($storageId)
            $filter['STORAGE_ID'] = $storageId;

        $dbItems = \Likee\Exchange\Tables\ReserveStorageTable::getList(
            ['filter' => $filter, 'select' => ['ID', 'QUANTITY']]
        );

        while ($arItem = $dbItems->fetch()) {
            $quantity += (float)$arItem['QUANTITY'];
        }

        return $quantity;
    }

    /*
    * Сброс данных по резервированию товара
    */
    public static function removeItemData($productId, $full = false)
    {
        $productId = (int)$productId;
        if ($productId <= 0 && !$full)
            return false;

        $filter = [];
        if ($productId)
            $filter = ['filter' => ['PRODUCT_ID' => $productId]];

        $dbItems = \Likee\Exchange\Tables\ReserveStorageTable::getList($filter);

        while ($arItem = $dbItems->fetch()) {
            \Likee\Exchange\Tables\ReserveStorageTable::delete($arItem['ID']);
        }
    }

    public static function clearOrderReserve($orderId)
    {
        $orderId = (int)$orderId;
        if ($orderId <= 0)
            return false;

        $filter = ['filter' => ['ORDER_ID' => $orderId]];
        $dbItems = \Likee\Exchange\Tables\ReserveStorageTable::getList($filter);

        while ($arItem = $dbItems->fetch()) {
            \Likee\Exchange\Tables\ReserveStorageTable::delete($arItem['ID']);
        }
    }
}