(function () {
    var singleImage = (function () {
        'use strict';

        tinymce.PluginManager.add("singleImage", function (editor, url) {

            function _onAction()
            {
                mediaLibrary.setImageWidgetStyle('image');
                mediaLibrary.open();
            }

            editor.ui.registry.addButton('singleImage', {
                text: "Image",
                icon: "image",
                onAction: _onAction
            });

            editor.ui.registry.addMenuItem('singleImage', {
                text: "Image",
                icon: "image",
                context: 'insert',
                onAction: _onAction
            });
        });
    }());
})();