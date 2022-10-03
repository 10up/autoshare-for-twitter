import { Button, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { TweetTextField } from './components/TweetTextField';
import {
	useTwitterAutoshareEnabled,
	useTwitterTextOverriding,
	useAllowTweetImage,
	useTwitterAutoshareErrorMessage,
	useSaveTwitterData,
	useHasFeaturedImage,
} from './hooks';

export default function AutoshareForTwitterPrePublishPanel() {
	const [ autoshareEnabled, setAutoshareEnabled ] = useTwitterAutoshareEnabled();
	const [ overriding, setOverriding ] = useTwitterTextOverriding();
	const [ allowTweetImage, setAllowTweetImage ] = useAllowTweetImage();
	const [ errorMessage ] = useTwitterAutoshareErrorMessage();
	const hasFeaturedImage = useHasFeaturedImage();

	useSaveTwitterData();

	return (
		<>
			<ToggleControl
				label={ autoshareEnabled ? __( 'Tweet when published', 'autoshare-for-twitter' ) : __( 'Don\'t Tweet', 'autoshare-for-twitter' )
				}
				checked={ autoshareEnabled }
				onChange={ ( checked ) => {
					setAutoshareEnabled( checked );
				} }
				className="autoshare-for-twitter-toggle-control"
			/>

			{ autoshareEnabled && hasFeaturedImage && (
				<ToggleControl
					label={ __( 'Use featured image in Tweet', 'autoshare-for-twitter' ) }
					checked={ allowTweetImage }
					onChange={ () => {
						setAllowTweetImage( ! allowTweetImage );
					} }
					className="autoshare-for-twitter-toggle-control"
				/>
			) }

			{ autoshareEnabled && (
				<div className="autoshare-for-twitter-prepublish__override-row">
					{ overriding && (
						<TweetTextField />
					) }

					<Button
						isLink
						onClick={ () => {
							setOverriding( ! overriding );
						} }
					>
						{ overriding ? __( 'Hide', 'autoshare-for-twitter' ) : __( 'Edit', 'autoshare-for-twitter' ) }
					</Button>
				</div>
			) }
			<div>{ errorMessage }</div>
		</>
	);
}
