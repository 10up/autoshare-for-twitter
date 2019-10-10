/**
 * Handles the Autotweet JS.
 *
 * @todo soooo much dependency :facepalm:
 */
( function( $ ) {
	'use strict';

	const $tweetPost = $( '#autotweet-enable' ),
		$icon = $( '#autotweet-icon' ),
		$tweetText = $( '#autotweet-text' ),
		$editLink = $( '#autotweet-edit' ),
		$editBody = $( '#autotweet-override-body' ),
		$hideLink = $( '.cancel-tweet-text' ),
		errorMessageContainer = document.getElementById( 'autotweet-error-message' ),
		counterWrap = document.getElementById( 'autotweet-counter-wrap' ),
		limit = 280;

	// Add enabled class if checked
	if ( $tweetPost.prop( 'checked' ) ) {
		$icon.addClass( 'enabled' );
	}

	// Event handlers.
	$tweetPost.on( 'click', handleRequest );
	$tweetText.change( handleRequest );
	$editLink.on( 'click', function() {
		$editBody.slideToggle();
		updateRemainingField();
		$( this ).hide();
	} );
	$tweetText.on( 'keyup', function() {
		updateRemainingField();
	} );
	$hideLink.on( 'click', function( e ) {
		e.preventDefault();
		$( '#autotweet-override-body' ).slideToggle();
		$editLink.show();
	} );

	// Runs on page load to auto-enable posts to be tweeted
	window.onload = function( event ) {
		if ( '' === adminAutotweet.currentStatus ) {
			handleRequest( event, true );
		}
	};

	/**
	 * Callback for failed requests.
	 *
	 * @param {Object} error Error object.
	 */
	function onRequestFail( error ) {
		let errorText = '';
		if ( 'statusText' in error && 'status' in error ) {
			errorText = `${ adminAutotweet.errorText } ${ error.status }: ${ error.statusText }`;
		} else {
			errorText = adminAutotweet.unkonwnErrorText;
		}

		errorMessageContainer.innerText = errorText;

		$icon.removeClass( 'pending' );
		$tweetPost.prop( 'checked', false );
	}

	/**
	 * AJAX handler
	 *
	 * @param {Object} event Click/change event.
	 * @param {string} status The status of whether to autotweet.
	 */
	function handleRequest( event, status = $tweetPost.prop( 'checked' ) ) {
		const data = {};
		data[ adminAutotweet.enableAutotweetKey ] = status;
		data[ adminAutotweet.tweetBodyKey ] = $tweetText.val();

		wp.apiFetch( {
			url: adminAutotweet.restUrl,
			data,
			method: 'POST',
			parse: false, // We'll check the response for errors.
		} )
			.then( function( response ) {
				if ( ! response.ok ) {
					throw response;
				}

				return response.json();
			} )
			.then( function( responseData ) {
				errorMessageContainer.innerText = '';

				$icon.removeClass( 'pending' );
				if ( responseData.enabled ) {
					$icon.toggleClass( 'enabled' );
					$tweetPost.prop( 'checked', true );
				} else {
					$icon.toggleClass( 'disabled' );
					$tweetPost.prop( 'checked', false );
				}
			} )
			.catch( onRequestFail );
	}

	/**
	 * Updates the counter
	 */
	function updateRemainingField() {
		const count = $tweetText.val().length;

		$( counterWrap ).text( count );

		// Toggle the .over-limit class.
		if ( limit < count ) {
			counterWrap.classList.add( 'over-limit' );
		} else if ( counterWrap.classList.contains( 'over-limit' ) ) {
			counterWrap.classList.remove( 'over-limit' );
		}
	}
// eslint-disable-next-line func-call-spacing
} ( jQuery ) );
