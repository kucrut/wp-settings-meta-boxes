=== Settings Page Meta Boxes ===
Contributors: kucrut
Donate Link: http://kucrut.org/#coffee
Tags: settings, meta-box
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 0.1.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A helper class to easily create custom meta boxes on plugin/theme settings page.


== Description ==
No more boring settings page. Spice it up with meta boxes!

= Usage =
See `settings-meta-boxes-demo.php`.

Please note that `advanced` context is *NOT* supported.

Development of this plugin is done on [GitHub](https://github.com/kucrut/wp-settings-meta-boxes). **Pull requests welcome**. Please see [issues reported](https://github.com/kucrut/wp-settings-meta-boxes/issues) there before going to the plugin forum.


== Screenshots ==
1. Settings Page with Meta Boxes


== Installation ==

1. Upload `settings-meta-boxes` to the `/wp-content/plugins/` directory
1. Activate the plugin through the *Plugins* menu in WordPress

or...

1. Drop settings-meta-boxes.php into `/wp-content/mu-plugins/` directory


== Changelog ==
= 0.1.2 =
* Reuse Core dashboard's style
* Remove support for `advanced` context

= 0.1.1 =
* Fix demo plugin: Don't register meta boxes when `DOING_AJAX`

= 0.1.0 =
* Initial public release
