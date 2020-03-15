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


if (isset($_GET['id'])) {
    $cat = pwg_db_real_escape_string($_GET['id']);
    
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
    $child_cat = $cat;
    while($child_cat != null) {
        $sql = "SELECT id, name, representative_picture_id, id_uppercat FROM ".CATEGORIES_TABLE." WHERE id IN ".
            "(SELECT id_uppercat FROM ".CATEGORIES_TABLE." WHERE id=".$child_cat.")";
        
        $result = pwg_query($sql);
        // ожидаем только одну строку
        if($row = pwg_db_fetch_row($result)) {
            // $row - массив значений колонок
            $result_categories[] = (object) [
                'id' => $row[0],
                'name' => $row[1],
                'representative_picture_id' => $row[2],
                'id_uppercat' => $row[3]
            ];
            
            $parent_cat = $row[0];
        } else {
            $parent_cat = null;
        }
        $child_cat = $parent_cat;
    }
    
    print(json_encode(array_reverse($result_categories)));
}

?>

