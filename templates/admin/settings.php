<?php
$date_format = apply_filters('diageve_date_formats', array());
?>
<div class="wrap">
    <h2><i class="fa fa-cogs"></i> <?php _e('Configuration', 'diageve')?></h2>
    <?php if(!empty($response_msg)) : ?>
        <div class="<?php echo $is_error ? 'error' : 'updated'?> settings-error notice is-dismissible">
            <p><strong><?php echo $response_msg ?></strong></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'diageve')?></span></button></div>
    <?php endif; ?>
    <form method="post" action="<?php echo admin_url('admin-ajax.php')?>" novalidate="novalidate">
        <input name="option_page" value="<?php echo @$_GET['page']?>" type="hidden">
        <input name="action" value="diageve_save_settings" type="hidden">
        <?php diageve_nonce_field('configuration') ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th class="diageve-title" colspan="2">
                    <h3><?php _e('General', 'diageve')?></h3>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="enabled"><?php _e('Enable Age Verificator', 'diageve')?></label></th>
                <td><label><input type="checkbox" id="enabled" <?php checked(diageve_enabled())?> name="enabled" value="1" />
                        <?php _e('Enable', 'diageve')?>
                    </label><br />
                    <small><em><?php printf(__('Before enabling verificator, we suggest you customize verification page to your taste using <a href="%s" target="_blank">appearance page</a>.', 'diageve'), admin_url('admin.php?page=diageve-appearance'))?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="age"><?php _e('Required Age', 'diageve')?></label></th>
                <td><input type="number" min="1" id="age" name="age" value="<?php echo (int)diageve_settings::get('age')?>" /> <?php _e('years old', 'diageve')?><br />
                    <small><em><?php _e('Minimal age required to view your website.', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="fallback_url"><?php _e('Fallback URL', 'diageve')?></label></th>
                <td><input type="text" style="width: 100%" id="fallback_url" name="fallback_url" placeholder="<?php printf(__('eg. %s', 'diageve'), get_bloginfo('url'))?>" value="<?php echo diageve_settings::get('fallback_url')?>" /><br />
                    <small><em><?php _e('Please provide full URL to page where visitor will be redirected if his age does not meet requirements. <strong>Also make sure this URL is in Whitelist below if it\'s an URL on your domain.</strong>', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="cookie_life"><?php _e('Cookie Lifetime', 'diageve')?></label></th>
                <td><input type="number" min="0" id="cookie_life" name="cookie_life" value="<?php echo (int)diageve_settings::get('cookie_life')?>" /> <?php _e('days', 'diageve')?><br />
                    <small><em><?php _e('How long to keep "VERIFIED" cookie in life?', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="date_format"><?php _e('Date Format', 'diageve')?></label></th>
                <td>
                    <select id="date_format" name="date_format">
                        <?php foreach($date_format as $k=>$v) : ?>
                            <option value="<?php echo $k?>" <?php echo diageve_settings::get('date_format') == $k ? 'selected' : ''?>><?php echo $v?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="form-table">
            <tbody>
            <tr>
                <th class="diageve-title" colspan="2">
                    <h3><?php _e('Whitelist', 'diageve')?></h3>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <p>
                        <em><?php _e('Here you can make a list of pages where Age Verificator will be disabled.', 'diageve')?></em>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="invert_whitelist"><?php _e('Invert whitelist', 'diageve')?></label></th>
                <td><label><input type="checkbox" id="invert_whitelist" <?php checked(diageve_settings::get('invert_whitelist'))?> name="invert_whitelist" value="1" />
                        <?php _e('Enable', 'diageve')?>
                    </label><br />
                    <small><em><?php _e('If inverted, Age Verificator will be disabled for all urls but the ones listed.', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="add_new"><?php _e('Add New', 'diageve')?></label></th>
                <td>
                    <div class="diageve-insert-box" data-list="#whitelist">
                        <input type="text" id="add_new" placeholder="<?php printf(__('eg. %s', 'diageve'), get_bloginfo('url').'/contact')?>" />
                        <button type="button" class="button"><?php _e('Add', 'diageve')?></button>
                    </div>
                    <small>
                        <em><?php _e('You can either use full URL or fragments, for example:', 'diageve')?><br /></em>
                        <ul>
                            <li><strong><?php echo get_bloginfo('url')?>/product/5</strong> - <?php _e('Applies only to exact URL.', 'diageve')?></li>
                            <li><strong><?php echo get_bloginfo('url')?>/product/*</strong> - <?php printf(__('Applies to all URLs that start with <strong>%s</strong>.', 'diageve'), get_bloginfo('url').'/product/')?></li>
                            <li><strong>*/product</strong> - <?php printf(__('Applies to all URLs that end with <strong>%s</strong>.', 'diageve'), '/product')?></li>
                            <li><strong>*product*</strong> - <?php printf(__('Applies to all URLs that contain string <strong>%s</strong>.', 'diageve'), 'product')?></li>
                        </ul>
                    </small>
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td>
                    <small><em><?php _e('Double click on an item to remove it from the list.', 'diageve')?></em></small>
                    <select multiple="multiple" name="whitelist[]" id="whitelist">
                        <?php
                        $whitelist = diageve_settings::get('whitelist');
                        if(is_array($whitelist) && !empty($whitelist)) :
                        foreach($whitelist as $item) : ?>
                        <option value="<?php echo $item?>"><?php echo $item?></option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                    <small><em><?php _e('Double click on an item to remove it from the list.', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row">&nbsp;</th>
                <td>
                    <p class="submit">
                        <input name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'diageve')?>" type="submit">
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>