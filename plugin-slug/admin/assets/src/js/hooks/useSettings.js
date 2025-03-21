import {
	__
} from '@wordpress/i18n'

import {
	useState,
	useEffect
} from '@wordpress/element'

import apiFetch from '@wordpress/api-fetch'

import {
	useDispatch
} from '@wordpress/data'

import {
	store as noticesStore
} from '@wordpress/notices'

export const useSettings = () => {
    const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore )

    const [ settingsLoaded, updateSettingsLoaded ] = useState( false )
    const [ settingsSaving, updateSettingsSaving ] = useState( false )
    const [ pluginLogLevel, updatePluginLogLevel ] = useState( 'off' )

	useEffect( () => {
        load()
    }, [] )

	const load = async () => {
        apiFetch( {
			path: '/wp/v2/settings'
		} ).then( ( settings ) => {
            updatePluginLogLevel( settings.PLUGIN_FUNC_PREFIX_plugin_settings.plugin_log_level )

			updateSettingsLoaded( true )
        } )
    }

	const saveSettings = async () => {
        updateSettingsSaving( true )

        const saveResult = await apiFetch( {
            path: '/wp/v2/settings',
            method: 'POST',
            data: {
                PLUGIN_FUNC_PREFIX_plugin_settings: {
                    plugin_log_level: pluginLogLevel,
                },
            },
        } )

        if ( ! saveResult || ! saveResult.success ) {
            updateSettingsSaving( false )

            createErrorNotice(
                sprintf(
                    /* translators: %s: Error message. */
                    __( 'Error saving settings: %s.', 'PLUGIN_SLUG' ),
                    ( saveResult?.message ?? 'Unknown error' )
                )
            )

            return
        }

        updateSettingsSaving( false )

        createSuccessNotice(
            __( 'Settings saved.', 'PLUGIN_SLUG' )
        )
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