<?php
/*
Plugin Name: Linear Tag Cloud
Plugin URI: http://www.orangedropdesign.com/
Description: A tag clob with bars and not dimensions.
Author: Andrea Rufo
Version: 0.1
Author URI: http://www.orangedropdesign.com/

*/

class ltc_widget extends WP_Widget{
	
	public function __construct() {
		parent::WP_Widget( 'ltc', 'Linear Tag Cloud', array('description' => 'Una tag cloud con le barre'));
	}
	
	public function form( $instance ){
        /* Impostazioni di default del widget */
		$defaults = array( 
            'title' => 'Tag Cloud',
			'number' => 10,
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
					
					echo '<li style="width:'.$lenght.'%; background:'.$instance['background'].'; border-color:'.$instance['border'].'">
						<a style="color:'.$instance['color'].'" href="'.home_url().'/?tag='.$array['slug'].'">
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
	
	//aggiorna i dati
	public function update( $new_instance, $old_instance ){
		
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['border'] = strip_tags( $new_instance['border'] );
		$instance['background'] = strip_tags( $new_instance['background'] );
		$instance['color'] = strip_tags( $new_instance['color'] );

		return $instance;
	}                     
}

//includo lo stile del widget
add_action( 'wp_enqueue_scripts', 'ltc_add_my_stylesheet' );

function ltc_add_my_stylesheet() {
	wp_register_style( 'ltc-style', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'ltc-style' );
}

// Custom get_tags function to support ignoring tags with less than $minnum posts.
function get_all_tags($max) {
	
	$args = array(
		 'orderby'	=>	'count', 
		 'order'	=>	'DESC',	  
		 'number'	=>	$max
	);
	
	extract($args);
	$alltags = get_terms('post_tag', $args);

	$tags = array();

	foreach ($alltags as $tag) {
		//if ($tag->count < $minnum || $tag->count > $maxnum)
			//continue;
			
		array_push($tags, $tag);
	}

	if (empty($tags)) {
		$return = array();
		return $return;
	}

	$tags = apply_filters('get_tags', $tags, $args);
	return $tags;
}

// registro il widget
function ltc_register_widgets(){
	register_widget( 'ltc_widget' );
}

add_action( 'widgets_init', 'ltc_register_widgets' );

?>