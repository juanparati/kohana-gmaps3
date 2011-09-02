# Polylines
	
	$map = Gmaps3::instance();
		
	// Set default lat, lon and zoom
	$map->set('default_lat', 48.014689)
			->set('default_lon', 1.828888)
			->set('default_zoom', 4);				
	
	// Define a polyline group 
	$map->add_polyline_group('Finisterre2Aarhus',	// Group name 
							 '#FF0000',				// Stroke color
							 3						// Stroke weight
							 );						// ... Fill opacity, Fill color	
													
	// Add coordinates
	$map->add_coord('Finisterre2Aarhus', 42.907115, -9.261206);
	$map->add_coord('Finisterre2Aarhus', 43.29976, -8.374586);
	$map->add_coord('Finisterre2Aarhus', 40.417678, -3.694153);
	$map->add_coord('Finisterre2Aarhus', 56.177668, 10.176086);
	
	$template = View::factory('gmaps')
							->set('external_scripts', $map->get_apilink())
							->set('internal_scripts', $map->get_map('map_container'))
							->render();	
							
	$this->response->body($template);											
			
