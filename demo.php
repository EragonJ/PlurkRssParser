<?php
  include_once("RssParser.php");

  $s = new RssParser(array("http://www.plurk.com/EragonJ.xml","http://www.plurk.com/buweichiu.xml"));
  print_r($s->exec()->sortSeeds()->toHTML());

?>
