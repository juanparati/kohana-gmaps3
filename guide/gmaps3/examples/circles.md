# Circles
	
	$map = Gmaps3::instance();
		
	$map->set('default_lat', 48.014689)
		->set('default_lon', 1.828888)
		->set('default_zoom', 4);				
			
	// Uh la la
	$map->add_circle(48.859068,	 // Lat 
					  2.346954,	 // Lon
					  200000, 	 // Radius
					  '#2080D0', // Stroke color
					  2, 		 // Stroke weight
					  1, 		 // Stroke opacity
					  0.5, 		 // Fill opacity
					  '#C0E0FF'  // Fill color
					);	 
	
	// The end of the earth
	$map->add_circle(42.877788, -9.265423, 50000, '#00FF00');
	
	
	$template = View::factory('gmaps')
				->set('external_scripts', $map->get_apilink())
				->set('internal_scripts', $map->get_map('map_container'))
				->render();	
							
	$this->response->body($template);
			
