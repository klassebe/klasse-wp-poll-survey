<?php

namespace includes;

require_once 'kwps_post_type.php';
require_once 'question.php';
require_once 'answer_option.php';

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

    public static function get_html($id){
	    $version = Version::get_as_array($id);
        $view_count = (int) $version['_kwps_view_count'];
        $view_count++;
        $version['_kwps_view_count'] = $view_count;

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

	    ob_start();


        if( $version['post_status'] == 'locked' || !$allowed_to_fill_out_test) {
            ?>
            <div class="kwps-version">
                <?php if(!empty($data['intro_result'])): ?>
                    <div class="kwps-page kwps-intro-result">
                        <div class="kwps-content">
                            <?php echo $data['intro_result']['post_content']; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php
        }
        elseif( $version['post_status'] == 'draft' && !current_user_can('edit_posts') ) {
            ?>
            <div class="kwps-version">You need to be logged in to view this version</div>
        <?php
        } else {
?>
        <div class="kwps-version">
            <?php if(!empty($data['intro'])): ?>
                <div class="kwps-page kwps-intro">
                    <div class="kwps-content">
                        <?php echo $data['intro']['post_content']; ?>
                    </div>
                    <div class="kwps-button">
                        <button class="kwps-next">Next</button>
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
                        <button class="kwps-next">Next</button>
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
                            $pattern = '/\[.*\]/';
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
            <input type="hidden" class="admin-url" value="<?php echo admin_url(); ?>">
        </div>
<?php
        }
		return ob_get_clean();
    }
}