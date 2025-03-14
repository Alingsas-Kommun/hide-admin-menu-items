<?php
/**
 * Admin Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class HAMI_Admin_Settings {
    private $plugin_path;
    private $plugin_url;
    private $menu_settings;
    private $admin_bar_settings;

    public function __construct($plugin_path, $plugin_url, $menu_settings, $admin_bar_settings) {
        $this->plugin_path = $plugin_path;
        $this->plugin_url = $plugin_url;
        $this->menu_settings = $menu_settings;
        $this->admin_bar_settings = $admin_bar_settings;

        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_save_hami_settings', array($this, 'handle_form_submission'));
        add_action('wp_ajax_load_role_settings', array($this, 'ajax_load_role_settings'));
        add_action('admin_notices', array($this, 'display_settings_updated_notice'));
    }

    public function add_settings_page() {
        add_options_page(
            __('Hide Admin Menu Items', 'hide-admin-menu-items'),
            __('Hide Admin Menu Items', 'hide-admin-menu-items'),
            'manage_options',
            'hide-admin-menu-items',
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_admin_assets($hook) {
        if ('settings_page_hide-admin-menu-items' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'hide-admin-menu-items',
            $this->plugin_url . 'assets/css/admin.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'hide-admin-menu-items',
            $this->plugin_url . 'assets/js/admin.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('hide-admin-menu-items', 'hideAdminMenuItems', array(
            'nonce' => wp_create_nonce('hide-admin-menu-items-nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    public function register_settings() {
        register_setting('hide-admin-menu-items', 'hami_menu_settings');
        register_setting('hide-admin-menu-items', 'hami_adminbar_settings');
    }

    public function ajax_load_role_settings() {
        check_ajax_referer('hide-admin-menu-items-nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $role = sanitize_text_field($_POST['role']);
        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'menu-items';
        
        if ($tab === 'menu-items') {
            $settings = get_option('hami_menu_settings_' . $role, array());
        } else {
            $settings = get_option('hami_adminbar_settings_' . $role, array());
        }

        wp_send_json_success($settings);
    }

    public function handle_form_submission() {
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        check_admin_referer('hide-admin-menu-items-options');

        $role = sanitize_text_field($_POST['selected_role']);
        $tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'menu-items';
        $settings = array();

        if (isset($_POST['hami_settings']) && is_array($_POST['hami_settings'])) {
            foreach ($_POST['hami_settings'] as $key => $value) {
                $clean_key = sanitize_text_field($key);
                if ($tab === 'admin-bar') {
                    // Remove admin_bar_ prefix for storage
                    $clean_key = str_replace('admin_bar_', '', $clean_key);
                }
                $settings[$clean_key] = true;
            }
        }

        if ($tab === 'menu-items') {
            update_option('hami_menu_settings_' . $role, $settings);
        } else {
            update_option('hami_adminbar_settings_' . $role, $settings);
        }

        // Redirect back to the settings page with a success message
        wp_redirect(add_query_arg(
            array(
                'page' => 'hide-admin-menu-items',
                'tab' => $tab,
                'settings-updated' => 'true'
            ),
            admin_url('options-general.php')
        ));
        exit;
    }

    private function get_current_role() {
        $current_user = wp_get_current_user();
        if (isset($current_user->roles[0])) {
            return $current_user->roles[0];
        }
        return 'administrator';
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $roles = wp_roles()->get_names();
        $menu_items = $this->menu_settings->get_menu_items();
        $admin_bar_items = $this->admin_bar_settings->get_admin_bar_items();
        $current_role = isset($_POST['selected_role']) ? sanitize_text_field($_POST['selected_role']) : $this->get_current_role();
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'menu-items';
        
        // Get appropriate settings based on tab
        if ($current_tab === 'menu-items') {
            $settings = get_option('hami_menu_settings_' . $current_role, array());
        } else {
            $settings = get_option('hami_adminbar_settings_' . $current_role, array());
        }

        include $this->plugin_path . 'includes/views/settings-page.php';
    }

    public function display_settings_updated_notice() {
        $screen = get_current_screen();
        if ($screen->id === 'settings_page_hide-admin-menu-items' && isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully!', 'hide-admin-menu-items') . '</p></div>';
        }
    }
} 