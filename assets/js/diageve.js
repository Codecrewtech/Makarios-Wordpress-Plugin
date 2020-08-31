jQuery(document).ready(function() {
	jQuery('.diageve-date > input[type="text"]').on('keyup', function(e) {
		var t = jQuery(this);
		console.log('test');
		if(t.val().length == 2) t.next('input').focus();
	})

    var response = jQuery('#diageve-frm-response');
    jQuery('#diageve-frm').ajaxForm({
        beforeSubmit: function(arr, $form, options) {
            response.stop(true,true).fadeOut();
            return true;
        },
        success: function(data, textStatus, jqXHR, $form) {
            console.log(data);
            try {
                var res = jQuery.parseJSON(data);
                if(res.result == 1) window.location.reload();
                else {
                    if(typeof res.data.redirect != "undefined")
                        window.location.href = res.data.redirect;
                    else {
                        if(res.message != "") {
                            response.html(res.message).stop(true,true).fadeIn();
                        }
                    }
                }
            }
            catch(e) {
                console.log('[diageve error] '+ e.message);
                window.location.reload();
            }
        }
    });
    jQuery(document).on('keypress', '#diageve-frm input', function(e) {
        return diageve_isNumber(e);
    })
});
function diageve_isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}