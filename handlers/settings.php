<?php

/**
 * This is the settings form for the blog app.
 */

$this->require_admin ();

$page->layout = 'admin';
$page->title = __ ('Events Settings');

$form = new Form ('post', $this);

$form->data = array (
    'title' => $appconf['Events']['title'],
    'layout' => $appconf['Events']['layout'],
    'event_layout' => $appconf['Events']['event_layout'],
    'gcal_link' => $appconf['Events']['gcal_link'],
    'payment_handler' => $appconf['Events']['payment_handler'],
    'payment_handlers' => events\App::payment_handlers ()
);

echo $form->handle (function ($form) {
    $settings = Appconf::merge ('events', array (
        'Events' => array (
            'title' => $_POST['title'],
            'layout' => $_POST['layout'],
            'event_layout' => $_POST['event_layout'],
            'gcal_link' => $_POST['gcal_link'],
            'payment_handler' => $_POST['payment_handler']
        )
    ));

    if (! Ini::write ($settings, 'conf/app.events.' . ELEFANT_ENV . '.php')) {
        printf ('<p>%s</p>', __ ('Unable to save changes. Check your folder permissions and try again.'));

        return;
    }

    $form->controller->add_notification (__ ('Settings saved.'));
    $form->controller->redirect ('/events/admin');
});
