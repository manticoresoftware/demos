<?php
require_once 'common/config.php';
$indexes = 'movies';
$arr = array();
$q = trim($_GET['q']);
$field = "movie_title";
$aq = explode(' ', $q);
   $query = $q . '*';
$query = "@$field " . $query;
	$q = "SELECT $field as field,SNIPPET($field,".$conn->quote($query).",'before_match=<b style=\"color:red\">') as snippet_field FROM $indexes WHERE MATCH(".$conn->quote($query).") ORDER BY WEIGHT() DESC, cast_total_facebook_likes DESC LIMIT 0,10 ";
$res = $conn->query($q)->fetchAll();


$arr['data'] = [];

foreach ($res as $r) {
    $arr['data'][] = array('name' =>   utf8_encode($r['snippet_field'])  );
}
echo json_encode($arr);
exit();
