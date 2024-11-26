"use strict";

var nhPoint = function () {

	var formEl;

	var initSubmit = function() {
		$(document).on('click', '.btn-save', function(e) {
			e.preventDefault();
			var formEl = $(this).closest('form');
			nhMain.initSubmitForm(formEl, $(this));
		});
	}

	return {
		init: function() {		
			var self = this;

			$(document).on('click', '.nh-quick-change', function(e) {
        		var _this = $(this);
        		$('.kt-datatable .nh-quick-change').not(this).popover('hide');
				_this.popover({
	    			placement: 'bottom',
	    			html: true,
	    			sanitize: false,
	    			trigger: 'manual',
		            content: $('#popover-quick-change').html(),
		           	template: '\
			            <div class="popover lg-popover" role="tooltip">\
			                <div class="arrow"></div>\
			                <h3 class="popover-header"></h3>\
			                <div class="popover-body"></div>\
			            </div>'
		        });
				var name = $(this).attr('data-change');
				var changeValue = $(this).attr('data-change-value').replace('...','');
				var label = $(this).attr('data-label');

		        _this.on('shown.bs.popover', function (e) {		        	
		        	var idPopover = _this.attr('aria-describedby');
		        	var _popover = $('#' + idPopover);

		        	_popover.find('label').text(label);
		        	_popover.find('#value-change').val(changeValue);
		        	nhMain.input.inputMask.init(_popover.find('.number-input'), 'number');
				})
		        _this.popover('show');
        	}); 

        	$(document).on('click', '#cancel-quick-change', function(e) {
				var idPopover = $(this).closest('.popover.lg-popover').attr('id');
				var btnPopover = $('.nh-quick-change[aria-describedby="'+ idPopover +'"]');
				btnPopover.popover('dispose');
			});

			$(document).on('click', '#confirm-quick-change', function(e) {
				var _popover = $(this).closest('.popover.lg-popover');
				var idPopover = _popover.attr('id');
				var btnPopover = $('.nh-quick-change[aria-describedby="'+ idPopover +'"]');

				var valueChange = _popover.find('#value-change').val();
				var nameChange = btnPopover.attr('data-change');
				var idChange = btnPopover.attr('data-id');

				btnPopover.html('<span class="point-title">+ '+ valueChange +' điểm</span>');
				btnPopover.attr('data-change-value', valueChange);
				btnPopover.closest('.change-point-day').find('input').attr('value', valueChange.replace(',',''));
				btnPopover.popover('dispose');
			});

			$(document).on('click', '.change-day-attendance', function(e) {
				$('.change-day-attendance').removeClass('btn-brand');
				$(this).addClass('btn-brand');
				$('.option-day input[name*=option_day]').addClass('kt-hidden');
				$('.option-day input[name*=option_day]').val('');

				var wrap = $('.day-grid');
				var day = $(this).data('day');

				// neu gia tri day khong co thi mac dinh la 1 tuan
				if (typeof day == _UNDEFINED || day == '') {
					day = 7;
				}

				if (day == 1) {
					$('.option-day input[name*=option_day]').removeClass('kt-hidden');

					return
				}

				$('input[name*=number_day]').attr('value', day);

				// check view hien tai ben ngoai dang hien thi la may tuan
				// neu dang hien thi la 3 tuan ma chon 1 tuan thi xoa bo di chi de 1 tuan
				// neu thieu thi se add them
				var html = $('.day-grid .item-day:first-child').html();
				var element = $('.day-grid .item-day');
				var length = element.length;

				if (length < day) {
					var loop = day - length;
					if (loop === 0) {return}	

					for (let i = 0; i < loop; i++) {
						var indexItem = length + i;

						wrap.append('<li class="item-day item-grid">'+ html +'</li>');
						self.resetItemDay(element, indexItem);
					}
				} else {
					self.removeItemDay(element, day);
				}

			});

			$(document).on('change', 'input[name*=option_day]', function(e) {
				var wrap = $('.day-grid');
				var day = $(this).val();

				// neu gia tri day khong co thi mac dinh la 1 tuan
				if (typeof day == _UNDEFINED || day == '') {
					day = 7;
				}

				$('input[name*=number_day]').attr('value', day);

				// check view hien tai ben ngoai dang hien thi la may tuan
				// neu dang hien thi la 3 tuan ma chon 1 tuan thi xoa bo di chi de 1 tuan
				// neu thieu thi se add them
				var html = $('.day-grid .item-day:first-child').html();
				var element = $('.day-grid .item-day');
				var length = element.length;

				if (length < day) {
					var loop = day - length;
					if (loop === 0) {return}	

					for (let i = 0; i < loop; i++) {
						var indexItem = length + i;

						wrap.append('<li class="item-day item-grid">'+ html +'</li>');
						self.resetItemDay(element, indexItem);
					}
				} else {
					self.removeItemDay(element, day);
				}

			});

			$('.number-input').each(function() {
				nhMain.input.inputMask.init($(this), 'number');
			});

			$('.kt_datepicker').datepicker({
	            format: 'dd/mm/yyyy',
	            todayHighlight: true,
	            autoclose: true,
	            startDate: '0d'
  			});

  			$('.kt-selectpicker').selectpicker();

			initSubmit();
		},
		removeItemDay: function(wrap, day) {
			$(wrap).each(function (key,val) {
				key = key + 1
	  			if (key > day) {
	  				$('.day-grid .item-day[data-id*='+ key +']').remove();
	  			}
			});
		},
		resetItemDay: function(wrap, index) {
  			var index_n = 1 + index;
			$(wrap).each(function (key,val) {
	  			$('.day-grid .item-day:last-child').attr('data-id', index_n);
	  			$('.day-grid .item-day:last-child .day-number').text('Ngày '+ index_n);
	  			$('.day-grid .item-day:last-child input').attr('name', 'point_config[]');
			});
		}
	};
}();


$(document).ready(function() {
	nhPoint.init();
});
