<?php

namespace includes;

require_once 'kwps-post-type.php';
require_once 'question.php';
require_once 'answer-option.php';

class Version extends Kwps_Post_Type{
    public static $required_fields = array(
        'post_status',
        'post_parent',
        '_kwps_sort_order',
    );

    public static $numeric_fields = array();

    public static $meta_data_fields = array(
        '_kwps_sort_order',
        '_kwps_view_count',
    );

    public static $label = 'kwps-version';

    public static $post_type = 'kwps_version';

    public static $rewrite = array(
            'slug' => 'versions',
            'with_front' => false,
        );

    public static $post_type_args = array(
            'public' => true,
            'supports' => array(
                'title',
            ),
            'labels' => array(
                'name' => 'versions',
                'singular_name' => 'version',
                'add_new' => 'Add New version',
                'add_new_item' => 'Add New version',
                'edit_item' => 'Edit version',
                'new_item' => 'New version',
                'view_item' => 'View version',
                'search_items' => 'Search versions',
                'not_found' => 'No versions Found',
                'not_found_in_trash' => 'No versions Found In Trash',
            ),
            'show_in_menu' => false,
            'show_ui' => false,
            'hierarchical' => true,
    );

    public static function get_test_modus($version_id)
    {
        $version = static::get_as_array($version_id);
        return Test_Collection::get_test_modus($version['post_parent']);
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }

    public static function shortcode($atts){
        extract( shortcode_atts( array(
            'id' => 0,
            'version' => 'all',
        ), $atts ) );

        return static::get_html($id);
    }

    public static function save_post($post_data){
        $post_id = wp_insert_post($post_data);
        if( ! isset($post_data['_kwps_view_count'] ) ) {
            $post_data['_kwps_view_count'] = 0;
        }

        if( $post_id != 0 ){
            foreach($post_data as $field => $value){
                if( strpos($field, 'kwps') ) {
                    update_post_meta($post_id, $field, $value);
                }
            }
        } else {
            return null;
        }

        return static::get_as_array($post_id);
    }

    public static function ajax_validate_for_publish(){
        $version = static::get_post_data_from_request();
        $response = static::validate_for_publish($version);

        if( sizeof( $response ) > 0 ) {
            wp_send_json_error($response);
        } else {
            wp_send_json_success($response);
        }

        die();
    }

    public static function validate_for_publish($version){
        /*
         * 1) intro result required, only one allowed
         * 2) outro required -> requires shortcode in content, only one allowed
         * 3) at least 1 question group required
         * 4) at least 1 question for each question group required
         * 5) at least 2 answer options for each question required
         * 6) check if allowed output types have limitations (min & max), if so, make sure number of outputs are correct
         */

        $errors = array();

        if( !isset( $version['ID'] ) ) {
            return array(
                array(
                    array(
                        'ID' => null,
                        'field' => 'All',
                        'message' => __( 'Cannot publish when not saved as draft first', 'klasse-wp-poll-survey' ),
                    )
                ),
            );
        } else {
            $version_id = $version['ID'];

            $intro_results = Intro_Result::get_all_by_post_parent( $version_id );

            $errors = array_merge($errors, static::check_array_to_hold_single_value( $intro_results, 'Intro result' ) );

//      Removed as per https://github.com/klassebe/klasse-wp-poll-survey/issues/46
//            $intros = Intro::get_all_by_post_parent( $version_id );
//            $errors = array_merge($errors, static::check_array_to_hold_single_value( $intros, 'Intro' ) );

            $outros = Outro::get_all_by_post_parent( $version_id );
            $errors = array_merge($errors, static::check_array_to_hold_single_value( $outros, 'Outro' ) );

            $test_modus = static::get_test_modus( $version_id );

            foreach($outros as $outro){
                $pattern = '/\[kwps_result.*\]/';
                $subject = $outro['post_content'];
                $shortcode_count = preg_match_all($pattern, $subject, $kwps_result_matches);
                if( $shortcode_count <= 0 ){
                    $errors[] = array(
                        'ID' => $version_id,
                        'field' => 'Outro',
                        'message' => __( 'No result shortcodes used!!', 'klasse-wp-poll-survey' ),
                    );
                } else {
                    foreach($kwps_result_matches as $shortcode){
                        $output_start_pos = strpos($shortcode[0], '=');

                        $output_type_temp = substr($shortcode[0], $output_start_pos + 1);
                        $output_type = trim($output_type_temp, ']');

                        if( !in_array( $output_type, $test_modus['_kwps_allowed_output_types'] ) ) {
                            $errors[] = array(
                                'ID' => $version_id,
                                'field' => 'Outro',
                                'message' => __( 'Invalid value ', 'klasse-wp-poll-survey') . $output_type .
                                    __(' in shortcode', 'klasse-wp-poll-survey' ) );
                        }
                    }
                }
            }


            $question_groups = Question_Group::get_all_by_post_parent( $version_id );

            if( count( $question_groups ) < 1 ){
                $errors[] = array(
                    'ID' => $version_id,
                    'field' => 'Question group',
                    'message' => __( 'Minimum 1 question page required', 'klasse-wp-poll-survey' )
                );
            }

            foreach($question_groups as $question_group) {
                $questions = Question::get_all_by_post_parent( $question_group['ID'] );

                if( count( $questions ) < 1 ){
                    $errors[] = array(
                        'ID' => $version_id,
                        'field' => 'Questions',
                        'message' => __( 'Minimum 1 question per question page required', 'klasse-wp-poll-survey' ),
                    );
                }

                foreach($questions as $question){
                    $answer_options = Answer_Option::get_all_by_post_parent( $question['ID'] );
                    if( count( $answer_options ) < 2 ){
                        $errors[] = array(
                            'ID' => $version_id,
                            'field' => 'Question group',
                            'message' => __( 'Minimum 2 answer options per question required', 'klasse-wp-poll-survey' ) );
                    }

                }
            }


            if( isset( $test_modus['_kwps_allowed_output_types'] ) ) {
                if( in_array( 'result-profile', $test_modus['_kwps_allowed_output_types'] ) ) {
                    $result_profiles = Result_Profile::get_all_by_post_parent( $version_id );
                    if( count( $result_profiles ) < 2 ) {
                        $errors[] = array(
                            'ID' => $version_id,
                            'field' => 'Result profile',
                            'message' => __( 'Minimum 2 result profiles needed', 'klasse-wp-poll-survey' ),
                        );
                    } else {
                        foreach($result_profiles as $result_profile_outer_loop){
                            if( $result_profile_outer_loop['_kwps_min_value'] >= $result_profile_outer_loop['_kwps_max_value'] ){
                                $errors[] = array(
                                    'ID' => $version_id,
                                    'field' => 'Result profile',
                                    'message' => __( 'Min. value should be smaller than Max. value' ),
                                );
                            }
                            foreach($result_profiles as $result_profile_inner_loop){
                                if( $result_profile_outer_loop['ID'] != $result_profile_inner_loop['ID']) {
                                    if(
                                        ( $result_profile_outer_loop['_kwps_min_value'] >= $result_profile_inner_loop['_kwps_min_value']
                                        && $result_profile_outer_loop['_kwps_min_value'] <= $result_profile_inner_loop['_kwps_max_value'] )
                                        ||
                                        ( $result_profile_outer_loop['_kwps_max_value'] >= $result_profile_inner_loop['_kwps_min_value']
                                            && $result_profile_outer_loop['_kwps_max_value'] <= $result_profile_inner_loop['_kwps_max_value'] )
                                    ) {
                                        $errors[] = array(
                                            'ID' => $version_id,
                                            'field' => 'Result profile',
                                            'message'=> __( 'Overlap between ', 'klasse-wp-poll-survey' ) . $result_profile_inner_loop['post_title'] .
                                                __( ' and ', 'klasse-wp-poll-survey') . $result_profile_outer_loop['post_title'],
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $errors;
        }

    }

    public static function check_array_to_hold_single_value($data, $field) {
        $errors = array();
        if( count( $data ) == 0 ) {
            $errors[] = array(
                'ID' => $version_id,
                'field' => $field,
                'message' => __( 'Required', 'klasse-wp-poll-survey' ) );
        } elseif( count( $data ) > 1 ) {
            $errors[] = array(
                'ID' => $version_id,
                'field' => $field,
                'message' => __( 'Only 1 allowed', 'klasse-wp-poll-survey' ));
        }

        return $errors;
    }

    public static function get_html($id){
	    $version = Version::get_as_array($id);
        $view_count = (int) $version['_kwps_view_count'];
        $view_count++;
	    $version['_kwps_view_count'] = $view_count;
        $test_collection = Test_Collection::get_as_array($version['post_parent']);

        static::save_post($version);
	    $limitations = Test_Collection::get_meta_data($version['post_parent']);

	    if( is_user_logged_in() ){
		    $limit_to_apply = $limitations['_kwps_logged_in_user_limit'];
	    } else {
		    $limit_to_apply = $limitations['_kwps_logged_out_user_limit'];
	    }


	    $data = array(
		    'settings' => array(
			    'first_question_id_allowed' => -1
		    )
	    );
	    $data['intro'] = Intro::get_one_by_post_parent($id);
	    $data['outro'] = Outro::get_one_by_post_parent($id);
	    $data['intro_result'] = Intro_Result::get_one_by_post_parent($id);
	    $data['question_groups'] = Question_Group::get_all_by_post_parent($id);

        $allowed_to_fill_out_test = false;

        foreach($data['question_groups'] as $questionGroupKey => $questionGroup) {
		    $data['question_groups'][$questionGroupKey]['questions'] = Question::get_all_by_post_parent($questionGroup['ID']);

		    foreach($data['question_groups'][$questionGroupKey]['questions'] as $questionKey => $question) {
			    if( Uniqueness::is_allowed($question['ID'], $limit_to_apply) && $data['settings']['first_question_id_allowed'] < 0 ){
				    $data['settings']['first_question_id_allowed'] = $question['ID'];
				    $allowed_to_fill_out_test = true;
			    }
			    $data['question_groups'][$questionGroupKey]['questions'][$questionKey]['answer_options'] = Answer_Option::get_all_by_post_parent($question['ID']);
		    }
	    }

        Session::set_version_info($id);
        ob_start();
?>
            <div class="kwps-version">
                <input type="hidden" class="kwps-version-id" value="<?php echo $version['ID']?>">
                <input type="hidden" class="admin-url" value="<?php echo admin_url(); ?>">
<?php
        if( in_array($test_collection['post_status'], array('locked', 'trash')) || !$allowed_to_fill_out_test) {
            ?>

                <?php if(!empty($data['intro_result'])): ?>
                    <div class="kwps-page kwps-intro-result">
                        <div class="kwps-content">
                            <?php
                            /* SEARCH THE SHORTCODE AND REPLACE IT */
                            $replacement_arr = [];
                            $pattern_arr = [];
                            $pattern = '/\[kwps_result.*\]/';
                            $subject = $data['intro_result']['post_content'];
                            preg_match_all($pattern, $subject, $kwps_result_matches);
                            foreach ($kwps_result_matches[0] as $kwps_result_match) {
                                $replacement_arr[] = do_shortcode($kwps_result_match);
                                $pattern_arr[] = '/\\' . substr($kwps_result_match,0,-1) . '\]/';
                            }
                            $output = preg_replace($pattern_arr, $replacement_arr, $subject);
                            echo $output;
                        ?>
                        </div>
                    </div>
                <?php endif; ?>

        <?php
        }
        elseif( $test_collection['post_status'] === 'draft' && !current_user_can('edit_posts') ) {
            ?>
            <div class="kwps-version"><?php echo __( 'You need to be logged in to view this version', 'klasse-wp-poll-survey' ); ?></div>
        <?php
        } else {
?>

            <?php if(!empty($data['intro'])): ?>
                <div class="kwps-page kwps-intro">
                    <div class="kwps-content">
                        <?php echo $data['intro']['post_content']; ?>
                    </div>
                    <div class="kwps-button">
                        <button class="kwps-next"><?php _e('Next', 'klasse-wp-poll-survey') ?></button>
                    </div>
                </div>
            <?php endif; ?>


            <?php foreach($data['question_groups'] as $questionGroup): ?>
                <div class="kwps-page kwps-question-group ">
                    <div class="kwps-question-group-title">
                        <?php echo $questionGroup['post_title']; ?>
                    </div>
                    <div class="kwps-questions">
                        <?php foreach($questionGroup['questions'] as $question): ?>
                            <div class="kwps-question">
                                <?php echo $question['post_content'] ?>
                                <div class="kwps-answer-option">
                                    <ul>
                                        <?php foreach($question['answer_options'] as $answerOption): ?>
                                            <li><input id="answer-option-<?php echo $answerOption['ID'] ?>" type="radio" name="question_id_<?php echo $question['ID'] ?>" value="<?php echo $answerOption['ID'] ?>"><label for="answer-option-<?php echo $answerOption['ID'] ?>"><?php echo $answerOption['post_content'] ?></label></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                    <div class="kwps-button">
                        <button class="kwps-next"><?php _e('Next', 'klasse-wp-poll-survey') ?></button>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(!empty($data['outro'])): ?>
                <div class="kwps-page kwps-outro">
                    <div class="kwps-content">
                        <?php
                            /* SEARCH THE SHORTCODE AND REPLACE IT */
                            $replacement_arr = [];
                            $pattern_arr = [];
                            $pattern = '/\[kwps_result.*\]/';
                            $subject = $data['outro']['post_content'];
                            preg_match_all($pattern, $subject, $kwps_result_matches);
                            foreach ($kwps_result_matches[0] as $kwps_result_match) {
                                $replacement_arr[] = do_shortcode($kwps_result_match);
                                $pattern_arr[] = '/\\' . substr($kwps_result_match,0,-1) . '\]/';
                            }
                            $output = preg_replace($pattern_arr, $replacement_arr, $subject);
                            echo $output;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        
<?php
        }
        ?>
        </div> <!-- END KWPS VERSION DIV -->
<?php
        return ob_get_clean();
    }
}