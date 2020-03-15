<?php

// если мы в plugins/piwigo4blog/api
// если просто в plugins/piwigo4blog, то на один dirname меньше
define('PHPWG_ROOT_PATH', dirname(dirname(dirname(dirname(__FILE__)))).'/');

// the common.inc.php file loads all the main.inc.php plugins files
include_once(PHPWG_ROOT_PATH.'include/common.inc.php' );


// select column_name from information_schema.columns where table_name='piwigo_images'
//$sql = "select column_name from information_schema.columns where table_name='".IMAGES_TABLE."'";
// id file date_available date_creation name comment author hit filesize width height coi representative_ext date_metadata_update
// rating_score path storage_category_id level md5sum added_by rotation latitude longitude lastmodified

// select column_name from information_schema.columns where table_name='piwigo_categories'
//$sql = "select column_name from information_schema.columns where table_name='".CATEGORIES_TABLE."'";
// id name id_uppercat comment dir rank status site_id visible representative_picture_id uppercats
// commentable global_rank image_order permalink lastmodified

// select column_name from information_schema.columns where table_name='piwigo_image_category'
// $sql = "select column_name from information_schema.columns where table_name='".IMAGE_CATEGORY_TABLE."'";
// image_id category_id rank

// https://fotki.sadrobot.su/picture.php?/13/
// уменьшеное: https://fotki.sadrobot.su/_data/i/upload/2018/08/02/20180802172311-764235b9-me.jpg
// исходное: https://fotki.sadrobot.su/upload/2018/08/02/20180802172311-764235b9.jpg

// в базе данных:
// 13 | Predator-2.jpeg | 2018-08-02 17:23:11 | Predator-2 | 6960 | 98 | 1920 | 1040 | 2018-08-02 |
// ./upload/2018/08/02/20180802172311-764235b9.jpg | 0 | 764235b9ed0024e53c7798ad7015a41e | 1 | 0 |
// 2018-08-02 17:23:11


if (isset($_GET['category'])) {
    $cat = pwg_db_real_escape_string($_GET['category']);
    
    // select * from piwigo_images where id in (select image_id from piwigo_image_category where category_id=3)
    $sql = "SELECT id, name, file, path FROM ".IMAGES_TABLE." WHERE id in (select image_id FROM ".IMAGE_CATEGORY_TABLE." WHERE category_id=".$cat.")";
    
    $result = pwg_query($sql);
    $result_images=array();
    while($row = pwg_db_fetch_row($result)) {
        // $row - массив значений колонок
        $result_images[] = (object) [
            'id' => $row[0],
            'name' => $row[1],
            'file' => $row[2],
            'path' => $row[3]
        ];
    }

    print(json_encode($result_images));
} else if(isset($_GET['img'])) {
    // select * from piwigo_images where id=2
    $img = pwg_db_real_escape_string($_GET['img']);
    $sql = "SELECT id, name, file, path FROM ".IMAGES_TABLE." WHERE id=".$img;
    
    $result = pwg_query($sql);
    $result_image;
    if($row = pwg_db_fetch_row($result)) {
        // $row - массив значений колонок
        $result_image = (object) [
            'id' => $row[0],
            'name' => $row[1],
            'file' => $row[2],
            'path' => $row[3]
        ];
    }

    print(json_encode($result_image));
    
} else {
    // Fallback behaviour goes here
}

?>

