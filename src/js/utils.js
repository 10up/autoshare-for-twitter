import { Icon } from '@wordpress/components';

import FailedIcon from '../../assets/images/twitter_failed.svg';
import TweetedIcon from '../../assets/images/twitter_tweeted.svg';
import DefaultIcon from '../../assets/images/twitter_default.svg';

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
			icon={ <StatusIcon /> }
			size={ 24 }
		/>
	);

	return TweetStatusIcon;
};
