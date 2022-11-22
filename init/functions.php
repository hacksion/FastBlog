<?php
function autoloadClass($class){
    $class_namespace = explode("\\", $class);
    $class_file_name = SERVER_DIR['CLASS'] . end($class_namespace) . '.php';
    if (is_readable($class_file_name)) {
        require $class_file_name;
        return true;
    } else {
        return false;
    }
}

function debug(){
    $arr = debug_backtrace();
    echo '<div>' . $arr[ 0 ][ 'file' ] . '  ' . $arr[ 0 ][ 'line' ] . '</div>';
    $args = func_get_args();
    echo '<pre style="border:1px solid #CCC; padding: 5px; font-family: monospace; font-size: 12px;">';
    foreach ($args as $val) {
        print_r($val);
    }
    echo '</pre>';
}

function dayOfWeek($lang = 'ja'){
    $result = [
        'en' => [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ],
        'ja' => [
            0 => '日',
            1 => '月',
            2 => '火',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
        ]
    ];
    return $result[$lang];
}

function hex2rgb ( $hex ) {
    if ( substr( $hex, 0, 1 ) == "#" ) $hex = substr( $hex, 1 ) ;
    if ( strlen( $hex ) == 3 ) $hex = substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) ;
    return array_map( "hexdec", [ substr( $hex, 0, 2 ), substr( $hex, 2, 2 ), substr( $hex, 4, 2 ) ] ) ;
}

function numberK($number=0){
    return $number > 1000 ? ($number / 1000).'k':number_format($number);
}

function trth($loop=[1,2], $rep=[], $ext = '')
{
    $result = '<tr>';
    for($i = $loop[0]; $i < $loop[1]; $i++){
        $result .= '<th>'.($rep ? $rep[$i]:$i).$ext.'</th>';
    }
    $result .= '</tr>';
    return $result;
}

function trtd($data, $loop=[1,2], $kflag=false)
{
    $result = '<tr>';
    for($i = $loop[0]; $i < $loop[1]; $i++){
        $value = $data[$i];
        if($kflag){
            $value = numberK($data[$i]);
        }
        $result .= '<td class="data">'.$value.'</td>';
    }
    $result .= '</tr>';
    return $result;
}

function strWidth($text, $width = 10)
{
    $result = "";
    if ($text) {
        $result = mb_strimwidth($text, 0, $width, "...", "utf8");
    }
    return $result;
}

function colFactory($column, $table=null)
{
    $result = [];
    foreach($column as $value){
        $result[] = $table ? '`'.$table.'`.`'.$value.'`':'`'.$value.'`';
    }
    return implode(',', $result);
}

function imageOrientation($filename, $orientation)
{
    $image = imagecreatefromjpeg($filename);
    $degrees = 0;
    switch ($orientation) {
        case 1:		//回転なし（↑）
            return;
        case 8:		//右に90度（→）
            $degrees = 90;
            break;
        case 3:		//180度回転（↓）
            $degrees = 180;
            break;
        case 6:		//右に270度回転（←）
            $degrees = 270;
            break;
        case 2:		//反転　（↑）
            $mode = IMG_FLIP_HORIZONTAL;
            break;
        case 7:		//反転して右90度（→）
            $degrees = 90;
            $mode = IMG_FLIP_HORIZONTAL;
            break;
        case 4:		//反転して180度なんだけど縦反転と同じ（↓）
            $mode = IMG_FLIP_VERTICAL;
            break;
        case 5:		//反転して270度（←）
            $degrees = 270;
            $mode = IMG_FLIP_HORIZONTAL;
            break;
    }

    if (isset($mode)) {
        $image = imageflip($image, $mode);
    }

    if ($degrees > 0) {
        $image = imagerotate($image, $degrees, 0);
    }

    ImageJPEG($image, $filename);

    imagedestroy($image);
}