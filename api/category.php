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
// иконка в галерее: https://fotki.sadrobot.su/_data/i/upload/2018/08/02/20180802172311-764235b9-th.jpg
// уменьшеное: https://fotki.sadrobot.su/_data/i/upload/2018/08/02/20180802172311-764235b9-me.jpg
// исходное: https://fotki.sadrobot.su/upload/2018/08/02/20180802172311-764235b9.jpg

// в базе данных:
// 13 | Predator-2.jpeg | 2018-08-02 17:23:11 | Predator-2 | 6960 | 98 | 1920 | 1040 | 2018-08-02 |
// ./upload/2018/08/02/20180802172311-764235b9.jpg | 0 | 764235b9ed0024e53c7798ad7015a41e | 1 | 0 |
// 2018-08-02 17:23:11

////////////////////////////////////
// usage examples:
// 
// root category:
// https://fotki.sadrobot.su/plugins/piwigo4blog/api/category.php
// 
// category with id=3:
// https://fotki.sadrobot.su/plugins/piwigo4blog/api/category.php?id=3
// 
// category with id=3, show max 8 images with offset 10:
// https://fotki.sadrobot.su/plugins/piwigo4blog/api/category.php?id=3&img_lim=8&img_offset=10


// @param cat_id: category id to show, show root category if not set
$cat_id = null;
if(isset($_GET['id'])) {
    $cat_id = pwg_db_real_escape_string($_GET['id']);
}

// @param img_lim: return not more than img_lim images (for paging),
//     return all if not set
$img_lim = null;
if(isset($_GET['img_lim'])) {
    $img_lim = pwg_db_real_escape_string($_GET['img_lim']);
}

// @param img_offset: return images starting from img_offset index (for paging),
//     start from the 1-st if not set.
//     Only used when $img_lim is set
$img_offset = null;
if(isset($_GET['img_offset'])) {
    $img_offset = pwg_db_real_escape_string($_GET['img_offset']);
}


////////////////////////////////////

// 
// Category info
// 
if ($cat_id != null) {
    $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id=".$cat_id;

    $result = pwg_query($sql);
    while($row = pwg_db_fetch_row($result)) {
        // $row - массив значений колонок
        $result_category = (object) [
            'id' => $row[0],
            'name' => $row[1],
            'representativePictureId' => $row[2],
            'idUppercat' => $row[3]
        ];
    }
} else {
    $result_category = (object) [
        'id' => null,
        'name' => 'ROOT',
        'representativePictureId' => null,
        'idUppercat' => null
    ];
}

// 
// Representative picture
// 
if($result_category->representativePictureId != null) {
    $sql = "SELECT id, name, file, path FROM ".IMAGES_TABLE." WHERE id=".$result_category->representativePictureId;
    
    $result = pwg_query($sql);
    $result_image;
    if($row = pwg_db_fetch_row($result)) {
        // $row - массив значений колонок
        $result_image = (object) [
            'id' => $row[0],
            'name' => $row[1],
            'file' => $row[2],
            'path' => $row[3],
            
            //
            'thumb' => $row[3],
            'orig' => $row[3]
        ];
    }
    
    $result_category->representativePicture = $result_image;
}

// 
// Child categories
// 
$sql="";
if ($cat_id != null) {
    $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id_uppercat=".$cat_id;
} else {
    $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id_uppercat IS NULL";
    //$sql = "SELECT id, id_uppercat FROM ".CATEGORIES_TABLE;
}

$result = pwg_query($sql);
$result_categories=array();
while($row = pwg_db_fetch_row($result)) {
    // $row - массив значений колонок
    $child_cat = (object) [
        'id' => $row[0],
        'name' => $row[1],
        'representativePictureId' => $row[2],
        'idUppercat' => $row[3]
    ];
    
    if($child_cat->representativePictureId != null) {
        $sql = "SELECT id, name, file, path FROM ".IMAGES_TABLE." WHERE id=".$child_cat->representativePictureId;
        
        $result1 = pwg_query($sql);
        $result_image;
        if($row1 = pwg_db_fetch_row($result1)) {
            // $row - массив значений колонок
            $result_image = (object) [
                'id' => $row1[0],
                'name' => $row1[1],
                'file' => $row1[2],
                'path' => $row1[3],
            
                //
                'thumb' => $row1[3],
                'orig' => $row1[3]
            ];
        }
        
        $child_cat->representativePicture = $result_image;
    }
        
    $result_categories[] = $child_cat;
}
$result_category->childCats = $result_categories;

// 
// Parent categories
// 
if ($cat_id != null) {
    // Путь до текущей категории - массив родительских категорий до категории верхнего уровня.
    // Было бы логично сделать в виде рекурсивного вызова SQL, но:
    
    // синтаксис WITH для рекурсивных CTE (common table expressions) появился в MariaDB в версии 10.2.2
    // https://mariadb.com/kb/en/with/
    // https://mariadb.com/kb/en/mariadb-1022-release-notes/
    // В Ubuntu 18.04 MariaDB версии 10.0.38 (=> рекурсивные запросы в ней не доступны)
    // SHOW VARIABLES LIKE '%version%'
    
    // Рекурсивные высовы: CTE (common table expressions) + WITH + UNION ALL
    // https://www.essentialsql.com/recursive-ctes-explained/
    // https://www.codeproject.com/Articles/683011/How-to-use-recursive-CTE-calls-in-T-SQL
    // https://www.w3schools.com/sql/sql_union.asp
    
    $result_categories=array();
    
    // Итак, рекурсия в SQL на MariaDB младше 10.2.2 не работает, поэтому сделаем в цикле
    $child_cat = $cat_id;
    while($child_cat != null) {
        // select * from piwigo_images where id in (select image_id from piwigo_image_category where category_id=3)
        $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id IN ".
            "(SELECT id_uppercat FROM ".CATEGORIES_TABLE." WHERE id=".$child_cat.")";
        
        $result = pwg_query($sql);
        // ожидаем только одну строку
        if($row = pwg_db_fetch_row($result)) {
            // $row - массив значений колонок
            $result_categories[] = (object) [
                'id' => $row[0],
                'name' => $row[1],
                'representativePictureId' => $row[2],
                'idUppercat' => $row[3]
            ];
            
            $parent_cat = $row[0];
        } else {
            $parent_cat = null;
        }
        $child_cat = $parent_cat;
    }
    
    $result_category->parentCats = array_reverse($result_categories);
} else {
    $result_category->parentCats = array();
}

// 
// Images count
// 

if ($cat_id != null) {
    $sql = "SELECT COUNT(id) FROM ".IMAGES_TABLE." WHERE id in (SELECT image_id FROM ".IMAGE_CATEGORY_TABLE." WHERE category_id=".$cat_id.")";
    
    $result = pwg_query($sql);
    $result_img_count = (int)pwg_db_fetch_row($result)[0];
    
    $result_category->img_count = $result_img_count;
} else {
    $result_category->img_count = 0;
}

// 
// Images
// 
if ($cat_id != null) {
    // select * from piwigo_images where id in (select image_id from piwigo_image_category where category_id=3)
    $sql = "SELECT id, name, file, path FROM ".IMAGES_TABLE." WHERE id in (SELECT image_id FROM ".IMAGE_CATEGORY_TABLE." WHERE category_id=".$cat_id.")";
    
    if($img_lim != null) {
        $sql = $sql." LIMIT ".$img_lim;
        
        if($img_offset != null) {
            $sql = $sql." OFFSET ".$img_offset;
        }
    }
    
    
    $result = pwg_query($sql);
    $result_images=array();
    while($row = pwg_db_fetch_row($result)) {
        // $row - массив значений колонок
        $result_images[] = (object) [
            'id' => $row[0],
            'name' => $row[1],
            'file' => $row[2],
            'path' => $row[3],
            
            //
            'thumb' => $row[3],
            'orig' => $row[3]
        ];
    }
    
    $result_category->images = $result_images;
} else {
    $result_category->images = array();
}

print(json_encode($result_category));
?>

