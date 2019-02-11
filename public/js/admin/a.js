a = {
    deleteObject: function (url, id) {
        if(!confirm('Are you sure?')) {
            return false;
        }

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                if (null == response.error) {
                    $('#'+id).hide('slow');
                }
            }
        });
    }
}
