<?php

/**
 * This file is called when the auth param is found in the URL.
 */
defined('ABSPATH') or die('No script kiddies please!');

// Redirect the user back to the home page if logged in.
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

// do a session a start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_options = new Affinidi_Login_Admin_Options();

// Grab a copy of the options and set the redirect location.
$user_redirect = affinidi_login_user_redirect_url();

// Not processing form or storing data.
// phpcs:disable WordPress.Security.NonceVerification.Recommended

// Check for custom redirect
if (!empty($_GET['redirect_uri'])) {
    $user_redirect = sanitize_url($_GET['redirect_uri']);
}

// Authenticate Check and Redirect
if (!isset($_GET['code']) && !isset($_GET['error_description']) && !isset($_GET['state'])) {

    // generate code verifier and challenge
    $verifier_bytes = bin2hex(openssl_random_pseudo_bytes(32));
    $code_verifier = rtrim(strtr(base64_encode($verifier_bytes), "+/", "-_"), "=");
    $challenge_bytes = hash("sha256", $code_verifier, true);
    $code_challenge = rtrim(strtr(base64_encode($challenge_bytes), "+/", "-_"), "=");

    // generate random state
    $state = bin2hex(openssl_random_pseudo_bytes(9));

    // store the code verifier in the SESSION
    $_SESSION[$state] = $code_verifier;

    $params = [
        'oauth'                 => 'authorize',
        'response_type'         => 'code',
        'scope'                 => 'openid',
        'client_id'             => $admin_options->client_id,
        'redirect_uri'          => site_url('?auth=affinidi'),
        'state'                 => urlencode($state),
        'code_challenge'        => $code_challenge,
        'code_challenge_method' => 'S256',
    ];
    $params = http_build_query($params);
    wp_redirect(sanitize_url($admin_options->backend) . '/oauth2/auth?' . $params);
    exit;
}

// Check for error 
if (empty($_GET['code']) && !empty($_GET['error_description'])) {
    // set error desc
    $log_message = sprintf("Affinidi Login: %s",
        sanitize_text_field($_GET['error_description'])
    );
    affinidi_login_write_log($log_message);
    // redirect user with error code
    wp_safe_redirect(wp_login_url() . "?message=affinidi_login_failed");
    
    exit;
}

// grab the code and state
$auth_code = sanitize_text_field($_GET['code']);
$state = sanitize_text_field($_GET['state']);

// phpcs:enable WordPress.Security.NonceVerification.Recommended

// Handle the callback from the backend is there is one.
if (!empty($auth_code)) {

    $backend    = sanitize_url($admin_options->backend) . '/oauth2/token';

    // retrieve the code verifier from the SESSION
    $code_verifier = sanitize_text_field($_SESSION[$state]);

    $request_body = [
        'grant_type'    => 'authorization_code',
        'code'          => $auth_code,
        'client_id'     => $admin_options->client_id,
        'code_verifier' => $code_verifier,
        'redirect_uri'  => site_url('?auth=affinidi')
    ];

    $response = wp_remote_post( $backend, array(
            'method'      => 'POST',
            'body'        => $request_body
        )
    );

    if (is_wp_error($response)) {
        // log error description
        $error_message = sanitize_text_field($response->get_error_message());
        affinidi_login_write_log($error_message);
        // redirect user with error code
        wp_safe_redirect(wp_login_url() . "?message=wp_error_affinidi_login");
        exit;
    }

    $tokens = json_decode(wp_remote_retrieve_body($response));

    if (isset($tokens->error)) {
        // log error description on server side
        $log_message = sprintf("Affinidi Login: error=%s&error_description=%s",
            sanitize_text_field($tokens->error),
            sanitize_text_field($tokens->error_description)
        );
        affinidi_login_write_log($log_message);
        // redirect user with error code
        wp_safe_redirect(wp_login_url() . "?message=affinidi_login_failed");
        exit;
    }
    // parse ID Token from Affinidi Login response
    $id_token = $tokens->id_token;
    $info = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $id_token)[1]))), true);

    // extract and sanitize user info from ID Token
    $user_info = affinidi_login_extract_prop($info);
    
    $user_id = null;

    if (email_exists($user_info['email']) == false) {
        if ($admin_options->login_only == 1) {
            wp_safe_redirect(wp_login_url() . '?message=affinidi_login_only');
            exit;
        }

        // Does not have an account... Register and then log the user in
        $random_password = wp_generate_password($length = 16, $extra_special_chars = true);
        $user_data = [
            'user_email'   => $user_info['email'],
            'user_login'   => (!empty($user_info['given_name']) ? $user_info['given_name'] : $user_info['email']), // default to mail if not present
            'user_pass'    => $random_password,
            'last_name'    => $user_info['family_name'],
            'first_name'    => $user_info['given_name'],
            'display_name' => (!empty($user_info['given_name']) ? $user_info['given_name'] : $user_info['email']) // default to mail if not present
        ];

        $user_id = wp_insert_user($user_data);

        // Trigger new user created action so that there can be modifications to what happens after the user is created.
        // This can be used to collect other information about the user.
        do_action('affinidi_user_created', $info, 1);

    } else {
        // Already Registered... Log the User In using ID or Email
        $user = get_user_by('email', $user_info['email']);

        /*
         * Added just in case the user is not used but the email may be. If the user returns false from the user ID,
         * we should check the user by email. This may be the case when the users are preregistered outside of OAuth
         */
        if (!$user) {
             // Get the user by name
            $user = get_user_by('login', $user_info['given_name']);
        }

        // Trigger action when a user is logged in.
        // This will help allow extensions to be used without modifying the core plugin.
        do_action('affinidi_user_login', $info, 1);

        $user_id = $user->ID;
    }

    // Did we retrieved or created the user successfully?
    if (!empty($user_id)) {
        // set current user session
        wp_clear_auth_cookie();
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        if (is_user_logged_in()) {
            wp_safe_redirect($user_redirect);
            exit;
        }
    }
}



