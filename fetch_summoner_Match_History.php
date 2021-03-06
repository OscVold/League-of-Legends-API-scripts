<?php
set_time_limit(0);
$key = "Your API Key";
$intToTier = array('BRONZE','SILVER','GOLD','PLATINUM','DIAMOND');
$intToDivision = array('I','II', 'III', 'IV');
$DivisionIndex = 0;
$tierIndex = 0;
$page = 'Page_1';

while ($tierIndex < 5 && $DivisionIndex < 4) {
$tier = $intToTier[$tierIndex];
$division = $intToDivision[$DivisionIndex];
$response = array();
$ids = file_get_contents('./summoner_PUUID_data/summonerPUUID_'.$tier.'_'.$division.'_'.$page.'.json');
$formatids = json_decode($ids,true);
foreach ($formatids as $index => $value) {
$summonerPUUID = $value;
if(is_null($value)) {
   continue;
}
$url = "https://europe.api.riotgames.com/tft/match/v1/matches/by-puuid/" . $summonerPUUID . "/ids?count=40&api_key=" . $key ."";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
array_push($response, json_decode(curl_exec($ch),true));
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code == 403) {
  echo $url;
  echo "403";
  exit;
}
if ( $code == 429 ) {
  sleep(120);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FRESH_CONNECT,1);
  array_push($response, json_decode(curl_exec($ch),true));
  curl_close($ch);
}
}
$fp = fopen('Match_histories_'.$tier.'_'.$division.'_'.$page.'.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);
if($DivisionIndex != 3) {
  $DivisionIndex++;
} else {
  $tierIndex++;
  $DivisionIndex = 0;
}
}

 ?>
