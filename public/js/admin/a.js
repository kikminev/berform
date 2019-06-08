a = {
    deleteObject: function (url, id) {
        if (!confirm('Are you sure?')) {
            return false;
        }

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                if (null == response.error) {
                    $('#' + id).hide('slow');
                }
            }
        });
    },
    generateSlug: function () {
        var name = $(".slug_source").val();
        var slug = $(".slug_input").val();
        if ('' !== slug) {
            return false;
        }

        $(".slug_input").val(url_slug(name));
    },
    selectSite: function (id) {

    },
    agLink: function (url) {
        $.get(url);
    },
    openRegistration: function (addTemplateUrl, redirectUrl) {
        $.ajax({
            type: "GET",
            url: addTemplateUrl,
            success: function (response) {
                window.location = redirectUrl;
            }
        });
    }
}
