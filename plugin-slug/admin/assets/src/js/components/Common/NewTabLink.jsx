import {
	Icon
} from '@wordpress/components'

const NewTabLink = ( props ) => {
	return (
		<a
			className="new-tab-link"
			href={ props.href }
			target="_blank"
		>
			{ props.text } <Icon icon="external" />
		</a>
	)
}

export default NewTabLink