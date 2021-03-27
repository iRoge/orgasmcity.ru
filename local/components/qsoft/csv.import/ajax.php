<?php
define('ADMIN_SECTION', true);
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\NotImplementedException;

class CSVImportAjax extends Controller
{

    private const LOG_PATH = '/local/logs/roistat/import/';
    /**
     * @return array
     */
    public function configureActions()
    {
        if (CModule::IncludeModule("sale")) {
            return [
                'processChunk' => []
            ];
        }
    }

    public function processChunkAction($start, $end, $tmpFile)
    {
        $resArr = file_get_contents($tmpFile);
        $adsInfo = unserialize($resArr);
        $chunk = array_slice($adsInfo, $start, $end);

        foreach ($chunk as $info) {
            $this->log('Updating order ' . $info['ID']);
            try {
                $order = Bitrix\Sale\Order::load($info['ID']);
            } catch (ArgumentNullException $e) {
                $this->log('Error: ' . $e->getMessage());
                $this->log($e->getTraceAsString());
            }
            if ($order) {
                try {
                    $order->setField('STATUS_ID', $info['STATUS']);
                    $this->log('Order status set to ' . $info['STATUS']);
                } catch (ArgumentException $e) {
                    $this->log('Error: ' . $e->getMessage());
                    $this->log($e->getTraceAsString());
                }
                try {
                    $propertyCollection = $order->getPropertyCollection();
                } catch (ArgumentException $e) {
                    $this->log('Error: ' . $e->getMessage());
                    $this->log($e->getTraceAsString());
                } catch (NotImplementedException $e) {
                    $this->log('Error: ' . $e->getMessage());
                    $this->log($e->getTraceAsString());
                }

                $revenueProp = $propertyCollection->getItemByOrderPropertyId(71);
                $revenue = $info['REVENUE'] ?: $order->getPrice();
                $revenueProp->setValue($revenue);
                $this->log('Order revenue set to ' . $revenue);

                try {
                    $order->save();
                    $this->log('Order saved');
                } catch (Exception $e) {
                    $this->log('Error: ' . $e->getMessage());
                    $this->log($e->getTraceAsString());
                }
            } else {
                $this->log('Error: order not found');
            }
        }

        $result = [
            'processed' => $start + $end
        ];

        if ($start + $end >= count($adsInfo)) {
            $result['finished'] = true;

            unlink($_REQUEST['tpm_file']);
        }

        return $result;
    }

    private function log($message)
    {
        $fileName = date('Y.m.d') . '.log';
        qsoft_logger($message, $fileName, self::LOG_PATH);
    }
}
