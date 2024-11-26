"use strict";

var nhSettingSocial = function () {

    var formEl;

    var initSubmit = function() {
        
        // init slider input facebook delay
        var facebookInput = document.getElementById('facebook-input');
        var facebookSlider = document.getElementById('facebook-time-delay');

        noUiSlider.create(facebookSlider, {
            start: [$('#facebook-input').val()],
            step: 1000,
            range: {
                'min': [ 0 ],
                'max': [ 9000 ]
            },
            format: wNumb({
                decimals: 0 
            })
        });

        facebookSlider.noUiSlider.on('update', function( values, handle ) {
            facebookInput.value = values[handle];
        });

        facebookInput.addEventListener('change', function(){
            facebookSlider.noUiSlider.set(this.value);
        });


        // init slider input google delay
        var googleInput = document.getElementById('google-input');
        var googleSlider = document.getElementById('google-time-delay');

        noUiSlider.create(googleSlider, {
            start: [$('#google-input').val()],
            step: 1000,
            range: {
                'min': [ 0 ],
                'max': [ 9000 ]
            },
            format: wNumb({
                decimals: 0 
            })
        });

        googleSlider.noUiSlider.on('update', function( values, handle ) {
            googleInput.value = values[handle];
        });

        googleInput.addEventListener('change', function(){
            googleSlider.noUiSlider.set(this.value);
        });

        $(document).on('click', '.btn-save', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm(formEl, $(this));
        });
    }

    return {
        init: function() {
            formEl = $('#main-form');
            initSubmit();
        }
    };
}();

$(document).ready(function() {
    nhSettingSocial.init();
});