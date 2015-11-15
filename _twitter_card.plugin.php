<?php
/**
 * This file implements the Latest posts widget for {@link http://b2evolution.net/}.
 * @copyright (c)2008 by Emin Özlem  - {@link http://eminozlem.com/}.
 * @license GNU General Public License 2 (GPL) - http://www.opensource.org/licenses/gpl-license.php
 * @package plugins
 * @author Emin Özlem
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

class twitter_card_plugin extends Plugin
{
	/**
	 * Variables below MUST be overriden by plugin implementations,
	 * either in the subclass declaration or in the subclass constructor.
	 */
	var $name = 'Twitter Card';
	/**
	 * Code, if this is a renderer or pingback plugin.
	 */
	var $code = 'eo_twitter_card';
	var $priority = 49;
	var $version = '0.1';
	var $author = 'Emin Özlem';
	var $author_url = 'http://eminozlem.com';
	var $help_url = 'http://forums.b2evolution.net/twitter-card-plugin';
	var $apply_rendering = 'never';
	var $number_of_installs = 1;

	/**
	 * Init
	 *
	 * This gets called after a plugin has been registered/instantiated.
	 */
	function PluginInit( & $params )
	{
		$this->short_desc = $this->T_('Integrate Twitter cards');
		$this->long_desc = $this->T_('Twitter card integration for your blog');
	}
	
	/**
	 * Define here default collection/blog settings that are to be made available in the backoffice.
	 *
	 * @param array Associative array of parameters.
	 * @return array See {@link Plugin::get_coll_setting_definitions()}.
	 */
	function get_coll_setting_definitions( & $params )
	{
		$domain = preg_replace('/^www\./','',$_SERVER['HTTP_HOST']); 
	//	global $Blog;
		return array_merge( parent::get_coll_setting_definitions( $params ),
			array(
				'card_type' => array(
					'label' => T_('Card Type'),
					'note' => T_('The card type'),
					'type' => 'select',
					'options' => array(
						'summary' => T_('Summary Card'),
						'summary_large_image' => T_('Summary Card with Large Image'),
				//		array( 'app', T_('App Card') ),
				//		array( 'player', T_('Player Card') ),
					),
					'defaultvalue' => 'summary_large_image',
				),
				'site_id' => array(
					'label' => T_( 'Site ID' ),
					'size' => 25,
					'note' => T_( 'REQUIRED. @username of website.' ),
					'defaultvalue' => $domain,
					// . $Blog->siteurl,
				),
			)
		);
	}
	
	function eo_get_featured_image_url($size=NULL) {
		global $Item;
		// Get list of attached files
		$feat_img_uri = false;
		$params = array(
			'before'              => '',
			'before_image'        => '',
			'before_image_legend' => '',
			'after_image_legend'  => '',
			'after_image'         => '',
			'after'               => '',
			'limit'               => 1,
			'image_link_to'       => 'original',
			'before_gallery'      => '',
			'after_gallery'       => '',
			// Optionally restrict to files/images linked to specific position: 'teaser'|'aftermore'
			'restrict_to_image_position' => 'teaser',
		);
		if(isset($size)) $params['image_size'] = $size;
		$post_images = $Item->get_images( $params,'raw' );
		if( ! empty ($post_images) ) {
	
			preg_match('/(src)=("[^"]*")/',$post_images, $img_parts);
			$feat_img_uri = str_replace(array('src="','"','&amp;'),array('','','&'),$img_parts[0]);
		}
		return $feat_img_uri;
		/*if( $app_version < 5 ) {
			if( ! $FileList = $Item->get_attachment_FileList(1, 'teaser') )
			{
				return '';
			}
	
			$r = '';
			$File = NULL;
			while( $File = & $FileList->get_next() )
			{
				if( $File->is_image() ) {
					$feat_img_uri = $File->_FileRoot->ads_url . $File->_rdfp_rel_path;
			//		var_dump($feat_img_uri);
				}
			}
		}*/		
	}
	
	
	function SkinBeginHtmlHead( & $params )
	{	
		global $Item,$Chapter,$Blog,$baseurl;
		$tw_img = false;
		$blogdesc = $Blog->longdesc;
		if( isset($Item) && ! empty($Item) ) {
			$id = $Item->ID;
	//		$og_uri = $baseurl .'index.php?p='.$id.'&amp;blog='.$Blog->ID.'&amp;redir=no';
			$tw_desc = $Item->excerpt;
			$tw_title = $Item->title;	
			$tw_img = $this->eo_get_featured_image_url();
		}
		else if ( isset($Chapter) && ! empty($Chapter) ) {
			//if we are on category 
			$tw_desc = $Chapter->description;
			$tw_title = $Chapter->name;
		}
		else {
			// Just in case, for everything else.	
			$tw_desc = $blogdesc;
			$tw_title = $Blog->name;
		}
		$card_type = ( $tw_img ) ? $this->get_coll_setting( 'card_type', $Blog ) : 'summary';
		add_headline('<meta name="twitter:card" content="'.$card_type.'">');
		add_headline('<meta name="twitter:site" content="'.$this->get_coll_setting( 'site_id', $Blog ).'">');
		add_headline('<meta name="twitter:title" content="'.format_to_output($tw_title,'htmlattr').'">');
		add_headline('<meta name="twitter:description" content="'.format_to_output($tw_desc,'htmlattr').'">');
		if( $tw_img ) add_headline('<meta name="twitter:image" content="'.$tw_img.'">');
	}
	
	/**
	 * Event handler: SkinTag (widget)
	 *
	 * @param array Associative array of parameters.
	 * @return boolean did we display?
	 *
	 */
//	function SkinTag( $params )	{}
}

?>