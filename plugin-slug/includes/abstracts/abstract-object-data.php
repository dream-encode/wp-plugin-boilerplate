<?php
/**
 * Class PLUGIN_CLASS_PREFIX_Abstract_Object_Data.
 *
 * @since 1.0.0
 */

namespace PLUGIN_NAMESPACE\Core\Abstracts;

use Exception;
use ReflectionMethod;
use WC_DateTime;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'PLUGIN_CLASS_PREFIX_Abstract_Object_Data' ) ) {

	/**
	 * Class PLUGIN_CLASS_PREFIX_Abstract_Object_Data
	 */
	abstract class PLUGIN_CLASS_PREFIX_Abstract_Object_Data {
		/**
		 * ID.
		 *
		 * @var int
		 */
		public $id = 0;

		/**
		 * Data.
		 *
		 * @var array
		 */
		protected $data = array();

		/**
		 * Extra data.
		 *
		 * @var array
		 */
		protected $extra_data = array();

		/**
		 * Changes.
		 *
		 * @var array
		 */
		protected $changes = array();

		/**
		 * Extra data changes.
		 *
		 * @var array
		 */
		protected $extra_data_changes = array();

		/**
		 * Don't cache these.
		 *
		 * @var bool
		 */
		protected $no_cache = false;

		/**
		 * MMEWOA_Abstract_Object_Data constructor.
		 *
		 * @param  null  $data  Data.
		 * @return void
		 */
		public function __construct( $data = null ) {
			$this->data = (array) $data;

			if ( array_key_exists( 'id', $this->data ) ) {
				$this->set_id( absint( $this->data['id'] ) );

				unset( $this->data['id'] );
			}
		}

		/**
		 * Set id of object in database.
		 *
		 * @param  int  $id  ID.
		 * @return void
		 */
		public function set_id( $id ) {
			$this->id = $id;
		}

		/**
		 * Get id of object in database.
		 *
		 * @return int
		 */
		public function get_id() {
			return absint( $this->id );
		}

		/**
		 * Get object data.
		 *
		 * @since  1.0.0
		 * @param  mixed  $name    Optional. Name of data want to get, true if return all.
		 * @param  mixed  $default  Default value.
		 * @return array|mixed
		 */
		public function get_data( $name = '', $default = '' ) {
			if ( is_string( $name ) && ! empty( $name ) ) {
				return array_key_exists( $name, $this->data ) ? $this->data[ $name ] : ( array_key_exists( $name, $this->extra_data ) ? $this->extra_data[ $name ] : $default );
			} elseif ( is_array( $name ) ) {
				$data = array();

				foreach ( $name as $key ) {
					$data[ $key ] = $this->get_data( $key, $default );
				}

				return $data;
			}

			return array_merge( $this->data, $this->extra_data );
		}

		/**
		 * Get data as WC_Datetime object
		 *
		 * @since  1.0.0
		 * @param  string  $name  Key.
		 * @return array|WC_Datetime|mixed
		 */
		public function get_data_date( $name ) {
			$data = $this->get_data( $name );

			return ( $data instanceof WC_Datetime ) ? $data : new WC_Datetime( $data );
		}

		/**
		 * Get an extra data key.
		 *
		 * @since  1.0.0
		 * @param  string       $name     Key to get.
		 * @param  false|mixed  $default  Default value.
		 * @return array|bool|mixed|string
		 */
		public function get_extra_data( string $name = '', $default = false ) {
			return array_key_exists( $name, $this->extra_data ) && ! empty( $this->extra_data[ $name ] ) ? $this->extra_data[ $name ] : $default;
		}

		/**
		 * Set object data.
		 *
		 * @since  1.0.0
		 * @param  array|string  $key_or_data  Key to set.
		 * @param  mixed         $value        Value.
		 * @param  false|mixed   $extra        Extra params.
		 * @return void
		 * @throws Exception  Exception.
		 */
		protected function set_data( $key_or_data, $value = '', $extra = false ) {
			if ( is_array( $key_or_data ) ) {
				foreach ( $key_or_data as $key => $value ) {
					$this->set_data( $key, $value, $extra );
				}
			} elseif ( $key_or_data ) {
				$data    = $extra ? $this->extra_data : $this->data;
				$changes = $extra ? $this->extra_data_changes : $this->changes;

				if ( $extra ) {
					// Do not allow to add extra data with the same key in data.
					if ( ! array_key_exists( $key_or_data, $this->data ) ) {
						$this->extra_data[ $key_or_data ] = $value;
					}
				} else {
					try {
						if ( ! is_string( $key_or_data ) && ! is_numeric( $key_or_data ) ) {
							throw new Exception( 'error' );
						}

						// Only change the data is already existed.
						if ( array_key_exists( $key_or_data, $this->data ) ) {
							$this->data[ $key_or_data ] = $value;
						} else {
							$this->extra_data[ $key_or_data ] = $value;
						}
					} catch ( Exception $ex ) {
						print_r( $key_or_data );  // @phpcs:ignore.
						print_r( $ex->getMessage() );  // @phpcs:ignore.
						die( __FILE__ . '::' . __FUNCTION__ );
					}
				}
			}
		}

		/**
		 * Set extra data.
		 *
		 * @since  1.0.0
		 * @param  array|string  $key_or_data  Key.
		 * @param  string        $value        Value.
		 * @return $this
		 */
		public function set_data_public( $key_or_data, $value = '' ) {
			$this->set_data( $key_or_data, $value, true );

			return $this;
		}

		/**
		 * Set a WC_DateTime data field.
		 *
		 * @since  1.0.0
		 * @param  string  $key    Key to set.
		 * @param  mixed   $value  Value.
		 * @return void
		 */
		public function set_data_date( $key, $value ) {
			if ( ! $value instanceof WC_Datetime ) {
				$value = new WC_Datetime( $value );
			}

			$this->set_data( $key, $value, true );
		}

		/**
		 * Set a WC_DateTime data field to null.
		 *
		 * @since  1.0.0
		 * @param  string  $key  Key.
		 * @return void
		 */
		public function set_data_null_date( $key ) {
			$this->set_data( $key, mmewoa_get_sql_null_date() );
		}

		/**
		 * Set data via methods in array.
		 *
		 * @since  1.0.0
		 * @param  array  $data  Array with key is method and value is value to set.
		 * @return void
		 * @throws Exception  Exception.
		 */
		public function set_data_via_methods( $data ) {
			$errors = array_keys( $data );

			foreach ( $data as $prop => $value ) {
				$setter = "set_$prop";
				if ( is_callable( array( $this, $setter ) ) ) {
					$reflection = new ReflectionMethod( $this, $setter );

					if ( $reflection->isPublic() ) {
						$this->{$setter}( $value );
						$errors = array_diff( $errors, array( $prop ) );
					}
				}
			}

			// If there is at least one method failed.
			if ( $errors ) {
				$errors = array_map( array( $this, 'prefix_set_method' ), $errors );

				throw new Exception(
					sprintf(
						/* translators: 1: List of functions 2: Class name */
						__( 'The following functions %1$s do not exists in %2$s', 'PLUGIN_SLUG' ),
						implode( ',', $errors ),
						get_class( $this )
					)
				);
			}
		}

		/**
		 * Return the keys of data.
		 *
		 * @since  1.0.0
		 * @param  bool  $extra  Optional. TRUE if including extra data.
		 * @return array
		 */
		public function get_data_keys( $extra = true ) {
			return $extra ? array_merge( array_keys( $this->data ), array_keys( $this->extra_data ) ) : array_keys( $this->data );
		}

		/**
		 * Prefix a set method.
		 *
		 * @since  1.0.0
		 * @param  string  $method  Method name.
		 * @return string
		 */
		public function prefix_set_method( $method ) {
			return "set_{$method}";
		}

		/**
		 * Apply the changesethod.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function apply_changes() {
			$this->data    = array_replace_recursive( $this->data, $this->changes );
			$this->changes = array();
		}

		/**
		 * Get the changes.
		 *
		 * @since  1.0.0
		 * @return array
		 */
		public function get_changes() {
			return $this->changes;
		}

		/**
		 * Sanitize feature key.
		 *
		 * @param  string  $feature  Feature key.
		 * @return mixed
		 */
		protected function sanitize_feature_key( $feature ) {
			return preg_replace( '~[_]+~', '-', $feature );
		}


		/**
		 * Set no_cache var.
		 *
		 * @param  mixed  $value  Value.
		 * @return void
		 */
		public function set_no_cache( $value ) {
			$this->no_cache = $value;
		}

		/**
		 * Get no_cache var.
		 *
		 * @return bool
		 */
		public function get_no_cache() {
			return $this->no_cache;
		}
	}
}
