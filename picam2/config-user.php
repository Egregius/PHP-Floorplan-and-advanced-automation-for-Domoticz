<?php
// If this file is edited, reload web pages to see the results.
//
// Change xxx_SCROLL_PIXELS to customize scroll window heights to your
// usual browser window height.
// DEFAULT_TEXT_COLOR applies to miscellaneous web page text.
// MEDIA_TEXT_COLOR applies to motion video and still filenames.
//
define("N_VIDEO_SCROLL_PIXELS", "380");
define("N_STILL_SCROLL_PIXELS", "300");
define("N_THUMB_SCROLL_PIXELS", "770");
define("N_LOG_SCROLL_PIXELS", "770");

define("DEFAULT_TEXT_COLOR", "black");
define("SELECTED_TEXT_COLOR", "#500808");
define("MEDIA_TEXT_COLOR", "#0000EE");
define("MANUAL_VIDEO_TEXT_COLOR", "#085008");
define("LOG_TEXT_COLOR", "black");

// For backgrounds, use shadow.jpg, paper1.png or passion.jpg
// in the images directory.  Or install your own background in the
// images directory and name it bg_XXX.jpg (or .png) so git will ignore it.
//
define("BACKGROUND_IMAGE", "images/paper1.png");

// PiKrellCam controls in development.  Set to "yes" to see mock up.
// User custom controls can be implemented in custom-control.php.
//
define("INCLUDE_CONTROL", "no");

// Setting VIDEO_URL allows a special case viewing of videos off site if
// your scripts have uploaded videos to Dropbox or Google Drive.
// E.g. to play uploaded Dropbox videos from PiKrellCam, edit VIDEO_URL to:
//     define("VIDEO_URL", "https://www.dropbox.com/home/Apps/myapp?preview=");
// Now, when the thumbs view is not scrolled (click "Toggle Scrolled"), thumb
// image URL links will be to VIDEO_URL with the video filename appended.
//
define("VIDEO_URL", "");

// The remaining defines here are changed by web page buttons and should not
// need to be edited here.
//
define("NAME_STYLE", "short");
define("N_COLUMNS", "4");
define("VIDEOS_MODE", "thumbs");
define("ARCHIVE_INITIAL_VIEW", "videos");
define("ARCHIVE_THUMBS_SCROLLED", "no");
define("MEDIA_THUMBS_SCROLLED", "no");

// Do not edit this value..
define("CONFIG_EVENT_COUNT", "18");
?>
