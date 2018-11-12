<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url)
{
  global $con;
  $query = $con->prepare("SELECT * FROM sites WHERE url = :url");
  $query->bindParam(":url", $url);
  $query->execute();

  return $query->rowCount() != 0;
}

function insertLink($url, $title, $description ,$keywords)
{
  global $con;
  $query = $con->prepare("INSERT INTO sites(url,title,description,keywords)
                          VALUES(:url,:title,:description,:keywords)");
  $query->bindParam(":url", $url);
  $query->bindParam(":title", $title);
  $query->bindParam(":description", $description);
  $query->bindParam(":keywords", $keywords);
  return $query->execute();
}

function insertImage($url, $src, $alt ,$title)
{
  global $con;
  $query = $con->prepare("INSERT INTO images(siteurl,imageurl,alt,title)
                          VALUES(:siteurl,:imageurl,:alt,:title)");
  $query->bindParam(":siteurl", $url);
  $query->bindParam(":imageurl", $src);
  $query->bindParam(":alt", $alt);
  $query->bindParam(":title", $title);
  $query->execute();
}

function createLink($src, $url)
{
  $scheme = parse_url($url)["scheme"]; //http
  $host = parse_url($url)["host"]; //www.example.com

  if(substr($src,0,2) == "//")
    $src =  $scheme. ":" . $src;
  else if(substr($src,0,1) == "/")
    $src = $scheme . "://" . $host . $src;
  else if(substr($src,0,2) == "./")
    $src =  $scheme. "://" . $host . dirname($parseurl($url)["path"]) . substr($src,1);
  else if(substr($src,0,3) == "../")
    $src =  $scheme. "://" . $host . "/" .$src;
  else if(substr($src,0,5) !="https" && substr($src,0,4) !="http")
    $src =  $scheme. "://" . $host . "/" .$src;
  return $src;
}

function getDetails($url)
{
  global $alreadyFoundImages;
  $parser = new DomDocumentParser($url);

  $titleArray = $parser->getTitleTags();
  $title = $titleArray->item(0)->nodeValue;

  if(sizeof($titleArray) ==0 || $titleArray->item(0) == NULL)
    return;
  $title = str_replace("\n","",$title);

  if($title == "")
    return;
  $description = "";
  $keywords = "";

  $metasArray = $parser->getMetatags();
  foreach ($metasArray as $meta)
  {
    if($meta->getAttribute("name") == "description")
    {
      $description = $meta->getAttribute("content");
    }

    if($meta->getAttribute("name") == "keywords")
    {
      $keywords = $meta->getAttribute("content");
    }
  }
  $description = str_replace("\n","",$description);
  $keywords = str_replace("\n","",$keywords);
  if(linkExists($url))
    echo "url already exists: $url<br>";
  else if(insertLink($url,$title,$description,$keywords))
    echo "success: $url<br>";
  else
    echo "failed: $url<br>";

  $imageArray = $parser->getImages();
  foreach($imageArray as $image)
  {
    $src = $image->getAttribute("src");
    $alt = $image->getAttribute("alt");
    $title = $image->getAttribute("title");

    if(!$title && !$alt)
      continue;

    $src = createLink($src,$url);

    if(!in_array($src, $alreadyFoundImages))
    {
      $alreadyFoundImages[]=$src;
      insertImage($url,$src,$alt,$title);
    }

  }

}

function followLinks($url)
{
  global $alreadyCrawled;
  global $crawling;

  $parser = new DomDocumentParser($url);
  $linksLists = $parser->getLinks();

  foreach ($linksLists as $link)
  {
     $href = $link->getAttribute("href");
     if(Strpos($href,"#") !== false||substr($href,0,11)=="javascript:")
       continue;

      $href = createLink($href,$url);

      if(!in_array($href, $alreadyCrawled))
      {
        $alreadyCrawled[] = $href;
        $crawling[] = $href;
        getDetails($href);
      }

      else
        return;
      echo $href."<br>";
      //see for crawled links
  }
     array_shift($crawling);
     foreach($crawling as $site)
     {
        followLinks($site);
     }
}

$startUrl="http://www.jiit.ac.in";
followLinks($startUrl);
?>
