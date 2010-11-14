<?php
  class RssParser
  {

    protected $dom; 

    protected $html;

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
        foreach ($this->dom->getElementsByTagName("entry") as $DOMNode)
        {
          $innerNodes = $DOMNode->childNodes;
          if(!empty($innerNodes))
          {
            $title = $innerNodes->item(1)->nodeValue;
            $published = $innerNodes->item(7)->nodeValue;
            $link = $innerNodes->item(9)->attributes->item(0)->nodeValue;
            $this->feedValue[] = array($title,$published,$link);
          }
        }
      }
      return $this;
    }

    public function sortSeeds()
    {

      function cmp($a,$b)
      {
        if($a[1] == $b[1])
        {
          return 0;
        }
        return ($a[1] < $b[1]) ? 1 : -1;
      }

      usort($this->feedValue,"cmp");
      return $this;
    }

    public function toHTML()
    {
      $this->html = "<html><head><meta http-equiv='content-type' content='text/html;charset=UTF-8'/><title>Plurk Widget</title><style type='text/css'> body { background-color: #cf682f; color: #f5e0d5 !important; } .user_info { border-bottom: 1px solid #e3a481; } .plurk-timeline { background-color: #cae7fd; border-bottom: 1px solid #e3a481; } a { color: #ffffff; } .plurk-plurks, .plurk-plurks a { color: #111111 !important; } .plurk-message { border-bottom: 1px solid #a8d7fc; } .plurk_on { background-color: #e3f2fe; } .plurk-meta, .plurk-meta a { color: #098df2 !important; } .action { background-color: #9d4f24; }</style><link rel='stylesheet' type='text/css' href='http://statics.plurk.com/5dfb4e9b0ae2b0e05ec2e92070d593c7.css.cgz?'></link><body><div class='container'><div id='content'><div id='plurks_holder'><div class='plurk-timeline plurk-plurks' style='height:318px;'>";

      foreach($this->feedValue as $userData)
      {
         $this->html .= "<div class='plurk-message'>".$userData[0];
         $this->html .= "<div class='plurk-meta'>".date("Y/m/d h:i:s",strtotime($userData[1]))." | <a href='http://www.plurk.com".$userData[2]."' target='_blank'>Read More</a></div>";
         $this->html .= "</div>";
      }

      $this->html .= "</div></div></div></body></html>";

      return $this->html;
    }

    public function toArray()
    {
      return $this->feedValue;
    }
  }
?>
