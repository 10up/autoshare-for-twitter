import { Component } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { PluginPrePublishPanel, PluginPostStatusInfo } from '@wordpress/edit-post';
import { dispatch, select, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { createAutotweetStore, STORE } from './store';
import AutotweetPrePublishPanel from './AutotweetPrePublishPanel';
import AutotweetPostStatusInfo from './AutotweetPostStatusInfo';

createAutotweetStore();

class AutotweetPrePublishPanelPlugin extends Component {
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
			const enabled = select( STORE ).getAutotweetEnabled();
			const enabledText = enabled ? __( 'Enabled', 'autotweet' ) : __( 'Disabled', 'autotweet' );

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
					__( 'Autotweet:', 'autotweet' ),
					<span className="editor-post-publish-panel__link" key="label">
						{ enabledText }
					</span>,
				] }>
				<AutotweetPrePublishPanel />
			</PluginPrePublishPanel>
		);
	}
}

const AutoTweetPostStatusInfoPlugin = () => {
	return <PluginPostStatusInfo
		className="my-plugin-post-status-info"
	>
		<AutotweetPostStatusInfo />
	</PluginPostStatusInfo>;
};

registerPlugin( 'autotweet-post-publish-panel', { render: AutotweetPrePublishPanelPlugin } );
registerPlugin( 'autotweet-post-status-info', { render: AutoTweetPostStatusInfoPlugin } );
