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
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\log\log */
	protected $log;

	/**
	* Constructor for listener
	*
	* @param \phpbb\config\config	$config		phpBB config
	* @param \phpbb\request\request	$request	phpBB request
	* @param \phpbb\log\log			$log		phpBB log
	*
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\request\request $request, \phpbb\log\log $log)
	{
		$this->config	= $config;
		$this->request	= $request;
		$this->log		= $log;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.thumbnail_create_before'	=> 'ck_it_create_tumbnail',
		);
	}

	/**
	 * Create a thumbnail using IMagick
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

		// TODO: Create language entries for error messages

		// TODO: enable Exception handling
		if (!($ck_it_thumb = new \Imagick(realpath($ck_it_source.'v'))))
		{
			ck_im_loggen('neue Instanz fehlgeschlagen');
		}

		ck_im_loggen("RealPath: ".$ck_it_source."<br/>Destination: ".$ck_it_destination."<br/>Mimetype: ".$ck_it_mimetype);

		if (!($ck_it_thumb->setImageFormat('JPEG')))
		{
			// Log-Meldung schreiben
			ck_im_loggen('setImageFormat fehlgeschlagen');
		}
		$this->log->add('admin', ANONYMOUS, '', "ImageMagick: ".$ck_it_thumb-> getImageFormat());

		// Compression quality is read from config, set in ACP
		// todo; add choise of filters to ACP
		if (!($ck_it_thumb->setImageCompressionQuality($this->config['ck_it_quality'])))
		{
			// Log-Meldung schreiben
			ck_im_loggen('setImageCompressionQuality fehlgeschlagen');
		}
		if (!($ck_it_thumb->resizeImage($ck_it_new_widht, $ck_it_new_height, \Imagick::FILTER_LANCZOS, 1, false)))
		{
			// Log-Meldung schreiben
			ck_im_loggen('resizeImage fehlgeschlagen');
		}
		if (!($ck_it_thumb->writeImage($ck_it_destination)))
		{
			// Log-Meldung schreiben
			ck_im_loggen('writeIMage fehlgeschlagen');
		}


		$event['thumbnail_created'] = true;
	}


	function ck_im_loggen($ck_log_message)
	{
		$this->log->add('admin', ANONYMOUS, '', $ck_log_message);
	}
}
