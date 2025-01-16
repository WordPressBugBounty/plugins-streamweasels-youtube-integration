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

			jQuery('#sw-tile-bg-colour').wpColorPicker();
			jQuery('#sw-tile-title-colour').wpColorPicker();
			jQuery('#sw-tile-subtitle-colour').wpColorPicker();
			jQuery('#sw-logo-bg-colour').wpColorPicker();
			jQuery('#sw-logo-border-colour').wpColorPicker();	
			jQuery('#sw-hover-colour').wpColorPicker();			
			
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
			// if ($('#sw-layout').val() !== '') {
			// 	var layout = ' layout="'+$('#sw-layout').val()+'"';
			// }	
			if ($('#sw-embed').val() !== '') {
				var embed = ' embed="'+$('#sw-embed').val()+'"';
			}	
			// if ($('#sw-embed-theme').val() !== '') {
			// 	var embedTheme = ' embed-theme="'+$('#sw-embed-theme').val()+'"';
			// }	
	
			// var embedChat = ( $('#sw-embed-chat').prop('checked') ? ' embed-chat="1"' : ' embed-chat="0"' );
			// var embedTitle = ( $('#sw-embed-title').prop('checked') ? ' embed-title="1"' : ' embed-title="0"' );
			var embedMuted = ( $('#sw-embed-muted').prop('checked') ? ' embed-muted="1"' : ' embed-muted="0"' );
	
			var showOffline = ( $('#sw-show-offline').prop('checked') ? ' show-offline="1"' : ' show-offline="0"' );
			var autoload = ( $('#sw-autoload').prop('checked') ? ' autoload="1"' : ' autoload="0"' );
			var autoplay = ( $('#sw-autoplay').prop('checked') ? ' autoplay="1"' : ' autoplay="0"' );
			// var profileImage = ( $('#sw-profile-image').prop('checked') ? ' profile-image="1"' : ' profile-image="0"' );
	
			if ($('#sw-show-offline-text').val() !== '') {
				var showOfflineText = ' show-offline-text="'+$('#sw-show-offline-text').val()+'"';
			}
			if ($('#sw-show-offline-image').val() !== '') {
				var showOfflineImage = ' show-offline-image="'+$('#sw-show-offline-image').val()+'"';
			}
			// if ($('#sw-featured-stream').val() !== '') {
			// 	var featuredStream = ' featured-stream="'+$('#sw-featured-stream').val()+'"';
			// }	
			if ($('#sw-title').val() !== '') {
				var title = ' title="'+$('#sw-title').val()+'"';
			}	
			if ($('#sw-subtitle').val() !== '') {
				var subtitle = ' subtitle="'+$('#sw-subtitle').val()+'"';
			}								
			// if ($('#sw-offline-image').val() !== '') {
			// 	var offlineImage = ' offline-image="'+$('#sw-offline-image').val()+'"';
			// }
			if ($('#sw-logo-image').val() !== '') {
				var logoImage = ' logo-image="'+$('#sw-logo-image').val()+'"';
			}	
			if ($('#sw-logo-bg-colour').val() !== '') {
				var logoBgColour = ' logo-bg-colour="'+$('#sw-logo-bg-colour').val()+'"';
			}
			if ($('#sw-logo-border-colour').val() !== '') {
				var logoBorderColour = ' logo-border-colour="'+$('#sw-logo-border-colour').val()+'"';
			}	
			if ($('#sw-max-width').val() !== '') {
				var maxWidth = ' max-width="'+$('#sw-max-width').val()+'"';
			}		
			if ($('#sw-tile-layout').val() !== '') {
				var tileLayout = ' tile-layout="'+$('#sw-tile-layout').val()+'"';
			}	
			if ($('#sw-tile-sorting').val() !== '') {
				var tileSorting = ' tile-sorting="'+$('#sw-tile-sorting').val()+'"';
			}
			// if ($('#sw-live-info').val() !== '') {
			// 	var liveInfo = ' live-info="'+$('#sw-live-info').val()+'"';
			// }				
			if ($('#sw-tile-bg-colour').val() !== '') {
				var tileBgColour = ' tile-bg-colour="'+$('#sw-tile-bg-colour').val()+'"';
			}	
			if ($('#sw-tile-title-colour').val() !== '') {
				var tileTitleColour = ' tile-title-colour="'+$('#sw-tile-title-colour').val()+'"';
			}	
			if ($('#sw-tile-subtitle-colour').val() !== '') {
				var tileSubtitleColour = ' tile-subtitle-colour="'+$('#sw-tile-subtitle-colour').val()+'"';
			}		
			if ($('#sw-tile-rounded-corners').val() !== '') {
				var tileRoundedCorners = ' tile-rounded-corners="'+$('#sw-tile-rounded-corners').val()+'"';
			}
			if ($('#sw-hover-colour').val() !== '') {
				var hoverColour = ' hover-colour="'+$('#sw-hover-colour').val()+'"';
			}
			if ($('#sw-hover-effect').val() !== '') {
				var hoverEffect = ' hover-effect="'+$('#sw-hover-effect').val()+'"';
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
	
			var shortcode = '['+plugin+layout+game+channels+playlist+livestreams+limit+embed+embedMuted+showOffline+showOfflineText+showOfflineImage+autoplay+autoload+title+subtitle+logoImage+logoBgColour+logoBorderColour+maxWidth+tileLayout+tileSorting+tileBgColour+tileTitleColour+tileSubtitleColour+tileRoundedCorners+hoverColour+hoverEffect+']';
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

		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-feature')) {
			jQuery('#sw-feature-controls-bg-colour').wpColorPicker();
			jQuery('#sw-feature-controls-arrow-colour').wpColorPicker();
		}	

		if (jQuery('body').hasClass('youtube-integration_page_streamweasels-youtube-showcase')) {
			jQuery('#sw-showcase-controls-bg-colour').wpColorPicker();
			jQuery('#sw-showcase-controls-arrow-colour').wpColorPicker();
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
