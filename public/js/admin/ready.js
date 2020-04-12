$(document).ready(function () {
    mediaLibrary.init();
    $(".slug_source").blur(function () {
        a.generateSlug();
    });

    // used by page editor as we want to show only link button
    tinymce.init({
        selector: ".htmlEditor textarea",
        height: 500,
        menubar: false,
        content_css : "/tinymce/skins/content/default/content.min.css, /css/admin/custom_tinymce.css",
        plugins: [
            'link'
        ],
        toolbar: 'link',
        contextmenu: "link",
    });

    tinymce.init({
        selector: ".htmlEditorBlog textarea",
        height: 500,
        menubar: false,
        content_css : "/tinymce/skins/content/default/content.min.css, /css/admin/custom_tinymce.css",
        external_plugins: {
            'singleImage': '/tinymce/plugins/imageLibrary/singleImage.js',
            'parallaxImage': '/tinymce/plugins/imageLibrary/parallaxImage.js',
        },
        plugins: [
            'link image'
        ],
        toolbar: 'link singleImage parallaxImage',
        contextmenu: "link singleImage parallaxImage",
    });

    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.modalWindow').hide();
        }
    });
});
