<?php
/**
 * TSO blog follow widget to display various icons to be used to follow/subscribe
 * to the blog via Feedly, Feedburner, RSS, email, etc.
 *
 * @since 3.9
 *
 * @package TSO\Widgets
 */
class TSO_Blog_Follow_Widget extends WP_Widget {

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
			'classname'   => 'tso-blog-follow-wrapper',
			'description' => __( 'Displays icons to be used to follow/subscribe to the blog.', 'genesis' ),
		);

		$control_ops = array(
			'id_base' => 'tso-blog-follow',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'tso-blog-follow', __( 'Sapphire Owl - Blog Follow Widget', 'genesis' ), $widget_ops, $control_ops );

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


        // TODO: Hardcode subscription links for now that are not included in general settings, unless I can find a way to update the php db with the various sharing links
        $subscription_links = array(
            "blog_RSS" => get_bloginfo('rss2_url'),
            "blog_feedly" => "http://www.feedly.com/home#subscription/feed/" . get_bloginfo('url') . "/feed/",
            "blog_bloglovin" => "http://www.bloglovin.com/en/blog/12218307",
            "blog_feedburner" => "http://feeds.feedburner.com/SapphireOwl"
        );

        $text .= '<ul>';

        foreach ($subscription_links as $key => $value) {
            if ( !empty($value) ) {
                $icon_name = str_replace("blog_", "", $key);
                $alt_title_msg = "Follow via " . $icon_name;

                $text .= '<li><a href="' . $value . '" target="_blank" title="' . $alt_title_msg . '"><img src="' . $GLOBALS['tso']['social_icon_map'][$icon_name] . '" alt="' . $alt_title_msg . '" /></a></li>';
            }
        }

        // Add subscribe by email button with special logic.
        $text .= '<li><a href="#"><img src="' . $GLOBALS['tso']['social_icon_map']['email'] . '" alt="Subscribe via Email" /></a></li>';

        $text .= '</ul>';


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

		<?php

	}

}