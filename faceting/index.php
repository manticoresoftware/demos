<?php
include 'common/config.php';
include 'common/functions.php';

$docs = array();
$start = 0;
$offset = 10;
$current = 1;
$url = '';
$rows = [];
$faceted = ["title_year", "content_rating", "director_name", "imdb_score"];
$faceted_type = array('title_year' => 'range', 'content_rating' => 'string', 'director_name' => 'string', 'imdb_score' => 'range');
$faceted_map = array('title_year' => 'year', 'content_rating' => 'content_rating', 'director_name' => 'director_name', 'imdb_score' => 'score');
$faceted_expr = array(
    "title_year" => "FACET INTERVAL(title_year,1910,1920,1930,1940,1950,1960,1970,1980,1990,2000,2010) as year ORDER BY title_year DESC",
    "content_rating[]" => "FACET content_rating ORDER BY COUNT(*) DESC LIMIT 10",
    "director_name" => "FACET director_name ORDER BY count(*) DESC LIMIT 10",
    "imdb_score" => "FACET INTERVAL(imdb_score,1.0,2.0,3.0,4.0,5.0,6.0,7.0,8.0,9.0) AS score ORDER BY score DESC"
);

$faceted_name = array(
    "title_year" => "Year",
    "content_rating" => "Rating",
    "director_name" => "Director's Name",
    "imdb_score" => "IMDB score"
);
$facet_string = implode(' ', $faceted_expr);
$filters = [];
foreach ($faceted as $f) {
    if (isset($_GET[$f])) {
        switch ($f) {
            case 'title_year':
                $filters[] = "$f BETWEEN " . (1900 + 10 * $_GET[$f]) . ' AND ' . (1900 + 10 * $_GET[$f] + 9);
                break;
            case 'imdb_score':
                $filters[] = "$f BETWEEN " . $_GET[$f] . ' AND ' . ($_GET[$f] + 0.9);
                break;
            case 'content_rating':
                $filters[] = "$f = " . $conn->quote($_GET[$f]);
                break;
            default:
                $filters[] = "$f = " . $conn->quote($_GET[$f]);
        }
    }
}

$query = (isset($_GET['query'])) ? trim($_GET['query']) : '';
$indexes = 'movies';
if (isset($_GET['start'])) {
    $start = $_GET['start'];
    $current = $start / $offset + 1;
}

$stmt = $conn->prepare("SELECT * FROM $indexes WHERE MATCH(:match) " .
    (count($filters) > 0 ? ' AND ' . implode(' AND ', $filters) : '')
    . " LIMIT $start,$offset OPTION ranker=sph04,field_weights=(title=100,content=1) " . $facet_string);
$stmt->bindValue(':match', EscapeString($query), PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll();

$facets = array();
$i = 0;
while ($stmt->nextRowset()) {
    $facets[$faceted[$i]] = $stmt->fetchAll();
    $i++;
}

$meta = $conn->query("SHOW META")->fetchAll();
foreach ($meta as $m) {
    $meta_map[$m['Variable_name']] = $m['Value'];
}
$total_found = $meta_map['total_found'];
$total = $meta_map['total'];

$title = 'Faceting demo';
include('common/template/layout.php');