(function () {
    var imagemanager = (function () {
        'use strict';

        tinymce.PluginManager.add("imagemanager", function (editor, url) {

            function _onAction()
            {
                // Do something when the plugin is triggered
                editor.insertContent("<p>Content added from my Hello World plugin.</p>")
            }

            // Define the Toolbar button
            editor.ui.registry.addButton('imagemanager', {
                text: "Hello Button",
                onAction: _onAction
            });

            // Define the Menu Item
            editor.ui.registry.addMenuItem('imagemanager', {
                text: 'Hello Menu Item',
                context: 'insert',
                onAction: _onAction
            });

            // Return details to be displayed in TinyMCE's "Help" plugin, if you use it
            // This is optional.
            return {
                getMetadata: function () {
                    return {
                        name: "Hello World example",
                        url: "https://www.martyfriedel.com/blog/tinymce-5-creating-a-plugin-with-a-dialog-and-custom-icons"
                    };
                }
            };
        });
    }());
})();