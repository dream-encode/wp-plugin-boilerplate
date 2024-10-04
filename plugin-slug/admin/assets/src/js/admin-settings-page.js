/* global wp */
const {
	createRoot
} = wp.element

import AdminSettingsPage from './components/AdminSettings/AdminSettingsPage.jsx'

const root = createRoot( document.getElementById( 'PLUGIN_SLUG-plugin-settings' ) )
root.render( <AdminSettingsPage /> )
