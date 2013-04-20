<?php

class AddImageToRSS{

	public function __construct(){
		//hook into routing
		Event::add('system.pre_controller', array($this, 'addImage'));
	}

	public function addImage(){
		Event::add('ushahidi_action.feed_rss_item', array($this, 'applyImage'));
	}

	public function applyImage(){
		//get the data
		$id = Event::$data;

		$incident = ORM::factory('incident')
			->where('id',$id)
			->find();

		$mediapath = false;
		$sizeinbytes = false;
		$mime_type = false;

		foreach($incident->media as $media){
			if($media->media_type == 1){//if it is a photo
				$mediapath = url::convert_uploaded_to_abs($media->media_medium);

				//basic path of the image, without the domain.
				$basicpath = Kohana::config('upload.relative_directory').'/'.$media->media_medium;

				if(file_exists($basicpath)){
					$sizeinbytes = filesize($basicpath);
					$mime_type = mime_content_type ($basicpath);
				}
			}
		}

		if(($mediapath != false) && ($sizeinbytes != false) && ($mime_type != false)){
			echo '<enclosure url="'.$mediapath.'" length="'.$sizeinbytes.'" type="'.$mime_type.'" />';
		}

	}

}

new AddImageToRSS;

?>