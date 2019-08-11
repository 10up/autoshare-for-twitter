/**
 * Handles the 10up Auto Tweet JS.
 *
 * @todo soooo much dependency :facepalm:
 */
( function( $ ) {
	'use strict';

	const $tweetPost = $( '#tenup-auto-tweet-enable' ),
		$icon = $( '#tenup-auto-tweet-icon' ),
		$tweetText = $( '#tenup-auto-tweet-text' ),
		$editLink = $( '#tenup-auto-tweet-edit' ),
		$editBody = $( '#tenup-auto-tweet-override-body' ),
		$hideLink = $( '.cancel-tweet-text' ),
		counterWrap = document.getElementById( 'tenup-auto-tweet-counter-wrap' ),
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
		$( '#tenup-auto-tweet-override-body' ).slideToggle();
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
	 */
	function onRequestFail() {
		$icon.removeClass( 'pending' );
		$tweetPost.prop( 'checked', false );
	}

	/**
	 * AJAX handler
	 * @param event
	 */
	function handleRequest( event, status = $tweetPost.prop( 'checked' ) ) {
		const data = {};
		data[adminAutotweet.enableAutotweetKey] = status;
		data[adminAutotweet.tweetBodyKey] = $tweetText.val();

		// Process AJAX action.
		$.ajax( adminAutotweet.restUrl, {
			beforeSend: function( xhr ) {
				pendingStatus();
				xhr.setRequestHeader( 'X-WP-Nonce', adminAutotweet.nonce );
			},
			data: data,
			dataType: 'json',
			error: onRequestFail,
			success: function( response ) {
				// Remove the pending and enabled/disabled classes depending on AJAX response
				$icon.removeClass( 'pending' );
				if ( 'true' === response.enabled ) {
					$icon.toggleClass( 'enabled' );
					$tweetPost.prop( 'checked', true );
				} else {
					$icon.toggleClass( 'disabled' );
					$tweetPost.prop( 'checked', false );
				}
			},
			type: 'POST',
		} );
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

	/**
	 * Helper for toggling classes to indicate something is happening.
	 */
	function pendingStatus() {
		$icon.toggleClass( 'pending' );
		$icon.removeClass( 'enabled' );
		$icon.removeClass( 'disabled' );
	}
} )( jQuery );
