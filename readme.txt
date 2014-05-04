=== Custom Bulk/Quick Edit by Aihrus ===

Contributors: comprock
Donate link: http://aihr.us/about-aihrus/donate/
Tags: custom, bulk edit, quick edit, custom post types, woocommerce
Requires at least: 3.6
Tested up to: 3.9.0
Stable tag: 1.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Bulk/Quick Edit by Aihrus allows you to easily add custom fields to the edit screen bulk and quick edit panels.


== Description ==

Through Custom Bulk/Quick Edit [by Aihrus](http://aihr.us/about-aihrus/), you have the option to edit post meta via text, checkbox, radio, select, and textarea inputs within Bulk Edit and Quick Edit screens. Further, you can enable editing of category and tag taxonomies that don't show up already. Next, taxnomony, checkbox, radio, and select fields have an option to be reset, as in remove current options during Bulk Editing. This is very helpful when you want to mass reset or remove information.

To use this Custom Bulk/Quick Edit plugin with custom post types, please purchase [Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/). Read more of the premium features below.

[youtube https://www.youtube.com/watch?v=wd6munNz0gI]

**[Video introduction](http://youtu.be/UXvzdlvIPtk)**

Custom Bulk/Quick Edit automatically detects custom fields that use the [manage_{$post_type}_posts_columns](http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_$post_type_posts_columns) filter to display additional columns on the edit screen. **Therefore, unless it's already configured, your theme's `functions.php` file will have to modified to add custom field columns.**

Read [**Installation**](http://wordpress.org/plugins/custom-bulkquick-edit/installation/) and [**FAQ**](http://wordpress.org/plugins/custom-bulkquick-edit/faq/) to get started.

= Primary Features =

* API of actions and filters
* Auto-suggest for bulk and quick edit taxonomy entries
* Easily remove or replace `category` and `taxonomy` relations
* Edit excerpts and titles
* Performance minded – Plugin automatically loads when needed
* Prevents editing of common and static fields like cb, id, author, etc.
* Remove specific category and tag entries
* Sample configurations provided as needed
* Save post handler can be customized for your needs.
* Setting options export/import
* Settings screen
* Supports WordPress's own taxonomy handlers for category and tag relations
* Unset checkbox, radio, and select values during bulk edit
* View category and tag relations on edit screen columns
* Works with posts and pages
* Works with [Edit Flow](http://wordpress.org/plugins/edit-flow/)'s checkbox, location, paragraph, and text types

= Add Ons =
* [Edit Flow](http://wordpress.org/plugins/cbqe-edit-flow/) - Date (Premium required), number, and user types
* [WordPress SEO](http://aihr.us/downloads/wordpress-seo-custom-bulkquick-edit/) - Modify WordPress SEO options via bulk and quick edit panels

= Custom Bulk/Quick Edit Premium =

Custom Bulk/Quick Edit Premium adds onto the best WordPress bulk edit plugin there is, [Custom Bulk/Quick Edit](http://wordpress.org/extend/plugins/custom-bulkquick-edit/). Custom Bulk/Quick Edit Premium supports [custom post types](https://aihr.us/custom-bulkquick-edit-premium/). Plus, it offers additional inputs options like date and multiple selects for use during bulk/quick edit operations.

[Buy Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/) plugin for WordPress.

= Primary Premium Features =

* Adds float, integer, and user inputs
* Bulk edit post dates
* Color input with color picker
* Coming Soon! [WooCommerce product attributes](http://www.woothemes.com/woocommerce/)
* Date and time input with datetime picker
* Date input with date picker
* Disable donate references
* Flexible API
* Multiple select selector
* Works with Custom Post Types
* Works with [WooCommerce custom attributes and product types](http://www.woothemes.com/woocommerce/)

[Buy Custom Bulk/Quick Edit Premium](http://aihr.us/downloads/custom-bulkquick-edit-premium-wordpress-plugin/) plugin for WordPress.

= Settings =

**Post**

* Enable "Title"? – Enable editing of post_type' title.
* Enable "Excerpt"? – Enable editing of post_type' excerpt.
* Edit "TBD" taxonomy? – Force making TBD an editable taxonomy field like checked categories or free-text tags.
	* No
	* No, but enable column view (view the column on the admin edit screen)
	* Like categories
	* Like tags
* Reset "TBD" Relations? – During bulk editing, easily remove all of the TBD's prior relationships and add new.
* Enable "Date"? – Enable bulk editing of post_type' date
* Enable "Custom Field"? - As checkbox, radio, select, input, or textarea
* "Custom Field" Configuration - You may create options formatted like "the-key|Supremely, Pretty Values" seperated by newlines.
	* Example configuration
`1
Two
3|Three
four|Four, and forty five
five-five|55`

**Pages**

See Post.

**Reset**

* Export Settings – These are your current settings in a serialized format. Copy the contents to make a backup of your settings.
* Import Settings – Paste new serialized settings here to overwrite your current configuration.
* Remove Plugin Data on Deletion? - Delete all Custom Bulk/Quick Edit data and options from database on plugin deletion
* Reset to Defaults? – Check this box to reset options to their defaults


== Installation ==

= Requirements =

* PHP 5.3+ [Read notice](https://aihrus.zendesk.com/entries/30678006) – Since 1.3.0

= Install Methods =

* Through WordPress Admin > Plugins > Add New, Search for "Custom Bulk Quick Edit"
	* Find "Custom Bulk/Quick Edit by Aihrus"
	* Click "Install Now" of "Custom Bulk/Quick Edit by Aihrus"
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

1. Read "[How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411)"
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


== Frequently Asked Questions ==

= Most Common Issues =

* [How do I add custom columns to my edit page?](https://aihrus.zendesk.com/entries/24800411)
* [How do you configure options?](https://aihrus.zendesk.com/entries/24911342)
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

See [Changelog](https://github.com/michael-cannon/custom-bulk-quick-edit/blob/master/CHANGELOG.md)


== Upgrade Notice ==

= 1.5.0 =

* Alters option `active_plugins` so that this plugin is among first loaded.

= 1.3.0 =

* Requires PHP 5.3+ [notice](https://aihrus.zendesk.com/entries/30678006)

= 1.1.0 =

* Please review your settings as some option keys have changed. There's no auto-upgrade at this time.

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
