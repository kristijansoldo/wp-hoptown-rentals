/**
 * Booking JavaScript
 *
 * @package Hoptown_Rental
 */

(function($) {
	'use strict';

	var HoptownBooking = {
		currentMonth: new Date().getMonth(),
		currentYear: new Date().getFullYear(),
		selectedDate: null,
		bookedDates: [],
		inflatableId: null,
		rentalPrice: 0,
		deliveryPrice: 0,

		init: function() {
			this.initCalendar();
			this.initForm();
		},

		initCalendar: function() {
			var self = this;
			var $calendar = $('.hoptown-booking-calendar');

			if ($calendar.length === 0) {
				return;
			}

			this.inflatableId = $calendar.data('inflatable-id');
			this.bookedDates = $calendar.data('booked-dates') || [];

			this.renderCalendar();

			$calendar.on('click', '.hoptown-calendar-prev', function() {
				self.currentMonth--;
				if (self.currentMonth < 0) {
					self.currentMonth = 11;
					self.currentYear--;
				}
				self.renderCalendar();
			});

			$calendar.on('click', '.hoptown-calendar-next', function() {
				self.currentMonth++;
				if (self.currentMonth > 11) {
					self.currentMonth = 0;
					self.currentYear++;
				}
				self.renderCalendar();
			});

			$calendar.on('click', '.hoptown-calendar-day:not(.hoptown-day-disabled):not(.hoptown-day-past):not(.hoptown-day-empty)', function() {
				var date = $(this).data('date');
				self.selectDate(date);
			});
		},

		renderCalendar: function() {
			var $calendar = $('.hoptown-booking-calendar');
			var monthNames = (hoptownRental.i18n && hoptownRental.i18n.monthNames) || [
				'January', 'February', 'March', 'April', 'May', 'June',
				'July', 'August', 'September', 'October', 'November', 'December'
			];

			$calendar.find('.hoptown-calendar-month').text(monthNames[this.currentMonth] + ' ' + this.currentYear);

			var firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
			firstDay = firstDay === 0 ? 6 : firstDay - 1; // Adjust for Monday start

			var daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();

			var $days = $calendar.find('.hoptown-calendar-days');
			$days.empty();

			var today = new Date();
			today.setHours(0, 0, 0, 0);

			// Empty cells before first day
			for (var i = 0; i < firstDay; i++) {
				$days.append('<div class="hoptown-calendar-day hoptown-day-empty"></div>');
			}

			// Days
			for (var day = 1; day <= daysInMonth; day++) {
				var date = this.formatDate(new Date(this.currentYear, this.currentMonth, day));
				var $day = $('<div class="hoptown-calendar-day" data-date="' + date + '">' + day + '</div>');

				var dayDate = new Date(this.currentYear, this.currentMonth, day);

				// Check if date is in the past
				if (dayDate < today) {
					$day.addClass('hoptown-day-past');
				}
				// Check if date is booked
				else if (this.bookedDates.indexOf(date) !== -1) {
					$day.addClass('hoptown-day-disabled');
				}
				// Check if date is selected
				else if (date === this.selectedDate) {
					$day.addClass('hoptown-day-selected');
				}

				$days.append($day);
			}
		},

		selectDate: function(date) {
			this.selectedDate = date;
			this.renderCalendar();

			// Update form
			$('#hoptown-booking-date').val(date);
			$('.hoptown-selected-date').text(this.formatDateDisplay(date));

			// Fetch pricing
			this.fetchPricing(date);
		},

		fetchPricing: function(date) {
			var self = this;

			$.ajax({
				url: hoptownRental.restUrl + '/price/' + this.inflatableId,
				method: 'GET',
				data: {
					date: date
				},
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', hoptownRental.nonce);
				},
				success: function(response) {
					if (response.available) {
						self.rentalPrice = response.rental_price;
						self.deliveryPrice = response.delivery_price;
						self.updatePricing();
						self.showFormSections();
					} else {
						alert(hoptownRental.i18n.dateNotAvailable || 'This date is not available.');
						self.selectedDate = null;
						self.renderCalendar();
					}
				},
				error: function() {
					alert(hoptownRental.i18n.pricingError || 'Error fetching pricing information.');
				}
			});
		},

		updatePricing: function() {
			$('.hoptown-rental-price').text(this.formatPrice(this.rentalPrice));
			$('.hoptown-delivery-price').text(this.formatPrice(this.deliveryPrice));

			var deliveryMethod = $('input[name="delivery_method"]:checked').val();
			var total = this.rentalPrice;

			if (deliveryMethod === 'delivery') {
				total += this.deliveryPrice;
				$('.hoptown-delivery-price-row').show();
			} else {
				$('.hoptown-delivery-price-row').hide();
			}

			$('.hoptown-total-price').text(this.formatPrice(total));
		},

		showFormSections: function() {
			$('.hoptown-pricing-info').show();
			$('.hoptown-customer-info').show();
			$('.hoptown-delivery-section').show();
			$('.hoptown-submit-section').show();
		},

		initForm: function() {
			var self = this;

			// Delivery method change
			$(document).on('change', 'input[name="delivery_method"]', function() {
				var method = $(this).val();

				if (method === 'pickup') {
					$('.hoptown-pickup-fields').show();
					$('.hoptown-delivery-fields').hide();
					$('#hoptown-pickup-time').prop('required', true);
					$('#hoptown-delivery-address').prop('required', false);
				} else {
					$('.hoptown-pickup-fields').hide();
					$('.hoptown-delivery-fields').show();
					$('#hoptown-pickup-time').prop('required', false);
					$('#hoptown-delivery-address').prop('required', true);
				}

				self.updatePricing();
			});

			// Form submission
			$(document).on('submit', '#hoptown-booking-form', function(e) {
				e.preventDefault();
				self.submitBooking($(this));
			});
		},

		submitBooking: function($form) {
			var self = this;
			var formData = $form.serializeArray();
			var data = {};

			$.each(formData, function(i, field) {
				data[field.name] = field.value;
			});

			data.action = 'hoptown_submit_booking';
			data.nonce = hoptownRental.ajaxNonce;

			$('.hoptown-submit-booking').prop('disabled', true).text(hoptownRental.i18n.submitting || 'Submitting...');
			$('.hoptown-form-messages').empty();

			$.ajax({
				url: hoptownRental.ajaxUrl,
				method: 'POST',
				data: data,
				success: function(response) {
					if (response.success) {
						self.showMessage('success', response.data.message);
						$form[0].reset();
						self.selectedDate = null;
						self.renderCalendar();
						$('.hoptown-pricing-info, .hoptown-customer-info, .hoptown-delivery-section, .hoptown-submit-section').hide();
						$('.hoptown-selected-date').text(hoptownRental.i18n.selectDate || 'Please select a date from the calendar');

						// Refresh booked dates
						self.fetchBookedDates();
					} else {
						self.showMessage('error', response.data.message);
					}
				},
				error: function() {
					self.showMessage('error', hoptownRental.i18n.submitError || 'An error occurred. Please try again.');
				},
				complete: function() {
					$('.hoptown-submit-booking').prop('disabled', false).text(hoptownRental.i18n.reserve || 'Reserve');
				}
			});
		},

		fetchBookedDates: function() {
			var self = this;

			$.ajax({
				url: hoptownRental.restUrl + '/availability/' + this.inflatableId,
				method: 'GET',
				beforeSend: function(xhr) {
					xhr.setRequestHeader('X-WP-Nonce', hoptownRental.nonce);
				},
				success: function(response) {
					self.bookedDates = response.booked_dates;
					self.renderCalendar();
				}
			});
		},

		showMessage: function(type, message) {
			var $message = $('<div class="hoptown-message ' + type + '">' + message + '</div>');
			$('.hoptown-form-messages').append($message);

			setTimeout(function() {
				$message.fadeOut(function() {
					$(this).remove();
				});
			}, 5000);
		},

		formatDate: function(date) {
			var year = date.getFullYear();
			var month = String(date.getMonth() + 1).padStart(2, '0');
			var day = String(date.getDate()).padStart(2, '0');
			return year + '-' + month + '-' + day;
		},

		formatDateDisplay: function(dateString) {
			var date = new Date(dateString);
			var day = String(date.getDate()).padStart(2, '0');
			var month = String(date.getMonth() + 1).padStart(2, '0');
			var year = date.getFullYear();
			return day + '.' + month + '.' + year;
		},

		formatPrice: function(price) {
			var format = (hoptownRental.i18n && hoptownRental.i18n.priceFormat) || {
				decimal: '.',
				thousands: ',',
				currency: '€',
				position: 'after',
				space: true
			};

			var amount = parseFloat(price || 0);
			var parts = amount.toFixed(2).split('.');
			var integer = parts[0];
			var fraction = parts[1];
			var withThousands = integer.replace(/\B(?=(\d{3})+(?!\d))/g, format.thousands);
			var formatted = withThousands + (fraction ? format.decimal + fraction : '');
			var space = format.space ? ' ' : '';

			if (format.position === 'before') {
				return format.currency + space + formatted;
			}

			return formatted + space + format.currency;
		}
	};

	$(document).ready(function() {
		HoptownBooking.init();
	});

})(jQuery);
