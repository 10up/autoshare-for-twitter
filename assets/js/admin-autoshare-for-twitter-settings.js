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

} )();
