/* global PLUGIN_SHORT_DEFINE_PREFIX */
export const fetchGetOptions = () => {
	return {
		headers: {
			"X-WP-Nonce": PLUGIN_SHORT_DEFINE_PREFIX.NONCES.REST,
		},
	};
}

export const fetchPostOptions = ( postData ) => {
	return {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			"X-WP-Nonce": PLUGIN_SHORT_DEFINE_PREFIX.NONCES.REST,
		},
		body: JSON.stringify(postData),
	};
}

export const fetchPostFileUploadOptions = ( formData ) => {
	return {
		method: 'POST',
		headers: {
			'X-WP-Nonce': PLUGIN_SHORT_DEFINE_PREFIX.NONCES.REST,
		},
		body: formData,
	}
}
