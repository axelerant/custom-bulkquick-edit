<?php
/**
 * Aihrus Framework
 * Copyright (C) 2015 Axelerant
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */


if ( class_exists( 'Aihrus_Widget' ) ) {
	return;
}


abstract class Aihrus_Widget extends WP_Widget {
	public static $default = array(
		'choices' => array(), // key => value
		'class' => null, // warning, etc.
		'desc' => null,
		'id' => null,
		'std' => null, // default key or value
		'suggest' => false, // attempt for auto-suggest on inputs
		'title' => null,
		'type' => 'text', // textarea, checkbox, radio, select, hidden, heading, password, expand_begin, expand_end
		'validate' => null, // required, term, slug, slugs, ids, order, single paramater PHP functions
		'widget' => 1,
	);

	public static $settings_saved = false;
	public static $suggest_id     = 0;


	public function __construct( $classname, $description, $id_base, $title ) {
		// Widget settings
		$widget_ops = array(
			'classname' => $classname,
			'description' => $description,
		);

		// Widget control settings
		$control_ops = array(
			'id_base' => $id_base,
		);

		// Create the widget
		parent::__construct(
			static::ID,
			$title,
			$widget_ops,
			$control_ops
		);
	}


	public function widget( $args, $instance ) {
		$args = wp_parse_args( $args, static::get_defaults() );
		extract( $args );

		// Before widget (defined by themes)
		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], null );
		if ( ! empty( $instance['title_link'] ) ) {
			$target = ! empty( $instance['target'] ) ? $instance['target'] : null;
			$title  = Aihrus_Common::create_link( $instance['title_link'], $title, $target );
		}

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$content = static::get_content( $instance, $this->number );
		echo $content;

		// After widget (defined by themes)
		echo $args['after_widget'];
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = static::validate_settings( $new_instance );

		return $instance;
	}


	public static function validate_settings( $input, $options = null, $do_errors = false ) {
		$null_options = false;
		if ( is_null( $options ) ) {
			$null_options = true;

			$defaults = static::get_defaults();
			$options  = static::form_parts();

			if ( is_admin() ) {
				if ( ! empty( $input['reset_defaults'] ) ) {
					foreach ( $defaults as $id => $std ) {
						$input[ $id ] = $std;
					}

					unset( $input['reset_defaults'] );

					$input['resetted'] = true;
				}
			}
		}

		return Aihrus_Settings::do_validate_settings( $input, $options, $do_errors );
	}


	public function form( $instance ) {
		$instance = static::form_instance( $instance );
		$defaults = static::get_defaults();
		$instance = wp_parse_args( $instance, $defaults );

		$form_parts = static::form_parts( $instance, $this->number );
		foreach ( $form_parts as $key => $part ) {
			$part['id'] = $key;
			$this->display_setting( $part, $instance );
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function display_setting( $args = array(), $options ) {
		extract( $args );

		if ( empty( $widget ) ) {
			echo '<div style="display:none;">';
		}

		$do_return = false;
		switch ( $type ) {
			case 'heading':
				if ( ! empty( $desc ) ) {
					echo '<h3>' . $desc . '</h3>';
				}

				$do_return = true;
				break;

			case 'expand_all':
				if ( ! empty( $desc ) ) {
					echo '<h3>' . $desc . '</h3>';
				}

				echo '<a id="' . $this->get_field_id( $id ) . '-expand" style="cursor:pointer;" onclick="jQuery( \'.af-collapsible-control\' ).slideToggle(); jQuery( \'.af-collapsible\' ).slideToggle(); jQuery( this ).hide(); jQuery( \'#' . $this->get_field_id( $id ) . '-collapse\' ).show();">&raquo; ' . esc_html__( 'Expand All Options' ) . '</a>';
				echo '<a id="' . $this->get_field_id( $id ) . '-collapse" style="cursor:pointer; display: none;" onclick="jQuery( \'.af-collapsible-control\' ).slideToggle(); jQuery( \'.af-collapsible\' ).slideToggle(); jQuery( this ).hide(); jQuery( \'#' . $this->get_field_id( $id ) . '-expand\' ).show();">&laquo; ' . esc_html__( 'Collapse All Options' ) . '</a>';

				$do_return = true;
				break;

			case 'expand_begin':
				if ( ! empty( $desc ) ) {
					echo '<h3>' . $desc . '</h3>';
				}

				echo '<span class="af-collapsible-control">';
				echo '<a id="' . $this->get_field_id( $id ) . '-expand" style="cursor:pointer;" onclick="jQuery( \'div#' . $this->get_field_id( $id ) . '\' ).slideToggle(); jQuery( this ).hide(); jQuery( \'#' . $this->get_field_id( $id ) . '-collapse\' ).show();">&raquo; ' . esc_html__( 'Expand' ) . '</a>';
				echo '<a id="' . $this->get_field_id( $id ) . '-collapse" style="cursor:pointer; display: none;" onclick="jQuery( \'div#' . $this->get_field_id( $id ) . '\' ).slideToggle(); jQuery( this ).hide(); jQuery( \'#' . $this->get_field_id( $id ) . '-expand\' ).show();">&laquo; ' . esc_html__( 'Collapse' ) . '</a>';
				echo '</span>';
				echo '<div id="' . $this->get_field_id( $id ) . '" style="display:none" class="af-collapsible">';

				$do_return = true;
				break;

			case 'expand_end':
				echo '</div>';

				$do_return = true;
				break;

			default:
				break;
		}

		if ( $do_return ) {
			if ( empty( $widget ) ) {
				echo '</div>';
			}

			return;
		}

		if ( ! isset( $options[ $id ] ) && 'checkbox' != $type ) {
			$options[ $id ] = $std;
		} elseif ( ! isset( $options[ $id ] ) ) {
			$options[ $id ] = 0;
		}

		$field_class = '';
		if ( ! empty( $class ) ) {
			$field_class = ' ' . $class;
		}

		echo '<p>';

		switch ( $type ) {
			case 'checkbox':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="1" ' . checked( $options[ $id ], 1, false ) . ' /> ';

				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				break;

			case 'select':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				echo '<select id="' . $this->get_field_id( $id ) . '"class="select' . $field_class . '" name="' . $this->get_field_name( $id ) . '">';

				foreach ( $choices as $value => $label ) {
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[ $id ], $value, false ) . '>' . $label . '</option>';
				}

				echo '</select>';
				break;

			case 'radio':
				$i             = 0;
				$count_options = count( $options ) - 1;

				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="' . $this->get_field_name( $id ) . '" id="' . $this->get_field_name( $id . $i ) . '" value="' . esc_attr( $value ) . '" ' . checked( $options[ $id ], $value, false ) . '> <label for="' . $this->get_field_name( $id . $i ) . '">' . $label . '</label>';
					if ( $i < $count_options ) {
						echo '<br />';
					}

					$i++;
				}

				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				break;

			case 'textarea':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				if ( function_exists( 'format_for_editor' ) ) {
					echo '<textarea class="widefat' . $field_class . '" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" placeholder="' . $std . '" rows="5" cols="30">' . format_for_editor( $options[ $id ] ) . '</textarea>';
				} else {
					echo '<textarea class="widefat' . $field_class . '" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[ $id ] ) . '</textarea>';
				}
				break;

			case 'password':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				echo '<input class="widefat' . $field_class . '" type="password" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="' . esc_attr( $options[ $id ] ) . '" />';
				break;

			case 'readonly':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				echo '<input class="widefat' . $field_class . '" type="text" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="' . esc_attr( $options[ $id ] ) . '" readonly="readonly" />';
				break;

			case 'text':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				$suggest_id = 'suggest_' . self::$suggest_id++;
				echo '<input class="widefat' . $field_class . ' ' . $suggest_id . '" type="text" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" placeholder="' . $std . '" value="' . esc_attr( $options[ $id ] ) . '" />';

				if ( $suggest ) {
					echo static::get_suggest( $id, $suggest_id );
				}
				break;

			default:
				break;
		}

		if ( ! empty( $desc ) ) {
			echo '<br /><span class="setting-description"><small>' . $desc . '</small></span>';
		}

		echo '</p>';

		if ( empty( $widget ) ) {
			echo '</div>';
		}
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function get_suggest( $id, $suggest_id ) {
		return;
	}


	public static function form_instance( $instance ) {
		if ( empty( $instance ) ) {
			$instance = static::get_defaults();
		} elseif ( ! empty( $instance['resetted'] ) ) {
			$instance = static::get_defaults();
		}

		return $instance;
	}


	public static function get_defaults() {
		$defaults = array();
		$options  = static::form_parts();
		foreach ( $options as $option => $value ) {
			$defaults[ $option ] = $value['std'];
		}

		return $defaults;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function form_parts( $instance = null, $number = null ) {
		$form_parts = array(
			'title' => array(
				'title' => esc_html__( 'Title' ),
				'validate' => 'wp_kses_post',
			),
			'title_link' => array(
				'title' => esc_html__( 'Title Link' ),
				'desc' => esc_html__( 'URL, path, or post ID to link widget title to. Ex: http://example.com/stuff, /testimonials, or 123' ),
				'validate' => 'wp_kses_data',
			),
		);

		return $form_parts;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function get_content( $instance = null, $widget_number = null ) {
		return;
	}
}


?>
