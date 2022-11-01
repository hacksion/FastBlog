/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */


CKEDITOR.editorConfig = function(config) {
    config.toolbarGroups = [
        {
            name: 'basicstyles',
            groups: ['basicstyles', 'cleanup']
        },
        {
            name: 'paragraph',
            groups: ['list', 'blocks', 'align', 'bidi']
        },
        {
            name: 'styles'
        },
        {
            name: 'colors'
        },
        {
            name: 'editing',
            groups: ['find', 'selection', 'spellchecker']
        },
        {
            name: 'links'
        },
        {
            name: 'insert'
        },
        {
            name: 'forms'
        },
        {
            name: 'tools'
        },
        {
            name: 'document',
            groups: ['mode', 'document', 'doctools', 'Preview']
        },
        {
            name: 'others'
        },
    ];

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Subscript,Superscript,Styles';

    // Set the most common block elements.
    config.format_tags = 'p;h2;h3;h4;pre';

    //config.stylesSet = [];

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';


    config.filebrowserImageBrowseUrl = locUrl + 'plugin/ckeditor/kcfinder/browse.php?type=images';

    //タグの要素を消さないようにする　デフォルトはセキュリティー上消しているもよう
    //config.allowedContent = true;

    //config.height = '30em';
    config.autoParagraph = false;
    config.fillEmptyBlocks = false;
    config.image_previewText = ' ';
    config.extraPlugins = 'preview,justify';
    config.contentsCss = [ locUrl + 'css/style.css', locUrl + 'css/custom.css'];
};
