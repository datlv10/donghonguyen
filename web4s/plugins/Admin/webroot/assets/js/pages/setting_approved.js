"use strict";

var nhSettingApproved = function () {

    var formApprovedArticle;
    var formApprovedProduct;

    var initSubmit = function() {
        $(document).on('click', '#btn-save-approved-article', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm(formApprovedArticle, $(this));
        });

        $(document).on('click', '#btn-save-approved-product', function(e) {
            e.preventDefault();
            nhMain.initSubmitForm(formApprovedProduct, $(this));
        });
    }

    return {
        init: function() {
            formApprovedArticle = $('#approved-article-form');
            formApprovedProduct = $('#approved-product-form');
            initSubmit();
        }
    };
}();

$(document).ready(function() {
    nhSettingApproved.init();
});