import { __ } from '@wordpress/i18n'

import {
	Dashicon,
	__experimentalText as Text
} from '@wordpress/components'

import {
	useState,
	useEffect
} from '@wordpress/element'

const CopyToClipboard = ( { content, icon } ) => {
	const [ contentCopied, updateContentCopied ] = useState( false )

	useEffect( () => {
		if ( contentCopied ) {
			setTimeout( () => updateContentCopied( false ), 2000 )
		}
	} , [ contentCopied ] )

	const copyContent = async () => {
		navigator.clipboard.writeText( content )

		updateContentCopied( true )
	}

	return (
		<div className="copy-to-clipboard">
			<Dashicon className="copy-icon" icon={ icon } onClick={ copyContent } />
			{ contentCopied && <Text className="copied-message">{ __( 'Copied!', 'PLUGIN_SLUG' ) }</Text> }
		</div>
	)
}

export default CopyToClipboard