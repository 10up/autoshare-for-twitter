import { useEffect, useState } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel, PluginPostStatusInfo } from '@wordpress/edit-post';
import { dispatch, select, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { createAutotweetStore, STORE } from './store';
import AutotweetPrePublishPanel from './AutotweetPrePublishPanel';
import AutotweetPostStatusInfo from './AutotweetPostStatusInfo';

createAutotweetStore();

const AutotweetPrePublishPanelPlugin = () => {
	const [ enabledText, setEnabledText ] = useState( '' );

	useEffect( () => {
		dispatch( STORE ).setLoaded();
	} );

	const maybeSetEnabledText = () => {
		try {
			const enabled = select( STORE ).getAutotweetEnabled();
			setEnabledText( enabled ? __( 'Enabled', 'autotweet' ) : __( 'Disabled', 'autotweet' ) );
		} catch ( e ) {}
	};

	useEffect( () => {
		subscribe( maybeSetEnabledText );
	}, [] );

	return (
		<PluginPrePublishPanel
			title={ [
				__( 'Autotweet:', 'autotweet' ),
				<span className="editor-post-publish-panel__link" key="label">
					{ enabledText }
				</span>,
			] }>
			<AutotweetPrePublishPanel />
		</PluginPrePublishPanel>
	);
};

const AutoTweetPostStatusInfoPlugin = () => {
	return <PluginPostStatusInfo
		className="my-plugin-post-status-info"
	>
		<AutotweetPostStatusInfo />
	</PluginPostStatusInfo>;
};

registerPlugin( 'autotweet-post-publish-panel', { render: AutotweetPrePublishPanelPlugin } );
registerPlugin( 'autotweet-post-status-info', { render: AutoTweetPostStatusInfoPlugin } );
