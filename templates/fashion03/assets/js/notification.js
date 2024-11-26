'use strict';

var nhListNotification = {
	miniElement: null,
	countElement: null,
	slidebarElement: null,
	wrapListElement: null,
	exist: 0,
	init: function(){
		var self = this;

		self.slidebarElement = $('[nh-slidebar="notification"]');
		self.miniElement = $('[nh-mini-notification]');
		self.wrapListElement = $('[nh-list-notification]');
		self.countElement = $('[nh-number-notification]');

		if(self.slidebarElement.length == 0) return;
		if(self.wrapListElement.length == 0) return;
		if(self.miniElement.length == 0) return;
		if(self.countElement.length == 0) return;

		if(typeof(nhMain.dataInit) != _UNDEFINED && typeof(nhMain.dataInit.notification) != _UNDEFINED && typeof(nhMain.dataInit.notification.exist) != _UNDEFINED){
			self.exist = nhMain.utilities.parseInt(nhMain.dataInit.notification.exist);
		}

		// kiểm tra hiện tại có thông báo nào không	
		var lastTime = nhMain.utilities.parseInt(nhMain.dataInit.notification.last_time);
		if(self.exist > 0 && lastTime > self.getSeenTimeLocal()){
			self.loadListNotifications(1, function(){
				self.showCount();
			});
		}

		self.event();
	},
	event: function(){
		var self = this;

		self.miniElement.on('click', function(e) {
			e.preventDefault();
			self.loadListNotifications(1);
			self.setSeenTimeLocal();
			self.showSlidebar();
			self.showCount(0);
		});

		$(document).on('click', '[nh-more-notification]', function(e) {
			var page = nhMain.utilities.parseInt($(this).attr('nh-more-notification'));
			if(page > 1){
				$(this).remove();
				self.loadListNotifications(page, function(e){
					self.showCount();
				});
			}
		});

		$(document).on('click', '[nh-slidebar-action="close"]', function(e) {
			e.preventDefault();

			self.hideSlidebar();
		});

		$(document).on('click', 'body', function(e) {
			if(($(e.target).is('[nh-slidebar-action="close"]') || $(e.target).is('body.dark-overlay')) && self.slidebarElement.hasClass('open')){
				self.hideSlidebar();
			}
		});
	},
	loadListNotifications: function(page = null, callback = null){
		var self = this;

		if(!page > 0) return;

		if (typeof(callback) != 'function') {
	        callback = function () {};
	    }

	    nhMain.showLoading.block(self.wrapListElement);
		nhMain.callAjax({
    		async: true,
    		dataType: 'html',
			url: '/notification/list',
			data: {
				page: page
			},
		}).done(function(response) {	
			if(page > 1){
				self.wrapListElement.append(response);
			}else{
				self.wrapListElement.html(response);
			}

			nhMain.showLoading.remove(self.wrapListElement)
			callback();
		});
	},
	getSeenTimeLocal: function(){
		var self = this;
		var timeClient = window.localStorage.getItem('nhSeenTimeNotification');
		return nhMain.utilities.parseInt(timeClient);
	},
	setSeenTimeLocal: function(){
		var self = this;
		var currentTime = nhMain.utilities.parseInt($.now()/1000);
		window.localStorage.setItem('nhSeenTimeNotification', currentTime);
	},
	countNew: function(){
		var self = this;

		var timeClient = window.localStorage.getItem('nhSeenTimeNotification');
		var count = 0;
		self.wrapListElement.find('[nh-time-notification]').each(function(index, item){
			var time = $(this).attr('nh-time-notification');
			time = nhMain.utilities.parseInt(time);
			if(!time > 0) return;
			if(time < timeClient) return;

			count++;

			// thêm class để hiển thị thông báo này chưa đọc
			$(this).addClass('not-seen')
		});

		return count;
	},
	showCount: function(number = null){
		var self = this;
		if(self.countElement.length == 0) return;

		var count = self.countNew();
		if(number != null) {
			count = number;
		}
		
		if(count > 0){
			if(count > 5) count = '5+';
			self.countElement.text(count);

			self.countElement.removeClass('d-none');
		}else{
			self.countElement.text('');
			self.countElement.addClass('d-none');
		}
	},
	showSlidebar: function (){
		var self = this;

		self.slidebarElement.addClass('open');
		$('body').addClass('dark-overlay');
	},
	hideSlidebar: function(){
		var self = this;

		self.slidebarElement.removeClass('open');
		$('body').removeClass('dark-overlay');
	}
}

var nhNotification = {
	wrapElement: null,
	dialogElement: null,
	helperElement: null,
	paragraphElement: null,
	permission: null,
	webPushCertificates: null,
	init: function(){
		var self = this;

		if(typeof(nhMain.dataInit) != _UNDEFINED && typeof(nhMain.dataInit.notification) != _UNDEFINED && typeof(nhMain.dataInit.notification.web_push_certificates) != _UNDEFINED){
			self.webPushCertificates = nhMain.dataInit.notification.web_push_certificates;
		}

		if(self.webPushCertificates == null || self.webPushCertificates.length == 0){
			nhMain.showLog(nhMain.getLabel('chua_cau_hinh_chuc_nang_gui_thong_bao'))
		}

		try {
			if(typeof(messaging) == _UNDEFINED || messaging == null) {
				nhMain.showLog(nhMain.getLabel('chua_cai_dat_thu_vien_chuc_nang_gui_thong_bao'))
				return false;
			}
		} catch(e) {
			nhMain.showLog(nhMain.getLabel('chua_cai_dat_thu_vien_chuc_nang_gui_thong_bao'));
			return false;
		}

		self.wrapElement = $('[nh-element-push="wrap"]');
		if(self.wrapElement.length == 0) return;

		self.paragraphElement = self.wrapElement.find('.push-paragraph');
		if(self.paragraphElement.length == 0) return;

		self.dialogElement = self.wrapElement.find('.push-dialog');
		if(self.dialogElement.length == 0) return;

		self.helperElement = self.wrapElement.find('.push-help');
		if(self.helperElement.length == 0) return;
		
		// chỉ hiển thị push notification khi ở trên các trình duyệt được hỗ trợ
		var browserInfo = self.getBrowserInfo();		
		if(typeof(browserInfo.browser) != _UNDEFINED && browserInfo.browser == 'Safari'){
			self.wrapElement.remove();
			return false;
		}

		self.wrapElement.removeClass('d-none');

		// lấy quyền gửi thông báo hay không
		Notification.requestPermission().then((permission) => {
			self.permission = permission;
			if(permission == 'granted'){
				var status = 'not';
				var localToken = self.getTokenLocal()
				if(typeof(localToken) != _UNDEFINED && localToken != null && localToken.length > 0){
					status = 'subscribed';
				}
				self.showStatusPush(status);
			}else{
				// nếu chưa có quyền thì hiển thị thông báo
				self.paragraphElement.text(nhMain.getLabel('website_dang_chan_thong_bao'));
				self.dialogElement.addClass('push-hidden');
			}
		});

		self.event();


	  	// Handle incoming messages. Called when:
	  	// - a message is received while the app has focus
	  	// - the user clicks on an app notification created by a service worker
		//   `messaging.onBackgroundMessage` handler.
		messaging.onMessage((payload) => {
			// console.log(payload);
			nhListNotification.loadListNotifications(1, function(){
				nhListNotification.showCount();
			});
		});

	},
	event: function(){
		var self = this;

		self.wrapElement.on('click', function(e) {
			e.preventDefault();
			if(self.permission != 'granted'){
				self.showHelp();
			}else{
				self.dialogElement.toggleClass('push-collapsed');
				self.paragraphElement.addClass('push-collapsed');
			}
		});

		self.wrapElement.on('mouseover', function(e){
		  	e.preventDefault();
		  	if(!self.dialogElement.hasClass('push-collapsed')) return;
		  	
		  	self.paragraphElement.removeClass('push-collapsed');
		});

		self.wrapElement.on('mouseleave', function(e){
		  	e.preventDefault();

		  	self.paragraphElement.addClass('push-collapsed');
		});

		self.wrapElement.find('[nh-action-push="subscribe"]:not(disable)').on('click', function(e) {
			e.preventDefault();

			// ẩn dialogElement
			self.dialogElement.addClass('push-collapsed');
			self.dialogElement.find('[nh-action-push="subscribe"]').addClass('disable');

			// kiểm tra token local
			var localToken = self.getTokenLocal()
			if (localToken == null || localToken.length == 0) {
				// unsubscribe
				self.showStatusPush('subscribed');
	            self.saveToken();
			}else{

				// subcribed
				self.showStatusPush('not');
            	self.removeToken();
			}

            self.dialogElement.find('[nh-action-push="subscribe"]').removeClass('disable');

			return false;
		});


		// ẩn diaglog subcribe và dialog help khi click ngoài wrap
		$(document).on('click', function (e) {
		    if ($(e.target).closest('[nh-element-push="wrap"]').length === 0) {
		        self.dialogElement.addClass('push-collapsed');
		        self.helperElement.addClass('push-collapsed');
		    }
		});
	},
	showStatusPush: function(status = 'subscribed'){
		var self = this;
		if(status == 'subscribed'){
			self.paragraphElement.text(nhMain.getLabel('ban_da_dang_ky_nhan_thong_bao'));
			self.dialogElement.find('[nh-action-push="subscribe"]').text(nhMain.getLabel('huy_nhan_thong_bao'));
		}else{
			self.paragraphElement.text(nhMain.getLabel('ban_chua_the_nhan_thong_bao'));
			self.dialogElement.find('[nh-action-push="subscribe"]').text(nhMain.getLabel('nhan_thong_bao'));
		}
	},
	saveToken: function(){
		var self = this;

		var browser = self.getBrowserInfo();

		messaging.getToken({
            vapidKey: self.webPushCertificates
        }).then((token) => {
        	self.setTokenLocal(token);
            self.sendTokenToServer(token, browser);
        }).catch((err) => {
        	console.log(err);
        });

		return true;
	},
	getTokenLocal: function(){
		var self = this;

		return window.localStorage.getItem('nhTokenMessageClient');
	},
   	setTokenLocal: function(token = null) {
   		var self = this;
   		if(token == null || token.length == 0) return;

       	window.localStorage.setItem('nhTokenMessageClient', token);
   	},
   	sendTokenToServer: function(token = null, browser = {}){
   		var self = this;
   		if(token == null || token.length == 0) return;

   		nhMain.callAjax({
    		async: true,
			url: '/notification/subscribe',
			data: {
				token: token,
				browser_info: browser
			},
		});
   	},   	
   	removeToken: function(){
   		var self = this;

   		// kiểm tra token trước khi xoá
   		messaging.getToken({
            vapidKey: self.webPushCertificates
        }).then((token) => {
   			// xoá token
            messaging.deleteToken(token).then(() => {
                self.removeTokenLocal();
   				self.removeTokenFromServer(token);
            }).catch((err) => {
                console.log(err);

                self.removeTokenLocal();
   				self.removeTokenFromServer(token);
            });
        }).catch((err) => {
            console.log(err);

            // vẫn xóa token khi token không hợp lệ
            self.removeTokenLocal();
   			self.removeTokenFromServer(token);
        });
   	},
   	removeTokenLocal: function() {
   		var self = this;

       	window.localStorage.removeItem('nhTokenMessageClient');
   	},
   	removeTokenFromServer: function(token = null){
   		var self = this;
   		if(token == null || token.length == 0) return;

   		nhMain.callAjax({
    		async: true,
			url: '/notification/unsubscribe',
			data: {
				token: token
			},
		});
   	},
   	showTextError: function(text = null) {
   		var self = this;
   		if(text == null || text.length == 0) false;
   		self.paragraphElement.text(text).removeClass('push-hidden push-collapsed');
   	},
   	showHelp: function() {
   		var self = this;
   		if(self.dialogElement.hasClass('push-collapsed')){
   			self.dialogElement.addClass('push-collapsed');
   		}

   		self.helperElement.removeClass('push-collapsed');   		
   	},
   	getBrowserInfo: function() {
   		var self = this;

   		var userAgent = navigator.userAgent;
   		var browserName  = navigator.appName;
   		
		if(userAgent.indexOf('Opera') != -1) {
		 	browserName = 'Opera';
		}
		else if (userAgent.indexOf('MSIE') != -1) {
		 	browserName = 'Microsoft Internet Explorer';
		}
		else if (userAgent.indexOf('Chrome') !=- 1) {
		 	browserName = 'Chrome';
		}
		else if (userAgent.indexOf('Safari') != -1) {
		 	browserName = 'Safari';
		}
		else if (userAgent.indexOf('Firefox') != -1) {
		 	browserName = 'Firefox';
		}

		return {
   			browser: browserName,
   			userAgent: userAgent
   		}
   	}
}

nhListNotification.init();
nhNotification.init();




