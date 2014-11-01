<?php

namespace events;

class App
{
    /**
	 * Fetch a list of payment handlers.
	 */
    public static function payment_handlers()
    {
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
}
