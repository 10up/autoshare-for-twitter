import { Component } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel, PluginPostPublishPanel, PluginPostStatusInfo } from '@wordpress/edit-post';
import { dispatch, select, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { createAutoshareStore, STORE } from './store';
import AutosharePrePublishPanel from './AutosharePrePublishPanel';
import AutosharePostStatusInfo from './AutosharePostStatusInfo';

createAutoshareStore();

class AutosharePrePublishPanelPlugin extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			enabledText: '',
		};

		this.maybeSetEnabledText = this.maybeSetEnabledText.bind( this );
	}

	componentDidMount() {
		dispatch( STORE ).setLoaded();
		subscribe( this.maybeSetEnabledText );
	}

	maybeSetEnabledText() {
		try {
			const enabled = select( STORE ).getAutoshareEnabled();
			const enabledText = enabled ? __( 'Enabled', 'auto-share-for-twitter' ) : __( 'Disabled', 'auto-share-for-twitter' );

			if ( enabledText !== this.state.enabledText ) {
				this.setState( { enabledText } );
			}
		} catch ( e ) {}
	}

	render() {
		const { enabledText } = this.state;

		return (
			<PluginPrePublishPanel
				title={ [
					__( 'Autoshare:', 'auto-share-for-twitter' ),
					<span className="editor-post-publish-panel__link" key="label">
						{ enabledText }
					</span>,
				] }
			>
				<AutosharePrePublishPanel />
			</PluginPrePublishPanel>
		);
	}
}

const AutosharePostStatusInfoPlugin = () => {
	return (
		<PluginPostStatusInfo className="my-plugin-post-status-info">
			<AutosharePostStatusInfo />
		</PluginPostStatusInfo>
	);
};

const AutosharePostPublishPanelPlugin = () => {
	return (
		<PluginPostPublishPanel className="my-plugin-post-status-info">
			<AutosharePostStatusInfo />
		</PluginPostPublishPanel>
	);
};

registerPlugin( 'autoshare-pre-publish-panel', { render: AutosharePrePublishPanelPlugin } );
registerPlugin( 'autoshare-post-status-info', { render: AutosharePostStatusInfoPlugin } );
registerPlugin( 'autoshare-post-publish-panel', { render: AutosharePostPublishPanelPlugin } );
