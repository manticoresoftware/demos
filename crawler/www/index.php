<form><h1>Manticore</h1><input name="search" type="text" style="width: 50%; border: 1px solid" value="<?=$_GET['search']?>"></form>
<hr>
<?php
if (isset($_GET['search'])) { # we have a search request, let's process it
    $ch = curl_init(); # initializing curl
    curl_setopt($ch, CURLOPT_URL,"http://manticore:9308/sql"); # we'll connect to Manticore's /sql endpoint via HTTP. There's also /json/search/ which gives much more granular control, but for the sake of simplicity we'll use the /sql endpoint
    curl_setopt($ch, CURLOPT_POST, 1); # we'll send via POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); # we need the response back, don't output it
    curl_setopt($ch, CURLOPT_POSTFIELDS, "mode=raw&query=SELECT url, highlight({}, 'title') title, highlight({}, 'body') body FROM rt WHERE MATCH('{$_GET['search']}') LIMIT 10"); /* here we are SELECTing :
 - url
 - highlighted title
 - highlighted body
 - from the index called "rt" 
 - we want all documents that MATCH() our search query
 - and we need only the first 10, hence LIMIT 10
*/
    if ($json = json_decode(curl_exec($ch))) { # running the query and decoding the JSON
        foreach ($json->data as $result) echo "<small>{$result->url}</small><br><a href=\"{$result->url}\">{$result->title}</a><br>{$result->body}<br><br>"; # and here we just output the results: url, title and body
    }
}
