<?php
/**
 * Plugin Name: Hide Admin Menu Items
 * Plugin URI: 
 * Description: Hide WordPress admin menu items based on user roles
 * Version: 1.0.0
 * Author: Cursor AI
 * Author URI: 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hide-admin-menu-items
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Make sure we have access to WordPress functions
if (!function_exists('add_action')) {
    exit;
}

// Autoload classes
spl_autoload_register(function ($class) {
    // Only handle our own classes
    if (strpos($class, 'HAMI_') !== 0) {
        return;
    }

    // Convert class name to file path
    $class_file = str_replace('_', '-', strtolower($class));
    $class_file = str_replace('hami-', '', $class_file);
    $class_file = 'class-' . $class_file . '.php';
    
    $class_path = plugin_dir_path(__FILE__) . 'includes/classes/' . $class_file;

    if (file_exists($class_path)) {
        require_once $class_path;
    }
});

class Hide_Admin_Menu_Items {
    private static $instance = null;
    private $plugin_path;
    private $plugin_url;
    private $menu_settings;
    private $admin_bar_settings;
    private $admin_settings;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);

        // Load text domain for translations
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        // Initialize components
        $this->menu_settings = new HAMI_Admin_Menu_Settings();
        $this->admin_bar_settings = new HAMI_Admin_Bar_Settings();
        $this->admin_settings = new HAMI_Admin_Settings(
            $this->plugin_path,
            $this->plugin_url,
            $this->menu_settings,
            $this->admin_bar_settings
        );
    }

    /**
     * Load plugin translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'hide-admin-menu-items',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
}

// Initialize the plugin
Hide_Admin_Menu_Items::get_instance(); 