<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Google Maps Module.
 *
 * @package    Gmaps v3
 * @author     Juan Lago D. <juanparati[at]gmail[dot]com>  
 * @copyright  (c) 2011 Kohana
 * @license    http://kohanaphp.com/license.htmlç
 * @version		 1.1 
 */
 
abstract class Kohana_Gmaps3 {

	protected static $instance;
	
	private $config;
  private $id;  
				
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
			$config = Kohana::config('qrcode');					
			
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
		
		// set default lat and lon
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
	
		array_push($this->circles, array('lat'						=> $lat, 
                                   	 'lon'						=> $lon,
																		 'radius'					=> $radius,
																		 'strokeColor'		=> $strokecolor,
																		 'strokeWeight'		=> $strokeweight,
																		 'strokeOpacity'	=> $strokeopacity, 
																		 'fillOpacity'		=> $fillopacity,
																		 'fillColor'			=> $fillcolor,                                                                     
                                   ));
    
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
    $this->coords[$group][] = array('lat'	=> $lat, 
                                   	'lon'	=> $lon                                    						                                    						 
															 		 );                                         
                                       
    return $this;      
	}
	
	

  /**
	 * Add a infowindow
	 *	 
	 * @chainable	 	 
	 * @param   string  Content (You can use HTML)	 	 	 	  	 
	 * @param   boolean Opened (Optional)
	 * @param   integer Mark id (FALSE = Last mark)   	          	       	 	 
	 * @return  object
	 */
	public function add_infowindow($content, $opened = FALSE, $mark_id = FALSE)
	{
    // Get mark id
    if ($mark_id === FALSE)
    {      
      $mark_id = $this->_last_key($this->marks);            
      
      if ($mark_id === FALSE)
        throw new Kohana_Exception('No marks for infowindow.');                                  
    }
        
	 	    	   
	  // Add mark
    array_push($this->infos, array('mark_id' => $mark_id, 
                                   'content' => $content, 
                                   'opened'  => $opened));
                                         
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
    array_push($this->marks, array('lat'       => $lat, 
                                   'lon'       => $lon, 
                                   'title'     => $title,
                                   'draggable' => $draggable,                                         
                                   'icon'      => $icon,
                                   'shadow'    => $shadow,
                                   'icon_ops'  => $icon_ops,
                                   'marker_ops'=> $marker_ops,                                   
                                   ));
    
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
		$this->_add_poly_group('Polygon', $group, $strokecolor, $strokeweight, $strokeopacity, $fillopacity, $fillcolor);																							                                 						                                    						 
															 			                                                                                    
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
  	$this->_add_poly_group('Polyline', $group, $strokecolor, $strokeweight, $strokeopacity, $fillopacity, $fillcolor);
		
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
	
		array_push($this->rectangles, array('lat'						=> $lat, 
                                		  	'lon'						=> $lon,
                                   	 		'elat'					=> $elat,
                                   	 		'elon'					=> $elon,																		 		
																		 		'strokeColor'		=> $strokecolor,
																		 		'strokeWeight'	=> $strokeweight,
																		 		'strokeOpacity'	=> $strokeopacity, 
																		 		'fillOpacity'		=> $fillopacity,
																		 		'fillColor'			=> $fillcolor,                                                                     
                                   		 ));
    
		return $this;      
			
	}
	
	
	
	
	
  
  /**
	 * Center the map position in relation with a mark 
	 *	       	 	 
	 * @chainable
	 * @param   integer  Mark id (FALSE = Center relative to the last mark)   	 	 
	 */
  public function center($mark_id = FALSE)
	{
    if ($mark_id === FALSE)
    {
      $mark_id = $this->_last_key($this->marks);
      
      if ($mark_id === FALSE)
        throw new Kohana_Exception('No marks for center.');          
    }          
                        
    $lat = $this->marks[$mark_id]['lat'];
    $lon = $this->marks[$mark_id]['lon'];
    
    $this->jscod['center'] = "map_{$this->id}.setCenter(new google.maps.LatLng($lat, $lon));\n";
    
    return $this;	   
	}
	
	
	
  /**
	 * Center and fit the map position in relation with all map elements 
	 *	       	 	 
	 * @chainable
	 * @param   boolean	Autofit   	 	 
	 * @param   array		Exclude elements (marks, polylines, circles or rectangles)   	 
	 */
  public function center_all($autofit = TRUE, $exclude = array())
  {
  	
  	if ($this->_last_key($this->marks) === FALSE)
  		throw new Kohana_Exception('No marks for center.');
  	
  	$coords = $this->get_bounds($exclude);
  	
  	$max = ($coords['lat_max'] + $coords['lat_min']) / 2;
  	$min = ($coords['lon_max'] + $coords['lon_min']) / 2;
  	
  	$this->jscod['center'] = "map_{$this->id}.setCenter(new google.maps.LatLng($max, $min));\n";
  	
  	if ($autofit)
  	{
  		$this->jscod['center'] .= "map_{$this->id}.fitBounds(
				new google.maps.LatLngBounds(
					new google.maps.LatLng({$coords['lat_min']}, {$coords['lon_min']}),
					new google.maps.LatLng({$coords['lat_max']}, {$coords['lon_max']})
				));\n";
  	}
  	
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
	 * Get the bounds of all map elements 
	 *	       	 	 	 	    	 	 
	 * @param		array		Exclude elements (marks, polylines, circles or rectangles)
	 * @return	array   	 
	 */
	public function get_bounds($exclude = array())
	{
				
		$lats = $lons = array();
		
		// Copy marks coordinates
		if (!in_array('marks', $exclude))
		{		           
			$lats = Arr::merge(Arr::pluck($this->marks, 'lat'), $lats);
			$lons = Arr::merge(Arr::pluck($this->marks, 'lon'), $lons);
		}
		
		// Copy polylines coordinates
		if (!in_array('polylines', $exclude))
		{					
			$lats = Arr::merge(Arr::pluck($this->coords, 'lat'), $lats);
			$lons = Arr::merge(Arr::pluck($this->coords, 'lon'), $lons);
		}
		
		// Copy circles coordinates
		if (!in_array('circles', $exclude))
		{		
			$lats = Arr::merge(Arr::pluck($this->circles, 'lat'), $lats);
			$lons = Arr::merge(Arr::pluck($this->circles, 'lon'), $lons);
		}
		
		// Copy triangles coordinates
		if (!in_array('rectangles', $exclude))
		{	
			foreach ($this->coords as $coords)
			{	
				$lats = Arr::merge(Arr::pluck($coords, 'lat'), $lats);
				$lons = Arr::merge(Arr::pluck($coords, 'lon'), $lons);
				
				$lats = Arr::merge(Arr::pluck($coords, 'elat'), $lats);						
				$lons = Arr::merge(Arr::pluck($coords, 'elon'), $lons);				
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
	 
	 return $reference ? "infowindow_{$this->id}_$last_key" : $last_key;
	}
	
				
	/**
	 * Get last mark key
	 *	       	 	 
	 * @param   boolean  TRUE for get javascript reference
	 * @return  integer
	 */
	public function get_mark_key($reference = FALSE)
	{
	 $last_key = $this->_last_key($this->marks);
	 
	 return $reference ? "marker_{$this->id}_$last_key" : $last_key;
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
	   $zoom = is_null($zoom) ? $this->config->default_zoom : $lon;
	   	        

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
     
     // Generate infowindows
     $js .= $this->_generate_infowindows();
     
     // Generate coords groups
     $js .= $this->_generate_coords();
          
     // Generate polylines
     if (isset($this->polys['Polyline']))
     	$js .= $this->_generate_polys('Polyline', $this->polys);
     
     // Generate polygons
     if (isset($this->polys['Polygon']))
     	$js .= $this->_generate_polys('Polygon', $this->polys);
    
     // Generate circles
     $js .= $this->_generate_circles();
     
     // Generate rectangles
     $js .= $this->_generate_rectangles();
                    
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
	 * @return  string
	 */
	
	public function get_apilink()
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
          
     	     	   
	   return HTML::script($this->config->maps_url.$query);
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
	 * Force auto open html window
	 *
	 * @param   integer  Mark key	 	  	 	 
	 * @return  void
	 */
  public function open_html($mark_key)
	{
   // Add slashes
   $html = str_replace('"', '\"', $this->marks[$mark_key]['html']);       
   	
	 $this->marks[$mark_key]['triggers'] .= "marker_{$this->id}_$mark_key.openInfoWindowHtml(\"$html\");\n";  	 	 
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
	 * @param		string	Poly type (Polyline or Polygon)	 
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
			$js .= "\nvar circle_{$this->id}_$k = new google.maps.Circle({";
			
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
			$js .= "\nvar coords_{$this->id}_".Inflector::camelize($k)." = [\n";			
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
	              
    // Add slashes
    $infowindow['content'] = str_replace('"', '\"', $infowindow['content']);
            
    $js .= "var infowindow_{$this->id}_$k = new google.maps.InfoWindow({
              content: \"{$infowindow['content']}\"});
            
            google.maps.event.addListener(marker_{$this->id}_{$infowindow['mark_id']}, 'click', function() {              
              infowindow_{$this->id}_$k.open(map_{$this->id}, this);
            });\n";
    
    // Opened
    $js .= $infowindow['opened'] ? "infowindow_{$this->id}_$k.open(map_{$this->id}, marker_{$this->id}_{$infowindow['mark_id']});\n" : '';      
                          
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
        $js .= "\nvar icon_{$this->id}_$k = new google.maps.MarkerImage('{$mark['icon']}',
                new google.maps.Size(".implode(', ', $icon_size)."),
                new google.maps.Point(".implode(', ', $icon_origin)."),
                new google.maps.Point(".implode(', ', $icon_anchor)."));\n";
       
        $icons_added["icon_{$this->id}_$k"] = $mark['icon'];
        $icon_key = "icon_{$this->id}_$k";
       }
              
       
       // Reuse old shadows      
       if (!empty($mark['shadow']))
       { 
         $shadow_key = array_search($mark['shadow'], $shadows_added);
         
         if ($shadow_key === FALSE)
         {
          $js .= "\nvar shadow_{$this->id}_$k = new google.maps.MarkerImage('{$mark['shadow']}',
                  new google.maps.Size(".implode(', ', $shadow_size)."),
                  new google.maps.Point(".implode(', ', $icon_origin)."),
                  new google.maps.Point(".implode(', ', $icon_anchor)."));\n";
         
          $shadows_added["shadow_{$this->id}_$k"] = $mark['shadow'];
          $shadow_key = "shadow_{$this->id}_$k";
         }
       }              
	                                        
     } 
     
     
     // Generate marker
     $js .= "\nvar marker_{$this->id}_$k = new google.maps.Marker({
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
	 * @param		string	Poly type (Polyline or Polygon)	 
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
			
			$group_var = "group_".lcfirst($type)."_{$this->id}_".Inflector::camelize($k);
			
			$js .= "\nvar $group_var = new google.maps.$type({";
						
			// Create group options
			foreach ($group as $option => $value)
			{			 
				$js .= "\n$option: ";
				$js .= is_numeric($value) ? $value : "'$value'";
				$js .= ',';
			}
							
			$js .= "\npath: coords_{$this->id}_".Inflector::camelize($k);
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
			$js .= "\nvar rectangle_{$this->id}_$k = new google.maps.Rectangle({";
			
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
