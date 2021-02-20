<?php

namespace Sprint\Migration;


class DevRelease20190222122317 extends Version {

    protected $description = "Изменения с теста 2.0-dev";

    public function up(){
        $helper = new HelperManager();

		/** HL блок для группировок */
        $hlblockId = $helper->Hlblock()->addHlblockIfNotExists([
            'NAME' => 'GroupProducts',
            'TABLE_NAME' => 'b_respect_group_products',
        ]);
		
		$helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_IDIBLOCK', [
            'USER_TYPE_ID' => 'integer',
            'SETTINGS' => [
                'SIZE' => '20',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'ID элемента',
                'en' => 'ID элемента',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'ID элемента',
                'en' => 'ID элемента',
            ],
        ]);
		$helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_SKU', [
            'USER_TYPE_ID' => 'string',
            'SETTINGS' => [
                'SIZE' => '50',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'Артикул',
                'en' => 'Артикул',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Артикул',
                'en' => 'Артикул',
            ],
        ]);
		$helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_GROUP', [
            'USER_TYPE_ID' => 'integer',
            'SETTINGS' => [
                'SIZE' => '20',
            ],
            'EDIT_FORM_LABEL' => [
                'ru' => 'ID группировки',
                'en' => 'ID группировки',
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'ID группировки',
                'en' => 'ID группировки',
            ],
        ]);
		
		/** инфоблок магазины */
		$iblockId1 = $helper->Iblock()->addIblockIfNotExists([
            'NAME' => 'Магазины',
            'CODE' => 'SHOPSLIST',
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LIST_PAGE_URL' => '',
        ]);
		$helper->Iblock()->addPropertyIfNotExists($iblockId1, [
            'NAME' => 'Город',
            'CODE' => 'SHOP_CITY',
            'PROPERTY_TYPE' => 'E',
            'MULTIPLE' => 'N',
			'REQUIRED' => 'Y',
            'SORT' => '100',
            'LINK_IBLOCK_ID' => 21,
        ]);
		
		/** поле Файл с артикулами для группировок */
		$helper->Iblock()->addPropertyIfNotExists(24, [
            'NAME' => 'Файл с артикулами',
            'CODE' => 'ARTICUL_FILE',
            'PROPERTY_TYPE' => 'F',
            'FILE_TYPE' => 'csv'
        ]);
		
		/** таблицы для филиалов */
		$db = \Bitrix\Main\Application::getConnection();
        $sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_branch` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `xml_id` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);
		
		$sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_branch_location2branch` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `location_id` int(11) NOT NULL,
		  `branch_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);

		$sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_city2branch` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
		  `branch_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);

		$sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_domains` (
		  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `UF_XML_ID` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `UF_NAME` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `UF_DESCRIPTION` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  `UF_FULL_DESCRIPTION` text COLLATE utf8_unicode_ci DEFAULT NULL,
		  PRIMARY KEY (`ID`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);

		$sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_product_price` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `product_id` int(11) NOT NULL,
		  `branch_id` int(11) NOT NULL,
		  `price` int(11) DEFAULT NULL,
		  `price1` int(11) DEFAULT NULL,
		  `price_segment_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `max_disc_bp` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);

		$sql = '
        CREATE TABLE IF NOT EXISTS `b_respect_store2branch` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `store_id` int(11) NOT NULL,
		  `branch_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ';
        $db->query($sql);
    }

    public function down(){
        $helper = new HelperManager();

        $helper->Hlblock()->deleteHlblockIfExists('GroupProducts');
		$helper->Iblock()->deleteIblockIfExists('SHOPSLIST');

    }

}
