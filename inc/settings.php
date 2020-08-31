<?php
class diageve_settings {
    const prefix = 'diageve_options';
    static function get_defaults() {
        return array(
            'enabled'=>true,
            'age'=>18,
            'fallback_url'=>'',
            'cookie_life'=>1,
            'date_format'=>'m/d',
            'invert_whitelist'=>false,
            'whitelist'=>array(),

            'content'=>__('<i class="fa fa-lock diageve-icon"></i><h3>Page is locked</h3>Please verify your age before accessing this page.', 'diageve'),
            'text_color'=>'#F07637',
            'bg_image'=>DIAGEVE_URL.'assets/img/overlay2.png',
            'bg_color'=>'#231F2A',
            'bg_image_repeat'=>'no-repeat',
            'bg_image_position'=>array('50%', 0),
            'bg_grid'=>DIAGEVE_URL.'assets/img/grid-16x16-white.png',
            'bg_grid_opacity'=>0.1
        );
    }
    static function get_user_defined() {
        $options = get_option(self::prefix, array());
        if(!is_array($options)) $options = array();
        return $options;
    }
    static function get_options() {
        $options = self::get_user_defined();
        $defaults = self::get_defaults();
        return array_merge($defaults, $options);
    }
    static function set($key, $value = '') {
        $options = self::get_user_defined();
        if(is_array($key)) {
            foreach($key as $k=>$v) {
                $options[$k] = $v;
            }
        }
        else $options[$key] = $value;
        return update_option(self::prefix, $options);
    }
    static function get($key, $fallback = null) {
        $options = self::get_options();
        if(isset($options[$key])) return $options[$key];
        else return $fallback;
    }
}