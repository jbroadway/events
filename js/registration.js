var event_registration = (function ($) {
	var self = {};

	// options
	self.opts = {};
	
	// the prefix for api requests
	self.prefix = '/events/api/registration/';
	
	// enable/disable debugging output to the console
	self.debug = false;
	
	self.timer_interval = null;
	
	// helper function to verify parameters
	var _has = function (obj, prop) {
		return obj.hasOwnProperty (prop);
	};
	
	// console log wrapper for debugging
	var _log = function (obj) {
		if (self.debug) {
			console.log (obj);
		}
		return obj;
	};
	
	self.init = function (opts) {
		self.opts = $.extend (self.opts, opts);
		
		$(self.opts.attendee_list).on ('change', self.reserve);
		$(self.opts.inputs).on ('submit', self.save_inputs);
		
		if (self.opts.reservation_id != 0) {
			$(self.opts.attendee_list).val (self.opts.num_attendees);
			$(self.opts.company_field).val (self.opts.company);
			$(self.opts.attendee_list).trigger ('change');
		}
		
		if (self.opts.price <= 0) {
			$(self.opts.subtotal_block).hide ();
		}
	};
	
	self.error = function (msg, real) {
		if (real === 'Unauthorized') {
			// back to the event page to sign in again
			history.go (-1);
			return;
		}
		$(self.opts.show_error).html (msg).show ();
	};
	
	self.format_price = function (price) {
		price = price / 100;
		return price.toFixed (2);
	};
	
	self.reserve = function (e) {
		e.preventDefault ();

		self.opts.num_attendees = $(this).val ();
		
		if (self.opts.num_attendees == 0) {
			self.hide_inputs ();
		}

		var url = self.prefix + 'reserve/' + self.opts.event_id + '/' + self.opts.num_attendees;

		$.post (url, {}, self.reserved);
	};

	self.reserved = function (res) {
		if (! res.success) {
			return self.error (
				self.opts.strings.reserved_error,
				res.error
			);
		}

		self.opts.reservation_id = res.data.id;

		$(self.opts.timer).data ('timer', res.data.timer).show ();
		self.timer_interval = setInterval (self.update_timer, 1000);

		$(self.opts.subtotal).html ('$' + self.format_price (self.opts.price * res.data.num_attendees));

		if (self.opts.num_attendees > 0) {
			self.show_inputs ();
		}
	};
	
	self.update_timer = function () {
		var timer = $(self.opts.timer),
			remaining = timer.data ('timer');
		
		if (remaining > 1) {
			remaining--;
			timer.data ('timer', remaining);

			var min = parseInt (remaining / 60),
				sec = remaining % 60;

			if (sec.toString ().length === 1) {
				sec = "0" + sec.toString ();
			}

			timer.html (self.opts.strings.time_remaining + ": " + min + ':' + sec);
		} else {
			timer.html (
				self.opts.strings.expired
				+ '<br />'
				+ '<a href="#" onclick="window.location.reload (true); return false">'
				+ self.opts.strings.start_over
				+ '</a>'
			);
		}
	};
	
	self.hide_inputs = function () {
		$(self.opts.inputs).hide ();
	};
	
	self.show_inputs = function () {		
		// grab the old names before clearing the html
		var form = $(self.opts.inputs),
			inputs = $(self.opts.attendee_inputs),
			attendee_inputs = form[0].elements['attendees[]'],
			names = [];
		
		if (attendee_inputs !== undefined) {
			for (var i = 0; i < attendee_inputs.length; i++) {
				names.push (attendee_inputs[i].value);
			}
		} else if (self.opts.attendees.length > 0) {
			names = self.opts.attendees;
		}

		// clear the fields
		inputs.html ('');

		// rebuild without losing existing names (unless they chose less this time :P)
		for (var i = 0; i < parseInt (self.opts.num_attendees); i++) {
			var html = '<span class="event-attendee">' + (i + 1) + '. <input type="text" name="attendees[]"';

			if (names[i] !== undefined) {
				html += ' value="' + names[i] + '"';
			}

			html += ' class="event-attendee-input" size="30" /></span>';

			inputs.append (html);
		}

		form.show ();
	};
	
	self.save_inputs = function (e) {
		e.preventDefault ();
		
		var company = $(self.opts.company_field).val (),
			attendees = [],
			form = $(self.opts.inputs),
			inputs = $(self.opts.attendee_inputs),
			attendee_inputs = $('.event-attendee-input');

		$('.event-attendee-input').each (function () {
			attendees.push ($(this).val ());
		});
		
		for (var i = 0; i < attendees.length; i++) {
			if (attendees[i].length === 0) {
				return self.error (self.opts.strings.attendees_error);
			}
		}
		
		var params = {company: company, 'attendees[]': attendees};
		
		$.post (self.prefix + 'update/' + self.opts.reservation_id, params, self.updated);
	};
	
	self.updated = function (res) {
		if (! res.success) {
			return self.error (self.opts.strings.updated_error, res.error);
		}

		if (self.opts.price > 0) {
			self.open_payment_form (res);
		} else {
			self.complete_registration (res);
		}
	};

	self.open_payment_form = function (res) {
		if ($('#invoice').is (':checked')) {
			window.location.href = '/events/register/' + self.opts.event_id + '/invoice';
		} else {
			window.location.href = '/events/register/' + self.opts.event_id + '/checkout';
		}
	};
	
	self.complete_registration = function (res) {
		$.post (self.prefix + 'complete/' + self.opts.reservation_id, {}, self.completed);
	};
	
	self.completed = function (res) {
		if (! res.success) {
			return self.error (self.opts.strings.completed_error, res.error);
		}
		
		window.location.href = '/events/registered/' + self.opts.event_id + '/' + self.opts.reservation_id;
	};
	
	return self;
})(jQuery);