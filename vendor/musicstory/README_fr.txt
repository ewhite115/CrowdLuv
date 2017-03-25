
Music Story SDK Php base Documentation
----------------------------------------------------------


La SDK ci-jointe écrite par Music Story, permet une utilisation simplifiée de l'API Music Story (developers.music-story.com).
Elle fournit des fonctionalités de base telles que l'authentification Oauth, la recherche et la récupération d'Objet, de connecteurs, l'exploration des résultats.
Elle pourra également servir de socle au développement de librairies aux fonctionalités plus avancées.


1) Authentification

L'authentification se fait lors de l'instanciation de la classe MusicStoryAPI.
Il est nécessaire pour vous identifier de fournir vos clés "ConsumerKey" et  "ConsumerSecret". 
Si vous ne précisez pas vos tokens d'accès "AccessToken" et "TokenSecret", l'authentification se chargera d'en obtenir de nouveaux par la fonction "getToken()", fonction que vous pouvez également utiliser à tout moment qui réinitialisera les tokens en plus de les retourner.

exemples:

// inclusion de la classe
require_once ('MusicStoryAPI.class.php');
// instanciation & authentification sans tokens
$MSAPI=new MusicStoryApi('{ConsumerKey}', '{ConsumerSecret}');
// si besoin, changement des tokens et récupération
$tokens=$MSAPI->getToken();
$AccessToken=$tokens['access_token'];
$TokenSecret=$tokens['token_secret'];
// nouvelle éventuelle authentification avec tokens si volonté de réutiliser les mêmes
$MSAPI=new MusicStoryApi('{ConsumerKey}', '{ConsumerSecret}',$AccessToken,$TokenSecret);


2) Récupération d'un Objet par ID

La fonction qui permet cette opération est la fonction "get{Nom de l'objet}({id de l'objet})".
Veillez à orthographier correctement les fonctions comme suivant: "get" suivi du nom de l'objet avec la première lettre en majuscule.
Référez vous à la documentation en ligne (developers.music-story.com/developers) pour connaître la liste des objets Music Story disponibles.

exemple:

// récupération du genre d'ID 66
$genre=$MSAPI->getGenre(66);
// récupération de l'artiste d'ID 25967
$artist=$MSAPI->getArtist(25967);

L'objet renvoyé sera une instance de la classe "MusicStoryObject", classe héritant de "MusicStoryApi".

suite de l'exemple:

// récupérer le nom du genre 66
$name_genre=$genre->name;
// récupérer le pays de l'artiste d'ID 25967
$pays_artist=$artist->country;

Si vous voulez obtenir l'objet via un ID partenaire (Amazon,Itunes,Musicbrainz...), précisez en second paramètre de quel partenaire il s'agit.
Dans ce cas, c'est une liste qui est retournée, non pas un objet (voir ci-dessous)

exemple:

// récupérer la version d'album correspondant à l'ID B00DQVSTI0 chez Amazon
$release=$MSAPI->getRelease('B00DQVSTI0','amazon');

3) Recherche d'objets

La recherche d'objets s'effectue via la fonction "search{Nom de l'objet}({tableau associatif de filtres},{page désirée},{nombre de résultats par page})".
Les règles à respecter concernant le nom de la fonction sont les mêmes que précédemment.
Par défaut le nombre de résultats par page est de 10 et est fixé à un maximum de 100.

exemple:

// recherche des versions d'album dont le nom comprend "love" et qui sont de type "live"
$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'));
// si on veut la troisieme page des résultats retournés à raison de 50 par page
$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'),3,50);

Cette fonction retourne un Objet "MusicStoryObjects" au comportement d'itérateur, qui liste des objets de type "MusicStoryObject".
Certaines méthodes explicites sont mises à disposition comme les fonctions d'itérateur "current()", "next()", "prev()","hasNext()","hasPrev()", ainsi que les fonctions "hasNextPage()" et "hasPrevPage()", qui peuvent être utiles pour une exploration complète des résultats.

suite de l'exemple:

// on désire afficher le nom de toutes les version d'album dont le nom comprend "love" et qui sont de type "live"
$i=1;
do{
	$releases=$MSAPI->searchRelease(array('title'=>'love','type'=>'Live'),$i);
	foreach($releases as $release)
		echo "\n ".$release->title;
	$i++;
}
while($releases->hasNextPage());


4) Connecteurs

Il est possible d'effectuer une requête de type connecteur sur un "MusicStoryObject" en utilisant la fonction "get{Nom du connecteur}({tableau associatif de filtres},{page désirée},{nombre de résultats par page})".
Veillez à orthographier correctement les fonctions comme suivant: "get" suivi du nom du connecteur de l'objet avec la première lettre en majuscule.
Référez vous à la documentation en ligne (developers.music-story.com/developers) pour connaître la liste des connecteurs de l'objet disponibles.
Le résultat est du même type que pour une recherche.

exemple:

// affichage des discographies de tous les artistes français de genre ou d'influence "latino" (id 64)
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

