<?php
/*
	Copyright 2013 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( class_exists( 'Aihrus_Widget' ) )
	return;

require_once 'interface-aihrus-widget.php';


abstract class Aihrus_Widget extends WP_Widget implements Aihrus_Widget_Interface {
	public static $suggest_id = 0;


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
		$this->WP_Widget(
			static::ID,
			$title,
			$widget_ops,
			$control_ops
		);
	}


	public function widget( $args, $instance ) {
		global $before_widget, $before_title, $after_title, $after_widget;

		$args = wp_parse_args( $args, static::get_defaults() );
		extract( $args );

		// Our variables from the widget settings
		$title   = apply_filters( 'widget_title', $instance['title'], null );
		$content = static::get_content( $instance, $this->number );

		// Before widget (defined by themes)
		echo $before_widget;

		if ( ! empty( $instance['title_link'] ) ) {
			$target = ! empty( $instance['target'] ) ? $instance['target'] : null;
			$title  = Aihrus_Common::create_link( $instance['title_link'], $title, $target );
		}

		// Display the widget title if one was input (before and after defined by themes)
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		// Display Widget
		echo $content;

		// After widget (defined by themes)
		echo $after_widget;
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


	public function form( $instance ) {
		$instance = static::form_instance( $instance );
		$defaults = static::get_defaults();
		$instance = wp_parse_args( $instance, $defaults );

		$form_parts = static::form_parts( $instance, $this->number );
		foreach ( $form_parts as $key => $part ) {
			$part[ 'id' ] = $key;
			$this->display_setting( $part, $instance );
		}
	}


	public static function widget_options( $options ) {
		foreach ( $options as $id => $parts ) {
			// remove non-widget parts
			if ( empty( $parts['widget'] ) )
				unset( $options[ $id ] );
		}

		return $options;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function display_setting( $args = array(), $options ) {
		extract( $args );

		$do_return = false;
		switch ( $type ) {
			case 'heading':
				if ( ! empty( $desc ) )
					echo '<h3>' . $desc . '</h3>';

				$do_return = true;
				break;

			case 'expand_all':
				if ( ! empty( $desc ) )
					echo '<h3>' . $desc . '</h3>';

				echo '<a id="' . $this->get_field_id( $id ) . '" style="cursor:pointer;" onclick="jQuery( \'.tw-collapsible-control\' ) . slideToggle(); jQuery( \'.tw-collapsible\' ) . slideToggle();">' . esc_html__( 'Expand/Collapse All Options' ) . ' &raquo;</a>';

				$do_return = true;
				break;

			case 'expand_begin':
				if ( ! empty( $desc ) )
					echo '<h3>' . $desc . '</h3>';

				echo '<a id="' . $this->get_field_id( $id ) . '" style="cursor:pointer;" onclick="jQuery( \'div#' . $this->get_field_id( $id ) . '\' ) . slideToggle();" class="tw-collapsible-control">' . esc_html__( 'Expand/Collapse' ) . ' &raquo;</a>';
				echo '<div id="' . $this->get_field_id( $id ) . '" style="display:none" class="tw-collapsible">';

				$do_return = true;
				break;

			case 'expand_end':
				echo '</div>';

				$do_return = true;
				break;

			default:
				break;
		}

		if ( $do_return )
			return;

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$field_class = '';
		if ( ! empty( $class ) )
			$field_class = ' ' . $class;

		echo '<p>';

		switch ( $type ) {
			case 'checkbox':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="1" ' . checked( $options[$id], 1, false ) . ' /> ';

				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				break;

			case 'select':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				echo '<select id="' . $this->get_field_id( $id ) . '"class="select' . $field_class . '" name="' . $this->get_field_name( $id ) . '">';

				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

				echo '</select>';
				break;

			case 'radio':
				$i             = 0;
				$count_options = count( $options ) - 1;

				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="' . $this->get_field_name( $id ) . '" id="' . $this->get_field_name( $id . $i ) . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $this->get_field_name( $id . $i ) . '">' . $label . '</label>';
					if ( $i < $count_options )
						echo '<br />';
					$i++;
				}

				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';
				break;

			case 'textarea':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				echo '<textarea class="widefat' . $field_class . '" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				break;

			case 'password':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				echo '<input class="widefat' . $field_class . '" type="password" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="' . esc_attr( $options[$id] ) . '" />';
				break;

			case 'readonly':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				echo '<input class="widefat' . $field_class . '" type="text" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" value="' . esc_attr( $options[$id] ) . '" readonly="readonly" />';
				break;

			case 'text':
				echo '<label for="' . $this->get_field_id( $id ) . '">' . $title . '</label>';

				$suggest_id = 'suggest_' . self::$suggest_id++;
				echo '<input class="widefat' . $field_class . ' ' . $suggest_id . '" type="text" id="' . $this->get_field_id( $id ) . '" name="' . $this->get_field_name( $id ) . '" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $suggest )
					echo static::get_suggest( $id, $suggest_id );
				break;

			default:
				break;
		}

		if ( ! empty( $desc ) )
			echo '<br /><span class="setting-description"><small>' . $desc . '</small></span>';

		echo '</p>';
	}


}


?>
