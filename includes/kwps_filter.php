<?php

/**
* Class KWPS_FILTER
* Author: Martin
* Description: All shortcodes for the polls
*
*/

class Kwps_Filter 
{
	function kwps_shortcode( $atts ) {


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



        if ( (get_post_status( $id ) ) === 'publish' ) {
			$dump .= '<div class="kwps-' . get_post_type( $id ) . ' kwps-' . $id . '" >';
			$dump .= '<div class="kwps-title">' . get_the_title( $mypost->ID ) . '</div>';
			$dump .= '<div class="kwps-intro">' . get_post_meta( $id, '_kwps_intro', true) . '</div>';
			$dump .= '<div class="kwps-outro">' .get_post_meta( $id, '_kwps_outro', true) . '</div>';
			$dump .= '<div class="kwps-content">';
			$dump .= '<div class="kwps-question">' . get_post_meta( $id, '_kwps_question', true) . '</div>';
			$dump .= '<div class="kwps-answers">';

			$answers = array(
				'post_parent' => $id,
				'post_type'   => 'kwps_answer_option', 
				'numberposts' => -1,
				'post_status' => 'publish' );
			$dump .= '<form name="form' . $id . '" method="POST" action="save_answers.php">';
			foreach( $answers as $answer) { 
				$dump .= '<div class="kwps-single-answer kwps-answer-' . $i . '"><input type="radio" name="kwps-answer-' . $id .'" value="'. $answer .'">'. $answer . '</div>';
			}
			$dump .= '</form>';
			$dump .= '</div>'; // kwps-answers
			$dump .= '</div>'; // kwps-content
			$dump .= '</div>'; // kwps full wrapper
		}

		return $dump;
	}
}

/* EOF */