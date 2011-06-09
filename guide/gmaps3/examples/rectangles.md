# Rectangles
	
	$map = Gmaps3::instance();
		
	$map->set('default_lat', 46.164614)
		->set('default_lon', 24.082031)
		->set('default_zoom', 6)
		->set('default_type', 'HYBRID');	// Set to Hybrid map view				
			
	// Transilvania 
	$map->add_rectangle(47.343136, // Begin Lat
						20.994141, // Begin Lon
						45.15115,  // End Lat
						25.208374, // End Lon
						'#000000', // Stroke color
						1,		   // Stroke weight
						0,		   // Stroke opacity (0 = Hidden)
						0.35,	   // Fill opacity
						'#FF00FF'  // Fill color
						);
	
				
	$template = View::factory('gmaps')
					->set('external_scripts', $map->get_apilink())
					->set('internal_scripts', $map->get_map('map_container'))
					->render();	
							
	$this->response->body($template);				
			
