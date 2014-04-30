var event_registration = (function ($) {
	var self = {};

	// options
	self.opts = {};
	
	// the prefix for api requests
	self.prefix = '/events/api/registration/';
	
	// enable/disable debugging output to the console
	self.debug = false;
	
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
		
		if (self.opts.reservation_id != 0) {
			$(self.opts.attendee_list).val (self.opts.num_attendees);
			$(self.opts.attendee_list).trigger ('change');
		}
	};
	
	self.error = function (msg, real) {
		$(self.opts.show_error).html (msg).show ();
	};
	
	self.format_price = function (price) {
		price = price / 100;
		return price.toFixed (2);
	};
	
	self.reserve = function (e) {
		e.preventDefault ();

		var num_attendees = $(this).val (),
			url = self.prefix + 'reserve/' + self.opts.event_id + '/' + num_attendees;

		$.post (url, self.reserved);
	};

	self.reserved = function (res) {
		if (! res.success) {
			return self.error (
				'Unable to make a reservation at this time. Please try again later.',
				res.error
			);
		}

		self.opts.reservation_id = res.data.id;

		$(self.opts.subtotal).html ('$' + self.format_price (self.opts.price * res.data.num_attendees));
	};
	
	return self;
})(jQuery);