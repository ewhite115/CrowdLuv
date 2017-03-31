<?php
/*
 * CrowdLuvYouTubeHelper
 * 
*/


class CrowdLuvYouTubeHelper {


    private $youTubeApi = null;
    private $client = null;
    private $authUrl = null;

	function __construct() {

		$this->client = new Google_Client();
		$this->client->setApplicationName("CrowdLuv");
		//$this->client->setDeveloperKey(GOOGLE_MAPS_APIKEY);
		$this->client->setClientId(GOOGLE_OAUTH_CLIENTID);
		$this->client->setClientSecret(GOOGLE_OAUTH_SECRET);

		$this->client->setAccessType("offline");
		$this->client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = "http://localhost:8001/myluvs?ytauth";
		$this->client->setRedirectUri($redirect);		
		$this->youTubeApi = new Google_Service_YouTube($this->client);

   	}


   	public function getAuthUrl(){

   		//if it was previously generated, return it
   		if($this->authUrl) return $this->authUrl;

		
		$state = mt_rand();
		$this->client->setState($state);
		$_SESSION['youtubestate'] = $state;

		//set the member object and return
		return ($this->authUrl = $this->client->createAuthUrl());

   	}

   	/** [getApi returns API object,  creating it if needed] */
   	public function getApi(){
   		if($this->youTubeApi) return $this->youTubeApi;
   	}


   	public function getYouTubeSession(){

		//If a previous call to this method found a session, just return that existing member object.
   		

   		// Check if an auth token exists for the required scopes
		$tokenSessionKey = 'youtube_token-' . $this->client->prepareScopes();
		if (isset($_GET['code']) && isset($_GET['ytauth']) ) {

			//Google API doesnt allow redirecting to IP, so the redirect is set to 'localhost' fror devleopment.
			//  Therefore, do an additional redirect back to the correct address, preserving query string
		  	if ($_SERVER['HTTP_HOST'] == "localhost:8001") {
		        header("Location:" . CLADDR . "myluvs?" . $_SERVER['QUERY_STRING']);
		        exit;   
		  	}
		  	if ( strval($_SESSION['youtubestate']) !== strval($_GET['state'])) {
		    	die('The session state did not match.');
		  	}
		  	$this->client->authenticate($_GET['code']);
		  	$_SESSION[$tokenSessionKey] = $this->client->getAccessToken();
		  	header('Location: myluvs' );
		}
		if (isset($_SESSION[$tokenSessionKey])) {
		  $this->client->setAccessToken($_SESSION[$tokenSessionKey]);
		}
		// Check to ensure that the access token was successfully acquired.
		if ($this->client->getAccessToken()) {
		  return ($_SESSION[$tokenSessionKey] = $this->client->getAccessToken());
		}


		return null;

   	}



   	/**
   	 * [getRecentVideosForBrand Returns an array of YouTube Video objects for videos posted in the last $months]
   	 * @param  [array] $ytChannelIds [array of YouTube channel ID's]
   	 * @param  [array] $ytUserNames [array of YouTube Usernames]
   	 * @return [array] [Array of YouTube PlaylistItems]
   	 */
   	public function getRecentUploadsForBrand($ytChannelIds, $ytUsernames, $months){

   		$recentVideos = "";
   		$ytChannelList = "";

   		foreach($ytChannelIds as $ytChannelId){
			
			//Query the YT API for the Channel listing
			try{$ytChannelList = $this->getApi()->channels->listChannels('contentDetails', ['id' => $ytChannelId]);}
			catch(Google_Service_Exception $e) {
         	   cldbgmsg("--Exception calling Youtube api");
        	   continue; //var_dump($e); die;
            }
        	
			//var_dump($ytChannelList);
			if(!$ytChannelList || sizeof($ytChannelList->items) ==0) { cldbgmsg("-ChannelList was empty when searching for channelID"); continue;}
			
			//Get the id of the 'uploads' playlist for the channel
			$ytChannelListContentDetail = $ytChannelList->items[0]->contentDetails;
			//var_dump($ytChannelListContentDetail); 
			$ytUploadsPlaylistId = $ytChannelListContentDetail->relatedPlaylists['uploads'];
			//var_dump($ytUploadsPlaylistId);
			cldbgmsg("-Found Playlist ID for uploads: " . $ytUploadsPlaylistId);
			
			//Get a listing of the items on that playlist
			$ytPlaylistItems = $this->getApi()->playlistItems->listPlaylistItems('snippet,contentDetails', ['playlistId' => $ytUploadsPlaylistId, 'maxResults' => '50']   )->items;
			cldbgmsg("-Found " . sizeof($ytPlaylistItems) .  " Playlist items");
			
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
			
   			//skip usernames that have a '/'  (to avoid searching for brands based on 'pages/')
   			if(strpos($ytUsername, "/")) continue;

			try{$ytChannelList = $this->getApi()->channels->listChannels('contentDetails', ['forUsername' => $ytUsername]);}
			catch(Google_Service_Exception $e) {
         	   cldbgmsg("--Exception calling Youtube api");
        	   var_dump($e); //die;
        	   continue; 
            }
			//var_dump($ytChannelList);
			if(!$ytChannelList || sizeof($ytChannelList->items) ==0) { cldbgmsg("-ChannelList was empty when searching for username " . $ytUsername); continue;}
			
			//Get the id of the 'uploads' playlist for the channel
			$ytChannelListContentDetail = $ytChannelList->items[0]->contentDetails;
			//var_dump($ytChannelListContentDetail); 
			$ytUploadsPlaylistId = $ytChannelListContentDetail->relatedPlaylists['uploads'];
			cldbgmsg("-Found Playlist ID for uploads for username " . $ytUsername . ": " . $ytUploadsPlaylistId);
			
			//Get a listing of the items on that playlist
			$ytPlaylistItems = $this->getApi()->playlistItems->listPlaylistItems('snippet,contentDetails', ['playlistId' => $ytUploadsPlaylistId, 'maxResults' => '50']   )->items;
			cldbgmsg("-Found " . sizeof($ytPlaylistItems) .  " Playlist items");

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


   	public function getSubscriptionListForCurrentUser(){

   		$ytSubsList = [];
   		$nextPageToken = "";

   		//Retrieve 50 subscriptions at a time from the YouTube API, and loop/merge until all have been retried
   		do{
			// Attempt the API call to retrieve sub lst
			$ytResponse="";
			try{$ytResponse = $this->getApi()->subscriptions->listSubscriptions('snippet', ['mine' => 'true', 'maxResults' => '50', 'pageToken' => $nextPageToken]);}
			catch(Google_Service_Exception $e) {
	     	   cldbgmsg("--Exception calling Youtube api");
	    	   //continue;
	    	   var_dump($e); die;
	        }
	        //var_dump($ytResponse);
	        //If there were subscriptions returned, merge them into a combined list
	        if(isset($ytResponse->items)) { $ytSubsList = array_merge($ytSubsList, $ytResponse->items); }

	        
	        //If there was a "nextPageToken" value returned (ie there were mre than 50 subscriptions), repeat
	    } while ( isset($ytResponse->nextPageToken) && ($nextPageToken = $ytResponse->nextPageToken));

		//var_dump($ytSubsList); die;
        return  $ytSubsList;

   	}








}//CrowdLuvYouTubeHelper
