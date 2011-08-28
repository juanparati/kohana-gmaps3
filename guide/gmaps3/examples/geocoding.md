# Geocoding
	
	$map = Gmaps3::instance();
		
	// Get geographic information from address
	$info = $map->get_from_address('Finisterre, Spain');
		
	echo "<pre><code>";
	print_r($info);						
	echo "</code></pre>";


				
