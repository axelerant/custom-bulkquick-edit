=== Custom Bulk/Quick Edit ===

Contributors: comprock
Donate link: http://aihr.us/about-aihrus/donate/
Tags: custom, bulk edit, quick edit, custom post types, woocommerce
Requires at least: 3.6
Tested up to: 4.1.0
Stable tag: 1.5.3RC1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Bulk/Quick Edit allows you to easily add custom fields to the edit screen bulk and quick edit panels.


== Description ==

Through Custom Bulk/Quick Edit [by Aihrus](http://aihr.us/about-aihrus/), you have the option to edit post meta via text, checkbox, radio, select, and textarea inputs within Bulk Edit and Quick Edit screens. Further, you can enable editing of category and tag taxonomies that don't show up already. Next, taxnomony, checkbox, radio, and select fields have an option to be reset, as in remove current options during Bulk Editing. This is very helpful when you want to mass reset or remove information.

**Video Introduction**

[youtube https://www.youtube.com/watch?v=wd6munNz0gI]

Custom Bulk/Quick Edit automatically detects custom fields that use the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter to display additional columns on the edit screen. **Therefore, unless it's already configured, your theme's `functions.php` file will have to modified to add custom field columns.**

Read the **Usage** section of [Installation](http://wordpress.org/plugins/custom-bulkquick-edit/installation/) and the **[FAQ](http://wordpress.org/plugins/custom-bulkquick-edit/faq/)** to get started.

= Custom Bulk/Quick Edit Premium =

Custom Bulk/Quick Edit Premium extends [Custom Bulk/Quick Edit](http://wordpress.org/extend/plugins/custom-bulkquick-edit/) to work with [custom post types](https://aihr.us/custom-bulkquick-edit-premium/) and adds additional inputs options. Read on for details…

* **Free, comprehensive support**
* Adds float, integer, and user inputs
* Bulk edit post dates
* Color input with color picker
* Date and time input with datetime picker
* Date input with date picker
* Disable donate references
* Flexible API
* Multiple select selector
* Works with Custom Post Types
* Works with [WooCommerce 2.1.0+ custom attributes and product types](http://www.woothemes.com/woocommerce/)

**[Buy Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/)** plugin for WordPress.

= Custom Bulk/Quick Edit Premium Doesn't Work For You? =

No problem, it has a 30-day, money back guarantee. Also, you can keep the software, sans support and updates.

= Custom Bulk/Quick Edit Add Ons =

* [Edit Flow](http://wordpress.org/plugins/cbqe-edit-flow/) - Date (Premium required), number, and user types
* [WordPress SEO](http://aihr.us/downloads/wordpress-seo-custom-bulkquick-edit/) - Modify WordPress SEO options via bulk and quick edit panels

= Loads of Benefits and Features =

* API of actions and filters
* Auto-suggest for bulk and quick edit taxonomy entries
* Easily remove or replace `category` and `taxonomy` relations
* Edit excerpts and titles
* Performance minded – Plugin automatically only loads when needed
* Prevents editing of common and static fields like cb, id, author, etc.
* Remove specific category and tag entries
* Sample configurations provided as needed
* Save post handler can be customized for your needs.
* Save values normally (serialized), as CSV, or multiple post meta entries
* Setting options export/import
* Settings screen
* Supports WordPress's own taxonomy handlers for category and tag relations
* Unset checkbox, radio, and select values during bulk edit
* View category and tag relations on edit screen columns
* Works with posts and pages
* Works with [Edit Flow](http://wordpress.org/plugins/edit-flow/)'s checkbox, location, paragraph, and text types


== Installation ==

= Requirements =

* PHP 5.3+ [Read notice](https://aihrus.zendesk.com/entries/30678006) – Since 1.3.0
* WordPress 3.6+
* [jQuery 1.10+](https://aihrus.zendesk.com/entries/23693363)

= Install Methods =

* Through WordPress Admin > Plugins > Add New, Search for "Custom Bulk Quick Edit"
	* Find "Custom Bulk/Quick Edit"
	* Click "Install Now" of "Custom Bulk/Quick Edit"
* Download [`custom-bulkquick-edit.zip`](http://downloads.wordpress.org/plugin/custom-bulkquick-edit.zip) locally
	* Through WordPress Admin > Plugins > Add New
	* Click Upload
	* "Choose File" `custom-bulkquick-edit.zip`
	* Click "Install Now"
* Download and unzip [`custom-bulkquick-edit.zip`](http://downloads.wordpress.org/plugin/custom-bulkquick-edit.zip) locally
	* Using FTP, upload directory `custom-bulkquick-edit` to your website's `/wp-content/plugins/` directory

= Activation Options =

* Activate the "Custom Bulk/Quick Edit" plugin after uploading
* Activate the "Custom Bulk/Quick Edit" plugin through WordPress Admin > Plugins

= Usage =

1. Read "[How do I add custom fields to my bulk/quick edit page?](https://aihrus.zendesk.com/entries/24800411)"
1. Read "[How do you configure options?](https://aihrus.zendesk.com/entries/24911342)"
1. Read "[Where can I find working samples?](https://aihrus.zendesk.com/entries/27667723)"
1. Select the post and page attributes to enable through WordPress Admin > Settings > Custom Bulk/Quick
1. Once you select 'Show' a configuration panel will open. Leave this blank as upon save, the proper configuration will be loaded.
1. Click "Save Changes"
1. Review and revise newly populated configuration options
1. Click "Save Changes"
1. Use edit page Bulk or Quick Edit panels as normal

= Upgrading =

* Through WordPress
	* Via WordPress Admin > Dashboard > Updates, click "Check Again"
	* Select plugins for update, click "Update Plugins"
* Using FTP
	* Download and unzip [`custom-bulkquick-edit.zip`](http://downloads.wordpress.org/plugin/custom-bulkquick-edit.zip) locally
	* Upload directory `custom-bulkquick-edit` to your website's `/wp-content/plugins/` directory
	* Be sure to overwrite your existing `custom-bulkquick-edit` folder contents

= Deactivation =

* Click the "Deactivate" link for "Custom Bulk/Quick Edit Premium" at WordPress Admin > Plugins

= Deletion =

* Click the "Delete" link for "Custom Bulk/Quick Edit Premium" at WordPress Admin > Plugins
* Click the "Yes, Delete these files and data" button to confirm "Custom Bulk/Quick Edit Premium" plugin removal

= Documentation =

* [Installation Guide](https://aihr.us/wordpress-bulk-edit-plugin/installation/)
* [Frequently Asked Questions](https://aihr.us/wordpress-bulk-edit-plugin/faq/)
* [API](https://aihr.us/wordpress-bulk-edit-plugin/api/)
* [Benefits and Features](https://aihr.us/wordpress-bulk-edit-plugin/)
* [CHANGELOG](https://aihr.us/wordpress-bulk-edit-plugin/changelog/)


== Frequently Asked Questions ==

= Most Common Issues =

* [How do I add custom fields to my bulk/quick edit page?](https://aihrus.zendesk.com/entries/24800411)
* [How do you configure options?](https://aihrus.zendesk.com/entries/24911342)
* [Extra vertical columns on edit page](https://aihrus.zendesk.com/entries/23711542)
* [Where can I find working samples?](https://aihrus.zendesk.com/entries/27667723)
* Got `Parse error: syntax error, unexpected T_STATIC…`? See [Most Aihrus Plugins Require PHP 5.3+](https://aihrus.zendesk.com/entries/30678006)
* [Debug theme and plugin conflicts](https://aihrus.zendesk.com/entries/25119302)

= Still Stuck or Want Something Done? Get Support! =

1. [Knowledge Base](https://aihrus.zendesk.com/categories/20112546) - read and comment upon frequently asked questions
1. [Open Issues](https://github.com/michael-cannon/custom-bulkquick-edit/issues) - review and submit bug reports and enhancement requests
1. [Support on WordPress](http://wordpress.org/support/plugin/custom-bulkquick-edit) - ask questions and review responses
1. [Contribute Code](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/CONTRIBUTING.md)
1. [Beta Testers Needed](http://aihr.us/become-beta-tester/) - provide feedback and direction to plugin development
1. [Old Plugin Versions](http://wordpress.org/plugins/custom-bulkquick-edit/developers/)


== Screenshots ==

1. Custom Bulk/Quick Edit Settings panel
2. TwentyTwelve theme with Posts Post Excerpts enabled
3. Posts Quick Edit with excerpts
4. Posts Bulk Edit with excerpts

[gallery]


== Changelog ==

Read [Changelog](https://github.com/michael-cannon/custom-bulk-quick-edit/blob/master/CHANGELOG.md)


== Upgrade Notice ==

TBD


== Notes ==

* This plugin ties into the [bulk_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/bulk_edit_custom_box) and [quick_edit_custom_box](http://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box) actions.
* Unless the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) is already configured, your theme's `functions.php` file will have to modified to add custom field columns.


== API ==

* Read the [Custom Bulk/Quick Edit API](https://github.com/michael-cannon/custom-bulkquick-edit/blob/master/API.md).


== Background ==

This plugin grew out of the frustration of having to custom write this code for every client. It works best when the custom post types have already added columns to the edit screen via the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter.


== Localization ==

* Spanish translation by [Andrew Kurtis from WebHostingHub](http://www.webhostinghub.com)
* Serbo-Croatian translation by [Borisa Djuraskovic](borisad@webhostinghub.com)

You can translate this plugin into your own language if it's not done so already. The localization file `custom-bulkquick-edit.pot` can be found in the `languages` folder of this plugin. After translation, please [send the localized file](http://aihr.us/contact-aihrus/) for plugin inclusion.

**[How do I localize?](https://aihrus.zendesk.com/entries/23691557)**


== Thank You ==
A big, special thank you to [Joe Weber](https://plus.google.com/100063271269277312276/posts) of [12 Star Creative](http://www.12starcreative.com/) for creating the Custom Bulk/Quick Edit banner.

Kudos to [Alex Stone](http://eoionline.org) for documentation revisions.

Current development by [Michael Cannon](https://profiles.wordpress.org/comprock/) of [Aihrus](http://aihr.us/about-aihrus/).
