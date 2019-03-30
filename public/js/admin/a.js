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
        var name = $("#page_name").val();
        var slug = $("#page_slug").val();
        if ('' !== slug) {
            return false;
        }

        $("#page_slug").val(url_slug(name));
    }
}
