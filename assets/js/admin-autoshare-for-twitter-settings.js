( function() {

	document.addEventListener( 'DOMContentLoaded', function() {
		var credSetup = document.querySelector( '.credentials-setup' ),
			openCredSettingsBtn = credSetup.querySelector( '.open' ),
			closeCredSettingsBtn = credSetup.querySelector( '.close' ),
			postTypesWrap = document.querySelector( '.post-types' ),
			postTypesCheckboxes = document.getElementsByName( 'autoshare-for-twitter[enable_for]' );

		openCredSettingsBtn.addEventListener( 'click', function() {
			credSetup.classList.remove( 'connected' );
		} );

		closeCredSettingsBtn.addEventListener( 'click', function() {
			credSetup.classList.add( 'connected' );
		} );

		postTypesCheckboxes.forEach( function( item ) {
			item.addEventListener( 'change', function( event) {
				if ( event.target.value === 'all' )
					return postTypesWrap.classList.add( 'hidden' );
				return postTypesWrap.classList.remove( 'hidden' );
			} );
		} );


	} );

	/**
	 * Get closest Element.
	 *
	 * @param {HTMLElement} el Element to get parent.
	 * @param {string} selector CSS selector to match.
	 */
	function getClosest( el, selector ) {
		if ( ! window.Element.prototype.matches ) {
			// Polyfill from https://developer.mozilla.org/en-US/docs/Web/API/Element/matches.
			window.Element.prototype.matches =
				window.Element.prototype.matchesSelector ||
				window.Element.prototype.mozMatchesSelector ||
				window.Element.prototype.msMatchesSelector ||
				window.Element.prototype.oMatchesSelector ||
				window.Element.prototype.webkitMatchesSelector ||
				function( s ) {
					var matches = ( this.document || this.ownerDocument ).querySelectorAll( s ),
						i = matches.length;

					while ( --i >= 0 && matches.item( i ) !== this ) { }

					return i > -1;
				};
		}

		// Get the closest matching elent
		for ( ; el && el !== document; el = el.parentNode ) {
			if ( el.matches( selector ) ) {
				return el;
			}
		}

		return null;
	}

} )();
