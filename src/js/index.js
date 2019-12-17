import { Component } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel, PluginPostPublishPanel, PluginPostStatusInfo } from '@wordpress/edit-post';
import { dispatch, select, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { createAutoshareStore, STORE } from './store';
import AutoshareForTwitterPrePublishPanel from './AutoshareForTwitterPrePublishPanel';
import AutoshareForTwitterPostStatusInfo from './AutoshareForTwitterPostStatusInfo';

createAutoshareStore();

class AutoshareForTwitterPrePublishPanelPlugin extends Component {
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
			const enabledText = enabled ? __( 'Enabled', 'autoshare-for-twitter' ) : __( 'Disabled', 'autoshare-for-twitter' );

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
					__( 'Autoshare:', 'autoshare-for-twitter' ),
					<span className="editor-post-publish-panel__link" key="label">
						{ enabledText }
					</span>,
				] }
			>
				<AutoshareForTwitterPrePublishPanel />
			</PluginPrePublishPanel>
		);
	}
}

const AutoshareForTwitterPostStatusInfoPlugin = () => {
	return (
		<PluginPostStatusInfo className="my-plugin-post-status-info">
			<AutoshareForTwitterPostStatusInfo />
		</PluginPostStatusInfo>
	);
};

const AutoshareForTwitterPostPublishPanelPlugin = () => {
	return (
		<PluginPostPublishPanel className="my-plugin-post-status-info">
			<AutoshareForTwitterPostStatusInfo />
		</PluginPostPublishPanel>
	);
};

registerPlugin( 'autoshare-for-twitter-pre-publish-panel', { render: AutoshareForTwitterPrePublishPanelPlugin } );
registerPlugin( 'autoshare-for-twitter-post-status-info', { render: AutoshareForTwitterPostStatusInfoPlugin } );
registerPlugin( 'autoshare-for-twitter-post-publish-panel', { render: AutoshareForTwitterPostPublishPanelPlugin } );
