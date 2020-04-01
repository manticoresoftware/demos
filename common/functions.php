<?php

function BuildTrigrams($keyword)
{
    $t = "__" . $keyword . "__";
    $trigrams = "";
    for ($i = 0; $i < strlen($t) - 2; $i++)
        $trigrams .= substr($t, $i, 3) . " ";
    return $trigrams;
}
function MakeQSuggest($keyword,$index,$ln_sph)
{
    $stmt = $ln_sph->prepare("CALL SUGGEST(:keyword,'$index')");
    $stmt->bindValue(':keyword', $keyword,PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['suggest'];
}

function MakeMultiQSuggest($words,$query,$index,$ln_sph)
{
    $suggested = array();
    $llimf = 0;
    $i = 0;
    foreach ($words  as $key => $word) {
        if ($word['docs'] != 0)
            $llimf +=$word['docs'];$i++;
    }
    $llimf = $llimf / ($i * $i);
    foreach ($words  as $key => $word) {
        if ($word['docs'] == 0 | $word['docs'] < $llimf) {
            $mis[] = $word['keyword'];
        }
    }
    if (count($mis) > 0) {
        foreach ($mis as $m) {
            $re = MakeQSuggest($m,$index, $ln_sph);
            if ($re) {
                if($m!=$re)
                    $suggested[$m] = $re;
            }
        }
        if(count($words) ==1 && empty($suggested)) {
            return false;
        }
        $phrase = explode(' ', $query);
        foreach ($phrase as $k => $word) {
            if (isset($suggested[strtolower($word)]))
                $phrase[$k] = $suggested[strtolower($word)];
        }
        $phrase = implode(' ', $phrase);
        return $phrase;
    }else{
        return false;
    }
}
function MakeSuggestion($keyword,$ln)
{
    $trigrams = BuildTrigrams($keyword);
    $query = "\"$trigrams\"/1";
    $len = strlen($keyword);

    $delta = LENGTH_THRESHOLD;
    $weight = 'weight()';
    if(SPHINX_20 == true) {
        $weight ='@weight';
    }
    $stmt = $ln->prepare("SELECT *, $weight as w, w+:delta-ABS(len-:len) as myrank FROM suggest WHERE MATCH(:match) AND len BETWEEN :lowlen AND :highlen
			ORDER BY myrank DESC, freq DESC
			LIMIT 0,:topcount OPTION ranker=wordcount");

    $stmt->bindValue(':match', $query, PDO::PARAM_STR);
    $stmt->bindValue(':len', $len, PDO::PARAM_INT);
    $stmt->bindValue(':delta', $delta, PDO::PARAM_INT);
    $stmt->bindValue(':lowlen', $len - $delta, PDO::PARAM_INT);
    $stmt->bindValue(':highlen', $len + $delta, PDO::PARAM_INT);
    $stmt->bindValue(':topcount',TOP_COUNT, PDO::PARAM_INT);
    $stmt->execute();


    if (!$rows = $stmt->fetchAll())
        return false;
    // further restrict trigram matches with a sane Levenshtein distance limit
    foreach ($rows as $match) {
        $suggested = $match["keyword"];
        if (levenshtein($keyword, $suggested) <= LEVENSHTEIN_THRESHOLD)
            return $suggested;
    }

    return $keyword;
}
function EscapeString ( $string )
{
    $from = array ( '\\', '(',')','|','-','!','@','~','"','&', '/', '^', '$', '=', '<' );
    $to   = array ( '\\\\', '\(','\)','\|','\-','\!','\@','\~','\"', '\&', '\/', '\^', '\$', '\=', '\<' );
    return str_replace ( $from, $to, $string );
}
function MakePhaseSuggestion($words,$query,$ln_sph)
{
    $suggested = array();
    $llimf = 0;
    $i = 0;
    foreach ($words  as $key => $word) {
        if ($word['docs'] != 0)
            $llimf +=$word['docs'];$i++;
    }
    $llimf = $llimf / ($i * $i);
    foreach ($words  as $key => $word) {
        if ($word['docs'] == 0 | $word['docs'] < $llimf) {
            $mis[] = $word['keyword'];
        }
    }
    if (count($mis) > 0) {
        foreach ($mis as $m) {
            $re = MakeSuggestion($m, $ln_sph);
            if ($re) {
                if($m!=$re)
                    $suggested[$m] = $re;
            }
        }
        if(count($words) ==1 && empty($suggested)) {
            return false;
        }
        $phrase = explode(' ', $query);
        foreach ($phrase as $k => $word) {
            if (isset($suggested[strtolower($word)]))
                $phrase[$k] = $suggested[strtolower($word)];
        }
        $phrase = implode(' ', $phrase);
        return $phrase;
    }else{
        return false;
    }
}
function interpolateQuery($query, $params) {
    $keys = array();
    $values = $params;

    # build a regular expression for each parameter
    foreach ($params as $key => $value) {
        if (is_string($key)) {
            $keys[] = '/:'.$key.'/';
        } else {
            $keys[] = '/[?]/';
        }

        if (is_string($value))
            $values[$key] = "'" . $value . "'";

        if (is_array($value))
            $values[$key] = "'" . implode("','", $value) . "'";

        if (is_null($value))
            $values[$key] = 'NULL';
    }

    $query = preg_replace($keys, $values, $query);

    return $query;
}
function color_luminance($hex, $percent)
{

    // validate hex string

    $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
    $new_hex = '#';

    if (strlen($hex) < 6) {
        $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
    }

    // convert to decimal and change luminosity
    for ($i = 0; $i < 3; $i++) {
        $dec = hexdec(substr($hex, $i * 2, 2));
        $dec = min(max(0, $dec + $dec * $percent), 255);
        $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
    }

    return $new_hex;
}

function highlightidf($str, $keywords, $max_tf_idf)
{

    $text = preg_split('/\s+/', $str);
    $newtext = "";
    foreach ($text as $t) {

        if (isset($keywords[strtolower($t)])) {

            $newtext .= " " . '<b style="color:' . color_luminance('ff0000', -(1 - ($keywords[strtolower($t)]['tfidf'] * 100 / $max_tf_idf) / 100)) . '">' . $t . '</b>';
        } else {
            $newtext .= " " . $t;
        }
    }
    return $newtext;
}