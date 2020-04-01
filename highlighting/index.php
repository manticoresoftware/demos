<?php
include 'common/config.php';
include 'common/functions.php';

$start = 0;
$offset = 20;
$max_query_terms = isset($_POST['max_query_terms'])??0;
if ($max_query_terms > 256) {
    $max_query_terms = 256;
}
$stopwords = file('en', FILE_IGNORE_NEW_LINES);
$stopwords_enabled = $_POST['stopwords_enabled'];
$minimum_should_match = $_POST['minimum_should_match'];
$cutoffgrey = $_POST['cutoffgrey'];

$tfidf_boost = $_POST['tfidf_boost'];
$index = 'news';
$field = $_POST['field'];

$boolean = $_POST['boolean'];
if (isset($_POST['start'])) {
    $start = $_POST['start'];
    $current = $start / $offset + 1;
}
$query = $_POST['query'];
$highlight = array(
    'before_match' => $_POST['before_match'],
    'after_match' => $_POST['after_match'],
    'chunk_separator' => $_POST['chunk_separator'],
    'field_separator' => $_POST['field_separator'],
    'limit' => $_POST['limit'],
    'around' => $_POST['around'],
    'exact_phrase' => $_POST['exact_phrase'],
    'use_boundaries' => $_POST['use_boundaries'],
    'weight_order' => $_POST['weight_order'],
    'query_mode' => $_POST['query_mode'],
    'force_all_words' => $_POST['force_all_words'],
    'limit_passages' => $_POST['limit_passages'],
    'limit_words' => $_POST['limit_words'],
    'start_passage_id' => $_POST['start_passage_id'],
    'html_strip_mode' => $_POST['html_strip_mode'],
    'allow_empty' => $_POST['allow_empty'],
    'passage_boundary' => $_POST['passage_boundary'],
    'emit_zones' => $_POST['emit_zones'],
    'force_passages' => $_POST['force_passages'],


);
$highlight = implode(',', $highlight);

if ($_POST['send']) {


    $stmt = $conn->prepare("SELECT id,HIGHLIGHT({$highlight}) as highlight FROM $index WHERE MATCH(:mlk) LIMIT $start,$offset");
    $stmt->bindValue(':mlk', $mlk_query, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $meta = $conn->query("SHOW META")->fetchAll();
    foreach ($meta as $m) {
        $meta_map[$m['Variable_name']] = $m['Value'];
    }
    $total_found = $meta_map['total_found'];
    $total = $meta_map['total'];
    $sphinxql = "SELECT id,HIGHLIGHT({$highlight}) as highlight FROM $index WHERE MATCH(:mlk) LIMIT $start,$offset";
} else {
    $rows = $conn->query("SELECT *,rand() as r FROM $index ORDER BY r ASC")->fetchAll();
    $meta = $conn->query("SHOW META")->fetchAll();
    foreach ($meta as $m) {
        $meta_map[$m['Variable_name']] = $m['Value'];
    }
    $total_found = $meta_map['total_found'];
    $total = $meta_map['total'];
}
$title = "More Like This";
include('common/template/layout.php');

