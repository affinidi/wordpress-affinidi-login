<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

function defaults()
{
    return [
        'client_id'             => '',
        'backend'               => '',
        'redirect_to_dashboard' => 0,
        'login_only'            => 0,
    ];
}

/**
 * get option value
 *
 * @param string $option_name
 *
 * @return void|string
 */
function affinidi_get_option(string $option_name)
{
    $options = array_shift(get_options(array(WP_Affinidi_Login_Admin::OPTIONS_NAME)));

    if (!empty($v = $options[$option_name])) {
        return $v;
    }
}

function affinidi_set_options(string $key, $value)
{
    $options = array_shift(get_options(array(WP_Affinidi_Login_Admin::OPTIONS_NAME)));
    $options[$key] = $value;
    update_option(WP_Affinidi_Login_Admin::OPTIONS_NAME, $options);
}
function remove_footer_admin () 
{
    echo '';
}
 
add_filter('admin_footer_text', 'remove_footer_admin');

/**
 * Get the login url of affinidi
 *
 * @param string $redirect
 *
 * @return string
 */
function get_affinidi_login_url(string $redirect = ''): string
{
    $params = [
        'oauth'         => 'authorize',
        'response_type' => 'code',
        'scope'         => 'openid',
        'client_id'     => affinidi_get_option('client_id'),
        'code_challenge' => $code_challenge,
        'code_challenge_method' => 'S256',
        'token_endpoint_auth_method' => 'none',
        'redirect_uri'  => site_url('?auth=affinidi'),
        'state'         => urlencode($user_redirect)
    ];
    $params = http_build_query($params);
    return affinidi_get_option('backend') . '/oauth2/auth?' . $params;
}

/**
 * Add login button for affinidi on the login form.
 *
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/login_form
 */
function affinidi_login_form_button()
{
    ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree&display=swap');
        .affinidi-login {
            background-color: #1d58fc !important;
            color: #ffffff !important;
            box-sizing: border-box !important;
            transition: all 0.125s ease-in-out 0s;
            width: 188px !important;
            height: 48px !important;
            display: flex !important;
            flex-direction: row !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 12px !important;
            margin: 0 auto !important;
            padding: 12px 20px !important;
            object-fit: contain !important;
            border-radius: 12px !important;

            flex-grow: 0 !important;
            font-family: Figtree !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            font-stretch: normal !important;
            font-style: normal !important;
            line-height: 1.25 !important;
            letter-spacing: 0.6px;
            text-align: left !important;
        }
    </style>
    <div class="affinidi-login-wrapper">
    <p style="text-align:center; margin:5px;">Log in <b>passwordless</b> with</p>
    <a style="color:#FFF; width:100%; text-align:center; margin-bottom:1em;" class="affinidi-login button"
       href="<?php echo site_url('?auth=affinidi'); ?>">
       <svg xmlns="http://www.w3.org/2000/svg" width="30" height="24" viewBox="0 0 30 24" fill="none">
            <path d="M3.927 20.281A11.966 11.966 0 0 0 12.61 24c3.416 0 6.499-1.428 8.684-3.719H3.926h.001zM21.295 6.762H1.813A11.933 11.933 0 0 0 .707 10.48h20.588V6.762zM21.293 3.719A11.967 11.967 0 0 0 12.609 0a11.966 11.966 0 0 0-8.683 3.719h17.367zM21.295 13.521H.707c.167 1.319.548 2.57 1.106 3.719h19.482v-3.718zM23.41 6.762c.558 1.148.94 2.4 1.106 3.718h4.78V6.762H23.41z" fill="#fff"/>
            <path d="M29.293 20.281h-8V24h8V20.28zM23.41 17.24h5.886v-3.718h-4.78a11.933 11.933 0 0 1-1.106 3.718zM29.293 0h-8v3.719h8V0z" fill="#fff"/>
            <path d="M24.514 10.48a11.934 11.934 0 0 0-1.106-3.72 12.017 12.017 0 0 0-2.115-3.041v16.563a12.05 12.05 0 0 0 2.115-3.042 11.935 11.935 0 0 0 1.2-5.24c0-.516-.031-1.023-.094-1.522v.001z" fill="#040822"/>
        </svg>
       Affinidi Login</a>
       <div style="clear:both;"></div>
    </div>
    <?php
}
// Fires following the ‘Password’ field in the login form.
// It can be used to customize the built-in WordPress login form. Use in conjunction with ‘login_head‘ (for validation).
add_action('login_message', 'affinidi_login_form_button');

/**
 * Login Button Shortcode
 *
 * @param  [type] $atts [description]
 *
 * @return [type]       [description]
 */
function affinidi_login_button_shortcode($atts)
{
    $a = shortcode_atts([
        'title'  => 'Affinidi Login',
        'class'  => 'affinidi-login button',
        'target' => '_blank',
        'text'   => 'Affinidi Login'
    ], $atts);

    return '<a class="' . $a['class'] . '" href="' . site_url('?auth=affinidi') . '" title="' . $a['title'] . '" target="' . $a['target'] . '">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="24" viewBox="0 0 30 24" fill="none">
        <path d="M3.927 20.281A11.966 11.966 0 0 0 12.61 24c3.416 0 6.499-1.428 8.684-3.719H3.926h.001zM21.295 6.762H1.813A11.933 11.933 0 0 0 .707 10.48h20.588V6.762zM21.293 3.719A11.967 11.967 0 0 0 12.609 0a11.966 11.966 0 0 0-8.683 3.719h17.367zM21.295 13.521H.707c.167 1.319.548 2.57 1.106 3.719h19.482v-3.718zM23.41 6.762c.558 1.148.94 2.4 1.106 3.718h4.78V6.762H23.41z" fill="#fff"/>
        <path d="M29.293 20.281h-8V24h8V20.28zM23.41 17.24h5.886v-3.718h-4.78a11.933 11.933 0 0 1-1.106 3.718zM29.293 0h-8v3.719h8V0z" fill="#fff"/>
        <path d="M24.514 10.48a11.934 11.934 0 0 0-1.106-3.72 12.017 12.017 0 0 0-2.115-3.041v16.563a12.05 12.05 0 0 0 2.115-3.042 11.935 11.935 0 0 0 1.2-5.24c0-.516-.031-1.023-.094-1.522v.001z" fill="#040822"/>
    </svg>
    ' . $a['text'] . '</a>';
}
add_shortcode('affinidi_login', 'affinidi_login_button_shortcode');

/**
 * Get user login redirect.
 * Just in case the user wants to redirect the user to a new url.
 *
 * @return string
 */
function affinidi_get_user_redirect_url(): string
{
    $options           = get_option('affinidi_options');
    // Retrieves the URL to the user’s dashboard.
    $user_redirect_set = $options['redirect_to_dashboard'] == '1' ? 'wp-admin' : 'index.php';
    $user_redirect     = apply_filters('affinidi_user_redirect_url', $user_redirect_set);

    return $user_redirect;
}

function extractProp($data, $name) {
    $val = null;
    foreach ($data as $customData) {
        if (isset($customData[$name])) {
            $val = $customData[$name];
            break;
        }
    }
    return $val;
}
