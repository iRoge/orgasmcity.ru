<?php

use Bitrix\Catalog\StoreTable;
use Bitrix\Main\Loader;
use Likee\Site\Helpers\HL;

/**
 * Class RdevsSellers
 */
class RdevsSellers extends CBitrixComponent
{
    /**
     * @var mixed|string
     */
    private $sellerGroupId;

    public function onPrepareComponentParams($arParams): array
    {
        parent::onPrepareComponentParams($arParams);

        $this->sellerGroupId = \Functions::getEnvKey('SELLERS_GROUP_ID');

        return $arParams;
    }

    public function executeComponent(): void
    {
        $isSeller = $this->checkUser();

        $sellerUser = $this->getSellerUser();

        $store = $this->getStore($sellerUser['UF_STORE']);

        $storeSellers = $this->getStoreSellers($store['ID']);

        $currentStoreSeller = $this->checkCurrentStoreSeller($storeSellers);

        $this->arResult['isSeller'] = $isSeller;
        $this->arResult['sellerUser'] = $sellerUser;
        $this->arResult['store'] = $store;
        $this->arResult['storeSellers'] = $storeSellers;
        $this->arResult['currentStoreSeller'] = $currentStoreSeller;

        $this->includeComponentTemplate();
    }

    private function getSellerUser()
    {
        return CUser::GetList(
            ($by = 'id'),
            ($order = 'asc'),
            ['WORK_PAGER' => $_COOKIE['seller_id']],
            [
                'FIELDS' => [
                    'WORK_COMPANY',
                    'WORK_DEPARTMENT',
                ],
                'SELECT' => [
                    'UF_STORE',
                ]
            ]
        )->Fetch();
    }

    private function getStore($storeId)
    {
        return StoreTable::getList(
            [
                'filter' =>
                    ['ID' => $storeId],
                'select' =>
                    ['ID', 'TITLE', 'XML_ID', 'UF_FILIAL']
            ]
        )->fetch();
    }

    private function getStoreSellers($storeId): array
    {
        Loader::includeModule('highloadblock');

        $obSellers = HL::getEntityClassByHLName('Sellers');
        $arSellers = [];

        if ($obSellers && is_object($obSellers)) {
            $sellerClass = $obSellers->getDataClass();

            $res = $sellerClass::getList(
                [
                    'filter' =>
                        [
                            'UF_STORE_ID' => $storeId,
                            'UF_FIRED' => 0,
                        ],
                    'select' =>
                        ['ID', 'UF_SURNAME', 'UF_NAME', 'UF_PATRONYMIC', 'UF_FULL_NAME'],
                ]
            );

            while ($seller = $res->fetch()) {
                $arSellers[$seller['ID']] = $seller;
            }
        }

        return $arSellers;
    }

    private function checkUser(): string
    {
        global $USER;

        $isSeller = '';

        if (in_array($this->sellerGroupId, $USER->GetUserGroupArray())) {
            $isSeller = 'seller';
        }

        return $isSeller;
    }

    private function checkCurrentStoreSeller(array $storeSellers)
    {
        if ($_COOKIE['storeSeller_id']) {
            $storeSeller = $storeSellers[$_COOKIE['storeSeller_id']];

            $arSellerName = explode(' ', $storeSeller['UF_FULL_NAME']);
            $storeSeller['SHORT_NAME'] = array_shift($arSellerName);
            count($arSellerName) > 0 ? $storeSeller['SHORT_NAME'] .= ' ' : '';
            foreach ($arSellerName as $item) {
                $storeSeller['SHORT_NAME'] .= mb_substr($item, 0, 1) . '.';
            }

            return $storeSeller;
        } else {
            return false;
        }
    }
}
