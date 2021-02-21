<?php
/**
*   DEBUG old
*/
if (!function_exists('debug_old')) {
    function debug_old($ar, $title = false, $qwe = false)
    {
        echo '<pre style="border:2px #ff0000 solid;font-size:large;width:700px;heigth:200px;overflow:auto;">';
        if ($title) {
            echo "<h3>$title</h3>\n";
        }
        ob_start();
        if ($qwe) {
            var_dump($ar);
        } else {
            if (is_array($ar) || is_object($ar)) {
                print_r($ar);
            } else {
                var_dump($ar);
            }
        }
            $result=htmlspecialchars(ob_get_contents());
            ob_end_clean();
        echo $result."</pre>";
    }
}
/**
*   DEBUG by Tolubaev Sergei
*/
if (!function_exists('debug')) {
    function debug($data = null, $comment = "c", $view = "c")
    {
        // ��� ������� �� ������
    # if(!in_array($_SERVER['REMOTE_ADDR'],array("213.243.91.98","91.77.192.17")))
     #      return false;
        if (!$data) {
            $data = gettype($data)." => false";
        }
        
        if (strlen($comment)<=1) {
            $view = $comment;
            $comment = null;
        }
        
        $info = debug_backtrace();
        $info = $info[0];
        $info['file'] = substr($info['file'], strlen($_SERVER['DOCUMENT_ROOT']));
        
        $where = "{$info['file']}:{$info['line']}";
        if ($comment) {
            $where .= "<span class='qs-debug-comment'>{$comment}</span>";
        }
        
        switch ($view) {
            case "t":
                echo "<pre style='color: #444; text-align: left; background-color: white !important; font-family: monospace;font-size: 12px;border:1px solid gray; display: block; padding: 10px;'>";
                echo "<div style='padding:3px;background:#444;color:white;font-size:10px;'>{$where}</div>";
                print_r($data);
                echo "</pre>";
                break;
            case "c":
                if (!defined("QS_DEBUG")) {
                    //��� ������� �� ������
                    define("QS_DEBUG", true);
                    echo "
                        <style type='text/css'>
                            div.qs-debug {
                                display: none;
                            }
                            #qs-debug {
                                text-align: left;
                                position: fixed;
                                background: #CCC;
                                color: black;
                                padding: 10px;
                                max-height: 512px;
                                top: 0;
                                left: 1%;
                                width: 96%;
                                opacity: 0.92;
                                font-size: 12px;
                                font-family: 'DejaVu Sans Mono',verdana;
                                font-weight: bold;
                                overflow: auto;
                                z-index: 99999;
                                display: none;
                                border-bottom:2px solid #333;
                                border-bottom-left-radius: 3px;
                                -moz-border-radius-bottomleft: 3px;
                                -webkit-border-bottom-left-radius: 3px;
                            }
                            #qs-debug div.qs-debug {
                                white-space: pre;
                                padding-bottom: 10px;
                                display: block;
                                border-bottom: 1px solid #999;
                                margin-bottom: 10px;
                                width: 100%;
                                overflow: hidden;
                            }
                            #qs-debug div.qs-debug div {
                                font-weight: bold;
                                padding-top: 2px;
                                padding-bottom: 4px;
                                margin-bottom: 3px;
                            }
                            span.qs-debug-comment {
                                color: green;
                                display: block;
                                padding-top: 5px;
                                font-style: bold;
                            }
                            #qs-debug-flag {
                                position: fixed;
                                bottom: 1%;
                                left: 1%;
                                background: black;
                                color: white;
                                font-family: monospace;
                                font-size: 12px;
                                padding: 3px;
                                border: 1px solid #888;
                                cursor: pointer;
                                text-style: italic;
                                z-index: 99999;
                            }
                        </style>
                        <script type='text/javascript'>
                            if (typeof $ == 'undefined') {
                                var s = document.createElement('script');
                                s.setAttribute('type','text/javascript');
                                s.setAttribute('src','http://code.jquery.com/jquery-latest.pack.js');
                                var b = document.getElementsByTagName('head')[0].appendChild(s);
                            }
                            var i = setInterval ('check_jq()', 100);
                            function check_jq () {
                                if (typeof $ == 'function') {
                                    clearInterval(i);
                                    
                                    var head = $('head');
                                    $('style').each(function(){
                                        head.append($(this));
                                    });
                                    var qs_debug = $('<div>').attr('id','qs-debug');
                                    $('body').append(qs_debug);
                                    var flag = $('<div>').attr('id','qs-debug-flag').html('debug').click(function(){
                                        qs_debug.toggle();
                                    })
                                    $('body').append(flag);
                                    document.onkeypress = function(e){
                                        var key = (e.which) ? e.which : e.keyCode;
                                        if (key == '96' || key == '1105') {
                                            qs_debug.toggle();
                                        }
                                    }
                                    
                                    $(document).ready(function(){
                                        $('div.qs-debug').each(function(){
                                            qs_debug.append($(this));
                                        });
                                    });
                                }
                            }
                        </script>
                    ";
                }
                
                echo "<div class='qs-debug'><div>{$where}</div>".print_r($data, true)."</div>";

                break;
        }
    }
}

/**
*   DEBUG ... maybe still used somewhere ...
*/
if (!function_exists('DebugMessage')) {
    function debugmessage($message, $title = false, $access = true, $color = '#008B8B')
    {
        ?>
        <table border="0" cellpadding="5" cellspacing="0" style="border:1px solid <?=$color?>;margin:2px;"><tr><td>
        <?php

        if (strlen($title)>0) {
            echo '<p style="color:'.$color.';font-size:11px;font-family:Verdana;">['.$title.']</p>';
        }

        if (is_array($message) || is_object($message)) {
            echo '<pre style="color:'.$color.';font-size:11px;font-family:Verdana;text-align: left; background-color:#FFF">';
            print_r($message);
            echo '</pre>';
        } else {
            echo '<p style="color:'.$color.';font-size:11px;font-family:Verdana;">'.var_dump($message).'</p>';
        }
        echo '</td></tr><tr><td>';
         echo '<div style="font-family:verdana; font-size: 10px; font-weight: normal">';
         $a = debug_backtrace();
         $a = $a[0];
         echo "{$a['file']}: {$a['line']}";
         echo '</div>';

        ?></td></tr></table><?php
    }
}

/*
        Доработанный trace_me
        $text - что выводить
        $debug - выводить в область debug (функция выше)
        $in_file - выводить в файл. В корне сайта будет создана папка trace_me
        $get_full - если false, то просто выводит в виде строки "файл, строку и функцию""
                    можно задать параметры что выводить:
                        l - строка
                        f - функция
                        c - класс
                        o - объект
                        t - тип
                        a - массив параметров в виде JSON(JSON_UNESCAPED_SLASHES)
                                Например "lca" - выведет строку, класс, массив параметров
                                Порядок значения не имеет
        $only_me - true: выводить только для себя. При этом происходит проверка на права Админа.
                    false: выводить для всех. При этом идет проверка на наличие логина:
                                                                                если есть, то файл будет типа %login%.log, 
                                                                                если нет, то все будет писаться в файл ALL.log
        $header - пояснялка к тому что выводит $text

        деф-параметры: ($text, true, false, false, true, false)
*/
if (!function_exists('trace_me')) {
    function trace_me($text, $debug = true, $in_file = false, $get_full = false, $only_my = true, $header = false)
    {
        global $USER;
        if ($only_my) {
            if (!$USER->IsAdmin()) {
                return;
            }
            $user_login=$USER->GetLogin();
        } else {
            $user_login=($USER->GetLogin())?$USER->GetLogin():"ALL";
        }
        if (!is_string($text)) {
            $text=var_export($text, true);
        }

        $trace_array = debug_backtrace();
        $trace_str='';
        for ($i=0; $i<=20; $i++) {
            if (empty($trace_array[$i])) {
                break;
            }
            if (!$get_full){
                $trace_str .= "\n".$trace_array[$i]['file'].' (line: '.$trace_array[$i]['line'].'; function: '.$trace_array[$i]['function'].')';
            } else {
                $trace_str .= "\n#".$i.' > '.$trace_array[$i]['file'];
                if (stripos($get_full, "l")!==false && $trace_array[$i]['line']){
                    $trace_str .= "\n\t".'line: '.$trace_array[$i]['line']; 
                }
                if (stripos($get_full, "f")!==false && $trace_array[$i]['function']){
                    $trace_str .= "\n\t".'function: '.$trace_array[$i]['function'];
                }
                if (stripos($get_full, "c")!==false && $trace_array[$i]['class']){
                    $trace_str .= "\n\t".'class: '.$trace_array[$i]['class'];
                }
                if (stripos($get_full, "o")!==false && $trace_array[$i]['object']){
                    $trace_str .= "\n\t".'object: '.get_class($trace_array[$i]['object']);
                }
                if (stripos($get_full, "t")!==false && $trace_array[$i]['type']){
                    $trace_str .= "\n\t".'type: '.$trace_array[$i]['type'];
                }
                if (stripos($get_full, "a")!==false && $trace_array[$i]['args']){
                    $trace_str .= "\n\t".'args: '.json_encode($trace_array[$i]['args'], JSON_UNESCAPED_SLASHES);
                }
            }
        }

        $str = "DATE: ".date('d-m-Y H:i:s')." SESSION: ".session_id()." \n";
        $str .= "USER ID: ".$USER->GetID()." \n";
        $str .= "USER LOGIN: ".$user_login." \n";
        $str .= "HTTP_REFERER: ".$_SERVER['HTTP_REFERER']." \n";
        $str .= "SCRIPT_FILENAME: ".$_SERVER['SCRIPT_FILENAME']." \n";
        $str .= "TRACE: ".(function_exists("debug_backtrace")? print_r($trace_str, true):'')." \n";
        if (!empty($header)) {
            $str .= "HEADER: ".$header."\n";
        }
        $str .= "TEXT: ".$text."\n";
        $str .= "----------------------------------------------------\n\n";

        if ($in_file) {
            $file_path = $_SERVER['DOCUMENT_ROOT']."/upload/deb/trace_me/";
            CheckDirPath($file_path);
            $file = $user_login.".log";
            $fp = fopen($file_path.$file, "ab+");
            fputs($fp, $str);
            @fclose($fp);
        }
        if ($debug) {
            debug($str);
        }
    }
}

if (!function_exists('debugFile')) {
    function debugfile($message, $file = "debug.dbg", $dir = '', $path = "/upload/debug/")
    {
        $message = (is_array($message) || is_object($message)) ? print_r($message, 1) : $message;
        $dir = (!empty($dir)) ? $dir.'/' : '';
        $log_path = $_SERVER['DOCUMENT_ROOT'].$path.$dir;
        CheckDirPath($log_path, true);
        $log_file = $log_path.$file;
        $info = debug_backtrace();
        $info = $info[0];
        $info['file'] = substr($info['file'], strlen($_SERVER['DOCUMENT_ROOT']));
        $where = "{$info['file']}:{$info['line']}";
        $str = $where."\r\n".$message."\r\n";
        file_put_contents($log_file, $str, FILE_APPEND);
    }
}

if (!function_exists('CheckDirPath')) {
    function CheckDirPath($path, $bPermission = true)
    {
        $path = str_replace(array("\\", "//"), "/", $path);

        //remove file name
        if (substr($path, -1) != "/") {
            $p = strrpos($path, "/");
            $path = substr($path, 0, $p);
        }

        $path = rtrim($path, "/");

        if ($path == "") {
            //current folder always exists
            return true;
        }

        if (!file_exists($path)) {
            return mkdir($path, 0775, true);
        }

        return is_dir($path);
    }
}

if (!function_exists('debugfileHTML')) {
    function debugfileHTML($message, $logName = "", $fileName = "debug", $path = "/upload/EGORdebug/")
    {
        $logName = $logName ? (string)$logName : "";
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        } elseif (is_bool($message)) {
            $message = $message ? "true" : "false";
        } elseif (is_null($message)) {
            $message = "null";
        }
        $time = (new \DateTime())->format("H:i:s.u d:m:Y");
        $log_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        CheckDirPath($log_path, true);
        $log_file = $log_path . $fileName . ".html";
        $info = debug_backtrace();
        $info = $info[0];
        $info['file'] = substr($info['file'], strlen($_SERVER['DOCUMENT_ROOT']));
        $where = "{$info['file']}:{$info['line']}";
        $log = '<table style="border:1px solid #008B8B;margin:2px;">
               <tr><td><div style="font-family:verdana; font-size: 10px; font-weight: normal">' . $time . '</div></td></tr>
               <tr><td><div style="font-family:verdana; font-size: 10px; font-weight: normal">' . $where . '</div></td></tr>' .
            ($logName != "" ? '<tr><td><div style="font-family:verdana; font-size: 12px; font-weight: bold">' . $logName . '</div></td></tr>' : "")
            . '<tr><td><hr></td></tr>
               <tr><td><pre>' . $message . '</pre></td></tr>
           </table>';
        file_put_contents($log_file, $log . "\r\n", FILE_APPEND);
    }
}

if (!function_exists('debugDB')) {
    /*
     * Использование. Сначала вызываем debugDB() для инициализации счетчика
     * После кода вызывыаем debugDB(0, #ПАРАМЕТРЫ#)
     * $type 'D' - debug, 'F' - File, 'T' - включена трассировка, 'M' - микротайм, 'L' - время по меткам
     * $typefile 1 - debugfile, 2 - debugfileHTML
     */
    function debugDB($start = 1, $type = 'D', $typefile = 1, $file = "debugDB.dbg", $path = "/upload/deb/")
    {
        global $connectionDBDebug;
        global $timeDBDebug;
        $queryListNew = [];
        if ($start == 1) {
            $timeDBDebug = \Bitrix\Main\Diag\Helper::getCurrentMicrotime();
            \Bitrix\Main\Diag\Debug::startTimeLabel("timeLabelDBDebug");
            $connectionDBDebug = \Bitrix\Main\Application::getInstance()->getConnectionPool()->getConnection();
            $connectionDBDebug->startTracker(true);
        } else {
            if (empty($connectionDBDebug)) {
                $queryListNew[] = 'Ошибка: Трекер не запущен.';
            } else {
                $connectionDBDebug->stopTracker();
                $queryListNew = [];
                foreach ($connectionDBDebug->getTracker() as $key => $value) {
                    $queryListNew[$key]["Время"] = $value->getTime();
                    $queryListNew[$key]["Запрос"] = $value->getSql();
                    if (stripos($type, 'T') !== false) {
                        $queryListNew[$key]["Трассировка"] = $value->getTrace();
                    }
                }
                \Bitrix\Main\Diag\Debug::endTimeLabel("timeLabelDBDebug");
                $times = \Bitrix\Main\Diag\Debug::getTimeLabels();
                $timeDBDebug = \Bitrix\Main\Diag\Helper::getCurrentMicrotime() - $timeDBDebug;
                if (stripos($type, 'D') !== false) {
                    if (stripos($type, 'M') !== false) {
                        debug($timeDBDebug, 'Микротайм');
                    }
                    if (stripos($type, 'L') !== false) {
                        debug($times, 'Время с метками');
                    }
                    debug($queryListNew, 'Запросы');
                }
                if (stripos($type, 'F') !== false) {
                    if (stripos($type, 'M') !== false) {
                        if ($typefile == 1) {
                            debugfile($timeDBDebug, $file, '', $path);
                        } else {
                            debugfileHTML($timeDBDebug, 'Микротайм', $file,  $path);
                        }
                    }
                    if (stripos($type, 'L') !== false) {
                        if ($typefile == 1) {
                            debugfile($times, $file, '', $path);
                        } else {
                            debugfileHTML($times, 'Время с метками', $file,  $path);
                        }
                    }
                    if ($typefile == 1) {
                        debugfile($queryListNew, $file, '', $path);
                    } else {
                        debugfileHTML($queryListNew, 'Запросы', $file,  $path);
                    }
                }
            }
        }
    }
}

if (!function_exists('debugTime')) {
    function debugTime($start = 1, $type = 'D', $file = "debugDB.dbg", $path = "/upload/deb/")
    {
        global $timeDBDebug;
        if ($start == 1) {
            $timeDebug = \Bitrix\Main\Diag\Helper::getCurrentMicrotime();
        } else {
            if (empty($timeDebug)) {
                $queryListNew[] = 'Ошибка: Трекер не запущен.';
            } else {
                $timeDBDebug = \Bitrix\Main\Diag\Helper::getCurrentMicrotime() - $timeDBDebug;
                if (stripos($type, 'D') !== false) {
                    debug($timeDBDebug, 'Время выполнения');
                }
                if (stripos($type, 'F') !== false) {
                    debugfile($timeDBDebug, $file, '', $path);
                }
            }
        }
    }
}

function dd($text)
{
    debugmessage($text);
    die();
}
?>
