<?php
$color_presets = apply_filters('diageve_text_color_presets', array());
$bg_color_presets = apply_filters('diageve_bgcolor_presets', array());

$img_repeat = array(
    'repeat'=>__('Repeat', 'diageve'),
    'repeat-x'=>__('Horizontally', 'diageve'),
    'repeat-y'=>__('Vertically', 'diageve'),
    'no-repeat'=>__('No repeat', 'diageve')
);
$grids = apply_filters('diageve_bg_grids', array());
$bg_pos = diageve_settings::get('bg_image_position', 0);
if(!is_array($bg_pos)) $bg_pos = array(0, 0);
?>
<div class="wrap">
    <h2><i class="fa fa-paint-brush"></i> <?php _e('Appearance', 'diageve')?></h2>
    <?php if(!empty($response_msg)) : ?>
        <div class="<?php echo $is_error ? 'error' : 'updated'?> settings-error notice is-dismissible">
            <p><strong><?php echo $response_msg ?></strong></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.', 'diageve')?></span></button></div>
    <?php endif; ?>
    <form method="post" action="<?php echo admin_url('admin-ajax.php')?>" novalidate="novalidate">
        <input name="option_page" value="<?php echo @$_GET['page']?>" type="hidden">
        <input name="action" value="diageve_save_settings" type="hidden">
        <?php diageve_nonce_field('appearance') ?>
        <table class="form-table">
            <tbody>
            <tr>
                <th class="diageve-title" colspan="2">
                    <h3><?php _e('Content', 'diageve')?></h3>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="content"><?php _e('Text', 'diageve')?></label></th>
                <td>
                    <?php
                    wp_editor(diageve_settings::get('content'), 'diageve-content', array(
                        'textarea_name'=>'content',
                        'textarea_rows'=>8
                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="text_color"><?php _e('Overall Text Color', 'diageve')?></label></th>
                <td>
                    <input name="text_color" id="text_color" value="<?php echo diageve_settings::get('text_color')?>" class="color-picker" type="text">
                    <?php
                    if(is_array($color_presets) && !empty($color_presets)) : ?>
                        <ul class="diageve-color-presets" data-input="#text_color">
                            <?php foreach($color_presets as $color=>$name) : ?>
                                <li title="<?php echo $name?>" data-color="<?php echo $color?>" >
                                    <div class="diageve-color-box" style="background-color: <?php echo "#$color"?>"></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="form-table">
            <tbody>
            <tr>
                <th class="diageve-title" colspan="2">
                    <h3><?php _e('Background', 'diageve')?></h3>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="bg_image"><?php _e('Background Image', 'diageve')?></label></th>
                <td>
                    <div class="diageve-file-picker">
                        <input name="bg_image" id="bg_image" placeholder="<?php _e('Enter URL or browse local files...', 'diageve')?>" value="<?php echo diageve_settings::get('bg_image')?>" class="file-picker" type="text">
                        <button type="button" class="button" title="<?php _e('Browse files', 'diageve')?>">...</button>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bg_image_repeat"><?php _e('Background Repeat', 'diageve')?></label></th>
                <td>
                    <select id="bg_image_repeat" name="bg_image_repeat">
                        <?php foreach($img_repeat as $k=>$v) : ?>
                            <option value="<?php echo $k?>" <?php echo diageve_settings::get('bg_image_repeat') == $k ? 'selected' : ''?>><?php echo $v?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bg_image_position-x"><?php _e('Background Position', 'diageve')?></label></th>
                <td>
                    <?php _e('Left:', 'diageve')?>
                    <input type="text" id="bg_image_position-x" style="width: 80px" name="bg_image_position[0]" value="<?php echo $bg_pos[0]?>" />
                    &nbsp; &nbsp;
                    <?php _e('Top:', 'diageve')?>
                    <input type="text" id="bg_image_position-y" style="width: 80px" name="bg_image_position[1]" value="<?php echo $bg_pos[1]?>" />
                    <br />
                    <small><em><?php _e('You can use either pixel (px) or percent (%) units. If no units are set, values will be treated as pixels.', 'diageve')?></em></small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bg_color"><?php _e('Background Color', 'diageve')?></label></th>
                <td>
                    <input name="bg_color" id="bg_color" value="<?php echo diageve_settings::get('bg_color')?>" class="color-picker" type="text">
                    <?php

                    if(is_array($bg_color_presets) && !empty($bg_color_presets)) : ?>
                        <ul class="diageve-color-presets" data-input="#bg_color">
                            <?php foreach($bg_color_presets as $color=>$name) : ?>
                                <li title="<?php echo $name?>" data-color="<?php echo $color?>" >
                                    <div class="diageve-color-box" style="background-color: <?php echo "#$color"?>"></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bg_grid"><?php _e('Background Grid', 'diageve')?></label></th>
                <td>
                    <select id="bg_grid" name="bg_grid">
                        <option value=""><?php _e('None', 'diageve')?></option>
                        <?php foreach($grids as $k=>$v) : ?>
                            <option value="<?php echo $k?>" <?php echo diageve_settings::get('bg_grid') == $k ? 'selected' : ''?>><?php echo $v?></option>
                        <?php endforeach; ?>
                    </select>
                    &nbsp; &nbsp; &nbsp;
                    <?php _e('Opacity:', 'diageve')?>
                    <input type="text" pattern="[0-9]*" placeholder="<?php _e('ex. 1.0', 'diageve')?>" name="bg_grid_opacity" value="<?php echo number_format(diageve_settings::get('bg_grid_opacity'), 2)?>" />
                </td>
            </tr>
            </tbody>
        </table>

        <table class="form-table">
            <tbody>
            <tr>
                <th class="diageve-title" colspan="2">
                    <h3><?php _e('Custom CSS', 'diageve')?></h3>
                </th>
            </tr>
            <tr>
                <th scope="row"><label for="custom_css"><?php _e('Code', 'diageve')?></label></th>
                <td>
                    <textarea name="custom_css" rows="8" id="custom_css"><?php echo diageve_settings::get('custom_css')?></textarea>
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