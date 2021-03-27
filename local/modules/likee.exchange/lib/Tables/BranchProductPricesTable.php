<?

namespace Likee\Exchange\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

class BranchProductPricesTable extends DataManager 
{	
	public static function getTableName() {
        return 'b_respect_product_price';
    }
	
	public static function getMap()  {
        return [
            new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new IntegerField('product_id', [
                'title' => 'ID товара'
            ]),
            new IntegerField('branch_id', [
                'title' => 'ID филиала'
            ]),
            new IntegerField('price', [
                'title' => 'Цена'
            ]),
            new IntegerField('price1', [
                'title' => 'Цена1'
            ]),
            new StringField('price_segment_id', [
                'title' => 'Сегмент'
            ]),
            new IntegerField('max_disc_bp', [
                'title' => 'Макс. размер скидки'
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