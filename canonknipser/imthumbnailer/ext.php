<?php
/**
 *
 * ImageMagick Thumbnailer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, canonknipser, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace canonknipser\imthumbnailer;

/**
 * ImageMagick Thumbnailer Extension base
 *
 */
class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		// several tests, each test is only executed if the previous tests did not fail

		// fixed issue #3
		$user = $this->container->get('user');
		$user->add_lang('imthumbnailer_acp', false, false, 'canonknipser/imthumbnailer');

		// first test: Imagick library installed?
		if (!class_exists('Imagick'))
		{
				trigger_error($user->lang('CK_IM_REQUIRE_IMAGICK'), E_USER_WARNING);
		}

		// second test: phpBB version greater equal 3.2.4?
		$config =$this->container->get('config');
		if (!phpbb_version_compare($config['version'], '3.2.4', '>='))
		{
				trigger_error($user->lang('CK_IM_REQUIRE_324'), E_USER_WARNING);
		}

		return true;
	}

}
