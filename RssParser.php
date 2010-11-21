<?php
  class RssParser
  {

    protected $dom; 

    protected $html;

    protected $feedValue = array();

    protected $cacheFile;

    /*
     * mixed 
     */
    protected $feedList = array();

    public function __construct($filename)
    {
      date_default_timezone_set("Asia/Taipei");
      $this->feedList = is_array($filename) ? $filename : array($filename);
      $this->cacheFile = __DIR__."/feed.cache";
      return $this;
    }

    public function exec()
    {
      if(!$this->cacheCheck())
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
              $allContent = $innerNodes->item(13)->nodeValue;
              preg_match("/(.*?) (.*)/",$allContent,$allContent);

              $published = $innerNodes->item(7)->nodeValue;
              $link = $innerNodes->item(9)->attributes->item(0)->nodeValue;
              $nick = $allContent[1];
              $content = $allContent[2];

              $this->feedValue[] = array($nick,$content,$published,$link);
            }
          }
        }
        $this->cacheStore();
      }
      return $this;
    }

    public function sortSeeds()
    {

      function cmp($a,$b)
      {
        if($a[2] == $b[2])
        {
          return 0;
        }
        return ($a[2] < $b[2]) ? 1 : -1;
      }

      usort($this->feedValue,"cmp");
      return $this;
    }

    public function toHTML($length)
    {
      $i = 0;

      $this->html = "<html><head><meta http-equiv='content-type' content='text/html;charset=UTF-8'/><title>Plurk Widget</title><style type='text/css'> body { background-color: #cf682f; color: #f5e0d5 !important; } .user_info { border-bottom: 1px solid #e3a481; } .plurk-timeline { background-color: #cae7fd; border-bottom: 1px solid #e3a481; } a { color: #ffffff; } .plurk-plurks, .plurk-plurks a { color: #111111 !important; } .plurk-message { border-bottom: 1px solid #a8d7fc; } .plurk_on { background-color: #e3f2fe; } .plurk-meta, .plurk-meta a { color: #098df2 !important; } .action { background-color: #9d4f24; }</style><link rel='stylesheet' type='text/css' href='http://statics.plurk.com/5dfb4e9b0ae2b0e05ec2e92070d593c7.css.cgz?'></link><script src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js'></script><script>$(document).ready(function(){\$('.plurk-message').mouseenter(function(){\$(this).toggleClass('plurk_on');}).mouseleave(function(){\$(this).toggleClass('plurk_on');})});</script></head><body>";

      $this->html .= "<div class='container'><div class='user_info' style='height:46px;font-size:20px;text-align:center;'>Made By Hax4.in</div>";
      $this->html .= "<div id='content'><div id='plurks_holder'><div class='plurk-timeline plurk-plurks' style='height:318px;'>";

      foreach($this->feedValue as $userData)
      {
        $this->html .= "<div class='plurk-message'>";
        $this->html .= "<span class='plurk-nickname'>".$userData[0]."</span>";
        $this->html .= "<span class='cnt'> ".$userData[1]."</span>";
        $this->html .= "<div class='plurk-meta'>".date("Y/m/d h:i:s",strtotime($userData[2]))." | <a href='http://www.plurk.com".$userData[3]."' target='_blank'>Read More</a></div>";
        $this->html .= "</div>";

        $i++;
        if($i >= $length)
        { 
          break;
        }
      }

      $this->html .= "</div></div></div></body></html>";

      return $this->html;
    }

    public function toArray()
    {
      return $this->feedValue;
    }

    private function cacheCheck($duration = 600)
    {
      if(file_exists($this->cacheFile))
      {
        if((time() - filemtime($this->cacheFile)) < $duration)
        {
          $this->feedValue = unserialize(file_get_contents($this->cacheFile));
          return true;
        }
      }
      return false;
    }

    private function cacheStore()
    {
      file_put_contents($this->cacheFile,serialize($this->feedValue));
    }
  }
?>
