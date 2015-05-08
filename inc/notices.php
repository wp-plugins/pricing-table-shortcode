<?php

add_action( 'admin_notices', 'pts_admin_notices' );
function pts_admin_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	$activationDate = get_user_meta( $user_id, 'pts_plugin_activation' );
	$activationDateVar = $activationDate[0];
	$aWeekFromActivation = strtotime( $activationDateVar . '+1 week' );
	$twoWeeksFromActivation = strtotime( $activationDateVar . '+2 weeks' );
	$currentPluginDate = strtotime( 'now' );

	$rateOutput = '<div id="message" class="updated notice">';
		$rateOutput .= '<p>Please rate <a href="https://wordpress.org/plugins/pricing-table-shortcode/" target="_blank">Pricing Table Shortcode</a>. If you have already, simply <a href="?pts_rate_ignore=dismiss">dismiss this notice</a>.</p>';
	$rateOutput .= '</div>';

	$donateOutput = '<div id="message" class="updated notice">';
		$donateOutput .= '<p>Looks like you\'re enjoying <a href="https://wordpress.org/plugins/pricing-table-shortcode/" target="_blank">Pricing Table Shortcode</a>. Consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=yusrimathews%40gmail%2ecom&lc=ZA&item_name=Yusri%20Mathews&item_number=pricing%2dtable%2dshortcode&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">making a donation</a>, alternatively <a href="?pts_donate_ignore=dismiss">dismiss this notice</a>.</p>';
	$donateOutput .= '</div>';

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'pts_rate_ignore' ) != 'true' && $currentPluginDate >= $aWeekFromActivation ){
		echo $rateOutput;
	}

	if( current_user_can( 'activate_plugins' ) && get_user_meta( $user_id, 'pts_donate_ignore' ) != 'true' && $currentPluginDate >= $twoWeeksFromActivation ){
		echo $donateOutput;
	}
}

add_action( 'admin_init', 'pts_ignore_notices' );
function pts_ignore_notices(){
	global $current_user;
	$user_id = $current_user->ID;

	if( isset( $_GET['pts_rate_ignore'] ) && $_GET['pts_rate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'pts_rate_ignore', 'true' );
	}

	if( isset( $_GET['pts_donate_ignore'] ) && $_GET['pts_donate_ignore'] == 'dismiss' ){
		update_user_meta( $user_id, 'pts_donate_ignore', 'true' );
	}
}
