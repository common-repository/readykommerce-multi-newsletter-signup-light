<?php

/**
 * Newsletter Signup widget
 * @package widget
 * @author readyKommerce
 * @see http://www.readykommerce.com
 * @since 1.0
 **/

class Nl_Lg_signup_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'nl_lg_signup_widget', // Base ID
			__('NL Signup', 'nl_signup'), // Name
			array( 'description' => __( 'Add subscriber to MailChimp and Aweber', 'nl_signup' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$default_value = Array(
			'mc_api' => '',
            'form_title' => 'Subscribe our newsletter',
            'form_desc' => 'We will not sell your email address',
            'mc_list_id' => '',
            'form_name' => 'hide_name',
            'form_email_type' => 'text'
	        );

		$options = wp_parse_args( get_option( 'nl_option_name' ), $default_value );

		$title = apply_filters( 'widget_title', $instance['mc_title'] );
		$desc = apply_filters( 'widget_desc', $instance['mc_desc'] );
		$mc_name = apply_filters( 'widget_mc_name', $instance['mc_name'] );
		$email_type = apply_filters( 'widget_email_type', $instance['mc_email_type'] );
		$list_id = apply_filters( 'widget_list_id', $instance['mc_list_id'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . __($title, 'nl_signup') . $args['after_title'];

		if ($desc!='') {
			echo '<p class="nl_desc">' . __($desc, 'nl_signup') . '</p>';
		}

		echo '<form action="" class="nl_subscribe_form" method="post">';

		echo '<div class="nl_msg_box"></div>';
		// if first name and last name is selected
		if ($mc_name == 'first_last_name') {
			// first name
			echo '<div class="input_row first_name">';
				echo '<label>'.__('First Name: ', 'nl_signup').': </label>';
				echo '<input name="fname" type="text" class="nl_input_item nl_f_name">';
			echo '</div>';
			// last name
			echo '<div class="input_row last_name">';
				echo '<label>'.__('Last Name: ', 'nl_signup').': </label>';
				echo '<input name="lname" type="text" class="nl_input_item nl_l_name">';
			echo '</div>';
		}

		// email
		echo '<div class="input_row email_add">';
			echo '<label>'.__('Email Address: ', 'nl_signup').': </label>';
			echo '<input name="email" type="text" class="nl_input_item nl_email" required>';
		echo '</div>';
		
		// hidden inputs
		echo '<input name="nl_mc_email_type" type="hidden" value="' .$email_type. '">';
		echo '<input name="nl_mc_list_id" type="hidden" value="' .$list_id. '">';
		echo '<input id="nl_from_widget" name="nl_from_widget" type="hidden" value="true">';

		// submit
		echo '<input type="submit" class="nl_submit nl_input_item" value="'.__('Subscribe', 'nl_signup').'">';

		echo '</form>';

		// echo __( 'Hello, World!', 'nl_signup' );
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$options = get_option( 'nl_option_name' );
		$default = array(
				'mc_title' => $options['form_title'],
				'mc_desc' => $options['form_desc'],
				'mc_name' => $options['form_name'],
				'mc_email_type' => $options['form_email_type'],
				'mc_list_id' => $options['mc_list_id']
			);
		extract( shortcode_atts($default, $instance) );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_title' ); ?>"><?php _e( 'Title:', 'nl_signup' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'mc_title' ); ?>" name="<?php echo $this->get_field_name( 'mc_title' ); ?>" type="text" value="<?php echo esc_attr( $mc_title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_desc' ); ?>"><?php _e( 'Description:', 'nl_signup' ); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'mc_desc' ); ?>" name="<?php echo $this->get_field_name( 'mc_desc' ); ?>" rows="3"><?php echo esc_attr( $mc_desc ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_name' ); ?>"><?php _e( 'Name Option:', 'nl_signup' ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'mc_name' ); ?>" id="<?php echo $this->get_field_id( 'mc_name' ); ?>">
				<option value="first_last_name" <?php selected( $mc_name, 'first_last_name' ) ?>><?php _e( 'First &amp; Last Name', 'nl_signup' ); ?></option>
				<option value="no_name" <?php selected( $mc_name, 'no_name' ) ?>><?php _e( 'No Name', 'nl_signup' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_email_type' ); ?>"><?php _e( 'Email Type:', 'nl_signup'  ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'mc_email_type' ); ?>" id="<?php echo $this->get_field_id( 'mc_email_type' ); ?>">
				<option value="html" <?php selected( $mc_email_type, 'html' ) ?>><?php _e( 'HTML', 'nl_signup' ); ?></option>
				<option value="text" <?php selected( $mc_email_type, 'text' ) ?>><?php _e( 'Plain Text', 'nl_signup' ); ?></option>
			</select>
		</p>
		<!-- <p>
			<label for="<?php echo $this->get_field_id( 'mc_email_type' ); ?>"><?php _e( 'Email Type:', 'nl_signup'  ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'mc_email_type' ); ?>" id="<?php echo $this->get_field_id( 'mc_email_type' ); ?>">
				<option value="html" <?php selected( $mc_email_type, 'no_video' ) ?>><?php _e( 'No Video', 'nl_signup' ); ?></option>
				<option value="html" <?php selected( $mc_email_type, 'before_form' ) ?>><?php _e( 'Before Form', 'nl_signup' ); ?></option>
				<option value="text" <?php selected( $mc_email_type, 'after_form' ) ?>><?php _e( 'After Form', 'nl_signup' ); ?></option>
			</select>
		</p> -->
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_list_id' ); ?>"><?php _e( 'List Unique ID:', 'nl_signup' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'mc_list_id' ); ?>" name="<?php echo $this->get_field_name( 'mc_list_id' ); ?>" type="text" value="<?php echo esc_attr( $mc_list_id ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['mc_title'] = ( ! empty( $new_instance['mc_title'] ) ) ? strip_tags( $new_instance['mc_title'] ) : '';
		$instance['mc_desc'] = ( ! empty( $new_instance['mc_desc'] ) ) ? strip_tags( $new_instance['mc_desc'] ) : '';
		$instance['mc_name'] = ( ! empty( $new_instance['mc_name'] ) ) ? strip_tags( $new_instance['mc_name'] ) : '';
		$instance['mc_email_type'] = ( ! empty( $new_instance['mc_email_type'] ) ) ? strip_tags( $new_instance['mc_email_type'] ) : '';
		$instance['mc_list_id'] = ( ! empty( $new_instance['mc_list_id'] ) ) ? strip_tags( $new_instance['mc_list_id'] ) : '';

		return $instance;
	}

} // END Nl_signup_Widget class 


// add widget to wp
function nl_lg_signup_widget(){
     register_widget( 'Nl_Lg_signup_Widget' );
}

add_action( 'widgets_init', 'nl_lg_signup_widget');