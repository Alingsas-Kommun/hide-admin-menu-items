jQuery(document).ready(function($) {
    const form = $('#hide-admin-menu-items-form');
    const roleInputs = form.find('input[name="selected_role"]');
    const checkboxes = form.find('input[type="checkbox"]');
    const currentRoleSpan = $('.hami-current-role');
    let currentRole = roleInputs.filter(':checked').val();
    let currentRoleName = roleInputs.filter(':checked').next('span').text();
    let currentTab = form.data('current-tab');

    // Set initial active state
    roleInputs.filter(':checked').closest('li').addClass('active');

    // Handle checkbox parent-child relationships
    function handleParentChildCheckboxes(parentCheckbox) {
        const $parent = $(parentCheckbox);
        const $submenuItems = $parent.closest('label').siblings('.hami-submenu-items').find('input[type="checkbox"]');
        
        // When parent is checked/unchecked
        $parent.on('change', function() {
            $submenuItems.prop('checked', $(this).prop('checked'));
        });

        // When children are checked/unchecked
        $submenuItems.on('change', function() {
            const allChecked = $submenuItems.length === $submenuItems.filter(':checked').length;
            const someChecked = $submenuItems.filter(':checked').length > 0;
            
            $parent.prop({
                'indeterminate': someChecked && !allChecked,
                'checked': allChecked
            });
        });

        // Initial state
        const checkedCount = $submenuItems.filter(':checked').length;
        if (checkedCount > 0) {
            if (checkedCount === $submenuItems.length) {
                $parent.prop('checked', true);
                $parent.prop('indeterminate', false);
            } else {
                $parent.prop('indeterminate', true);
            }
        }
    }

    // Initialize parent-child behavior for all menu items
    $('.hami-menu-item > label > input[type="checkbox"]').each(function() {
        handleParentChildCheckboxes(this);
    });

    // Update current role indicator
    function updateCurrentRoleIndicator(roleName) {
        currentRoleSpan.fadeOut(200, function() {
            $(this).text(wp.i18n.__('Editing:', 'hide-admin-menu-items') + ' ' + roleName).fadeIn(200);
        });
    }

    // Load saved settings for the selected role
    function loadRoleSettings(role) {
        $.ajax({
            url: hideAdminMenuItems.ajaxurl,
            type: 'POST',
            data: {
                action: 'load_role_settings',
                role: role,
                tab: currentTab,
                nonce: hideAdminMenuItems.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reset all checkboxes
                    checkboxes.prop({
                        'checked': false,
                        'indeterminate': false
                    });

                    // Update checkboxes based on saved settings
                    $.each(response.data, function(key, value) {
                        const checkbox = currentTab === 'admin-bar' 
                            ? $(`input[name="hami_settings[admin_bar_${key}]"]`)
                            : $(`input[name="hami_settings[${key}]"]`);
                        
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });

                    // Reinitialize parent-child behavior
                    $('.hami-menu-item > label > input[type="checkbox"]').each(function() {
                        handleParentChildCheckboxes(this);
                    });

                    // Update current role display
                    updateCurrentRoleIndicator(currentRoleName);
                }
            }
        });
    }

    // Handle role selection change
    roleInputs.on('change', function() {
        const $this = $(this);
        currentRole = $this.val();
        currentRoleName = $this.next('span').text().trim();
        
        // Update the current role indicator with animation
        updateCurrentRoleIndicator(currentRoleName);
        
        // Load settings for the new role
        loadRoleSettings(currentRole);
        
        // Add active class to parent li
        $('.hami-roles-list li').removeClass('active');
        $this.closest('li').addClass('active');
    });

    // Handle tab changes
    $('.nav-tab').on('click', function() {
        currentTab = $(this).attr('href').split('tab=')[1];
        loadRoleSettings(currentRole);
    });

    // Load initial settings and set initial role indicator
    loadRoleSettings(currentRole);
    updateCurrentRoleIndicator(currentRoleName);
}); 