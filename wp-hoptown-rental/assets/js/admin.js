/**
 * Admin JavaScript
 *
 * @package Hoptown_Rental
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Day-specific pricing toggle
		$('#hoptown_use_day_pricing').on('change', function() {
			if ($(this).is(':checked')) {
				$('.hoptown-day-pricing').show();
			} else {
				$('.hoptown-day-pricing').hide();
			}
		});

		// Gallery management
		var frame;

		$('.hoptown-add-gallery-images').on('click', function(e) {
			e.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Select Gallery Images',
				button: {
					text: 'Add to Gallery'
				},
				multiple: true
			});

			frame.on('select', function() {
				var attachments = frame.state().get('selection').toJSON();
				var galleryInput = $('#hoptown_gallery');
				var currentIds = galleryInput.val() ? galleryInput.val().split(',') : [];
				var galleryList = $('.hoptown-gallery-images');

				$.each(attachments, function(index, attachment) {
					if (currentIds.indexOf(String(attachment.id)) === -1) {
						currentIds.push(attachment.id);
						var thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						galleryList.append('<li data-id="' + attachment.id + '"><img src="' + thumbnail + '" /><a href="#" class="hoptown-remove-gallery-image">&times;</a></li>');
					}
				});

				galleryInput.val(currentIds.join(','));
			});

			frame.open();
		});

		$(document).on('click', '.hoptown-remove-gallery-image', function(e) {
			e.preventDefault();
			var $li = $(this).closest('li');
			var imageId = $li.data('id');
			var galleryInput = $('#hoptown_gallery');
			var currentIds = galleryInput.val().split(',');
			var newIds = currentIds.filter(function(id) {
				return String(id) !== String(imageId);
			});

			galleryInput.val(newIds.join(','));
			$li.remove();
		});
	});

})(jQuery);
