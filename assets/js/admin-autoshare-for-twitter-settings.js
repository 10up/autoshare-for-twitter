( function() {

	document.addEventListener( 'DOMContentLoaded', function() {
		var credSetup = document.querySelector( '.credentials-setup' ),
			openCredSettingsBtn = credSetup.querySelector( '.open' ),
			closeCredSettingsBtn = credSetup.querySelector( '.close' ),
			postTypesWrap = document.querySelector( '.post-types' ),
			postTypesCheckboxes = document.getElementsByName( 'autoshare-for-twitter[enable_for]' );

		if ( openCredSettingsBtn ) {
			openCredSettingsBtn.addEventListener( 'click', function() {
				credSetup.classList.remove( 'connected' );
			} );
		}

		if ( closeCredSettingsBtn ) {
			closeCredSettingsBtn.addEventListener( 'click', function() {
				credSetup.classList.add( 'connected' );
			} );
		}

		if ( postTypesCheckboxes ) {
			postTypesCheckboxes.forEach( function( item ) {
				item.addEventListener( 'change', function( event) {
					if ( event.target.value === 'all' )
						return postTypesWrap.classList.add( 'hidden' );
					return postTypesWrap.classList.remove( 'hidden' );
				} );
			} );
		}
	} );

	jQuery( document ).ready(function() {
		jQuery( document ).on( 'click', ".astCopyToClipCard", function( e ) {
			e.preventDefault();
			$el = jQuery(this);
			var text = $el.closest("p.copy-container").find("span.copy-content").first().text();
			const $temp_input = jQuery( '<textarea style="opacity:0">' );
			jQuery( 'body' ).append( $temp_input );
			$temp_input.val( text ).trigger( 'select' );
			try {
				document.execCommand( 'copy' );
				const copyIcon = $el.html();
				$el.attr("disabled", "disabled");
				$el.text("Copied!");
				setTimeout(function(){
					$el.html(copyIcon);
					$el.removeAttr("disabled");
				}, 1000);
			} catch ( err ) {
				alert("Copy to clipboard failed");
			}

			$temp_input.remove();
		});
	});
} )();
