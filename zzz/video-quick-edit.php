<?php
/**
 *  Quick Edit and Bulk Edit helper for Media Burn video records
 *
 *  @author Michael Cannon <mc@aihr.us>
 *  @ref http://rachelcarden.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/
 */

add_action( 'bulk_edit_custom_box', 'quick_edit_custom_box_video', 10, 2 );
add_action( 'quick_edit_custom_box', 'quick_edit_custom_box_video', 10, 2 );

function quick_edit_custom_box_video( $column_name, $post_type ) {
	$slug = 'video';
	if ( $slug !== $post_type )
		return;

	if ( ! in_array( $column_name, array( 'additional_copies', 'main_credits' ) ) )
		return;

	static $printNonce = true;
	if ( $printNonce ) {
		$printNonce = false;
		wp_nonce_field( plugin_basename( __FILE__ ), 'video_edit_nonce' );
	}

?>
    <fieldset class="inline-edit-col-right inline-edit-video">
      <div class="inline-edit-col inline-edit-<?php echo $column_name ?>">
		<label class="inline-edit-group">
        <?php
	switch ( $column_name ) {
	case 'additional_copies':
?>
			<span class="title">Additional Copies</span>
			<textarea cols="22" rows="1" name="additional_copies" autocomplete="off"></textarea>
			<?php
		break;
	case 'main_credits':
?>
			<span class="title">Main Credits</span>
			<textarea cols="22" rows="1" name="main_credits" autocomplete="off"></textarea>
			<?php
		break;
	}
?>
		</label>
      </div>
    </fieldset>
    <?php
}


add_action( 'save_post', 'save_video_meta' );

function save_video_meta( $post_id ) {
	// TODO make $slug static
	$slug = 'video';
	if ( $slug !== $_POST['post_type'] )
		return;

	if ( !current_user_can( 'edit_post', $post_id ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( isset( $post->post_type ) && 'revision' == $post->post_type )
		return;

	$_POST += array( "{$slug}_edit_nonce" => '' );
	if ( !wp_verify_nonce( $_POST["{$slug}_edit_nonce"], plugin_basename( __FILE__ ) ) )
		return;

	if ( isset( $_REQUEST['additional_copies'] ) )
		update_post_meta( $post_id, 'additional_copies', wp_kses_post( $_REQUEST['additional_copies'] ) );

	if ( isset( $_REQUEST['main_credits'] ) )
		update_post_meta( $post_id, 'main_credits', wp_kses_post( $_REQUEST['main_credits'] ) );
}


add_action( 'admin_print_scripts-edit.php', 'admin_edit_video_foot' );
function admin_edit_video_foot() {
	$slug = 'video';
	// load only when editing a video
	if ( ( isset( $_GET['page'] ) && $slug == $_GET['page'] )
		|| ( isset( $_GET['post_type'] ) && $slug == $_GET['post_type'] ) ) {
		wp_enqueue_script( 'admin-quick-edit-video', get_template_directory_uri() . '/functions/user/custom/fitv/quick_edit.js', array( 'jquery', 'inline-edit-post' ), '', true );
	}
}


add_action( 'wp_ajax_save_bulk_edit_video', 'save_bulk_edit_video' );
function save_bulk_edit_video() {
	$post_ids          = ( ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
	$additional_copies = ( ! empty( $_POST[ 'additional_copies' ] ) ) ? wp_kses_post( $_POST[ 'additional_copies' ] ) : null;
	$main_credits      = ( ! empty( $_POST[ 'main_credits' ] ) ) ? wp_kses_post( $_POST[ 'main_credits' ] ) : null;

	if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
		foreach ( $post_ids as $post_id ) {
			if ( ! empty( $additional_copies ) )
				update_post_meta( $post_id, 'additional_copies', $additional_copies );

			if ( ! empty( $main_credits ) )
				update_post_meta( $post_id, 'main_credits', $main_credits );
		}
	}

	die();
}


?>
