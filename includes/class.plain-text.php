<?php
if( !class_exists('Recipe_PasteAsPlainText') ){
class Recipe_PasteAsPlainText {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		if( !is_admin() ){
			add_filter( 'tiny_mce_before_init', array( $this, 'forcePasteAsPlainText' ) );
			add_filter( 'teeny_mce_before_init', array( $this, 'forcePasteAsPlainText' ) );
			add_filter( 'teeny_mce_plugins', array( $this, 'loadPasteInTeeny' ) );
			add_filter( 'mce_buttons_2', array( $this, 'removePasteAsPlainTextButton' ) );
		}
	}

	function forcePasteAsPlainText( $mceInit ) {

		global $tinymce_version;

		if ( $tinymce_version[0] < 4 ) {
			$mceInit[ 'paste_text_sticky' ] = true;
			$mceInit[ 'paste_text_sticky_default' ] = true;
		} else {
			$mceInit[ 'paste_as_text' ] = true;
		}

		return $mceInit;
	}

	function loadPasteInTeeny( $plugins ) {

		return array_merge( $plugins, (array) 'paste' );

	}

	function removePasteAsPlainTextButton( $buttons ) {

		if( ( $key = array_search( 'pastetext', $buttons ) ) !== false ) {
			unset( $buttons[ $key ] );
		}

		return $buttons;

	}

}
}
new Recipe_PasteAsPlainText();
?>