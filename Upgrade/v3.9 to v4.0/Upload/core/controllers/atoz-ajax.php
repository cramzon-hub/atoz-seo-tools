<?php
defined('AJAX_CUS') or die(header('HTTP/1.1 403 Forbidden'));

/**
 * @author Balaji
 * @name: A to Z SEO Tools - PHP Script
 * @copyright 2025 ProThemes.Biz
 *
 */

//AJAX ONLY

//POST REQUEST Handler
if ($_SERVER['REQUEST_METHOD'] =='POST'){

    $authCode = '';
    $authError = true;
    $authData = array();
    if(isset($_POST['authcode'])){
        $authCode = raino_trim($_POST['authcode']);

        if(isset($_SESSION[N_APP.'sec'.$authCode])){
            $authData = $_SESSION[N_APP.'sec'.$authCode];
            if(time() <= $authData[1]){
                $authError = false;
                $authData[0] = $authData[0] +1;
                $_SESSION[N_APP.'sec'.$authCode] = $authData;
            }
        }
    }
    //if($authError)
    //die('Invalid authentication code!');

    //Blog Ping
    if(isset($_POST['blogPing'])){
        if(isset($_POST['pingUrl'])){
            $pingUrl = raino_trim($_POST['pingUrl']);
            $myBlogName = raino_trim($_POST['blogName']);
            $myBlogUrl = raino_trim($_POST['blogUrl']);
            $myBlogUpdateUrl = raino_trim($_POST['blogUpdateUrl']);
            $myBlogRSSFeedUrl = raino_trim($_POST['blogRssUrl']);
            $errorMsg = $lang['221'];
            $outData = xmlRpcPing($pingUrl,$myBlogName,$myBlogUrl,$myBlogUpdateUrl,$myBlogRSSFeedUrl,'Thanks for the ping.');
            echo $outData["message"];
            die();
        }else{
            echo $lang['4'];
            die();
        }
    }

    //Google Cache Checker
    if(isset($_POST['googleCache'])){
        if(isset($_POST['sitelink'])) {
            $siteLink = raino_trim($_POST['sitelink']);

            if(substr($siteLink, 0, 7) !== 'http://' && substr($siteLink, 0, 8) !== 'https://')
                $siteLink = 'http://'.$siteLink;

            $data = googleCache($siteLink);
            if ($data != "") {
                echo $data;
                die();
            }
            else {
                echo '0';
                die();
            }

        }
        else {
            echo '0';
            die();
        }
    }

    //SEOMoz Page / Domain Authority Checker
    if(isset($_POST['mozAuthority'])){
        if(isset($_POST['sitelink'])) {
            $moz_access_id = $moz_secret_key = '';
            $result = mysqli_query($con,"SELECT moz_access_id,moz_secret_key FROM pr24 where id=1");
            $resArr = mysqli_fetch_assoc($result);
            extract($resArr);

            if($moz_access_id == null || $moz_access_id== '')
                $error = $lang['209'];

            if($moz_secret_key == null || $moz_secret_key== '')
                $error = $lang['210'];

            if (!isset($error)) {
                $siteLink = raino_trim($_POST['sitelink']);
                if($siteLink != ""){
                    $siteLink = "http://".clean_with_www($siteLink);
                    if (!filter_var($siteLink, FILTER_VALIDATE_URL) === false) {
                        $my_url = parse_url($siteLink);
                        $host = $my_url['host'];
                        if(isset($_POST['domainAuthority'])) {
                            $seoMoz = seoMoz($host,$moz_access_id,$moz_secret_key);
                            $domainAuth = $seoMoz[2];

                            if ($domainAuth != "") {
                                echo $domainAuth;
                                die();
                            }

                        } elseif(isset($_POST['pageAuthority'])){
                            $seoMoz = seoMoz(clean_with_www($siteLink),$moz_access_id,$moz_secret_key);
                            $pageAuth = $seoMoz[1];
                            if ($pageAuth != "") {
                                echo $pageAuth;
                                die();
                            }

                        }
                    }
                }
            }
        }
        echo '0';
        die();
    }

    //Keyword Position Checker 
    if(isset($_POST['keywordPos'])){

        if(isset($_POST['keyword'])) {
            $searchKeyword = raino_trim($_POST['keyword']);
            if($searchKeyword == "" || $searchKeyword == null){
                echo $lang['168'].'::|::'.$lang['168'];
                die();
            }
            $searchUrl = "http://".clean_with_www(raino_trim($_POST['searchUrl']));
            $keyPos = raino_trim($_POST['pos']);
            $yahooDomain = "yahoo.com";
            $googleDomain = "google.com";
            $googleRes = googleRank($searchUrl,$searchKeyword,$keyPos,$googleDomain);
            $yahooRes = dooRank($searchUrl,$searchKeyword,$keyPos,$yahooDomain);
            $resultData = ($googleRes[1] == "" ? $lang['167']." $keyPos" : ordinal($googleRes[1])." ".$lang['169']). "::|::". ($yahooRes[1] == "" ? $lang['167']." $keyPos" : ordinal($yahooRes[1])." ".$lang['169']);
            echo $resultData;
            die();
        }else{
            echo $lang['4'].'::|::'.$lang['4'];
            die();
        }
    }

    //Backlink Maker
    if(isset($_POST['backlink'])) {
        if(isset($_POST['sitelink'])) {
            $siteLink = raino_trim($_POST['sitelink']);
            $data = simpleCurlGET($siteLink);
            echo '1';
        } else {
            echo '0';
        }
        die();
    }

    //XML Sitemap Generator
    if(isset($_POST['sitemap'])){
        $my_url = raino_trim($_POST['url']);

        if(substr($my_url, 0, 7) !== 'http://' && substr($my_url, 0, 8) !== 'https://')
            $my_url = 'http://'.$my_url;

        $outData = genSiteLinks($my_url);

        if(!is_array($outData))
            $outData = array();

        $outDataCount = count($outData);
        $loop = 1;
        foreach ($outData as $link){
            if ($loop == $outDataCount){
                $genData .=  Trim($link);
            }else{
                $genData .=  Trim($link) . PHP_EOL;
            }
            $loop++;
        }
        echo $outDataCount.'::|::'.$genData;
        die();
    }

    //Plagiarism Checker
    if(isset($_POST['plagiarism'])) {

        $type= (int)raino_trim($_POST['type']);
        $check_data = stripslashes(Trim($_POST['data']));

        if($type == 3){
            //Google CSE API (New)
            $check_data = str_replace("�", "'", $check_data);
            $check_data = strtolower($check_data);
            $gcheck_data = urlencode('"' . $check_data . '"');
            if(googleCSEQueryCheck($gcheck_data)){
                die('1'); //Matched
            }else{
                die('2'); //No Match Found
            }
        } elseif($type == 2) {
            //ProThemes Plagiarism API
            $url = 'http://googleapi.prothemes.biz/api.php?data='.urlencode($check_data).'&domain='.$_SERVER['HTTP_HOST'].'&code='.$item_purchase_code;
            $palData = getMyData($url);
            echo $palData;
            die();
        } elseif($type == 1) {
            //Google Direct Search
            $search_keyword = str_replace("�", "'", $check_data);
            $search_keyword = '"'.$search_keyword.'"';

            $apiKey = getGoogleCSEKey($con);
            $output = googleCSESearch($search_keyword, $apiKey);

            if($output['unique']){
                //No Match Found
                die('2');
            }else{
                //Matched
                die('1');
            }
        }
    }
}


//Get Website Screenshot
if(isset($_GET['getWebSnap'])) {
    $site = raino_trim($_GET['site']);
    $token = raino_trim($_GET['token']);
    $tokenKey = raino_trim($_SESSION['getWebSnap']);
    if(!isset($token))
        die();
    if($token==null||$token=="")
        die();
    if($tokenKey != $token)
        die();
    $site = clean_url($site); $site = "http://$site";
    $site = parse_url(Trim($site));
    $host = $site['host'];
    if (file_exists(HEL_DIR."site_snapshot/$host.jpg")) {
        $file = HEL_DIR."site_snapshot/$host.jpg";
    }else {
        $file = HEL_DIR."site_snapshot/no-preview.png";
    }
    header("Content-type: image/png");
    readfile("$file");
    die();
}


//Upgrade / Add Ons Installation - Only Authenticated Users
if(isset($_GET['upgrade'])){

    if(isset($_GET['itemCode'])){
        $itemCode = raino_trim($_GET['itemCode']);
        if($itemCode == $item_purchase_code){
            //Delete the Index!
            unlink(ROOT_DIR."index.php");
            //Delete the old files if needed!
            if(isset($_GET['delUy'])){
                $delUy = raino_trim($_GET['delUy']);
                unlink($delUy);
            }
        }
        //Finished  - Start the Installation
        die('1');
    }
    if(isset($_GET['authCode'])){
        $userAuthCode = raino_trim($_GET['authCode']);
        if($authCode == $userAuthCode){
            //Delete the Index!
            unlink(ROOT_DIR."index.php");
            //Delete the old files if needed!
            if(isset($_GET['delUy'])){
                $delUy = raino_trim($_GET['delUy']);
                unlink($delUy);
            }
            //Finished  - Start the Installation
            die('1');
        }
    }
    //Unknown Error
    echo "0";
    die();
}

if(isset($_POST['getMobileFriendly'])){

    $my_url = raino_trim($_POST['url']);
    $my_url = clean_with_www($my_url);

    $myHost = ucfirst($my_url);
    $my_url = "http://$my_url";

    $jsonData = getMobileFriendly($my_url);
    $mobileScore = intval($jsonData['score']);
    $isMobileFriendly = $jsonData['passed'];
    $screenData = $jsonData['screenshot'];

    if(!isset($error)){
        if($isMobileFriendly){
            $isMobileFriendlyMsg = $lang['AD82'];
            $isMobileFriendlyColor = "2cc36b";
        }
        else{
            $isMobileFriendlyMsg = $lang['AD83'];
            $isMobileFriendlyColor = "c0392b";
        }

        echo '              <div class="mobileRes">
                        <br />         
                         <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td><strong>'.$lang['24'].'</strong></td>
                                <td>'.$myHost.'</td>
                            </tr>
                            <tr>
                                <td><strong>'.$lang['69'].'</strong></td>
                                <td style="color: #'.$isMobileFriendlyColor.'; font-size: 18px;">'.$isMobileFriendlyMsg.'</td>
                            </tr>
                            <tr>
                                <td><strong>'.$lang['AD84'].'</strong></td>
                                <td>      
                                <div><input type="text" data-readonly="true" data-fgcolor="#'.$isMobileFriendlyColor.'" data-height="90" data-width="90" value="'.$mobileScore.'" class="knob" readonly="readonly" style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px none; background: none repeat scroll 0% 0% transparent; font: bold 18px Arial; text-align: center; color: rgb(60, 141, 188); padding: 0px;" /></div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>'.$lang['AD85'].'</strong></td>
                                <td><img src="data:image/jpeg;base64,'.$screenData.'" /></td>
                            </tr>
                        </tbody></table>
                        </div>';
        die();
    }else{
        echo '
        <div class="mobileRes">
        <br />   
        <div class="alert alert-error">
        <strong>Alert!</strong> '.$lang['97'].'
        </div>
        </div>';
        die();
    }

}