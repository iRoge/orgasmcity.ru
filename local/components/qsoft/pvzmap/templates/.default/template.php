<? if (!$arParams['IS_AJAX']) : ?>
    <div class="widget__popup-mask" style="display: none;">
        <div class="widget__popup">
            <div id="pvzWidget">

                <div id="pvzMap"></div>
                <div class="pvz_sidebar">
                    <ul class="sidebar-menu">
                        <li>
                            <div class="sidebar-burger close">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="pvz_panel panel_hidden">
                    <div class="panel_header"><span></span></div>
                    <div class="panel_content">
                        <div class="panel_container">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="hidden_block">
        <div id="filter_block">

            <div class="panel-list__item">
                <button id="pvz_filter" onclick="pvzmap.changeFilteredPoints(event);" style="display: none;">Применить
                    фильтр
                </button>
            </div>

            <div id="pvz_item">
                <button type="button" class="pvz_item__close" onclick="panel.closePanel();">
                    <svg class="icon-cross" fill="#ffffff" viewBox="0 0 20 20" width="20" height="20">
                        <path d="M18.2 20L0 1.7 1.7 0 20 18 18 20z"></path>
                        <path d="M1.7 20L20 1.7 18 0 0 18.2 1.7 20z"></path>
                    </svg>
                </button>

                <div class="panel-details__block">
                    <p class="panel-details__block-head">Адрес пункта выдачи заказов:</p>
                    <p class="panel-details__block-text"></p>
                </div>

                <div class="panel-details__block">
                    <p class="panel-details__block-head">Время работы:</p>
                    <p class="panel-details__block-text"></p>
                </div>

                <div class="panel-details__block">
                    <button class="widget__choose bttn" data-label="Выбрать" onclick="panel.choose(event, this);">
                        Выбрать
                    </button>
                </div>
            </div>

        </div>
    </div>
    <script>
        window.loadPVZMap = function(){
            $('.load-more-btn-loader').show();
            if (window.pvzmap === undefined) {
                $.ajax({
                    url: '/local/components/qsoft/pvzmap/ajax.php',
                    data: {
                        pvz_ids: '<?=json_encode($GLOBALS['PVZ_IDS'])?>',
                        pvz_prices: '<?=json_encode($GLOBALS['PVZ_PRICES'])?>',
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        window.pvz = data.PVZ.CLASS_MAP;
                        window.pvzObj = data.PVZ;

                        let script = document.createElement("script");
                        script.src = '/local/components/qsoft/pvzmap/templates/.default/pvzmap.js';
                        document.getElementsByTagName("head")[0].appendChild(script);

                        script = document.createElement("script");
                        script.src = 'https://api-maps.yandex.ru/2.1/?apikey=ab43734d-eaa7-46b7-b203-b6c5d32acc8c&lang=ru_RU&onload=pvzmap.init&load=package.standard';
                        document.getElementsByTagName("head")[0].appendChild(script);
                    }
                });
            } else {
                pvzmap.show();
                $('.load-more-btn-loader').hide();
            }
        };
    </script>
<? else : ?>
    <?= json_encode($arResult, JSON_UNESCAPED_UNICODE) ?>
<? endif; ?>