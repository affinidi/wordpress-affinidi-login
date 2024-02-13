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
        'token_endpoint_auth_method' => 'client_secret_post',
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
        .affinidi {
    position: relative;
    display: flex !important;
    gap: 1rem;
    align-items: center;
    justify-content: center;
    background-color: rgb(29, 88, 252) !important;
    border: 2px solid rgb(29, 88, 252);
    border-radius: 48px !important;
    box-sizing: border-box !important;
    transition: all 0.125s ease-in-out 0s;
    font-size: 14pt !important;
    font-family: Figtree;
    font-weight: 500;
    line-height: 2.75rem !important;
        }
    </style>
    <p style="text-align:center; margin:5px;">Log in <b>passwordless</b> with</p>
    <a style="color:#FFF; width:100%; text-align:center; margin-bottom:1em;" class="affinidi button button-primary button-large"
       href="<?php echo site_url('?auth=affinidi'); ?>">
       <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADkAAAAwCAYAAACrF9JNAAAAAXNSR0IArs4c6QAAAl1JREFUaEPtms1SgzAQx0lLR+pJvelRHwSYOuP4Cr5MwY8H0Bdq6E3fwaue/DhZndLGWVpsyvCxBAiJlCPNJvvLbpp/whKjpqdnHnnQFSF7NukPnbRul+GnD++Xxhc1wm+KHdq0Thm2bVo7UsUYwHrmwVi0D7aYUcZ+gmX4Hk1Q1iMf0rScPjkcZ0VLFBiinAUrFbI/OJ7UDZeclDB8cZOpLAfStBzTPJmIRqmsHaTxYv7qxnaNQ1Zdd2UB4/YRKPvwIaqNQspIz6JJgPStmkXZ/66m5fSM/dStoMixun+3HdsOaBCI9ltpCxEdtKwdY4y5rutSStF7Kz+GNpAACKBlJygSKCJGsm0gkjCmKKhWkAAqkrbaQYpE8w8yFtiyUxEz3mL+tqWPR6NLn06fMKZRmxWkZEWD9m7dcD573jIJpo/G+cUVupsIsi1Vg/UyCQl2AAmwmCeCrCqbMANVaZMGeX17b9zcPaC6JaqnKlCkQcL7wfAMB6l6qtYCqYIILwpHViSxKUtUX495kURDFs2iCr/Hsi7pi+/7vud5ufdDm31SBZIcH3aQu0iu0kM7gc5n9W5NcrPRjS1EZzGAFemkE7JOV4Fe5kzZkaOWpodm7DFrs0924fojvgJRVcLWc5GlKt3aL16gV7qSVJmTh/z3l8tYrZoMmDYCXSRNY1htIAkhwr7uPsKqIN6b/ZweJ3RLQoGvAKl6o4jL84YKlLK2LeklLrwjTR/L+LIWflw5kUxMed2wWXCbFdNyAWFeVWSRiorq6RAVk61EMs15/kt1WuVkXBEJthiwOtP1F+VEe4wNzA9zAAAAAElFTkSuQmCC" width="28.5" height="24" alt="Affinidi Logo">Affinidi Login</a>
    <div style="clear:both;"></div>
    <p style="text-align:center;">- or with passwords below -</p>
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
        'type'   => 'primary',
        'title'  => 'Affinidi Login',
        'class'  => 'sso-button',
        'target' => '_blank',
        'text'   => 'Affinidi Login'
    ], $atts);

    return '<a class="' . $a['class'] . '" href="' . site_url('?auth=affinidi') . '" title="' . $a['title'] . '" target="' . $a['target'] . '">' . $a['text'] . '</a>';
}
add_shortcode('sso_button', 'affinidi_login_button_shortcode');

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
