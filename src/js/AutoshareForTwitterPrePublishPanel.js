import { Button, ToggleControl, ExternalLink, CardDivider } from '@wordpress/components';
import { select } from '@wordpress/data';
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
import { getIconByStatus } from './utils';
import { TwitterAccounts } from './components/TwitterAccounts';

export default function AutoshareForTwitterPrePublishPanel() {
	const [ autoshareEnabled, setAutoshareEnabled ] = useTwitterAutoshareEnabled();
	const [ overriding, setOverriding ] = useTwitterTextOverriding();
	const [ allowTweetImage, setAllowTweetImage ] = useAllowTweetImage();
	const [ errorMessage ] = useTwitterAutoshareErrorMessage();
	const hasFeaturedImage = useHasFeaturedImage();

	const messages = select( 'core/editor' ).getCurrentPostAttribute( 'autoshare_for_twitter_status' );
	useSaveTwitterData();

	return (
		<>
			{ messages && !!messages.message.length && ( <div className="autoshare-for-twitter-post-status">
				{ messages.message.map( ( statusMessage, index ) => {
					const TweetIcon = getIconByStatus( statusMessage.status );

					return (
						<div className="autoshare-for-twitter-log" key={ index }>
							{ TweetIcon }{ statusMessage.url ? <ExternalLink href={ statusMessage.url }>{ statusMessage.message }</ExternalLink> : statusMessage.message }
						</div>
					);
				} ) }
				<CardDivider />
			</div> ) }
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

			{ autoshareEnabled && <TwitterAccounts /> }

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
