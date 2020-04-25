landingSite = {
    openRegistration: function (addTemplateUrl, redirectUrl) {
        $.ajax({
            type: "GET",
            url: addTemplateUrl,
            success: function (response) {
                window.location = redirectUrl;
            }
        });
    },

    randomizeWelcomeScreen() {
        $('#welcome-screen').css('height', $(window).height() - 40);
        var backgrounds = ['rp_d1', 'rp_d2', 'rp_d4'];
        $('#welcome-screen').addClass(backgrounds[Math.floor(Math.random()*backgrounds.length)]);
    }
}
