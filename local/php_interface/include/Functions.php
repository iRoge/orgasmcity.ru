<?php
/**
 * Created by PhpStorm.
 * User: ZeK
 * Date: 06.09.2018
 * Time: 16:29
 */

use Bitrix\Iblock\Component\Tools;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Глобавльные вспомогательные функции
 * Class Functions
 */
class Functions
{
    public static $iCacheTime = 3600;
    public static $sCachePath = 'functions';

    public static function abort404()
    {
        if (!defined("ERROR_404")) {
            define("ERROR_404", "Y");
        }

        return false;
    }
    /** Метод ищет в кэше массив
     * @param string $cacheId - ключ кэширования
     * @return array|bool
     */
    public function getArrayCache($cacheId)
    {
        $obCache = new CPHPCache();
        $cachePath = '/'.SITE_ID.'/' . self::$sCachePath . '/' .$cacheId;
        if ($obCache->InitCache(self::$iCacheTime, $cacheId, $cachePath)) {
            $vars = $obCache->GetVars();
            return  $vars['result'];
        } else {
            return false;
        }
    }

    /** Метод записывает в кэш массив
     * @param string $cacheId - ключ кэширования
     * @param array $arVariables - массив
     * @return bool
     */
    public static function setArrayCache($cacheId, $arVariables)
    {
        $obCache = new CPHPCache();
        $cachePath = '/'.SITE_ID.'/' . self::$sCachePath . '/' .$cacheId;
        if ($obCache->InitCache(self::$iCacheTime, $cacheId, $cachePath) && $obCache->StartDataCache()) {
            $obCache->EndDataCache(array(
                "result"    => $arVariables
            ));
            return true;
        }
        return false;
    }

    /**
     * Возвращает массив с информацией о инфоблоке по его коду
     * @param string $sCode - символьный код инфоблока
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIBlockByCode($sCode)
    {
        $sCacheKey = __METHOD__ . '.' . $sCode;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            if ($arReturn = self::getArrayCache($sCacheKey)) {
                return $arReturn;
            } else {
                $res = CIBlock::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", 'CODE' => $sCode));
                $arResult = $res->fetch();
                self::setArrayCache($sCacheKey, $arResult);
                return $arResult;
            }
        }
    }


    /**
     * Возвращает массив с информацией о свойстве инфоблока по его коду
     * @param string $sCode - символьный код инфоблока
     * @param integer $iIblockID - ид инфоблока
     * @return array|bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIBlockPropertyByCode($sCode, $iIblockID)
    {
        $sCacheKey = __METHOD__ . '.' . $iIblockID . '.'. $sCode;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            if ($arReturn = self::getArrayCache($sCacheKey)) {
                return $arReturn;
            } else {
                $res = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => $iIblockID, 'CODE' => $sCode));
                $arResult = $res->fetch();
                self::setArrayCache($sCacheKey, $arResult);
                return $arResult;
            }
        }
    }

     /**
     * Возвращает id инфоблока по артиклу
     * @param string $Article - символьный код инфоблока
     * @return array|bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIBlockElementByArticle($Article)
    {
        $sCacheKey = __METHOD__ . '.' . $Article;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            if ($arReturn = self::getArrayCache($sCacheKey)) {
                return $arReturn;
            } else {
                $arFilter = array('IBLOCK_ID'=> 16, 'PROPERTY_ARTICLE'=>$Article);
                $arSelect = array("ID");
                $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize"=>1), $arSelect);
                $arResult = $res->fetch();
                self::setArrayCache($sCacheKey, $arResult);
                return $arResult;
            }
        }
    }
    /**
     * Возвращает артикл инфоблока по id
     * @param string $Article - символьный код инфоблока
     * @return array|bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getArticleByID($id)
    {
        $sCacheKey = __METHOD__ . '.' . $id;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            if ($arReturn = self::getArrayCache($sCacheKey)) {
                return $arReturn;
            } else {
                $arFilter = array('IBLOCK_ID'=> 16, 'ID'=>$id);
                $arSelect = array("PROPERTY_ARTICLE");
                $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize"=>1), $arSelect);
                $arResult = $res->fetch();
                self::setArrayCache($sCacheKey, $arResult);
                return $arResult[PROPERTY_ARTICLE_VALUE];
            }
        }
    }

    /**
     * Поиск по вложенным массивам
     * @param arr $id - значение поиска
     * @param arr $arSKU - массив массивов
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function ib_deep_in_array($id, $arSKU)
    {
        foreach ($arSKU as $value) {
            if (in_array($id, $value, true)) {
                return true;
            }
            return false;
        }
    }

    public static function exitJson($data)
    {
        global $APPLICATION;
        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');
        exit(json_encode($data));
    }

    public static function ResizeImageGet($file, $arSize, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL, $bInitSizes = false, $arFilters = false, $bImmediate = false, $jpgQuality = false)
    {
        if (!is_array($file) && intval($file) > 0) {
            $file = CFile::GetFileArray($file);
        }

        if (!is_array($file) || !array_key_exists("FILE_NAME", $file) || strlen($file["FILE_NAME"]) <= 0) {
            return false;
        }

        if ($resizeType !== BX_RESIZE_IMAGE_EXACT && $resizeType !== BX_RESIZE_IMAGE_PROPORTIONAL_ALT) {
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
        }

        if (!is_array($arSize)) {
            $arSize = array();
        }
        if (!array_key_exists("width", $arSize) || intval($arSize["width"]) <= 0) {
            $arSize["width"] = 0;
        }
        if (!array_key_exists("height", $arSize) || intval($arSize["height"]) <= 0) {
            $arSize["height"] = 0;
        }
        $arSize["width"] = intval($arSize["width"]);
        $arSize["height"] = intval($arSize["height"]);

        $uploadDirName = COption::GetOptionString("main", "upload_dir", "upload");

        $imageFile = "/".$uploadDirName."/".$file["SUBDIR"]."/".$file["FILE_NAME"];
        $arImageSize = false;
        $bFilters = is_array($arFilters) && !empty($arFilters);

        if (($arSize["width"] <= 0 || $arSize["width"] >= $file["WIDTH"])
            && ($arSize["height"] <= 0 || $arSize["height"] >= $file["HEIGHT"])
        ) {
            if ($bFilters) {
                //Only filters. Leave size unchanged
                $arSize["width"] = $file["WIDTH"];
                $arSize["height"] = $file["HEIGHT"];
                $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
            } else {
                global $arCloudImageSizeCache;
                $arCloudImageSizeCache[$file["SRC"]] = array($file["WIDTH"], $file["HEIGHT"]);

                return array(
                    "src" => $file["SRC"],
                    "width" => intval($file["WIDTH"]),
                    "height" => intval($file["HEIGHT"]),
                    "size" => $file["FILE_SIZE"],
                );
            }
        }

        $io = CBXVirtualIo::GetInstance();
        $cacheImageFile = "/".$uploadDirName."/resize_cache/".$file["SUBDIR"]."/".$arSize["width"]."_".$arSize["height"]."_".$resizeType.(is_array($arFilters)? md5(serialize($arFilters)): "")."/".$file["FILE_NAME"];

        $cacheImageFileCheck = $cacheImageFile;
        if ($file["CONTENT_TYPE"] == "image/bmp") {
            $cacheImageFileCheck .= ".jpg";
        }

        static $cache = array();
        $cache_id = $cacheImageFileCheck;
        if (isset($cache[$cache_id])) {
            return $cache[$cache_id];
        } elseif (!file_exists($io->GetPhysicalName($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck))) {
            /****************************** QUOTA ******************************/
            $bDiskQuota = true;
            if (COption::GetOptionInt("main", "disk_space") > 0) {
                $quota = new CDiskQuota();
                $bDiskQuota = $quota->checkDiskQuota($file);
            }
            /****************************** QUOTA ******************************/

            if ($bDiskQuota) {
                if (!is_array($arFilters)) {
                    $arFilters = array(
                        array("name" => "sharpen", "precision" => 15),
                    );
                }

                $sourceImageFile = $_SERVER["DOCUMENT_ROOT"].$imageFile;
                $cacheImageFileTmp = $_SERVER["DOCUMENT_ROOT"].$cacheImageFile;
                $bNeedResize = true;
                $callbackData = null;

                foreach (GetModuleEvents("main", "OnBeforeResizeImage", true) as $arEvent) {
                    if (ExecuteModuleEventEx($arEvent, array(
                        $file,
                        array($arSize, $resizeType, array(), false, $arFilters, $bImmediate),
                        &$callbackData,
                        &$bNeedResize,
                        &$sourceImageFile,
                        &$cacheImageFileTmp,
                    ))) {
                        break;
                    }
                }

                if ($bNeedResize && Functions::ResizeImageFile($sourceImageFile, $cacheImageFileTmp, $arSize, $resizeType, array(), $jpgQuality, $arFilters)) {
                    $cacheImageFile = substr($cacheImageFileTmp, strlen($_SERVER["DOCUMENT_ROOT"]));

                    /****************************** QUOTA ******************************/
                    if (COption::GetOptionInt("main", "disk_space") > 0) {
                        CDiskQuota::updateDiskQuota("file", filesize($io->GetPhysicalName($cacheImageFileTmp)), "insert");
                    }
                    /****************************** QUOTA ******************************/
                } else {
                    $cacheImageFile = $imageFile;
                }

                foreach (GetModuleEvents("main", "OnAfterResizeImage", true) as $arEvent) {
                    if (ExecuteModuleEventEx($arEvent, array(
                        $file,
                        array($arSize, $resizeType, array(), false, $arFilters),
                        &$callbackData,
                        &$cacheImageFile,
                        &$cacheImageFileTmp,
                        &$arImageSize,
                    ))) {
                        break;
                    }
                }
            } else {
                $cacheImageFile = $imageFile;
            }

            $cacheImageFileCheck = $cacheImageFile;
        }

        if ($bInitSizes && !is_array($arImageSize)) {
            $arImageSize = CFile::GetImageSize($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck);

            $f = $io->GetFile($_SERVER["DOCUMENT_ROOT"].$cacheImageFileCheck);
            $arImageSize[2] = $f->GetFileSize();
        }

        $cache[$cache_id] = array(
            "src" => $cacheImageFileCheck,
            "width" => intval($arImageSize[0]),
            "height" => intval($arImageSize[1]),
            "size" => $arImageSize[2],
        );
        return $cache[$cache_id];
    }

    public static function ResizeImageFile($sourceFile, &$destinationFile, $arSize, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL, $arWaterMark = array(), $jpgQuality = false, $arFilters = false)
    {
        $io = CBXVirtualIo::GetInstance();

        if (!$io->FileExists($sourceFile)) {
            return false;
        }

        $bNeedCreatePicture = false;

        if ($resizeType !== BX_RESIZE_IMAGE_EXACT && $resizeType !== BX_RESIZE_IMAGE_PROPORTIONAL_ALT) {
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
        }

        if (!is_array($arSize)) {
            $arSize = array();
        }
        if (!array_key_exists("width", $arSize) || intval($arSize["width"]) <= 0) {
            $arSize["width"] = 0;
        }
        if (!array_key_exists("height", $arSize) || intval($arSize["height"]) <= 0) {
            $arSize["height"] = 0;
        }
        $arSize["width"] = intval($arSize["width"]);
        $arSize["height"] = intval($arSize["height"]);

        $arSourceSize = array("x" => 0, "y" => 0, "width" => 0, "height" => 0);
        $arDestinationSize = array("x" => 0, "y" => 0, "width" => 0, "height" => 0);

        $arSourceFileSizeTmp = CFile::GetImageSize($sourceFile);
        if (!in_array($arSourceFileSizeTmp[2], array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP))) {
            return false;
        }

        $orientation = 0;
        if ($arSourceFileSizeTmp[2] == IMAGETYPE_JPEG) {
            $exifData = CFile::ExtractImageExif($io->GetPhysicalName($sourceFile));
            if ($exifData  && isset($exifData['Orientation'])) {
                $orientation = $exifData['Orientation'];
                //swap width and height
                if ($orientation >= 5 && $orientation <= 8) {
                    $tmp = $arSourceFileSizeTmp[1];
                    $arSourceFileSizeTmp[1] = $arSourceFileSizeTmp[0];
                    $arSourceFileSizeTmp[0] = $tmp;
                }
            }
        }

        if (CFile::isEnabledTrackingResizeImage()) {
            header("X-Bitrix-Resize-Image: {$arSize["width"]}_{$arSize["height"]}_{$resizeType}");
        }
        if (class_exists("imagick") && function_exists('memory_get_usage')) {
            //When memory limit reached we'll try to use ImageMagic
            $memoryNeeded = $arSourceFileSizeTmp[0] * $arSourceFileSizeTmp[1] * 4 * 3;
            $memoryLimit = CUtil::Unformat(ini_get('memory_limit'));
            if ((memory_get_usage() + $memoryNeeded) > $memoryLimit) {
                if ($arSize["width"] <= 0 || $arSize["height"] <= 0) {
                    $arSize["width"] = $arSourceFileSizeTmp[0];
                    $arSize["height"] = $arSourceFileSizeTmp[1];
                }
                CFile::ScaleImage($arSourceFileSizeTmp[0], $arSourceFileSizeTmp[1], $arSize, $resizeType, $bNeedCreatePicture, $arSourceSize, $arDestinationSize);
                if ($bNeedCreatePicture) {
                    $new_image = CTempFile::GetFileName(bx_basename($sourceFile));
                    CheckDirPath($new_image);
                    $im = new Imagick();
                    try {
                        $im->setOption('jpeg:size', $arDestinationSize["width"].'x'.$arDestinationSize["height"]);
                        $im->setSize($arDestinationSize["width"], $arDestinationSize["height"]);
                        $im->readImage($io->GetPhysicalName($sourceFile));
                        $im->setImageFileName($new_image);
                        $im->thumbnailImage($arDestinationSize["width"], $arDestinationSize["height"], true);
                        $im->writeImage();
                        $im->destroy();
                    } catch (ImagickException $e) {
                        $new_image = "";
                    }

                    if ($new_image != "") {
                        $sourceFile = $new_image;
                        $arSourceFileSizeTmp = CFile::GetImageSize($io->GetPhysicalName($sourceFile));
                    }
                }
            }
        }

        if ($io->Copy($sourceFile, $destinationFile)) {
            switch ($arSourceFileSizeTmp[2]) {
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($io->GetPhysicalName($sourceFile));
                    $bHasAlpha = true;
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($io->GetPhysicalName($sourceFile));
                    $bHasAlpha = true;
                    break;
                case IMAGETYPE_BMP:
                    $sourceImage = CFile::ImageCreateFromBMP($io->GetPhysicalName($sourceFile));
                    $bHasAlpha = false;
                    break;
                default:
                    $sourceImage = imagecreatefromjpeg($io->GetPhysicalName($sourceFile));
                    if ($sourceImage === false) {
                        ini_set('gd.jpeg_ignore_warning', 1);
                        $sourceImage = imagecreatefromjpeg($io->GetPhysicalName($sourceFile));
                    }

                    if ($orientation > 1) {
                        $properlyOriented = CFile::ImageHandleOrientation($orientation, $sourceImage);

                        if ($jpgQuality === false) {
                            $jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
                        }
                        if ($jpgQuality <= 0 || $jpgQuality > 100) {
                            $jpgQuality = 95;
                        }

                        if ($properlyOriented) {
                            imagejpeg($properlyOriented, $io->GetPhysicalName($destinationFile), $jpgQuality);
                            $sourceImage = $properlyOriented;
                        }
                    }
                    $bHasAlpha = false;
                    break;
            }

            $sourceImageWidth = intval(imagesx($sourceImage));
            $sourceImageHeight = intval(imagesy($sourceImage));

            if ($sourceImageWidth > 0 && $sourceImageHeight > 0) {
                if ($arSize["width"] <= 0 || $arSize["height"] <= 0) {
                    $arSize["width"] = $sourceImageWidth;
                    $arSize["height"] = $sourceImageHeight;
                }

                CFile::ScaleImage($sourceImageWidth, $sourceImageHeight, $arSize, $resizeType, $bNeedCreatePicture, $arSourceSize, $arDestinationSize);

                if ($bNeedCreatePicture) {
                    if (CFile::IsGD2()) {
                        $picture = ImageCreateTrueColor($arDestinationSize["width"], $arDestinationSize["height"]);
                        if ($arSourceFileSizeTmp[2] == IMAGETYPE_PNG) {
                            $transparentcolor = imagecolorallocatealpha($picture, 0, 0, 0, 127);
                            imagefilledrectangle($picture, 0, 0, $arDestinationSize["width"], $arDestinationSize["height"], $transparentcolor);

                            imagealphablending($picture, false);
                            imagefilter($sourceImage, IMG_FILTER_NEGATE);
                            imagecopyresampled(
                                $picture,
                                $sourceImage,
                                0,
                                0,
                                $arSourceSize["x"],
                                $arSourceSize["y"],
                                $arDestinationSize["width"],
                                $arDestinationSize["height"],
                                $arSourceSize["width"],
                                $arSourceSize["height"]
                            );
                            imagefilter($picture, IMG_FILTER_NEGATE);
                            imagealphablending($picture, true);
                        } elseif ($arSourceFileSizeTmp[2] == IMAGETYPE_GIF) {
                            imagepalettecopy($picture, $sourceImage);

                            //Save transparency for GIFs
                            $transparentcolor = imagecolortransparent($sourceImage);
                            if ($transparentcolor >= 0 && $transparentcolor < imagecolorstotal($sourceImage)) {
                                $RGB = imagecolorsforindex($sourceImage, $transparentcolor);
                                $transparentcolor = imagecolorallocate($picture, $RGB["red"], $RGB["green"], $RGB["blue"]);
                                imagecolortransparent($picture, $transparentcolor);
                                imagefilledrectangle($picture, 0, 0, $arDestinationSize["width"], $arDestinationSize["height"], $transparentcolor);
                            }
                            imagefilter($sourceImage, IMG_FILTER_NEGATE);
                            imagecopyresampled(
                                $picture,
                                $sourceImage,
                                0,
                                0,
                                $arSourceSize["x"],
                                $arSourceSize["y"],
                                $arDestinationSize["width"],
                                $arDestinationSize["height"],
                                $arSourceSize["width"],
                                $arSourceSize["height"]
                            );
                            imagefilter($picture, IMG_FILTER_NEGATE);
                        } else {
                            imagefilter($sourceImage, IMG_FILTER_NEGATE);
                            imagecopyresampled(
                                $picture,
                                $sourceImage,
                                0,
                                0,
                                $arSourceSize["x"],
                                $arSourceSize["y"],
                                $arDestinationSize["width"],
                                $arDestinationSize["height"],
                                $arSourceSize["width"],
                                $arSourceSize["height"]
                            );
                            imagefilter($picture, IMG_FILTER_NEGATE);
                        }
                    } else {
                        $picture = ImageCreate($arDestinationSize["width"], $arDestinationSize["height"]);
                        imagecopyresized(
                            $picture,
                            $sourceImage,
                            0,
                            0,
                            $arSourceSize["x"],
                            $arSourceSize["y"],
                            $arDestinationSize["width"],
                            $arDestinationSize["height"],
                            $arSourceSize["width"],
                            $arSourceSize["height"]
                        );
                    }
                } else {
                    $picture = $sourceImage;
                }

//                if (is_array($arFilters)) {
//                    foreach ($arFilters as $arFilter) {
//                        $bNeedCreatePicture |= CFile::ApplyImageFilter($picture, $arFilter, $bHasAlpha);
//                    }
//                }
//
//                if (is_array($arWaterMark)) {
//                    $arWaterMark["name"] = "watermark";
//                    $bNeedCreatePicture |= CFile::ApplyImageFilter($picture, $arWaterMark, $bHasAlpha);
//                }

                if ($bNeedCreatePicture) {
                    if ($io->FileExists($destinationFile)) {
                        $io->Delete($destinationFile);
                    }
                    switch ($arSourceFileSizeTmp[2]) {
                        case IMAGETYPE_GIF:
                            imagegif($picture, $io->GetPhysicalName($destinationFile));
                            break;
                        case IMAGETYPE_PNG:
                            imagealphablending($picture, false);
                            imagesavealpha($picture, true);
                            imagepng($picture, $io->GetPhysicalName($destinationFile));
                            break;
                        default:
                            if ($arSourceFileSizeTmp[2] == IMAGETYPE_BMP) {
                                $destinationFile .= ".jpg";
                            }
                            if ($jpgQuality === false) {
                                $jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
                            }
                            if ($jpgQuality <= 0 || $jpgQuality > 100) {
                                $jpgQuality = 95;
                            }
                            imagejpeg($picture, $io->GetPhysicalName($destinationFile), $jpgQuality);

                            break;
                    }
                    imagedestroy($picture);
                }
            }

            return true;
        }
        return false;
    }

    public static function getRests($offerIds): array
    {
        $rests = [];
        if (count($offerIds) > 5000) {
            foreach (array_chunk($offerIds, 5000) as $ids) {
                $rsStoreProduct = \Bitrix\Catalog\ProductTable::getList(
                    [
                        'filter' => [
                            'ID' => $ids
                        ],
                        'select' => ['ID', 'QUANTITY']
                    ],
                );
                while ($arStoreProduct = $rsStoreProduct->fetch()) {
                    $rests[$arStoreProduct['ID']] = $arStoreProduct['QUANTITY'];
                }
            }
        } else {
            $rsStoreProduct = \Bitrix\Catalog\ProductTable::getList(
                [
                    'filter' => [
                        'ID' => $offerIds
                    ],
                    'select' => ['ID', 'QUANTITY']
                ],
            );
            while ($arStoreProduct = $rsStoreProduct->fetch()) {
                $rests[$arStoreProduct['ID']] = $arStoreProduct['QUANTITY'];
            }
        }

        return $rests;
    }

    public static function getAllOffers(): array
    {
        global $CACHE_MANAGER;
        $offerCache = new CPHPCache;
        $arOffers = [];

        if ($offerCache->InitCache(360000, 'allOffers', 'offers')) {
            $arOffers = $offerCache->GetVars()['allOffers'];
        } elseif ($offerCache->StartDataCache()) {
            $CACHE_MANAGER->StartTagCache('offers');
            $CACHE_MANAGER->RegisterTag('catalogAll');

            $arFilter = [
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
                "!PROPERTY_CML2_LINK" => false,
            ];

            $arSelect = [
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_SIZE",
                "PROPERTY_COLOR",
            ];

            $resOffers = CIBlockElement::GetList(
                ["SORT" => "ASC"],
                $arFilter,
                false,
                false,
                $arSelect,
            );

            while ($offer = $resOffers->Fetch()) {
                $arOffers[$offer['ID']] = $offer;
            }

            $CACHE_MANAGER
                ->endTagCache();
            $offerCache->EndDataCache(['allOffers' => $arOffers]);
        }

        return $arOffers;
    }

    public static function filterOffersByRests($offers)
    {
        $rests = Functions::getRests(array_keys($offers));

        foreach ($offers as $id => $offer) {
            if (!isset($rests[$id]) || !$rests[$id]) {
                unset($offers[$id]);
            }
        }

        return $offers;
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function sendMarketingMail($emailTo, $subject, $body, $subscriberID = null)
    {
        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.orgasmcity.ru';
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->Username = 'market@orgasmcity.ru';
        $mail->Password = 'org@smcity-market';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('market@orgasmcity.ru', 'Ваш проводник в Городе Оргазма');
        $mail->addAddress($emailTo);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        if ($subscriberID) {
            $mail->AddCustomHeader(
                "List-Unsubscribe",
                '<https://' . DOMAIN_NAME . '/unsubscribe/?email=' . $emailTo . '&id=' . $subscriberID . '&check=1>'
            );
            $mail->AddCustomHeader(
                "List-Unsubscribe-Post",
                'List-Unsubscribe=One-Click'
            );
        }
        $mail->AddCustomHeader(
            "Precedence",
            'bulk'
        );

        $mail->send();
    }

    public static function insertFields($message, $fields)
    {
        foreach ($fields as $field => $value) {
            $message = str_replace('##' . $field . '##', $value, $message);
        }

        return $message;
    }
}
