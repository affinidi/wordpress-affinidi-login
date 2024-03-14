<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

function defaults()
{
    return [
        'client_id'            => '',
        'backend'              => '',
        'redirect_user_origin' => 0,
        'enable_ecommerce_support' => '',
        'ecommerce_sync_address_info' => 'billing',
        'ecommerce_show_al_button' => 'top_form',
        'affinidi_login_loginform_header' => 'Log in passwordless with',
        'affinidi_login_regform_header' => 'Sign up seamlessly with',
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

    $redirect_to = affinidi_get_user_redirect_url();
    ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,600;1,600&display=swap');

        #affinidi-login-m {
            border: 0;
            width: 188px;
            height: 48px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            box-sizing: border-box;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            object-fit: contain;
            border-radius: 48px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="30" height="24" viewBox="0 0 30 24" fill="none"><path d="M3.927 20.281A11.966 11.966 0 0 0 12.61 24c3.416 0 6.499-1.428 8.684-3.719H3.926h.001zM21.295 6.762H1.813A11.933 11.933 0 0 0 .707 10.48h20.588V6.762zM21.293 3.719A11.967 11.967 0 0 0 12.609 0a11.966 11.966 0 0 0-8.683 3.719h17.367zM21.295 13.521H.707c.167 1.319.548 2.57 1.106 3.719h19.482v-3.718zM23.41 6.762c.558 1.148.94 2.4 1.106 3.718h4.78V6.762H23.41z" fill="%23fff"/><path d="M29.293 20.281h-8V24h8V20.28zM23.41 17.24h5.886v-3.718h-4.78a11.933 11.933 0 0 1-1.106 3.718zM29.293 0h-8v3.719h8V0z" fill="%23fff"/><path d="M24.514 10.48a11.934 11.934 0 0 0-1.106-3.72 12.017 12.017 0 0 0-2.115-3.041v16.563a12.05 12.05 0 0 0 2.115-3.042 11.935 11.935 0 0 0 1.2-5.24c0-.516-.031-1.023-.094-1.522v.001z" fill="%23040822"/></svg>') no-repeat 20px center;
            background-color: #1d58fc;
            color: #ffffff;
            padding-left: 60px;

            flex-grow: 0;
            font-family: Figtree;
            font-size: 16px;
            font-weight: 600;
            font-stretch: normal;
            font-style: normal;
            line-height: 1.25;
            letter-spacing: 0.6px;
            text-decoration: none !important;
        }

        #affinidi-login-m:hover {
            background-color: #4a79fd;
            filter: contrast(90%);
        }

        #affinidi-login-m:active {
            background-color: #1d58fc;
        }

        #affinidi-login-m-loading {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g clip-path="url(%23uzl55nssua)"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.727 4.545v2.728l3.864-3.637L9.727 0v2.727C5.457 2.727 2 5.982 2 10c0 1.427.444 2.755 1.198 3.873l1.41-1.327A5.09 5.09 0 0 1 3.932 10c0-3.01 2.598-5.455 5.795-5.455zm6.53 1.582-1.41 1.328c.425.763.676 1.627.676 2.545 0 3.01-2.599 5.454-5.796 5.454v-2.727l-3.863 3.637L9.727 20v-2.727c4.27 0 7.727-3.255 7.727-7.273a6.904 6.904 0 0 0-1.197-3.873z" fill="%23fff"/></g><defs><clipPath id="uzl55nssua"><path fill="%23fff" d="M0 0h20v20H0z"/></clipPath></defs></svg>') no-repeat center;
            background-color: #4a79fd;
            filter: contrast(90%);
        }

        #affinidi-login-m:disabled {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="30" height="24" viewBox="0 0 30 24" fill="none"><path d="M3.927 20.281A11.966 11.966 0 0 0 12.61 24c3.416 0 6.499-1.428 8.684-3.719H3.926h.001zM21.295 6.762H1.813A11.933 11.933 0 0 0 .707 10.48h20.588V6.762zM21.293 3.719A11.967 11.967 0 0 0 12.609 0a11.966 11.966 0 0 0-8.683 3.719h17.367zM21.295 13.521H.707c.167 1.319.548 2.57 1.106 3.719h19.482v-3.718zM23.41 6.762c.558 1.148.94 2.4 1.106 3.718h4.78V6.762H23.41z" fill="%23fff"/><path d="M29.293 20.281h-8V24h8V20.28zM23.41 17.24h5.886v-3.718h-4.78a11.933 11.933 0 0 1-1.106 3.718zM29.293 0h-8v3.719h8V0z" fill="%23fff"/><path d="M24.514 10.48a11.934 11.934 0 0 0-1.106-3.72 12.017 12.017 0 0 0-2.115-3.041v16.563a12.05 12.05 0 0 0 2.115-3.042 11.935 11.935 0 0 0 1.2-5.24c0-.516-.031-1.023-.094-1.522v.001z" fill="%23fff"/></svg>') no-repeat 20px center;
            background-color: #e6e6e9;
        }

    </style>
    <div class="affinidi-login-wrapper">
    <p style="text-align:center; margin:5px;">Log in <b>passwordless</b> with</p>
    <a style="margin:1em auto;" rel="nofollow" class="affinidi-login" id="affinidi-login-m"
       href="<?php echo esc_url(site_url('?auth=affinidi&state=' . $redirect_to)); ?>">Affinidi Login</a>
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
function affinidi_login_button_shortcode($atts = array())
{
    if (is_user_logged_in()) {
        return;
    }

    $a = shortcode_atts([
        'title'  => 'Affinidi Login',
        'class'  => 'affinidi-login',
        'target' => '_self',
        'text'   => 'Affinidi Login'
    ], $atts);

    $redirect_to = affinidi_get_user_redirect_url();

    return '<a id="affinidi-login-m" rel="nofollow" class="' . esc_attr($a['class']) . '" href="' . esc_url(site_url('?auth=affinidi&state='.$redirect_to)) . '" title="' . esc_attr($a['title']) . '" target="' . esc_attr($a['target']) . '">' . esc_html($a['text']) . '</a>';
}

add_shortcode('affinidi_login', 'affinidi_login_button_shortcode');

function get_wc_login_form_button($atts = array()) {

    $options = array_shift(get_options(array('affinidi_options')));

    $display_button_header = $options['affinidi_login_loginform_header'];

    return '
        <div class="form-affinidi-login"><div><p class="form-affinidi-login-header">' . esc_html($display_button_header) . '</p></div><div>' . affinidi_login_button_shortcode($atts) . '</div></div>';
}

function get_wc_regs_form_button($atts = array()) {

    $options = array_shift(get_options(array('affinidi_options')));

    $display_button_header = $options['affinidi_login_regform_header'];

    return '
        <div class="form-affinidi-login"><div><p class="form-affinidi-login-header">' . esc_html($display_button_header) . '</p></div><div>' . affinidi_login_button_shortcode($atts) . '</div></div>';
}

/**
 * Get user login redirect.
 * Just in case the user wants to redirect the user to a new url.
 *
 * @return string
 */
function affinidi_get_user_redirect_url(): string
{
    // Global WP instance
    global $wp;

    // Homepage as default redirect
    $redirect_url = home_url();

    // Redirect users if directly logging-in from wp-login.php form or redirect to dashboard option is set
    if ( $GLOBALS['pagenow'] == 'wp-login.php' ) {
        $redirect_url = admin_url();
    }

    // Check if we are passing redirect_to value, use it
    if ( isset( $_REQUEST['redirect_to'] ) ) {
        $redirect_url = esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) );
    } else {
        // Get the current page of the user where the button is triggered (if redirect to dashboard is not set)
        if ( affinidi_get_option('redirect_user_origin') == 1) {
            if ( ! empty( $wp->request ) ) {
                if ( ! empty( $wp->did_permalink ) && $wp->did_permalink == true ) {
                    // build url from the current page with query strings attached
                    $redirect_url = home_url( add_query_arg( $_GET, trailingslashit( $wp->request ) ) );
                } else {
                    $redirect_url = home_url( add_query_arg( null, null ) );
                }
            } else {
                // homepage with query strings
                if ( ! empty( $wp->query_string ) ) {
                    $redirect_url = home_url( '?' . $wp->query_string );
                }
            }
        }
    }

    // generate random state
    $state = md5( mt_rand() . microtime( true ) );
    // store redirect_to transient info to options
    $affinidi_state_values = array(
        $state => array(
            'redirect_to' => $redirect_url
        )
    );
    set_transient("affinidi_user_redirect_to" . $state, $affinidi_state_values, 300);

    return $state;

}

function extract_claim($idToken, $field, $isCustom = true) {
    
    if ($isCustom) {
        return isset($idToken['custom'][$field]) ? $idToken['custom'][$field] : "";    
    }
    // return from top-level
    return isset($idToken[$field]) ? $idToken[$field] : "";

}

function extract_user_info($info) {

    // extract user info
    $email = extract_claim($info, 'email', false);
    $firstName = extract_claim($info, 'given_name', false);
    $lastName = extract_claim($info, 'family_name', false);
    $displayName = trim("{$firstName} {$lastName}");

    return array(
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'display_name' => $displayName
    );

}

function extract_contact_info($info) {
    // get list of countries for transformation
    include_once(AFFINIDI_PLUGIN_DIR . '/templates/countries-list.php');
    // extract user info
    $streetAddress = extract_claim($info['address'], 'street_address', false);
    $locality = extract_claim($info['address'], 'locality', false);
    $region = extract_claim($info['address'], 'region', false);
    $postalCode = extract_claim($info['address'], 'postal_code', false);
    $country = extract_claim($info['address'], 'country', false);
    $phoneNumber = extract_claim($info, 'phone_number', false);

    // get the country code
    $country = array_search($country, $countries_list);

    return array(
        'address_1' => $streetAddress,
        'city' => $locality,
        'state' => $region,
        'postcode' => $postalCode,
        'country' => $country,
        'phone' => $phoneNumber
    );
}

function set_wc_billing_address($customer, $userInfo, $contactInfo) {
    // set billing info
    $customer->set_billing_first_name($userInfo['first_name']);
    $customer->set_billing_last_name($userInfo['last_name']);
    $customer->set_billing_email($userInfo['email']);
    $customer->set_billing_phone($contactInfo['phone']);

    $customer->set_billing_address($contactInfo['address_1']);
    $customer->set_billing_city($contactInfo['city']);
    $customer->set_billing_state($contactInfo['state']);
    $customer->set_billing_postcode($contactInfo['postcode']);
    $customer->set_billing_country($contactInfo['country']);

    $customer->save();
}

function set_wc_shipping_address($customer, $userInfo, $contactInfo) {
    // set billing info
    $customer->set_shipping_first_name($userInfo['first_name']);
    $customer->set_shipping_last_name($userInfo['last_name']);
    $customer->set_shipping_phone($contactInfo['phone']);

    $customer->set_shipping_address($contactInfo['address_1']);
    $customer->set_shipping_city($contactInfo['city']);
    $customer->set_shipping_state($contactInfo['state']);
    $customer->set_shipping_postcode($contactInfo['postcode']);
    $customer->set_shipping_country($contactInfo['country']);

    $customer->save();
}

function sync_address_info($userId, $userInfo, $contactInfo, $isSignup) {
    // is WC support enabled?
    if (is_woocommerce_activated()) {
        // Get the WC_Customer instance object from user ID
        $customer = new WC_Customer( $userId );
        // sync address info from Vault
        if ($isSignup) {
            set_wc_billing_address($customer, $userInfo, $contactInfo);
            set_wc_shipping_address($customer, $userInfo, $contactInfo);
        } else if (affinidi_get_option('ecommerce_sync_address_info') == "billing") {
            set_wc_billing_address($customer, $userInfo, $contactInfo);
        } else if (affinidi_get_option('ecommerce_sync_address_info') == "billing_shipping") {
            set_wc_billing_address($customer, $userInfo, $contactInfo);
            set_wc_shipping_address($customer, $userInfo, $contactInfo);
        } else {
            // do nothing
        }
    }
}


function wp_users_can_signup() {
    return is_multisite() ? users_can_register_signup_filter() : get_site_option( 'users_can_register' );
}


function filter_woocommerce_customer_login_form( $html ) {
    // display affinidi login button
    // HTML attr and text already escaped
    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	return printf(get_wc_login_form_button(array()));
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

function filter_woocommerce_customer_regs_form( $html ) {
    // display affinidi login button
    // HTML attr and text already escaped
    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	return printf(get_wc_regs_form_button(array()));
    // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
}

function filter_position_al_button_wc_myaccount_form() {

    if (!is_woocommerce_activated()) {
        return;
    }

    $options = array_shift(get_options(array('affinidi_options')));

    $display_button_opt = $options['ecommerce_show_al_button'];

    if ($display_button_opt == "") {
        // do nothing
        return;
    }

    $button_position = $display_button_opt == 'top_form' ? 'woocommerce_login_form_start' : 'woocommerce_login_form_end';

    add_filter( $button_position, 'filter_woocommerce_customer_login_form' );
}

function filter_position_al_button_wc_reg_form() {

    if (!is_woocommerce_activated()) {
        return;
    }

    $options = array_shift(get_options(array('affinidi_options')));

    $display_button_opt = $options['ecommerce_show_al_button'];

    if ($display_button_opt == "") {
        // do nothing
        return;
    }

    $button_position = $display_button_opt == 'top_form' ? 'woocommerce_register_form_start' : 'woocommerce_register_form_end';

    add_filter( $button_position, 'filter_woocommerce_customer_regs_form' );
}

/**
 * Check if WooCommerce is activated
 */
if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	function is_woocommerce_activated() {
		if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
	}
}

// filter display button for wc
filter_position_al_button_wc_myaccount_form();
filter_position_al_button_wc_reg_form();
