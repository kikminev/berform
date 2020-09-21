userSite = {
    init: function () {
        imageWidget.transform();

        if(typeof(customInit) === typeof(Function)) {
            customInit();
        }
    }
}

$(document).ready(function () {
    userSite.init();
});