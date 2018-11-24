<?php
/**
 *
 * ImageMagick Thumbnailer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, canonknipser, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace canonknipser\imthumbnailer\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ImageMagick Thumbnailer Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.thumbnail_create_before'	=> 'ck_it_create_tumbnail',
		);
	}
	/**
		* Constructor
		*
		* @param \phpbb\controller\helper			$helper
		* @param \phpbb\template\template			$template
		* @param \phpbb\config\config				$config		Config object
		* @param string								$root_path	phpBB root path
		* @param \phpbb\user						$user		user object
		* @param \phpbb\request\request				$request
		* @param \phpbb\db\driver\driver_interface	$db

	 */
	public function __construct(\phpbb\config\config $config)
	{
		$this->config		= $config;
	}

	/**
	 * A sample PHP event
	 * Modifies the names of the forums on index
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function ck_it_create_tumbnail($event)
	{
		/**
		store all variables from $event for future use,
		return value is only thumbnail_created
		*/

		$ck_it_source		= $event['source'];
		$ck_it_destination	= $event['destination'];
		$ck_it_mimetype		= $event['mimetype'];
		$ck_it_new_widht	= $event['new_width'];
		$ck_it_new_height	= $event['new_height'];

		$ck_it_thumb = new \Imagick(realpath($ck_it_source));

		// Compression quality is read from config, set in ACP
		// todo; add choise of filters to ACP
		$ck_it_thumb->setImageCompressionQuality($this->config['ck_it_quality']);
		$ck_it_thumb->resizeImage($ck_it_new_widht, $ck_it_new_height, $ck_it_thumb->FILTER_LANCZOS, 1, false);
		$ck_it_thumb->writeImage($ck_it_destination);

		$event['thumbnail_created'] = true;
	}
}
