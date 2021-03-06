<?php
/**
 *
 * ImageMagick Thumbnailer. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, canonknipser, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace canonknipser\imthumbnailer\migrations;

class install_acp_v1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['ck_it_quality']);
	}

	static public function depends_on()
	{
		return array('\canonknipser\imthumbnailer\migration\install_acp_V1_0_0');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('ck_it_version', '1.0.2')),
			array('config.add', array('ck_it_width', 0)),
			array('config.add', array('ck_it_heigth', 0)),
			array('config.add', array('ck_it_orig_width', 0)),
			array('config.add', array('ck_it_orig_heigth', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_CK_IT_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_CK_IT_TITLE',
				array(
					'module_basename'	=> '\canonknipser\imthumbnailer\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
