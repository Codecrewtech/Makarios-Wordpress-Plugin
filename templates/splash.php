<style>
    <?php include DIAGEVE_DIR.'/assets/css/style.php'; ?>
</style>
<div class="diageve-splash">
    <div class="diageve-splash-grid"></div>
    <div class="diageve-splash-content">
        <div class="diageve-description">
            <?php echo wpautop(diageve_settings::get('content')) ?>
        </div>
        <div class="diageve-form">
            <form id="diageve-frm" action="<?php echo admin_url('admin-ajax.php')?>" method="post">
                <input type="hidden" name="action" value="diageve_verify" />
                <div id="diageve-frm-response"></div>
                <div class="diageve-date">
                    <?php if(diageve_settings::get('date_format') == 'd/m') : ?>
                        <input type="text" maxlength="2" pattern="[0-9]*" placeholder="<?php echo _x('DD', 'Date field placeholder', 'diageve')?>" name="d" />
                        <input type="text" maxlength="2" pattern="[0-9]*" placeholder="<?php echo _x('MM', 'Date field placeholder', 'diageve')?>" name="m" />
                    <?php else : ?>
                        <input type="text" maxlength="2" pattern="[0-9]*" placeholder="<?php echo _x('MM', 'Date field placeholder', 'diageve')?>" name="m" />
                        <input type="text" maxlength="2" pattern="[0-9]*" placeholder="<?php echo _x('DD', 'Date field placeholder', 'diageve')?>" name="d" />
                    <?php endif; ?>
                    <input type="text" maxlength="4" pattern="[0-9]*" class="diageve-year" placeholder="<?php echo _x('YYYY', 'Date field placeholder', 'diageve')?>" name="y" />
                </div>
                <div class="diageve-submit">
                    <button type="submit"><?php _e('Verify', 'diageve')?></button>
                </div>
            </form>
        </div>
    </div>
</div>