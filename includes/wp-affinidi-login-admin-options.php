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
        <div class="wrap">
            <h2>Affinidi Login Configuration</h2>
            <p>This plugin is meant to be used with <a href="https://www.affinidi.com/product/affinidi-login" target="_blank">Affinidi Login</a> and uses <a href="https://oauth.net/2/pkce/" target="_blank">PKCE</a> extension of OAuth 2.0 standard.</p>
            <p>When activated, this plugin will redirect all login requests to your affinidi page.</p>
            <p>
                <strong>NOTE:</strong> If you want to add a
                custom link anywhere in your theme simply link to
                <strong><?= site_url('?auth=affinidi'); ?></strong>
                if the user is not logged in.
            </p>
            <div id="accordion">
                <h4>Step 1: Setup</h4>
                <div>
                    <strong>Setting up Affinidi Login</strong>
                    <ol>
                        <li>Login to <a
                                    href="https://portal.affinidi.com" target="_blank">Affinidi Portal</a> and go to the Affinidi Login service.
                        </li>
                        <li>Create a Login Configuration and set the following fields:
                            <p>
                            <strong>Redirect URIs:</strong>
                            <code><?= site_url('?auth=affinidi'); ?></code></p>
                            <p>
                            <strong>Auth method:</strong> <code>None</code></p>
                        </li>
                        <li>Copy and paste the Client ID and Issuer URL in Step 2 below.</li>
                    </ol>
                </div>
                <h4 id="sso-configuration">Step 2: Configuration</h4>
                <div>
                    <form method="post" action="options.php">
                        <?php settings_fields('affinidi_options'); ?>
                        <table class="form-table">
                        <tr valign="top">
                                <th scope="row">Activate Affinidi Login</th>
                                <td>
                                    <input type="checkbox"
                                        name="<?= self::OPTIONS_NAME ?>[active]"
                                        value="1" <?= affinidi_get_option('active') == 1 ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Client ID</th>
                                <td>
                                    <input type="text" class="regular-text" name="<?= self::OPTIONS_NAME ?>[client_id]" min="10"
                                           value="<?= affinidi_get_option('client_id') ?>"/>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Issuer URL</th>
                                <td>
                                    <input type="text" class="regular-text" name="<?= self::OPTIONS_NAME ?>[backend]" min="10"
                                           value="<?= affinidi_get_option('backend'); ?>"/>
                                    <p class="description">Example: https://[YOUR_PROJECT_ID]].apse1.login.affinidi.io</p>
                                </td>
                            </tr>

                            <tr valign="top">
                                <th scope="row">Redirect to the dashboard after signing in</th>
                                <td>
                                    <input type="checkbox"
                                           name="<?= self::OPTIONS_NAME ?>[redirect_to_dashboard]"
                                           value="1" <?= affinidi_get_option('redirect_to_dashboard') == 1 ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Restrict flow to log in only (new users will not be allowed to signup)</th>
                                <td>
                                    <input type="checkbox"
                                           name="<?= self::OPTIONS_NAME ?>[login_only]"
                                           value="1" <?= affinidi_get_option('login_only') == 1 ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Require all visitors to log-in</th>
                                <td>
                                    <input type="checkbox"
                                           name="<?= self::OPTIONS_NAME ?>[auto_sso]"
                                           value="1" <?= affinidi_get_option('auto_sso') == 1 ? 'checked="checked"' : ''; ?> />
                                </td>
                            </tr>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                        </p>
                </div>

                </form>
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
        $input['redirect_to_dashboard'] = isset($input['redirect_to_dashboard']) ? $input['redirect_to_dashboard'] : 0;
        $input['login_only']            = isset($input['login_only']) ? $input['login_only'] : 0;
        $input['organization']          = isset($input['organization']) ? $input['organization'] : 'built-in';

        return $input;
    }
}

WP_Affinidi_Login_Admin::init();
