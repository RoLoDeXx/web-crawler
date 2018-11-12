$(document).ready(function(){

  $(".result").on("click",function(){
    var url = $(this).attr("href");
    var id = $(this).attr("data-linkId");

    if(!id)
      alert("id not found !ALERT");

    increaseLinkClicks(id,url);
    return false;
  });

  var grid = $(".imageResults");
  grid.masonry({
    itemSelector: ".gridItem",
    columnWidth:200,
    gutter:5,
    isInitLayout:false
  });
});

function loadImage(url,className)
{
  var image = $("<img>");
  image.on("load", function(){
    $("." + className + " a").append(image);

    $(".imageResults").masonry();
  });

  image.on("error",function(){

  });
  image.attr("src",url);
}

function increaseLinkClicks(linkId,url)
{
  $.post("ajax/updateLinkCount.php",{linkId: linkId})
  .done(function(result) {
    if(result != ""){
      alert(result);
      return;
    }
    window.location.href=url;
  });
}
