"use strict";

var nhSeoInfo = {
    init: function() {
        var self = this;

        self.event();

        $('.tagify-input').each(function() {
            var tagify = new Tagify(this, {
                pattern: /^.{0,45}$/,
                delimiters: ", ",
                maxTags: 10
            });
        });

        nhMain.selectMedia.single.init();
    },
    event: function(){
        var self = this;

        $(document).on('click', '.btn-toggle-item', function(e) {
            var item = $(this).closest($('.wrap-item'));
            var hidden = item.hasClass('kt-portlet--collapse');

            if(hidden){
                item.find('.kt-portlet__body').slideDown();
                item.removeClass('kt-portlet--collapse');
            }else{
                item.find('.kt-portlet__body').slideUp();
                item.addClass('kt-portlet--collapse');
            }
        });            

        $(document).on('click', '#btn-save', function(e) {
            e.preventDefault();

            nhMain.initSubmitForm($('#main-form'), $(this));
        });
    }
}

$(document).ready(function() {
    nhSeoInfo.init();
});