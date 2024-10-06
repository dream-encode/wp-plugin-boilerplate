import { __ } from '@wordpress/i18n'

import {
	PanelBody,
	PanelRow,
	Placeholder,
	Spinner,
	Snackbar,
	Button,
	SelectControl,
	__experimentalHStack as HStack
} from '@wordpress/components'

import {
	Fragment,
	useState,
	useEffect,
} from '@wordpress/element'

const defaultSettingsState = {
	'log-level': 'off',
}

import { apiGetSettings, apiSaveSettings } from '../../utils/api'
import { LOG_LEVELS } from '../../utils/constants'
import { capitalizeFirstLetter } from '../../utils/helpers'

const mappedLogLevels = LOG_LEVELS.map( ( level ) => ( {
	label: capitalizeFirstLetter( level ),
	value: level
} ) )

const AdminSettingsPage = () => {
	const [ apiLoaded, setAPILoaded ] = useState( false )
	const [ apiSaving, setAPISaving ] = useState( false )
	const [ apiSaved, setAPISaved ]   = useState( false )
	const [ settings, setSettings ]   = useState( defaultSettingsState )

	useEffect( () => {
		apiGetSettings()
			.then( ( response ) => {
				setSettings( response.data )

				setAPILoaded( true )
			} )
	}, [] )

	const updateSetting = ( key, value ) => {
		setSettings( ( previousSettings ) => ( {
			...previousSettings,
			[ key ]: value,
		} ) )
	}

	const saveSettings = async ( event ) => {
		event.preventDefault()

		setAPISaving( true )

		apiSaveSettings( settings )
			.then( () => {
				setAPISaving( false )
				setAPISaved( true )

				setTimeout( () => {
					setAPISaved( false )
				}, 5000 )
			} )
	}

	return (
		<Fragment>
			<div className="settings-header">
				<div className="settings-container">
					<div className="settings-logo">
						<h1>{ __( 'PLUGIN_NAME', 'PLUGIN_SLUG' ) }</h1>
					</div>
				</div>
			</div>

			<div className="settings-main">
				{ ! apiLoaded ? (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) : (
					<Fragment>
						{ apiSaved && (
							<Snackbar>
								<p>{ __( 'Settings saved!', 'PLUGIN_SLUG' ) }</p>
							</Snackbar>
						) }

						<PanelBody title={ __( 'Developer', 'PLUGIN_SLUG' ) }>
							<PanelRow className="field-row">
								<SelectControl
									label={ __( 'Log Level', 'PLUGIN_SLUG' ) }
									value={ settings[ 'log-level' ] || 'off' }
									options={ mappedLogLevels }
									onChange={ ( value ) => updateSetting( 'log-level', value ) }
									__nextHasNoMarginBottom
								/>
							</PanelRow>
						</PanelBody>
						<PanelBody title={ __( 'Processing' ) }>
							<HStack
								alignment="center"
							>
								<Button
									variant="primary"
									isBusy={ apiSaving }
									isLarge
									target="_blank"
									href="#"
									onClick={ saveSettings }
								>
									{ __( 'Save', 'PLUGIN_SLUG' ) }
								</Button>
							</HStack>
						</PanelBody>

					</Fragment>
				) }
			</div>
		</Fragment>
	)
}

export default AdminSettingsPage
