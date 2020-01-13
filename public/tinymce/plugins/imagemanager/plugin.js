// (function () {
//     var imagemanager = (function () {
//         'use strict';
//
//         tinymce.PluginManager.add("imagemanager", function (editor, url) {
//
//             function _onAction()
//             {
//                 // Do something when the plugin is triggered
//                 // editor.insertContent("<p>Content added from my Hello World plugin.</p>")
//                 $('.imageLibrary').show('slow');
//
//                 $('.imageLibrary').html(
//                     $('#imagesWrap').html()
//                 );
//
//             }
//
//             // Define the Toolbar button
//             editor.ui.registry.addButton('imagemanager', {
//                 text: "Image Library",
//                 onAction: _onAction
//             });
//
//             // Define the Menu Item
//             editor.ui.registry.addMenuItem('imagemanager', {
//                 text: 'Hello Menu Item',
//                 context: 'insert',
//                 onAction: _onAction
//             });
//         });
//     }());
// })();