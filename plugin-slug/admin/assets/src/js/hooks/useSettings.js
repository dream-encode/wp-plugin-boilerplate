import {
	useState,
	useEffect
} from '@wordpress/element'

import apiFetch from '@wordpress/api-fetch'

export const useSettings = () => {
    const [ settingsLoaded, updateSettingsLoaded ] = useState( false )
    const [ settingsSaving, updateSettingsSaving ] = useState( false )
    const [ pluginLogLevel, updatePluginLogLevel ] = useState( 'off' )

	useEffect( () => {
        apiFetch( {
			path: '/wp/v2/settings'
		} ).then( ( settings ) => {
            updatePluginLogLevel( settings.PLUGIN_FUNC_PREFIX_plugin_settings.plugin_log_level )

			updateSettingsLoaded( true )
        } )
    }, [] )

	const saveSettings = async () => {
        updateSettingsSaving( true )

        apiFetch( {
            path: '/wp/v2/settings',
            method: 'POST',
            data: {
                PLUGIN_FUNC_PREFIX_plugin_settings: {
                    plugin_log_level: pluginLogLevel,
                },
            },
        } ).then( () => {
            updateSettingsSaving( false )
        } )
    }

    return {
		settingsLoaded,
		updateSettingsLoaded,
        pluginLogLevel,
        updatePluginLogLevel,
		saveSettings,
        settingsSaving,
        updateSettingsSaving
    }
}