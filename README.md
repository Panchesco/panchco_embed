# panchco_embed

## Description
ExpressionEngine 2 Plugin for displaying video player and oEmbed data fields as template tags for a YouTube or Vimeo videos


## Usage
 
 
* Tagpairs
 
	* Use a tag pair to display returned oEmbed xml field values as tags
	
	_Note: Check oEmbed specs http://oembed.com/ 
 	and Vimeo/YouTube oEmbed APIs for what's available for each
	[Vimeo oEmbed API] (https://developer.vimeo.com/apis/oembed)
	[YouTube] (http://apiblog.youtube.com/2009/10/oembed-support.html)_
 
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
 
* Use a single, player tag to return just the player 
 
        {exp:panchco_embed:player url="http://www.youtube.com/watch?v=qhaxrc2OhJU"}

## Parameters
 
     url  (required)
     
     maxwidth
     
     maxheight
     
     wmode
     
     autoplay
     
     loop
     
     byline
				 
