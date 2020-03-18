/**
 * Handles the Autoshare JS.
 *
 * @todo soooo much dependency :facepalm:
 */
(function($) {
	'use strict';

	var $tweetPost = $('#autoshare-for-twitter-enable'),
		$icon = $('#autoshare-for-twitter-icon'),
		$tweetText = $('#autoshare-for-twitter-text'),
		$editLink = $('#autoshare-for-twitter-edit'),
		$editBody = $('#autoshare-for-twitter-override-body'),
		$hideLink = $('.cancel-tweet-text'),
		errorMessageContainer = document.getElementById('autoshare-for-twitter-error-message'),
		counterWrap = document.getElementById('autoshare-for-twitter-counter-wrap'),
		limit = 280;

	// Add enabled class if checked
	if ($tweetPost.prop('checked')) {
		$icon.addClass('enabled');
	}

	// Event handlers.
	$tweetPost.on('click', handleRequest);
	$tweetText.change(handleRequest);
	$editLink.on('click', function() {
		$editBody.slideToggle();
		updateRemainingField();
		$(this).hide();
	});
	$tweetText.on('keyup', function() {
		updateRemainingField();
	});
	$hideLink.on('click', function(e) {
		e.preventDefault();
		$('#autoshare-for-twitter-override-body').slideToggle();
		$editLink.show();
	});

	// Runs on page load to auto-enable posts to be tweeted
	window.onload = function(event) {
		if ('' === adminAutoshareForTwitter.currentStatus) {
			handleRequest(event, true);
		}
	};

	/**
	 * Callback for failed requests.
	 */
	function onRequestFail(error) {
		var errorText = '';
		if ('statusText' in error && 'status' in error) {
			errorText = `${adminAutoshareForTwitter.errorText} ${error.status}: ${error.statusText}`;
		} else {
			errorText = adminAutoshareForTwitter.unkonwnErrorText;
		}

		errorMessageContainer.innerText = errorText;

		$icon.removeClass('pending');
		$tweetPost.prop('checked', false);
		$('#submit').attr('disabled', true);
	}

	/**
	 * AJAX handler
	 * @param event
	 */
	function handleRequest(event, status = $tweetPost.prop('checked')) {
		var data = {};
		data[adminAutoshareForTwitter.enableAutoshareKey] = status;
		data[adminAutoshareForTwitter.tweetBodyKey] = $tweetText.val();
		$('#submit').attr('disabled', true);

		wp.apiFetch({
			url: adminAutoshareForTwitter.restUrl,
			data: data,
			method: 'POST',
			parse: false, // We'll check the response for errors.
		})
			.then(function(response) {
				if (!response.ok) {
					throw response;
				}

				return response.json();
			})
			.then(function(data) {
				errorMessageContainer.innerText = '';

				$icon.removeClass('pending');
				if (data.enabled) {
					$icon.toggleClass('enabled');
					$tweetPost.prop('checked', true);
				} else {
					$icon.toggleClass('disabled');
					$tweetPost.prop('checked', false);
				}

				$('#submit').attr('disabled', false);
			})
			.catch(onRequestFail);
	}

	/**
	 * Updates the counter
	 */
	function updateRemainingField() {
		var count = $tweetText.val().length;

		$(counterWrap).text(count);

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
