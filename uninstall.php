<?php


// Make sure that uninstall was called by WordPress
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit;

// Remove the database entry created by this plugin
delete_option('osmapsWP');

