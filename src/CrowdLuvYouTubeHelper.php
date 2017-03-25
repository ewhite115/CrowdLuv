<?php
/*
 * CrowdLuvYouTubeHelper
 * 
*/


class CrowdLuvYouTubeHelper {


    private $youTubeApi = null;
    private $client = null;

	function __construct() {

		$this->client = new Google_Client();
		$this->client->setApplicationName("CrowdLuv");
		$this->client->setDeveloperKey(GOOGLE_MAPS_APIKEY);
		$this->youTubeApi = new Google_Service_YouTube($this->client);
		
   	}

   	/** [getApi returns API object,  creating it if needed] */
   	public function getApi(){
   		if($this->youTubeApi) return $this->youTubeApi;
   	}


   	/**
   	 * [getRecentVideosForYouTubeId Returns an array of YouTube Video objects for videos posted in the last $months]
   	 * @param  [array] $ytChannelIds [array of YouTube channel ID's]
   	 * @param  [array] $ytUserNames [array of YouTube Usernames]
   	 * @return [array] [Array of YouTube PlaylistItems]
   	 */
   	public function getRecentUploads($ytChannelIds, $ytUsernames, $months){

   		$recentVideos = "";
   		$ytChannelList = "";

   		foreach($ytChannelIds as $ytChannelId){
			//Get the id of the 'uploads' playlist for the channel
			try{$ytChannelList = $this->getApi()->channels->listChannels('contentDetails', ['id' => $ytChannelId]);}
			catch(Google_Service_Exception $e) {
         	   cldbgmsg("--Exception calling Youtube api");
        	   continue; //var_dump($e); die;
            }
        
			//var_dump($ytChannelList);
			if(!$ytChannelList || sizeof($ytChannelList->items) ==0) continue;
			$ytChannelListContentDetail = $ytChannelList->items[0]->contentDetails;
			//var_dump($ytChannelListContentDetail); 
			$ytUploadsPlaylistId = $ytChannelListContentDetail->relatedPlaylists['uploads'];
			//var_dump($ytUploadsPlaylistId);
			//Get a listing of the items on that playlist
			$ytPlaylistItems = $this->getApi()->playlistItems->listPlaylistItems('snippet,contentDetails', ['playlistId' => $ytUploadsPlaylistId, 'maxResults' => '50']   )->items;
			$recentVideos = null;
			foreach ($ytPlaylistItems as $ytPlaylistItem) {
				//var_dump($ytPlaylistItem);	
				$dateInterval = date_diff((new DateTime()),	(new DateTime($ytPlaylistItem['contentDetails']['videoPublishedAt'])));
				$ageMonths = $dateInterval->m + ($dateInterval->y * 12);			
				if($ageMonths < $months) {
					//$url = "https://www.youtube.com/watch?v=" .  $ytPlaylistItem['contentDetails']['videoId'];
					//echo "Video Title: <a href='" . $url . "'>" . $ytPlaylistItem['snippet']['title'] . "</a> - ID: " . $ytPlaylistItem['contentDetails']['videoId'] . " - Published At: " . $ytPlaylistItem['contentDetails']['videoPublishedAt'] . 'AgeMonths: ' . $ageMonths;
					//echo '<br>';
					$recentVideos[] = $ytPlaylistItem;
				}			
			} //foreach
		}

   		foreach($ytUsernames as $ytUsername){
   			//echo "search for videos for " . $ytUsername;
			//Get the id of the 'uploads' playlist for the channel
			try{$ytChannelList = $this->getApi()->channels->listChannels('contentDetails', ['forUsername' => $ytUsername]);}
			catch(Google_Service_Exception $e) {
         	   cldbgmsg("--Exception calling Youtube api");
        	   continue; //var_dump($e); die;
            }
			//var_dump($ytChannelList);
			if(!$ytChannelList || sizeof($ytChannelList->items) ==0) continue;
			$ytChannelListContentDetail = $ytChannelList->items[0]->contentDetails;
			//var_dump($ytChannelListContentDetail); 
			$ytUploadsPlaylistId = $ytChannelListContentDetail->relatedPlaylists['uploads'];
			//var_dump($ytUploadsPlaylistId);
			//Get a listing of the items on that playlist
			$ytPlaylistItems = $this->getApi()->playlistItems->listPlaylistItems('snippet,contentDetails', ['playlistId' => $ytUploadsPlaylistId, 'maxResults' => '50']   )->items;
			$recentVideos = null;
			foreach ($ytPlaylistItems as $ytPlaylistItem) {
				//var_dump($ytPlaylistItem);	
				$dateInterval = date_diff((new DateTime()),	(new DateTime($ytPlaylistItem['contentDetails']['videoPublishedAt'])));
				$ageMonths = $dateInterval->m + ($dateInterval->y * 12);			
				if($ageMonths < $months) {
					//$url = "https://www.youtube.com/watch?v=" .  $ytPlaylistItem['contentDetails']['videoId'];
					//echo "Video Title: <a href='" . $url . "'>" . $ytPlaylistItem['snippet']['title'] . "</a> - ID: " . $ytPlaylistItem['contentDetails']['videoId'] . " - Published At: " . $ytPlaylistItem['contentDetails']['videoPublishedAt'] . 'AgeMonths: ' . $ageMonths;
					//echo '<br>';
					$recentVideos[] = $ytPlaylistItem;
				}			
			} //foreach
		}


		return $recentVideos;
   	} 










}//CrowdLuvYouTubeHelper
