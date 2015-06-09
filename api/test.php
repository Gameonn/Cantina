<?php

$categories = array(
    array('id' => 1,  'parent' => 0, 'name' => 'Category A'),
    array('id' => 2,  'parent' => 0, 'name' => 'Category B'),
    array('id' => 3,  'parent' => 0, 'name' => 'Category C'),
    array('id' => 4,  'parent' => 0, 'name' => 'Category D'),
    array('id' => 5,  'parent' => 0, 'name' => 'Category E'),
    array('id' => 6,  'parent' => 2, 'name' => 'Subcategory F'),
    array('id' => 7,  'parent' => 2, 'name' => 'Subcategory G'),
    array('id' => 8,  'parent' => 3, 'name' => 'Subcategory H'),
    array('id' => 9,  'parent' => 4, 'name' => 'Subcategory I'),
    array('id' => 10, 'parent' => 9, 'name' => 'Subcategory J'),
    array('id' => 11, 'parent' => 10, 'name' => 'Subcategory K')
);
$report=categoriesToTree($categories);
//print_r($report);
function categoriesToTree(&$categories) {

    $map = array(
        0 => array('subcategories' => array())
    );

    foreach ($categories as &$category) {
        $category['subcategories'] = array();
        $map[$category['id']] = &$category;
    }

    foreach ($categories as &$category) {
        $map[$category['parent']]['subcategories'][] = &$category;
    }

    return $map[0]['subcategories'];

}
