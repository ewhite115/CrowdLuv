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
   	 * [getMusicStoryIdByFacebookId ]
   	 * @param  [Long Unsigned Int] $fb_id [Facebook ID]
   	 * @return [int]        [MusicStory ID,  or null.]
   	 */
   	public function getMusicStoryIdByFacebookId($fb_id){

		//If we can't contact the MS api, return.
		if (! $this->getApi() ){ return null;}
		
		//Query MS Api based on facebook ID.
		$msArtistFromFbId = null;
		try{ $msArtistFromFbId=$this->getApi()->getArtist($fb_id, 'facebook');}
		catch(Exception $e){ return null;}
		//var_dump($artistFromFbId);die;
		//If the return value is empty (either because it wasnt found or because musoc-story API was unreachable), return null
		if(! isset($msArtistFromFbId)) {return null;}
		$cur = $msArtistFromFbId->current();
		if(! $cur) {return null;}
		return $cur->id;

   	}



   	/**
   	 * [getMetaDataForBrandFromFacebookId Uses MusicStory API to obtain a Spotify ID, given a facebook ID]
   	 * @param  [type] $fb_pid [Facebook Page ID]
   	 * @return [Array]         [Music Story ID,  Spotify ID, YouTube Channel ID. Null if not found.]
   	 */
   	public function getMetaDataForBrand($msId){

   		//Return Array -- default values to null
		$metaData = ["music-story-id" => $msId,  "facebook-page-id" => null, "spotify-artist-id" => null,  "youtube-channel-id" => null];
		
		//If we can't contact the MS api, return.
		if (! $this->getApi() ){ return $metaData;}
		
		//Get MS Artist Object from API
		try{$msArtist = $this->getApi()->getArtist($msId);}
		catch(Exception $e){ cldbgmsg("Exception calling MS Api for metadata - aborting"); return $metaData; }
		//var_dump($msArtist);
		
		//Retrieve Spotify ID
		$artistSpotifyObj = $msArtist->getConnector('spotify', []);
		//var_dump($artistSpotify);
		$spotifyId = null;
		$updateDate = null;
		foreach($artistSpotifyObj as $as){
			if(strpos($as->url, "open.spotify.com") && ($as->update_date > $updateDate)   ) {
				//echo "Found spotify ID: " . $as->id . " updated " . $as->update_date;
				$spotifyId = $as->id;
				$updateDate = $as->update_date;
			}//if
		}//foreach
		$metaData["spotify-artist-id"] = $spotifyId;

		//Retrieve Facebook ID
		$artistFacebookObj = $msArtist->getConnector('facebook', []);
		//var_dump($artistFacebookObj);
		$facebookId = null;
		$updateDate = null;
		foreach($artistFacebookObj as $fb){
			if(strpos($fb->url, "facebook.com") && ($fb->update_date > $updateDate)   ) {
				//echo "Found Facebook ID: " . $fb->id . " updated " . $fb->update_date;
				$facebookId = $fb->id;
				$updateDate = $fb->update_date;
			}//if
		}//foreach
		$metaData["facebook-page-id"] = $facebookId;


		//Retrieve YouTube Channel ID
		$artistYouTubeChannelObj = $msArtist->getConnector('youtubechannel', []);
		//var_dump($artistYouTubeChannelObj);
		$youTubeChannelId = null;
		$updateDate = null;
		foreach($artistYouTubeChannelObj as $ytc){
			if(strpos($ytc->url, "facebook.com") && ($ytc->update_date > $updateDate)   ) {
				//echo "Found YouTube Channel ID: " . $ytc->id . " updated " . $ytc->update_date;
				$youTubeChannelId = $ytc->id;
				$updateDate = $ytc->update_date;
			}//if
		}//foreach
		$metaData["youtube-channel-id"] = $facebookId;



		return $metaData;


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
