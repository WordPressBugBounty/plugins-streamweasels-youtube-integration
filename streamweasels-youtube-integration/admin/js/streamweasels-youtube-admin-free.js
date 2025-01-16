(function( $ ) {
	'use strict';

	jQuery(document).ready(function(a) {
		if (jQuery('body').hasClass('toplevel_page_streamweasels-youtube')) {
			
			var clipboard = new ClipboardJS('#sw-copy-shortcode');

			clipboard.on('success', function (e) {
				jQuery(e.trigger).addClass('tooltipped');
				jQuery(e.trigger).on('mouseleave', function() {
					jQuery(e.trigger).removeClass('tooltipped');
				})
			});

			jQuery(document).on("click", "#sw-delete-log-submit", function(a) {
				jQuery("#sw-delete-log").val("1");
			});	

			jQuery("#sw-form").on("submit", function(e) {
				var fieldCount = 0;
				($('#sw-channel-id').val() !== '') ? fieldCount++ : '';
				($('#sw-playlist-id').val() !== '') ? fieldCount++ : '';
				($('#sw-livestream-id').val() !== '') ? fieldCount++ : '';
				if (fieldCount > 1) {
					e.preventDefault();
					$('.postbox-main-settings .inside .notice').remove()
					var error = '<div class="notice notice-error"><p><strong>Error. You cannot use the Channel field, the Playlist field, or the livestream field together! Choose only one.</strong></p></div>';
					$('.postbox-main-settings .inside').prepend(error)
					$('html').animate({scrollTop: $(".postbox-main-settings").offset().top}, 1000);
				}
			});			
			
			var tileRoundedCorners = document.querySelector('#sw-tile-rounded-corners');
			var tileRoundedCornersVal = tileRoundedCorners.value;
			var tileRoundedCornersInit = new Powerange(tileRoundedCorners, { callback: function() {tileRoundedCorners.nextElementSibling.nextElementSibling.innerHTML = tileRoundedCorners.value+'px'}, step: 5, max: 40, start: tileRoundedCornersVal, hideRange: true });					
			
			jQuery('#sw-form input, #sw-form select').on('change', function() {
				buildShortcode()
			})
	
			buildShortcode()
		  
		  function buildShortcode() {
	
			var game = '';
			var channels = '';
			var playlist = '';
			var limit = '';
			var livestreams = '';
			var colour = '';
			var embed = '';
			var embedTheme = '';
			var embedChat = '';
			var embedTitle = '';
			var embedMuted = '';
			var showOffline = '';
			var showOfflineText = '';
			var showOfflineImage = '';
			var autoplay = '';
			var featuredStream = '';
			var title = '';
			var subtitle = '';
			var offlineImage = '';
			var logoImage = '';
			var profileImage = '';
			var logoBgColour = '';
			var logoBorderColour = '';
			var maxWidth = '';
			var tileLayout = '';
			var tileSorting = '';
			var liveInfo = '';
			var tileBgColour = '';
			var tileTitleColour = '';
			var tileSubtitleColour = '';
			var tileRoundedCorners = '';
			var hoverEffect = '';
			var hoverColour = '';
			var plugin = 'streamweasels-youtube';
	
			 if ($('#sw-channel-id').val() !== '') {
			  var game = ($('#sw-channel-id').val() !== '') ? ' channel="'+$('#sw-channel-id').val()+'"' : '';
			}
			if ($('#sw-channel-id').val() !== '') {
				var channels = ($('#sw-channel-id').val() !== '') ? ' channel="'+$('#sw-channel-id').val()+'"' : '';
			  } else if ($('#sw-playlist-id').val() !== '') {
			  	var playlist = ' playlist="'+$('#sw-playlist-id').val()+'"';
			} else if ($('#sw-livestream-id').val() !== '') {
			  	var livestreams = ' livestream="'+$('#sw-livestream-id').val()+'"';
			}
			if ($('#sw-colour-theme').val() !== '') {
				var colour = ' colour-theme="'+$('#sw-colour-theme').val()+'"';
			}			
			if ($('#sw-limit').val() !== '') {
			  var limit = ' limit="'+$('#sw-limit').val()+'"';
			}
			if ($('#sw-layout').val() !== '') {
				var layout = ' layout="'+$('#sw-layout').val()+'"';
			}
			
			// Wall settings
			// if ($('#sw-tile-column-count').val() !== '') {
			// 	var wallColumnCount = ' wall-column-count="'+$('#sw-tile-column-count').val()+'"';
			// }

			// if ($('#sw-tile-column-spacing').val() !== '') {
			// 	var wallColumnSpacing = ' wall-column-spacing="'+$('#sw-tile-column-spacing').val()+'"';
			// }			
	
			var shortcode = '['+plugin+layout+channels+playlist+livestreams+limit+colour+']';
			$('.postbox-shortcode .advanced-shortcode').html(shortcode)
			}	

			$('.upload-btn').click(function(e) {
				e.preventDefault();
				var btn = $(this);
				var image = wp.media({ 
					title: 'Upload Image',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(e){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field 
					btn.prev().val(image_url);
				});
			});			

		}

		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-wall')) {
			var columnCount = document.querySelector('#sw-tile-column-count');
			var columnCountVal = columnCount.value;
			var columnCountInit = new Powerange(columnCount, { callback: function() {columnCount.nextElementSibling.nextElementSibling.innerHTML = columnCount.value+' columns'}, step: 1, max: 6, start: columnCountVal, hideRange: true });		

			var columnSpacing = document.querySelector('#sw-tile-column-spacing');
			var columnSpacingVal = columnSpacing.value;
			var columnSpacingInit = new Powerange(columnSpacing, { callback: function() {columnSpacing.nextElementSibling.nextElementSibling.innerHTML = columnSpacing.value+'px'}, step: 5, max: 100, start: columnSpacingVal, hideRange: true });
		}

		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-showcase')) {
			jQuery('#sw-showcase-controls-bg-colour').wpColorPicker();
			jQuery('#sw-showcase-controls-arrow-colour').wpColorPicker();
			// sw-showcase-cslide-count
			var showcaseSlideCount = document.querySelector('#sw-showcase-slide-count');
			var showcaseSlideCountVal = showcaseSlideCount.value ?? 4;
			var showcaseSlideCountInit = new Powerange(showcaseSlideCount, { callback: function() {showcaseSlideCount.nextElementSibling.nextElementSibling.innerHTML = showcaseSlideCount.value+' columns'}, step: 1, max: 6, min: 3, start: showcaseSlideCountVal, hideRange: true });			
		}		

		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-player')) {
			jQuery('#sw-welcome-bg-colour').wpColorPicker();
			jQuery('#sw-welcome-text-colour').wpColorPicker();

			$('.upload-btn').click(function(e) {
				e.preventDefault();
				var btn = $(this);
				var image = wp.media({ 
					title: 'Upload Image',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(e){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field 
					btn.prev().val(image_url);
				});
			});
						
		}	
		
		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-status')) {

			jQuery('#sw-accent-colour').wpColorPicker();
			jQuery('#sw-logo-background-colour').wpColorPicker();
			jQuery('#sw-carousel-background-colour').wpColorPicker();
			jQuery('#sw-carousel-arrow-colour').wpColorPicker();

			$('.upload-btn').click(function(e) {
			  e.preventDefault();
			  var btn = $(this);
			  var image = wp.media({ 
				  title: 'Upload Image',
				  // mutiple: true if you want to upload multiple files at once
				  multiple: false
			  }).open()
			  .on('select', function(e){
				  // This will return the selected image from the Media Uploader, the result is an object
				  var uploaded_image = image.state().get('selection').first();
				  // We convert uploaded_image to a JSON object to make accessing it easier
				  // Output to the console uploaded_image
				  console.log(uploaded_image);
				  var image_url = uploaded_image.toJSON().url;
				  // Let's assign the url value to the input field 
				  btn.prev().val(image_url);
			  });
		  });			  
		} 	
		
        $(document).on('click', '.swyi-notice button.notice-dismiss', function() {
            var data = {
                action : 'swyi_admin_notice_dismiss',
            };
    
            $.post(ajaxurl, data, function (response) {
                console.log(response, 'DONE!');
            });
        });	
        
        $(document).on('click', '.swyi-notice a.dismiss-for-good', function() {
            $('.swyi-notice').hide()
            var data = {
                action : 'swyi_admin_notice_dismiss_for_good',
            };
            $.post(ajaxurl, data, function (response) {
                console.log(response, 'SWTI - Notice Closed');
            });
        });		

	})


})( jQuery );
