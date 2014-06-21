<?php
/**
 * TSO social icon widget to display the author's social accounts. To be used in sidebar,
 * but could be used on About page, author box, etc.
 *
 * @since 3.9
 *
 * @package TSO\Widgets
 */
class TSO_Social_Icon_Widget extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	function __construct() {

        // Init the TSO global variables for use within this plugin.
        init_tso_global_vars();

        $this->defaults = array(
			'title'          => '',
			'user'           => ''
		);

		$widget_ops = array(
			'classname'   => 'tso-user-icons',
			'description' => __( 'Displays social icons for a user\'s social accounts.', 'genesis' ),
		);

		$control_ops = array(
			'id_base' => 'tso-user-icons',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'tso-user-icons', __( 'Sapphire Owl - User Social Icons', 'genesis' ), $widget_ops, $control_ops );

	}

	/**
	 * Echo the widget content.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		extract( $args );

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;

        // Widget Title
        if ( ! empty( $instance['title'] ) )
            echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;

        $text = '';

        // Get the user info.
        $userData = get_userdata( $instance['user'] );

        if ( !empty($userData) ) {
            // Get social accounts here and output icons
            $social_urls = array(
                'twitter' => get_the_author_meta( 'user_twitter' ),
                'facebook' => get_the_author_meta( 'user_facebook' ),
                'linkedin' => get_the_author_meta( 'user_linkedin' ),
                'youtube' => get_the_author_meta( 'user_youtube' ),
                'spotify' => get_the_author_meta( 'user_spotify' ),
                'googleplus' => get_the_author_meta( 'googleplus' ),
                'github' => get_the_author_meta( 'user_github' ),
                'instagram' => get_the_author_meta( 'user_instagram' ),
                'foursquare' => get_the_author_meta( 'user_foursquare' ),
                'pinterest' => get_the_author_meta( 'user_pinterest' ),
                'rss' => get_the_author_meta( 'user_rss' ),
                'bloglovin' => get_the_author_meta( 'user_bloglovin' ),
                'feedly' => get_the_author_meta( 'user_feedly' )
            );


            $text .= '<ul>';

            foreach ($social_urls as $key => $value) {
                if ( !empty($value) ) {
                    // TODO: add username to <a> title tag
                    $text .= '<li><a href="' . $value . '" target="_blank" title="' . $key . '"><div class="tso-social-icon tso-icon-' . $key . '"></div></a></li>';
                }
            }

            $text .= '</ul>';
        }

        //* Echo $text
        echo wpautop( $text );


		echo $after_widget;

	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']          = strip_tags( $new_instance['title'] );
		$new_instance['user']  = strip_tags( $new_instance['user'] );

		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'genesis' ); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
        </p>

		<p>
			<label for="<?php echo $this->get_field_name( 'user' ); ?>"><?php _e( 'Select a user to display social icons.', 'genesis' ); ?></label><br />
			<?php wp_dropdown_users( array( 'who' => 'authors', 'name' => $this->get_field_name( 'user' ), 'selected' => $instance['user'] ) ); ?>
		</p>

		<?php

	}
}