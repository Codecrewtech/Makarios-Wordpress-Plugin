jQuery(document).ready(function() {
    //Save tinyMCE content before submitting the form in order to pass data to request
    jQuery("input[type='submit'], button[type='submit']").on("mousedown",function() {
        if(typeof tinymce != "undefined") {
            tinyMCE.triggerSave();
        }
        if(jQuery('#whitelist').length) {
            jQuery('#whitelist option').prop('selected', 'selected');
        }
    });
    if(typeof jQuery.fn.wpColorPicker != "undefined") {
        jQuery('input.color-picker').wpColorPicker({
            palettes: [],
            width: 200
        });
    }
    jQuery(document).on('click', '.diageve-file-picker button', function(e) {
        e.preventDefault();
        if(typeof wp.media != "undefined") {
            var input = jQuery(this).parent().find('input');
            var image = wp.media({
                multiple: false
            }).open()
                .on('select', function(e){
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    input.val(image_url);
                });
        }
        else {
            alert('Error: WordPress Media Uploader is not loaded.');
        }
    });
    jQuery(document).on('click', '.diageve-color-presets > li', function(e) {
        var t = jQuery(this);
        var color = t.attr('data-color');
        if(color == "") return;
        var input = t.parent().attr('data-input');
        if(input != "") input = jQuery(input);
        if(!input.length) return;
        input.wpColorPicker('color', color);
    });
    jQuery(document).on('click', '.diageve-insert-box button', function(e) {
        e.preventDefault();
        diageve_add_to_list(jQuery(this).parent());
    });
    jQuery(document).on('keydown', '.diageve-insert-box input', function(e) {
        if(e.keyCode == 13) {
            e.preventDefault();
            diageve_add_to_list(jQuery(this).parent());
        }
    });
    jQuery(document).on('dblclick', '#whitelist', function(e) {
        var t = jQuery(this);
        if(t.val() != '') {
            var op = t.find('option[value="'+ t.val() +'"]');
            if(op.length) {
                if(confirm(diageve_lang.item_removal_prompt))
                    op.remove();
            }
        }
    });
});
function diageve_add_to_list(e) {
    var input = e.find('input');
    if(input.val() == "") return;
    var val = input.val();
    var list = e.attr('data-list');
    if(list == '') return;
    list = jQuery(list);
    if(!list.length) return;
    var contains = false;

    if(val.substr(val.length -1, 1) == '/') val = val.substr(0, val.length - 1);

    list.find('option').each(function() {
        if(jQuery(this).val() == val) {
            contains = true;
        }
    });
    if(!contains) {
        list.append('<option value="'+val+'">'+val+'</option>');
        input.val('');
    }
    else alert(diageve_lang.item_already_exists);
}