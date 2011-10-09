=== movingboxes-wp ===
Contributors: jonhorner
Donate link: http://www.web-first.co.uk/wordpress/moving-boxes-wordpress-plugin/
Tags: simple, gallery, slider, images, image, Moving Boxes, jquery, pictures, thumbnail, shortcode
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: trunk

movingboxes-wp displays images attached to posts or pages in a jQuery image slider which expands the images as you scroll through them.

== Description ==

movingboxes-wp presents images from the Wordpress media library as a MovingBoxes image gallery using a shortcode to position it in your posts or pages. As well as display images along with titles and short captionsl links can be also added.

Images are added to the galleries by uploading via the WordPress Media Gallery and attaching them to the post or page on which they will be displayed. The MovingBoxes gallery can be configured to customise the starting panel, gallery width, panel width, reduced size, fixed height, speed, use of hash tags, wrapping the slider animation, controlling navigation and the animation easing.

The Moving Boxes jQuery code was originally written by Chris Coyier (css-tricks.com) and is currently being updated and maintained by Mottie. It is available from GitHub here: https://github.com/chriscoyier/MovingBoxes. Documentation for the actual image gallery can be found here: https://github.com/chriscoyier/MovingBoxes/wiki.

= Requirements =

Server

* WordPress 3.0+ (Not tested on earlier versions but may work.)
* PHP 5+ (Recommended)

Client

* IE 7+, FireFox 3.6+, Chrome, Safari 3+, Opera 8+

== Installation ==

1. Unzip and upload the folder and all files to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Upload your images to the WordPress media library `/wp-admin/media-new.php` and attach them to the appropriate page/post.
4. Place the shortcode [MovingBoxes] in the location that you want the gallery to appear.
5. Control the galleries settings in `wp-admin/options-general.php?page=MovingBoxes-Admin`.

        
== Screenshots ==

1. This screenshot shows how to set up the text for each image.
2. This screenshot shows how to attach the images to your posts.
3. This shows how to select the images to display in the gallery and how to position the gallery in your post.
4. This shows the gallery settings

== Frequently Asked Questions ==

None as yet. If you have a question please ask it here: http://www.web-first.co.uk/wordpress/moving-boxes-wordpress-plugin/

== Upgrade Notice ==

No functionality updates, just fixed some broken links.


== Changelog ==

= 0.4.2 =
* Added support link
* Fixed plugin link

= 0.4.1 =
* Initial release. Provides basic configuration options and the original template.