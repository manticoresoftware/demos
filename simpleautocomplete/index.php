<?php

require_once 'common/config.php';
require_once 'common/functions.php';

$docs = array();
$start = 0;
$offset = 5;
$current = 1;
$field = 'movie_title';
$url = '';
$total_found = 0;
$total = 0;
$rows =[];
if (isset($_GET['query']) && trim($_GET['query']) != '') {
	$query = trim($_GET['query']);
    $query_match = '@movie_title '.EscapeString (trim($_GET['query']));
    $indexes = 'movies';
    if (isset($_GET['start'])) {
        $start = $_GET['start'];
        $current = $start / $offset + 1;
    }
	
    $stmt = $conn->prepare("SELECT *,SNIPPET($field,:snip,'before_match=<b style=\"color:red\">') as snippet_field FROM $indexes WHERE MATCH(:match)  LIMIT $start,$offset");
	$stmt->bindValue(':snip', $query_match, PDO::PARAM_STR);
    $stmt->bindValue(':match', $query_match, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $meta = $conn->query("SHOW META")->fetchAll();
    foreach ($meta as $m) {
        $meta_map[$m['Variable_name']] = $m['Value'];
    }
    $total_found = $meta_map['total_found'];
    $total = $meta_map['total'];
}
 
$title = 'Demo simple autocomplete on title';
include('common/template/layout.php');