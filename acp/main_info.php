<?php
/**
 *
 * ImageMagick Thumbnailer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, canonknipser, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace canonknipser\IMThumbnailer\acp;

/**
 * ImageMagick Thumbnailer ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\canonknipser\IMThumbnailer\acp\main_module',
			'title'		=> 'ACP_CK_IT_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_CK_IT',
					'auth'	=> 'ext_canonknipser/IMThumbnailer && acl_a_board',
					'cat'	=> array('ACP_CK_IT_TITLE')
				),
			),
		);
	}
}
