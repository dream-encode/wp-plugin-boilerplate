import {
	Spinner,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	__experimentalSpacer as Spacer
} from '@wordpress/components'

const Loader = () => {
	return (
		<HStack
			alignment="center"
			justify="center"
		>
			<VStack
				alignment="center"
				justify="center"
			>
				<Spacer />
				<Spinner />
				<Spacer />
			</VStack>
		</HStack>
	)
}

export default Loader