<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Class affinidi_admin
 */
class Affinidi_Login_Admin_Settings
{
    const OPTIONS_NAME = 'affinidi_options';

    private $admin_options;

    private $option_name;

    private $admin_settings_fields = array(
        'active',
        'client_id',
        'backend',
        'redirect_to_dashboard',
        'login_only',
    );

    public function __construct(Affinidi_Login_Admin_Options $options) 
    {
        $this->admin_options = $options;
        $this->option_name = $this->admin_options->get_option_name();
    }

    public static function init(Affinidi_Login_Admin_Options $options)
    {
        $admin_settings = new self($options);
        // add_action adds a callback function to an action hook.
        // admin_init fires as an admin screen or script is being initialized.
        add_action('admin_init', [$admin_settings, 'admin_init']);
        // admin_menu fires before the administration menu loads in the admin.
        // This action is used to add extra submenus and menu options to the admin panel’s menu structure. It runs after the basic admin panel menu structure is in place.
        add_action('admin_menu', [$admin_settings, 'add_page']);
    }

    public function get_admin_settings() 
    {
        return $this->admin_settings_fields;
    }

    /**
     * [admin_init description]
     *
     * @return [type] [description]
     */
    public function admin_init()
    {
        // A callback function that sanitizes the option's value
        register_setting('affinidi_options', $this->option_name, [$this, 'validate']);
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
        <div class="affinidi-login-settings">
            <div class="admin-settings-header">
                <h1>Affinidi Login</h1>
                <a class="affinidi-login-doc" href="https://docs.affinidi.com/labs/3rd-party-plugins/passwordless-authentication-for-wordpress/" target="_blank">
                    <?php esc_html_e('Documentation', 'affinidi-login'); ?>
                </a>
            </div>
            <div class="admin-settings-inside">
                <p><?php esc_html_e('This plugin is meant to be used with', 'affinidi-login'); ?> <a href="https://www.affinidi.com/product/affinidi-login" target="_blank">Affinidi Login</a> and uses <a href="https://oauth.net/2/pkce/" target="_blank">PKCE</a> extension of OAuth 2.0 standard.</p>
                <p>
                    <strong>NOTE:</strong> <?php esc_html_e('If you want to add a
                    custom link anywhere in your theme simply link to', 'affinidi-login'); ?>
                    <strong><?php echo esc_url(site_url('?auth=affinidi')); ?></strong>
                    <?php esc_html_e('if the user is not logged in.', 'affinidi-login'); ?>
                </p>
                <div id="accordion">
                    <h3><?php esc_html_e('Step 1: Setup', 'affinidi-login'); ?></h3>
                    <div>
                        <strong>Create a Login Configuration</strong>
                        <ol>
                            <li>Login to <a
                                        href="https://portal.affinidi.com" target="_blank">Affinidi Portal</a> and go to the Affinidi Login service.
                            </li>
                            <li><?php esc_html_e('Create a Login Configuration and set the following fields:', 'affinidi-login'); ?>
                                <p>
                                <strong>Redirect URIs:</strong>
                                <code><?php echo esc_url(site_url('?auth=affinidi')); ?></code></p>
                                <p>
                                <strong>Auth method:</strong> <code>None</code></p>
                            </li>
                            <li><?php esc_html_e('Copy and paste the Client ID and Issuer URL in Step 2 below.', 'affinidi-login') ?></li>
                        </ol>
                    </div>
                    <h3 id="sso-configuration"><?php esc_html_e('Step 2: Configure', 'affinidi-login'); ?></h3>
                    <div>
                        <form method="post" action="options.php">
                            <?php settings_fields($this->option_name); ?>
                            <table class="form-table">
                            <tr valign="top">
                                    <th scope="row">Activate Affinidi Login</th>
                                    <td>
                                        <input type="checkbox"
                                            name="<?php echo  esc_html($this->option_name); ?>[active]"
                                            value="1" <?php echo $this->admin_options->active == 1 ? 'checked="checked"' : ''; ?> />
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e('Client ID', 'affinidi-login'); ?></th>
                                    <td>
                                        <input type="text" class="regular-text" name="<?php echo esc_html($this->option_name); ?>[client_id]" min="10"
                                            value="<?php echo esc_html($this->admin_options->client_id); ?>"/>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e('Issuer URL', 'affinidi-login'); ?></th>
                                    <td>
                                        <input type="text" class="regular-text" name="<?php echo esc_html($this->option_name); ?>[backend]" min="10"
                                            value="<?php echo esc_html($this->admin_options->backend); ?>"/>
                                        <p class="description"><?php esc_html_e('Example:', 'affinidi-login'); ?> https://[YOUR_PROJECT_ID]].apse1.login.affinidi.io</p>
                                    </td>
                                </tr>

                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e('Redirect to the dashboard after signing in', 'affinidi-login'); ?></th>
                                    <td>
                                        <input type="checkbox"
                                            name="<?php echo esc_html($this->option_name); ?>[redirect_to_dashboard]"
                                            value="1" <?php echo $this->admin_options->redirect_to_dashboard == 1 ? 'checked="checked"' : ''; ?> />
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e('Restrict flow to log in only (new users will not be allowed to signup)', 'affinidi-login'); ?></th>
                                    <td>
                                        <input type="checkbox"
                                            name="<?php echo esc_html($this->option_name) ?>[login_only]"
                                            value="1" <?php echo $this->admin_options->login_only == 1 ? 'checked="checked"' : ''; ?> />
                                    </td>
                                </tr>
                            </table>
                            <p class="submit">
                                <input type="submit" class="button-primary" value="<?php esc_html_e('Save Changes', 'affinidi-login'); ?>"/>
                            </p>
                    </div>

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
    public function validate(array $input)
    {
        $admin_settings = $this->get_admin_settings();
        $options = array();

		foreach ( $admin_settings as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$options[ $field ] = sanitize_text_field( trim( $input[ $field ] ) );
			} else {
				$options[ $field ] = '';
			}
		}

		return $options;
    }
}

$admin_options = new Affinidi_Login_Admin_Options();

Affinidi_Login_Admin_Settings::init($admin_options);
