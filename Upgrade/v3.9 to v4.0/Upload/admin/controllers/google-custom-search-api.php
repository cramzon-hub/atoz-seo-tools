<?php
defined('APP_NAME') or die(header('HTTP/1.1 403 Forbidden'));

/**
 * @author Balaji
 * @name: Rainbow PHP Framework
 * @copyright 2025 ProThemes.Biz
 *
 */

$pageTitle = 'Google Custom Search API';
$subTitle = 'API Keys - Plagiarism Checker / Google Index Checker';
$fullLayout = 1; $footerAdd = true; $footerAddArr = array();

$resDa = mysqli_query($con,"SHOW TABLES LIKE 'google_custom_search_api'");
if(!(mysqli_num_rows($resDa) > 0)) {

    $sql = "CREATE TABLE `google_custom_search_api` (
  `id` int(11) NOT NULL,
  `api` text,
  `total` int(11) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    mysqli_query($con, $sql);

    $sql = 'ALTER TABLE `google_custom_search_api` ADD PRIMARY KEY (`id`)';
    mysqli_query($con, $sql);

    $sql = 'ALTER TABLE `google_custom_search_api` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
    mysqli_query($con, $sql);

}

//Get all API key informations
if($pointOut === 'api-keys'){
    // DB table to use
    $table = 'google_custom_search_api';

    // Table's primary key
    $primaryKey = 'id';

    // Database columns
    $columns = array(
        array( 'db' => 'id', 'dt' => 0 ),
        array( 'db' => 'api', 'dt' => 1 ),
        array( 'db' => 'total', 'dt' => 2),
        array( 'db' => 'available',  'dt' => 3),
    );

    $columns2 = array(
        array( 'db' => 'api', 'dt' => 0 ),
        array( 'db' => 'total', 'dt' => 1 ),
        array( 'db' => 'available',  'dt' => 2 ),
        array( 'db' => 'actions',  'dt' => 3),
    );


    // SQL connection information
    $sql_details = array(
        'user' => $dbUser,
        'pass' => $dbPass,
        'db'   => $dbName,
        'host' => $dbHost
    );

    echo json_encode(
        SSPGOOGLECSE::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $columns2 )
    );
    die();
}

//Delete Action
if($pointOut === 'delete'){
    $user_id = $args[0];
    if($args[0] != ''){
        $query = "DELETE FROM google_custom_search_api WHERE id=$user_id";
        $result = mysqli_query($con, $query);
        if (mysqli_errno($con)){
            $msg = errorMsgAdmin(mysqli_error($con));
        } else {
            header('Location:'.adminLink($controller,true));
            die();
        }
    }
}

//Profile Action
if($pointOut === 'add-key'){
    $subTitle = 'Add API Key';

    if(isset($_POST['api'])){

        $myValues = array_map_recursive(function ($item) use ($con) {
            return escapeTrim($con, $item);
        }, $_POST);

        $myValues['date'] = date('Y-m-d');

        if(insertToDbPrepared($con, 'google_custom_search_api', array('api' => $myValues['api'], 'total' => $myValues['total'], 'available' => $myValues['total'], 'date' => $myValues['date']))){
            $msg = errorMsgAdmin(mysqli_error($con));
        }else{
            header('Location:'.adminLink($controller,true));
            die();
        }
    }
}