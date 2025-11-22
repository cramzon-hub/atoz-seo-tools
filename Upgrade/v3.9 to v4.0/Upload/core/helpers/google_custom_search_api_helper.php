<?php

/**
 * @author Balaji
 * @name: A to Z SEO Tools - PHP Script
 * @copyright 2025 ProThemes.Biz
 *
 */


function googleCSESearch($searchQuery, $apiKey){

    $searchQuery = urlencode($searchQuery);
    $indexCount = 0;

    $cx = '017909049407101904515:uifmlkwue1w';
    $url = 'https://www.googleapis.com/customsearch/v1?key=' . $apiKey . '&cx=' . $cx . '&q=' . $searchQuery;

    $data = curlGET($url);
    $dataArr = json_decode($data, true);
    $outArr = array();
    $count = 0;

    if(isset($dataArr['searchInformation']['totalResults']))
        $indexCount = $dataArr['searchInformation']['totalResults'];

    if (isset($dataArr['items'])) {
        foreach ($dataArr['items'] as $item) {
            $outArr[$count]['title'] = $item['title'];
            $outArr[$count]['url'] = $item['formattedUrl'];
            $outArr[$count]['des'] = $item['snippet'];
            $count++;
        }
        return array('unique' => false, 'webs' => $outArr, 'indexCount' => $indexCount);
    }

    return array('unique' => true, 'indexCount' => $indexCount);
}

function resetGoogleCSEKeys($con){

    $date = date('Y-m-d');

    $result = mysqli_query($con, "SELECT id FROM google_custom_search_api WHERE date != '$date'");
    while ($row = mysqli_fetch_assoc($result)) {
        updateToDbPrepared($con, 'google_custom_search_api', array('available' => 99, 'date' => $date), array('id' => $row['id']));
    }
}

function getGoogleCSEKey($con){
    resetGoogleCSEKeys($con);
    $apiKey = '';
    $result = mysqli_query($con, "SELECT id,api,available FROM google_custom_search_api WHERE available > 0 LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    if(isset($row['available'])) {
        $row['available'] = intval($row['available']) - 1;
        $date = date('Y-m-d');
        updateToDbPrepared($con, 'google_custom_search_api', array('available' => $row['available'], 'date' => $date), array('id' => $row['id']));
        $apiKey = $row['api'];
    }
    return $apiKey;
}