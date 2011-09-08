# Infowindows
	
		
	$map = Gmaps3::instance();			
 		
	// Attach infowindow for default group to this mark
	$map->add_mark(56.177668, 10.103302, 'Mark 1')
				->add_infowindow('Infowindow 1', FALSE);
		                
	// Attach auto opened infowindow for default group to this mark
	$map->add_mark(55.928433, 8.595428, 'Mark2')
				->add_infowindow('Infowindow 2', TRUE);	
		
	// Attach infowindow for custom group "MyGroup2" to this mark 
	$map->add_mark(55.663257, 13.364182, 'Mark3', FALSE,
				'http://maps.google.com/mapfiles/ms/micons/yellow-dot.png')
				->add_infowindow('Infowindow 3', FALSE, 'MyGroup2'); 
	
	// Attach infowindow for custom group "MyGroup3" to this rectangle
	$map->add_rectangle(53.608803, // Begin Lat
						10.475464, // Begin Lon
						54.660916, // End Lat
						11.412292, // End Lon
						'#2080D0', // Stroke color
						2,		   // Stroke weight
						1,		   // Stroke opacity (0 = Hidden)
						0.5,	   // Fill opacity
						'#C0E0FF'  // Fill color
						)->add_infowindow('Infowindow 4', FALSE, 'MyGroup3');
	
	// Attach auto opened infowindow for custom group "MyGroup3" to this circle
	$map->add_circle(54.367759,	// Lat
					 12.722168, // Lon
					 50000,     // Radius
					 '#2080D0', // Stroke color
					 2,         // Stroke weight
					 1,         // Stroke opacity
					 0.5,       // Fill opacity
					 '#C0E0FF'  // Fill color
					 )->add_infowindow('Infowindow 5', TRUE, 'MyGroup3');
	
	// Center and fit map position in relation with all elements
	$map->center_all();
					
	$template = View::factory('gmaps')
					->set('external_scripts', $map->get_apilink())
					->set('internal_scripts', $map->get_map('map_container'))
					->render();	
							
	$this->response->body($template);					
			
