=== Plugin Name ===
Contributors: Jesper800
Tags: bulk,categories,add,category,multiple,mass,edit,taxonomy,taxonomies,term,terms,slug,create
Requires at least: 2.7.0
Tested up to: 3.5.1
Stable tag: 1.2
License: GPLv2 or later

The WPEssence Bulk Categories plugin allows for easily creating multiple categories at once, including slugs and category parents.

== Description ==

= DEPRECATION NOTICE =

As of the **14th of march, 2013**, this plugin is **no longer supported** as it has been superseded by the [BulkPress plugin](http://wordpress.org/extend/plugins/bulkpress/). The BulkPress plugin does everything the WPEssence Bulk Categories plugin does but adds enhanced functionality to adding terms and allows you to add other types of content, such as posts.

The WPEssence Bulk Categories plugin will no longer be supported, and you are strongly encouraged to deactivate it on your website and install the BulkPress plugin instead.

= Old information below =

**Update**: As of version 1.1, WPEssence Bulk Categories also allows you to create tags, other custom taxonomies and even taxonomies registered by your plugin or theme!

Have you ever had to create a category for every single city in the Netherlands? I know I have. It can take quite some time to manually add tens or even hundreds of categories through the WordPress admin panel, and that's where this plugin comes in.

The WPEssence Bulk Categories plugin allows you to easily **add multiple categories** to your WordPress website, without manually adding them one by one. The plugin features **full category paths**, meaning you can add top categories as well as category children. WPEssence Bulk Categories will not do anything to the front-end of your website, but just add a menu item under "Posts" in the sidebar of the admin panel, where you can add as many categories as you want in one go.

WPessence Bulk Categories is *not limited to standard categories*, it features other taxonomies such as tags, but also taxonomies registered by your plugin or theme. Just select the taxonomy you wish to create at the top of the Bulk Categories admin panel page!

Custom slugs are also possible for every created category, as well as the option to add all categories you want to create under the same parent category, so you won't have to type out that category name in the category path every time. You can also choose not to create parent categories which do not already exist, to make sure there won't be a new branch of categories if you accidentally typed the wrong category name in one of the paths.

== Installation ==

1. Upload the folder 'wpe-bulkcategories' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go the plugin page (via the menu item in the sidebar, under "Posts")
1. Start adding categories in bulk!

== Frequently Asked Questions ==

= Adding categories =
Enter the categories you want to add in the left textbox, separating different categories by newlines. You can assign categories to parent categories by entering the full category path, separating different category names by slashes (/).

= Slugs =
Slugs will be automatically generated if they are not manually set, but it is also possible to enter specific slugs for each category. You can do this by putting the slugs in the right textbox on the lines of that textbox corresponding with the lines in the left textbox (for the category paths).

= How can I choose my own slugs for the categories? =
In the "Bulk Add Categories" screen, there are two textboxes. The left one is for the categories, and the right one is for the category slugs. The textboxes have line numbers, allowing you to easily choose the slugs for the right categories, even if you are adding a lot of categories at once. If you leave a line blank, the category with that line number will automatically generate its slug.

= How do I specify a full category path? =
Parent and child categories are separated by a slash (*/*). If your category name contains a slash, such as "Audio/Video", you can use quotes to use that as a full name (so it won't get seperated as Audio -> Video).

= Can I add other taxonomies, besides regular categories? =
Yes, as of version **1.1**, the plugin allows you to create custom taxonomies as well as other built-in taxonomies such as tags. To create custom taxonomies, go to the Bulk Categories screen and select the taxonomy you wish to create at the top.

= Are there any examples of category lists available? =
If you go the "Bulk Add Categories" screen, you can view an example by clicking on the example-link (under the "Example" heading).

== Screenshots ==

1. The "Bulk Add Categories" screen, with the example categories listed
2. Additional configuration for adding the categories
3. The newly added categories!

== Changelog ==

= 1.2 =
* Minor changes
* Plugin deprecated and replaced by the BulkPress plugin, which offers much more functionality

= 1.1.1 =
* Fixed JavaScript problems causing the editing of menu's not to work

= 1.1 =
* Added feature to create custom taxonomies. You can select whether you want to add categories, tags or even taxonomies registered by your theme or plugin!
* Added a screenshots page to the WordPress page

= 1.0.1 =
* Fixed a problem with the plugin path

= 1.0 =
* First version

== Upgrade Notice ==

= 1.1.1 =
This upgrade fixes JavaScript-related problems you may have experienced configuring menu's with the plugin enabled.

= 1.1 =
This upgrade adds support for custom taxonomies

= 1.0.1 =
This upgrade fixes the include errors. If the plugin did not show errors for you, this plugin is not necessary.

= 1.0 =
First version

== Translations ==
There is currently one translation available.

* Dutch (nl_NL)