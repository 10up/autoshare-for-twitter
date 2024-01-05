import { Icon } from '@wordpress/components';
import { FailedIcon, TweetedIcon, DefaultIcon } from './components/PluginIcon';

export const getIconByStatus = ( tweetStatus ) => {
	let StatusIcon = DefaultIcon;

	if ( tweetStatus ) {
		if ( tweetStatus === 'published' ) {
			StatusIcon = TweetedIcon;
		} else if ( tweetStatus === 'error' ) {
			StatusIcon = FailedIcon;
		} else {
			StatusIcon = DefaultIcon;
		}
	}

	const TweetStatusIcon = (
		<Icon
			className="autoshare-for-twitter-icon"
			icon={ StatusIcon }
			size={ 48 }
		/>
	);
	return TweetStatusIcon;
};
