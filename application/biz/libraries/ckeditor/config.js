/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    config.extraPlugins='video'; 
    config.filebrowserVideoBrowseUrl;
	config.allowedContent = true;
	extraAllowedContent: 'video[*]{*}';
    
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
config.filebrowserBrowseUrl = 'http://devmys.lifetek.vn/public/ckfinder/ckfinder.html';

config.filebrowserImageBrowseUrl = 'http://devmys.lifetek.vn/public/ckfinder/ckfinder.html?type=Images';

config.filebrowserFlashBrowseUrl = 'http://devmys.lifetek.vn/public/ckfinder/ckfinder.html?type=Flash';

config.filebrowserUploadUrl = 'http://devmys.lifetek.vn/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=File';

config.filebrowserImageUploadUrl = 'http://devmys.lifetek.vn/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';

config.filebrowserFlashUploadUrl = 'http://devmys.lifetek.vn/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
config.pasteFromWordRemoveFontStyles=false;
config.pasteFromWordRemoveStyles=false;
};
