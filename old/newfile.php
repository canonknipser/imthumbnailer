<?php
switch($ck_it_thumb->getImageOrientation())
{

	case \Imagick::ORIENTATION_UNDEFINED:
		$message = 'Orientation undefined';
		break;
	case \Imagick::ORIENTATION_TOPLEFT:
		$message = 'Orientation TOPLEFT';
		break;
	case \Imagick::ORIENTATION_TOPRIGHT:
		$message = 'Orientation TOPRIGHT';
		break;
	case \Imagick::ORIENTATION_BOTTOMRIGHT:
		$message = 'Orientation BOTTOMRIGHT';
		break;
	case \Imagick::ORIENTATION_BOTTOMLEFT:
		$message = 'Orientation BOTTOMLEFT';
		break;
	case \Imagick::ORIENTATION_LEFTTOP:
		$message = 'Orientation LEFTTOP';
		break;
	case \Imagick::ORIENTATION_RIGHTTOP:
		$message = 'Orientation RIGHTTOP';
		break;
	case \Imagick::ORIENTATION_RIGHTBOTTOM:
		$message = 'Orientation RIGHTBOTTOM';
		break;
	case \Imagick::ORIENTATION_LEFTBOTTOM:
		$message = 'Orientation LEFTBOTTOM';
		break;
	default:
		$message = 'Orientation unnknown';
		break;
}
$this->ck_im_loggen($message);

/* Get the EXIF information */
$exifArray = $ck_it_thumb->getVersion();

$message = '';
/* Loop trough the EXIF properties */
foreach ($exifArray as $name => $property)
{
	$message .=  "{$name} => {$property}<br />\n";
}
$this->ck_im_loggen($message);
$message = '';
/* Get the EXIF information */
$exifArray = $ck_it_thumb->getImageProperties("exif:Orientation");

/* Loop trough the EXIF properties */
foreach ($exifArray as $name => $property)
{
	$message .=  "{$name} => {$property}<br />\n";
}
$this->ck_im_loggen($message);

