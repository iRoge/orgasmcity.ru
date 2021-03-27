<?

namespace Likee\Exchange\Tables;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

class BranchTable extends DataManager 
{	
	public static function getTableName() {
        return 'b_respect_branch';
    }
	
	public static function getMap()  {
        return [
            new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID'
            ]),
            new StringField('xml_id', [
                'title' => 'ID филиала'
            ]),
			new StringField('name', [
				'title' => 'Название филиала'
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