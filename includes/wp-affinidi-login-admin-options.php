<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Class affinidi_admin
 */
class WP_Affinidi_Login_Admin
{
    const OPTIONS_NAME = 'affinidi_options';

    public static function init()
    {
        // add_action adds a callback function to an action hook.
        // admin_init fires as an admin screen or script is being initialized.
        add_action('admin_init', [new self, 'admin_init']);
        // admin_menu fires before the administration menu loads in the admin.
        // This action is used to add extra submenus and menu options to the admin panel’s menu structure. It runs after the basic admin panel menu structure is in place.
        add_action('admin_menu', [new self, 'add_page']);
    }

    /**
     * [admin_init description]
     *
     * @return [type] [description]
     */
    public function admin_init()
    {
        // A callback function that sanitizes the option's value
        register_setting('affinidi_options', self::OPTIONS_NAME, [$this, 'validate']);
    }

    /**
     * Add affinidi submenu page to the settings main menu
     */
    public function add_page()
    {
        add_options_page('Affinidi Login', 'Affinidi Login', 'manage_options', 'affinidi_settings', [$this, 'options_do_page']);
    }

    /**
     * [options_do_page description]
     *
     * @return [type] [description]
     */
    public function options_do_page()
    {
        ?>
        <div class="affinidi-login-settings container-fluid">
            <div class="admin-settings-header">
                <h1>Affinidi Login</h1>
                <a class="affinidi-login-doc" href="https://docs.affinidi.com/labs/3rd-party-plugins/passwordless-authentication-for-wordpress/" target="_blank">
                    Documentation
                </a>
            </div>
            <div class="admin-settings-inside">
                <p>This plugin is meant to be used with <a href="https://www.affinidi.com/product/affinidi-login" target="_blank">Affinidi Login</a> and uses <a href="https://oauth.net/2/pkce/" target="_blank">PKCE</a> extension of OAuth 2.0 standard.</p>
                <p>
                    <strong>NOTE:</strong> If you want to add a
                    custom link anywhere in your theme, simply link to
                    <code><?php echo esc_url(site_url('?auth=affinidi')); ?></code>
                    if the user is not logged in.
                </p>
                <div id="accordion">
                    <h3>Step 1: Setup</h3>
                    <div>
                        <strong>Create a Login Configuration</strong>
                        <ol>
                            <li>Login to <a
                                        href="https://portal.affinidi.com" target="_blank">Affinidi Portal</a> and go to the Affinidi Login service.
                            </li>
                            <li>Create a Login Configuration and set the following fields:
                                <p>
                                <strong>Redirect URIs:</strong>
                                <code><?php echo esc_url(site_url('?auth=affinidi')); ?></code></p>
                                <p>
                                <strong>Auth method:</strong> <code>None</code></p>
                            </li>
                            <li>Copy the <strong>Client ID</strong> and <strong>Issuer URL</strong> and paste it in Step 2 below.</li>
                            <li>
                                <p>Modify the <strong>Presentation Definition</strong> and <strong>ID Token Mapping</strong> using <a href="https://docs.affinidi.com/labs/3rd-party-plugins/passwordless-authentication-for-wordpress/#presentation-definition-and-id-token-mapping" target="_blank">this template.</a></p>
                                <p><em>If you have enabled support for E-Commerce, use the template for E-Commerce.</em></p>
                            </li>
                        </ol>
                    </div>
                    <h3 id="sso-configuration">Step 2: Configure</h3>
                    <div class="row">
                        <form method="post" action="options.php">
                            <?php settings_fields('affinidi_options'); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">Client ID</th>
                                    <td>
                                        <input type="text" class="regular-text" name="<?php echo esc_html(self::OPTIONS_NAME); ?>[client_id]" min="10"
                                            value="<?php echo esc_html(affinidi_get_option('client_id')); ?>"/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row">Issuer URL</th>
                                    <td>
                                        <input type="text" class="regular-text" name="<?php echo esc_html(self::OPTIONS_NAME); ?>[backend]" min="10"
                                            value="<?php echo esc_html(affinidi_get_option('backend')); ?>"/>
                                        <p class="description">Example: https://[YOUR_PROJECT_ID].apse1.login.affinidi.io</p>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row">Redirect user to Origin Page</th>
                                    <td>
                                        <input type="checkbox"
                                            name="<?php echo esc_html(self::OPTIONS_NAME); ?>[redirect_user_origin]"
                                            value="1" <?php echo affinidi_get_option('redirect_user_origin') == 1 ? 'checked="checked"' : ''; ?> />
                                        <p class="description">By default, users will be redirected to Homepage. If the user used the <em>wp-login.php</em> form, they will be redirected to Dashboard.</p>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row">Restrict flow to log in only (new users will not be allowed to signup)</th>
                                    <td>
                                        <?php
                                        if (wp_users_can_signup()) {
                                        ?>
                                        <p class="description">Signup is currently <strong>enabled</strong> in the WordPress General Settings.</p>
                                        <p class="description"> Update the WordPress settings if you wish to restrict users from signing up using their Vault.</p>
                                        <?php
                                        } else {
                                        ?>
                                        <p class="description">Sign up is currently <strong>disabled</strong> in the WordPress General Settings.</p>
                                        <p  class="description">Update the WordPress settings if you wish to allow users to signup using their Vault.</p>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                    </div>
                    <?php
                    if (is_woocommerce_activated()) {
                    ?>
                    <h3 id="sso-configuration">WooCommerce Settings</h3>
                    <div class="row">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">Sync address info on login</th>
                                <td>
                                    <select name="<?php echo esc_html(self::OPTIONS_NAME); ?>[ecommerce_sync_address_info]">
                                        <option value="billing" <?php selected( affinidi_get_option('ecommerce_sync_address_info'), "billing" ); ?>>Billing Address Info</option>
                                        <option value="billing_shipping" <?php selected( affinidi_get_option('ecommerce_sync_address_info'), "billing_shipping" ); ?>>Billing and Shipping Address Info</option>
                                    </select>
                                    <p class="description">Affinidi Login will always populate both Billing and Shipping Address info on Signup.</p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Display Affinidi Login button</th>
                                <td>
                                    <select name="<?php echo esc_html(self::OPTIONS_NAME); ?>[ecommerce_show_al_button]">
                                        <option value="top_form" <?php selected( affinidi_get_option('ecommerce_show_al_button'), "top_form" ); ?>>At the top of the Login Form</option>
                                        <option value="bottom_form" <?php selected( affinidi_get_option('ecommerce_show_al_button'), "bottom_form" ); ?>>At the bottom of the Login Form</option>
                                        <option value="" <?php selected( affinidi_get_option('ecommerce_show_al_button'), "" ); ?>>Don't display on Login Form</option>
                                    </select>
                                    <p class="description">If you choose <em>"Don't display on Login Form"</em>, use the shortcode <strong>[affinidi_login]</strong> to display the button on your desired page.</p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Affinidi Login button header (Login Form)</th>
                                <td>
                                    <input type="text" class="regular-text" name="<?php echo esc_html(self::OPTIONS_NAME); ?>[affinidi_login_loginform_header]" min="10"
                                        value="<?php echo esc_html(affinidi_get_option('affinidi_login_loginform_header') ? affinidi_get_option('affinidi_login_loginform_header') : "Log in passwordless with"); ?>"/>
                                    <p class="description"></p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Affinidi Login button header (Registration Form)</th>
                                <td>
                                    <input type="text" class="regular-text" name="<?php echo esc_html(self::OPTIONS_NAME); ?>[affinidi_login_regform_header]" min="10"
                                        value="<?php echo esc_html(affinidi_get_option('affinidi_login_regform_header') ? affinidi_get_option('affinidi_login_regform_header') : "Sign up seamlessly with"); ?>"/>
                                    <p class="description"></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                    }
                    ?>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes') ?>"/>
                    </p>
                    </form>
                </div>
            </div>
        </div>
        <div style="clear:both;"></div>
        <?php
    }

    /**
     * Settings Validation
     *
     * @param array $input option array
     *
     * @return array
     */
    public function validate(array $input): array
    {
        $input['redirect_user_origin'] = isset($input['redirect_user_origin']) ? $input['redirect_user_origin'] : 0;
        $input['enable_ecommerce_support'] = isset($input['enable_ecommerce_support']) ? $input['enable_ecommerce_support'] : '';
        $input['affinidi_login_loginform_header'] = isset($input['affinidi_login_loginform_header']) ? $input['affinidi_login_loginform_header'] : 'Log in passwordless with';
        $input['affinidi_login_regform_header'] = isset($input['affinidi_login_regform_header']) ? $input['affinidi_login_regform_header'] : 'Sign up seamlessly with';

        return $input;
    }
}

WP_Affinidi_Login_Admin::init();
