<?

namespace Likee\Exchange\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\FloatField;
use Bitrix\Main\Entity\StringField;

class ReserveStorageTable extends DataManager {	
	
	public static function getTableName() {
        return 'b_likee_items_reserve_storage';
    }
	
	public static function getMap()  {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new IntegerField('PRODUCT_ID', [
                'title' => 'ID товара'
            ]),
			new IntegerField('STORAGE_ID', [
				'title' => 'ID склада'
			]),
			new FloatField('QUANTITY', [
				'title' => 'Количество'
			]),
			new StringField('STATUS', [
				'title' => 'Статус резервирования'
			]), 
			new IntegerField('ORDER_ID', [
				'title' => 'ID заказа'
			]),			
        ];
    }
	
	public static function add(array $data) {
        $result = parent::add($data);		
        return $result;
    }

	public static function delete($primary)  {
        $result = parent::delete($primary);
        return $result;
    }
}