"use strict";

var nhSeoAnalysis = {
    wrapObject: '#nh-analysis',
    options:{
        baseUrl: '/',
        title: $('.nh-format-link').val(),
        titleObject: '.nh-format-link',
        contentObject: 'textarea[name="content"]',
        keywordObject: 'input[name="seo_keyword"]',
    },
    data:{
        title: '',
        content: '',
        contentHtml: '',
        contentWords: 0,
        contentChars: 0,
        seoTitle: '',        
        url:'',
        keywords: [],
        seoDescription: ''
    },
    countIssues:{},
    rules:{
        length:{
            seoTitle:{
                0: {                    
                    min:0,
                    max:0,
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('tieu_de_seo'),
                    message: nhMain.getLabel('hay_nhap_thong_tin_tieu_de_seo'),
                },
                1: {                    
                    min:1,
                    max:30,
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_tieu_de_seo'),
                    message: nhMain.getLabel('do_dai_tieu_de_seo_qua_ngan'),
                },
                2: {                    
                    min:31,
                    max:60,
                    show:true,
                    type: 'success',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_tieu_de_seo'),
                    message: nhMain.getLabel('tuyet_voi'),
                },
                3: {
                    min:1,
                    max:65,
                    show:false,
                    type: 'danger',
                    group: 'general'
                },
            },
            seoDescription:{
                0: {
                    min:0,
                    max:0,                   
                    show:true,
                    type: 'danger',
                    group: 'general',
                    title: nhMain.getLabel('mo_ta_seo'),
                    message: nhMain.getLabel('hay_nhap_mo_ta_seo'),
                },
                1: {             
                    min:1,
                    max:70,
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_mo_ta_seo'),
                    message: nhMain.getLabel('do_dai_mo_ta_seo_qua_ngan_duoi_120_ky_tu_co_the_len_toi_155_ky_tu'),
                },
                2: {
                    min:70,
                    max:155,
                    show:true,
                    type: 'success',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_mo_ta_seo'),
                    message: nhMain.getLabel('rat_tot'),
                },
                3: {
                    min:1,
                    max:200,
                    show:false,
                    type: 'warning',
                    group: 'general'
                }
            },
            url:{
                0: {
                    min:0,
                    max:0,             
                    show:true,
                    type: 'danger',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_bai_viet'),
                    message: nhMain.getLabel('hay_nhap_duong_dan_bai_viet'),
                },
                1: {
                    min:72,
                    max:999999,             
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_bai_viet'),
                    message: nhMain.getLabel('duong_dan_cho_bai_viet_nay_hoi_dai'),
                }
            },
            content:{
                0: {   
                    min:0,
                    max:0,                 
                    show:true,
                    type: 'danger',
                    group: 'general',
                    title: nhMain.getLabel('noi_dung_bai_viet'),
                    message: nhMain.getLabel('hay_nhap_noi_dung_bai_viet'),
                }
            },
            keywords:{
                0: {                    
                    min:0,
                    max:0,
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('tu_khoa_seo'),
                    message: nhMain.getLabel('hay_nhap_mot_tu_khoa_seo'),
                },
            }
        },
        words:{
            content:{
                0: {                    
                    min:0,
                    max:299,
                    show:true,
                    type: 'danger',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_van_ban'),
                    message: nhMain.getLabel('noi_dung_bai_viet_qua_ngan_so_luong_tu_qua_it_so_voi_muc_toi_thieu_300_tu'),
                },
                1: {                    
                    min:300,
                    max:30000,
                    show:true,
                    type: 'success',
                    group: 'general',
                    title: nhMain.getLabel('do_dai_van_ban'),
                    message: nhMain.getLabel('tuyet_voi'),

                },
                2: {                    
                    min:30001,
                    max:99999999,
                    show: true,
                    type: 'danger',
                    group: 'general',
                    message: nhMain.getLabel('noi_dung_bai_viet_qua_dai_vuot_qua_30000_tu'),
                }
            },
        },
        requireImage:{
            contentHtml:{
                0: {                    
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('anh_bai_viet'),
                    message: nhMain.getLabel('neu_co_anh_trong_noi_dung_bai_viet'),
                }
            }
        },
        requireLinkInternal:{
            contentHtml:{
                0: {                    
                    show:true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_noi_bo'),
                    message: nhMain.getLabel('nen_co_duong_dan_noi_bo_trong_noi_dung_bai_viet'),
                },
                1: {
                    show:true,
                    type: 'success',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_noi_bo'),
                    message: nhMain.getLabel('da_co_duong_dẫn_noi_bo_trong_noi_dung_bai_viet'),
                }
            }
        },
        requireLinkExternal:{
            contentHtml:{
                0: {                    
                    show: true,
                    type: 'warning',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_ngoai_trang'),
                    message: nhMain.getLabel('nen_co_duong_dan_ngoai_trang_trong_noi_dung_bai_viet'),
                },
                1: {                    
                    show: true,
                    type: 'success',
                    group: 'general',
                    title: nhMain.getLabel('duong_dan_ngoai_trang'),
                    message: nhMain.getLabel('da_co_duong_dan_ngoai_trang_trong_noi_dung_bai_viet'),
                }
            }
        },
        keywordInField: {
            keywords:{
                0: {      
                    show: true,
                    field: 'seoTitle',
                    minDensity: 0,
                    maxDensity: 0,
                    type: 'warning',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_tieu_de'),
                    message: nhMain.getLabel('tu_khoa_nen_xuat_hien_trong_tieu_de_seo'),
                },
                1: {      
                    show: true,
                    field: 'seoTitle',
                    minDensity: 0.1,
                    maxDensity: 999999,
                    type: 'success',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_tieu_de'),
                    message: nhMain.getLabel('tieu_de_seo_da_chua_tu_khoa'),
                },
                2: {      
                    show: true,
                    field: 'seoDescription',
                    minDensity: 0,
                    maxDensity: 0,
                    type: 'danger',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_mo_ta'),
                    message: nhMain.getLabel('tu_khoa_khong_xuat_hien_trong_mo_ta_seo'),
                },
                3: {      
                    show: true,
                    field: 'seoDescription',
                    minDensity: 0.1,
                    maxDensity: 0.9,
                    type: 'warning',
                    group: 'keyword',
                    title: nhMain.getLabel('mat_do_tu_khoa_trong_mo_ta'),
                    message: nhMain.getLabel('tu_khoa_xuat_hien_trong_mo_ta_seo_qua_it_ti_le_xuat_hien_duoi_1'),
                },
                4: {      
                    show: true,
                    field: 'seoDescription',
                    minDensity: 1,
                    maxDensity: 999999,
                    type: 'success',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_mo_ta'),
                    message: nhMain.getLabel('tuyet_voi_tu_khoa_da_chua_mo_ta_seo'),
                },
                5: {      
                    show: true,
                    field: 'content',
                    minDensity: 0,
                    maxDensity: 0,
                    type: 'danger',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_noi_dung'),
                    message: nhMain.getLabel('tu_khoa_khong_xuat_hien_trong_noi_dung_bai_viet_cua_ban'),
                },
                6: {
                    show: true,
                    field: 'content',
                    minDensity: 0.1,
                    maxDensity: 0.4,    
                    type: 'warning',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_noi_dung'),
                    message: nhMain.getLabel('tu_khoa_xuat_hien_trong_noi_dung_bai_viet_qua_it_ti_le_xuat_hien_duoi_05'),
                },
                7: {      
                    show: true,
                    field: 'content',
                    minDensity: 0.5,
                    maxDensity: 999999,
                    type: 'success',
                    group: 'keyword',
                    title: nhMain.getLabel('mat_do_tu_khoa_trong_noi_dung'),
                    message: nhMain.getLabel('rat_tot'),
                },
                8: {      
                    show: true,
                    field: 'url',
                    minDensity: 0,
                    maxDensity: 0,
                    type: 'warning',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_duong_dan'),
                    message: nhMain.getLabel('tu_khoa_nen_xuat_hien_trong_duong_dan_bai_viet'),
                },             
            },
        },
        keywordInBegin: {
            keywords:{
                0: {
                    show:true,
                    field: 'content',   
                    type: 'danger',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_phan_gioi_thieu'),
                    message: nhMain.getLabel('tu_khoa_cua_ban_khong_xuat_hien_trong_doan_van_dau_cua_noi_dung_bai_viet'),
                },
                1: {
                    show:true,
                    field: 'content',   
                    type: 'success',
                    group: 'keyword',
                    title: nhMain.getLabel('tu_khoa_trong_phan_gioi_thieu'),
                    message: nhMain.getLabel('rat_tot'),
                },
            }
        },
        keywordInAltImg: {
            keywords:{
                0: {
                    show:true,
                    field: 'content_html',   
                    type: 'danger',
                    group: 'keyword',
                    title: nhMain.getLabel('thuoc_tinh_mo_ta_anh_alt'),
                    message: nhMain.getLabel('cac_anh_trong_trang_nay_khong_co_thuoc_tinh_mo_ta_anh_chua_tu_khoa'),
                },
                1: {
                    show:true,
                    field: 'content_html',   
                    type: 'success',
                    group: 'keyword',
                    title: nhMain.getLabel('thuoc_tinh_mo_ta_anh_alt'),
                    message: nhMain.getLabel('rat_tot_mot_so_anh_trong_trang_nay_co_thuoc_tinh_mo_ta_anh_chua_tu_khoa'),
                },
            }
        }
    },
    init: function (options){
        var self = this;
        $.extend(self.options, options);        
        
        self.data.seoTitle = $.trim($('input[name="seo_title"]').val());
        self.data.url = $.trim($('input[name="link"]').val());
        self.data.seoDescription = $.trim($('input[name="seo_description"]').val());
        self.data.title = $.trim($(self.titleObject).val());
        self.data.contentHtml =  $.trim($(self.options.contentObject).val());
        self.data.content = self.data.contentHtml;
        self.data.contentWords = self.data.content.split(/[\w\u2019\'-]+/).length;
        self.data.contentChars = self.data.content.length;
        var strKeywords = $.trim($(self.options.keywordObject).val());

        if(strKeywords.length > 0){
            self.data.keywords = strKeywords.split(',');
            // trim 2 đầu của từ khoá
            $.each(self.data.keywords, function( index, value ) {
                self.data.keywords[index] = $.trim(value);
            });
        }
        
        $(document).on('change keyup', '.nh-format-link', function () {
        	self.data.title = $(this).val();
		    var link_input = $('.nh-link');
		    if (typeof(link_input.data('link-id')) == _UNDEFINED || link_input.data('link-id') == '') {
		    	self.data.url = nhMain.utilities.parseToUrl(self.data.title);
		        link_input.val(self.data.url);

		        $('input[name="seo_title"]').val(self.data.title);
        		self.checkRules();
		    }		    
		});

        $(document).on('change keyup', 'input[name="seo_title"]', function (e) {
            self.data.seoTitle = $.trim($(this).val());
            self.showProcessbar();
        	self.checkRules();
        });

        $(document).on('change keyup', 'input[name="link"]', function (e) {
            self.data.url = $.trim($(this).val());
            self.checkRules();
        });

        $(document).on('change keyup', 'input[name="seo_description"]', function (e) {
            self.data.seoDescription = $.trim($(this).val());
            self.showProcessbar();
        	self.checkRules();
        });

        var tagify = new Tagify(document.getElementById('seo_keyword'), {
            pattern: /^.{0,45}$/,
            delimiters: ", ",
            maxTags: 10
        }).on('add', function(e){
            if ($.inArray(e.detail.data.value, self.data.keywords) == -1){
                self.data.keywords.push(e.detail.data.value);
                self.generateHtmlAnalysis('keyword');
                self.checkRules();
            }
        }).on('remove', function(e){
            self.data.keywords.splice($.inArray(e.detail.data.value, self.data.keywords), 1);
            self.generateHtmlAnalysis('keyword');
            self.checkRules();
        });
            
        self.generateHtmlAnalysis();
        self.checkRules();
    },
    getContentWhenKeyUpTinyMCE:function(e){
        var self = this;

        var divTmp = $('<div style="display:none;">').html(e.contentHtml);
        self.data.content = divTmp.html();
        self.data.contentHtml = divTmp.html();
        self.data.contentWords = self.data.content.split(/[\w\u2019\'-]+/).length;
        self.data.contentChars = e.contentChars;
        divTmp.remove();

        self.checkRules();
    },
    showProcessbar: function(){
        var self = this;       

        var title_length = self.data.seoTitle.length;
        var description_length = self.data.seoDescription.length;

        var type_title = self.checkLengthForProcessBar('seoTitle', title_length);
        var type_description = self.checkLengthForProcessBar('seoDescription', description_length);

        var max_title_length_default = 65;
        var max_description_length_default = 200;

        var percent_title = title_length > max_title_length_default ? 100 : (title_length/max_title_length_default) * 100;
        var percent_description = description_length > max_description_length_default ? 100 : (description_length/max_description_length_default) * 100;

        $('#progress-bar-title > div, #progress-bar-description > div').removeClass();
        $('#progress-bar-title > div').addClass('progress-bar progress-bar-striped bg-' + type_title).css('width', percent_title + '%');
        $('#progress-bar-description > div').addClass('progress-bar progress-bar-striped bg-' + type_description).css('width', percent_description + '%');
    },
    checkLengthForProcessBar: function(ruleName, length){
        var self = this;
        var result = 'danger';
        if(typeof(self.rules.length[ruleName]) != _UNDEFINED){            
            $.each(self.rules.length[ruleName], function( index, item ) {  
                if(item.min <= length && item.max >= length){                     
                    result = item.type;
                    return false;
                }
            });
        }

        return result;
    },
    getResultCheckRule: function(ruleOption, keyword){
        var self = this;
        var item = {
            type: typeof(ruleOption.type) != _UNDEFINED ? ruleOption.type : 'danger',
            group: typeof(ruleOption.group) != _UNDEFINED ? ruleOption.group : 'general',
            keyword: typeof(keyword) != _UNDEFINED ? keyword : '',
            title: typeof(ruleOption.title) != _UNDEFINED ? ruleOption.title : '',
            message: typeof(ruleOption.message) != _UNDEFINED ? ruleOption.message : '',
        }

        if(item.group == 'keyword'){
            if(typeof(self.countIssues.keyword[keyword]) == _UNDEFINED) self.countIssues.keyword[keyword] = {
                'danger' : 0,
                'warning': 0,
                'success': 0,
            };
            self.countIssues.keyword[keyword][item.type] += 1;
        }else{
            self.countIssues.general[0][item.type] += 1;
        }        

        return item;
    },
    checkLinkIsInternal: function(link){
        var self = this;
        var result = false;
        var internalLinks = ['/','../../../',self.options.baseUrl];
        $.each(internalLinks, function(index, inLink) {
            if(link.indexOf(inLink) == 0){
                result = true;                
            }
        });
        return result;
    },
    checkLinkIsExternal: function(link){
        var self = this;
        var result = false;
        var externalLinks = ['../../../'];
        $.each(externalLinks, function(index, exlLink) {
            if(link.indexOf(exlLink) != 0){
                result = true;                
            }
        });
        return result;
    },
    checkRules: function(){
        var self = this;

        // reset countIssues
        self.countIssues = {
            general: {
                0:{
                    'danger' : 0,
                    'warning': 0,
                    'success': 0,
                }
            },
            keyword : {}
        };
        var result = [];

        $.each(self.rules, function(type_rule, fieldsApply) {
            $.each(fieldsApply, function(field, rule) {
                if(typeof(self.data[field]) != _UNDEFINED){
                    $.each(rule, function(index, ruleOption) {
                        if(typeof(ruleOption.show) != _UNDEFINED && ruleOption.show == true){
                            switch(type_rule) {
                                case 'length':
                                    if(ruleOption.min <= self.data[field].length && ruleOption.max >= self.data[field].length){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    }
                                    break;
                                case 'words':
                                    if(ruleOption.min <= self.data[field].split(/[\w\u2019\'-]+/).length && ruleOption.max >= self.data[field].split(/[\w\u2019\'-]+/).length){                                        
                                        result.push(self.getResultCheckRule(ruleOption));
                                    }        
                                    break;
                                case 'requireImage':
                                    var img = $(self.data[field]).find('img');
                                    if(typeof(img.attr('src')) == _UNDEFINED){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    }
                                    break;
                                case 'requireLinkInternal':
                                    var links = $(self.data[field]).find('a');
                                    var check = false;
                                    $.each(links, function(i, link) {
                                        if(typeof($(link).attr('href')) != _UNDEFINED && self.checkLinkIsInternal($(link).attr('href'))){
                                            check = true;
                                        }
                                    });
                                    

                                    if(!check && (ruleOption.type == 'warning' || ruleOption.type == 'danger')){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    } 

                                    if(check && ruleOption.type == 'success'){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    } 

                                    break;
                                case 'requireLinkExternal':
                                    var links = $(self.data[field]).find('a');
                                    var check = false;
                                    $.each(links, function(i, link) {
                                        if(typeof($(link).attr('href')) != _UNDEFINED && self.checkLinkIsExternal($(link).attr('href'))){
                                            check = true;
                                        }
                                    });

                                    if(!check && (ruleOption.type == 'warning' || ruleOption.type == 'danger')){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    } 

                                    if(check && ruleOption.type == 'success'){
                                        result.push(self.getResultCheckRule(ruleOption));
                                    }

                                    break;

                                case 'keywordInField':
                                    var keywords = self.data[field];
                                    $.each(keywords, function(i, keyword) {
                                        keyword = keyword.toLowerCase();
                                        if(ruleOption.field == 'url'){
                                            keyword = self.formatToSlug(keyword);
                                        }
                                        var fieldValue = typeof(self.data[ruleOption.field]) != _UNDEFINED ? self.data[ruleOption.field].toLowerCase() : '';
                                        var count = self.countOccurrences(fieldValue, keyword);

                                                                                
                                        var tmpDiv = document.createElement('div');
                                        tmpDiv.innerHTML = fieldValue;
                                        var stripTagFieldValue = tmpDiv.innerText;
                                        tmpDiv.remove();

                                        var keyphrase_density = ((count * keyword.length) /stripTagFieldValue.length) * 100;
                                        if(keyphrase_density.toFixed(1) >= ruleOption.minDensity && keyphrase_density.toFixed(1) <= ruleOption.maxDensity){
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        }

                                    });

                                    break;

                                case 'keywordInBegin':
                                    var keywords = self.data[field];
                                    $.each(keywords, function(i, keyword) {
                                        keyword = keyword.toLowerCase();
                                        var fieldValue = typeof(self.data[ruleOption.field]) != _UNDEFINED ? self.data[ruleOption.field].toLowerCase().replace("\r\n", "\n").split("\n")[0] : '';
                                        var count = self.countOccurrences(fieldValue, keyword);

                                        if(count == 0 && (ruleOption.type == 'warning' || ruleOption.type == 'danger')){
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        } 
                                        
                                        if(count > 0 && ruleOption.type == 'success'){
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        }

                                    });

                                    break;

                                case 'keywordInAltImg':
                                    var keywords = self.data[field];
                                    $.each(keywords, function(i, keyword) {
                                        keyword = keyword.toLowerCase();
                                        
                                        var divTmp = $('<div style="display:none;">').html(self.data.content);
                                        var images = divTmp.find('img');
                                        var count = 0;
                                        $.each(images, function(i, img) {
                                            var alt_img = typeof($(img).attr('alt')) != _UNDEFINED ?  $.trim($(img).attr('alt')).toLowerCase() : '';   

                                            var countInImage = self.countOccurrences(alt_img, keyword);
                                            if(countInImage > 0) {
                                                count = countInImage;
                                            }
                                        });

                                        var hasDanger, hasWarning, hasSuccess = false;
                                        if(count == 0 && ruleOption.type == 'danger' && !hasDanger){                                        
                                            hasDanger = true;
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        } 

                                        if(count == 0 && ruleOption.type == 'warning' && !hasWarning){
                                            hasWarning = true;
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        }
                                        
                                        
                                        if(count > 0 && ruleOption.type == 'success' && !hasSuccess){
                                            hasSuccess = true;
                                            result.push(self.getResultCheckRule(ruleOption, keyword));
                                        }

                                        divTmp.remove();
                                    });
                                    break;
                            }
                        }
                    });
                }
                
            });
               
        });

        self.showAnalysis(result);
    },
    generateHtmlAlert: function(alert){
        var html = '<p><span class="issue-badge"></span><b> ' + alert.title + '</b>: ' + alert.message + '</p>';
        return html;
    },
    countOccurrences: function(string, word){
        return string.split(word).length - 1;
    },
    generateHtmlAnalysis: function(type){
        var self = this;
        $(self.wrapObject + ' .all-analysis .analysis-keyword').remove();
        var html = '';
        
        if(type == 'general' || typeof(type) == _UNDEFINED){
            html += '<div class="wrap-analysis analysis-general" data-type="general">' +
                        '<h4 class="title-analysis">' +
                            '<a>'+
                                '<span class="issue-badge"></span>' + nhMain.getLabel('bai_viet') +
                            '</a>' +
                        '</h4>' +

                        '<div id="analysis-general" class="clearfix">' +
                            '<div class="wrap-issues">' +
                                '<div  class="wrap-danger">' +
                                    '<h5>' +
                                        '<span data-toggle="collapse" href="#list-danger-general">'+
                                            '<span class="kt-badge kt-badge--danger kt-badge--dot"></span> ' + nhMain.getLabel('cac_van_de') + ' <span class="number-issue"></span>' +
                                        '</span>'+
                                    '</h5>' +
                                    
                                    '<div id="list-danger-general" class="list-danger collapse in"></div>' +
                                '</div>' +

                                '<div class="wrap-warning">' +
                                    '<h5>' +
                                        '<span data-toggle="collapse" href="#list-warning-general">' +
                                            '<span class="kt-badge kt-badge--warning kt-badge--dot"></span> ' + nhMain.getLabel('cac_cai_tien') + ' <span class="number-issue"></span>' +
                                        '</span>' +
                                    '</h5>' +                                    
                                    '<div id="list-warning-general" class="list-warning collapse in"></div>' +
                                '</div>' +                                
                                '<div class="wrap-success">' +
                                    '<h5>' +
                                        '<span data-toggle="collapse" href="#list-success-general">' +
                                            '<span class="kt-badge kt-badge--success kt-badge--dot"></span> ' + nhMain.getLabel('ket_qua_tot') + ' <span class="number-issue"></span>' +
                                        '</span>' +
                                    '</h5>' +                                    
                                    '<div id="list-success-general" class="list-success collapse in"></div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>';                        
        }

        if(type == 'keyword' || typeof(type) == _UNDEFINED){
            $.each(self.data.keywords, function(index, keyword) {
                html += '<div class="wrap-analysis analysis-keyword" data-type="keyword" data-keyword="'+ keyword.toLowerCase() +'">'+
                        '<h4 class="title-analysis">'+
                            '<a>'+
                                '<span class="issue-badge"></span> ' + nhMain.getLabel('tu_khoa') + ' <b> "' + keyword + '" </b>' + 
                            '</a>'+
                        '</h4>' +
                        '<div id="analysis-keyword-'+ index +'" class="clearfix" data-keyword="'+ keyword.toLowerCase() +'">' +
                            '<div class="wrap-issues">'+
                                '<div class="wrap-danger">'+
                                    '<h5>' + 
                                        '<span data-toggle="collapse" href="#list-danger-'+ index +'">' +                                             
                                                '<span class="kt-badge kt-badge--danger kt-badge--dot"></span> ' + nhMain.getLabel('cac_van_de') + ' <span class="number-issue"></span>'+ 
                                            '</span>' +
                                    '</h5>' +
                                    '<div id="list-danger-'+ index +'" class="list-danger collapse in"></div>' +
                                '</div>'+
                                '<div class="wrap-warning">'+
                                    '<h5>' + 
                                        '<span data-toggle="collapse" href="#list-warning-'+ index +'">' +                                             
                                                '<span class="kt-badge kt-badge--warning kt-badge--dot"></span> ' + nhMain.getLabel('cac_cai_tien') + ' <span class="number-issue"></span>'+ 
                                            '</span>' +
                                    '</h5>' +
                                    '<div id="list-warning-'+ index +'" class="list-warning collapse in"></div>' +
                                '</div>'+
                                '<div class="wrap-success">'+
                                    '<h5>' + 
                                        '<span data-toggle="collapse" href="#list-success-'+ index +'">' + 
                                                '<span class="kt-badge kt-badge--success kt-badge--dot"></span> ' + nhMain.getLabel('ket_qua_tot') + ' <span class="number-issue"></span>'+ 
                                            '</span>' +
                                    '</h5>' +
                                    '<div id="list-success-'+ index +'" class="list-success collapse in"></div>' +
                                '</div>'+
                            '</div>'+
                        '</div>' +
                    '</div>';
            });
        }

        $(self.wrapObject + ' .all-analysis').append(html);
    },
    addClassBadgeTitle:function(data,object_badge){
        if(data.danger > 0){
            object_badge.addClass('badge-danger');
        }else if(data.warning > 2){
            object_badge.addClass('badge-warning');
        }else if(data.success > 0){
            object_badge.addClass('badge-success');
        }else{
            object_badge.addClass('badge-success');
        }
    },
    showAnalysis: function(data){
        var self = this;
 
        $('.all-analysis .list-danger, .all-analysis .list-warning, .all-analysis .list-success').html('');
        $.each(data, function(index, alert) {
            var wrapAnalysis = $('#analysis-general');
            if(alert.group == 'keyword'){
                wrapAnalysis = $('.analysis-keyword div[data-keyword="'+ alert.keyword +'"]');
            }
            var html = self.generateHtmlAlert(alert);    
            switch(alert.type) {
                case 'danger':
                    wrapAnalysis.find('.list-danger').append(html);
                    break;
                case 'warning':
                    wrapAnalysis.find('.list-warning').append(html);
                    break;
                case 'success':
                    wrapAnalysis.find('.list-success').append(html);
                    break;
            }
        });
        

        //show badge  
        $('.title-analysis .issue-badge').removeClass('badge-danger badge-warning badge-success');  
        var object_badge = $('.analysis-general .title-analysis .issue-badge');
        self.addClassBadgeTitle(self.countIssues.general[0], object_badge);

        //show count
        $('.wrap-issues h5').addClass('hidden');
        $.each(self.countIssues, function(group, item) {                  
            $.each(item, function(keyword, list_type) {
                var wrapAnalysis = $('#analysis-general');                
                if(group == 'keyword'){
                    wrapAnalysis = $('.analysis-keyword div[data-keyword="'+ keyword +'"]');
                    object_badge = $('.analysis-keyword[data-keyword="'+ keyword +'"] .title-analysis .issue-badge');
                    //show badge
                    self.addClassBadgeTitle(self.countIssues.keyword[keyword], object_badge);
                }
     
                $.each(list_type, function(type, count) {
                    var objectTitle = null;
                    switch(type){
                        case 'danger':
                            objectTitle = wrapAnalysis.find('.wrap-danger h5');
                            break;
                        case 'warning':
                            objectTitle = wrapAnalysis.find('.wrap-warning h5');
                            break;
                        case 'success':
                            objectTitle = wrapAnalysis.find('.wrap-success h5');
                            break;
                    }

                    if(!$.isEmptyObject(objectTitle) && count > 0){
                        objectTitle.find('.number-issue').text('('+ count+')');
                        objectTitle.removeClass('hidden');
                    }else if(!$.isEmptyObject(objectTitle)){
                        objectTitle.find('.number-issue').text('');
                    }
                    
                });                
            });            
        });
    },
    formatToSlug: function(text){
        text = text.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẩ|ă|ằ|ắ|ẳ|ặ|ẵ/g, "a");
        text = text.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ă|Ằ|Ắ|Ặ|Ẵ|ẵ/g, "a");
        text = text.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ+/g, "e");
        text = text.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ+/g, "e");
        text = text.replace(/ì|í|ị|ỉ|ĩ/g, "i");
        text = text.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "i");
        text = text.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ+/g, "o");
        text = text.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ+/g, "o");
        text = text.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
        text = text.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "u");
        text = text.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
        text = text.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "y");
        text = text.replace(/đ/g, "d");
        text = text.replace(/Đ/g, "d");
        return text.toLowerCase().trim().replace(/[^\w ]+/g, '').replace(/ +/g, '-').replace(/&/g, '-and-');
    },
    clearData: function(){
        var self = this;
        self.data = {
            title: '',
            content: '',
            contentHtml: '',
            contentWords: 0,
            contentChars: 0,
            seoTitle: '',        
            url:'',
            keywords: [],
            seoDescription: ''
        };
        self.countIssues = {};

        self.generateHtmlAnalysis();
    },
    getScore: function(){
        var self = this;
        var seoScore = _SUCCESS;
        var dataSeo = self.countIssues.general[0];
        if (dataSeo.danger > 0){
            seoScore = _DANGER;
        } else if (dataSeo.warning > 2){
            seoScore = _WARNING;
        }
        
        var seoKeywordScore = null;
        var checkKeyword = [];
        var dataKeyWord = self.countIssues.keyword;
        if (!$.isEmptyObject(dataKeyWord)) {
            $.each(dataKeyWord, function (key, item) {
                var seoKeyword = _SUCCESS;
                if (item.danger > 0){
                    seoKeyword = _DANGER;
                } else if (item.warning > 2){
                    seoKeyword = _WARNING;
                }
                checkKeyword.push(seoKeyword);
            });

            seoKeywordScore = _SUCCESS;
            if(checkKeyword.indexOf(_DANGER) !== -1){
                seoKeywordScore = _DANGER;
            } else if (checkKeyword.indexOf(_WARNING) !== -1){
                seoKeywordScore = _WARNING;
            }
        }

        return {
            seoScore: seoScore,
            seoKeywordScore: seoKeywordScore
        };
    }

}