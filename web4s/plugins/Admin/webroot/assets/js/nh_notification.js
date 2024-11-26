'use strict';

var nhListNotificationSystem = {
	miniElement: null,
	countElement: null,
	slidebarElement: null,
	wrapListMyNotificationElement: null,
	wrapListmGeneralElement: null,
	exist: 0,
	init: function(){
		var self = this;

		self.slidebarElement = $('[nh-notification="slidebar"]');
		self.miniElement = $('[nh-notification="mini"]');
		self.countElement = $('[nh-notification="count-notification"]');
		self.countMyNotificationElement = $('[nh-notification="count-my-notification"]');
		self.countGeneralNotificationElement = $('[nh-notification="count-general-notification"]');
		
		self.wrapListMyNotificationElement = $('[nh-list-notification="my_notification"]');
		self.wrapListmGeneralElement = $('[nh-list-notification="general"]');
		
		if(self.slidebarElement.length == 0) return;		
		if(self.miniElement.length == 0) return;
		if(self.countElement.length == 0 || self.countMyNotificationElement.length == 0 || self.countGeneralNotificationElement.length == 0) return;
		if(self.wrapListMyNotificationElement.length == 0 || self.wrapListmGeneralElement.length == 0) return;

		// kiểm tra hiện tại có thông báo nào không	
		var lastTime = nhMain.utilities.parseInt(self.countElement.data('last-time'));
		var seenTime = self.getSeenTimeLocal();

		if(lastTime > seenTime){
			self.showCount();
		}

		self.event();
	},
	event: function(){
		var self = this;

		self.miniElement.on('click', function(e) {
			e.preventDefault();
			self.setSeenTimeLocal();
			self.displayCount('hide');
		});

		$(document).on('click', '[nh-notification="more"]', function(e) {
			var page = nhMain.utilities.parseInt($(this).data('page'));
			var group = $(this).closest('[nh-list-notification]').attr('nh-list-notification');

			if(page >= 1 && group != _UNDEFINED){
				page = page + 1;

				$(this).remove();
				self.loadListNotifications(page, group);
			}
		});

		$(document).on('mouseenter', '[nh-notification="item"]', function(e) {
			$(this).removeClass('not-seen');
		});
	},
	loadListNotifications: function(page = null, group = null, callback = null){
		var self = this;

		if(!page > 0 || group == null || typeof(group) == _UNDEFINED) return;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }
	    
	    var wrapElement = $('[nh-list-notification="' + group + '"]');
	    if(wrapElement.length == 0) return;

        KTApp.block(wrapElement[0], blockOptions);
		nhMain.callAjax({
    		async: true,
    		dataType: 'html',
			url: adminPath + '/nh-notification/list',
			data: {
				page: page,
				group: group
			},
		}).done(function(response) {
			if(page > 1){
				wrapElement.append(response);
			}else{
				wrapElement.html(response);
			}

			KTApp.unblock(wrapElement[0]);
			callback(response);
		});
	},
	getSeenTimeLocal: function(){
		var self = this;
		var timeClient = window.localStorage.getItem('nhAdminNotificationSeenTime');
		return nhMain.utilities.parseInt(timeClient);
	},
	setSeenTimeLocal: function(){
		var self = this;
		var currentTime = nhMain.utilities.parseInt($.now()/1000);
		window.localStorage.setItem('nhAdminNotificationSeenTime', currentTime);
	},
	getCountNew: function(){
		var self = this;

		var timeClient = window.localStorage.getItem('nhAdminNotificationSeenTime');

		// đếm ở danh sách thông báo của tôi	
		var my_notification = 0;
		self.wrapListMyNotificationElement.find('[nh-notification="item"]').each(function(index, item){
			var time = $(this).data('time');
			time = nhMain.utilities.parseInt(time);
			if(!time > 0) return;
			if(time < timeClient) return;

			my_notification++;

			// thêm class để hiển thị thông báo này chưa đọc
			$(this).addClass('not-seen')
		});

		// đếm thông báo chung
		var general = 0;
		self.wrapListmGeneralElement.find('[nh-notification="item"]').each(function(index, item){
			var time = $(this).data('time');
			time = nhMain.utilities.parseInt(time);
			if(!time > 0) return;
			if(time < timeClient) return;

			general++;

			// thêm class để hiển thị thông báo này chưa đọc
			$(this).addClass('not-seen')
		});

		var result = {
			my_notification: my_notification,
			general: general
		}

		return result;
	},
	showCount: function(number = null){
		var self = this;

		var count = 0;

		var countNew = self.getCountNew();
		var my_notification = nhMain.utilities.notEmpty(countNew.my_notification) ? countNew.my_notification : 0;
		var general = nhMain.utilities.notEmpty(countNew.general) ? countNew.general : 0;

		count = my_notification + general;

		if(number != null) count = number;
		
		// hiển thị tổng số thông báo
		if(count > 0){
			if(count > 6) count = '6+';
			self.countElement.text(count);
			self.displayCount('show');
		}else{
			self.countElement.text('');
			self.displayCount('hide');
		}

		// hiển thị số thông báo của tôi
		if(my_notification > 0){
			if(my_notification > 6) my_notification = '6+';
			self.countMyNotificationElement.text(my_notification);
			self.countMyNotificationElement.removeClass('d-none');
		}else{
			self.countMyNotificationElement.text('');
			self.countMyNotificationElement.addClass('d-none');
		}

		// hiển thị số thông báo chung
		if(general > 0){
			if(general > 6) general = '6+';
			self.countGeneralNotificationElement.text(general);
			self.countGeneralNotificationElement.removeClass('d-none');
		}else{
			self.countGeneralNotificationElement.text('');
			self.countGeneralNotificationElement.addClass('d-none');
		}
	},
	displayCount: function(type = null){
		var self = this;

		if(type == 'show'){
			self.countElement.removeClass('d-none');
			self.miniElement.find('i').addClass('text-danger').removeClass('text-info');
		}else{
			self.countElement.addClass('d-none');
			self.miniElement.find('i').addClass('text-info').removeClass('text-danger');
		}
	}
}

$(document).ready(function() {
	nhListNotificationSystem.init();
});









