$(document).foundation();

//loading
$(window).load(function () {
  $('#loaderBg').delay(900).fadeOut(800);
  $('#loaderWrap').delay(600).fadeOut(300);
});

//link scroll
$(function() {
 $('a[href^="#"]').click(function() {
    var speed = 1000;
    var href= $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top;
    $('body,html').animate({scrollTop:position}, speed, 'swing');
    return false;
  });
});

//modal youtute
$(document).on('closed.zf.reveal', '[data-reveal]', function () {
 $('#smartViewModal iframe').attr('src', 'https://www.youtube.com/embed/NE6Ra_3FJqw');
});

//mute youtube
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var youtubeData = [
  {
    youtubeId: 'NE6Ra_3FJqw',
    embedArea: 'player1'
  }, {
    youtubeId: 'NE6Ra_3FJqw',
    embedArea: 'player2'
  }
];
function onYouTubeIframeAPIReady() {
  for (var i = 0; i < youtubeData.length; i++) {
    embedYoutube(i);
  }
}
function embedYoutube(num) {
  player = new YT.Player( youtubeData[num]['embedArea'], {
    height: '390',
    width: '640',
    videoId: 'NE6Ra_3FJqw',
    wmode: 'transparent',
    playerVars:{
      'autoplay': 1,
      'loop': 1,
      'playlist': youtubeData[num]['youtubeId'],
      'rel': 0,
      'showinfo': 0,
      'controls': 0,
      'color': 'white',
    },
    events: {
      'onReady': onPlayerReady,
      'onStateChange': onPlayerStateChange
    }
  });
}
  function onPlayerReady(event) {
  event.target.playVideo();
  event.target.mute();
}
  function onPlayerStateChange(event) {
}

// background youtube
function resizeMovie () {
    var $w = $('#simpleWallet'),
    bw = 1200,
    bh = (bw/16) * 9,
    w = $w.width(),
    h = $w.height(),
    mw = w,
    mh =  Math.round(bh * (mw/bw));

    if ( mh < h ) {
        mh = h;
        mw = Math.round(bw * (mh/bh));
    }

    $('#player1').css({
      width: mw,
      height: mh,
      marginTop: (h - mh)/2,
      marginLeft: (w - mw)/2
    });
}

resizeMovie();
$(window).resize(resizeMovie);

