( function() {

	document.addEventListener( 'DOMContentLoaded', function() {
		var credActions = document.querySelector( '.credentials-actions' ),
			openCredSettingsBtn,
			closeCredSettingsBtn;

		if( ! credActions ) {
			return;
		}

		openCredSettingsBtn = credActions.querySelector( '.open' );
		closeCredSettingsBtn = credActions.querySelector( '.close' );
	;

		openCredSettingsBtn.addEventListener( 'click', function() {
			credActions.classList.remove( 'connected' );
		} );

		closeCredSettingsBtn.addEventListener( 'click', function() {
			credActions.classList.add( 'connected' );
		} );

	} );
} )();
