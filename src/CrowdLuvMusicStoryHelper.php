<?php
/*
 * CrowdLuvMusicStoryHelper
 * 
*/


require_once ROOT_PATH . 'vendor/musicstory/MusicStoryAPI.class.php';
//use musicstory\MusicStoryApi;
  

class CrowdLuvMusicStoryHelper {


    private $musicStoryApi = null;
    

	function __construct() {

		$MSAPI = new MusicStoryApi(MUSIC_STORY_CUSTOMER_KEY, MUSIC_STORY_CUSTOMER_SECRET);
		// if needed, change of the tokens and retrieval
		//$tokens=$MSAPI->getToken();
		//$AccessToken=$tokens['access_token'];
		//$TokenSecret=$tokens['token_secret'];
		// New eventual authentication with tokens if will of re-using the same ones
		//$MSAPI = new MusicStoryApi(MUSIC_STORY_CUSTOMER_KEY, MUSIC_STORY_CUSTOMER_SECRET  );
		
		$this->musicStoryApi = $MSAPI;
		
   	}



   	public function getSpotifyIdFromFacebookId($fb_pid){

		$artistFromFbId = null;
		try{ $artistFromFbId=$this->musicStoryApi->getArtist($fb_pid, 'facebook');}
		catch(Exception $e){ return null;}

		//var_dump($artistFromFbId);
		$msArtist = $this->musicStoryApi->getArtist($artistFromFbId->current()->id);
		//var_dump($msArtist);
		$artistSpotify = $msArtist->getConnector('spotify', []);
		//var_dump($artistSpotify);

		$spotifyId = null;
		$updateDate = null;
		foreach($artistSpotify as $as){

			if(strpos($as->url, "open.spotify.com") && ($as->update_date > $updateDate)   ) {
				//echo "Found spotify ID: " . $as->id . " updated " . $as->update_date;
				$spotifyId = $as->id;
				$updateDate = $as->update_date;
			}//if

		}//foreach

		return $spotifyId;

   	}//getSpotifyIdForFacebookId






}//CrowdLuvMusicStoryHelper
