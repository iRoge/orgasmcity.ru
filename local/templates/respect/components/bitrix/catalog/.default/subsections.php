<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<div class="col-xs-12 subsection">
    <div id="wrapper" class="wrapper-scroll">
        <div class="content">
            <?
            $arSection = \CIBlockSection::GetByID($arCurSection['ID'])->Fetch();
            $rsSections = \CIBlockSection::GetList(
                ['NAME' => 'ASC'],
                [
                    'IBLOCK_ID' => IBLOCK_CATALOG,
                    'ACTIVE' => 'Y',
                    'DEPTH_LEVEL' => 3,
//                    '>LEFT_BORDER' => $arSection['LEFT_MARGIN'],
//                    '<RIGHT_BORDER' => $arSection['RIGHT_MARGIN']
                ],
                false,
                ['ID', 'IBLOCK_ID', 'NAME', 'CODE', 'SECTION_PAGE_URL']
            );
            
            while ($arSubSection = $rsSections->GetNext()) :
                $imageCode = $arSubSection['CODE'];
                $imagePath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/img/'.$imageCode.'.png';
                if (!file_exists($imagePath)) {
                    $imageCode = 'botinki';
                }
                ?>
                <div class="section top-botinki">
                    <a href="<?= $arSubSection['SECTION_PAGE_URL']; ?>">
                        <div class="in-img">
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/<?= $imageCode; ?>.png" class="img-g" />
                            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/<?= $imageCode; ?>-b.png" class="img-b" />
                        </div>
                        <p><?= $arSubSection['NAME']; ?></p>
                    </a>
                </div>
            <? endwhile; ?>

        </div>
    </div>
</div>
<style>
        .wrapper-scroll {
          -webkit-overflow-scrolling: touch;
          overflow-scrolling: touch;
          overflow: auto;

          margin-top: 30px;
          margin-bottom: 20px;
    
          z-index: 1;
          position: relative;
        }

        .content {
          width: -moz-max-content;
          width: -webkit-max-content;
          width: -ms-max-content;
          width: max-content;
        }


        .section {
          float: left;
          overflow: hidden;
          width: 100px;
          margin: 0 10px 0 0;
          text-align: center;
        }
        .section a
        {
          text-decoration: none;
          color: #8e8787!important;
          font-family: 'gilroyRegular';
          font-size: 14px;
        }
        .section:hover > a
        {

          color: #ff5e5e!important;
        }
        .wrapper-scroll::-webkit-scrollbar
        {
          height: 5px;
          background: #eeeeee;
        }
        .wrapper-scroll::-webkit-scrollbar-thumb
        {
          background: #d5d2d2;
        }
        .wrapper-scroll::-webkit-scrollbar-thumb:hover
        {
          background: #ff5e5e;
        }
</style>
<script src="<?= SITE_TEMPLATE_PATH; ?>/js/horwheel.js"></script>
<script>
    var view = document.getElementById('wrapper');
    horwheel(view);
</script>
