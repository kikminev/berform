imageWidget = {
    transform: function () {

        $(".parallaxImageWrap").each(function (index) {
            // console.log(index + ": " + [0].attr('src'));
            var img = $(this).find('img');
            var src = $(img).attr('src');

            $(this).css('background-image', 'url("' + src + '")');

            var link = '<a style="background-image: url(\'' + src + '\')" data-fancybox="gallery-p-'+index+'" href="' + src + '"></a>';
            $(this).find('img').remove();
            $(this).html(link);
        });

        $(".singleImageStaticWrap").each(function (index) {
            // console.log(index + ": " + [0].attr('src'));
            var img = $(this).find('img');
            var src = $(img).attr('src');

            var link = '<a style="background-image: url(\'' + src + '\')" data-fancybox="gallery-s-'+index+'" href="' + src + '"></a>';
            $(this).find('img').remove();
            $(this).html(link);
        });

    }
}
