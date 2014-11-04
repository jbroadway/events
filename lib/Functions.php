<?php

function events_yes_no()
{
    return array (
        (object) array ('key' => 'no', 'value' => i18n_get ('No')),
        (object) array ('key' => 'yes', 'value' => i18n_get ('Yes'))
    );
}
