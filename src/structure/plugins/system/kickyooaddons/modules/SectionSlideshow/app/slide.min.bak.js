var offset = 0;
var init = 0;

function resizeSlideshow(current) {
  var highestElement = 0;
  var highestTile = 0;
  var slideshow = document.querySelector('.uk-slideshow-items');
  UIkit.util.css( UIkit.util.$$('.el-item .uk-tile.uk-flex', slideshow), 'min-height', 0);
  var texttiles = UIkit.util.$$('.el-item .uk-tile.uk-flex', slideshow)
  UIkit.util.addClass(UIkit.util.$$('.el-item', slideshow), 'uk-display-block');
  UIkit.heightMatch()
  //UIkit.heightMatch(slideshow, {'target': '.el-item .uk-tile'});

  for (var i = 0, nb = texttiles.length; i < nb; i++) {
    var texttile = texttiles[i];
    var itemheight = texttile.offsetHeight;

    if (itemheight > highestTile) {
      highestTile = Math.ceil(texttile.offsetHeight);
    }
  }
  UIkit.util.css( UIkit.util.$$('.el-item .uk-tile.uk-flex', slideshow), 'min-height', highestTile);
  // tile primary anpassen
  // block
  // höhe match each.

  var items = slideshow.querySelectorAll('.el-item .getheight');
  for (var i = 0, nb = items.length; i < nb; i++) {
    var item = items[i];

    var itemheight = item.offsetHeight;
    if (itemheight > highestElement) {
      highestElement = Math.ceil(item.offsetHeight);
    }
  }
  slideshow.setAttribute('style', "min-height: " + highestElement + "px");
  UIkit.util.removeClass(UIkit.util.$$('.el-item', slideshow), 'uk-display-block');
}

// Einstellungen für die Slideshow laden
function initSlideshow() {
  UIkit.util.on('#kickyooslide', 'itemshow', function (e, slideshow,) {
    setTimeout(function () {
      resizeSlideshow(slideshow.index);
    }, 200)
  });
};

window.addEventListener("DOMContentLoaded", function () {
  resizeSlideshow();
  initSlideshow();
});
window.addEventListener("resize", function () {
  resizeSlideshow()
});
