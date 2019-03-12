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

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

//	protected $ck_it_thumb;

	/**
	* Constructor for listener
	*
	* @param \phpbb\config\config		$config		phpBB config
	* @param \phpbb\request\request		$request	phpBB request
	* @param \phpbb\log\log				$log		phpBB log
	* @param \phpbb\user				$user		phpBB user
	* @param \phpbb\language\language	$language	phpBB language
	*
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\request\request $request, \phpbb\log\log $log, \phpbb\user $user, \phpbb\language\language $language)
	{
		$this->config	= $config;
		$this->request	= $request;
		$this->log		= $log;
		$this->user		= $user;
		$this->language	= $language;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.thumbnail_create_before'	=> 'ck_it_create_tumbnail',
			'core.modify_uploaded_file'		=> 'ck_it_modify_uploaded_file',
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
		return value is only thumbnail_created, defaults to false
		*/

		$ck_it_source				= $event['source'];
		$ck_it_destination			= $event['destination'];
		$ck_it_mimetype				= $event['mimetype'];
		$ck_it_new_width			= $event['new_width'];
		$ck_it_new_height			= $event['new_height'];
		$ck_it_thumbnail_created	= false;

		// load language file
		// fixed issue #4 Use $language instead of $user->lang
		$this->language->add_lang('common', 'canonknipser/imthumbnailer');

		try
		{

			// create a new instance of ImageMagick and load current image
			if (!($ck_it_thumb = new \Imagick(realpath($ck_it_source))))
			{
				$this->ck_im_loggen($this->language->lang('CK_ERR_NEW_INSTANCE'));
			}

			$this->ck_im_set_format($ck_it_thumb, $ck_it_mimetype);

			// Compression quality is read from config, set in ACP
			$this->ck_im_set_compression($ck_it_thumb, $this->config['ck_it_quality']);

			// rotate the image according it's orientation flag
			$this->ck_im_autorotate($ck_it_thumb, $ck_it_new_width, $ck_it_new_height);

			// Store the image
			if (($ck_it_thumb->writeImage($ck_it_destination)))
			{
				$ck_it_thumbnail_created = true;
			}
			else
			{
				$this->ck_im_loggen($this->language->lang('CK_ERR_WRITE_IMAGE'));
			}

		}
		catch ( \ImagickException $ex)
		{
			// write error message for Imagick exceptions to admin log
			$this->ck_im_loggen($this->language->lang('CK_ERR_CALLING_IMAGICK').'<br/>'.$ex->getMessage());
		}

		// set return value
		$event['thumbnail_created'] = $ck_it_thumbnail_created;
	}

	public function ck_it_modify_uploaded_file($event)
	{
		/**
		 store all variables from $event for future use,
		 */

		$ck_it_filedata				= $event['filedata'];
		//contains:
		//  filesize
		//  mimetype
		//  extension
		//  physical_filename
		//  real_filename
		//  filetime
		//  post_attach
		//  error
		//  thumbnail

		$ck_it_is_image				= $event['is_image'];
		if ($ck_it_is_image)
		{
			// do the magic only for images
		}

	}
	/**
	 * Log a message to the admin log
	 *
	 * @param string	$ck_log_message	Message written to the log
	 */
	function ck_im_loggen($ck_log_message)
	{
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $ck_log_message);
	}

	/**
	 * set the image format for the generated image
	 *
	 * @param object	$image		image object
	 * @param string	$mimetype	mimetype of the image in phpBB internal format
	 */
	function ck_im_set_format($image, $mimetype)
	{
		$imageformat = '';
		// Check the mimetype and set the appropriate type for the thumbnail
		switch ($mimetype)
		{
			case 'image/jpeg':
				$imageformat = 'JPEG';
				break;
			case 'image/png':
				$imageformat = 'PNG';
				break;
			case 'image/gif':
				$imageformat = 'GIF';
				break;
			default:
				// unknown type, set a default
				$imageformat = 'JPEG';
				$this->ck_im_loggen($this->language->lang('CK_WARN_MIMETYPE').$mimetype);
				break;
		}

		if (!($image->setImageFormat($imageformat)))
		{
			$this->ck_im_loggen($this->language->lang('CK_ERR_SET_FORMAT'));
		}
	}

	/**
	 * set the compression value for the generated image
	 *
	 * @param object	$image		image object
	 * @param integer	$quality	image quality value
	 */
	function ck_im_set_compression($image, $quality)
	{
		if (!($image->setImageCompressionQuality($quality)))
		{
			$this->ck_im_loggen($this->language->lang('CK_ERR_SET_FORMAT'));
		}


	}

	/**
	 * rotate and resize the generated image
	 *
	 * @param object	$image		image object
	 * @param integer	$width		new witdth of the image
	 * @param integer	$height		new height of the image
	 */
	function ck_im_autorotate($image, $width, $height)
	{
		// read the Orientation from the Image
		if (!($ck_it_orientation = $image->getImageOrientation()))
		{
			// Orientation flag not available
			$ck_it_orientation = \Imagick::ORIENTATION_UNDEFINED;
		}

		// set flags needed for rotation
		// $ck_it_flop = rotate image around central y-axis
		// $ct_it_rotate = angle in degrees to rotate
		switch ($ck_it_orientation)
		{
			case \Imagick::ORIENTATION_TOPLEFT:
				$ck_it_flop = false;
				$ck_it_rotate = 0;
				break;
			case \Imagick::ORIENTATION_TOPRIGHT:
				$ck_it_flop = true;
				$ck_it_rotate = 0;
				break;
			case \Imagick::ORIENTATION_BOTTOMRIGHT:
				$ck_it_flop = false;
				$ck_it_rotate = 180;
				break;
			case \Imagick::ORIENTATION_BOTTOMLEFT:
				$ck_it_flop = true;
				$ck_it_rotate = 180;
				break;
			case \Imagick::ORIENTATION_LEFTTOP:
				$ck_it_flop = true;
				$ck_it_rotate = -90;
				break;
			case \Imagick::ORIENTATION_RIGHTTOP:
				$ck_it_flop = false;
				$ck_it_rotate = 90;
				break;
			case \Imagick::ORIENTATION_RIGHTBOTTOM:
				$ck_it_flop = true;
				$ck_it_rotate = 90;
				break;
			case \Imagick::ORIENTATION_LEFTBOTTOM:
				$ck_it_flop = false;
				$ck_it_rotate = -90;
				break;
			default:
				$ck_it_flop = false;
				$ck_it_rotate = 0;
				break;
		}
		if ($ck_it_flop)
		{
			$image->flopImage();
		}
		if ($ck_it_rotate != 0)
		{
			$image->rotateImage("#000", $ck_it_rotate);
		}
		if (abs($ck_it_rotate) == 90)
		{
			$new_width = $height;
			$new_height = $width;
		}
		else
		{
			$new_width = $width;
			$new_height = $height;

		}
		$image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);

		// Do the magic: resize the image using a proper filter
		// todo: add choise of filters to ACP (Issue #2)
		if (!($image->resizeImage($new_width, $new_height, \Imagick::FILTER_LANCZOS, 1, false)))
		{
			$this->ck_im_loggen($this->language->lang('CK_ERR_RESIZE'));
		}


	}
}
