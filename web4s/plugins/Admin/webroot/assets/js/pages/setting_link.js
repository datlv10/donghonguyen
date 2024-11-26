"use strict";

var nhSetting = function () {
    var formEl;

    return {
        init: function() {
            var self = this;

            formEl = $('#main-form');

            $(document).on('click', '.btn-save', function(e) {
                e.preventDefault();
                nhMain.initSubmitForm(formEl, $(this));
            });

            $('#list-link .suggest-item').each(function(){
                if(($('input[name="custom_url"]').val()).indexOf($(this).attr('data-code')) > 0) {
                    $(this).addClass('active');
                }
            });

            $(document).on('click', '#list-link .suggest-item', function(e) {
                var _keyword = $(this).attr('data-code');
                var _inputCustom = $('input[name="custom_url"]');
                var _customUrl = $('input[name="custom_url"]').val();
                var index = _customUrl.indexOf(_keyword);

                if(index < 0){
                    _inputCustom.val(_customUrl + '/' + _keyword);
                } else {
                    _customUrl = _customUrl.replace('/' + _keyword, '');
                    _inputCustom.val(_customUrl);
                }

                $(this).toggleClass('active');

                $('input[id="custom-type"]').val(self.getTypeCustom());
            });

            $(document).on('change', '#main-form input[type="radio"][name="type"]', function(e) {
                if($(this).attr('id') == 'custom-type'){
                    $('input[id="custom-type"]').val(self.getTypeCustom());
                }else{
                    $('input[id="custom-type"]').val('');
                }
            });            
        },
        getTypeCustom: function(){
            var result = '';
            var customUrl = $('input[name="custom_url"]').val();

            var urlSplit = customUrl.split('/');
            urlSplit = urlSplit.filter(function(v){return v != ''});

            if(!$.isEmptyObject(urlSplit)){
                result = '|' + urlSplit.join('|') + '|';
            }

            return result;
        }
    };
}();

$(document).ready(function() {
    nhSetting.init();
});