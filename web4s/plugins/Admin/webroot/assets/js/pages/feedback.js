"use strict";

var nhFeeback = {
	formElement: $('#feedback-form'),
	files: {},
	init: function(){
		var self = this;

		if(self.formElement.length == 0) return;

		self.fileDropzone.init();

		var validator = self.formElement.validate({
			ignore: ":hidden",
			rules: {
				full_name: {
					required: true,
					maxlength: 100
				},
				phone: {
					required: true,
					maxlength: 12
				},
				content: {
					required: true,
					maxlength: 500
				},
			},
			messages: {
				full_name: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },

                phone: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                },
                content: {
                    required: nhMain.getLabel('vui_long_nhap_thong_tin'),
                    maxlength: nhMain.getLabel('thong_tin_nhap_qua_dai')
                }
            },

            errorPlacement: function(error, element) {
            	var messageRequired = element.attr('message-required');
            	if(typeof(messageRequired) != _UNDEFINED && messageRequired.length > 0){
            		error.text(messageRequired);
            	}
            	error.addClass('invalid-feedback')

                var group = element.closest('.input-group');
                if (group.length) {
                    group.after(error);
                }else if(element.hasClass('select2-hidden-accessible')){
            		element.closest('.form-group').append(error);
                }else{
                	element.after(error);
                }
            },

			invalidHandler: function(event, validator) {
				KTUtil.scrollTo(validator.errorList[0].element, nhMain.validation.offsetScroll);
			}
		});

		$(document).on('click', '.btn-save:not(.disabled)', function(e) {
			e.preventDefault();

			if (validator.form()) {
				self.formElement.find('input[name="files"]').val(JSON.stringify(self.files));
				nhMain.initSubmitForm(self.formElement, $(this));
			}
		});
	},
	fileDropzone: {
		init: function(){
			var self = this;

			// set the dropzone container id
		    var id = '#files-attach';
		    if($(id).length == 0) return;

		    // set the preview element template
		    var previewNode = $(id + " .dropzone-item");
		    previewNode.id = "";
		    var previewTemplate = previewNode.parent('.dropzone-items').html();
		    previewNode.remove();

		    var filesAttach = new Dropzone(id, { // Make the whole body a dropzone
		        url: adminPath + '/feedback/upload-files', // Set the url for your upload script location
		        headers: {
			        'X-CSRF-Token': csrfToken
			    },
		        maxFiles: 5,
		        previewTemplate: previewTemplate,
		        acceptedFiles: ".jpg,.jpeg,.png,pdf,.doc,.docx,.xls,.xlsx",
		        maxFilesize: 2, // Max filesize in MB
		        autoQueue: true, // Make sure the files aren't queued until manually added
		        previewsContainer: id + " .dropzone-items", // Define the container to display the previews
		        clickable: id + " .dropzone-select" // Define the element that should be used as click trigger to select files.
		    });

		    filesAttach.on("addedfile", function(file) {
		        // Hookup the start button
		        file.previewElement.querySelector(id + " .dropzone-start").onclick = function() { 
		        	filesAttach.enqueueFile(file); 
		        };
		        $(document).find( id + ' .dropzone-item').css('display', '');
		        $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'inline-block');
		    });

		    // Update the total progress bar
		    filesAttach.on("totaluploadprogress", function(progress) {
		        $(this).find( id + " .progress-bar").css('width', progress + "%");
		    });

		    filesAttach.on("sending", function(file) {
		    	nhFeeback.formElement.find('.btn-save').addClass('disabled');
		        // Show the total progress bar when upload starts
		        $( id + " .progress-bar").css('opacity', '1');
		        // And disable the start button
		        file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
		    });

		    // Hide the total progress bar when nothing's uploading anymore
		    filesAttach.on("complete", function(progress) {
		        var thisProgressBar = id + " .dz-complete";
		        setTimeout(function(){
		            $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
		        }, 300)
		    });

		    // Setup the button for remove all files
		    document.querySelector(id + " .dropzone-remove-all").onclick = function() {
		        $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
		        filesAttach.removeAllFiles(true);

		        nhFeeback.files = {};
		    };

		    // On all files completed upload
		    filesAttach.on("queuecomplete", function(progress){
		        $( id + " .dropzone-upload").css('display', 'none');

		        nhFeeback.formElement.find('.btn-save').removeClass('disabled');
		    });

		    // On all files removed
		    filesAttach.on("removedfile", function(file){
		    	var uuid = typeof(file.upload.uuid) != _UNDEFINED ? file.upload.uuid : null;
		    	delete nhFeeback.files[uuid];
		        if(filesAttach.files.length < 1){		        	 
		            $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
		        }
		    });

		    // attach callback to the `success` event
			filesAttach.on("success", function(file, res ) {
			  	var result = JSON.parse(res);

			  	if(result.code == _SUCCESS){
			  		var data = typeof(result.data) != _UNDEFINED ? result.data : {};

			  		var url = typeof(data.url) != _UNDEFINED && data.url.length > 0 ? data.url : null;
			  		var uuid = typeof(file.upload.uuid) != _UNDEFINED ? file.upload.uuid : null;
			  		if(url != null && uuid != null){
			  			nhFeeback.files[uuid] = url;
			  		}			  		
			  	}
			});
		}
	}
}

$(document).ready(function() {
	nhFeeback.init();
});
