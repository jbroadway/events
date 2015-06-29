<?php

namespace events;

class App {
    /**
	 * Fetch a list of payment handlers.
	 */
    public static function payment_handlers () {
        $files = glob ('apps/*/conf/payments.php');
        $files = is_array ($files) ? $files : array ();
        $providers = array ();
        foreach ($files as $file) {
            $ini = parse_ini_file ($file);
            if (! is_array ($ini)) {
                continue;
            }
            $providers = array_merge ($providers, $ini);
        }
        asort ($providers);

        return $providers;
    }
	
	/**
	 * Fetch discount, if available. Returns it as a number out of 100,
	 * or 0 if there is no discount.
	 */
	public static function discount () {
		$cb = \Appconf::events ('Events', 'discount_callback');
		if (is_callable ($cb)) {
			return call_user_func ($cb);
		}
		return 0;
	}
	
	/**
	 * Fetch the "allow invoice" option, if available. Returns true or false.
	 * If true, members of that type can be invoiced for the event separately
	 * and bypass the payment processing to register for the event immediately.
	 */
	public static function allow_invoice () {
		$cb = \Appconf::events ('Events', 'allow_invoice_callback');
		if (is_callable ($cb)) {
			return call_user_func ($cb);
		}
		return false;
	}
}
