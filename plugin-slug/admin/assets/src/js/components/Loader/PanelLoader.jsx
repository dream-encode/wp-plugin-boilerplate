import {
	__experimentalHeading as Heading,
	__experimentalVStack as VStack
} from '@wordpress/components'

import Loader from './Loader.jsx'

const PanelLoader = ( { text } ) => {
	return (
		<VStack
			alignment="center"
			justify="center"
			className="panel-loader"
		>
			<Heading level={ 4 }>{ text }</Heading>
			<Loader />
		</VStack>
	)
}

export default PanelLoader