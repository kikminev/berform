mediaLibrary = {
    imageWidgetStyle: 'image',
    setImageWidgetStyle: function(style) {
        mediaLibrary.imageWidgetStyle = style;
    },
    getWidgetHtml: function(imageUrl, imageWidgetStyle) {
        if(imageWidgetStyle == 'image') {
            var html = '<div class="singleImageWrap singleImageStaticWrap"><img src="' + imageUrl + '" /></div>';
        } else {
            var html = '<div class="parallaxImageWrap singleImageWrap"><img src="' + imageUrl + '" /></div>';
        }

        return html;
    },
    init: function () {
        $(".imgWrap").click(function () {
            let imageUrl = $(this).data('url');
            mediaLibrary.insertImageInEditor(mediaLibrary.getWidgetHtml(imageUrl, mediaLibrary.imageWidgetStyle));
        });
    },
    open: function () {
        $('.imageLibrary').fadeIn('slow');
    },
    close: function () {
        $('.imageLibrary').fadeOut();
    },
    insertImageInEditor: function (image) {
        tinymce.activeEditor.insertContent(image);
        mediaLibrary.close();
    }
}
