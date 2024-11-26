"use strict";

var nhRedirect = {
    init: function() {
        var self = this;

        self.event();
    },
    event: function(){
        var self = this;        

        $(document).on('click', '#btn-save-redirect', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm($('#redirect-form'), $(this));
        });

        $(document).on('click', '#btn-save-redirect-page-error', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm($('#redirect-page-error'), $(this));
        });

        $(document).on('click', '[nh-redirect-error]', function(e) {
            var _val = parseInt($(this).val());

            if (typeof _val != _UNDEFINED && _val == 1) {
                $('[name*=redirect_page_type]').prop('disabled', false);
            } else {
                $('[name*=redirect_page_type]').prop('disabled', true);
            }
        });
    }
}

$(document).ready(function() {
    nhRedirect.init();
});