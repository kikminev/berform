(function () {
    var parallaxImage = (function () {
        'use strict';

        tinymce.PluginManager.add("parallaxImage", function (editor, url) {

            function _onAction()
            {
                mediaLibrary.setImageWidgetStyle('parallax');
                mediaLibrary.open();
            }

            editor.ui.registry.addButton('parallaxImage', {
                text: "Parallax",
                icon: "image",
                onAction: _onAction
            });

            editor.ui.registry.addMenuItem('parallaxImage', {
                text: "Parallax",
                icon: "image",
                context: 'insert',
                onAction: _onAction
            });
        });
    }());
})();