<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

	public function contextToClassNames()
	{
		return sprintf("%s %s %s", $this->params["controller"], $this->params["action"], implode(" ", $this->params["pass"]));
	}

	public function getTitle($title_for_layout)
	{
		return sprintf("%s %s", ($title_for_layout) ? $title_for_layout . " &mdash; " : "",  __("The Music Tank"));
	}

	public function getImageUrl($obj, $type = "thumb")
	{
		$image = $obj ? Hash::get($obj, "image") : null;
		$imgsrc = "";
		$ds = DIRECTORY_SEPARATOR;

		if($image && file_exists(WWW_ROOT . "img" . $ds . "cache" . $ds . $image . "_" . $type . ".jpg")) {
			$imgsrc = "/img/cache/" . $image . "_" . $type . ".jpg";
		}
		else {
			$imgsrc = "/img/placeholder.png";
		}

		return $imgsrc;
	}

	public function getTrackPlayerAttributes($artist, $track, $trackYoutube)
	{
		if(Hash::check($trackYoutube, "youtube_key_manual")) {
			return sprintf('data-song-vid="%s"', $trackYoutube["youtube_key_manual"]);
		}
		elseif(Hash::check($trackYoutube, "youtube_key")) {
			return sprintf('data-song-vid="%s"', $trackYoutube["youtube_key"]);
		}
		return sprintf('data-song="%s/%s"', $artist["slug"], $track["slug"]);
	}
}
