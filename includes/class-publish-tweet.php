<?php
/**
 * Class to handle Tweet publishing.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core\Publish_Tweet;

use TenUp\AutoshareForTwitter\Utils as Utils;
use TenUp\AutoshareForTwitter\Core\Twitter_API as Twitter_API;

use const \TenUp\AutoshareForTwitter\Core\Post_Meta\TWEET_ALLOW_IMAGE;

/**
 * Publish tweets to twitter.
 */
class Publish_Tweet {

	/**
	 * Twitter API.
	 *
	 * @var Twitter_API The Twitter API Class Instance.
	 */
	private $twitter_api;

	/**
	 * Construct the PublishTweet class.
	 */
	public function __construct() {}

	/**
	 * POST a status update.
	 *
	 * @param string   $body The tweet body.
	 * @param \WP_Post $post The post object.
	 * @param int|null $account_id The Twitter account ID.
	 *
	 * @return object
	 */
	public function status_update( $body, $post, $account_id = null ) {
		$this->twitter_api = new Twitter_API( $account_id );

		// Bail early if the body text is empty.
		if ( empty( $body ) ) {
			return;
		}

		$update_data = array(
			'text' => $body, // URL encoding handled by buildHttpQuery vai TwitterOAuth.
		);

		$is_image_allowed = Utils\get_autoshare_for_twitter_meta( $post->ID, TWEET_ALLOW_IMAGE );
		if ( 'no' !== $is_image_allowed ) {
			$media_id = $this->get_upload_data_media_id( $post );
			if ( $media_id ) {
				$update_data['media'] = array(
					'media_ids' => [ (string) $media_id ],
				);
			}
		}

		/**
		 * Filters data posted to Twitter.
		 *
		 * @see https://twitteroauth.com/
		 * @see https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update
		 *
		 * @param array   Data sent to the Twitter endpoint.
		 * @param \WP_Post The post associated with the tweet.
		 */
		$update_data = apply_filters( 'autoshare_for_twitter_tweet', $update_data, $post );

		/**
		 * Filters the client response before it is sent, to facilitate caching and testing.
		 *
		 * @param null|mixed Any non-null value will suppress the request to the Twitter endpoint.
		 * @param array      Data to send to the Twitter endpoint.
		 * @param \WP_Post    The post associated with the tweet.
		 */
		$response = apply_filters( 'autoshare_for_twitter_pre_status_update', null, $update_data, $post );

		if ( ! is_null( $response ) ) {
			return $response;
		}

		// Send tweet to Twitter.
		$response = $this->twitter_api->tweet( $update_data );

		/**
		 * Fires after the request to the Twitter endpoint had been made.
		 *
		 * @param array|object The response from the Twitter endpoint.
		 * @param array        Data to send to the Twitter endpoint.
		 * @param \WP_Post      The post associated with the tweet.
		 */
		do_action( 'autoshare_for_twitter_after_status_update', $response, $update_data, $post );

		return $response;
	}

	/**
	 * Provides the max filesize for images uploaded to Twitter.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_max_filesize() {
		/**
		 * Filters the maximum file size of images to send to Twitter.
		 *
		 * @param int Default 5MB.
		 */
		return apply_filters( 'autoshare_for_twitter_max_image_size', 5000000 ); // 5MB default.
	}

	/**
	 * Retrieves the URL of the largest version of an attachment image accepted by the ComputerVision service.
	 *
	 * @param string $full_image The path to the full-sized image source file.
	 * @param array  $sizes      Intermediate size data from attachment meta.
	 * @return string|null The image path, or null if no acceptable image found.
	 */
	public function get_largest_acceptable_image( $full_image, $sizes ) {
		$file_size     = @filesize( $full_image ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$max_file_size = $this->get_max_filesize();

		if ( $file_size && $max_file_size >= $file_size ) {
			return $full_image;
		}

		if ( empty( $sizes ) ) {
			return null;
		}

		// Sort the image sizes in order of total width + height, descending.
		$sort_sizes = function( $size_1, $size_2 ) {
			$size_1_total = $size_1['width'] + $size_1['height'];
			$size_2_total = $size_2['width'] + $size_2['height'];
			if ( $size_1_total === $size_2_total ) {
				return 0;
			}

			return $size_1_total > $size_2_total ? -1 : 1;
		};

		usort( $sizes, $sort_sizes );

		foreach ( $sizes as $size ) {
			$sized_file = str_replace( basename( $full_image ), $size['file'], $full_image );
			$file_size  = @filesize( $sized_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			if ( $file_size && $max_file_size >= $file_size ) {
				return $sized_file;
			}
		}

		return null;
	}

	/**
	 * Provides the ID of an image uploaded to Twitter to send with the status update.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post The post associated with the tweet.
	 * @return null|int The Twitter media ID or null if no image is to be sent.
	 */
	public function get_upload_data_media_id( $post ) {
		/**
		 * Filters the ID of the image attachment to include with the post. If the result is null, the post's featured
		 * image will be used if set. If the result is false, no image will be sent.
		 *
		 * @since 1.0.0
		 *
		 * @param null|int An attachment ID, null to fall back to the featured image, or false to send no image.
		 * @param \WP_Post  The post associated with the tweet.
		 */
		$attachment_id = apply_filters( 'autoshare_for_twitter_attached_image', null, $post );

		if ( false === $attachment_id ) {
			return null;
		}

		if ( is_null( $attachment_id ) && has_post_thumbnail( $post ) ) {
			$attachment_id = get_post_thumbnail_id( $post );
		}

		if ( ! $attachment_id ) {
			return null;
		}

		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( ! is_array( $metadata ) ) {
			return;
		}

		$file = $this->get_largest_acceptable_image(
			get_attached_file( $attachment_id ),
			isset( $metadata['sizes'] ) ? $metadata['sizes'] : []
		);
		if ( ! $file ) {
			return null;
		}

		$media_id = $this->upload( $file );

		return $media_id ? $media_id : null;
	}

	/**
	 * Upload an image
	 *
	 * @see https://developer.twitter.com/en/docs/media/upload-media/overview
	 *
	 * @param string $image Image file path.
	 * @return int|null The Twitter ID for the uploaded image, or null on failure.
	 */
	public function upload( $image ) {
		/**
		 * Filters the media upload ID before the request is sent.
		 *
		 * @param null|mixed Any non-null value will suppress the request to Twitter's media upload endpoint.
		 * @param string     The path to the image file.
		 */
		$media_upload_id = apply_filters( 'autoshare_for_twitter_pre_media_upload', null, $image );

		if ( ! is_null( $media_upload_id ) ) {
			return $media_upload_id;
		}

		$response = $this->twitter_api->upload_media( $image );

		if ( ! is_object( $response ) || ! isset( $response->media_id ) ) {
			$media_upload_id = 0;
		} else {
			$media_upload_id = $response->media_id;
		}

		/**
		 * Fires after an image has been uploaded to Twitter.
		 *
		 * @param int    The Twitter ID for the uploaded image.
		 * @param string The path to the image file that was uploaded to Twitter.
		 * @param object The full response object recieved from Twitter.
		 */
		do_action( 'autoshare_for_twitter_after_media_upload', $media_upload_id, $image, $response );

		return $media_upload_id;
	}
}
