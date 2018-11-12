<?php
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");
$term = "";
$type = "sites";
$page = 1;
if(isset($_GET['term']))
  $term = $_GET['term'];

if(isset($_GET['type']))
  $type = $_GET['type'];

if(isset($_GET['page']))
  $page = $_GET['page'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Ishjoot</title>
    <link rel="stylesheet" href="assets/css/indexStyle.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="wrapper">
      <div class="header">
        <div class="headerContent">
          <div class="logoContainer">
            <a href="index.php"><img src="assets/logos/ishjoot_main.png"></a>
          </div>
          <div class="tabsContainer">
              <table>
                  <tr>
                    <td id="tabItems">
                      <a class="<?php echo $type == 'sites' ? 'active': '' ;?>" href='<?php echo "search.php?term=$term&type=sites"; ?>'><img src="assets/logos/searchType-sites.png"></a>
                    </td>
                    <td id="tabItems">
                      <a class="<?php echo $type == 'images'? 'active': '' ;?>" href='<?php echo "search.php?term=$term&type=images"; ?>'><img src="assets/logos/searchType-images.png"></a>
                    </td>
                  </tr>
              </table>
          </div>
        </div>

          <div class="searchBar">
            <form action="search.php" method="GET">
              <div class="searchBarContainer">
                <input type="hidden" name="type" value="<?php echo $type;?>">
                <table>
                  <tr></tr>
                  <tr>
                    <td><input type="text" name="term" class="searchBox" autocomplete="off" value="<?php echo $term; ?>">
                    <button id="searchButton"><img src="assets/logos/searchIcon.png"></button></td>
                  </tr>
              </table>
              </div>
            </form>
          </div>
      </div>

      <div class="mainResultsSection">
        <?php
        if($type == "sites")
        {
          $resultsProvider = new SiteResultsProvider($con);
          $pageLimit = 20;
        }

        else
        {
          $resultsProvider = new ImageResultsProvider($con);
          $pageLimit = 30;
        }

        $results = $resultsProvider->getNumResults($term);
        echo "<p id='resultCounter'>$results results found</p>";


        echo $resultsProvider->getResultsHtml($page,$pageLimit,$term);
        ?>
      </div>

     </div>

     <div class="paginationContainer">
       <?php
       $pagesToShow = 10;
       $numPages = ceil($results/$pageLimit);
       $pagesLeft = min($pagesToShow,$numPages);

       $currentPage = $page - floor($pagesToShow/2);
       if($currentPage<1)
         $currentPage =1;
       while($pagesLeft !=0)
       {
         if($currentPage == $page)
           echo "<span id='pageNumbers'>$currentPage</span>";
         else
           echo "<a href='search.php?term=$term&type=$type&page=$currentPage'><span id='pageNumbers'>$currentPage</span></a>";
          $pagesLeft--;
          $currentPage++;
       }
       ?>
     </div>
   <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
   <script src="assets/js/ishjoot.js" charset="utf-8"></script>
  </body>
</html>
