<?php
  include_once("PlurkRssParser.php");

  $plurkUsers = array(
      "http://www.plurk.com/EragonJ.xml",
      "http://www.plurk.com/buweichiu.xml",
      "http://www.plurk.com/BlackACE.xml",
      "http://www.plurk.com/SS33.xml"
  );

  $rss = new PlurkRssParser($plurkUsers);
  print_r($rss->exec()->sortSeeds()->toHTML(10));
?>
