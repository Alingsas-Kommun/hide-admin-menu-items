<?php
/**
 * Admin Menu Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class HAMI_Admin_Menu_Settings {
    private $original_menu;
    private $original_submenu;

    public function __construct() {
        add_action('admin_menu', array($this, 'store_original_menu'), 99);
        add_action('admin_head', array($this, 'hide_menu_items'));
    }

    public function store_original_menu() {
        global $menu, $submenu;
        // Create a deep copy of the menu arrays
        $this->original_menu = is_array($menu) ? array_map(function($item) {
            return is_array($item) ? array_values($item) : $item;
        }, $menu) : array();
        
        $this->original_submenu = array();
        if (is_array($submenu)) {
            foreach ($submenu as $key => $items) {
                $this->original_submenu[$key] = array_map(function($item) {
                    return is_array($item) ? array_values($item) : $item;
                }, $items);
            }
        }
    }

    public function get_menu_items() {
        $items = array();

        if (empty($this->original_menu)) {
            return $items;
        }

        foreach ($this->original_menu as $menu_item) {
            // Skip separators and empty items
            if (!is_array($menu_item) || empty($menu_item[2])) {
                continue;
            }

            $menu_id = sanitize_title($menu_item[2]);
            $items[] = array(
                'id' => $menu_id,
                'title' => strip_tags($menu_item[0]),
                'submenu' => isset($this->original_submenu[$menu_item[2]]) ? 
                    $this->get_submenu_items($this->original_submenu[$menu_item[2]], $menu_id) : array()
            );
        }

        return $items;
    }

    private function get_submenu_items($submenu_items, $parent_id) {
        $items = array();
        
        if (!is_array($submenu_items)) {
            return $items;
        }

        foreach ($submenu_items as $submenu_item) {
            if (!is_array($submenu_item) || empty($submenu_item[2])) {
                continue;
            }

            $items[] = array(
                'id' => $parent_id . '-' . sanitize_title($submenu_item[2]),
                'title' => strip_tags($submenu_item[0])
            );
        }
        return $items;
    }

    public function hide_menu_items() {
        $current_user = wp_get_current_user();
        
        if (empty($current_user->roles)) {
            return;
        }

        $role = $current_user->roles[0];
        $settings = get_option('hami_menu_settings_' . $role, array());

        if (empty($settings)) {
            return;
        }

        foreach ($settings as $menu_id => $hidden) {
            if ($hidden) {
                $this->remove_menu_item($menu_id);
            }
        }
    }

    private function remove_menu_item($menu_id) {
        global $menu, $submenu;

        // Handle main menu items
        if (is_array($menu)) {
            foreach ($menu as $menu_key => $menu_item) {
                if (sanitize_title($menu_item[2]) === $menu_id) {
                    unset($menu[$menu_key]);
                    break;
                }
            }
        }

        // Handle submenu items
        if (is_array($submenu)) {
            foreach ($submenu as $parent_menu => &$parent_submenu) {
                if (is_array($parent_submenu)) {
                    foreach ($parent_submenu as $submenu_key => $submenu_item) {
                        $submenu_id = sanitize_title($parent_menu) . '-' . sanitize_title($submenu_item[2]);
                        if ($submenu_id === $menu_id) {
                            unset($parent_submenu[$submenu_key]);
                            break;
                        }
                    }
                }
            }
        }
    }
} 