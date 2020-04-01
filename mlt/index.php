<?php
include 'common/config.php';
include 'common/functions.php';

$start = 0;
$offset = 20;
$max_query_terms = $_POST['max_query_terms']??256;
if ($max_query_terms > 256) {
    $max_query_terms = 256;
}
$stopwords = file('common/en', FILE_IGNORE_NEW_LINES);
$stopwords_enabled = $_POST['stopwords_enabled']??1;
$minimum_should_match = $_POST['minimum_should_match']??0.2;
$cutoffgrey = $_POST['cutoffgrey']??0.5;

$tfidf_boost = $_POST['tfidf_boost']??1;
$index = 'news';
$field = $_POST['field']??'combined';

$boolean = $_POST['boolean']??0;
if (isset($_POST['start'])) {
    $start = $_POST['start'];
    $current = $start / $offset + 1;
}
$query = $_POST['query']??'';
$ranker = $_POST['ranker']??'bm25';
$rankerexpr = $_POST['rankerexpr']??'';

if (isset($_POST['send'])) {

    $index_status = $conn->query("SHOW INDEX $index STATUS")->fetchAll();
    foreach ($index_status as $arr) {
        if ($arr['Variable_name'] == 'indexed_documents') {
            $total_docs = $arr['Value'];
        }
    }
    $mltQuery = function ($query) use ($conn, $stopwords_enabled, $stopwords, $max_query_terms, $index, $tfidf_boost, $total_docs) {
        $stmt = $conn->prepare("CALL KEYWORDS(:keywords,'$index',1 as stats)");
        $stmt->bindValue(':keywords', $query, PDO::PARAM_STR);
        $stmt->execute();
        $callkeywords = $stmt->fetchAll();
        $keywords = [];

        foreach ($callkeywords as $callkey) {
            if ($stopwords_enabled == true && in_array($callkey['tokenized'], $stopwords)) {
                continue;
            }

            if (isset($keywords[$callkey['tokenized']])) {
                $keywords[$callkey['tokenized']]['appear']++;
            } else {
                $keywords[$callkey['tokenized']] = array(
                    'appear' => 1,
                    'docs' => $callkey['docs'],
                    'hits' => $callkey['hits'],
                    'idf' => 1 + log($total_docs / ($callkey['docs'] + 1))
                );
            }
        }
        $max_tf_idf = 0;
        foreach ($keywords as $k => $v) {
            $keywords[$k]['tf'] = sqrt($v['appear']);
            $keywords[$k]['tfidf'] = $keywords[$k]['tf'] * $keywords[$k]['idf'];
            $keywords[$k]['query'] = ($tfidf_boost ? $k . '^' . $keywords[$k]['tfidf'] : $k);
            if ($keywords[$k]['tfidf'] > $max_tf_idf) {
                $max_tf_idf = $keywords[$k]['tfidf'];
            }
        }

        uasort($keywords, function ($a, $b) {
            return $a['tfidf'] <=> $b['tfidf'];
        });
        $keywords = array_reverse($keywords, true);

        $keywords = array_slice($keywords, 0, $max_query_terms);
        return array($keywords, $max_tf_idf);
    };

    list($content_keywords, $content_keywords_max_tf_idf) = $mltQuery($_POST['query_content']);
    list($title_keywords, $title_keywords_max_tf_idf) = $mltQuery($_POST['query_title']);

    if ($boolean == true) {
        $content_query = '@content ( ' . implode('| ', array_column($content_keywords, 'query')) . ')';
        $title_query = '@title ( ' . implode('| ', array_column($title_keywords, 'query')) . ')';
    } else {
        $content_query = '@content "' . implode(' ', array_column($content_keywords, 'query')) . '"/' . "$minimum_should_match";
        $title_query = '@title "' . implode(' ', array_column($title_keywords, 'query')) . '"/' . "$minimum_should_match";
    }
    if ($ranker == 'expr') {
        $ranking = "expr('" . $rankerexpr . "')";
    } else {
        $ranking = $ranker;
    }
    if ($field == 'combined') {
        $mlk_query = $title_query . ' ' . $content_query;
    } elseif ($field == 'title') {
        $mlk_query = $title_query;
    } else {
        $mlk_query = $content_query;
    }


    $stmt = $conn->prepare("SELECT *,WEIGHT() as w FROM $index WHERE MATCH(:mlk) LIMIT $start,$offset OPTION ranker=$ranking, idf='plain,tfidf_unnormalized',field_weights=(title=1,content=1)");
    $stmt->bindValue(':mlk', $mlk_query, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $meta = $conn->query("SHOW META")->fetchAll();
    foreach ($meta as $m) {
        $meta_map[$m['Variable_name']] = $m['Value'];
    }
    $total_found = $meta_map['total_found'];
    $total = $meta_map['total'];
    $sphinxql = interpolateQuery("SELECT *,WEIGHT() as w FROM $index WHERE MATCH(:mlk) LIMIT $start,$offset OPTION ranker=$ranking, idf='plain,tfidf_unnormalized',field_weights=(title=1,content=1", array('mlk' => $mlk_query));
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
