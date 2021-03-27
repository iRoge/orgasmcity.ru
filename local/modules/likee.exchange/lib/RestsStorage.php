<?php
namespace Likee\Exchange;

use \Bitrix\Main\Application;

class RestsStorage
{
    const TABLE_NAME = 'b_likee_1c_rests_session';

    public static function init()
    {
        $connection = Application::getConnection();

        $connection->query('CREATE TABLE IF NOT EXISTS '.self::TABLE_NAME.' (product_id INT(11), store_id INT(11), UNIQUE KEY id_product_store (product_id, store_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8');
        $connection->query('TRUNCATE TABLE '.self::TABLE_NAME);
    }
    
    protected static function isInitialized()
    {
        static $status = null;

        if (is_null($status)) {
            $status = false; 
            
            try {
                $connection = Application::getConnection();
                $status = (bool) $connection->query('DESCRIBE '.self::TABLE_NAME)->getSelectedRowsCount();
            } catch (\Exception $e) {}
        }

        return $status;
    }

    public function add($productId, $storeId)
    {
        if (self::isInitialized()) {
            $connection = Application::getConnection();

            $sql = 'INSERT IGNORE INTO '.self::TABLE_NAME.' (product_id, store_id) VALUES('.intval($productId).', '.intval($storeId).')';
            $connection->query($sql);
        }
    }

    public function check($productId, $storeId)
    {
        if (self::isInitialized()) {
            $connection = Application::getConnection();

            $sql = 'SELECT product_id FROM '.self::TABLE_NAME.' WHERE product_id = '.intval($productId).' AND store_id = '.intval($storeId).' GROUP BY product_id';
            return (bool) $connection->query($sql)->getSelectedRowsCount();
        }

        return false;
    }
}