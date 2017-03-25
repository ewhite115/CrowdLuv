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
		//New eventual authentication with tokens if will of re-using the same ones
		//$MSAPI = new MusicStoryApi(MUSIC_STORY_CUSTOMER_KEY, MUSIC_STORY_CUSTOMER_SECRET  );
		
		return $this->musicStoryApi = $MSAPI;

   	}




   	/**
   	 * [getMusicStoryIdByFacebookId ]
   	 * @param  [Long Unsigned Int] $fb_id [Facebook ID]
   	 * @return [int]        [MusicStory ID,  or null.]
   	 */
   	private function getMusicStoryIdByFacebookId($fb_id){

		//If we can't contact the MS api, return.
		if (! $this->getApi() ){ return NULL;}
		
		//Query MS Api based on facebook ID.
		$msArtistFromFbId = NULL;
		try{ $msArtistFromFbId=$this->getApi()->getArtist($fb_id, 'facebook');}
		catch(Exception $e){ cldbgmsg("Exception calling Musicstory API from getMusicStoryIdByFacebookId"); return NULL;}
		//var_dump($msArtistFromFbId);//die;
		//If the return value is empty (either because it wasnt found or because music-story API was unreachable), return NULL
		if(! isset($msArtistFromFbId)) {return NULL;}
		$cur = $msArtistFromFbId->current();
		//var_dump($cur);
		if(! $cur) {return NULL;}
		return $cur->id;

   	}


   	/**
   	 * [getMetaDataForBrandFromFacebookId Uses MusicStory API to obtain a Spotify ID, given a facebook ID]
   	 * @param  [type] $brandObj [Brand Object]
   	 * @return [Array]         [Music Story ID,  Spotify ID, YouTube Channel ID. Null if not found.]
   	 */
   	public function getMetaDataForBrand($brandObj){

		//Check if the brand already has a music_story ID -- if not, try to retrieve it by facebook ID
		$msId = $brandObj['music_story_id'];
		
		if(  (! $msId || $msId == "null") && (time() - strtotime($brandObj['timestamp_last_music_story_id_retrieval']) >  10 * 24 * 60 * 60) ){	 
			$msId = $this->getMusicStoryIdByFacebookId($brandObj['fb_pid']);
			$resultMsg = "Attempted to retrieve Music Story ID for " . $brandObj['fb_page_name'] . ($msId ? " -Found " . $msId : " -Not found");
			cldbgmsg($resultMsg);
		}
		
   		//Initialize the Return Array with the msId found, and default other values to null
		$metaData = ["music-story-id" => $msId,  "facebook-page-id" => null, "spotify-artist-id" => null,  "youtube-channel-id" => null, "bandpage-id" => null];
		
		//if there is no music-story ID, return
		//var_dump($msId);
		if (! $msId || $msId == "null" ){ cldbgmsg("-No MS Id found - aborting metadata retrieval"); return $metaData;}	

		//If we can't contact the MS api, return.
		if (! $this->getApi() ){ return $metaData;}
		
		//Get MS Artist Object from API
		$msArtist = null;
		try{$msArtist = $this->getApi()->getArtist($msId);}
		catch(Exception $e){ cldbgmsg("Exception calling MS Api for metadata - aborting"); return $metaData; }
		if(! $msArtist) { cldbgmsg("MS API Returned NULL Artist for ID: " . $msId . " for Brand: " . $brandObj['fb_page_name']); return null;}
		
		//Attempt to retrieve Spotify ID if we don't already have it
		if(!$brandObj['spotify_artist_id']){
			$artistSpotifyObj = $msArtist->getConnector('spotify', []);
			//TODO:  error handling:

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
			$resultMsg = "Attempted to retrieve Spotify ID for " . $brandObj['fb_page_name'] . ($spotifyId ? " -Found " . $msId : " -Not found");
			 cldbgmsg($resultMsg);
		}
		//Retrieve Facebook ID if we dont already have it
		if(!$brandObj['fb_pid']){
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
			$resultMsg = "Attempted to retrieve Facebook ID for " . $brandObj['fb_page_name'] . ($facebookId ? " -Found " . $msId : " -Not found");
			cldbgmsg($resultMsg);

		}

		//Retrieve YouTube Channel ID if we dont already have it
		if(!$brandObj['youtube_channel_id']){
			$artistYouTubeChannelObj = $msArtist->getConnector('youtubechannel', []);
			//var_dump($artistYouTubeChannelObj);
			$youTubeChannelId = null;
			$updateDate = null;
			foreach($artistYouTubeChannelObj as $ytc){
				if(strpos($ytc->url, "youtube.com") && ($ytc->update_date > $updateDate)   ) {
					//echo "Found YouTube Channel ID: " . $ytc->id . " updated " . $ytc->update_date;
					$youTubeChannelId = $ytc->id;
					$updateDate = $ytc->update_date;
				}//if
			}//foreach
			$metaData["youtube-channel-id"] = $youTubeChannelId;
			$resultMsg = "Attempted to retrieve YoutubeChannel ID for " . $brandObj['fb_page_name'] . ($youTubeChannelId ? " -Found " . $youTubeChannelId : " -Not found");
			cldbgmsg($resultMsg);

		}
	
		/*
		if(! $brandObj['bandpage_id']){
			//Retrieve BandPage Channel ID
			$artistBandPageChannelObj = $msArtist->getConnector('bandpage', []);
			//var_dump($artistBandPageChannelObj);
			$BandPageChannelId = null;
			$updateDate = null;
			foreach($artistBandPageChannelObj as $ytc){
				if(strpos($ytc->url, "bandpage.com") && ($ytc->update_date > $updateDate)   ) {
					//echo "Found BandPage Channel ID: " . $ytc->id . " updated " . $ytc->update_date;
					$BandPageChannelId = $ytc->id;
					$updateDate = $ytc->update_date;
				}//if
			}//foreach
			$metaData["bandpage-id"] = $BandPageChannelId;
			$resultMsg = "Attempted to retrieve BandPage ID for " . $brandObj['fb_page_name'] . ($BandPageChannelId ? " -Found " . $BandPageChannelId : " -Not found");
			cldbgmsg($resultMsg);

		}
		*/
	

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
