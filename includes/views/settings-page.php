<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="hide-admin-menu-items-form" data-current-tab="<?php echo esc_attr($current_tab); ?>">
        <input type="hidden" name="action" value="save_hami_settings">
        <input type="hidden" name="tab" value="<?php echo esc_attr($current_tab); ?>">
        <?php wp_nonce_field('hide-admin-menu-items-options'); ?>
        <div class="hami-container">
            <div class="hami-roles-column">
                <h2><?php _e('User Roles', 'hide-admin-menu-items'); ?></h2>
                <ul class="hami-roles-list">
                    <?php foreach ($roles as $role_slug => $role_name) : ?>
                        <li>
                            <label>
                                <input type="radio" name="selected_role" value="<?php echo esc_attr($role_slug); ?>" <?php checked($role_slug === 'administrator'); ?>>
                                <span><?php echo esc_html($role_name); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="hami-content-wrapper">
                <div class="hami-settings-column">
                    <nav class="nav-tab-wrapper">
                        <a href="?page=hide-admin-menu-items&tab=menu-items" class="nav-tab <?php echo $current_tab === 'menu-items' ? 'nav-tab-active' : ''; ?>">
                            <?php _e('Menu Items', 'hide-admin-menu-items'); ?>
                        </a>
                        <a href="?page=hide-admin-menu-items&tab=admin-bar" class="nav-tab <?php echo $current_tab === 'admin-bar' ? 'nav-tab-active' : ''; ?>">
                            <?php _e('New content', 'hide-admin-menu-items'); ?>
                        </a>
                    </nav>
                    <div class="hami-settings-content">
                        <div class="hami-content-header">
                            <div class="hami-content-header-top">
                                <h2 class="hami-content-header-title">
                                    <?php 
                                        if ($current_tab === 'menu-items') {
                                            _e('Menu Items', 'hide-admin-menu-items');
                                        } else {
                                            _e('New content', 'hide-admin-menu-items');
                                        }
                                    ?>
                                </h2>
                                <span class="hami-current-role"><?php printf(__('Editing: %s', 'hide-admin-menu-items'), esc_html($roles[$current_role])); ?></span>
                            </div>

                            <p class="hami-content-header-description"><?php _e('Choose which items to hide', 'hide-admin-menu-items'); ?></p>
                        </div>

                        <?php if ($current_tab === 'menu-items'): ?>
                            <div class="hami-menu-items-list">
                                <?php foreach ($menu_items as $menu_item) : ?>
                                    <?php if(isset($menu_item['title']) && $menu_item['title']): ?>
                                        <div class="hami-menu-item">
                                            <label>
                                                <input type="checkbox" name="hami_settings[<?php echo esc_attr($menu_item['id']); ?>]" value="1" <?php checked(isset($settings[$menu_item['id']])); ?>>
                                                <?php echo esc_html($menu_item['title']); ?>
                                            </label>
                                            <?php if (!empty($menu_item['submenu'])) : ?>
                                                <div class="hami-submenu-items">
                                                    <?php foreach ($menu_item['submenu'] as $submenu_item) : ?>
                                                        <label>
                                                            <input type="checkbox" name="hami_settings[<?php echo esc_attr($submenu_item['id']); ?>]"
                                                                value="1" <?php checked(isset($settings[$submenu_item['id']])); ?>>
                                                            <?php echo esc_html($submenu_item['title']); ?>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="hami-admin-bar-items-list">
                                <?php if (isset($admin_bar_items['new-content'])): ?>
                                    <div class="hami-menu-item">
                                        <label>
                                            <input type="checkbox" name="hami_settings[admin_bar_<?php echo esc_attr($admin_bar_items['new-content']['id']); ?>]" 
                                                    value="1" <?php checked(isset($settings[$admin_bar_items['new-content']['id']])); ?>>
                                            <?php echo esc_html($admin_bar_items['new-content']['title']); ?>
                                        </label>
                                        <?php if (!empty($admin_bar_items['new-content']['children'])): ?>
                                            <div class="hami-submenu-items">
                                                <?php foreach ($admin_bar_items['new-content']['children'] as $child_item): ?>
                                                    <label>
                                                        <input type="checkbox" name="hami_settings[admin_bar_<?php echo esc_attr($child_item['id']); ?>]" 
                                                            value="1" <?php checked(isset($settings[$child_item['id']])); ?>>
                                                        <?php echo esc_html($child_item['title']); ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="hami-save-box">
                    <?php submit_button(__('Save Settings', 'hide-admin-menu-items'), 'primary', 'submit', false); ?>
                </div>
            </div>
        </div>
    </form>
</div> 