<?php
class SiteResultsProvider
{

	private $con;

	public function __construct($con)
  {
		$this->con = $con;
	}

	public function getNumResults($term)
  {

		$query = $this->con->prepare("SELECT COUNT(*) as total
                									 FROM sites WHERE title LIKE :term
                									 OR url LIKE :term
                									 OR keywords LIKE :term
                									 OR description LIKE :term");

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
                									 FROM sites WHERE title LIKE :term
                									 OR url LIKE :term
                									 OR keywords LIKE :term
                									 OR description LIKE :term ORDER BY clicks DESC
																	 LIMIT :fromLimit,:pageSize");

		$searchTerm = "%". $term . "%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromLimit", $fromLimit,PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize,PDO::PARAM_INT);
		$query->execute();

    $resultsHtml = "<div class='siteResults'>";

    while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
        $id = $row["id"];
        $url = $row["url"];
        $title = $row["title"];
        $description = $row["description"];

				$url = $this->trimFeild($url,30);
				$title = $this->trimFeild($title,30);
				$description = $this->trimFeild($description,55);

        $resultsHtml .="<div class='resultContainer'>
                        <h3 class='title'><a data-linkId='$id' class='result' href='$url'>$title</a></h3>
                        <span class='url'>$url</span>
                        <span class='description'>$description</span>
                        </div>";
    }

    $resultsHtml .= "</div>";

    return $resultsHtml;
  }

	private function trimFeild($string, $charLimit)
	{
				$dots = strlen($string) > $charLimit ? "...":"";
				return substr($string,0, $charLimit) . $dots;
	}


}
?>
