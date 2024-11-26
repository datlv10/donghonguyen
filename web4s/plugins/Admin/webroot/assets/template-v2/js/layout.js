'use strict';
function getInsertionCSS()
{
    var styles = ""+
        ".reserved-drop-marker{width:100%;height:2px;background:#00a8ff;position:absolute}.reserved-drop-marker::after,.reserved-drop-marker::before{content:'';background:#00a8ff;height:7px;width:7px;position:absolute;border-radius:50%;top:-2px}.reserved-drop-marker::before{left:0}.reserved-drop-marker::after{right:0}";
    styles += "[data-dragcontext-marker],[data-sh-parent-marker]{outline:#19cd9d solid 2px;text-align:center;position:absolute;z-index:123456781;pointer-events:none;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif}[data-dragcontext-marker] [data-dragcontext-marker-text],[data-sh-parent-marker] [data-sh-parent-marker-text]{background:#19cd9d;color:#fff;padding:2px 10px;display:inline-block;font-size:14px;position:relative;top:-24px;min-width:121px;font-weight:700;pointer-events:none;z-index:123456782}[data-dragcontext-marker].invalid{outline:#dc044f solid 2px}[data-dragcontext-marker].invalid [data-dragcontext-marker-text]{background:#dc044f}[data-dragcontext-marker=body]{outline-offset:-2px}[data-dragcontext-marker=body] [data-dragcontext-marker-text]{top:0;position:fixed}";
    styles += '.drop-marker{pointer-events:none;}.drop-marker.horizontal{background:#00adff;position:absolute;height:2px;list-style:none;visibility:visible!important;box-shadow:0 1px 2px rgba(255,255,255,.4),0 -1px 2px rgba(255,255,255,.4);z-index:123456789;text-align:center}.drop-marker.horizontal.topside{margin-top:0}.drop-marker.horizontal.bottomside{margin-top:2px}.drop-marker.horizontal:before{content:"";width:8px;height:8px;background:#00adff;border-radius:8px;margin-top:-3px;float:left;box-shadow:0 1px 2px rgba(255,255,255,.4),0 -1px 2px rgba(255,255,255,.4)}.drop-marker.horizontal:after{content:"";width:8px;height:8px;background:#00adff;border-radius:8px;margin-top:-3px;float:right;box-shadow:0 1px 2px rgba(255,255,255,.4),0 -1px 2px rgba(255,255,255,.4)}.drop-marker.vertical{height:50px;list-style:none;border:1px solid #00ADFF;position:absolute;margin-left:3px;display:inline;box-shadow:1px 0 2px rgba(255,255,255,.4),-1px 0 2px rgba(255,255,255,.4)}.drop-marker.vertical.leftside{margin-left:0}.drop-marker.vertical.rightside{margin-left:3px}.drop-marker.vertical:before{content:"";width:8px;height:8px;background:#00adff;border-radius:8px;margin-top:-4px;top:0;position:absolute;margin-left:-4px;box-shadow:1px 0 2px rgba(255,255,255,.4),-1px 0 2px rgba(255,255,255,.4)}.drop-marker.vertical:after{content:"";width:8px;height:8px;background:#00adff;border-radius:8px;margin-left:-4px;bottom:-4px;position:absolute;box-shadow:1px 0 2px rgba(255,255,255,.4),-1px 0 2px rgba(255,255,255,.4)}';
    return styles;
}

var nhLayout = {
	elements: {
		nhEditorWrapper: $('#nh-editor-wrapper'),

		nhPanel: $('#nh-panel'),
		nhPanelInner: $('#nh-panel-inner'),
		nhModeSwitcher: $('#nh-mode-switcher'),
		nhModeSwitcherInner: $('#nh-mode-switcher-inner'),
		nhModeSwitcherPreviewInput: $('#nh-mode-switcher-preview-input'),
		nhModeSwitcherPreview: $('#nh-mode-switcher-preview'),

		nhPanelHeaderWrapper: $('#nh-panel-header-wrapper'),
		nhPanelHeader: $('#nh-panel-header'),
		nhPanelHeaderTitle: $('#nh-panel-header-title'),
		nhPanelHeaderAddButton: $('#nh-panel-header-add-button'),
		nhPanelContentWrapper: $('#nh-panel-content-wrapper'),
		nhPanelPageElements: $('#nh-panel-page-elements'),

		nhPanelElementsNavigation: $('#nh-panel-elements-navigation'),
		nhPanelElementsSearchArea: $('#nh-panel-elements-search-area'),
		nhPanelElementsSearchWrapper: $('#nh-panel-elements-search-wrapper'),
		nhPanelElementsSearchInput: $('#nh-panel-elements-search-input'),
		nhPanelElementsWrapper: $('#nh-panel-elements-wrapper'),
		nhPanelElementsCategories: $('#nh-panel-elements-categories'),

		nhPanelCategories: $('#nh-panel-categories'),

		nhPanelFooter: $('#nh-panel-footer'),
		nhPanelFooterTools: $('#nh-panel-footer-tools'),
		nhPanelFooterSettings: $('#nh-panel-footer-settings'),
		nhPanelFooterNavigator: $('#nh-panel-footer-navigator'),
		nhPanelFooterHistory: $('#nh-panel-footer-history'),
		nhPanelFooterResponsive: $('#nh-panel-footer-responsive'),
		nhPanelFooterSaverPreview: $('#nh-panel-footer-saver-preview'),
		nhPanelFooterSaverPublish: $('#nh-panel-footer-saver-publish'),
		nhPanelSaverButtonPublish: $('#nh-panel-saver-button-publish'),

		nhPreview: $('#nh-preview'),
		nhLoading: $('#nh-loading'),
		nhResponsiveBar: $('#nh-responsive-bar'),
		nhPreviewLoading: $('#nh-preview-loading'),
		nhPreviewIframe: $('#nh-preview-iframe'),

		nhNavigator: $('#nh-navigator')
	},
	dragData: {
		el: null,
		elRect: null,
		mousePos: null
	},
	percentBreakPoint: {
		x: 5, // phần trăm khoảng cách con trỏ chuột  so với 2 bên block (5%)
		y: 5 // phần trăm khoảng cách con trỏ chuột so với trên và dưới block (5%)
	},
	init: function(options){
		var self = this;

		// kiểm tra elements
		var validateElement = true;
		$.each(self.elements, function(key, element) {
            if(element.length == 0){
            	validateElement = false;
            	return false;
            }
        });

        if(validateElement != true) return false;

		self.initLayout();
		self.events();
	},
	events: function(){
		var self = this;

		var frameWindow = self.elements.nhPreviewIframe.get(0).contentWindow;
        var changeFlag = null;
        var currentElement = null;
        var elementRectangle = null;
        var countdown = 0;
        var processtimer = null;

	    $(document).on('dragstart', '.nh-element', function(event) {	        
            processtimer = setInterval(function() {
                self.orchestrateDragDrop();
            }, 100);

	        var insertingHTML = $(this).attr('data-insert-html');
	        event.originalEvent.dataTransfer.setData("Text",insertingHTML);
		});

        $(document).on('dragend', '.nh-element', function() {
            clearInterval(processtimer);
	        self.removePlaceholder();
	        self.clearContainerContext();
	    });

        self.elements.nhPreviewIframe.on('load', function(){
            var style = $("<style data-reserved-styletag></style>").html(getInsertionCSS());
            $(frameWindow.document.head).append(style);
            var htmlBody = $(frameWindow.document).find('body,html');
            
            htmlBody.find('*').on('dragenter', function(event){
                event.stopPropagation();

                // console.log('dragenter');
                currentElement = $(event.target);
                changeFlag = true;
                elementRectangle = event.target.getBoundingClientRect();
                // countdown = 1;

            }).on('dragover',function(event){
                event.preventDefault();
                event.stopPropagation();

                // if(countdown % 15 != 0 && changeFlag == false)
                // {
                //     countdown = countdown + 1;
                //     return;
                // }
                event = event || window.event;

                var x = event.originalEvent.clientX;
                var y = event.originalEvent.clientY;
                // countdown = countdown + 1;
                changeFlag = false;

                self.dragData = {
                    el: currentElement,
                    elRect: elementRectangle,
                    mousePos: {x:x, y:y}
                }
            })

            $(frameWindow.document).find('body,html').on('drop', function(event) {
                event.preventDefault();
                event.stopPropagation();

                var originalEvent;
                if (event.isTrigger){
                    originalEvent = triggerEvent.originalEvent;
                }else{
                    originalEvent = event.originalEvent;
                }

                try {
                    var textData = originalEvent.dataTransfer.getData('text');
                    var insertionPoint = self.elements.nhPreviewIframe.contents().find('.drop-marker');
                    var checkDiv = $(textData);
                    console.log(textData);
                    console.log(insertionPoint);
                    console.log(checkDiv);
                    insertionPoint.after(checkDiv);
                    insertionPoint.remove();
                }catch(originalEvent){
                    console.log(e);
                }
            });
        });
	},
	initLayout: function(){
		var self = this;
		
		var data = {};

		//load danh sách element
		nhMain.callAjax({
			url: adminPath + '/template-v2/get-elements',
			data: data,
			dataType: _HTML
		}).done(function(response) {
			self.elements.nhPanelCategories.html(response);
		});
	},
    getMouseBearingsPercentage: function(elementObj, elementRect, mousePos){
    	var self = this;

        if(!elementRect){
            elementRect = elementObj.get(0).getBoundingClientRect();
        }
                
        var mousePosPercentX = ((mousePos.x - elementRect.left) / (elementRect.right - elementRect.left)) * 100;
        var mousePosPercentY = ((mousePos.y - elementRect.top) / (elementRect.bottom - elementRect.top)) * 100;

        return {
            x: mousePosPercentX,
            y: mousePosPercentY
        };
    },
	orchestrateDragDrop: function(){
		var self = this;

		var elementObj = self.dragData.el;
		var elRect = self.dragData.elRect;
		var mousePos = self.dragData.mousePos;
        
		if(typeof(elementObj) == _UNDEFINED || elementObj == null || typeof(elRect) == _UNDEFINED || elRect == null || typeof(mousePos) == _UNDEFINED || mousePos == null) return;

        if(elementObj.is('html')) elementObj = elementObj.find('body');

        var mousePercents = self.getMouseBearingsPercentage(elementObj, elRect, mousePos);        
        if((mousePercents.x > self.percentBreakPoint.x && mousePercents.x < 100 - self.percentBreakPoint.x) && (mousePercents.y > self.percentBreakPoint.y && mousePercents.y < 100 - self.percentBreakPoint.y))
        {
            var tmplElement = elementObj.clone();
            tmplElement.find('.drop-marker').remove();
            if(tmplElement.html() == '' && !self.checkVoidElement(tmplElement))
            {
                if(mousePercents.y < 90) return self.placeInside(elementObj);
            }
            else if(tmplElement.children().length == 0)
            {
                //text element detected
                self.decideBeforeAfter(elementObj, mousePercents);
            }
            else if(tmplElement.children().length == 1)
            {
                //only 1 child element detected
                self.decideBeforeAfter(elementObj.children(':not(.drop-marker,[data-dragcontext-marker])').first(), mousePercents);
            }
            else
            {
                var positionAndElement = self.findNearestElement(elementObj, mousePos.x, mousePos.y);
                self.decideBeforeAfter(positionAndElement.el, mousePercents, mousePos);
                //more than 1 child element present
                //console.log("More than 1 child detected");
            }
        }
        else if((mousePercents.x <= self.percentBreakPoint.x) || (mousePercents.y <= self.percentBreakPoint.y))
        {
            var validElement = null
            if(mousePercents.y <= mousePercents.x)
                validElement = self.findValidParent(elementObj, 'top');
            else
                validElement = self.findValidParent(elementObj, 'left');

            if(validElement.is('body,html'))
                validElement = self.elements.nhPreviewIframe.contents().find('body').children(':not(.drop-marker,[data-dragcontext-marker])').first();
            self.decideBeforeAfter(validElement,mousePercents,mousePos);
        }
        else if((mousePercents.x >= 100 - self.percentBreakPoint.x) || (mousePercents.y >= 100-self.percentBreakPoint.y))
        {
            var validElement = null
            if(mousePercents.y >= mousePercents.x)
                validElement = self.findValidParent(elementObj, 'bottom');
            else
                validElement = self.findValidParent(elementObj, 'right');

            if(validElement.is('body,html'))
                validElement = self.elements.nhPreviewIframe.contents().find('body').children(':not(.drop-marker,[data-dragcontext-marker])').last();
            self.decideBeforeAfter(validElement,mousePercents,mousePos);
        }
	},
    decideBeforeAfter : function(elementObj = null, mousePercents = null, mousePos = null) {
        if(mousePos) {
            mousePercents = this.getMouseBearingsPercentage(elementObj, null, mousePos);
        }

        var orientation = (elementObj.css('display') == 'inline' || elementObj.css('display') == 'inline-block');

        if(elementObj.is('br'))
            orientation = false;

        if(orientation)
        {
            if(mousePercents.x < 50)
            {
                return this.placeBefore(elementObj);
            }
            else
            {
                return this.placeAfter(elementObj);
            }
        }
        else
        {
            if(mousePercents.y < 50)
            {
                return this.placeBefore(elementObj);
            }
            else
            {
                return this.placeAfter(elementObj);
            }
        }
    },
	checkVoidElement: function(elementObj = null){
		var self = this;
		if(elementObj == null || elementObj.length == 0) return false;

		var voidelements = ['i', 'area','base','br','col','command','embed','hr','img','input','keygen','link','meta','param','video','iframe','source','track','wbr'];
        var selector = voidelements.join(',');

        if(!elementObj.is(selector)) return false;
        return true;
	},
    findValidParent : function(elementObj = null, direction = null){
        if(elementObj == null || elementObj.length == 0) return;

        switch(direction)
        {
            case 'left':
                while(true)
                {
                    var elementRect = elementObj.get(0).getBoundingClientRect();
                    var tempElement = elementObj.parent();
                    var tempelementRect = tempElement.get(0).getBoundingClientRect();
                    if(elementObj.is('body'))
                        return elementObj;
                    if(Math.abs(tempelementRect.left - elementRect.left) == 0)
                        elementObj = elementObj.parent();
                    else
                        return elementObj;
                }
                break;
            case 'right':
                while(true)
                {
                    var elementRect = elementObj.get(0).getBoundingClientRect();
                    var tempElement = elementObj.parent();
                    var tempelementRect = tempElement.get(0).getBoundingClientRect();
                    if(elementObj.is('body'))
                        return elementObj;
                    if(Math.abs(tempelementRect.right - elementRect.right) == 0)
                        elementObj = elementObj.parent();
                    else
                        return elementObj;
                }
                break;
            case 'top':
                while(true)
                {
                    var elementRect = elementObj.get(0).getBoundingClientRect();
                    var tempElement = elementObj.parent();
                    var tempelementRect = tempElement.get(0).getBoundingClientRect();
                    if(elementObj.is('body'))
                        return elementObj;
                    if(Math.abs(tempelementRect.top - elementRect.top) == 0)
                        elementObj = elementObj.parent();
                    else
                        return elementObj;
                }
                break;
            case 'bottom':
                while(true)
                {
                    var elementRect = elementObj.get(0).getBoundingClientRect();
                    var tempElement = elementObj.parent();
                    var tempelementRect = tempElement.get(0).getBoundingClientRect();
                    if(elementObj.is('body'))
                        return elementObj;
                    if(Math.abs(tempelementRect.bottom - elementRect.bottom) == 0)
                        elementObj = elementObj.parent();
                    else
                        return elementObj;
                }
                break;
        }
    },
	addPlaceHolder : function(elementObj, position, placeholder){
        var self = this;
        if(!placeholder) placeholder = self.getPlaceHolder();

        self.removePlaceholder();
        switch(position)
        {
            case 'before':
                placeholder.find('.message').html(elementObj.parent().data('sh-dnd-error'));
                elementObj.before(placeholder);
                self.addContainerContext(elementObj, 'sibling');
                break;
            case 'after':
                placeholder.find('.message').html(elementObj.parent().data('sh-dnd-error'));
                elementObj.after(placeholder);
                self.addContainerContext(elementObj, 'sibling');
                break
            case 'inside-prepend':
                placeholder.find('.message').html(elementObj.data('sh-dnd-error'));
                elementObj.prepend(placeholder);
                self.addContainerContext(elementObj, 'inside');
                break;
            case 'inside-append':
                placeholder.find('.message').html(elementObj.data('sh-dnd-error'));
                elementObj.append(placeholder);
                self.addContainerContext(elementObj, 'inside');
                break;
        }
    },
	removePlaceholder: function(){
    	var self = this;
        self.elements.nhPreviewIframe.contents().find('.drop-marker').remove();
    },
    getPlaceHolder: function(){
    	var self = this;
        return $("<li class='drop-marker'></li>");
    },
    placeInside: function(elementObj = null){
    	var self = this;
    	if(elementObj == null || elementObj.length == 0) return;

        var placeholder = self.getPlaceHolder();
        placeholder.addClass('horizontal').css('width', elementObj.width() + 'px');
        self.addPlaceHolder(elementObj, 'inside-append', placeholder);
    },
    placeBefore: function(elementObj = null){
    	var self = this;
    	if(elementObj == null || elementObj.length == 0) return;

        var placeholder = self.getPlaceHolder();
        var inlinePlaceholder = (elementObj.css('display') == 'inline' || elementObj.css('display') == 'inline-block');
        if(elementObj.is('br'))
        {
            inlinePlaceholder = false;
        }
        else if(elementObj.is('td,th'))
        {
            placeholder.addClass('horizontal').css('width' , elementObj.width() + 'px');
            return self.addPlaceHolder(elementObj, 'inside-prepend', placeholder);
        }

        if(inlinePlaceholder){
            placeholder.addClass('vertical').css('height', elementObj.innerHeight() + 'px');
        }
        else{
            placeholder.addClass('horizontal').css('width', elementObj.parent().width() + 'px');
        }

        self.addPlaceHolder(elementObj, 'before', placeholder);
    },
    placeAfter: function(elementObj){
        var self = this;

        var placeholder = self.getPlaceHolder();
        var inlinePlaceholder = (elementObj.css('display') == 'inline' || elementObj.css('display') == 'inline-block');

        if(elementObj.is('br')) {
            inlinePlaceholder = false;
        }else if(elementObj.is('td,th')) {
            placeholder.addClass('horizontal').css('width', elementObj.width() + 'px');
            return self.addPlaceHolder(elementObj, 'inside-append', placeholder);
        }

        if(inlinePlaceholder) {
            placeholder.addClass('vertical').css('height', elementObj.innerHeight() + 'px');
        }
        else{
            placeholder.addClass('horizontal').css('width', elementObj.parent().width() + 'px');
        }
            
        self.addPlaceHolder(elementObj, 'after', placeholder);
    },
    calculateDistance : function(elementData, mouseX, mouseY){
        return Math.sqrt(Math.pow(elementData.x - mouseX, 2) + Math.pow(elementData.y - mouseY, 2));
    },
    findNearestElement : function(elementContainer, clientX, clientY){
        var self = this;
        var previousElData = null;
        var childElement = elementContainer.children(':not(.drop-marker,[data-dragcontext-marker])');
        if(childElement.length > 0)
        {
            childElement.each(function()
            {
                if($(this).is('.drop-marker')) return;

                var offset = $(this).get(0).getBoundingClientRect();
                var distance = 0;
                var distance1, distance2 = null;
                var position = '';
                var xPosition1 = offset.left;
                var xPosition2 = offset.right;
                var yPosition1 = offset.top;
                var yPosition2 = offset.bottom;
                var corner1 = null;
                var corner2 = null;

                //Parellel to Yaxis and intersecting with x axis
                if(clientY > yPosition1 && clientY <  yPosition2 )
                {
                    if(clientX < xPosition1 && clientY < xPosition2)
                    {
                        corner1 = {
                            x: xPosition1, 
                            y: clientY, 
                            'position': 'before'
                        };
                    }
                    else
                    {
                        corner1 = {
                            x: xPosition2, 
                            y: clientY,
                            'position': 'after'
                        };
                    }

                }
                //Parellel to xAxis and intersecting with Y axis
                else if(clientX > xPosition1 && clientX < xPosition2)
                {
                    if(clientY < yPosition1 && clientY < yPosition2)
                    {
                        corner1 = {
                            x: clientX, 
                            y: yPosition1,
                            'position': 'before'
                        };
                    }
                    else
                    {
                        corner1 = {
                            x: clientX, 
                            y: yPosition2,
                            'position': 'after'
                        };
                    }

                }
                else
                {
                    //runs if no element found!
                    if(clientX < xPosition1 && clientX < xPosition2)
                    {
                        corner1 = {x:xPosition1, y:yPosition1, 'position':'before'};          //left top
                        corner2 = {x:xPosition1, y :yPosition2, 'position':'after'};       //left bottom
                    }
                    else if(clientX > xPosition1 && clientX > xPosition2)
                    {
                        //console.log('I m on the right of the element');
                        corner1 = {x:xPosition2, y:yPosition1, 'position':'before'};   //Right top
                        corner2 = {x:xPosition2, y :yPosition2, 'position':'after'}; //Right Bottom
                    }
                    else if(clientY < yPosition1 && clientY < yPosition2)
                    {
                        // console.log('I m on the top of the element');
                        corner1 = {x :xPosition1, y:yPosition1, 'position':'before'}; //Top Left
                        corner2 = {x :xPosition2, y:yPosition1, 'position':'after'}; //Top Right
                    }
                    else if(clientY > yPosition1 && clientY > yPosition2)
                    {
                        // console.log('I m on the bottom of the element');
                        corner1 = {x :xPosition1, y:yPosition2, 'position':'before'}; //Left bottom
                        corner2 = {x :xPosition2, y:yPosition2, 'position':'after'} //Right Bottom
                    }
                }

                distance1 = self.calculateDistance(corner1, clientX, clientY);

                if(corner2 !== null)
                    distance2 = self.calculateDistance(corner2, clientX, clientY);

                if(distance1 < distance2 || distance2 === null)
                {
                    distance = distance1;
                    position = corner1.position;
                }
                else
                {
                    distance = distance2;
                    position = corner2.position;
                }

                if(previousElData !== null)
                {
                    if(previousElData.distance < distance)
                    {
                        return true; //continue statement
                    }
                }

                previousElData =  {
                    'el': this,
                    'distance': distance,
                    'xPosition1': xPosition1,
                    'xPosition2': xPosition2,
                    'yPosition1': yPosition1,
                    'yPosition2': yPosition2, 
                    'position': position
                }
            });

            if(previousElData !== null)
            {
                var position = previousElData.position;
                return {
                    'el': $(previousElData.el),
                    'position': position
                };
            }
            else
            {
                return false;
            }
        }
    },
    getContextMarker : function(){
        var self = this;
        return $('<div data-dragcontext-marker><span data-dragcontext-marker-text></span></div>');
    },
    addContainerContext : function(elementObj, position){
        var self = this;

        var contextMarker = self.getContextMarker();
        self.clearContainerContext();
        if(elementObj.is('html,body'))
        {
            position = 'inside';
            elementObj =  self.elements.nhPreviewIframe.contents().find('body');
        }

        switch(position)
        {
            case 'inside':
                self.positionContextMarker(contextMarker, elementObj);
                if(elementObj.hasClass('stackhive-nodrop-zone')) contextMarker.addClass('invalid');

                var name = self.getElementName(elementObj);
                contextMarker.find('[data-dragcontext-marker-text]').html(name);
                if(self.elements.nhPreviewIframe.contents().find('body [data-sh-parent-marker]').length != 0)
                    self.elements.nhPreviewIframe.contents().find('body [data-sh-parent-marker]').first().before(contextMarker);
                else
                    self.elements.nhPreviewIframe.contents().find('body').append(contextMarker);
                break;
            case 'sibling':
                self.positionContextMarker(contextMarker, elementObj.parent());
                if(elementObj.parent().hasClass('stackhive-nodrop-zone')) contextMarker.addClass('invalid');

                var name = self.getElementName(elementObj.parent());
                contextMarker.find('[data-dragcontext-marker-text]').html(name);
                contextMarker.attr('data-dragcontext-marker', name.toLowerCase());

                if(self.elements.nhPreviewIframe.contents().find('body [data-sh-parent-marker]').length != 0)
                    self.elements.nhPreviewIframe.contents().find('body [data-sh-parent-marker]').first().before(contextMarker);
                else
                    self.elements.nhPreviewIframe.contents().find('body').append(contextMarker);
                break;
        }
    },
    positionContextMarker: function(contextMarkerObj, elementObj){
        var self = this;

        var rect = elementObj.get(0).getBoundingClientRect();
        contextMarkerObj.css({
            height: (rect.height + 4) + 'px',
            width: (rect.width + 4) + 'px',
            top: (rect.top + $(self.elements.nhPreviewIframe.get(0).contentWindow).scrollTop() - 2) + 'px',
            left: (rect.left + $(self.elements.nhPreviewIframe.get(0).contentWindow).scrollLeft() - 2) + "px"
        });

        if(rect.top + self.elements.nhPreviewIframe.contents().find('body').scrollTop() < 24)
            contextMarkerObj.find('[data-dragcontext-marker-text]').css('top', '0px');
    },
	clearContainerContext: function(){
        var self = this;
        self.elements.nhPreviewIframe.contents().find('[data-dragcontext-marker]').remove();
    },
    getElementName: function(elementObj){
        var self = this;
        return elementObj.prop('tagName');
    }
}

$(document).ready(function() {
	nhLayout.init();
});