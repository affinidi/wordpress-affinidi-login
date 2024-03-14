<?php

// ABSPATH prevent public user to directly access your .php files through URL.
defined('ABSPATH') or die('No script kiddies please!');

/**
 * The main class of plugin
 */
class Affinidi
{
    public $version = '1.0.0';

    public static $_instance = null;

    protected $default_settings = [
        'client_id'            => '',
        'backend'              => '',
        'redirect_user_origin' => 0,
        'enable_ecommerce_support' => '',
        'ecommerce_sync_address_info' => 'billing',
        'ecommerce_show_al_button' => 'top_form',
        'affinidi_login_loginform_header' => 'Log in passwordless with',
        'affinidi_login_regform_header' => 'Sign up seamlessly with',
    ];

    public function __construct()
    {
        add_action('init', [__CLASS__, 'includes']);
        //add_action('init', [__CLASS__, 'custom_login']); // when activated, Affinidi Login will become the only login option
    }

    /**
     * populate the instance if the plugin for extendability
     *
     * @return Affinidi
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * plugin includes called during load of plugin
     *
     * @return void
     */
    public static function includes()
    {
        require_once(AFFINIDI_PLUGIN_DIR . '/includes/functions.php');
        require_once(AFFINIDI_PLUGIN_DIR . '/includes/wp-affinidi-login-admin-options.php');
        require_once(AFFINIDI_PLUGIN_DIR . '/includes/wp-affinidi-login-rewrites.php');
    }

    /**
     * Plugin Setup
     */
    public function setup()
    {
        $options = get_option('affinidi_options');
        if (!isset($options['backend'])) {
            update_option('affinidi_options', $this->default_settings);
        }
        $this->install();
    }

    /**
     * When wp-login.php was visited, redirect to the login page of affinidi
     *
     * @return void
     */
    public static function custom_login()
    {
        global $pagenow;
        $activated = absint(affinidi_get_option('active'));
        if ('wp-login.php' == $pagenow && $_GET['action'] != 'logout' && $activated) {
            $url = get_affinidi_login_url();
            wp_redirect($url);
            exit();
        }
    }

    public function logout()
    {
        wp_redirect(home_url());
        die();
    }

    /**
     * Loads the plugin styles and scripts into scope
     *
     * @return void
     */
    public function wp_enqueue()
    {
        // Registers the script if $src provided (does NOT overwrite), and enqueues it.
        wp_enqueue_script('jquery-ui-accordion');
        // Registers the style if source provided (does NOT overwrite) and enqueues.
        wp_enqueue_style('affinidi_admin');
        wp_enqueue_script('affinidi_admin');
    }

    /**
     * Plugin Initializer
     */
    public function plugin_init()
    {
    }

    /**
     * Plugin Install
     */
    public function install()
    {
    }

    /**
     * Plugin Upgrade
     */
    public function upgrade()
    {
    }
}
