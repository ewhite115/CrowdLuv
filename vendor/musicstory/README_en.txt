
Music Story SDK Php base Documentation
----------------------------------------------------------



The attached SDK written by Music Story allows a simplified use of the API Music Story (developers.music-story.com).

It provides basis functionalities such as the Oauth authentification, the search and retrieval of Objects, connectors, the exploration of results.
It can also serve as a basis for the development of libraries with more advanced functionalities.




1) Authentication

The authentication is done during the instancing of the MusicStoryAPI class.
It is necessary in order to identify yourself to provide your "ConsumerKey" and "ConsumerSecret" keys.
 
If you don't specify your access tokens: "AccessToken" and "TokenSecret", the authentication will take care of getting new ones by the "getToken()" function that you can use at any moment to reinitialise the tokens as well as retrieving them.



examples:


// inclusion of the class

require_once ('MusicStoryAPI.class.php');

// instantiating & authentication without tokens

$MSAPI=new MusicStoryApi('{ConsumerKey}', '{ConsumerSecret}');

// if needed, change of the tokens and retrieval

$tokens=$MSAPI->getToken();

$AccessToken=$tokens['access_token'];

$TokenSecret=$tokens['token_secret'];

// New eventual authentication with tokens if will of re-using the same ones

$MSAPI=new MusicStoryApi('{ConsumerKey}', '{ConsumerSecret}',$AccessToken,$TokenSecret);




2) Retrieval of an object by an ID


The function that allows this operation is the function "get{Name of the object}({id of the object})".

Make sure to spell correctly the functions as followed : "get" followed by the name of the object with the first letter in capital.

Refer to the online documentation (developers.music-story.com/developers) to know the list of Music Story objects available.

example:



// retrieval of the genre with the ID 66

$genre=$MSAPI->getGenre(66);

// retrieval of the artist with the ID 25967

$artist=$MSAPI->getArtist(25967);



The object sent back will be an instance with the "MusicStoryObject" class.

The data of the object retrieved will be accessible by its "data" property.

next part of example:


// retrieve the name of the genre with the ID 66

$name_genre=$genre->name;

// retrieve the country of the artist with the ID 25967

$pays_artist=$artist->country;



If you want to get the object via a partner ID (Amazon, Itunes, Musicbrainz ...), specify as second parameter which partner it is.


In this case a list is returned, not an object (see below)

example:


// get the album version corresponding to the ID B00DQVSTI0 at Amazon

$release=$MSAPI->getRelease('B00DQVSTI0','amazon');




3) Search for the objects

The search for objects is done via the method: "search{Name of the object}({associative table of filters},{desired page},{number of results per page})".


The rules to respect concerning the name of the method are the same as before.

By default the number of results per page is 10 and is fixed as a maximum of 100.

example:



// search for the album versions which includes the word "love" in the name and is of the "live" type

$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'));

// if you want the third page of results sent back at the rate of 50 per page

$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'),3,50);



This function sends back a "MusicStoryObjects" object with an iterator behaviour, that lists the objects of the "MusicStoryObject" type.

Some explicit methods are put at your disposition such as the iterator functions "current()", "next()", "prev()","hasNext()","hasPrev()", as well as the functions "hasNextPage()" et "hasPrevPage()", that can be useful for a complete exploration of the results.

next part of example:


// we seek to display the name of all the album versions which includes the name "love" and that are of the "live" type.

$i=1;

do{

	$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'),$i);

	foreach($releases as $release)

		echo "\n ".$release->title;

	$i++;

}
while($releases->hasNextPage());


4) 

Connectors


It is possible to carry out a request of the connector type on a "MusicStoryObject" by using the method "get{Name of the connector}({filters associative array},{desired page},{number of results per page})".

Make sure to correctly spell the functions as followed: "get" followed by the name of the connector of the object with the first letter in capital.


Refer to the online documentation (developers.music-story.com/developers) to know the list of connectors of the available object.

The result is of the same type as for a search request.

example:



// displaying the discographies of all the French artists of "latino" genre or influence (ID 64)
$genre=$MSAPI->getGenre(64);

$i=1;

do{

	$artistes=$genre->getArtists(array('country'=>'France'),$i);

	foreach($artistes as $artiste){

		echo "\n".$artiste->name;

		$j=1;

		do {

			$albums=$artiste->getAlbums(array('main'=>'1'),$j);

			foreach($albums as $album){

				echo "\n    ".$album->title;

			}

			$j++;

		}while($albums->hasNextPage());

	}

	$i++;

}while($artistes->hasNextPage());

echo "\n";
