"use strict";

var nhSettingEmAttribute = {
	table: $('#table-attribute'),
	inputName: $('input#name'),
	selectAttribute: $('select#attribute'),
	selectView: $('select#view'),
	init: function(params = {}){
		var self = this;
		if(self.table.length == 0) return;
		self.events();
	},
	events: function(){
		var self = this;

		$('.kt-selectpicker').selectpicker();

		$(document).on('click', '#btn-add-attribute', function(e) {
			var attribute = self.selectAttribute.val();
			var attributeName = self.selectAttribute.find('option:selected').text();
			var view = self.selectView.val();
			var name = $.trim(self.inputName.val());
			var data = {
				attribute: attribute,
				attribute_name: attributeName,
				view: view,
				name: name,
			}
			self.add(data);
		});

		$(document).on('click', '[nh-delete-attribute]', function(e) {
			$(this).closest('tr').remove();
		});

		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			self.getConfig();
			nhMain.initSubmitForm($(this).closest('form'), $(this));
		});
	},
	add: function(data = {}){
		var self = this;
		var attribute = typeof(data.attribute) != _UNDEFINED ? data.attribute : null;
		var attributeName = typeof(data.attribute_name) != _UNDEFINED ? data.attribute_name : null;
		var view = typeof(data.view) != _UNDEFINED ? data.view : null;
		var name = typeof(data.name) != _UNDEFINED ? data.name : null;

		if(attribute == null || attribute.length == 0){
			toastr.error(nhMain.getLabel('vui_long_chon_thuoc_tinh'));
			return false;
		}

		if(view == null || view.length == 0){
			toastr.error(nhMain.getLabel('vui_long_chon_embed_view'));
			return false;
		}

		if(name == null || name.length == 0){
			toastr.error(nhMain.getLabel('vui_long_nhap_ten_ma_nhung'));
			return false;
		}

		var exist = self.checkExist(attribute, view);
		if(exist){
			toastr.error(nhMain.getLabel('cau_hinh_nay_da_ton_tai'));
			return false;
		}
		
		var html = '<tr nh-attribute="'+ attribute +'" nh-attribute-name="'+ attributeName +'" nh-view="'+ view +'" nh-name="'+ name +'"><td class="kt-font-bolder">'+ name +'</td><td>'+ attributeName +'</td><td>'+ view +'</td><td></td></tr>';

		self.table.find('tbody').append(html);
		self.table.find('tbody tr.no-attribute').remove();

		self.selectAttribute.val('');
        self.selectAttribute.selectpicker('refresh');

        self.selectView.val('');
        self.selectView.selectpicker('refresh');

        self.inputName.val('');
	},
	getConfig: function(){
		var self = this;

		var config = [];
		self.table.find('tbody tr').each(function(index) {
		  	var attribute = $(this).attr('nh-attribute');
		  	var attributeName = $(this).attr('nh-attribute-name');
		  	var view = $(this).attr('nh-view');
		  	var name = $(this).attr('nh-name');

		  	if(attribute == _UNDEFINED || attribute.length == 0) return;
			if(view == _UNDEFINED || view.length == 0) return;
			if(name == _UNDEFINED || name.length == 0) return;

		  	config.push({
		  		attribute: attribute,
		  		attribute_name: attributeName,
		  		view: view,
		  		name: name
		  	});
		});

		var jsonConfig = !$.isEmptyObject(config) ? JSON.stringify(config) : '';

		$('input[name="config_embed_attribute"]').val(jsonConfig);
	},
	checkExist: function(attribute = null, view = null){
		var self = this;
		var tr = self.table.find('tbody tr[nh-attribute="'+ attribute +'"][nh-view="'+ view +'"]');

		var result = false;
		if(tr.length > 0){
			result = true;
		}
		return result;
	}
	
}

$(document).ready(function() {
	nhSettingEmAttribute.init();
});
