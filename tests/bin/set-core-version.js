#!/usr/bin/env node

const fs = require( 'fs' );

const path = `${ process.cwd() }/.wp-env.json`;

let config = fs.existsSync( path ) ? require( path ) : { plugins: [ '.', 'https://downloads.wordpress.org/plugin/classic-editor.1.6.1.zip' ] };

const args = process.argv.slice( 2 );

if ( args.length == 0 ) return;

if ( args[ 0 ] == 'latest' ) return;

config.core = args[ 0 ];

try {
    fs.writeFileSync( path, JSON.stringify( config ) );
} catch ( err ) {
    console.error( err );
}