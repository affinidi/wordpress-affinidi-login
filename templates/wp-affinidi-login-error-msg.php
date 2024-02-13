<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

$message = $wp_query->get('message');
$alert_message = '';
if ($message == 'affinidi_login_only') {
    $alert_message = 'This affinidi vault user doesn\'t exists in wordpress, please use another.';
} elseif ($message == 'affinidi_sso_failed') {
    $alert_message = 'Affinidi Login Failed. User mismatch or clash with existing data and SSO can not complete.';
} elseif ($message == 'affinidi_id_not_allowed') {
    $alert_message = 'For security reasons, this user can not use Single Sign On.';
}

if (!empty($alert_message)) : ?>
    <div class="error">
        <p class="alertbar"><?= $alert_message . ' <a href="' . site_url('?auth=affinidi') . '">Please try again</a>'?></p>
    </div>
<?php endif; ?>
