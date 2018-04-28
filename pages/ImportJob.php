<?php 



$clRequestInformation->clFacebookHelper->setAsAppSession();
$clRequestInformation->clSpotifyHelper->setAsClientCredentialSession();



/**
 * Run the Metadata Retrieval Job
 *
 */
 $clRequestInformation->clModel->runMetaDataRetrievalJob();





/**
 * Run the Event-Import Job
 *   Invoked on every page load - runs once every N minutes to import events from
 *     FB, BIT, Spotify
 */
$clRequestInformation->clModel->runEventImportJob( 1356998400  );
//1515542400



echo "CrowdLuv Diagnostics...<br>";
file_put_contents('../crowdluvdata/log/eventimportjoblog_'.date("j.n.Y").'.txt', "CrowdLuv Diagnostics...<br>" . time() . PHP_EOL, FILE_APPEND);
foreach($clResponseInformation->clDiagnostics->debugMessages as $dbgmsg)
  {
    echo $dbgmsg . "<br>";
    file_put_contents('../crowdluvdata/log/eventimportjoblog_'.date("j.n.Y").'.txt', $dbgmsg . PHP_EOL, FILE_APPEND);

  }

echo "done";



?>

