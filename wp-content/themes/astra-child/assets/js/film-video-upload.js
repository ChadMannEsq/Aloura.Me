jQuery(document).ready(function($) {
    $('#film_video_button').click(function(e) {
        e.preventDefault();
        var videoUploader = wp.media({
            title: 'Choose Video',
            button: {
                text: 'Choose Video'
            },
            library: {
                type: 'video'
            },
            multiple: false
        });
        videoUploader.on('select', function() {
            var attachment = videoUploader.state().get('selection').first().toJSON();
            $('#film_video').val(attachment.url);
        });
        videoUploader.open();
    });
});


$(document).ready(function () {
    var video = $("#video");
    var isPlaying = false;

    // Check if the video is ready to play
    video.on('canplay', function () {
        video.mouseenter(function () {
            if (!isPlaying) {
                video[0].play();
                isPlaying = true;
            }
        }).mouseleave(function () {
            video[0].pause();
            isPlaying = false;
        });
    });
});