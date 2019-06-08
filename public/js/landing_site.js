landingSite = {
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
