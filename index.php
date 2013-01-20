<?php
/*
Plugin Name: Linear Tag Cloud
Plugin URI: http://www.orangedropdesign.com/
Description: A simple and clean tag cloud with a list of tags in percentual lines.
Author: Andrea Rufo
Version: 1.3.1
Author URI: http://www.orangedropdesign.com/

*/

class ltc_widget extends WP_Widget{
	
	public function __construct() {
		parent::WP_Widget( 'ltc', 'Linear Tag Cloud', array('description' => 'Setup your new tag-cloud with bars.'));
	}
	
	public function form( $instance ){
        /* Impostazioni di default del widget */
		$defaults = array( 
            'title' => 'Tag Cloud',
			'number' => 6,
			'border' => '#ccc',
			'background' => '#eee',
			'color' => '#000'
        );
        
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Widget title:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">Max number of tags:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" />	
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'border' ); ?>">Border color:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" value="<?php echo $instance['border']; ?>" />	
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'background' ); ?>">Background color:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" value="<?php echo $instance['background']; ?>" />	
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'color' ); ?>">Text color:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>" value="<?php echo $instance['color']; ?>" />	
		</p>
            
		<?php
	}
	
	//stampa il widget
	public function widget( $args, $instance ){
		
		extract( $args );

		$title = apply_filters('widget_title', $instance['title'] );
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		?>
			<div class="linear-tag-cloud">

                <ul>
				<?php
				
				$tags = get_all_tags($instance['number']);
				
				//ottengo il valore massimo di count
				$primo = (array) $tags['0'];
				$max = $primo['count'];
						
				//unità di percentuale sul valore massimo di count		
				$base = 100/$max;
				
				foreach($tags as $item => $value){
					
					//trasformo in array
					$array = (array) $value;
					
					//lunghezza della barra in percentuale					
					$lenght = $base * $array['count'];
					
					echo '<li style="width:'.$lenght.'%;';
					
					if($instance['background'] != ''){
						echo ' background:'.$instance['background'].';';
					}
					
					if($instance['border'] != ''){
						echo ' border-color:'.$instance['border'];
					}
					
					echo'"><a ';
					
					if($instance['color'] != ''){
						echo 'style="color:'.$instance['color'].'" ';
					}
					
					echo 'href="'.home_url().'/?tag='.$array['slug'].'">
						'.$array['name'].' ('.$array['count'].')
						</a>
						</li>';
				}
				
				?>
                </ul>
                
			</div>
		<?php
		echo $after_widget;
	}
	
	//update the input
	public function update( $new_instance, $old_instance ){
		
		$instance = $old_instance;
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['number'] 		= strip_tags( $new_instance['number'] );
		$instance['border'] 		= strip_tags( $new_instance['border'] );
		$instance['background'] 	= strip_tags( $new_instance['background'] );
		$instance['color'] 			= strip_tags( $new_instance['color'] );

		return $instance;
	}                     
}

//add the plugin's css to the page 
add_action( 'wp_enqueue_scripts', 'ltc_add_my_stylesheet' );

function ltc_add_my_stylesheet() {
	wp_register_style( 'ltc-style', plugins_url('ltc-style.css', __FILE__) );
	wp_enqueue_style( 'ltc-style' );
}

//custom get_all_tags function to get all tags ordered by count whit $max number of elements
function get_all_tags($max) {
	
	$args = array(
		 'orderby'	=>	'count',
		 'order'	=>	'DESC',	  
		 'number'	=>	$max
	);
	
	extract($args);
	$alltags = get_terms('post_tag', $args);

	$tags = array();

	foreach ($alltags as $tag){
		array_push($tags, $tag);
	}

	if (empty($tags)) {
		$return = array();
		return $return;
	}

	$tags = apply_filters('get_tags', $tags, $args);
	return $tags;
}

// register the widget
add_action( 'widgets_init', 'ltc_register_widgets' );

function ltc_register_widgets(){
	register_widget( 'ltc_widget' );
}

?>