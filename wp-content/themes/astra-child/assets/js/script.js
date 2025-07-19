jQuery(document).ready(function ($) {
  $(".photoset-gallery").magnificPopup({
    delegate: "a", // child items selector, by clicking on it popup will open
    type: "image",
    gallery: {
      enabled: true,
    },
  });

  $(".banner_slider").slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    prevArrow: $(".slick_prev"),
    nextArrow: $(".slick_next"),
  });
});

jQuery(document).ready(function () {
  jQuery(".popup_open_btn").click(function () {
    jQuery(".filter_container").css({
      display: "block",
    });
    jQuery("body").css({
      overflow: "hidden",
    });
  });

  jQuery(".popup_close_btn").click(function () {
    jQuery(".filter_container").css({
      display: "none",
    });
    jQuery("body").css({
      overflow: "auto",
    });
  });
});

jQuery(document).ready(function ($) {
  // Pause video on mouse leave
  jQuery(".film_item").mouseleave(function () {
    $(this).find("video")[0].pause();
  });
});

jQuery(document).ready(function () {
  var video = jQuery(".preview_video")[0]; // Get the actual video element

  // Play the video on hover
  jQuery(".video-container").hover(
    function () {
      video.currentTime = 0; // Reset video to start
      video.play();
    },
    function () {
      video.pause();
    }
  );
});

jQuery(document).ready(function () {
  jQuery("#more_posts").on("click", function () {
    let type = jQuery(this).data("type");
    load_posts(type);
  });
});

var ppp = 6; // Posts per page
var pageNumber = 0;

function load_posts(type) {
  pageNumber++;
  var str =
    "&pageNumber=" +
    pageNumber +
    "&ppp=" +
    ppp +
    "&type=" +
    type +
    "&action=more_post_ajax";
  jQuery.ajax({
    type: "POST",
    dataType: "html",
    url: ajax_admin.ajax_url,
    data: str,
    success: function (data) {
      var $data = jQuery(data);

      if ($data.length) {
        jQuery("#ajax-posts").append($data);
      } else {
        jQuery("#more_posts").addClass("disabled");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR + " :: " + textStatus + " :: " + errorThrown);
    },
  });
  return false;
}

// Assuming there's a button with id 'more_posts' to load more posts
jQuery(document).ready(function ($) {
  $("#more_posts").on("click", function () {
    var type = $(this).data("type");
    if (!$(this).hasClass("disabled")) {
      load_posts(type);
    }
  });
  jQuery(".menu-toggle").on("click", function () {
    jQuery("body").css({
      overflow: "hidden",
    });
    // jQuery("#ast-mobile-popup-wrapper .ast-custom-button").attr('id', 'get-call-form');
  });

  jQuery(".menu-toggle-close").on("click", function () {
    jQuery("body").css("overflow", "auto");
  });
});


