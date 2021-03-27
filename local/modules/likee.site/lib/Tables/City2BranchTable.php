<?php

namespace Likee\Site\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

class City2BranchTable extends DataManager
{
	public static function getTableName() {
        return 'b_respect_city2branch';
    }
	
	public static function getMap()  {
        return [
            new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new StringField('name', [
                'title' => 'Город'
            ]),
            new IntegerField('branch_id', [
                'title' => 'ID филиала'
            ]),
        ];
    }
}