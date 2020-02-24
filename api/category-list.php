<?php

// если мы в plugins/piwigo4blog/api
// если просто в plugins/piwigo4blog, то на один dirname меньше
define('PHPWG_ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))).'/');


// the common.inc.php file loads all the main.inc.php plugins files
include_once(PHPWG_ROOT_PATH.'include/common.inc.php' );


// select column_name from information_schema.columns where table_name='piwigo_categories'
//$sql = "select column_name from information_schema.columns where table_name='".CATEGORIES_TABLE."'";
// id name id_uppercat comment dir rank status site_id visible representative_picture_id uppercats
// commentable global_rank image_order permalink lastmodified


// select column_name from information_schema.columns where table_name='piwigo_image_category'
// $sql = "select column_name from information_schema.columns where table_name='".IMAGE_CATEGORY_TABLE."'";
// image_id category_id rank


// select column_name from information_schema.columns where table_name='piwigo_images'
//$sql = "select column_name from information_schema.columns where table_name='".IMAGES_TABLE."'";
// id file date_available date_creation name comment author hit filesize width height coi representative_ext date_metadata_update
// rating_score path storage_category_id level md5sum added_by rotation latitude longitude lastmodified


$sql="";
if (isset($_GET['id'])) {
    // select * from piwigo_images where id in (select image_id from piwigo_image_category where category_id=3)
    $cat = pwg_db_real_escape_string($_GET['id']);
    
    $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id_uppercat=".$cat;
} else {
    $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id_uppercat IS NULL";
    //$sql = "SELECT id, id_uppercat FROM ".CATEGORIES_TABLE;
}

$result = pwg_query($sql);
$result_categories=array();
while($row = pwg_db_fetch_row($result)) {
    // $row - массив значений колонок
    $result_categories[] = (object) [
        'id' => $row[0],
        'name' => $row[1],
        'representative_picture_id' => $row[2],
        'id_uppercat' => $row[3]
    ];
}

print(json_encode($result_categories));

?>

