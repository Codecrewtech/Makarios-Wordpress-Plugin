<?php
$css = array();

$bg_color = diageve_settings::get('bg_color');
$bg_image = diageve_settings::get('bg_image');
$bg_repeat = diageve_settings::get('bg_image_repeat');
$bg_position = diageve_settings::get('bg_image_position');
if(!empty($bg_position)) {
    if(is_numeric(@$bg_position[0])) @$bg_position[0] .= "px";
    if(is_numeric(@$bg_position[1])) @$bg_position[1] .= "px";
}
$text_color = diageve_settings::get('text_color');

$bg_grid = diageve_settings::get('bg_grid');
$bg_grid_opacity = diageve_settings::get('bg_grid_opacity');

if(!empty($bg_color)) {
    $css['.diageve-splash']['background-color'] = $bg_color;
    $css['.diageve-splash']['text-shadow'] = "0 0 1px $bg_color";
    $css['body']['background-color'] = $bg_color;

    if(diageve_get_brightness($bg_color) < 220) :
        $css['.diageve-form .diageve-date']['background'] = diageve_set_brightness($bg_color, -15);
        $css['.diageve-form .diageve-date']['border-color'] = diageve_set_brightness($bg_color, 25);
        $css['.diageve-form .diageve-date input']['border-color'] = diageve_set_brightness($bg_color, 25);
    else :
        $css['.diageve-form .diageve-date']['background'] = diageve_set_brightness($bg_color, 20);
        $css['.diageve-form .diageve-date']['border-color'] = diageve_set_brightness($bg_color, -30);
        $css['.diageve-form .diageve-date input']['border-color'] = diageve_set_brightness($bg_color, -30);
        $css['.diageve-form .diageve-date input']['color'] = $text_color;
    endif;
    if(diageve_get_brightness($bg_color) > 200) :
        $css['.diageve-form .diageve-date']['box-shadow'] = '0 0 35px '.diageve_hexToRgb(diageve_set_brightness($bg_color, -30)).', inset 0 0 0 1px rgba(0,0,0,0.2)';
        $css['.diageve-description']['box-shadow'] = '0 0 35px '.diageve_hexToRgb(diageve_set_brightness($bg_color, -30));
    else:
        $css['.diageve-form .diageve-date']['box-shadow'] = '0 0 35px rgba(255,255,255,0.1), inset 0 0 0 1px rgba(0,0,0,0.2)';
        $css['.diageve-description']['box-shadow'] = '0 0 65px rgba(255,255,255,0.05)';

    endif;
    if(diageve_get_brightness($text_color) > 120) :
        $css['.diageve-description']['background'] = 'rgba(0,0,0,0.1)';
    else:
        $css['.diageve-description']['background'] = 'rgba(255,255,255,0.1)';
    endif;
    if($text_color != $bg_color) :
        $css['.diageve-form .diageve-submit button']['color'] = $bg_color;
        $css['.diageve-form .diageve-date input']['color'] = $text_color;
    else :
        $css['.diageve-form .diageve-date input']['color'] =  diageve_get_brightness($bg_color) < 120 ? diageve_set_brightness($text_color, 200) : diageve_set_brightness($text_color, -200);
        $css['.diageve-form .diageve-submit button']['color'] = diageve_get_brightness($text_color) < 120 ? diageve_set_brightness($bg_color, 200) : diageve_set_brightness($bg_color, -200);
    endif;
    $css['.diageve-form .diageve-submit button']['background'] = $text_color;
    $css['.diageve-form .diageve-submit button:hover']['border-color'] = $text_color;
    $css['.diageve-form .diageve-submit button:hover']['background'] = 'transparent';
    $css['.diageve-form .diageve-submit button:hover']['color'] = $text_color;
}
if(!empty($bg_image)) {
    $css['.diageve-splash']['background-image'] = "url($bg_image)";
}
if(!empty($bg_repeat)) {
    $css['.diageve-splash']['background-repeat'] = $bg_repeat;
}
if(!empty($bg_position)) {
    $css['.diageve-splash']['background-position'] = implode(' ',$bg_position);
}
if(!empty($text_color)) {
    $css['.diageve-splash']['color'] = $text_color;
}

if(!empty($bg_grid)) $css['.diageve-splash-grid']['background'] = "url($bg_grid) repeat 0 0";
if($bg_grid_opacity !== '') $css['.diageve-splash-grid']['opacity'] = $bg_grid_opacity;

include_once DIAGEVE_DIR.'/assets/css/style.css';
foreach($css as $k=>$data) {
    echo "$k {\n";
    foreach ($data as $x => $y) {
        echo "$x: $y;\n";
    }
    echo "}\n";
}

echo diageve_settings::get('custom_css');