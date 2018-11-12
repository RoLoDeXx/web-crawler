<?php
class ImageResultsProvider
{

	private $con;

	public function __construct($con)
  {
		$this->con = $con;
	}

	public function getNumResults($term)
  {

		$query = $this->con->prepare("SELECT COUNT(*) as total
                									 FROM images
                                   WHERE title LIKE :term
                                   OR alt LIKE :term
                                   AND broken = 0");

		$searchTerm = "%". $term . "%";
		$query->bindParam(":term", $searchTerm);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"];

	}

  public function getResultsHtml($page,$pageSize,$term)
  {
		$fromLimit = ($page-1)*$pageSize;

    $query = $this->con->prepare("SELECT *
              									  FROM images
                                  WHERE title LIKE :term
                                  OR alt LIKE :term
                                  AND broken = 0
																  ORDER BY clicked DESC
																  LIMIT :fromLimit,:pageSize");

		$searchTerm = "%". $term . "%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromLimit", $fromLimit,PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize,PDO::PARAM_INT);
		$query->execute();

    $resultsHtml = "<div class='imageResults'>";
		$count =0;
    while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
				$count++;
        $id = $row["id"];
        $imageurl = $row["imageurl"];
				$siteurl = $row["siteurl"];
        $title = $row["title"];
				$alt = $row["alt"];

				if($title)
					$displayText = $title;
				else if($alt)
					$displayText = $alt;
				else
					$displayText = $imageurl;

        $resultsHtml .="<div class='gridItem image$count'>
                        <a href='$imageurl'>
												<script>
												$(document).ready(function(){
													loadImage(\"$imageurl\",\"image$count\");
												});
												</script>
												<span id='details'>$displayText</span>
												</a>
                        </div>";
    }

    $resultsHtml .= "</div>";

    return $resultsHtml;
  }


}
?>
