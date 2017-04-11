<?php
/*
 * CrowdLuvBrandMetadataHelper
 * 
*/

 

class CrowdLuvBrandMetaDataHelper {


	public $clFacebookHelper = null;
	public $clSpotifyHelper = null;
	public $clYouTubeHelper = null;

    
	function __construct() {
		
   	}

   	


   	/**
   	 * [getMetaDataForBrandFromFacebookId Uses MusicStory API to obtain a Spotify ID, given a facebook ID]
   	 * @param  [type] $brandObj [Brand Object]
   	 * @return [Array]         [Music Story ID,  Spotify ID, YouTube Channel ID. Null if not found.]
   	 */
   	public function getMetaDataForBrand($brandObj){

		
   		//Initialize the Return Array with the msId found, and default other values to null
		$metaData = ["facebook-page-id" => null, "spotify-artist-id" => null,  "youtube-channel-id" => null, "bandpage-id" => null];
		
		
		//Attempt to retrieve Spotify ID if we don't already have it
		if(!$brandObj['spotify_artist_id']){

			//echo $brandObj['fb_page_name'];

			$artistObj = $this->clSpotifyHelper->getArtistObjectByNameSearch($brandObj['fb_page_name'] );
			//var_dump($artistObj->artists);die;

			if( isset($artistObj->id) ) $metaData["spotify-artist-id"] = $artistObj->id;
			$resultMsg = "--Attempted to retrieve Spotify ID for " . $brandObj['fb_page_name'] . ($metaData["spotify-artist-id"] ? " -Found " . $metaData["spotify-artist-id"] : " -Not found");
			cldbgmsg($resultMsg);

		}
		//Retrieve Facebook ID if we dont already have it
		if(!$brandObj['fb_pid']){




			$metaData["facebook-page-id"] = $facebookId;
			$resultMsg = "--Attempted to retrieve Facebook ID for " . $brandObj['fb_page_name'] . ($facebookId ? " -Found " . $msId : " -Not found");
			cldbgmsg($resultMsg);

		}

		//Retrieve YouTube Channel ID if we dont already have it
		if(!$brandObj['youtube_channel_id']){

			$youTubeChannelId = null;			
			$ytChannelSnippet = $this->clYouTubeHelper->findChannelSnippetForBrand($brandObj);
			$youTubeChannelId = $ytChannelSnippet['channelId'];
			//var_dump($youTubeChannelId);die;

			$metaData["youtube-channel-id"] = $youTubeChannelId;
			$resultMsg = "--Attempted to retrieve YoutubeChannel ID for " . $brandObj['fb_page_name'] . ($youTubeChannelId ? " -Found " . $youTubeChannelId : " -Not found");
			cldbgmsg($resultMsg);

		}
	

		return $metaData;

   	}//getSpotifyIdForFacebookId







   	/**
   	 * [getFacebookIdFromSpotifyId Uses MusicStory API to obtain a Fqcebook page ID ID, given a spotify artist ID]
   	 * @param  [type] $spId [Facebook Page ID]
   	 * @return [type]         [Facebook page ID or null if not found.]
   	 */
   	public function getFacebookIdFromSpotifyId($spId){





		return $facebookId;

   	}//getFacebookIdForSpotifyId








}//CrowdLuvMusicStoryHelper
