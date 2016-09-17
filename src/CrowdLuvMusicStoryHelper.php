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
		
   	}

   	/** [getApi returns Music-Story API object,  creating it if needed] */
   	private function getApi(){

   		if($this->musicStoryApi) return $this->musicStoryApi;

		$MSAPI = new MusicStoryApi(MUSIC_STORY_CUSTOMER_KEY, MUSIC_STORY_CUSTOMER_SECRET);
		// if needed, change of the tokens and retrieval
		//$tokens=$MSAPI->getToken();
		//$AccessToken=$tokens['access_token'];
		//$TokenSecret=$tokens['token_secret'];
		// New eventual authentication with tokens if will of re-using the same ones
		//$MSAPI = new MusicStoryApi(MUSIC_STORY_CUSTOMER_KEY, MUSIC_STORY_CUSTOMER_SECRET  );
		
		return $this->musicStoryApi = $MSAPI;

   	}

   	/**
   	 * [getSpotifyIdFromFacebookId Uses MusicStory API to obtain a Spotify ID, given a facebook ID]
   	 * @param  [type] $fb_pid [Facebook Page ID]
   	 * @return [type]         [Spotify ID or null if not found.]
   	 */
   	public function getSpotifyIdFromFacebookId($fb_pid){

		if (! $this->getApi() ){ return null;}

		$artistFromFbId = null;
		try{ $artistFromFbId=$this->getApi()->getArtist($fb_pid, 'facebook');}
		catch(Exception $e){ return null;}
		//var_dump($artistFromFbId);die;
		//If the return value is empty (either because it wasnt found or because musoc-story API was unreachable), return null
		if(! isset($artistFromFbId)) {return null;}
		$cur = $artistFromFbId->current();
		if(! $cur) {return null;}

		$msId = $artistFromFbId->current()->id;
		$msArtist = $this->getApi()->getArtist($msId);
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







   	/**
   	 * [getFacebookIdFromSpotifyId Uses MusicStory API to obtain a Fqcebook page ID ID, given a spotify artist ID]
   	 * @param  [type] $spId [Facebook Page ID]
   	 * @return [type]         [Facebook page ID or null if not found.]
   	 */
   	public function getFacebookIdFromSpotifyId($spId){

		if (! $this->getApi() ){ return null;}

		$artistFromSpId = null;
		try{ $artistFromSpId=$this->getApi()->getArtist($spId, 'spotify');}
		catch(Exception $e){ return null;}
		//var_dump($artistFromSpId);die;
		//If the return value is empty (either because it wasnt found or because musoc-story API was unreachable), return null
		if(! isset($artistFromSpId)) {return null;}
		$cur = $artistFromSpId->current();
		if(! $cur) {return null;}

		$msId = $artistFromSpId->current()->id;
		$msArtist = $this->getApi()->getArtist($msId);
		//var_dump($msArtist);die;
		$artistFacebook = $msArtist->getConnector('facebook', []);
		//var_dump($artistFacebook); die;


		$facebookId = null;
		$updateDate = null;
		foreach($artistFacebook as $as){

			if(strpos($as->url, "facebook.com") && ($as->update_date > $updateDate)   ) {
				//echo "Found fb ID: " . $as->id . " updated " . $as->update_date;
				$facebookId = $as->id;
				$updateDate = $as->update_date;
			}//if

		}//foreach

		return $facebookId;

   	}//getFacebookIdForSpotifyId








}//CrowdLuvMusicStoryHelper
