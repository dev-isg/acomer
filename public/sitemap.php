<?php
class SiteMap
{ 
  public $sitemapFileName = "sitemap.xml";
  public $sitemapIndexFileName = "sitemap-index.xml";
  public $robotsFileName = "robots.txt";
  //public $maxURLsPerSitemap = 50000;
  //public $createGZipFile = false;
  private $_baseUrl;
  private $_basePath;
  private $_searchEngines = array(
                                  array("http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=USERID&url=",
                                        "http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap="),
                                  "http://www.google.com/webmasters/tools/ping?sitemap=",
                                  "http://submissions.ask.com/ping?sitemap=",
                                  "http://www.bing.com/webmaster/ping.aspx?siteMap="
                                  );
  private $_urls;
  private $_sitemaps;
  private $_sitemapIndex;
  private $_fileSitemap;
  private $_fileSitemapIndex;
  public function SiteMap ($baseUrl, $basePath = '')
  { 
    $this->_baseUrl= $baseUrl;
    $this->_basePath = $basePath;
  } //end function __constructor

  public function init ()
  { 
    $this->_openFileSitemapIndex();
    $this->_openFileSitemap();
//    echo $this->_fileSitemapIndex  .'-'.$this->sitemapFileName. PHP_EOL;exit;
    fwrite($this->_fileSitemapIndex, "<sitemap><loc>{$this->_baseUrl}/{$this->sitemapFileName}</loc></sitemap>");
  } //end function init
  public function done ()
  { 
    $this->_closeFileSitemapIndex();
    $this->_closeFileSitemap();
  } //end function done
  
  function addUrl ($url, $lastModified = null, $changeFrequency = null, $priority = null)
  { 
    fwrite($this->_fileSitemap, "<url>\n"
           . "\t<loc>".htmlspecialchars($url,ENT_QUOTES,'UTF-8')."</loc>\n" 
           . (($lastModified)?"\t<lastmod>$lastModified</lastmod>\n":'') 
           . (($changeFrequency)?"\t<changefreq>$changeFrequency</changefreq>\n":'') 
           . (($priority)?"\t<priority>$priority</priority>":'') 
           . "</url>\n");
  } //end function addUrl
  protected function _openFileSitemapIndex ()
  { 
    $this->_fileSitemapIndex = fopen($this->_basePath . $this->sitemapIndexFileName,'w');
    $sitemapIndexHeader = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
      . '<sitemapindex '."\n"
      . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n"
      . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n" 
      . 'http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"' . "\n" 
      . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
    fwrite($this->_fileSitemapIndex, $sitemapIndexHeader);
  } //end function initFileSitemapIndex
  protected function _closeFileSitemapIndex ()
  { 
    fwrite($this->_fileSitemapIndex, "\n</sitemapindex>\n");
    fclose($this->_fileSitemapIndex);
  } //end function _closeFileSitemapIndex
  public function addSitemap ($filename)
  { 
    if ( $this->_fileSitemap ){  
      $this->_closeFileSitemap();
    } //end if $this->_fileSitemap
    fwrite($this->_fileSitemapIndex, "\n<sitemap><loc>{$this->_baseUrl}/$filename</loc></sitemap>");
    $this->sitemapFileName = $filename;
    $this->_openFileSitemap();
    return true;
  } //end function addSitemap
  
  protected function _openFileSitemap ()
  { 
    $sitemapHeader = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" 
      . '<urlset' ."\n" 
      . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n" 
      . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n" 
      . 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"' . "\n" 
      . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $this->_fileSitemap = fopen($this->_basePath . $this->sitemapFileName,'w');
    fwrite($this->_fileSitemap, $sitemapHeader);
  } //end function initFileSitemap

  protected function _closeFileSitemap ()
  { 
    fwrite($this->_fileSitemap,"\n</urlset>\n");
    fclose($this->_fileSitemap);
  } //end function _closeFileSitemap  

  public function send($yahooAppId = null) {
    if (!extension_loaded('curl'))
      throw new BadMethodCallException("cURL library is needed to do submission.");
    $sitemapFullURL = $this->_baseUrl.'/'.$this->sitemapIndexFileName;
    $searchEngines = $this->_searchEngines;
    $searchEngines[0] = isset($yahooAppId) ? str_replace("USERID", $yahooAppId, $searchEngines[0][0]) : $searchEngines[0][1];
    $result = array();
    for($i=0;$i<sizeof($searchEngines);$i++) {
      $submitSite = curl_init($searchEngines[$i].htmlspecialchars($sitemapFullURL,ENT_QUOTES,'UTF-8'));
// curl_setopt($submitSite, CURLOPT_HTTPPROXYTUNNEL, 1); 
// curl_setopt($submitSite, CURLOPT_PROXYPORT, 9090); 
// curl_setopt($submitSite, CURLOPT_PROXY, 'http://172.19.0.4:9090'); 
// curl_setopt($submitSite, CURLOPT_PROXYUSERPWD, 'pacmamhe:draco12'); 
      curl_setopt($submitSite, CURLOPT_RETURNTRANSFER, true);
      $responseContent = curl_exec($submitSite);
      $response = curl_getinfo($submitSite);
      $submitSiteShort = array_reverse(explode(".",parse_url($searchEngines[$i], PHP_URL_HOST)));
      $result[] = array("site" => $submitSiteShort[1].".".$submitSiteShort[0],
                        "fullsite "=> $searchEngines[$i].htmlspecialchars($sitemapFullURL, ENT_QUOTES,'UTF-8'),
                        "http_code" => $response['http_code'],
                        "message" => str_replace("\n", " ", strip_tags($responseContent)));
    }
    return $result;
  }

  public function updateRobots() {
    $sampleRobotsFile = "User-agent: *\nAllow: /";
    $sitemapFullURL = $this->_baseUrl.'/'.$this->sitemapIndexFileName;
    if (file_exists($this->_basePath . $this->robotsFileName)) {
      $robotsFile = explode("\n", file_get_contents($this->_basePath . $this->robotsFileName));
      $robotsFileContent = "";
      foreach($robotsFile as $key=>$value) {
        if(substr($value, 0, 8) == 'Sitemap:') unset($robotsFile[$key]);
        else $robotsFileContent .= $value."\n";
      }
      $robotsFileContent .= "Sitemap: $sitemapFullURL";
      file_put_contents($this->_basePath . $this->robotsFileName,$robotsFileContent);
    }
    else {
      $sampleRobotsFile = $sampleRobotsFile."\n\nSitemap: ".$sitemapFullURL;
      file_put_contents($this->_basePath . $this->robotsFileName, $sampleRobotsFile);
    }
  }
} //end class SiteMap
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/vendor/zendframework/zendframework/library'),
    get_include_path()
)));
    
    require_once 'Zend/Loader/StandardAutoloader.php';
    $config =include APPLICATION_PATH .'/config/autoload/global.php';
    $autoLoader = new Zend\Loader\StandardAutoloader(array(
                'prefixes' => array(
                'MyVendor' => '/vendor/zendframework/zendframework/library/Zend',
                ),
                'fallback_autoloader' => true,
                ));

                // register our StandardAutoloader with the SPL autoloader
                $autoLoader->register();
    $adapter=new \Zend\Db\Adapter\Adapter($config['db']);
    $baseUrl=$config['host']['base'];
    $avisoSitemapCount = 1;
$sm = new SiteMap($baseUrl, APPLICATION_PATH .'/public/');
$sm->sitemapIndexFileName = 'sitemap_index.xml';
$sm->sitemapFileName = 'platos'.str_pad($avisoSitemapCount,2,'0',STR_PAD_LEFT).'_sitemap.xml';
$sm->init();

    $platos=$adapter->query('SELECT ta_plato.in_id,ta_plato.va_nombre,tr.va_nombre AS restaurante 
                FROM ta_plato
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                WHERE   ta_plato.en_estado=1  AND tr.en_estado=1
                GROUP BY in_id',$adapter::QUERY_MODE_EXECUTE);
    include APPLICATION_PATH.'/module/Application/src/Application/View/Helper/CanonicalUrl.php';
    include APPLICATION_PATH.'/module/Application/src/Application/View/Helper/Canonical.php';
    $crul=new Application\View\Helper\CanonicalUrl();
    $limpiando=new Application\View\Helper\Canonical();
      foreach($platos as $plato){                                                    
          $platourl=$crul($limpiando($plato->va_nombre), array('suffix' =>$plato->in_id));
          $restauranteurl=$crul($limpiando($plato->restaurante));
          $sm->addUrl($baseUrl . '/plato/'.$restauranteurl.'/' . $platourl);
        }   
$sm->updateRobots();
$result = $sm->send();
print_r($result);
$sm->done();

echo "\nUso de memoria: " . number_format(memory_get_peak_usage()/(1024*1024),2)."MB\n";
$time2 = explode(" ",microtime());
$time2 = $time2[1];
echo "\nTiempo de ejecución: " . number_format($time2-$time)."s\n";


die();