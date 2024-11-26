"use strict";

var nhCustomerPoint = {
	form: $('#main-form'),
	typeDiscount: null,
	init: function(params = {}){
		var self = this;
		if(self.form.length == 0) return false;

		self.initInput();
		self.event();
		self.suggestItem.init();
	},
	initInput: function(){
		var self = this;
		
		self.form.find('.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});

		self.form.find('.kt-selectpicker').selectpicker();
	},
	event: function(){
		var self = this;

		var validatorForm = $('#main-form').validate({
			ignore: ':hidden',
			rules: {
				point: {
					required: true,
				}
			},
			messages: {
                point: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin_diem')
                }
            },

            errorPlacement: function(error, element) {
                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error.addClass('invalid-feedback'));
                }else{                	
                    element.after(error.addClass('invalid-feedback'));
                }
            },
			invalidHandler: function(event, validator) {
				KTUtil.scrollTop();
			},
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			if (!validatorForm.form()){
  				return;
  			};
			nhMain.initSubmitForm($('#main-form'), $(this));
		});
	},
	suggestItem: {
		wrapElement: null,
		type: null,
		init: function(){
			var self = this;

			self.event();
		},
		event: function(){
			var self = this;
			$(document).on('keyup keypress paste focus', 'input[suggest-item]', function(e) {
				self.wrapElement = $(this).closest('[nh-wrap-select]');
				if(self.wrapElement.length == 0) return;

				var inputSuggest = 'input[suggest-item="customer"]';
				var fieldLabel = 'full_name';
				
				nhMain.autoSuggest.basic({
					inputSuggest: inputSuggest,
					url: adminPath + '/customer/auto-suggest',
					fieldLabel: fieldLabel
				}, function(response){
					if(!$.isEmptyObject(response) && typeof(response.id) != _UNDEFINED){
						self.addItemSelected(response);
					}
				});
				
				if(e.type == 'focusin'){
					$(this).autocomplete('search', $(this).val());
				}
			});

			$(document).on('click', '[nh-wrap-select] .tagify__tag__removeBtn', function(e) {
				$(this).closest('.tagify__tag').remove();
			});
		},
		addItemSelected: function(item = {}){
			var self = this;
			
			if($.isEmptyObject(item)) return;
			if(self.wrapElement == null || self.wrapElement.length == 0) return;
			
			var point = 0;
			if(KTUtil.isset(item, 'point') && item.point != null){
				point = nhMain.utilities.parseNumberToTextMoney(item.point);
			}

			var point_promotion = 0;
			if(KTUtil.isset(item, 'point_promotion') && item.point_promotion != null){
				point_promotion = nhMain.utilities.parseNumberToTextMoney(item.point_promotion);
			}

			var itemHtml = 
			'<p><label class="fw-600">' + nhMain.getLabel('ho_ten') + ': </label> ' + item.full_name + '</p>\
			<p><label class="fw-600">' + nhMain.getLabel('diem_hien_tai') + ': </label><span class="text-primary fw-600"> ' + point + '</span></p>\
			<p><label class="fw-600">' + nhMain.getLabel('diem_thuong') + ': </label><span class="text-primary fw-600"> ' + point_promotion + '</span></p>';
			self.wrapElement.find('[nh-item-selected]').html(itemHtml);
			self.wrapElement.find('input[name*=customer_id]').val(item.id);
			self.wrapElement.find('input[name*=point]').val(item.point);
		}		
	}
};


$(document).ready(function() {
	nhCustomerPoint.init();
});
