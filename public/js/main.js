uSite = {
    centerContentVertically: function (element) {
        const windowHeight = window.innerHeight;

        const verticalCenter = Math.floor(windowHeight/2);
        const elementHeight = $(element + ' .contentContainer').height();

        let marginTop = verticalCenter - (elementHeight / 2);

        if(marginTop <= 0 || (windowHeight / 3) < elementHeight) {
            marginTop = verticalCenter;
        }

        $(element).css('margin-top', marginTop+'px');
        $(element).animate({'opacity' : 1}, 1000);
    },
    centerElementVertically: function (element) {
        const verticalCener = Math.floor(window.innerHeight/2);
        const elementHeight = $(element).height();

        let marginTop = verticalCener - (elementHeight / 2);

        if(marginTop <= 0) {
            marginTop = verticalCener;
        }

        $(element).css('margin-top', marginTop+'px');
        $(element).animate({'opacity' : 1}, 1000);
    }
};

