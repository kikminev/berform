$(document).ready(function () {
    mediaLibrary.init();
    $(".slug_source").blur(function () {
        a.generateSlug();
    });

    // used by PAGE editor as it differs from the blog editor
    tinymce.init({
        selector: ".htmlEditor textarea",
        height: 500,
        menubar: false,
        content_css : "/tinymce/skins/content/default/content.min.css, /css/admin/custom_tinymce.css",
        plugins: [
            'link',
            'code'
        ],
        toolbar: 'link code',
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
            'link image code codesample'
        ],
        toolbar: 'link singleImage parallaxImage code codesample',
        contextmenu: "link singleImage parallaxImage",
    });

    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('.modalWindow').hide();
        }
    });
});
