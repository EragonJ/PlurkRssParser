<?php
  class RssParser
  {

    protected $dom; 

    protected $feedValue = array();

    /*
     * mixed 
     */
    protected $feedList = array();

    public function __construct($filename)
    {
      $this->feedList = is_array($filename) ? $filename : array($filename);
      return $this;
    }

    public function exec()
    {
      foreach($this->feedList as $k => $v)
      {
        $this->dom = new DOMDocument();
        $this->dom->load($v);
        $this->feedValue[] = array();
        foreach ($this->dom->getElementsByTagName("entry") as $DOMNode)
          {
          $innerNodes = $DOMNode->childNodes;
          if(!empty($innerNodes))
          {
            $title = $innerNodes->item(1)->nodeValue;
            $published = $innerNodes->item(7)->nodeValue;
            $link = $innerNodes->item(9)->attributes->item(0)->nodeValue;
            $this->feedValue[$k][] = array($title,$published,$link);
          }
        }
      }
      print_r($this->feedValue);
    }
  }

  $s = new RssParser(array("http://www.plurk.com/EragonJ.xml","http://www.plurk.com/buweichiu.xml"));
  $s->exec();
?>
