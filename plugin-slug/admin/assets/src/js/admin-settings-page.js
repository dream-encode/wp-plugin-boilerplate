import {
	createRoot
} from '@wordpress/element'

import domReady from '@wordpress/dom-ready'

import AdminSettingsPage from './components/AdminSettings/AdminSettingsPage.jsx'

domReady( () => {
	const root = createRoot(
		document.getElementById( 'PLUGIN_SLUG-plugin-settings' )
	)

	root.render( <AdminSettingsPage /> )
} )
