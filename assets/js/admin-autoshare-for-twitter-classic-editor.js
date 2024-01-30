/**
 * Handles the Autoshare JS.
 *
 * @todo soooo much dependency :facepalm:
 */
(function ($) {
	'use strict';

	var $tweetPost = $('#autoshare-for-twitter-enable'),
		$tweetText = $('#autoshare-for-twitter-text'),
		$editLink = $('#autoshare-for-twitter-edit'),
		$editBody = $('#autoshare-for-twitter-override-body'),
		$hideLink = $('.cancel-tweet-text'),
		$allowTweetImage = $('#autoshare-for-twitter-tweet-allow-image'),
		errorMessageContainer = document.getElementById(
			'autoshare-for-twitter-error-message'
		),
		counterWrap = document.getElementById(
			'autoshare-for-twitter-counter-wrap'
		),
		allowTweetImageWrap = $(
			'.autoshare-for-twitter-tweet-allow-image-wrap'
		),
		limit = 280;
	const { __, sprintf } = wp.i18n;

	// Event handlers.
	$tweetPost.on('click', handleRequest);
	$tweetText.change(handleRequest);
	$tweetPost.change(toggleVisibility);
	$allowTweetImage.change(handleRequest);
	$editLink.on('click', function () {
		$editBody.slideToggle();
		updateRemainingField();
		$(this).hide();
	});
	$tweetText.on('keyup', function () {
		updateRemainingField();
	});
	$hideLink.on('click', function (e) {
		e.preventDefault();
		$('#autoshare-for-twitter-override-body').slideToggle();
		$editLink.show();
	});
	$('input.autoshare-for-twitter-account-checkbox').on(
		'change',
		handleRequest
	);

	// Runs on page load to auto-enable posts to be tweeted
	window.onload = function (event) {
		if ('' === adminAutoshareForTwitter.currentStatus) {
			handleRequest(event, true);
		}
		updateRemainingField();
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

		$tweetPost.prop('checked', false);
		$('#publish').prop('disabled', false);
	}

	/**
	 * AJAX handler
	 * @param event
	 */
	function handleRequest(event, status = $tweetPost.prop('checked')) {
		let data = {};
		let enabledAccounts = [];
		$('input.autoshare-for-twitter-account-checkbox:checked').each(
			function () {
				enabledAccounts.push($(this).val());
			}
		);
		data[adminAutoshareForTwitter.enableAutoshareKey] = status;
		data[adminAutoshareForTwitter.tweetBodyKey] = $tweetText.val();
		data[adminAutoshareForTwitter.allowTweetImageKey] =
			$allowTweetImage.prop('checked');
		data[adminAutoshareForTwitter.tweetAccountsKey] = enabledAccounts;
		$('#publish').prop('disabled', true);

		wp.apiFetch({
			url: adminAutoshareForTwitter.restUrl,
			data: data,
			method: 'POST',
			parse: false, // We'll check the response for errors.
		})
			.then(function (response) {
				if (!response.ok) {
					throw response;
				}

				return response.json();
			})
			.then(function (data) {
				errorMessageContainer.innerText = '';

				if (data.enabled) {
					$tweetPost.prop('checked', true);
				} else {
					$tweetPost.prop('checked', false);
				}

				if (data.allowImage) {
					$allowTweetImage.prop('checked', true);
				} else {
					$allowTweetImage.prop('checked', false);
				}

				$('#publish').prop('disabled', false);
			})
			.catch(onRequestFail);
	}

	/**
	 * Calculates the permalink length
	 */
	function getPermalinkLength() {
		let permalinkLength = 0;
		if ($('#sample-permalink').length) {
			if (
				! adminAutoshareForTwitter.isLocalSite &&
				! isNaN( adminAutoshareForTwitter.twitterURLLength )
			) {
				// According to this page https://developer.twitter.com/en/docs/counting-characters, all URLs are transformed to a uniform length
				permalinkLength = Number(
					adminAutoshareForTwitter.twitterURLLength
				);
			} else {
				// Calculate the permalink length.
				const slug = jQuery('#editable-post-name-full').text();
				const aTagContents = jQuery('#sample-permalink > a')[0].innerHTML;
				const permalinkPrefix = 'string' === typeof aTagContents ? aTagContents.split('<span')[0] : '';
				const permalink = permalinkPrefix + slug + '/';
				permalinkLength = permalink.length;
			}
		}
		// +5 because of the space between body and URL and the ellipsis.
		permalinkLength += 5;
		return permalinkLength;
	}

	/**
	 * Updates the counter
	 */
	function updateRemainingField() {
		const permalinkLength = getPermalinkLength();

		var count = $tweetText.val().length + permalinkLength;
		$tweetText.attr('maxlength', limit - permalinkLength);

		$(counterWrap).text(count);

		// Toggle the .over-limit class.
		if (limit <= count) {
			counterWrap.classList.remove('near-limit');
			counterWrap.classList.add('over-limit');
			/* translators: %d is post message character count */
			$(counterWrap).text(
				sprintf(__('%d - Too Long!', 'autoshare-for-twitter'), count)
			);
		} else if (240 <= count) {
			counterWrap.classList.remove('over-limit');
			counterWrap.classList.add('near-limit');
			/* translators: %d is post message character count */
			$(counterWrap).text(
				sprintf(
					__('%d - Getting Long!', 'autoshare-for-twitter'),
					count
				)
			);
		} else {
			counterWrap.classList.remove('near-limit');
			counterWrap.classList.remove('over-limit');
		}
	}

	// Update the counter when the permalink is changed.
	$('#titlediv').on('focus', '.edit-slug', function () {
		updateRemainingField();
	});

	// Show/Hide "Use featured image in Tweet" checkbox.
	if (allowTweetImageWrap && wp.media.featuredImage) {
		toggleAllowImageVisibility();
		// Listen event for add/remove featured image.
		wp.media.featuredImage.frame().on('select', toggleAllowImageVisibility);
		$('#postimagediv').on(
			'click',
			'#remove-post-thumbnail',
			toggleAllowImageVisibility
		);
	}

	/**
	 * Show/Hide accounts and visibility options.
	 */
	function toggleVisibility(event) {
		toggleAllowImageVisibility(event);
		const autoshareEnabled = $tweetPost.prop('checked');
		const accountsWrap = $('.autoshare-for-twitter-accounts-wrapper');
		if (autoshareEnabled) {
			accountsWrap.show();
		} else {
			accountsWrap.hide();
		}
	}

	/**
	 * Show/Hide "Use featured image in Tweet" checkbox.
	 */
	function toggleAllowImageVisibility(event) {
		let hasMedia = wp.media.featuredImage.get();
		// Handle remove post thumbnail click
		if (
			event &&
			event.target &&
			'remove-post-thumbnail' === event.target.id &&
			'click' === event.type
		) {
			hasMedia = -1;
		}

		const tweetNow = $('#tweet_now').length;
		const autoshareEnabled = $tweetPost.prop('checked');
		// Autoshare is enabled and post has featured image.
		if (hasMedia > 0 && (autoshareEnabled || tweetNow)) {
			allowTweetImageWrap.show();
		} else {
			allowTweetImageWrap.hide();
		}
	}

	// Tweet Now functionality.
	$('#tweet_now').on('click', function () {
		$('#autoshare-for-twitter-error-message').html('');
		$(this).addClass('disabled');
		$('.autoshare-for-twitter-tweet-now-wrapper span.spinner').addClass(
			'is-active'
		);

		const postId = $('#post_ID').val();
		const body = new FormData();
		body.append('action', adminAutoshareForTwitter.retweetAction);
		body.append('nonce', adminAutoshareForTwitter.nonce);
		body.append('post_id', postId);
		body.append('is_classic', 1);

		// Send request to Tweet now.
		fetch(ajaxurl, {
			method: 'POST',
			body,
		})
			.then((response) => response.json())
			.then((response) => {
				if (
					response &&
					response.data &&
					((response.success && response.data.message) ||
						(false === response.success &&
							false === response.data.is_retweeted))
				) {
					$('.autoshare-for-twitter-status-logs-wrapper').html(
						response.data.message
					);
					if (response.data.is_retweeted) {
						$tweetText.val(''); // Reset the tweet text.
					}
				} else {
					$('#autoshare-for-twitter-error-message').html(
						adminAutoshareForTwitter.unknownErrorText
					);
				}
			})
			.catch((error) => {
				if (error.message) {
					$('#autoshare-for-twitter-error-message').html(
						error.message
					);
				} else {
					$('#autoshare-for-twitter-error-message').html(
						adminAutoshareForTwitter.unknownErrorText
					);
				}
			})
			.finally(() => {
				$(this).removeClass('disabled');
				$(
					'.autoshare-for-twitter-tweet-now-wrapper span.spinner'
				).removeClass('is-active');
			});
	});

	// Toggle Tweet Now panel
	jQuery('#autoshare_for_twitter_metabox .tweet-now-button').on(
		'click',
		function (e) {
			e.preventDefault();
			$editBody.show();
			jQuery(this).find('span').toggleClass('dashicons-arrow-up-alt2');
			jQuery('.autoshare-for-twitter-tweet-now-wrapper').slideToggle();
		}
	);
})(jQuery);
