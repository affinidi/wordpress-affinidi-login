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
        'active'               => 0,
        'client_id'            => '',
        'backend'              => '',
        'redirect_to_dashboard'=> 0,
        'login_only'           => 0,
    ];

    public function __construct()
    {
        add_action('init', [__CLASS__, 'includes']);
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
        require_once(AFFINIDI_PLUGIN_DIR . '/includes/wp-affinidi-login-admin-settings.php');
        require_once(AFFINIDI_PLUGIN_DIR . '/includes/wp-affinidi-login-rewrites.php');
    }

    /**
     * Plugin Setup
     */
    public function setup()
    {
        $admin_options = get_option('affinidi_options');

        if (!isset($admin_options['backend'])) {
            update_option('affinidi_options', $this->default_settings);
        }

        $this->install();
    }

    public function logout()
    {
        wp_redirect(home_url());
        exit();
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
    }

    /**
     * Register and enqueue a custom stylesheet in the WordPress admin.
     */
    public function affinidi_login_enqueue_admin_scripts() {
        wp_register_style( 'affinidi_login_admin_css', plugins_url('/assets/css/admin.css', __FILE__), false, '1.0.0' );
        wp_enqueue_style( 'affinidi_login_admin_css' );

        wp_register_script( 'affinidi_login_admin_js', plugins_url('/assets/js/admin.js', __FILE__), false, '1.0.0', true );
        wp_enqueue_script( 'affinidi_login_admin_js' );
    }

    public function affinidi_login_enqueue_fe_scripts()
    {
        // Register a CSS stylesheet.
        wp_register_style('affinidi_login_fe_css', plugins_url('/assets/css/affinidi-login.css', __FILE__), false, '1.0.0');
        wp_enqueue_style( 'affinidi_login_fe_css' );
        // Register a new script.
        wp_register_script('affinidi_login_fe_js', plugins_url('/assets/js/affinidi-login.js', __FILE__), array(), '1.0.0', true);
        wp_enqueue_script( 'affinidi_login_fe_js' );
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
