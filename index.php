<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('vendor/autoload.php');
require_once("config.php");

session_start();

$client = new Google_Client();
$client->setClientId($config['oauth']['CLIENT_ID']);
$client->setClientSecret($config['oauth']['CLIENT_SECRET']);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
    FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

$youtube = new Google_Service_YouTube($client);

if (isset($_GET['code'])) {
    if (strval($_SESSION['state']) !== strval($_GET['state'])) {
        die('The session state did not match.');
    }
    
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    header('Location: ' . $redirect);
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  $xamfy_videos = json_decode(file_get_contents($config['xamfy_api']['url']));
  foreach($xamfy_videos as $xamfy_video) {
      try{
          // THIS IS AN EXAMPLE VIDEO ID! HERE'S GOING TO BE THE LOOP
          if ($xamfy_video->youtube_id == "GvhirnDgclk") {
                        
              // Call the API's videos.list method tho retrieve the video resource.
              $listResponse = $youtube->videos->listvideos("snippet",
                array('id' => $xamfy_video->youtube_id));
            
              // If $listResponse is empty, the specified video was not found.
              if(empty($listResponse)) {
                  $htmlBody .= sprintf("<h3> Can't find a video with video id: %s</h3>", $videoId);
              } else {
                  // Since the request specified a video ID, the response only contains one video resource.
                  $video = $listResponse[0];
                  $videoSnippet = $video['snippet'];
                  
                  $videoSnippet['title'] = $xamfy_video->composed_title;
                  $videoSnippet['description'] = "Mehr Infos zum Video: http://xamfy.de/video"
                    . $xamfy_video->xamfy_id . "\n\n";
                  $videoSnippet['description'] .= "+++++\n";
                  $videoSnippet['description'] .= "\n";
                  $videoSnippet['description'] .= $xamfy_video->description . "\n";
                  $videoSnippet['description'] .= "\n";
                  
                  $videoSnippet['description'] .= "Spieler:\n";
                  foreach($xamfy_video->spieler as $spieler) {
                      $videoSnippet['description'] .= "- " . $spieler ."\n";
                  }
                  
                  $videoSnippet['description'] .= "\n";
                  $videoSnippet['description'] .= "+++++\n";
                  $videoSnippet['description'] .= "\n";
                  $videoSnippet['description'] .= "Offizielle Website: http://xamfy.de\n";
                  $videoSnippet['description'] .= "Facebook: http://xamfy.de/facebook\n";
                  $videoSnippet['description'] .= "Twitter: http://xamfy.de/twitter\n";
                  $videoSnippet['description'] .= "Googleplus: http://xamfy.de/googleplus\n";
                  $videoSnippet['description'] .= "\n";
                  $videoSnippet['description'] .= "+++++\n";
                  $videoSnippet['description'] .= "\n";
                  $videoSnipper['description'] .= "Videos von Xamfy wird mit 100% Recycling-Pixel aus rein gentechnikfreier, ökologischer Freiland-Bodenhaltung erstellt.";
                  
                  $updateResponse = $youtube->videos->update("snippet", $video);
                  
                  $htmlBody = "<h3>Successfully updated</h3>";
              }
          }
      } catch (Google_ServiceException $e) {
          $htmlBody .=sprintf('<p>A service error occured: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
      } catch (Google_Exception $e) {
          $htmlBody .=sprintf('<p>An client error occured: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
      }
  }
} else {
    // If the user hasn't authorized the app, initiate the OAuth flow
    $state = mt_rand();
    $client->setState($state);
    $_SESSION['state'] = $state;
    
    $authUrl = $client->createAuthUrl();
    $htmlBody = '<h3>Authorization Required</h3>
    <p>You need to <a href="'.$authUrl.'">authorize access</a> before proceeding.</p>
    ';
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Videos Updated</title>
    </head>
    <body>
        <?php print $htmlBody ?>    
    </body>
</html>