<?php
function diageve_enabled() {
    if(!diageve_settings::get('enabled')) return false;
    $required = (int)diageve_settings::get('age');
    if($required < 1) return false;
    return true;
}
function diageve_template_file($path, $extendable = true, $tpl_dir = '') {
    if(empty($path)) return '';
    if(empty($tpl_dir)) $tpl_dir = DIAGEVE_DIR."/templates/";
    if(!diageve_ends_with($tpl_dir, '/'))  $tpl_dir .= '/';
    if(diageve_starts_with($path, '/')) $path = substr($path, 1, strlen($path) - 1);
    $file = $tpl_dir.$path;

    if($extendable && file_exists(get_stylesheet_directory()."/diageve/$path"))
        $file = get_stylesheet_directory()."/diageve/$path";

    if(!file_exists($file) || (file_exists($file) && is_dir($file))) $file = '';
    return $file;
}
function diageve_starts_with($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}
function diageve_ends_with($haystack, $needle) {
    return substr($haystack, strlen($haystack) - strlen($needle), strlen($needle)) === $needle;
}
function diageve_pg($key, $post_has_priority=true, $default=null) {
    if($post_has_priority) {
        if(isset($_POST[$key])) return $_POST[$key];
        elseif(isset($_GET[$key])) return $_GET[$key];
    }
    else {
        if(isset($_GET[$key])) return $_GET[$key];
        elseif(isset($_POST[$key])) return $_POST[$key];
    }
    return $default;
}
function diageve_isset_pg($key, $post_has_priority = true) {
    if($post_has_priority) {
        if(isset($_POST[$key])) return true;
        elseif(isset($_GET[$key])) return true;
    }
    else {
        if(isset($_GET[$key])) return true;
        elseif(isset($_POST[$key])) return true;
    }
    return false;
}
function diageve_nonce_field($action) {
    $action = 'diageve-'.$action;
    $nonce = wp_create_nonce($action);
    echo "<input type=\"hidden\" name=\"_wpnonce\" value=\"$nonce\" />";
}
function diageve_nonce_check($action) {
    $action = 'diageve-'.$action;
    $nonce = diageve_pg('_wpnonce', true, '');
    if(wp_verify_nonce( $nonce, $action )) return true;
    return false;
}
function diageve_response($result = 1, $message = '', $data = array(), $reload = 0, $echo = false, $die = true) {
    $arr = array('result'=>$result, 'message'=>$message, 'data'=>$data, 'reload'=>$reload ? 1 : 0);
    if($echo) {
        echo json_encode($arr);
        if($die) die();
    }
    return json_encode($arr);
}
function diageve_ajax_response($result = 1, $message = '', $data = array(), $reload = 0) {
    diageve_response($result, $message, $data, $reload, true);
}
function diageve_clean_html_content($string,$html_tags="", $autop = true) {
    $string = diageve_preclean_content($string);
    if(empty($html_tags)) $html_tags = "<b><strong><em><i><br><hr><p><span><small><h1><h2><h3><h4><h5><ul><ol><li><a><del><blockquote><pre><code>";

    $string = strip_tags($string,$html_tags);
    if($autop) $string = wpautop($string);
    return $string;
}
function diageve_preclean_content($string) {
    $rplc = array(
        "\\'"=>"'",
        '\\"'=>'"',
        "\\\\"=>"\\"
    );
    $string = str_replace(array_keys($rplc),array_values($rplc), $string);
    $string = str_replace(array_keys($rplc),array_values($rplc), $string);
    return $string;
}

/**
 * Escape single quotes in a string
 * @param $string
 * @return mixed
 */
function diageve_escape_quote($string) {
    return str_replace("'", "\\'", $string);
}

/**
 * Escape double quotes in a string
 * @param $string
 * @return mixed
 */
function diageve_escape_quotes($string) {
    return str_replace('"', '\"', $string);
}
function diageve_current_url() {
    $url = is_ssl() ? "https://" : "http://";
    $url .= "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return $url;
}
function diageve_is_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
}
function diageve_age_from_date($bdate) {
    $age = floor((time() - strtotime($bdate)) / 31556926);
    return (int)$age;
}
// returns brightness value from 0 to 255
function diageve_get_brightness($hex) {
    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}
function diageve_set_brightness($hex, $diff) {
    $rgb = str_split(trim($hex, '# '), 2);
    foreach ($rgb as &$hex) {
        $dec = hexdec($hex);
        if ($diff >= 0) $dec += $diff;
        else $dec -= abs($diff);
        $dec = max(0, min(255, $dec));
        $hex = str_pad(dechex($dec), 2, '0', STR_PAD_LEFT);
    }
    return "#".implode($rgb);
}
function diageve_hexToRgb($hex, $alpha = 1) {
    if(empty($hex)) return '';
    if ($hex[0] == '#' ) $hex = substr( $hex, 1 );
    if (strlen($hex) == 6)
        $hex = array( $hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5] );
    elseif ( strlen( $hex ) == 3 )
        $hex = array( $hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2] );
    else
        return '';

    $rgb =  array_map('hexdec', $hex);
    if($alpha) {
        if(abs($alpha) > 1) $alpha = 1.0;
        $output = 'rgba('.implode(",",$rgb).','.$alpha.')';
    } else
        $output = 'rgb('.implode(",",$rgb).')';

    return $output;
}