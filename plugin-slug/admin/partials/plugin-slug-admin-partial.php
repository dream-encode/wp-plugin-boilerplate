<?php
/**
 * View for editing a "Simple" product
 *
 * Mostly HTML to display when editing a "Simple" product.
 *
 * @link       https://dream-encode.com
 * @since      0.1.0
 *
 * @package    Max_Marine_Enhanced_Product_Changelogs
 * @subpackage Max_Marine_Enhanced_Product_Changelogs/admin/partials
 */

?>
<div id="mmepc-changelog" class="panel woocommerce_options_panel hidden" style="display: none;">
	<h2><?php esc_html_e( 'Product Changelog', 'max-marine-enhanced-product-changelogs' ); ?></h2>

	<div id="mmepc-last-update-container">
		<?php
		$last_update = ( is_array( $mmepc_log_entries ) && count( $mmepc_log_entries ) > 0 ) ? $mmepc_log_entries[0] : false;

		if ( ! $last_update ) {
			?>

			<span class="no-entries"><?php esc_html_e( 'No logged updates.', 'max-marine-enhanced-product-changelogs' ); ?></span>

			<?php
		} else {
			?>

			<span class="mmepc-last-update-text">
				<strong><?php esc_html_e( 'Last update', 'max-marine-enhanced-product-changelogs' ); ?>: </strong>

				<?php
				// @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
				printf(
					/* translators: 1: Key label, 2: Old value, 3: New value, 4: Datetime, 5: User, 6: Change source, 7: Link to log entry detail. */
					__( '<span class="action">%1$s</span> changed from <span class="old">%2$s</span> to <span class="new">%3$s</span> on <a href="%7$s" target="_blank"><date>%4$s</date></a> by <span class="user">%5$s</span> via <span class="source">%6$s</span>.', 'max-marine-enhanced-product-changelogs' ),
					mmepc_get_logged_key_label( $last_update->field ),
					mmepc_format_logged_value_by_key( $last_update->old_value, $last_update->field ),
					mmepc_format_logged_value_by_key( $last_update->new_value, $last_update->field ),
					esc_html( mmepc_format_datetime_long_from_timestamp( $last_update->timestamp ) ),
					esc_html( mmepc_get_user_display_name_by_id( $last_update->user_id ) ),
					esc_html( $last_update->source ),
					esc_url( mmepc_get_log_entry_detail_link( $last_update->id ) ),
				);
				// @phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
				?>

			</span>

			<?php
		}
		?>
	</div>

	<?php if ( $mmepc_log_entries ) : ?>

	<div id="mmepc-all-entries-container">
		<table>
			<thead>
				<tr>
					<th class="date"><?php esc_html_e( 'Date', 'max-marine-enhanced-product-changelogs' ); ?></th>
					<th class="source"><?php esc_html_e( 'Source', 'max-marine-enhanced-product-changelogs' ); ?></th>
					<th class="user"><?php esc_html_e( 'User', 'max-marine-enhanced-product-changelogs' ); ?></th>
					<th class="field"><?php esc_html_e( 'Field', 'max-marine-enhanced-product-changelogs' ); ?></th>
					<th class="old"><?php esc_html_e( 'Old Value', 'max-marine-enhanced-product-changelogs' ); ?></th>
					<th class="new"><?php esc_html_e( 'New Value', 'max-marine-enhanced-product-changelogs' ); ?></th>
				</tr>
			</thead>
			<tbody>

				<?php
				foreach ( $mmepc_log_entries as $entry ) {
					?>

					<tr data-id="<?php echo esc_attr( $entry->id ); ?>" class="<?php echo esc_attr( mmepc_changelog_entry_row_class( $entry ) ); ?>">
						<td class="date">
							<a href="<?php echo esc_url( mmepc_get_log_entry_detail_link( $entry->id ) ); ?>" target="_blank">
								<?php echo esc_html( mmepc_format_datetime_long_from_timestamp( $entry->timestamp ) ); ?>
							</a>
						</td>
						<td class="source"><?php echo esc_html( $entry->source ); ?></td>
						<td class="user"><?php echo esc_html( mmepc_get_user_display_name_by_id( $entry->user_id ) ); ?></td>
						<td class="field <?php echo esc_attr( sanitize_title( $entry->field ) ); ?>"><?php echo esc_html( mmepc_get_logged_key_label( $entry->field ) ); ?></td>
						<td class="old"><?php echo mmepc_format_logged_value_by_key( $entry->old_value, $entry->field ); // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
						<td class="new"><?php echo mmepc_format_logged_value_by_key( $entry->new_value, $entry->field ); // @phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>

					<?php
				}
				?>

			</tbody>
		</table>
	</div>

	<?php endif; ?>

</div>
