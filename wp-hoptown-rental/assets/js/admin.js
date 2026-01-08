/**
 * Admin JavaScript
 *
 * @package Hoptown_Rental
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		function syncGalleryMeta(value) {
			if (!window.wp || !wp.data || !wp.data.dispatch) {
				return;
			}

			var metaKey = (window.hoptownAdmin && window.hoptownAdmin.galleryMetaKey) ? window.hoptownAdmin.galleryMetaKey : '_hoptown_gallery';

			wp.data.dispatch('core/editor').editPost({
				meta: {
					[metaKey]: value
				}
			});
		}

		function markGalleryDirty() {
			var dirtyInput = $('#hoptown_gallery_dirty');
			if (dirtyInput.length) {
				dirtyInput.val('1');
			}
		}

		function getGalleryIdsFromList() {
			var ids = [];
			$('.hoptown-gallery-images li').each(function() {
				var id = $(this).attr('data-id');
				if (id) {
					ids.push(String(id));
				}
			});
			return ids;
		}

		function setGalleryValue(ids) {
			var value = ids.join(',');
			$('input[name="hoptown_gallery"]').val(value);
		}

		// Day-specific pricing toggle
		$(document).on('change', '#hoptown_use_day_pricing', function() {
			if ($(this).is(':checked')) {
				$('.hoptown-day-pricing').show();
			} else {
				$('.hoptown-day-pricing').hide();
			}
		});

		// Gallery management
		var frame;

		$(document).on('click', '.hoptown-add-gallery-images', function(e) {
			e.preventDefault();

			var galleryInput = $('#hoptown_gallery');
			if (!galleryInput.length) {
				return;
			}

			if (frame) {
				frame.open();
				return;
			}

			var galleryTitle = (window.hoptownAdmin && window.hoptownAdmin.galleryTitle) ? window.hoptownAdmin.galleryTitle : 'Select Gallery Images';
			var galleryButton = (window.hoptownAdmin && window.hoptownAdmin.galleryButton) ? window.hoptownAdmin.galleryButton : 'Add to Gallery';

			frame = wp.media({
				title: galleryTitle,
				button: {
					text: galleryButton
				},
				multiple: true
			});

			frame.on('select', function() {
				var attachments = frame.state().get('selection').toJSON();
				var currentIds = getGalleryIdsFromList();
				var galleryList = $('.hoptown-gallery-images');

				$.each(attachments, function(index, attachment) {
					if (currentIds.indexOf(String(attachment.id)) === -1) {
						currentIds.push(attachment.id);
						var thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						galleryList.append('<li data-id="' + attachment.id + '"><img src="' + thumbnail + '" /><a href="#" class="hoptown-remove-gallery-image">&times;</a></li>');
					}
				});

				setGalleryValue(currentIds);
				markGalleryDirty();
				syncGalleryMeta($('#hoptown_gallery').val());
				frame.close();
			});

			frame.open();
		});

		$(document).on('click', '.hoptown-remove-gallery-image', function(e) {
			e.preventDefault();
			var $li = $(this).closest('li');
			var imageId = $li.attr('data-id');
			var galleryInput = $('#hoptown_gallery');
			if (!galleryInput.length) {
				$li.remove();
				return;
			}

			$li.remove();
			var newIds = getGalleryIdsFromList().filter(function(id) {
				return id && String(id) !== String(imageId);
			});

			setGalleryValue(newIds);
			markGalleryDirty();
			syncGalleryMeta($('#hoptown_gallery').val());
		});

		(function initGalleryState() {
			var currentIds = getGalleryIdsFromList();
			if (currentIds.length) {
				setGalleryValue(currentIds);
			}
		})();
	});

})(jQuery);
