=== StreamWeasels YouTube Integration ===
Contributors: streamweasels, j.burleigh1, freemius
Tags: youtube, youtube shorts, youtube live, youtube channel, youtube embed
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed YouTube content on your WordPress site. Easily embed a YouTube channel, shorts, gallery, feed, or live on your website.

== Description ==

=== The most advanced YouTube plugin for WordPress ===

https://www.youtube.com/watch?v=QQKCuSG2eHQ

For over 5 years, [StreamWeasels](https://www.streamweasels.com?utm_source=wordpress&utm_medium=youtube-integration&utm_campaign=readme) have been helping thousands of WordPress websites **embed YouTube channel, YouTube gallery, YouTube feed or YouTube live**.

StreamWeasels YouTube Integration is the latest and greatest plugin from StreamWeasels that **takes YouTube Integration to the next level**.

This plugin allows you to **display YouTube embeds anywhere on your website**, based on YouTube Channel ID, YouTube Playlist or YouTube Live streams.

=== Display YouTube Videos by YouTube Channel, Shorts, Playlist, or Live ===

StreamWeasels YouTube Integration allows you to display YouTube shorts, YouTube video and YouTube live on your website from youtube.com based on YouTube Channel ID, Playlist or YouTube Live streams.

* Display upto 50 shorts from a specific YouTube **Channel**.
* Display upto 50 videos from a specific YouTube **Channel**.
* Display upto 50 videos from a specified YouTube **Playlist**.
* Display upto 50 YouTube live streams from a defined YouTube **Channel** list.

=== Display YouTube Shorts ===

https://www.youtube.com/shorts/3Y1ab3k3ilw

=== Layouts ===

The best part about StreamWeasels YouTube Integration is our range of layouts. As of our first release, StreamWeasels YouTube Integration allows you to embed YouTube channel content using a YouTube Wall layout. Display your YouTube content as it may appear directly on YouTube. Customise the number of YouTube tiles that display, the number of tiles in a row, the spacing, rounded borders and more.

=== Read More ===

If you want to learn more about StreamWeasels YouTube Integration, check out these links.

* [YouTube Integration - Getting Started Guide](https://support.streamweasels.com/article/74-getting-started-with-youtube-integration)
* [Check out our YouTube Guides](https://www.youtube.com/channel/UCo885jUiOeyhtHDFUbdx8rQ)
* [Follow us on Twitter](https://twitter.com/StreamWeasels)
* [Join us on Discord](https://discord.com/invite/HSwfPbm)
* [Need Help? Get in touch!](https://www.streamweasels.com/contact/)

== Frequently Asked Questions ==

= How do I display YouTube Shorts from a specific YouTube Channel? =

To display YouTube shorts, simply set the channel ID and set the layout to Shorts showcase like this:

[sw-youtube channel="UCAuUUnT6oDeKwE6v1NGQxug" layout="showcase"]

= How do I display YouTube videos from a specific YouTube Channel? =

[sw-youtube channel="UCAuUUnT6oDeKwE6v1NGQxug"]

= How do I display YouTube videos from a specific YouTube Playlist? =

[sw-youtube playlist="UUAuUUnT6oDeKwE6v1NGQxug"]

= How do I display YouTube live streams from a list of YouTube Channels? =

[sw-youtube livestream="UCXuqSBlHAE6Xw-yeJA0Tunw,UCAuUUnT6oDeKwE6v1NGQxug"]

= How do I create a YouTube API key? =

This plugin requires a YouTube API key in order to pull data from YouTube. An API key from YouTube comes with an API quota of 10,000 units per day. For most sites, this YouTube API key quota will be enough to display all the YouTube channel content you wish.

https://www.youtube.com/watch?v=uA2zoyVVQMs

== Screenshots ==
1. Display YouTube shorts content (showcase layout)
2. Display YouTube channel content (wall layout)
3. Display YouTube live stream content (wall layout)
4. Display YouTube playlist content (wall layout)
5. Display YouTube channel content (player layout)
6. Display YouTube channel content with embed active (player layout)

== Changelog ==

= 1.4.0 =
* Fixed an issue with YouTube cache enabled
* updated freemius

= 1.3.9 =
* updated freemius

= 1.3.8 =
* Pagination now works once again
* Added a new minimal option to tile layout which hides video title and description

= 1.3.7 =
* Properly sanitize and escape all output from sw-youtube-embed shortcode

= 1.3.6 =
* Showcase layout now works with playlists

= 1.3.5 =
* Fixed an issue where the shorts / showcase slide count wasn't working for free users

= 1.3.4 =
* Fixed a bug with nonce generation

= 1.3.3 =
* Nonce generation moved to server side to avoid caching issues
* Properly sanitize and escape all output from sw-youtube-embed shortcode
* updated freemius

= 1.3.2 =
* Added new skew effect to Feature layout
* Fixed some styling issues with Feature layout

= 1.3.1 =
* added alert for new Status Bar plugin

= 1.3.0 =
* fixed an issue with undefined variables
* updating freemius

= 1.2.8 =
* added option to hide shorts from wall layout
* added option to override slide count for shorts
* sw-youtube-embed shortcode cleaned up
* updating freemius

= 1.2.7 =
* now hides private and unlisted videos

= 1.2.6 =
* API requests moved from client side to server side
* added some missing escaping and sanitisation
* added mising headers to API requests
* improved logic for handling number of livestreams

= 1.2.3 =
* removed support for legacy shorts
* added PHP 8.x support

= 1.2.2 =
* Added logged-in check to endpoint

= 1.2.1 =
* You can now display YouTube channel videos from multiple channels at once
* You can now display YouTube shorts from multiple channels at once

= 1.2.0 =
* Changed how shorts are loaded to use the YouTube API
* CSS fixes for the Feature layout on mobile devices (<530px)

= 1.1.9 =
* Added a slide-count variable that can be added to shortcodes to override shorts slide count

= 1.1.8 =
* CSS variables shifted to kebab case to better support html minification
* carousel controls reversed to correct left / right arrows

= 1.1.7 =
* updating freemius
* Debug log will now trim itself when it gets too big
* Debug log will now be emptied when the plugin is de-activated

= 1.1.6 =
* Added a new cache option to cache video results for a single channel
* Added a new cache option to cache playlist results

= 1.1.4 =
* Added block support for all layouts
* Updated freemius

= 1.1.2 =
* Fixed a bug with Shorts on a channel where no videos exist
* added support for YouTube status layout

= 1.1.1 =
* updated freemius to latest
* added translations support for strings
* added messaging for channels with no shorts
* added fix for pagination styles

= 1.1.0 =
* new channel-title field
* Double commas and spaces are now stripped from livestream
* showcase now handles slidesToShow on resize

= 1.0.9 =
* YouTube shorts now display based on container width

= 1.0.8 =
* No longer directly accessing PHP files
* API key marked as not needed for Shorts

= 1.0.7 =
* Now compatible with Block themes
* Using Playlists no longer shows private videos
* ChannelTitle is now more resilient when not returned

= 1.0.6 =
* Added Player layout YouTube videos
* Added Showcase layout for YouTube Shorts

= 1.0.4 =
* Added Feature layout for PRO

= 1.0.3 =
* shortcode attribute fixes
* added support for pagination

= 1.0.2 =
* readme tweaks
* First release