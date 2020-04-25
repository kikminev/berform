$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    landingSite.randomizeWelcomeScreen();
});

$(window).resize(function() {
    var windowHeight = $(window).height();
    landingSite.randomizeWelcomeScreen();
});
