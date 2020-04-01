<?php
include 'common/config.php';
include 'common/functions.php';

$docs = array();
$start = 0;
$offset = 10;
$current = 1;
$url = '';
$rows = [];
$snippet_fields = array('movie_title', 'plot_keywords', 'director_name', 'actor_1_name', 'actor_2_name', 'actor_3_name');
if (isset($_GET['query']) && trim($_GET['query']) != '') {
    $query = trim($_GET['query']);
    $query_match = '@movie_title ' . EscapeString(trim($_GET['query']));
    $indexes = 'movies';
    if (isset($_GET['start'])) {
        $start = $_GET['start'];
        $current = $start / $offset + 1;
    }
    foreach ($snippet_fields as $s) {
        $snips[] = "SNIPPET($s,'" . $query . "','before_match=<b style=\"color:red\">') as snippet_" . $s;
    }
    $rows = $conn->query("SELECT *," . implode(",", $snips) . " FROM $indexes WHERE MATCH(" . $conn->quote($query_match) . ")  LIMIT $start,$offset OPTION ranker=sph04,field_weights=(title=100,content=1)")->fetchAll();
    $meta = $conn->query("SHOW META")->fetchAll();
    foreach ($meta as $m) {
        $meta_map[$m['Variable_name']] = $m['Value'];
    }
    $total_found = $meta_map['total_found'];
    $total = $meta_map['total'];
    if ($total_found == 0) {
        foreach ($meta as $m) {
            if (preg_match('/keyword\[\d+]/', $m['Variable_name'])) {
                preg_match('/\d+/', $m['Variable_name'], $key);
                $key = $key[0];
                $words[$key]['keyword'] = $m['Value'];
            }
            if (preg_match('/docs\[\d+]/', $m['Variable_name'])) {
                preg_match('/\d+/', $m['Variable_name'], $key);
                $key = $key[0];
                $words[$key]['docs'] = $m['Value'];
            }
        }
        $didyoumean = false;
        $didyoumeanquery = [];
        foreach ($words as $i => $keyword) {
            if ($keyword['docs'] == 0) {
                $qs = $conn->prepare("CALL SUGGEST(:keyword,'movies')");
                $qs->bindValue(':keyword', $keyword['keyword'], PDO::PARAM_STR);
                $qs->execute();
                $rows = $qs->fetchAll();
                if (count($rows) > 0) {
                    $keywords[$i]['keyword'] = $rows[0]['suggest'];
                    $didyoumeanquery[] = $rows[0]['suggest'];
                    $didyoumean = true;
                }
            } else {
                $didyoumeanquery[] = $keyword['keyword'];
            }
        }
        $didyoumeanstring = implode(" ", $didyoumeanquery);
        if ($didyoumean == true) {
            $snips = array();
            foreach ($snippet_fields as $s) {
                $snips[] = "SNIPPET($s,'" . implode(" ", $didyoumeanquery) . "','before_match=<b style=\"color:red\">') as snippet_" . $s;
            }
            $rows = $conn->query("SELECT *," . implode(",", $snips) . " FROM movies WHERE MATCH('" . implode(" ", $didyoumeanquery) . "')")->fetchAll();
            $meta = $conn->query("SHOW META")->fetchAll();
            foreach ($meta as $m) {
                $meta_map[$m['Variable_name']] = $m['Value'];
            }
            $total_found = $meta_map['total_found'];
            $total = $meta_map['total'];

        }
    }

}

$title = 'Demo did you mean';
include('common/template/layout.php');
