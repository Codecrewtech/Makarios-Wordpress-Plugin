<?php
class diageve_hooks {
    function __construct() {
        /** Admin Menu */
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_diageve_save_settings', array($this, 'save_settings'));

        /** Presets */
        add_filter('diageve_bgcolor_presets', array($this, 'color_presets'), 0.01);
        add_filter('diageve_text_color_presets', array($this, 'color_presets'), 0.01);
        add_filter('diageve_date_formats', array($this, 'date_formats'), 0.01);
        add_filter('diageve_bg_grids', array($this, 'bg_grids'), 0.01);

        /** Real thing */
        add_action('wp_head', array($this, 'wp_head'));
        add_action('wp_footer', array($this, 'wp_footer'));
        add_action('wp_ajax_diageve_verify', array($this, 'check_verification'));
        add_action('wp_ajax_nopriv_diageve_verify', array($this, 'check_verification'));
		add_action('wp_print_scripts', array($this, 'unload_scripts'), 900);
    }

    function show_splash() {
		if(is_admin()) return false;
		if(!diageve_enabled()) return false;
		$min_capability = apply_filters('diageve_min_capability', 'intentionally_mispronounced_capability');
		if(current_user_can($min_capability)) return false;
		if($this->is_verified()) return false;
		$inverted = diageve_settings::get('invert_whitelist') ? true : false;
		$list = diageve_settings::get('whitelist');
		if(!is_array($list)) $list = array();
		$curr_url = strtolower(diageve_current_url());
		if(diageve_ends_with($curr_url, '/')) $curr_url = substr($curr_url, 0, strlen($curr_url) - 1); //remove "/" at the end if found


		$verify = false;
		//Whitelist is empty, block or allow all
		if(empty($list)) {
			$verify = !$inverted;
		}
		//White list is not empty, filter urls then
		else {
			$found = false;
			foreach($list as $url) {
				$url = strtolower($url);
				//URL ends with
				if(diageve_starts_with($url, '*') && !diageve_ends_with($url, '*')) {
					$url = substr($url, 1, strlen($url) - 1); //without first *
					if(diageve_ends_with($url, '/')) $url = substr($url, 0, strlen($url) - 1); //remove "/" at the end if found
					if(diageve_ends_with($curr_url, $url)) {
						$found = true;
						break;
					}
				}
				//URL contains
				elseif(diageve_starts_with($url, '*') && diageve_ends_with($url, '*')) {
					$url = substr($url, 1, strlen($url) - 1); //without first *
					$url = substr($url, 0, strlen($url) - 1); //without last *
					if(diageve_ends_with($url, '/')) $url = substr($url, 0, strlen($url) - 1); //remove "/" at the end if found
					if(strpos($curr_url, $url) !== false) {
						$found = true;
						break;
					}
				}
				//URL starts with
				elseif(!diageve_starts_with($url, '*') && diageve_ends_with($url, '*')) {
					$url = substr($url, 0, strlen($url) - 1); //without last *
					if(diageve_ends_with($url, '/')) $url = substr($url, 0, strlen($url) - 1); //remove "/" at the end if found
					if(diageve_starts_with($curr_url, $url)) {
						$found = true;
						break;
					}
				}
				//URL fully matches
				else {
					if(diageve_ends_with($url, '/')) $url = substr($url, 0, strlen($url) - 1); //remove "/" at the end if found
					if(diageve_ends_with($curr_url, $url)) {
						$found = true;
						break;
					}
				}
			}
			if($found) {
				if($inverted) $verify = true;
				else $verify = false;
			}
			else {
				if($inverted) $verify = false;
				else $verify = true;
			}
		}
		if(!$verify)
			return false;

		return true;
	}
    /**
     *
     */
    function wp_head() {
		if($this->show_splash()) {
			wp_enqueue_script('diageve-main', DIAGEVE_URL.'assets/js/diageve.js', array('jquery', 'jquery-form'), false, true);
			wp_enqueue_style('diageve-font-awesome', DIAGEVE_URL.'assets/css/fontawesome/css/font-awesome.min.css');
		}
    }
    function wp_footer() {
    	if($this->show_splash()) {
			$this->splash_screen();
		}
	}
    function unload_scripts() {
		if($this->show_splash()) {
			global $wp_scripts, $wp_styles;
			$wp_scripts->queue = array();
			$wp_styles->queue = array();
		}
	}
    function splash_screen() {
        $tpl = diageve_template_file('splash.php');
        if(file_exists($tpl)) {
            include $tpl;
        }
    }
    function check_verification() {
        if(!diageve_enabled()) diageve_ajax_response(1);
        $d = (int)diageve_pg('d') > 9 ? (int)diageve_pg('d') : "0".(int)diageve_pg('d');
        $m = (int)diageve_pg('m') > 9 ? (int)diageve_pg('m') : "0".(int)diageve_pg('m');
        $y = (int)diageve_pg('y');
        $bday = "$y-$m-$d";
        if(!diageve_is_date($bday))
            diageve_ajax_response(0, __('Please enter a valid date of birth.', 'diageve'));

        $required = (int)diageve_settings::get('age');
        $url = diageve_settings::get('fallback_url');
        if(empty($url)) $url = get_bloginfo('url');
        if(diageve_age_from_date($bday) < $required)
            diageve_ajax_response(0, __('You are too young for this content.', 'diageve'), array('redirect'=>$url));
        else {
            $this->verify($bday);
            diageve_ajax_response(1);
        }
    }
    private function is_verified() {
        $age = @$_COOKIE['diageve-age'];
        $required = (int)diageve_settings::get('age');
        if(empty($age)) return false;
        if(!diageve_is_date($age)) return false;
        if(diageve_age_from_date($age) < $required) return false;
        return true;
    }
    private function verify($age) {
        $life = (int)diageve_settings::get('cookie_life');
        if($life < 1) $life = 1;
        setcookie('diageve-age', $age, time() + $life * (60*60*24), '/', null);
    }

    function bg_grids($grids) {
        $g = array(
            DIAGEVE_URL.'assets/img/grid-8x8-black.png'=>__('8x8 Black', 'diageve'),
            DIAGEVE_URL.'assets/img/grid-8x8-white.png'=>__('8x8 White', 'diageve'),
            DIAGEVE_URL.'assets/img/grid-16x16-black.png'=>__('16x16 Black', 'diageve'),
            DIAGEVE_URL.'assets/img/grid-16x16-white.png'=>__('16x16 White', 'diageve'),
            DIAGEVE_URL.'assets/img/grid-32x32-black.png'=>__('32x32 Black', 'diageve'),
            DIAGEVE_URL.'assets/img/grid-32x32-white.png'=>__('32x32 White', 'diageve'),
        );
        return array_merge($grids, $g);
    }
    function date_formats($dates) {
        $date_format = array(
            'd/m'=>'DD/MM/YYYY',
            'm/d'=>'MM/DD/YYYY'
        );
        return array_merge($dates, $date_format);
    }
    /**
     * Color presets for configuration page
     *
     * @filter diageve_bgcolor_presets
     * @filter diageve_text_color_presets
     *
     * @param $presets
     * @return array
     */
    function color_presets($presets) {
        $new = array(
            '1dd2af'=>__('Turquoise', 'diageve'),
            '16a085'=>__('Green sea', 'diageve'),
            '2ecc71'=>__('Emerald', 'diageve'),
            '27ae60'=>__('Nephritis', 'diageve'),
            'f1c40f'=>__('Sunflower', 'diageve'),
            'f39c12'=>__('Orange', 'diageve'),
            'e67e22'=>__('Carrot', 'diageve'),
            'd35400'=>__('Pumpkin', 'diageve'),
            'e74c3c'=>__('Alizarin', 'diageve'),
            'c0392b'=>__('Pomegranate', 'diageve'),
            '3498db'=>__('Peter River', 'diageve'),
            '2980b9'=>__('Belize Hole', 'diageve'),
            '9b59b6'=>__('Amethyst', 'diageve'),
            '8e44ad'=>__('Wisteria', 'diageve'),
            '34495e'=>__('Wet asphalt', 'diageve'),
            '2c3e50'=>__('Midnight blue', 'diageve'),
            '95a5a6'=>__('Concrete', 'diageve'),
            '7f8c8d'=>__('Asbestos', 'diageve'),
            'ecf0f1'=>__('Clouds', 'diageve'),
            'bdc3c7'=>__('Silver', 'diageve')
        );
        return array_merge($presets, $new);
    }
    function save_settings() {
        $page = diageve_pg('option_page');
        $pages = array('diageve-configuration', 'diageve-appearance');
        if(!in_array($page, $pages)) {
            die('Cheating huh?');
        }
        $nonce = $page == 'diageve-configuration' ? 'configuration' : 'appearance';
        if(!diageve_nonce_check($nonce)) {
            wp_safe_redirect("admin.php?page=$page&error=1");
            exit;
        }
        $res = false;
        if($page == 'diageve-appearance') {
            $res = diageve_settings::set(array(
                'content'=>diageve_preclean_content(diageve_pg('content')),
                'text_color'=>diageve_pg('text_color'),
                'bg_color'=>diageve_pg('bg_color'),
                'bg_image'=>diageve_pg('bg_image'),
                'bg_image_repeat'=>diageve_pg('bg_image_repeat'),
                'bg_image_position'=>diageve_pg('bg_image_position'),
                'bg_grid'=>diageve_pg('bg_grid'),
                'bg_grid_opacity'=>(int)diageve_pg('bg_grid_opacity') > 1 ? 1 : number_format((float)diageve_pg('bg_grid_opacity'), 2),
                'custom_css'=>diageve_preclean_content(diageve_pg('custom_css'))
            ));
            $res = true;
        }
        elseif($page == 'diageve-configuration') {
            $res = diageve_settings::set(array(
                'enabled'=>diageve_isset_pg('enabled'),
                'invert_whitelist'=>diageve_isset_pg('invert_whitelist'),
                'age'=>(int)diageve_pg('age') > 0 ? (int)diageve_pg('age') : 1,
                'fallback_url'=>diageve_pg('fallback_url'),
                'cookie_life'=>diageve_pg('cookie_life'),
                'date_format'=>diageve_pg('date_format'),
                'whitelist'=>diageve_pg('whitelist')
            ));
        }
        wp_safe_redirect("admin.php?page=$page&success=1");
        exit;
    }
    function admin_menu() {
        add_menu_page(
            __('Configuration | Diwave Age Verificator', 'diageve'),
            __('Age Verificator', 'diageve'),
            'ga4356-34asfguhq345', 'diageve', array($this, 'admin_page'));
        add_submenu_page('diageve',
            __('Configuration | Diwave Age Verificator', 'diageve'),
            __('Configuration', 'diageve'),
            'manage_options', 'diageve-configuration', array($this, 'admin_page'));
        add_submenu_page('diageve',
            __('Appearance | Diwave Age Verificator', 'diageve'),
            __('Appearance', 'diageve'),
            'manage_options', 'diageve-appearance', array($this, 'admin_appearance_page'));
    }
    function admin_page() {
        $this->scripts();
        $tpl = diageve_template_file('admin/settings.php', false);
        $response_msg = '';
        $is_error = false;
        if(diageve_pg('error', true, 0) == 1) {
            $response_msg = DIAGEVE_PERMISSION_ERR;
            $is_error = true;
        }
        elseif(diageve_pg('error', true, 0) == 2) {
            $response_msg = __('Settings could not be saved. Please try again.', 'diageve');
            $is_error = true;
        }
        if(diageve_isset_pg('success')) {
            $is_error = false;
            $response_msg = __('Settings saved.', 'diageve');
        }
        include $tpl;
    }
    function admin_appearance_page() {
        $this->scripts();
        $tpl = diageve_template_file('admin/appearance.php', false);
        $response_msg = '';
        $is_error = false;
        if(diageve_pg('error', false, 0) == 1) {
            $response_msg = DIAGEVE_PERMISSION_ERR;
            $is_error = true;
        }
        elseif(diageve_pg('error', false, 0) == 2) {
            $response_msg = __('Settings could not be saved. Please try again.', 'diageve');
            $is_error = true;
        }
        if(diageve_isset_pg('success')) {
            $is_error = false;
            $response_msg = __('Settings saved.', 'diageve');
        }
        include $tpl;
    }
    private function scripts() {
        wp_enqueue_media();
        wp_enqueue_style('diageve-admin', DIAGEVE_URL.'assets/css/admin/main.css');
        wp_enqueue_style('font-awesome', DIAGEVE_URL.'assets/css/fontawesome/css/font-awesome.min.css');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('diageve-admin', DIAGEVE_URL.'assets/js/admin/main.js', array('wp-color-picker'));
        wp_localize_script('diageve-admin', 'diageve_lang', array(
            'item_already_exists'=>__('Item already exists in the list.', 'diageve'),
            'item_removal_prompt'=>__('Are you sure you want to remove this item from the list?', 'diageve')
        ));
    }
}