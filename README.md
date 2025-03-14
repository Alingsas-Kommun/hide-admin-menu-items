# Hide Admin Menu Items

A WordPress plugin that provides granular control over which admin menu items and admin bar items are visible to different user roles.

## Description

Hide Admin Menu Items is a powerful WordPress plugin designed to help administrators customize the WordPress admin interface by controlling the visibility of menu items based on user roles. This enhances security and provides a cleaner, more focused admin experience for different types of users.

## Features

- Selectively hide/show admin menu items for specific user roles
- Control visibility of admin bar items
- User-friendly interface for managing menu visibility settings
- Role-based access control
- Clean and efficient code implementation
- Compatible with the latest WordPress version

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click on "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now" and then "Activate"

## Usage

1. Navigate to Settings > Hide Admin Menu Items in your WordPress admin panel
2. Select the user role you want to configure from the dropdown menu
3. Check/uncheck the menu items you want to hide/show for that role
4. Save your changes
5. Repeat for other user roles as needed

## Translations

The plugin comes with support for multiple languages. Here's how to work with translations:

### Updating Translation Template

To update the POT (template) file when new strings are added to the plugin:

```bash
wp i18n make-pot . languages/hide-admin-menu-items.pot
```

### Adding a New Translation

1. Copy the template file to create a new PO file for your language:
   ```bash
   cp languages/hide-admin-menu-items.pot languages/hide-admin-menu-items-{language_code}.po
   ```
   Replace {language_code} with your language code (e.g., sv_SE for Swedish)

2. Edit the PO file using a translation editor like Poedit
3. Save the file - this will automatically generate the required .mo file

### Available Translations

- Swedish (sv_SE)

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Support

For support questions, feature requests, or bug reports, please create an issue in the plugin's repository.

## License

This plugin is licensed under the GPL v2 or later.

## Author

Created by Cursor AI

## Changelog

### 1.0.0
- Initial release
- Basic functionality for hiding admin menu items
- Role-based access control
- Admin interface for managing settings 