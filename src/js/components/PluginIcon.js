import { SVG } from '@wordpress/primitives';

const TwitterIcon = ( fillColor ) => (
	<SVG
		version="1.1"
		xmlSpace="preserve"
		width={ 256 }
		height={ 256 }
		viewBox="0 0 256 256"
	>
		<g
			style={ {
				stroke: 'none',
				strokeWidth: 0,
				strokeDasharray: 'none',
				strokeLinecap: 'butt',
				strokeLinejoin: 'miter',
				strokeMiterlimit: 10,
				fill: 'none',
				fillRule: 'nonzero',
				opacity: 1,
			} }
		>
			<path
				d="M45 90C20.147 90 0 69.853 0 45S20.147 0 45 0s45 20.147 45 45-20.147 45-45 45z"
				style={ {
					stroke: 'none',
					strokeWidth: 1,
					strokeDasharray: 'none',
					strokeLinecap: 'butt',
					strokeLinejoin: 'miter',
					strokeMiterlimit: 10,
					fill: fillColor,
					fillRule: 'nonzero',
					opacity: 1,
				} }
				transform="matrix(2.33 0 0 2.33 22.612 22.612)"
			/>
			<path
				d="M17.884 19.496 38.925 47.63 17.751 70.504h4.765l18.538-20.027 14.978 20.027h16.217L50.024 40.788l19.708-21.291h-4.765L47.895 37.94 34.101 19.496H17.884zm7.008 3.51h7.45L65.24 66.993h-7.45L24.892 23.006z"
				style={ {
					stroke: 'none',
					strokeWidth: 1,
					strokeDasharray: 'none',
					strokeLinecap: 'butt',
					strokeLinejoin: 'miter',
					strokeMiterlimit: 10,
					fill: '#fff',
					fillRule: 'nonzero',
					opacity: 1,
				} }
				transform="matrix(2.33 0 0 2.33 22.612 22.612)"
			/>
		</g>
	</SVG>
);

const DefaultIcon = TwitterIcon( '#1B1C20' );
const EnabledIcon = TwitterIcon( '#1DA1F2' );
const DisabledIcon = TwitterIcon( '#787E88' );
const FailedIcon = TwitterIcon( '#D0494A' );
const TweetedIcon = TwitterIcon( '#7FD051' );

export { DefaultIcon, EnabledIcon, DisabledIcon, FailedIcon, TweetedIcon };
