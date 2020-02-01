window.onscroll = function() {
  var wScroll = window.scrollY;

  // MOVE DOWN
  document.getElementsByClassName("overlay-text")[0].style.transform =
    "translateY(" + wScroll / 5.5 + "%)";

  // MOVE LEFT
  // document.getElementsByClassName("left-side-bar")[0].style.transform =
  //   "translateX(-" + wScroll / 15 + "%)";
};
