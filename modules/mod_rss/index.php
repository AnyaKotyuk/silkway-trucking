<?
        include_once($_SERVER['DOCUMENT_ROOT'].'/rss_fetch.inc');
    
      
        $rss = fetch_rss("http://news.bigmir.net/rss/ukraine/");
        print_r($rss);

?>