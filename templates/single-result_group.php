<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header();
global $post;
$result_group = \kwps_classes\Result_Group::get_as_array( $post->ID );
$versions = \kwps_classes\Version::get_all_by_post_parent( $post->post_parent );
?>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<h3><?php echo $post->post_title; ?></h3>
            <div class="kwps-grouping-urls">
                <?php foreach( $versions as $version ) : ?>
                    <?php
                        $params = array(
                            'version' => $version['ID'],
                            '_kwps_group' => $result_group['_kwps_hash'],
                        );
                        $url = add_query_arg( $params, $result_group['_kwps_referer'] );
                    ?>
                    <a href="<?php echo $url;?>"><?php echo $version['post_title'] ?></a>
                <?php endforeach; ?>
            </div>
            <div class="kwps-result-url">
                <?php
                    $params = array(
                        'test_collection' => $post->post_parent,
                        '_kwps_result_hash' => $result_group['_kwps_result_hash'],
                    );
                    $url = add_query_arg( $params, $result_group['_kwps_referer'] );
                ?>
                <a href="<?php echo $url; ?>"><?php echo __('Results');?></a>
            </div>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
