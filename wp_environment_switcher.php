<?php

/*
Plugin Name: WP Environment Switcher
Plugin URI: https://github.com/theideabureau/WP-Environment-Switcher
Version: 0.1
Author: Ben Everard (The Idea Bureau)
Description: Allows for easy switching of environments within the WordPress admin bar
Text Domain: wp-environment-switcher
License: GPLv3
*/

add_action('wp_head', 'wp_environment_switcher_styles');
add_action('admin_head', 'wp_environment_switcher_styles');

function wp_environment_switcher_styles() {

	echo '<style>
		#wpadminbar .environment-type {
			float: left;
			-webkit-border-radius: 100%;
			-moz-border-radius: 100%;
			border-radius: 100%;
			height: 11px;
			width: 11px;
			background-color: red;
			margin: 8px 8px 0 0;
		}
		#wp-admin-bar-switch-to-live .environment-type {
			margin: 7px 7px 0 0;
		}
		#wpadminbar .environment-type-www {
			background-color: green;
		}
		#wpadminbar .environment-type-staging {
			background-color: orange;
		}
		#wpadminbar .environment-type-dev {
			background-color: red;
		}
	</style>';

}

add_action('admin_bar_menu', 'wp_environment_switcher_menu', 1000);

function wp_environment_switcher_menu() {

	global $wp_admin_bar;

	$environment_labels = array(
		'www' => 'Live',
		'staging' => 'Staging',
		'dev' => 'Development'
	);

	$sub_domain = current(explode('.', $_SERVER['HTTP_HOST']));

	// if the sub_domain is not in the pre-existing list, set it to www
	if ( ! isset($environment_labels[$sub_domain]) ) {
		$sub_domain = 'www';
	}

	$current_environment = $environment_labels[$sub_domain];

	$wp_admin_bar->add_menu(array(
		'id' => 'environment_switch_menu',
		'title' => '<span class="environment-type environment-type-' . $sub_domain . '"></span> Environment: ' . $current_environment, 
		'href' => false
	));

	foreach ( $environment_labels as $key => $environment ) {

		// don't give the option to switchv
		if ( $sub_domain === $key ) {
			continue;
		}

		$wp_admin_bar->add_menu(array(
			'parent' => 'environment_switch_menu',
			'title' => '<span class="environment-type environment-type-' . $key . '"></span> Switch to ' . strtolower($environment),
			'href' => 'http://' . str_replace($sub_domain . '.', $key . '.', $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']
		));

	}

}