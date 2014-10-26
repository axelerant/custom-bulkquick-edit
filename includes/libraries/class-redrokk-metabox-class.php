<?php
/**
 *
 *
 * @Author Anonymous
 * @link http://www.redrokk.com
 * @Package Wordpress
 * @SubPackage RedRokk Library
 *
 * @version 2.0
 */


//security
defined( 'ABSPATH' ) or die( 'You\'re not supposed to be here.' );

/**
 *
 *
 * @author Anonymous
 * @example
 * $gallery = redrokk_metabox_class::getInstance('gallery');
 */
if ( !class_exists( 'redrokk_metabox_class' ) ):
	class redrokk_metabox_class {

	/**
	 * HTML 'id' attribute of the edit screen section
	 *
	 * @var string
	 */
	var $_id;

	/**
	 * Save the form fields here that will be displayed to the user
	 *
	 * @var array
	 */
	var $_fields;

	/**
	 * Title of the edit screen section, visible to user
	 * Default: None
	 *
	 * @var string
	 */
	var $title;

	/**
	 * Function that prints out the HTML for the edit screen section. Pass
	 * function name as a string. Within a class, you can instead pass an
	 * array to call one of the class's methods. See the second example under
	 * Example below.
	 * Default: None
	 *
	 * @var callback
	 */
	var $callback = null;

	/**
	 * The part of the page where the edit screen section should be shown
	 * ('normal', 'advanced', or 'side'). (Note that 'side' doesn't exist before 2.7)
	 * Default: 'advanced'
	 *
	 * @var string
	 */
	var $context = 'normal';

	/**
	 * The priority within the context where the boxes should show
	 * ('high', 'core', 'default' or 'low')
	 * Default: 'default'
	 *
	 * @var string
	 */
	var $priority = 'default';

	/**
	 * Arguments to pass into your callback function. The callback will receive the
	 * $post object and whatever parameters are passed through this variable.
	 * Default: null
	 *
	 * @var array
	 */
	var $callback_args;

	/**
	 * Prebuilt metaboxes can be activated by using this type
	 * Default: default
	 *
	 * (options:)
	 * default
	 * images
	 *
	 * @var string
	 */
	var $_type;

	/**
	 *
	 *
	 * @var unknown_type
	 */
	var $_category_name;

	/**
	 * The type of Write screen on which to show the edit screen section
	 * ('post', 'page', 'link', or 'custom_post_type' where custom_post_type
	 * is the custom post type slug)
	 * Default: None
	 *
	 * @var array
	 */
	var $_object_types = array();

	/**
	 * Whether or not to make the fields available as wp-options
	 *
	 * @var bool
	 */
	var $_isAdminPage = false;


	/**
	 * Constructor.
	 *
	 */
	function __construct( $options = array() ) {
		//initializing
		$this->setProperties( $options );
		$this->setOptionHooks();

		if ( !$this->callback ) {
			$this->callback = array( $this, 'show' );
		}
		if ( !$this->title ) {
			$this->title = ucfirst( $this->_id );
		}

		//registering this metabox
		add_action( 'add_meta_boxes', array( $this, '_register' ) );

		// backwards compatible (before WP 3.0)
		// add_action( 'admin_init', array($this, '_register'), 1 );

		add_action( 'save_post', array( $this, '_save' ) );
		add_filter( 'wp_redirect', array( $this, '_redirectIntervention' ), 40, 1 );
	}


	/**
	 * Method properly inturprets the given parameter and sets it accordingly
	 *
	 * @param string|object $value
	 */
	function setObjectTypes( $value ) {
		if ( is_a( $value, 'redrokk_post_class' ) ) {
			$value = $value->_post_type;
		}
		if ( is_a( $value, 'redrokk_admin_class' ) ) {
			$value = $value->id;
			$this->_isAdminPage = $value;
		}
		if ( is_array( $value ) ) {
			foreach ( $value as $v )
				$this->_object_types[] = $v;
			return $this;
		}

		$this->_object_types[] = $value;
		return $this;
	}


	/**
	 * Method is designed to return the currently visible post type
	 */
	function getCurrentPostType() {
		$post_type = false;
		if ( isset( $_REQUEST['post_type'] ) ) {
			$post_type = $_REQUEST['post_type'];
		}
		elseif ( isset( $_REQUEST['post'] ) ) {
			$post = get_post( $_REQUEST['post'] );
			$post_type = $post->post_type;
		}
		elseif ( isset( $_REQUEST['page'] ) ) {
			$post_type = $_REQUEST['page'];
		}

		return $post_type;
	}


	/**
	 * Method properly prepares the metabox type by binding the necessary hooks
	 *
	 * @param mixed   $value
	 */
	function settype( $value = 'default' ) {
		$this->_type = $value;

		switch ( $this->_type ) {
		default:
		case 'default':
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderForm' ), 20, 1 );
			add_action( 'metabox-save-'.$this->_id, array( $this, 'saveAsPostMeta' ), 10, 2 );
			break;
		case 'image':
		case 'images':
			$this->_fields = array(
				array(
					'name' => 'New Image',
					'type' => 'title',
				),
				array(
					'name' => 'Image Title',
					'id' => $this->_id.'_post_title',
					'type' => 'text',
				),
				array(
					'name' => 'Description',
					'id' => $this->_id.'_post_content',
					'type' => 'textarea',
				),
				array(
					'name' => 'Image File',
					'id' => $this->_id.'_image',
					'type' => 'image',
				),
				array(
					'name' => 'Save Image',
					'type' => 'submit',
				),
			);
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderListImageAttachments' ), 20, 1 );
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderForm' ), 20, 1 );
			add_action( 'metabox-save-'.$this->_id, array( $this, 'saveAsAttachment' ), 1, 2 );
			break;
		case 'video':
		case 'videos':
			$this->_fields = array(
				array(
					'name' => 'New Video',
					'type' => 'title',
				),
				array(
					'name' => 'Video Title',
					'id' => $this->_id.'_post_title',
					'type' => 'text',
				),
				array(
					'name' => 'Description',
					'id' => $this->_id.'_post_content',
					'type' => 'textarea',
				),
				array(
					'name' => 'Video File',
					'id' => $this->_id.'_image',
					'type' => 'image',
				),
				array(
					'name' => 'Video Link',
					'id' => $this->_id.'_link',
					'type' => 'text',
				),
				array(
					'name' => '_videocat',
					'id' => $this->_id.'_videocat',
					'std'=> $this->getCategory(),
					'type' => 'hidden',
				),
				array(
					'name' => '_metaid',
					'id' => $this->_id.'_metaid',
					'type' => 'hidden',
				),
				array(
					'name' => 'Save Video',
					'type' => 'submit',
				),
			);
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderListAttachments' ), 20, 1 );
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderListVideoAttachments' ), 20, 1 );
			add_action( 'metabox-show-'.$this->_id, array( $this, '_renderForm' ), 20, 1 );
			add_action( 'metabox-save-'.$this->_id, array( $this, 'saveAsPostMeta' ), 1, 2 );
			break;
		}
	}


	/**
	 * Returns the category to use
	 */
	function getCategory() {
		return isset( $this->_category_name )
			? $this->_category_name
			: '_videocat';
	}


	/**
	 * Method will save the posted content as an image attachment
	 *
	 */
	function saveAsAttachment( $source, $post_id ) {
		if ( empty( $_FILES ) || !isset( $_REQUEST[$this->_id.'files'] ) ) return $source;

		// initializing
		$property = $_REQUEST[$this->_id.'files'];
		$post_data = array();

		if ( isset( $source[$this->_id.'_post_title'] ) && $source[$this->_id.'_post_title'] ) {
			$post_data['post_title'] = $source[$this->_id.'_post_title'];
		}

		if ( isset( $source[$this->_id.'_post_content'] ) && $source[$this->_id.'_post_content'] ) {
			$post_data['post_content'] = $source[$this->_id.'_post_content'];
		}

		$id = media_handle_upload( $property, $post_id, $post_data );
		$source[$property] = $id;

		$type = 'post';
		if ( $this->getCurrentPostType() ) {
			$type = $this->getCurrentPostType();
		}

		//saving the attachment ID to the taxonomy
		if ( !in_array( $type, get_post_types( array( 'public' => false ) ) ) ) {
			$old = get_metadata( $type, $post_id, $property, true );
			if ( $id && $id != $old ) {
				wp_delete_attachment( $old, true );
				update_metadata( $type, $post_id, $property, $id );
			}
		}

		foreach ( (array)$source as $property => $new ) {
			//skip everything but the specially prefixed
			if ( strpos( $property, $this->_id ) !== 0 ) continue;
			if ( in_array( $property, array(
						$this->_id.'_post_title',
						$this->_id.'_post_content',
					) ) ) continue;

			$old = get_metadata( $type, $id, $property, true );
			if ( $new && $new != $old ) {
				update_metadata( $type, $id, $property, $new );
			}
			elseif ( !$new ) {
				delete_metadata( $type, $id, $property, $old );
			}
		}

		return $source;
	}


	/**
	 * Method saves the data provided as post meta values
	 *
	 * @param array   $source
	 * @param integer $post_id
	 */
	function saveAsPostMeta( $source, $post_id ) {
		$type = 'post';
		if ( !$this->getCurrentPostType() ) {
			$type = $this->_table;
		}

		//save as a file
		//if there's no FILES then we save as a meta
		$source = $this->saveAsAttachment( $source, $post_id );

		//get the ID of this meta set
		$id = false;
		if ( isset( $source[$this->_id.'_metaid'] ) && $source[$this->_id.'_metaid'] ) {
			$id = $source[$this->_id.'_metaid'];
		}

		// if this is a built in metabox
		if ( $this->_type != 'default'
			&& ( !isset( $source[$this->_id.'_image'] ) || !$source[$this->_id.'_image'] ) )
			return false;

		// Saving only the specially prefixed items
		foreach ( (array)$source as $property => $new ) {
			//skip everything but the specially prefixed
			if ( strpos( $property, $this->_id ) !== 0 ) continue;

			//each meta set has it's own ID
			$property = str_replace( $this->_id, $this->_category_name.'_'.$id, $property );

			$old = get_metadata( $type, $post_id, $property, true );
			if ( $new && $new != $old ) {
				update_metadata( $type, $post_id, $property, $new );
			}
			elseif ( !$new ) {
				delete_metadata( $type, $post_id, $property, $old );
			}
		}

		// maybe there's a last id
		if ( !$id ) {
			if ( !$id = get_metadata( $type, $post_id, '_metaidlast', true ) ) {
				$id = 0;
			}
			$id++;
			update_metadata( $type, $post_id, '_metaidlast', $id );
		}

		// saving all of the standard items
		foreach ( (array)$source as $property => $new ) {
			//skip special properties that are prefixed with the id
			if ( strpos( $property, $this->_id ) === 0 ) continue;

			$old = get_metadata( $type, $post_id, $property, true );
			update_metadata( $type, $post_id, $property, $new );

			//  if ($new && $new != $old) {
			//   update_metadata($type, $post_id, $property, $new);
			//  }
			//  elseif (!$new) {
			//   delete_metadata($type, $post_id, $property, $old);
			//  }

		}

		return true;
	}


	/**
	 * Do something with the data entered
	 *
	 * @param integer $post_id
	 */
	function _save( $post_id ) {
		//initializing
		$post = get_post( $post_id );

		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( !isset( $_REQUEST[ get_class().$this->_id ] ) )
			return;

		if ( !wp_verify_nonce( $_REQUEST[ get_class().$this->_id ], plugin_basename( __FILE__ ) ) )
			return;

		// this metabox is to be displayed for a certain object type only
		if ( !in_array( $post->post_type, $this->_object_types ) )
			return;

		// Check permissions
		if ( 'page' == $post->post_type ) {
			if ( !current_user_can( 'edit_page', $post->ID ) )
				return;
		}
		else {
			if ( !current_user_can( 'edit_post', $post->ID ) )
				return;
		}

		//saving the request data
		if ( !$this->_type ) $this->setType();
		do_action( 'metabox-save-'.$this->_id, $this->getRequestPostMetas(), $post->ID, $this );
		return true;
	}


	/**
	 * Method returns the post meta
	 *
	 */
	function getRequestPostMetas() {
		$ignores = array( 'post_title', 'post_name', 'post_content', 'post_excerpt', 'post',
			'post_status', 'post_type', 'post_author', 'ping_status', 'post_parent', 'message',
			'post_category', 'comment_status', 'menu_order', 'to_ping', 'pinged', 'post_password',
			'guid', 'post_content_filtered', 'import_id', 'post_date', 'post_date_gmt', 'tags_input',
			'action' );

		$fields = array();
		foreach ( (array)$this->_fields as $field ) {
			if ( !array_key_exists( 'id', $field ) ) continue;
			$fields[] = $field['id'];
		}

		$requests = $_REQUEST;
		foreach ( (array)$requests as $k => $request ) {
			if ( ( !empty( $fields ) && !in_array( $k, $fields ) )
				|| ( in_array( $k, $ignores ) || strpos( $k, 'nounce' ) !== false ) ) {
				unset( $requests[$k] );
			}
		}

		return apply_filters( 'metabox-requests-'.$this->_id, $requests );
	}


	/**
	 * Display the inner contents of the metabox
	 *
	 * @param object  $post
	 */
	function show( $post ) {
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), get_class().$this->_id );
		do_action( 'metabox-show-'.$this->_id, $this->_fields, $this );
	}


	/**
	 * Method displays a list of attached images
	 *
	 */
	function _renderListImageAttachments() {
		global $post, $current_screen;
		$images = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image" );

		// no images to render
		if ( empty( $images ) ) {
			?><p>No images have been saved.</p><?php

			// rendering the images
		} else {

?>
		<table class="wp-list-table  form-table widefat" style="border:none;">
			<?php foreach ( (array)$images as $post_id => $image ): ?>
			<?php $image_attributes = wp_get_attachment_image_src( $image->ID, 'thumbnail' ); ?>
			<tbody id="the-list">
			<tr>
				<th scope="row" style="width: 140px">
					<div style="padding:10px;background:whiteSmoke;">
						<img src="<?php echo wp_get_attachment_thumb_url( $image->ID ); ?>" /></div>
				</th>
				<td>
					<b><?php echo $image->post_title; ?></b>
					<p><?php echo get_the_content( $image->ID ); ?></p>

					<div class="row-actions">
						<span class="inline">
							<a href="<?php echo wp_nonce_url(
				"media.php?attachment_id=$image->ID"
				."&action=edit&_redirect="
				.urlencode( $this->_currentPageURL() )
			); ?>">
							Edit</a> |
						</span>
						<span class="trash">
							<a 	class="submitdelete"
								onclick="return showNotice.warn();"
								href="<?php echo wp_nonce_url(
				"post.php?action=delete&_redirect="
				.urlencode( $this->_currentPageURL() )
				."&amp;post=$image->ID",
				'delete-attachment_' . $image->ID ); ?>">
							Delete Permanently</a> |
						</span>
						<span class="inline">
							<a 	target="_blank"
								href="<?php echo get_attachment_link( $image->ID ); ?>">
							View</a>
						</span>
					</div>
					</td>
			</tr>
			</tbody>
			<?php endforeach; ?>
		</table>
			<?php
		}
		return;
	}


	/**
	 * Return a clean list of meta listings created by this system
	 *
	 * @param string  $category
	 * $param object $post
	 */
	public static function getMetaListings( $category, $post = null ) {
		// initializing
		if ( $post === NULL ) {
			global $post;
		}
		$custom = get_post_custom( $post->ID );
		$return = array();

		//looping all values to build our return array
		foreach ( (array)$custom as $property => $value ) {
			$parts = explode( '_', $property );
			if ( !isset( $parts[0] ) || !isset( $parts[1] ) || !isset( $parts[2] ) ) continue;
			if ( $parts[0] != $category ) continue;

			$pro = str_replace( $parts[0].'_'.$parts[1].'_', '', $property );
			$return[$parts[1]][$pro] = $value;
		}

		return $return;
	}


	/**
	 * Function removes a specific category meta
	 *
	 * @param string  $category
	 * $param string $meta_id
	 * $param object $post
	 */
	public static function deleteMetaListing( $category, $meta_id, $post = null ) {
		// initializing
		if ( $post === NULL ) {
			global $post;
		}
		$listings = redrokk_metabox_class::getMetaListings( $category, $post );
		if ( !isset( $listings[$meta_id] ) ) return false;

		$type = 'post';

		foreach ( (array)$listings[$meta_id] as $property => $value ) {
			$pro = $category.'_'.$meta_id.'_'.$property;
			delete_metadata( $type, $post->ID, $pro, $value[0] );
		}
		return true;
	}


	/**
	 * Method displays a list of meta attachments
	 *
	 */
	function _renderListAttachments() {
		global $post;

		//delete action prior to pulling new listings
		if ( isset( $_REQUEST['redrokkdelete'] ) && $_REQUEST['redrokkdelete'] ) {
			redrokk_metabox_class::deleteMetaListing( $this->_category_name, $_REQUEST['redrokkdelete'], $post );
		}

		//pull new listings
		$metaListings = redrokk_metabox_class::getMetaListings( $this->_category_name, $post );

		if ( !empty( $metaListings ) ) {
?>
		<table class="wp-list-table form-table widefat" style="border:none;">
			<tbody id="the-list">
			<?php foreach ( (array)$metaListings as $meta_id => $video ): ?>
			<?php $video = apply_filters( 'redrokk_metabox_class::_renderListAttachments', $video, $meta_id );?>

			<tr id="<?php echo $this->_category_name; ?>_<?php echo $meta_id; ?>">
				<th scope="row" style="width: 140px">
					<div style="padding:10px;background:whiteSmoke;">
						<?php if ( isset( $video['link'] ) ) echo apply_filters( 'the_content', $video['link'][0] ); ?>
					</div>
				</th>
				<td>
					<b><?php if ( isset( $video['post_title'] ) ) echo $video['post_title'][0]; ?></b>
					<p><?php if ( isset( $video['post_content'] ) ) echo $video['post_content'][0]; ?></p>

					<div class="row-actions">
						<span class="inline">
							<a href="#" id="edit_<?php echo $this->_category_name; ?>_<?php echo $meta_id; ?>">
							Edit</a> |
						</span>
						<span class="trash">
							<a class="submitdelete"
								onclick="return showNotice.warn();"
								href="<?php echo site_url( "wp-admin/post.php?post={$post->ID}"
				."&action=edit"
				."&redrokkdelete=$meta_id"
			); ?>">
							Delete Permanently</a>
						</span>
					</div>
<script>
jQuery('#edit_<?php echo $this->_category_name; ?>_<?php echo $meta_id; ?>').click(function(){
	var data = {
	<?php
			$data = array();

			//making sure all fields will be cleared
			foreach ( (array)$this->_fields as $field ) {
				if ( !isset( $field['id'] ) || !isset( $field['type'] ) ) continue;
				if ( !in_array( $field['type'], array( 'text', 'file', 'image', 'textarea', 'hidden' ) ) )
					continue;

				$id = str_replace( $this->_id.'_', '', $field['id'] );
				$data[$id] = "'$id':''";
			}

			//adding our values to the array
			foreach ( (array)$video as $vp => $vv ) {
				if ( isset( $vv[0] ) ) $vv = $vv[0];
				$data[$vp] = "'$vp':'$vv'";
			}

			//adding the meta ID to the array
			$data[$id] = "'metaid':'$meta_id'";

			echo implode( ',', $data );
?>
	};

	jQuery.each(data, function(key, value){
		jQuery('#<?php echo $this->_id; ?>_'+key).val( value );
	});
	return false;
});
</script>
				</td>
				<?php do_action( 'redrokk_metabox_class::_renderListAttachments::rows', $video, $meta_id, $this ); ?>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		}

	}


	/**
	 * Method displays a list of attached videos
	 *
	 */
	function _renderListVideoAttachments() {
		global $post;

		//pull new listings
		$videos = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=video/mp4" );

		// no images to render
		if ( !empty( $videos ) ) {
?>
		<table class="wp-list-table form-table widefat" style="border:none;">
			<tbody id="the-list">
			<?php foreach ( (array)$videos as $post_id => $video ): ?>
			<?php $image_attributes = wp_get_attachment_link( $video->ID ); ?>
			<tr>
				<th scope="row" style="width: 140px">
					<div style="padding:10px;background:whiteSmoke;">
						<?php echo $image_attributes; ?>
					</div>
				</th>
				<td>
					<b><?php echo $video->post_title; ?></b>
					<p><?php echo get_the_content( $video->ID ); ?></p>

					<div class="row-actions">
						<span class="inline">
							<a href="<?php echo wp_nonce_url(
				"media.php?attachment_id=$meta_id"
				."&action=edit&_redirect="
				.urlencode( $this->_currentPageURL() )
			); ?>">
							Edit</a> |
						</span>
						<span class="trash">
							<a class="submitdelete"
								onclick="return showNotice.warn();"
								href="<?php echo wp_nonce_url(
				"post.php?action=delete&_redirect="
				.urlencode( $this->_currentPageURL() )
				."&amp;post=$video->ID",
				'delete-attachment_' . $video->ID ); ?>">
							Delete Permanently</a>
						</span>
					</div>
					</td>
			</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		}
	}


	/**
	 * Method renders the form from any source
	 *
	 * @param array   $fields
	 */
	function _renderForm( $fields = array() ) {
		//initializing
		global $post;
		$defaults = array(
			'name' => '',
			'desc' => '',
			'id' => '',
			'type' => 'text',
			'options' => array(),
			'std' => '',
			'value' => '',
			'class' => '',
			'multiple' => '',
			'args' => array(
				'hide_empty' => 0,
				'name' => 'element_name',
				'hierarchical' => true
			),
			'attributes' => ''
		);

		// no fields to render
		if ( empty( $fields ) ) {
?>
		<p>No form fields have been defined. Use <pre>
		$metabox->set('_fields', array(
			array(
				'name' => 'Title',
				'type' => 'title',
			),
			array(
				'name' => 'Title',
				'desc' => '',
				'id' => 'title',
				'type' => 'text',
				'std' => ''
			),
			array(
				'name' => 'image',
				'desc' => '',
				'id' => 'imagefile',
				'type' => 'image',
				'std' => ''
			),
			array(
				'name' => 'Textarea',
				'desc' => 'Enter big text here',
				'id' => 'textarea_id',
				'type' => 'textarea',
				'std' => 'Default value 2'
			),
			array(
				'name' => 'Select box',
				'id' => 'select_id',
				'type' => 'select',
				'options'=> array(
					'value1' => 'Value 1',
					'value2' => 'Value 2',
					'value3' => 'Value 3',
					'value4' => 'Value 4',
				)
			),
			array(
				'name' => 'Radio',
				'id' => 'radio_id',
				'type' => 'radio',
				'value' => 'test',
				'desc' => 'Check this box if you want its value saved',
			),
			array(
				'name' => '',
				'id' => 'radio_id',
				'type' => 'radio',
				'value' => 'test2',
				'desc' => 'Check this box if you want its value saved',
			),
			array(
				'name' => 'Checkbox',
				'id' => 'checkbox_id',
				'type' => 'checkbox',
				'desc' => 'Check this box if you want its value saved',
			),
		));</pre>
		</p>
			<?php

			// rendering the fields
		} else {
?>
		<table class="form-table">
			<?php
			// do_action("{$this->_class}_before");
			$custom = get_post_custom( $this->_id );

			foreach ( (array)$fields as $field ):
				$field = wp_parse_args( $field, $defaults );
			$field['args'] = wp_parse_args( $field['args'], $defaults['args'] );

			extract( $field );
			$field['args']['name'] = $element_name = $id;

			// grabbing the meta value
			if ( array_key_exists( $id, $custom ) ) {
				if ( isset( $custom[$id][0] ) )
					$meta = esc_attr( $custom[$id][0] );
				else
					$meta = esc_attr( $custom[$id] );
			} else {
				$meta = $std;
			}

			$id = sanitize_title( $id );

			if ( array_key_exists( 'deleteattachment', $_GET )
				&& $id == $_GET['fileproperty']
				&& $meta == $_GET['deleteattachment'] ) {
				wp_delete_attachment( $_GET['deleteattachment'], $force_delete = true );
				update_post_meta( $post->ID, $id, '' );
			}
?>
			<?php switch ( $type ) { default: ?>
			<?php if ( is_callable( $type ) && function_exists( $type ) ) : ?>
			<?php if ( ! isset( $custom[ $id ] ) ) $custom[ $id ] = null; ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php call_user_func( $type, $args, $field, $custom[ $id ] ); ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; endif; ?>
			<?php case 'text': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<input  <?php echo $attributes ?>
						id="<?php echo $id; ?>"
						value="<?php echo $meta; ?>"
						type="<?php echo $type; ?>"
						name="<?php echo $id; ?>"
						class="text large-text <?php echo $class; ?>" />
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'submit': ?>
			<?php case 'button': ?>
			<tr>
				<td colspan="2">
					<input  <?php echo $attributes ?>
						id="<?php echo $id; ?>"
						value="<?php echo $name; ?>"
						type="submit"
						name="submit"
						class="button-primary <?php echo $class; ?>" />
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'file': ?>
			<?php case 'image': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php if ( $meta && wp_get_attachment_url( $meta ) ): ?>
						<?php echo wp_get_attachment_image( $meta ); ?>
						<span><a href="<?php echo add_query_arg( 'fileproperty', $id, add_query_arg( 'deleteattachment', $meta, $this->_currentPageURL() ) ); ?>">Delete Image</a></span>
					<?php else: ?>
						<input type="hidden" name="<?php echo $this->_id; ?>files" value="<?php echo $id; ?>" />
						<!-- first hidden input forces this item to be submitted when it is not checked -->
						<input  <?php echo $attributes ?>
							id="<?php echo $id; ?>"
							type="file"
							name="<?php echo $id; ?>"
							onChange="jQuery(this).closest('form').attr('enctype', 'multipart/form-data');"
							class="<?php echo $class; ?>" />
					<?php endif; ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'title': ?>
			<tr>
				<th colspan="2" scope="row">
					<h3  <?php echo $attributes ?> style="border: 1px solid #ddd;
						padding: 10px;
						background: #eee;
						border-radius: 2px;
						color: #666;
						margin: 0;"><?php echo $name; ?>
					</h3>
				</th>
			</tr>
			<?php break; ?>
			<?php case 'checkbox': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<input type="hidden" name="<?php echo $id; ?>" value="" />
					<!-- first hidden input forces this item to be submitted when it is not checked -->

					<?php foreach ( (array)$options as $_value => $_name ): ?>
							<input value="<?php echo $_value; ?>" type="checkbox" <?php echo $attributes ?>
								name="<?php echo $element_name; ?>" id="<?php echo $id; ?>"
								<?php echo $meta == $_value? 'checked="checked"' :''; ?>
								class="<?php echo $class; ?>" />
							<?php echo $_name; ?>
					<?php endforeach; ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'radio': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php foreach ( (array)$options as $_value => $_name ): ?>
						<input name="<?php echo $element_name; ?>"  id="<?php echo $id; ?>"
							value="<?php echo $_value; ?>" type="<?php echo $type; ?>"
							<?php echo $meta == $_value?'checked="checked"' :''; ?>
							 <?php echo $attributes ?> class="<?php echo $class; ?>" />
							<?php echo $_name; ?>
					<?php endforeach; ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'textarea': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<textarea  <?php echo $attributes ?>
						id="<?php echo $id; ?>"
						name="<?php echo $id; ?>"
						class="large-text <?php echo $class; ?>"
						><?php echo $meta; ?></textarea>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'wpeditor': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php wp_editor( $meta, $id, $settings = array() ); ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'select_capabilities': ?>
				<?php $options = $type=='select_capabilities' ?$this->get_options_capabilities()+$options :$options; ?>

			<?php case 'select_roles': ?>
				<?php $options = $type=='select_roles' ?$this->get_options_roles()+$options :$options; ?>

			<?php case 'select_menu': ?>
				<?php $options = $type=='select_menu' ?$this->get_options_menus()+$options :$options; ?>

			<?php case 'select_pages': ?>
				<?php $options = $type=='select_pages' ?$this->get_options_pages()+$options :$options; ?>

			<?php case 'select_users': ?>
				<?php $options = $type=='select_users' ?$this->get_options_users()+$options :$options; ?>

			<?php case 'select_categories': ?>
			<?php case 'select': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php if ( $type == 'select_categories' ): ?>
						<?php wp_dropdown_categories( $args ); ?>

					<?php else: ?>

					<select  <?php echo $attributes ?>
						id="<?php echo $id; ?>"
						name="<?php echo $id; ?>"
						class="<?php echo $class; ?>"
						<?php echo $multiple ?"MULTIPLE SIZE='$multiple'" :''; ?>
						><?php foreach ( (array)$options as $_value => $_name ): ?>

						<option
							value="<?php echo $_value; ?>"
							<?php echo $meta == $_value ?' selected="selected"' :''; ?>
							><?php echo $_name; ?></option>

					<?php endforeach; ?></select>
					<?php endif; ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'hidden': ?>
			<tr>
				<td colspan="2">
					<input  <?php echo $attributes ?>
						id="<?php echo $id; ?>"
						value="<?php echo $meta ?$meta :$std; ?>"
						type="<?php echo $type; ?>"
						name="<?php echo $id; ?>"
						style="visibility:hidden;" />
				</td>
			</tr>
			<?php break; ?>
			<?php case 'custom': ?>
			<tr>
				<td colspan="2">
					<?php echo $desc.$std; ?>
				</td>
			</tr>
			<?php case 'date': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php echo mysql2date( get_option( 'date_format' ), $meta ); ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'time': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php echo mysql2date( get_option( 'time_format' ), $meta ); ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php case 'datetime': ?>
			<tr>
				<th scope="row" style="width: 140px">
					<label for="<?php echo $id; ?>"><?php echo $name; ?></label>
				</th>
				<td>
					<?php echo mysql2date( get_option( 'date_format' ), $meta ); ?>
					<?php echo mysql2date( get_option( 'time_format' ), $meta ); ?>
					<span class="description"><?php echo $desc; ?></span>
				</td>
			</tr>
			<?php break; ?>
			<?php } ?>
			<?php endforeach; ?>
		</table>
		<?php
		}
		return $this;
	}


	/**
	 * Returns an options list of menus
	 */
	function get_options_pages() {
		// initializing
		$options = array( '0'=>' -- ' );
		$pages = get_pages( array( 'post_type' => 'page', 'post_status' => 'publish' ) );

		foreach ( $pages as $page ) {
			$options[$page->ID] = $page->post_title;
		}

		return $options;
	}


	/**
	 * Returns an options list of menus
	 */
	function get_options_menus() {
		// initializing
		$options = array( '0'=>' -- ' );
		$menus = get_terms( 'nav_menu', array(
				'hide_empty' => 0
			) );

		foreach ( $menus as $menu ) {
			$options[$menu->slug] = $menu->name;
		}

		return $options;
	}


	/**
	 * Returns an options list of users
	 */
	function get_options_users() {
		// initializing
		global $wpdb;

		$options = array( '0'=>' -- ' );
		$query = $wpdb->prepare( "SELECT $wpdb->users.ID, $wpdb->users.display_name FROM $wpdb->users", null );
		$results = $wpdb->get_results( $query );

		foreach ( (array)$results as $result ) {
			$options[$result->ID] = $result->display_name;
		}

		return $options;
	}


	/**
	 * Returns an options list of capabilities
	 */
	function get_options_capabilities() {
		// initializing
		global $wpdb;

		$options = array();
		$roles = get_option( $wpdb->prefix . 'user_roles' );

		foreach ( (array)$roles as $role ) {
			if ( !isset( $role['capabilities'] ) ) continue;
			foreach ( (array)$role['capabilities'] as $cap => $v ) {
				$options[$role['name']."::$cap"] = $role['name']."::$cap";
			}
		}

		return $options;
	}


	/**
	 * Returns an options list of roles
	 */
	function get_options_roles() {
		// initializing
		global $wpdb;

		$options = array(
			'read' => 'Public'
		);
		$roles = get_option( $wpdb->prefix . 'user_roles' );

		foreach ( (array)$roles as $role ) {
			$options[strtolower( $role['name'] )] = $role['name'];
		}

		return $options;
	}


	/**
	 * Adds a box to the main column on the Post and Page edit screens
	 *
	 */
	function _register() {
		// this metabox is to be displayed for a certain object type only
		if ( !empty( $this->_object_types ) && !in_array( $this->getCurrentPostType(), $this->_object_types ) )
			return;

		if ( !$this->callback_args ) {
			$this->callback_args = $this;
		}

		// if the user has not already set the type of this metabox,
		// then we need to do that now
		if ( !$this->_type ) {
			$this->setType();
		}

		add_meta_box(
			$this->_id,
			$this->title,
			$this->callback,
			$this->getCurrentPostType(),
			$this->context,
			$this->priority,
			$this->callback_args
		);
	}


	/**
	 * Method set's the hooks for the options creted by this metabox
	 *
	 */
	function setOptionHooks() {
		foreach ( (array)$this->_fields as $field ) {
			if ( !isset( $field['id'] ) ) continue;

			//creating the callback for the admin page
			$function = create_function( '$default', '
				return redrokk_admin_class::getInstance("'.$this->_isAdminPage.'")
					->getOption("'.$field['id'].'", $default, true);
			' );
			add_filter( "pre_option_{$field['id']}", $function, 20, 2 );
		}
	}


	/**
	 * Method redirects the user if we have added a request redirect
	 * in the url
	 *
	 * @param string  $location
	 */
	function _redirectIntervention( $location ) {
		if ( isset( $_GET['_redirect'] ) ) {
			$location = urldecode( $_GET['_redirect'] );
		}
		return $location;
	}


	/**
	 * Get the current page url
	 */
	function _currentPageURL() {
		$pageURL = 'http';
		if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) {$pageURL .= "s";}
		$pageURL .= "://";
		if ( $_SERVER["SERVER_PORT"] != "80" ) {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}


	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param mixed   $src    An associative array or object to bind to the JTable instance.
	 * @param mixed   $ignore An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link http://docs.joomla.org/JTable/bind
	 * @since   11.1
	 */
	public function bind( $src, $ignore = array() ) {
		// If the source value is not an array or object return false.
		if ( !is_object( $src ) && !is_array( $src ) ) {
			trigger_error( 'Bind failed as the provided source is not an array.' );
			return false;
		}

		// If the source value is an object, get its accessible properties.
		if ( is_object( $src ) ) {
			$src = get_object_vars( $src );
		}

		// If the ignore value is a string, explode it over spaces.
		if ( !is_array( $ignore ) ) {
			$ignore = explode( ' ', $ignore );
		}

		// Bind the source value, excluding the ignored fields.
		foreach ( $this->getProperties() as $k => $v ) {
			// Only process fields not in the ignore array.
			if ( !in_array( $k, $ignore ) ) {
				if ( isset( $src[$k] ) ) {
					$this->$k = $src[$k];
				}
			}
		}

		return true;
	}


	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param mixed   $properties Either an associative array or another object.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 *
	 * @see  set()
	 */
	public function setProperties( $properties ) {
		if ( is_array( $properties ) || is_object( $properties ) ) {
			foreach ( (array) $properties as $k => $v ) {
				// Use the set function which might be overridden.
				$this->set( $k, $v );
			}
			return true;
		}

		return false;
	}


	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param string  $property The name of the property.
	 * @param mixed   $value    The value of the property to set.
	 *
	 * @return  mixed  Previous value of the property.
	 *
	 * @since   11.1
	 */
	public function set( $property, $value = null ) {
		$_property = 'set'.str_replace( ' ', '', ucwords( str_replace( '_', ' ', $property ) ) );
		if ( method_exists( $this, $_property ) ) {
			return $this->$_property( $value );
		}

		$previous = isset( $this->$property ) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}


	/**
	 * Returns an associative array of object properties.
	 *
	 * @param boolean $public If true, returns only the public properties.
	 *
	 * @return  array
	 *
	 * @see  get()
	 */
	public function getProperties( $public = true ) {
		$vars = get_object_vars( $this );
		if ( $public ) {
			foreach ( $vars as $key => $value ) {
				if ( '_' == substr( $key, 0, 1 ) ) {
					unset( $vars[$key] );
				}
			}
		}

		return $vars;
	}


	/**
	 * contains the current instance of this class
	 *
	 * @var object
	 */
	static $_instances = null;

	/**
	 * Method is called when we need to instantiate this class
	 *
	 * @param array   $options
	 */
	public static function getInstance( $_id, $options = array() ) {
		if ( !isset( self::$_instances[$_id] ) ) {
			$options['_id'] = $_id;
			$class = get_class();
			self::$_instances[$_id] = new $class( $options );
		}
		return self::$_instances[$_id];
	}


}


endif;
