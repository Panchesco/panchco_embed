<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	/**
	* Panchco_embed Class
	*
	* @package ExpressionEngine
	* @category Plugin
	* @author Richard Whitmer
	* @copyright Copyright (c) 2013, Richard Whitmer
	* @link NA
	*/
	
     $plugin_info        = array(  
        'pi_name'        => 'Panchco Embed',  
        'pi_version'     => '1.0.0',  
        'pi_author'      => 'Richard Whitmer',  
        'pi_author_url'  => 'http://panchco.com',  
        'pi_description' => '
        - Format a Vimeo or YouTube URL to iframed player<br />
        - Access xml field data as template tags',  
        'pi_usage'       => Panchco_embed::usage()  
        );
                

        
		class Panchco_embed {
			
				private $provider = FALSE;
				private	$tagdata;
				private	$format = 'xml';
				private	$vimeo_format = 'xml';
				private	$request = '';
				private	$xml_properties = array(
				
					'type',
					'version',
					'provider_name',
					'title',
					'author_name',
					'html',
					'width',
					'height',
					'maxwidth',
					'maxheight',
					'duration',
					'description',
					'thumbnail_url',
					'thumbnail_width',
					'thumbnail_height',
					'video_id'
					
					);
				
				// Configure provider endpoints here
				private $endpoints = array(
						
					'youtube'	=> 'http://www.youtube.com/oembed',
					'vimeo'		=> 'http://vimeo.com/api/oembed.xml'
						
					);
				
				// Set display options to an array we can use		
				private	$options = array(	
			
					'width',
					'height',
					'maxwidth',
					'maxheight',
					'byline',
					'title',
					'portrait',
					'color',
					'callback',
					'autoplay',
					'loop',
					'wmode',
					
					);
									
				// Player properties array to be populated by the class
				public $player_properties = array();
			
				
				function __construct()
				{
						$this->EE =& get_instance();
			
						// Set default values for xml properties
						foreach($this->xml_properties as $key)
						{
							$this->{$key} = '';
						}
						
						// Tag pair?
						if($this->EE->TMPL->tagdata) 
						{
							$this->player();
						}
			
				}
					
					
				//-----------------------------------------------------------------------------

				
					/** Return iframe/embedded player for single tag, player and tagdata for a tag pair
					 *	@return string
					 */
					public function player()
					{
						// Get the URL
						$this->url = $this->EE->TMPL->fetch_param('url');
						
						// Clean up $url and set it to object
						$this->clean_url();
						
						$this->_set_provider();
						$this->_set_endpoint();
						$this->_add_options();
						$this->_load_xml();
						$this->_xml_properties();

							// Create array to hold player data
							foreach($this->xml_properties as $key)
							{
								if(preg_match("/\{" . $key . "\}/",$this->EE->TMPL->tagdata))
								{
									$player_data[$key] = $this->{$key};
								}
							}
			
						
						if($this->EE->TMPL->tagdata)
						{
							$this->return_data = $this->EE->functions->var_swap($this->EE->TMPL->tagdata, $player_data);
							
						} else {
						
							return $this->html;
						}
					}
				
				
								
				//-----------------------------------------------------------------------------
				
					public function clean_url()
					{
							// Clean up any empty space
							return preg_replace("/[[:space:]]/",'',$this->url);
					}
					
					
					/** Set the provider
					 *
					 *	@return void
					 */
					private function _set_provider()
					{
					
						if(preg_match("/youtu(\.){0,1}be/",$this->url))
						{
							$this->provider	= 'youtube';
						}
						
						if(preg_match("/vimeo/",$this->url))
						{
							$this->provider	=  'vimeo';
						}
			
			
						return;
						
					}
					
				//-----------------------------------------------------------------------------
				
					/** Set the endpoint for requested URL
					 *
					 * return void
					 */
					private function _set_endpoint()
					{
						if($this->provider!==FALSE)
						{
							$this->endpoint = $this->endpoints[$this->provider];
							return;
						}
			
					}
					
					
				//-----------------------------------------------------------------------------
				
					/** Build URL query string to send to endpoint 
					  * returned player
					  *
					  * @return void
					  */
					private function _add_options()
					{
						if($this->provider!==FALSE)
						{
							$this->request = '?url=' . urlencode($this->url) . '&format=xml';
								
							foreach($this->options as $key)
							{
							
								$this->{$key} = $this->EE->TMPL->fetch_param($key);
								
							    if($this->{$key})
							    {
							    	$this->request.= '&';
							    	$this->request.= $key . '=' . $this->{$key};	    
							    }
							}
						}
			
			
						return;
					
					}
					
				//-----------------------------------------------------------------------------
				
					/** Get URL and set to XML String
					 *
					 * @return void
					 */
					private function _load_xml()
					{
						if($this->provider!==FALSE)
						{
							
								$url = $this->endpoint . $this->request;
								
								$url = $this->_curl_get($url);
							
								$this->xml	= @simplexml_load_string($url);
			
						}
						
						return;
					
					}
					
					
				//-----------------------------------------------------------------------------
				
				
				
				/** Set fields in this->xml string to object properties
				 *
				 * @return void
				 */
				private function _xml_properties()
				{		
					if(isset($this->xml))
					{
						foreach($this->xml_properties as $key)
						{
						    $this->{$key} = (isset($this->xml->{$key})) ? $this->xml->{$key} : '';
						}
					}	
					
					return;
				}
									
				
				//-----------------------------------------------------------------------------
				
				
				/** Curl helper function
				 *	Adapted from Vimeo API Examples at https://github.com/vimeo/vimeo-api-examples
				 *	@param $url
				 *	@return string
				 */
				private function _curl_get($url) 
				{
				        $curl = curl_init($url);
				        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				        $return = curl_exec($curl);
				        curl_close($curl);
				        
				    return $return;
				}
					
										
				//-----------------------------------------------------------------------------
			
			
				// ----------------------------------------  
				// Usage  
				// ----------------------------------------  
			
			
			                  
			     function usage()  
			     {  
			     ob_start();                
			     ?>
			     
				 ------------------
				 TAGS:
				 ------------------
				 
				 /** Use tag pair to display returned oEmbed xml field values as tags
				  *	Note: Check oEmbed specs http://oembed.com/ 
				  *	and Vimeo/YouTube oEmbed APIs for what's available
				  *
				  *	Vimeo oEmbed API: https://developer.vimeo.com/apis/oembed
				  *	YouTube: http://apiblog.youtube.com/2009/10/oembed-support.html
				  */
				 
				 {exp:panchco_embed url="http://www.youtube.com/watch?v=qhaxrc2OhJU"}
				 
				     {type}
				     {version}
				     {provider_name}
				     {title}
				     {author_name}
				     {html} 
				     {width}
				     {height}
				     {maxwidth}
				     {maxheight}
				     {duration}
				     {description}
				     {thumbnail_url}
				     {thumbnail_width}
				     {thumbnail_height}
				     {video_id}
		
				 {/exp:panchco_embed}
				 
				 // Use the player tag to return just the player 
				 
				 {exp:panchco_embed:player url="http://www.youtube.com/watch?v=qhaxrc2OhJU"}
			   
				 ------------------
				 PARAMETERS:
				 ------------------
				 
				     url  (required)
				     maxwidth
				     maxheight
				     wmode
				     autoplay
				     loop
				     byline
				 
			
			     <?php
			     
			     $buffer = ob_get_contents();
			       
			     ob_end_clean();   
			       
			     return $buffer;  
			     

				/**	TODO:
				 *	
				 *	- Additional providers
				 *	- "photo" Type support
				 *	- Display multiple players in tagdata
				 */

			
			    }  
			    
		}
   			
			/* End of file pi.panchco_embed.php */  
			/* Location: ./system/expressionengine/third_party/panchco_embed/pi.panchco_embed.php */
