# Marks

	$map = Gmaps3::instance();
		
	/* Generate a simple mark */
	// We can use chainable methods
	$map->add_mark(56.177668, 10.103302)// Coordinates	
		->set('view_shadow', TRUE)		// View icons shadows
		->center(FALSE);				// Center map relative to this mark but without autofit zoom
		
	
	
	/* Generate a draggable yellow mark */
	$map->add_mark(56.005628, 10.02305, 'My draggable mark', TRUE, 'http://maps.google.com/mapfiles/ms/micons/yellow-dot.png');
	
	$template = View::factory('gmaps')
				->set('external_scripts', $map->get_apilink())
				->set('internal_scripts', $map->get_map('map_container'))
				->render();	
							
	$this->response->body($template);
			
