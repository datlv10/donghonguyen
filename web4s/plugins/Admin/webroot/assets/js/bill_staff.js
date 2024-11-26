"use strict";

var nhBillStaff = {
	init: function(){
		$(document).on('keyup keypress paste focus', '#staff-suggest', function(e) {
			var inputValue = 'input[name="staff_id"]';
			if(e.type != 'focusin'){
				$(inputValue).val('');	
			}				

			nhMain.autoSuggest.basic({
				inputSuggest: 'input#staff-suggest',
				inputValue: inputValue,
				fieldLabel: 'full_name',
				url: adminPath + '/user/auto-suggest'
			}, function(response){
				if(!$.isEmptyObject(response) && typeof(response.full_name) != _UNDEFINED){
					$('#staff-suggest').val(response.full_name)
				}
			});

			if(e.type == 'focusin' && $(this).val() == ''){
				$(this).autocomplete('search', $(this).val());
			}
		});
	}
}