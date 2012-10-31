<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Google Maps Module for Kohana 3.2.
 *
 * @package    Gmaps v3
 * @author     Juan Lago D. <juanparati[at]gmail[dot]com>  
 * @copyright  (c) 2011 Kohana
 * @license    http://kohanaphp.com/license.html
 * @version		 1.5
 */
 
abstract class Kohana_Gmaps3 {

	protected static $instance;
	
	private $config;
  private $id;  
  private $last_key;
				
	protected $marks			= array();
	protected $polys			= array();	
	protected $circles		= array();
	protected $rectangles	= array();
	protected $coords			= array();
	protected $infos			= array();
	protected $jscod			= array();
		    
	public $lat;
	public $lon;
	
	
        
	public static function instance()
	{
		if (!isset(Gmaps3::$instance))
		{
			// Load the configuration for this type
			$config = Kohana::$config->load('gmaps3');					
			
			// Create a new session instance
			Gmaps3::$instance = new Gmaps3($config);
		}
		
		return Gmaps3::$instance;
	}


	/**
	 * Create an instance of Gmaps3.
	 *
	 * @return  object
	 */
	public static function factory($config = array())
	{
		return new Gmaps3($config);
	}


	/**
	 * Loads configuration options.
	 *
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Save the config in the object
		$this->config = $config;
		
		// set last centered lat and lon 
		$this->lat = $config->default_lat;
		$this->lon = $config->default_lon;
		
		$this->id  = uniqid();
	}
	
	

	/**
	 * Add circle
	 *
	 * @chainable	 
	 * @param   string  Latitude    
	 * @param   string  Longitude
	 * @param		string	Radius (Default 50000)	 
	 * @param   string  Stroke color in hex html value (Default '#000000')    
	 * @param   integer Stroke line weight (Default 2)
	 * @param		float		Stroke opacity (Default 1.0)
	 * @param		string	Fill color in hex html vlaue (Default '#FF0000');
	 * @param		float		Fill opacity (Default 0)	 		          	       	 	 
	 * @return  object 
	 */
	public function add_circle($lat, $lon, $radius = 50000, $strokecolor = '#000000', $strokeweight = 2, $strokeopacity = 1, $fillopacity = 0, $fillcolor = '#FF0000')
	{
	
		$this->last_key = 'circle_'.(int)sizeof($this->circles);
		
		$this->circles[$this->last_key]['lat'] 						= $lat; 
		$this->circles[$this->last_key]['lon'] 						= $lon;
		$this->circles[$this->last_key]['radius']					= $radius;
		$this->circles[$this->last_key]['strokeColor']		= $strokecolor;
		$this->circles[$this->last_key]['strokeWeight']		= $strokeweight;
		$this->circles[$this->last_key]['strokeOpacity']	= $strokeopacity;
		$this->circles[$this->last_key]['fillOpacity']		= $fillopacity;
		$this->circles[$this->last_key]['fillColor']			= $fillcolor;                                   	 																				 																				 																							 																							 					 																  				   
    
		return $this;      
				
	}
	
	
	
	/**
	 * Add a coord point
	 *
	 * @chainable	 
	 * @param		string	Polyline group	 
	 * @param   string  Latitude    
	 * @param   string  Longitude	     	 	          	       	 	 
	 * @return  object 
	 */
	public function add_coord($group, $lat, $lon)
	{
	
		$group = Inflector::camelize($group);
		
	  $this->coords[$group][] = array('lat' => $lat,
   	                                'lon' => $lon);
		                            						                                                              
    return $this;      		    
	}
	
	

  /**
	 * Add a infowindow
	 *	 
	 * @chainable	 	 
	 * @param   string  Content (You can use HTML)	 	 	 	  	 
	 * @param   boolean Opened (Optional)
	 * @param		string	Group (Default group = 0)	 
	 * @param   integer Mark id (FALSE = Last mark)   	          	       	 	 
	 * @return  object
	 */
	public function add_infowindow($content, $opened = FALSE, $group = 0, $mark_id = FALSE)
	{
    // Get mark id
    if ($mark_id === FALSE)
    {      
      $mark_id = $this->last_key;            
      
      if (empty($mark_id))
        throw new Kohana_Exception('You need define an element.');                                  
    }
        	 	    	  
	  $group = Inflector::camelize($group);
	  
		// Add infowindow
		$this->infos[$group][] = array('mark_id' => $mark_id, 
                                   'content' => $content, 
                                   'opened'  => $opened																	 															 
																	);
                                         
    return $this;     	       
	}
	
	
	/**
	 * Add a marker
	 *
	 * @chainable	 
	 * @param   string  Latitude    
	 * @param   string  Longitude
	 * @param   string  Title (Optional)	 	 	  	 
	 * @param   boolean Draggable? (Optional)
	 * @param   url     Icon  (see http://www.visual-case.it/cgi-bin/vc/GMapsIcons.pl) (Optional)
	 * @param   url     Icon shadow (Optional)
	 * @param   array   Icon custom options (Optional)
	 * @param   array   Marker custom options (Optional)    	 	          	       	 	 
	 * @return  object 
	 */
	public function add_mark($lat, $lon, $title = NULL, $draggable = FALSE, $icon = NULL, $shadow = '', $icon_ops = array(), $marker_ops = array())
	{						      	    
		    
		$this->last_key = 'mark_'.(int)sizeof($this->marks);						 
		
		$this->marks[$this->last_key]['lat']				= $lat;
		$this->marks[$this->last_key]['lon']				= $lon;
		$this->marks[$this->last_key]['title']			= $title;
		$this->marks[$this->last_key]['draggable']	= $draggable;
		$this->marks[$this->last_key]['icon']				= $icon;
		$this->marks[$this->last_key]['shadow']			= $shadow;
		$this->marks[$this->last_key]['icon_ops']		= $icon_ops;
		$this->marks[$this->last_key]['marker_ops']	= $marker_ops;				    
    
    return $this;      
	}
	
	
	
	/**
	 * Add a polygon group
	 *
	 * @chainable	 
	 * @param		string	Polygon group	 
	 * @param   string  Stroke color in hex html value (Default '#000000')    
	 * @param   integer Stroke line weight (Default 2)
	 * @param		float		Stroke opacity (Default 1.0)
	 * @param		float		Fill opacity (Default 0)	 	
	 * @param		string	Fill color in hex html vlaue (Default '#FF0000');	  	 	     	 	          	       	 	 
	 * @return  object 
	 */
	public function add_polygon_group($group, $strokecolor = '#000000', $strokeweight = 2, $strokeopacity = 1.0, $fillopacity = 0, $fillcolor = '#FF0000')
	{
		$this->_add_poly_group('polygon', $group, $strokecolor, $strokeweight, $strokeopacity, $fillopacity, $fillcolor);																							                                 						                                    						 
															 			                                                                                    
    return $this;      
	}
	
	
	/**
	 * Add a polyline group
	 *
	 * @chainable	 
	 * @param		string	Polyline group	 
	 * @param   string  Stroke color in hex html value (Default '#000000')    
	 * @param   integer Stroke line weight (Default 2)
	 * @param		float		Stroke opacity (Default 1.0)	 
	 * @param		float		Fill opacity (Default 0)	 	
	 * @param		string	Fill color in hex html vlaue (Default '#FF0000');	  	 	     	 	          	       	 	 
	 * @return  object 
	 */
	public function add_polyline_group($group, $strokecolor = '#000000', $strokeweight = 2, $strokeopacity = 1.0, $fillopacity = 0, $fillcolor = '#FF0000')
	{
  	$this->_add_poly_group('polyline', $group, $strokecolor, $strokeweight, $strokeopacity, $fillopacity, $fillcolor);
		
		return $this;      
	}
	
	
	
	/**
	 * Add rectangle
	 *
	 * @chainable	 
	 * @param   string  Begin latitude    
	 * @param   string  Begin longitude
	 * @param   string  End latitude    
	 * @param   string  End longitude	 	 	 	 	 
	 * @param   string  Stroke color in hex html value (Default '#000000')    
	 * @param   integer Stroke line weight (Default 2)
	 * @param		float		Stroke opacity (Default 1.0)
	 * @param		string	Fill color in hex html vlaue (Default '#FF0000');
	 * @param		float		Fill opacity (Default 0)	 		          	       	 	 
	 * @return  object 
	 */
	public function add_rectangle($lat, $lon, $elat, $elon, $strokecolor = '#000000', $strokeweight = 2, $strokeopacity = 1, $fillopacity = 0, $fillcolor = '#FF0000')
	{
	
		$this->last_key = 'rectangle_'.(int)sizeof($this->rectangles);
		
		$this->rectangles[$this->last_key]['lat']						= $lat;
		$this->rectangles[$this->last_key]['lon']						= $lon;
		$this->rectangles[$this->last_key]['elat']					= $elat;
		$this->rectangles[$this->last_key]['elon']					= $elon;
		$this->rectangles[$this->last_key]['strokeColor']		= $strokecolor;
		$this->rectangles[$this->last_key]['strokeWeight']	= $strokeweight;
		$this->rectangles[$this->last_key]['strokeOpacity']	= $strokeopacity;
		$this->rectangles[$this->last_key]['fillOpacity']		= $fillopacity;
		$this->rectangles[$this->last_key]['fillColor']			= $fillcolor;
		    
		return $this;      
					
	}
		
  
  /**
	 * Center the map position in relation with an element
	 *	       	 	 
	 * @chainable
	 * @param		boolean  Autofit zoom	 
	 * @param   mixed    Mark id (FALSE = Center relative to the last element)   	 	 
	 */
  public function center($autofit = TRUE, $mark_id = FALSE)
	{
    if ($mark_id === FALSE)
    {
      $mark_id = $this->last_key;     
      
      if (empty($mark_id))
        throw new Kohana_Exception('You need define an element.');    
    }   
		
		$type = $this->_detect_type($mark_id).'s'; 
		
		// Calculate center
		switch ($type)
		{
			case 'polygons':							
			case 'polylines':
																          
				// Calculate poly center
				$polygroup = explode('_', $mark_id);
				
				// Get poly bounds
				$bounds = $this->get_bounds(array('marks', 'circles', 'rectangles'), $polygroup[1]);
				
				// Calculate center
				$center = $this->_calculate_center($bounds['lat_max'], 
																					 $bounds['lat_min'],
																					 $bounds['lon_max'],
																					 $bounds['lon_min']);  		
																					 																				 																								
			break;
			
			case 'rectangles':
																								
				// Get rectangle bounds
				$bounds = $this->get_bounds(array('marks', 'circles', 'polylines'), $mark_id);								
				
				// Calculate center
				$center = $this->_calculate_center($bounds['lat_max'], 
																					 $bounds['lat_min'],
																					 $bounds['lon_max'],
																					 $bounds['lon_min']);
																					 	 				
								
			break;
			
			default:
			
				$bounds['lat_max'] = $bounds['lat_min'] = $this->{$type}[$mark_id]['lat'];
				$bounds['lon_max'] = $bounds['lon_min'] = $this->{$type}[$mark_id]['lon'];
				
				// Calculate other elements center
				$center['lat'] = $this->{$type}[$mark_id]['lat'];
    		$center['lon'] = $this->{$type}[$mark_id]['lon'];			
		}  
		
		// Set last centered position
		$this->lat = $center['lat'];
		$this->lon = $center['lon'];    
                            
    $this->jscod['center'] = "map_{$this->id}.setCenter(new google.maps.LatLng({$center['lat']}, {$center['lon']}));\n";
    
    if ($autofit)
    {
  		$this->jscod['center'] .= "map_{$this->id}.fitBounds(
			new google.maps.LatLngBounds(
				new google.maps.LatLng({$bounds['lat_min']}, {$bounds['lon_min']}),
				new google.maps.LatLng({$bounds['lat_max']}, {$bounds['lon_max']})
			));\n";
		}
		 		
    return $this;	   
	}
	
	
	
  /**
	 * Center and fit the map position in relation with all map elements 
	 *	       	 	 
	 * @chainable
	 * @param   boolean	Autofit zoom   	 	 
	 * @param   array		Exclude elements (marks, polylines, circles or rectangles)   	 
	 */
  public function center_all($autofit = TRUE, $exclude = array())
  {  	  	
  	
  	$bounds = $this->get_bounds($exclude);				  	  	
  	
  	$center = $this->_calculate_center($bounds['lat_max'], 
																			 $bounds['lat_min'], 
																			 $bounds['lon_max'], 
																			 $bounds['lon_min']);
  	
  	
		// Set last centered position
		$this->lat = $center['lat'];
		$this->lon = $center['lon'];
		
		$this->jscod['center'] = "map_{$this->id}.setCenter(new google.maps.LatLng({$center['lat']}, {$center['lon']}));\n";
  	
  	if ($autofit)
  	{
  		$this->jscod['center'] .= "map_{$this->id}.fitBounds(
				new google.maps.LatLngBounds(
					new google.maps.LatLng({$bounds['lat_min']}, {$bounds['lon_min']}),
					new google.maps.LatLng({$bounds['lat_max']}, {$bounds['lon_max']})
				));\n";
  	}
  	
  	return $this;  	
	}		
	
	/**
	 * Get configuration configuration and options
	 *	 	 
	 * @param   string   Configuration key	 	  	 	 	    	 
	 * @return  mixed
	 */
  public function get($key)
  {
  	return Arr::path($this->config, $key); 
	}
		
	/**
	 * Get the bounds of all map elements or filtered elements 
	 *	       	 	 	 	    	 	 
	 * @param		array		Exclude elements (marks, polylines, circles or rectangles)
	 * @param		mixed		Filter by key (Optional) 	 
	 * @return	array   	 
	 */
	public function get_bounds($exclude = array(), $keys = NULL)
	{
				
		$lats = $lons = array();
		
		if ($keys != NULL && !is_array($keys))
			$keys = array($keys);
		
		// Copy marks coordinates
		if (!in_array('marks', $exclude))
		{
						
			foreach ($this->marks as $k => $mark)
			{
				if ($keys == NULL || in_array($k, $keys))
				{
					$lats[] = $mark['lat'];
					$lons[] = $mark['lon'];	
				}				  
			}		

		}
		
		// Copy polylines coordinates
		if (!in_array('polylines', $exclude))
		{	
			
			foreach ($this->coords as $k => $poly)
			{					
					if ($keys == NULL || in_array($k, $keys))
					{
						$lats = array_merge(Arr::pluck($poly, 'lat'), $lats);
						$lons = array_merge(Arr::pluck($poly, 'lon'), $lons);
					}
			}					
			
		}
		
		// Copy circles coordinates
		if (!in_array('circles', $exclude))
		{
		
			foreach ($this->circles as $k => $circle)
			{
				if ($keys == NULL || in_array($k, $keys))
				{
					$lats[] = $circle['lat'];
					$lons[] = $circle['lon'];	
				}				  
			}	
							
		}
		
		// Copy rectangles coordinates
		if (!in_array('rectangles', $exclude))
		{
		
			foreach ($this->rectangles as $k => $rectangle)
			{
				if ($keys == NULL || in_array($k, $keys))
				{
					$lats[] = $rectangle['lat'];
					$lons[] = $rectangle['lon'];
					
					$lats[] = $rectangle['elat'];
					$lons[] = $rectangle['elon'];	
				}				  
			}	
									
		}
		
		if (sizeof($lats) && sizeof($lons))
		{				
			$bounds['lat_max'] = max($lats);
			$bounds['lat_min'] = min($lats);
			$bounds['lon_max'] = max($lons);
			$bounds['lon_min'] = min($lons);
		}
		else
		{
			$bounds['lat_max'] = $this->config->default_lat;
			$bounds['lat_min'] = $this->config->default_lat;
			$bounds['lon_max'] = $this->config->default_lon;
			$bounds['lon_min'] = $this->config->default_lon;
		}	
		
		return $bounds;			
	}
	
			
	/**
	 * Get georequest from coordinates
	 *	       	
	 * @param   float    Latitude
	 * @param   float    Longitude 
	 * @param   boolean	 Decode response in JSON format? (Default TRUE)        	 
	 * @return  object
	 */
	public function get_from_coordinates($lat, $lon, $decode = TRUE)
	{	 	 	 
	 return $this->_geo_request(NULL, $lat, $lon, $decode);
	}
	
	
	/**
	 * Get georequest from address
	 *	       	
	 * @param   string  Search query (Ex "Rua Real 3, 15155 Finisterre, A Coruña")   
	 * @param   boolean Decode response in JSON format? (Default TRUE)       	 
	 * @return  object
	 */
	public function get_from_address($query, $decode = TRUE)
	{	 	    
   return $this->_geo_request($query, NULL, NULL, $decode);
	}
	
	
	/**
	 * Get last infowindow key
	 *	       	
	 * @param   boolean  TRUE for get javascript reference    	 
	 * @return  integer
	 */
	public function get_infowindow_key($reference = FALSE)
	{	 
	 $last_key = $this->_last_key($this->infos);
	 
	 return $reference ? "infowindow_$last_key_{$this->id}" : $last_key;
	}
	
				
	/**
	 * Get last element key
	 *	       	 	 
	 * @param   boolean  TRUE for get javascript reference
	 * @return  string
	 */
	public function get_key($reference = FALSE)
	{	 
	 return $reference ? "{$this->last_key}_{$this->id}" : $this->last_key;
	}
	
			
	/**
	 * Get javascript render script
	 *
	 * @param   string   Map ID container    
	 * @param   string  Latitude
	 * @param   string  Longitude      	 	 
	 * @return  string
	 */
	public function get_map($container, $lat = NULL, $lon = NULL, $zoom = NULL)
	{
	
	   // Set default lat, lon and zoom if this params are null
     $lat  = is_null($lat)  ? $this->config->default_lat  : $lat;
	   $lon  = is_null($lon)  ? $this->config->default_lon  : $lon;
	   $zoom = is_null($zoom) ? $this->config->default_zoom : $zoom;
	   	        

     // Set map options
     $js = "var mapOptions_{$this->id} = {
      zoom: $zoom,      
      center: new google.maps.LatLng($lat, $lon),
      mapTypeId: google.maps.MapTypeId.{$this->config->default_type}";
      
    
     // Prevent IE Javascript comma bug
     $js .= count($this->config->options) ? ",\n" : '';
      
     // Set extra options
     $js .= $this->_generate_options($this->config->options);
     $js .= "};\n";
                    
      
     // Initialize map
     $js .= "var map_{$this->id} = new google.maps.Map(document.getElementById(\"$container\"), mapOptions_{$this->id});\n";
               
     // Set tilt
     if ($this->config->tilt <> 0)
     {
      $js .= "map_{$this->id}.setTilt({$this->config->tilt});\n";
     
      // Set heading     
      $js .= $this->config->rotation <> 0 ? "map_{$this->id}.setHeading({$this->config->rotation});\n" : '';
     }
     	  	   
     // Generate markers
     $js .= $this->_generate_markers();          
     
     // Generate coords groups
     $js .= $this->_generate_coords();
          
     // Generate polylines
     if (isset($this->polys['polyline']))
     	$js .= $this->_generate_polys('polyline', $this->polys);
     
     // Generate polygons
     if (isset($this->polys['polygon']))
     	$js .= $this->_generate_polys('polygon', $this->polys);
    
     // Generate circles
     $js .= $this->_generate_circles();
     
     // Generate rectangles
     $js .= $this->_generate_rectangles();
     
     // Generate infowindows
     $js .= $this->_generate_infowindows();
                    
     // Generate layers
     $js .= $this->_generate_layers();
     
     // Extra code
     foreach ($this->jscod as $code)
      $js .= "\n$code";
           	        
     return $js;
	 
	}
	
	
	/**
	 * Get javascript api link
	 *
	 * @param		boolean		Retrieve URL without enclosed tags (Default False)	 
	 * @return  string
	 */
	
	public function get_apilink($only_url = FALSE)
	{	   	            
     
     // Activate sensor
     $query  = '?sensor='.$this->_b2s($this->config->sensor);          
          
     // Set language
     $query .= '&language=';
     $query .= $this->config->language == 'i18n' ? substr(I18n::lang(), 0, 2) : $this->config->language;
     
     // Get region
     if ($this->config->region)     
      $query .= '&region='.$this->config->region;
      
     // Get libraries
     $libraries = '';
     
     foreach ($this->config->layers as $layer)
     {
      if (!empty($layer['lib']))
      {       
        $libraries .= empty($libraries) ? '' : ',';
        $libraries .= $layer['lib'];
      }      
     }
     
     if (!empty($libraries))
      $query .= '&libraries='.$libraries;
               	     	   
	   $url = $this->config->maps_url.$query;		 		 		
		 			      	     	  
	   return $only_url ? $url : HTML::script($url); 
	}
	
			
	/**
	 * Change / Get map id
	 *
	 * @param   string   New map id	 	  	 	 	    	 
	 * @return  string   Map id
	 */	
	public function id($id = NULL)
  {    
    $this->id = is_null($id) ? $this->id : $id;
        
    return 'map_'.$this->id;
  }       
	
	
	/**
	 * Add / Change default configuration and options
	 *
	 * @chainable	 
	 * @param   string   Configuration key	 	  	 
	 * @param   mixed    Value	    	 
	 * @return  void
	 */
  public function set($key, $value)
	{	
	 
	 $options = explode('.', $key);
	 
	 $pieces = count($options);
	 
	 $config = &$this->config->$options[0];

	 for ($i = 1; $i < $pieces; $i++)	 
    $config = &$config[$options[$i]];
	 	 
   $config = $value;           
   	 	 	 
	 return $this;
	}
	
	
	
	/**
	 * Add a poly group
	 *
	 * @chainable	 
	 * @param		string	Poly type (polyline or polygon)	 
	 * @param		string	Group	 
	 * @param   string  Stroke color in hex html value (Default '#000000')    
	 * @param   integer Stroke line weight (Default 2)
	 * @param		float		Stroke opacity (Default 1.0)
	 * @param		float		Fill opacity (Default 0.35)		 
	 * @param		string	Fill color in hex html vlaue (Default '#FF0000');	  	 	 	     	 	          	       	 	 
	 * @return  object 
	 */
	protected function _add_poly_group($type, $group, $strokecolor = '#000000', $strokeweight = 2, $strokeopacity = 1.0, $fillopacity = '0.35', $fillcolor = '#FF0000')
	{			
	
		$group = Inflector::camelize($group);
		 
		$this->last_key = $type.'_'.$group;		
		
    $this->polys[$type][$group] = array('strokeColor'		=> $strokecolor, 
    														 			  'strokeWeight'	=> $strokeweight,   
																			  'strokeOpacity'	=> $strokeopacity,
																			  'fillOpacity'		=> $fillopacity,
																			  'fillColor'			=> $fillcolor,																			  																							                                 						                                    						 
															 				 );                                             
                                       
    return $this;      
	}
	
	
	/**
	 * Convert boolean to string
	 *
	 * @param   mixed    Boolean string type or boolean	 	  	 	 
	 * @return  string   
	 */
  public function _b2s($value)
	{
	 
   if (is_string($value))
    return $value;
   else
    return $value ? 'true' : 'false'; 
    
	}
	
	public function _calculate_center($lat_max, $lat_min, $lon_max, $lon_min)
	{
		return array('lat' => ($lat_max + $lat_min) / 2,
								 'lon' => ($lon_max + $lon_min) / 2);		  		
	}
	
	
	/**
	 * Detect the type of a key
	 *
	 * @param   string   Key id	 	  	 	 
	 * @return  string   
	 */
	protected function _detect_type($key)
	{
		$separator_pos = strpos($key, '_');
		
		return substr($key, 0, $separator_pos);	
	}
	
	
	/**
	 * Generate circles
	 * 	    	
	 * @return  string
	 */
	protected function _generate_circles()
	{
		
		$js = '';
		
		foreach ($this->circles as $k => $circle)
		{
			$js .= "\nvar {$k}_{$this->id} = new google.maps.Circle({";
			
			foreach ($circle as $option => $value)
			{			 
				if ($option != 'lat' && $option != 'lon')
				{
					$js .= "\n$option: ";
					$js .= is_numeric($value) ? $value : "'$value'";
					$js .= ',';
				}
			}
			
			$js .= "\ncenter: new google.maps.LatLng({$circle['lat']}, {$circle['lon']}),"; 
			$js .= "\nmap: map_{$this->id}";
			$js .= "\n});\n";
		}
		
		return $js;
	}
	
	/**
	 * Generate coords groups
	 * 	    	
	 * @return  string
	 */
	protected function _generate_coords()
	{
	
		$js = '';
		
		// Generate points
		foreach ($this->coords as $k => $points)
		{
			$js .= "\nvar coords_{$k}_{$this->id} = [\n";			
			$js .= $this->_get_points($points);			
			$js .= "\n];\n";
		}
		
		return $js;
		
	}
	
		
	/**
	 * Generate infowindow
	 *	    	 
	 * @return  string
	 */
	protected function _generate_infowindows()
	{
	
	 	$js = '';
	    
   	foreach($this->infos as $k => $infowindow)
   	{	
	  
		  // Generate infowindow
		  $js .= "var infowindow_{$k}_{$this->id} =  new google.maps.InfoWindow();\n";
		  
			foreach($infowindow as $info)
			{
			
				// Add slashes
				$content = str_replace('"', '\"', $info['content']);
				
				// Convert line breaks
				$content = str_replace(array("\r\n", "\n", "\r"), '<br />', $content);
				
				$type = $this->_detect_type($info['mark_id']);
				 
				if ( $type == 'mark')
				{
					$self_reference = ', this';
					$position = '';
				}
				else
				{
					$self_reference = '';
					$position = "infowindow_{$k}_{$this->id}.setPosition(e.latLng);";
				}
				
				$js .= "google.maps.event.addListener({$info['mark_id']}_{$this->id}, 'click', function(e) {
									infowindow_{$k}_{$this->id}.setContent(\"$content\");
									$position                            								
									infowindow_{$k}_{$this->id}.open(map_{$this->id}{$self_reference});
	            	});\n";
	      
	      // Opened
	      if ($info['opened'])
	      {      	      
	      	
					// Center infowindow into element position
					switch ($type)
					{	
						case 'polygon':							
						case 'polyline':
																			          
							// Calculate poly center
							$polygroup = explode('_', $info['mark_id']);
							
							// Get poly bounds
							$bounds = $this->get_bounds(array('marks', 'circles', 'rectangles'), $polygroup[1]);
							
							// Calculate center
							$center = $this->_calculate_center($bounds['lat_max'], 
																								 $bounds['lat_min'],
																								 $bounds['lon_max'],
																								 $bounds['lon_min']);
							
							$js .= "infowindow_{$k}_{$this->id}.setPosition(new google.maps.LatLng({$center['lat']}, {$center['lon']}));\n";   		
																								 																				 																								
						break;
						 				
						case 'rectangle':																											
							$js .= "infowindow_{$k}_{$this->id}.setPosition({$info['mark_id']}_{$this->id}.getBounds().getCenter());\n";			
						break;
						
						case 'circle':
							$js .= "infowindow_{$k}_{$this->id}.setPosition({$info['mark_id']}_{$this->id}.getCenter());\n";
						break;
						
						default:
							$self_reference = ", {$info['mark_id']}_{$this->id}";																											
					}
									
					$js .= "infowindow_{$k}_{$this->id}.setContent(\"$content\");\n";								                                                                                        
					$js .= "infowindow_{$k}_{$this->id}.open(map_{$this->id}$self_reference);\n";     		
	    	}
				             		
			}
		}
	    
  	return $js;
 	 
	}
	
	
	/**
	 * Generate layers
	 *	    	 
	 * @return  string
	 */
	protected function _generate_layers()
	{
	 
	 $js = '';
	 
	 foreach($this->config->layers as $layer)
	 {	   	   	   
    $layer_id = 'layer_'.uniqid();
      
    $js .= "var $layer_id = new {$layer['instance']};\n";
    $js .= "$layer_id.setMap(map_{$this->id});\n";      
	 }
	 
	 return $js;
	 
	}
					
  /**
	 * Generate code for markers
	 *	    	 
	 * @return  string
	 */
	protected function _generate_markers()
	{	 
	 $icons_added   = array();
	 $shadows_added = array();
	 
	 // Generate master icon	 	 	 
   $js  = "\nvar baseIcon_{$this->id} = new google.maps.MarkerImage('{$this->config->default_icon}', 
            new google.maps.Size(".implode(', ', $this->config->icon_size)."),
            new google.maps.Point(".implode(', ', $this->config->icon_origin)."),
            new google.maps.Point(".implode(', ', $this->config->icon_anchor)."));\n";
   
   // Generate master shadow icon 
   if ($this->config->view_shadow)  
   {
    $js .= "\nvar baseShadow_{$this->id} = new google.maps.MarkerImage('{$this->config->default_shadow}',
             new google.maps.Size(".implode(', ', $this->config->shadow_size)."),
             new google.maps.Point(".implode(', ', $this->config->icon_origin)."),
             new google.maps.Point(".implode(', ', $this->config->icon_anchor)."));\n";
   }
                 	
	 // Generate marks
	 foreach ($this->marks as $k => $mark)
	 {
   	   	  
	   // Generate customs icons
     if (!empty($mark['icon']))
	   {
	     	 	     
	     // Add extra icon option
       $icon_size   = isset($mark['icon_size'])   ? $mark['icon_size']   : $this->config->icon_size;
       $shadow_size = isset($mark['shadow_size']) ? $mark['shadow_size'] : $this->config->shadow_size;
	     $icon_origin = isset($mark['icon_origin']) ? $mark['icon_origin'] : $this->config->icon_origin;
	     $icon_anchor = isset($mark['icon_anchor']) ? $mark['icon_anchor'] : $this->config->icon_anchor;
	     
	     // Reuse old icons
       $icon_key = array_search($mark['icon'], $icons_added);
       
       if ($icon_key === FALSE)
       {              	     	            
        $js .= "\nvar icon_{$k}_{$this->id} = new google.maps.MarkerImage('{$mark['icon']}',
                new google.maps.Size(".implode(', ', $icon_size)."),
                new google.maps.Point(".implode(', ', $icon_origin)."),
                new google.maps.Point(".implode(', ', $icon_anchor)."));\n";
       
        $icons_added["icon_{$k}_{$this->id}"] = $mark['icon'];
        $icon_key = "icon_{$k}_{$this->id}";
       }
              
       
       // Reuse old shadows      
       if (!empty($mark['shadow']))
       { 
         $shadow_key = array_search($mark['shadow'], $shadows_added);
         
         if ($shadow_key === FALSE)
         {
          $js .= "\nvar shadow_{$k}_{$this->id} = new google.maps.MarkerImage('{$mark['shadow']}',
                  new google.maps.Size(".implode(', ', $shadow_size)."),
                  new google.maps.Point(".implode(', ', $icon_origin)."),
                  new google.maps.Point(".implode(', ', $icon_anchor)."));\n";
         
          $shadows_added["shadow_{$k}_{$this->id}"] = $mark['shadow'];
          $shadow_key = "shadow_{$k}_{$this->id}";
         }
       }              
	                                        
     } 
     
     
     // Generate marker
     $js .= "\nvar {$k}_{$this->id} = new google.maps.Marker({
              position: new google.maps.LatLng({$mark['lat']}, {$mark['lon']}),
              map: map_{$this->id},
              icon: ".($mark['icon'] ? $icon_key : 'baseIcon_'.$this->id).",
              draggable: ".$this->_b2s($mark['draggable']);
     
     
     // Add shadow
     if (!empty($mark['shadow']) || $this->config->view_shadow)      
      $js .= ",\nshadow: ".(isset($shadow_key) ? $shadow_key : 'baseShadow_'.$this->id);
     else
      $js .= ",\nflat: true";
     
     
     // Add title
     $js .= empty($mark['title']) ? '' : ",\ntitle: '{$mark['title']}'";            
              
     
     // Add extra mark options (zIndex, visible, shape...)
     foreach ($mark['marker_ops'] as $k => $marker_option)     
      $js .= ",\n$k: {$marker_option}";
     
     $js .= "\n});";
                                                  
	 }
	 
	 return $js;
	 
	}
	
	
  /**
	 * Generate code for options
	 *
	 * @param   array    Options	 	  	 	 
	 * @param   string   Option key   	 
	 * @return  string
	 */
	public function _generate_options($options, $parent_key = '')
	{
		                      
    $js = empty($parent_key) ? '' : "$parent_key: {\n";
    
    $first = true;
    
    // Generate options
    foreach ($options as $k => $option)
    {          
      
      // Add separator comma      
      $js .= $first ? '' : ",\n";
      $first = FALSE;
      
      if (is_array($option))
        $js .= $this->_generate_options($option, $k, FALSE);
      else
      {        
        $js .= $k.': ';
                
        // Add values
        if ($k == 'position' && !empty($parent_key))
        	$js .= "google.maps.ControlPosition.$option";                                         
        else if ($k == 'style' && !empty($parent_key))
        {                    
          $property = ucfirst(str_replace('Options', 'Style', $parent_key));          
          $js .= "google.maps.$property.$option";                 
        }
        else if ($option == 'true' || $option == 'false' || $option == 'null' 
                 || is_int($option) || is_bool($option))
        {          
          if (is_bool($option)) // Convert boolean to string
            $js .= $this->_b2s($option);
          else
            $js .= $option;            
        }
        else
          $js .= "'$option'";
                
      }
       
    }
    
    $js .= empty($parent_key) ? '' : "\n}";
            
    return $js; 
	}	
	
	
	/**
	 * Generate code for polys
	 * 
	 * @param		string	Poly type (polyline or polygon)	 
	 * @param		array		Poly groups	 	 	 	 
	 *	    	 
	 * @return  string
	 */
	protected function _generate_polys($type, $polygroups)
	{
				
		$js = '';
		
		// Generate groups
		foreach ($polygroups[$type] as $k => $group)
		{
			
			$group_var = "{$type}_{$k}_{$this->id}";
			
			$js .= "\nvar $group_var = new google.maps.".ucfirst($type)."({";
						
			// Create group options
			foreach ($group as $option => $value)
			{			 
				$js .= "\n$option: ";
				$js .= is_numeric($value) ? $value : "'$value'";
				$js .= ',';
			}
							
			$js .= "\npath: coords_{$k}_{$this->id}";
			$js .= "\n });";
			
			$js .= "\n$group_var.setMap(map_{$this->id});";
		}
		
		return $js;
				
	}
	
	
	
	/**
	 * Generate rectangles
	 * 	    	
	 * @return  string
	 */
	protected function _generate_rectangles()
	{
		
		$js = '';
		
		foreach ($this->rectangles as $k => $rectangle)
		{
			$js .= "\nvar {$k}_{$this->id} = new google.maps.Rectangle({";
			
			foreach ($rectangle as $option => $value)
			{			 
				if ($option != 'lat' && $option != 'lon' && $option != 'elat' && $option != 'elon')
				{
					$js .= "\n$option: ";
					$js .= is_numeric($value) ? $value : "'$value'";
					$js .= ',';
				}
			}
			
			$js .= "\nbounds: new google.maps.LatLngBounds(new google.maps.LatLng({$rectangle['lat']}, {$rectangle['lon']}), new google.maps.LatLng({$rectangle['elat']}, {$rectangle['elon']})),"; 
			$js .= "\nmap: map_{$this->id}";
			$js .= "\n});\n";
		}
		
		return $js;
	}
			
	
	/**
	 * Get a Geocoding Request
	 *
	 * @param   string  Address query
	 * @param   float   Latitude
	 * @param   float   Longitude	
	 * @param   boolean Decode response in JSON format? (Default TRUE)     
	 * @return  mixed
	 */
	protected function _geo_request($address = NULL, $lat = NULL, $lon = NULL, $decode = TRUE)
	{
		// Format the URL			
		$url = $this->config->geocoding_url.'/json?sensor='.$this->_b2s($this->config->sensor);
		
		// Set region
		if ($this->config->region !== FALSE)		
      $url .= '&region='.$this->config->region;    
    
    // Set language    
    $url .= '&language=';
    $url .= $this->config->language == 'i18n' ? substr(I18n::lang(), 0, 2) : $this->config->language;
				
		// Set params
    if (!empty($address))
		  $url .= '&address='.htmlentities($address);
		else if (!empty($lat) && !empty($lon))
			$url .= '&lat='.$lat.'&lon='.$lon;	
		else
		  throw new Kohana_Exception('You need add address or coordinates params');
		  
		// Convert spaces
		$url = str_replace(' ', '+', $url);
											  
		// Get the server response
		$json_response = Request::factory($url)->execute()->body();
			
		// Decode response
    $response = json_decode($json_response);
        		         
    // Return error or response  
		if ($response->status == 'OK')
    {       
      return $decode ? $response : $json_response;
    }
		else 
      return FALSE;
	}
	
		
	/**
	 * Generate position points from an array
	 * 
	 * @param		array
	 * @return	string
	 */
	public function _get_points($positions)
	{
		
		$js = '';
		
		$num_positions = sizeof($positions);
		$act_position  = 1;
				     
		foreach($positions as $position)
		{
			$js .= "new google.maps.LatLng({$position['lat']}, {$position['lon']})";
			$js .= $act_position < $num_positions ? ",\n" : '';
						
			$act_position++;						
		}
		
		return $js;		
	} 	 	 	 	 	
  
  
  /**
	 * Get array last key
	 *	    	 
	 * @param   array	 
	 * @return  string
	 */
	public function _last_key($array)
	{
	  $keys = array_keys($array);
	  
    return sizeof($array) > 0 ? $keys[sizeof($array) - 1] : FALSE;      
  }	  	     
	
}
