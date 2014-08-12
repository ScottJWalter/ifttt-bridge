ifttt-bridge WordPress project
==================================================

Project description
-------------------
This plugin contains a WordPress plugin in a source folder and additional build and test resources. If you want to use the project files as a WordPress plugin use only the files in the **src** folder!

Plugin description
------------------
IFTTT Bridge for WordPress is a plugin that allows you to display IFTTT-processed data on your WordPress site in any way you like.

*One plugin, unlimited possibilities*

If you love IFTTT, but have always regretted that there are too many limits on what you can do with the standard IFTTT-WordPress channel, then this plugin is for you.

IFTTT Bridge for WordPress is a technical bridge between IFTTT und WordPress that allows flexible use of IFTTT-processed data in WordPress. There are no limits to what can be displayed and how.

One example is the **IFTTT Instagram Gallery**. Instead of using the standard WordPress channel offered by IFTTT, which only posts one photo at a time, IFTTT Instagram Gallery will allow you to show your latest Instagram photos in an awesome and highly customizable sidebar grid or within your text field, displaying any number of photos and columns you like.

*For blog owners and administrators*

IFTTT Bridge for WordPress will only prepare and process your IFTTT data, ensuring that you can use it on your WordPress blog in any way you like. To make it “come alive” on your blog, you will have to install a second plugin. Below you will find a list of currently available plugins that are compatible with IFTTT Bridge for WordPress:  

- IFTTT Instagram Gallery

*For developers*

IFTTT Bridge for WordPress will process the data received and call the WordPress activity "ifttt-bridge". Any plugins that have registered for this activity will be notified and will receive the data.

If you have developed a plugin or plan to do so, feel free to contact me! I will gladly include your published plugin in this list.

*What is IFTTT?*

IFTTT or “If This Then That” is a service that enables users to connect different web applications (e.g., Facebook, Evernote, Weather, Dropbox, etc.) together through simple conditional statements known as “Recipes”. It sounds very technical but is actually really easy. Here are some typical examples of what IFTTT can do:

* If you post a new photo on Instagram, it will automatically be posted on your Facebook wall.
* When a new item on Ebay comes up that matches your search certain criteria, the results will be sent to you via email.
* Every time you are tagged in a photo on Facebook, it will be sent to Dropbox.

 
*What do I have to do to use this plugin?*

1. Install this plugin (installation instructions can be found under the “Installations” tab)
1. Register at www.ifttt.com
1. Install the IFTTT Instagram Gallery or any other IFTTT plugin that fits your purpose. If you are a developer, you might even want to develop a plugin yourself.

If you need help, don’t hesitate to contact me!

If you like this plugin, please rate it.

Installation
------------
1. Upload plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure plugin (Settings -> IFTTT Bridge for WordPress)

Build
-----
This project contains files to use **Composer** and **Grunt**:

*  The composer file contains the PHP dependencies to execute the **behat** specs (see below)
*  With **grunt** you can update the .pot file according to the used i18n methods in the source code.

Test
----
This projects contains several **behat** specs. A folder **install** with several files and a proper **behat.yml** is necessary. In **install** there must exist:

*  WordPress installation file (for the test a German installation file)
*  Plugin installation files
*  SQLite database file