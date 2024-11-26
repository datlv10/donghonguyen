"use strict";

var nhConfigByOrder = {
	validate: false,
	htmlRow: '',
	table: $('table#table-config-by-order'),
	data: {},
	init: function(){
		var self = this;
		if(self.table.length == 0) return;
		self.htmlRow = self.table.find('tbody tr:first-child')[0].outerHTML;
		if(self.htmlRow == _UNDEFINED || self.htmlRow.length == 0) return;

		self.event();

		self.table.find('tbody tr').each(function(index, tr) { 
		   self.initInputRow($(tr));
		});
	},
	event: function(){
		var self = this;

		$(document).on('click', '[btn-action="add-item"]', function(e) {
            e.preventDefault();            
            self.table.find('tbody').append(self.htmlRow);
            var rowElement = self.table.find('tbody tr:last-child');

            self.clearRow(rowElement);
            self.initInputRow(rowElement);
        });

        $(document).on('click', '[btn-action="remove-item"]', function(e) {
            e.preventDefault();

            $(this).closest('tr').remove();
        });
	},
	clearRow: function(rowElement = null){
		var self = this;
		if(rowElement == null || rowElement.length == 0) return;

		rowElement.find('input').val('');
	},
	initInputRow: function (rowElement = null){
		var self = this;
		if(rowElement == null || rowElement.length == 0) return;

		rowElement.find('select.select2-multile-select').select2();

		rowElement.find('input.number-input').each(function() {
			nhMain.input.inputMask.init($(this), 'number');
		});
	},
	getDataConfig: function(){
		var self = this;

		self.data = {};
		self.table.find('tbody tr').each(function(index, tr) { 
			self.data[index] = {};
		   	$(tr).find('input, select').each(function(i, element) {
		   		var inputName = $(element).attr('name');

		   		if(typeof(inputName) == _UNDEFINED || inputName == _UNDEFINED || inputName.length == 0) return;
		   		if(inputName.indexOf('order_') == -1) return;

		   		self.data[index][inputName] = $(element).val();
		   	});
		});
	}
}

var nhFormSetting = {
	formEl: $('form#shipping-form'),
	init: function(){
		var self = this;

		if(self.formEl.length == 0) return;

		nhMain.input.inputMask.init($('input[name="general_shipping_fee"]'), 'number');
		$(document).on('click', '#btn-save', function(e) {
			e.preventDefault();

			nhConfigByOrder.getDataConfig();			
			self.formEl.find('input[name="custom_config"]').val(JSON.stringify(nhConfigByOrder.data));

			nhMain.initSubmitForm(self.formEl, $(this));
		});
	}
}

$(document).ready(function() {
	nhConfigByOrder.init();
    nhFormSetting.init();
});