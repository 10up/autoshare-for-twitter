/**
 * Handles the 10up Auto Tweet JS.
 *
 * @todo soooo much dependency :facepalm:
 */
(function ($) {
	'use strict';

	var $tweetPost = $('#tenup-auto-tweet-enable'),
		$icon = $('#tenup-auto-tweet-icon'),
		$postTitle = $('input[name="post_title"]'),
		$tweetText = $('#tenup-auto-tweet-text'),
		$editLink = $('#tenup-auto-tweet-edit'),
		$editBody = $('#tenup-auto-tweet-override-body'),
		$hideLink = $('.cancel-tweet-text'),
		counterWrap = document.getElementById('tenup-auto-tweet-counter-wrap'),
		limit = 280;


	// Add enabled class if checked
	if ($tweetPost.prop('checked')) {
		$icon.addClass('enabled')
	}

	// Event handlers.
	$tweetPost.on('click', handleRequest );
	$tweetText.change(handleRequest);
	$editLink.on('click', function() {
		$editBody.slideToggle();
		updateRemainingField();
		$(this).hide();
	})
	$tweetText.on('keyup', function() {
		updateRemainingField();
	})
	$hideLink.on('click', function(e) {
		e.preventDefault();
		$('#tenup-auto-tweet-override-body').slideToggle();
		$editLink.show();
	});

	// Runs on page load to auto-enable posts to be tweeted
	window.onload = function(event) {
		if ( '' === adminTUAT.currentStatus ) {
			handleRequest(event, true)
		}
	}

	/**
	 * AJAX handler
	 * @param event
	 */
	function handleRequest(event, status = $tweetPost.prop('checked') ) {

		// Process AJAX action.
		$.ajax(ajaxurl, {
			'data': {
				'action': 'tenup_auto_tweet',
				'checked': status,
				'value': $tweetPost.val(),
				'nonce': adminTUAT.nonce,
				'post_id': adminTUAT.postId,
				'text': $tweetText.val(),
				'type': event.type
			},
			'beforeSend': pendingStatus(),
			'dataType': 'json',
			'success': function (response) {

				// If successful
				if (response.success) {

					// Remove the pending and enabled/disabled classes depending on AJAX response
					$icon.removeClass('pending');
					if ('true' === response.data.enabled) {
						$icon.toggleClass('enabled');
						$tweetPost.prop('checked', true);
					} else {
						$icon.toggleClass('disabled');
						$tweetPost.prop('checked', false);
					}

					// Something went wrong with the AJAX request. Remove the class and uncheck the box.
				} else {
					$icon.removeClass('pending');
					$tweetPost.prop('checked', false);
				}

			}

		});
	};

	/**
	 * Updates the counter
	 */
	function updateRemainingField() {
		var count = $tweetText.val().length;

		counterWrap.innerHTML = '<span class="counter">' + count + '</span>';

		// Toggle the .over-limit class.
		if (limit < count) {
			counterWrap.classList.add('over-limit');

		} else if (counterWrap.classList.contains('over-limit')) {
			counterWrap.classList.remove('over-limit');
		}
	}

	/**
	 * Helper for toggling classes to indicate something is happening.
	 */
	function pendingStatus() {
		$icon.toggleClass('pending');
		$icon.removeClass('enabled');
		$icon.removeClass('disabled');
	}

})(jQuery);
