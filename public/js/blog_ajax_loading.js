jQuery(document).ready(function ($) {
    //set some variables
    var isAnimating = false,
        firstLoad = false,
        newScaleValue = 1;

    //cache DOM elements
    var dashboard = $('body'),
        mainContent = $('.cd-main'),
        loadingBar = $('#cd-loading-bar');

    //select a new section
    dashboard.on('click', 'a.ajaxLink', function (event) {
        event.preventDefault();
        var target = $(this),
            //detect which section user has chosen
            sectionTarget = target.data('menu'),
            sectionId = target.data('sectionId');

        if (!target.hasClass('selected') && !isAnimating) {
            //if user has selected a section different from the one alredy visible - load the new content
            loadNewContent(sectionTarget, sectionId, true);
        }

        firstLoad = true;
    });

    //detect the 'popstate' event - e.g. user clicking the back button
    $(window).on('popstate', function () {
        if (firstLoad) {
            /*
            Safari emits a popstate event on page load - check if firstLoad is true before animating
            if it's false - the page has just been loaded
            */
            var newPageArray = location.pathname.split('/'),
                //this is the url of the page to be loaded
                newPage = newPageArray[newPageArray.length - 1].replace('', '');
            if (!isAnimating) {
                loadNewContent(newPage, 'home', false);
            }
        }
        firstLoad = true;
    });

    function loadNewContent(newSection, sectionId, bool) {
        isAnimating = true;

        loadingBar.css('height', $( window ).height());
        loadingBar.slideToggle(function () {
            $('.visibleSection').hide();
            loadingBar.css('top', '0px');
            window.scrollTo(0, 0);
        });

        newSection = (newSection === '') ? 'home' : newSection;
        sectionId = (sectionId === '') ? 'home' : sectionId;

        //update dashboard
        dashboard.find('.ajaxLink').removeClass('selected');
        dashboard.find('*[data-section-id="' + sectionId + '"]').addClass('selected').parent('li').siblings('li').children('.selected').removeClass('selected');


        setTimeout(function () {

            // todo: remove the old sections
            //create a new section element and insert it into the DOM
            var section = $('<section class="cd-section overflow-hidden ' + sectionId + '"></section>').appendTo(mainContent);
            //load the new content from the proper html file
            section.load(newSection + ' .cd-section > *', function (event) {

                $('.visibleSection').slideUp(function () {
                    $('.visibleSection').removeClass('visibleSection');
                    section.addClass('visibleSection');

                    $('.visibleSection').slideDown();
                });

                if (newSection !== window.location && bool) {
                    window.history.pushState({path: newSection}, '', newSection);
                }

                $('#cd-loading-bar').animate({
                    height: 1
                }, 500, function(){
                    loadingBar.css('top', '');
                    $(this).hide();
                });

                isAnimating = false;
                userSite.init();

                var body = $("html, body");
            });

        }, 100);
    }
});