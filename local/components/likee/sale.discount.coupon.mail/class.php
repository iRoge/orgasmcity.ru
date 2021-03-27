<?php
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;
use \Bitrix\Main\Loader as Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class CSaleDiscountCouponMailComponent extends CBitrixComponent
{
	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params["CACHE_TIME"] = 0;
		$params["DETAIL_URL"] = trim($params["DETAIL_URL"]);

		if(Loader::includeModule("sale"))
		{

		}

		return $params;
	}

	/**
	 * @override
	 * @throws Exception
	 */
	protected function checkModules()
	{
		if(!Loader::includeModule("sale"))
			throw new SystemException(Loc::getMessage("CVP_SALE_MODULE_NOT_INSTALLED"));
		if(!Loader::includeModule("catalog"))
			throw new SystemException(Loc::getMessage("CVP_CATALOG_MODULE_NOT_INSTALLED"));
	}

	/**
	 * @override
	 * @throws Exception
	 */
	protected function prepareData()
	{
		$saleDiscountId = null;
		$wasAdded = false;
		$xmlId = $this->arParams['DISCOUNT_XML_ID'];
		$saleDiscountValue = (float) $this->arParams['DISCOUNT_VALUE'];
		$saleDiscountUnit = (string) $this->arParams['DISCOUNT_UNIT'];

		$fieldsAdd = array(
			'SITE_ID' => $this->getSiteId(),
			'NAME' => Loc::getMessage("CVP_DISCOUNT_NAME"),
			'ACTIVE' => 'Y',
			'ACTIVE_FROM' => '',
			'ACTIVE_TO' => '',
			'PRIORITY' => 1,
			'SORT' => 100,
			'LAST_DISCOUNT' => 'Y',
			'XML_ID' => $xmlId,
			'VALUE' => $saleDiscountValue,
			'VALUE_TYPE' => $catalogDiscountUnit,
			'CURRENCY' => 'RUB',
			'GROUP_IDS' => array(2),
			'CONDITIONS' => serialize(Array(
				'CLASS_ID' => 'CondGroup',
				'DATA' => Array(
					'All' => 'AND',
					'True' => 'True',
				),
				'CHILDREN' => Array()
			))
		);

		if(strlen($xmlId) <= 0)
		{
			return;
		}

		$fields = array(
			'XML_ID' => $xmlId,
			'ACTIVE' => 'Y'
		);
		$saleDiscountDb = CCatalogDiscount::GetList(array('DATE_CREATE' => 'DESC'), $fields, false, false, array('ID', 'VALUE', 'CONDITIONS'));
		if($saleDiscount = $saleDiscountDb->Fetch())
		{
			if($saleDiscount['VALUE'] == $fieldsAdd['VALUE'] && $saleDiscount['CONDITIONS'] == $fieldsAdd['CONDITIONS'])
			{
				$saleDiscountId = $saleDiscount['ID'];
			}

		}

		if(!$saleDiscountId)
		{
			$fieldsAdd['ACTIVE'] = 'N';
			$saleDiscountId = CCatalogDiscount::Add($fieldsAdd);
			$wasAdded = true;
		}

		$this->arResult['COUPON'] = '';
		if($saleDiscountId)
		{
			$coupon = CatalogGenerateCoupon();

			$arFields = array(
				"DISCOUNT_ID" => $saleDiscountId,
				"ACTIVE" => 'Y',
				"COUPON" => $coupon,
				"DATE_APPLY" => '',
				"ONE_TIME" => 'O',
				"DESCRIPTION" => $this->arParams['COUPON_DESCRIPTION'],
			);
			$ID = CCatalogDiscountCoupon::Add($arFields);

			if ($ID) {
				$this->arResult['COUPON'] = $coupon;
				if($wasAdded) {
					CCatalogDiscount::Update($saleDiscountId, array('ACTIVE' => 'Y'));
				}
			}
		}
	}

	/**
	 * Start Component
	 */
	public function executeComponent()
	{
		try
		{
			$this->checkModules();
			$this->prepareData();
			$this->includeComponentTemplate();
		}
		catch (SystemException $e)
		{
			ShowError($e->getMessage());
		}
	}
}