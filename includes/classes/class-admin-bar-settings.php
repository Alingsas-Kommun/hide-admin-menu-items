<?php
/**
 * Admin Bar Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class HAMI_Admin_Bar_Settings {
    private $original_admin_bar;

    public function __construct() {
        add_action('admin_bar_menu', array($this, 'store_original_admin_bar'), 999);
        add_action('wp_before_admin_bar_render', array($this, 'hide_admin_bar_items'));
    }

    public function store_original_admin_bar() {
        global $wp_admin_bar;
        
        if (!is_object($wp_admin_bar)) {
            return;
        }

        $nodes = $wp_admin_bar->get_nodes();
        $this->original_admin_bar = array();

        if ($nodes) {
            // First, find the "new-content" parent node
            foreach ($nodes as $node) {
                if ($node->id === 'new-content') {
                    $this->original_admin_bar['new-content'] = array(
                        'id' => 'new-content',
                        'title' => strip_tags($node->title),
                        'parent' => null,
                        'children' => array()
                    );
                    break;
                }
            }

            // Then collect all child nodes
            foreach ($nodes as $node) {
                if ($node->parent === 'new-content') {
                    $this->original_admin_bar['new-content']['children'][] = array(
                        'id' => $node->id,
                        'title' => strip_tags($node->title),
                        'parent' => 'new-content'
                    );
                }
            }
        }
    }

    public function get_admin_bar_items() {
        return $this->original_admin_bar;
    }

    public function hide_admin_bar_items() {
        global $wp_admin_bar;
        
        if (!is_object($wp_admin_bar)) {
            return;
        }

        $current_user = wp_get_current_user();
        if (!$current_user || !$current_user->roles) {
            return;
        }

        $role = reset($current_user->roles);
        $settings = get_option('hami_adminbar_settings_' . $role, array());

        if (empty($settings)) {
            return;
        }

        // Check if parent 'new-content' should be hidden
        if (isset($settings['new-content'])) {
            $wp_admin_bar->remove_node('new-content');
            return; // If parent is hidden, no need to check children
        }

        // Check children
        if (isset($this->original_admin_bar['new-content']['children'])) {
            foreach ($this->original_admin_bar['new-content']['children'] as $child_item) {
                if (isset($settings[$child_item['id']])) {
                    $wp_admin_bar->remove_node($child_item['id']);
                }
            }
        }
    }
} 