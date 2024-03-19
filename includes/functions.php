<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Add login button for affinidi on the login form.
 *
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/login_form
 */
function affinidi_login_form_button()
{
    ?>
    <div class="affinidi-login-wrapper">
    <p style="text-align:center; margin:5px;">Log in <b>passwordless</b> with</p>
    <a style="margin:1em auto;" rel="nofollow" class="button" id="affinidi-login-m"
       href="<?php echo esc_url(site_url('?auth=affinidi')); ?>">Affinidi Login</a>
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
        'class'  => 'button',
        'target' => '_self',
        'text'   => 'Affinidi Login'
    ], $atts);

    return '<a id="affinidi-login-m" rel="nofollow" class="' . esc_attr($a['class']) . '" href="' . esc_url(site_url('?auth=affinidi')) . '" title="' . esc_attr($a['title']) . '" target="' . esc_attr($a['target']) . '">' . esc_html($a['text']) . '</a>';
}

add_shortcode('affinidi_login', 'affinidi_login_button_shortcode');

/**
 * Get user login redirect.
 * Just in case the user wants to redirect the user to a new url.
 *
 * @return string
 */
function affinidi_login_user_redirect_url(): string
{
    $admin_options = new Affinidi_Login_Admin_Options();
    // Retrieves the URL to the user’s dashboard.
    $user_redirect_set = $admin_options->redirect_to_dashboard == '1' ? 'wp-admin' : 'index.php';
    $user_redirect     = apply_filters('affinidi_user_redirect_url', $user_redirect_set);

    return $user_redirect;
}

function affinidi_login_extract_prop($data, $name) {
    $val = null;
    foreach ($data as $customData) {
        if (isset($customData[$name])) {
            $val = $customData[$name];
            break;
        }
    }
    return $val;
}

/**
 * For security reason, we don't want to expose error description to FE,
 * instead give developers to view the full error via PHP Log
 */
function affinidi_login_write_log ( $log )  {
    if ( is_array( $log ) || is_object( $log ) ) {
        error_log( print_r( $log, true ) );
    } else {
        error_log( $log );
    }
}