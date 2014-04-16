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
		if ( wp_post_status( $id ) === 'publish' ) {
			$dump .= '<div class="kwps_poll">';
			$dump .= get_the_title( $mypost->ID );
			$dump .= get_post_meta( $id, '_kwps_intro', true);
			$dump .= get_post_meta( $id, '_kwps_outro', true);
			$dump .= get_post_meta( $id, '', true);
			$dump .= get_post_meta( $id, '', true);
			$dump .= '</div>';
		}

		return $dump;
	}
}

/* EOF */