<?php
$f = fopen('php://stdin', 'r'); # we'll be waiting for data at STDIN
$manticore = new mysqli('manticore', '', '', '', 9306); # let's connect to Manticore Search via MySQL protocol
$manticore->query("CREATE TABLE IF NOT EXISTS rt(title text, body text, url text stored) html_strip='1' html_remove_elements='style,script,a' morphology='stem_en' index_sp='1'"); /* creating a table "rt" if it doesn't exist with the following settings:
- html_strip='1': stripping HTML is on
- html_remove_elements='style,script,a': for HTML tags <style>/<script>/<a> we don't need their contents, so we are stripping them completely
- morphology='stem_en': we'll use English stemmer as a morphology processor
- index_sp='1': we'll also index sentences and paragraphs for more advanced full-text search capabilities and better relevance
*/
while (!feof($f)) { # reading from STDIN while there's something
    $s = fgets($f); /* getting one line. Here is an example of wget returns:
    2020-04-08 07:39:33 URL:https://www.who.int/westernpacific/ [98667/98667] -> "index.html.3" [1]
    which means that:
    - the original URL was https://www.who.int/westernpacific/
    - that it saved the contents to index.html.3
    */
    if (!preg_match('/URL:(?<url>http.*?) \[.*?\] -> "(?<path>.*?)"/', $s, $match)) continue; # if wget returns smth else we are just skipping it, otherwise we use regexp to put the url and the path to $match
    do { # it may be that wget returns the info about a download earlier than the file appears, so we are looping until can read from the file:
        $content = @file_get_contents('./'.$match['path']); # reading from the file
        usleep(10000); # sleeping 10 milliseconds
    } while (!$content); # end the loop when we have the content
    if (preg_match('/<title>(?<title>.*?)<\/title>/is', $content, $content_match)) $title = trim(html_entity_decode($content_match['title'])); # here we are doing a simple HTML page parsing to get <title> from that
    else continue; # we are not interested in pages without a title
    echo "{$match['path']}: $title {$match['url']} ".strlen($content)." bytes\n"; # let's say something about our progress
    $manticore->query("REPLACE INTO rt (id,title,url,body) VALUES(".crc32($match['url']).",'".$manticore->escape_string($title)."','".$manticore->escape_string($match['url'])."','".$manticore->escape_string($content)."')"); # and we are finally putting the contents to Manticore. We use crc32($match['url']) as a document ID to avoid duplicates.
} # and we are going back to the next page wget reports as downloaded
