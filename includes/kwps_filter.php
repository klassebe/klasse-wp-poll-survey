<?php
/**
* Class KWPS_FILTER
* Author: Martin
* Description: All shortcodes for the polls
*
*/

class Kwps_Filter 
{
	function poll_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'id' => 0,
			'version' => 'all',
			), $atts ) );

		$dump = '';

		if ($version !== 'all') {
			$mypost = get_post( $id );
		} else {
			$mypost = get_post( $id );
		}

		$dump .= '<div class="well pull-left">';
		$dump .= get_the_title( $mypost->ID, 'medium' );
		$dump .= get_the_post_thumbnail( $mypost->ID, 'medium' );
		$dump .= '</div>';

		return $dump;
	}
}

?>