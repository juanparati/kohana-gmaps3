# Polygons

	$map = Gmaps3::instance();
		
	// Set default lat, lon and zoom
	$map->set('default_lat', 24.886436490787712)
		->set('default_lon', -70.2685546875)
		->set('default_zoom', 4);				
	
	// Define a polygon group
	$map->add_polygon_group('BermudasTriangle',	// Group name 
							'#FF00FF',			// Stroke color 
							3,					// Stroke weight 
							1,					// Stroke opacity (From 0.0 to 1.0)
							0.5,				// Fill opacity (From 0.0 to 1.0) 
							'#FFF000'			// Fill color
							);
	
	// Define coordinates for group BermudasTriangle
	$map->add_coord('BermudasTriangle', 25.774252, -80.190262);
	$map->add_coord('BermudasTriangle', 18.466465, -66.118292);
	$map->add_coord('BermudasTriangle', 32.321384, -64.75737);
	$map->add_coord('BermudasTriangle', 25.774252, -80.190262);
	
	// You can use add_polygon_group and add_coord as chainable methods
	$map->add_polygon_group('MyTriangle')
			->add_coord('MyTriangle', 17.999632, -78.815918)
			->add_coord('MyTriangle', 19.890723, -80.782471)
			->add_coord('MyTriangle', 17.067287, -81.095581);					
	
			
	$template = View::factory('gmaps')
							->set('external_scripts', $map->get_apilink())
							->set('internal_scripts', $map->get_map('map_container'))
							->render();	
							
	$this->response->body($template);									
			
