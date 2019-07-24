<div id="wpdonations-donation-dashboard">
	<p><?php _e( 'Your donations are shown in the table below.', 'wpdonations' ); ?></p>
	<table class="wpdonations-donations">
		<thead>
			<tr>
				<th class="donation_title"><?php _e( 'Donation', 'wpdonations' ); ?></th>
				<th class="date"><?php _e( 'Date', 'wpdonations' ); ?></th>
				<th class="status"><?php _e( 'Status', 'wpdonations' ); ?></th>
				<th class="recurring"><?php _e( 'Recurring?', 'wpdonations' ); ?></th>
				<th class="anonymous"><?php _e( 'Anonymous?', 'wpdonations' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! $donations ) : ?>
				<tr>
					<td colspan="6"><?php _e( 'You do not have any validated donations.', 'wpdonations' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $donations as $donation ) : ?>
					<tr>
						<td class="donation_title">
							<a href="<?php echo get_permalink( $donation->ID ); ?>"><?php echo $donation->post_title; ?></a>
							<ul class="donation-dashboard-actions">
								<?php
									$actions = array();

									switch ( $donation->post_status ) {
										case 'publish' :
											//$actions['edit'] = array( 'label' => __( 'Edit', 'wpdonations' ), 'nonce' => false );

											if ( is_anonymous_donation( $donation ) )
												$actions['mark_public'] = array( 'label' => __( 'Mark public', 'wpdonations' ), 'nonce' => true );
											else
												$actions['mark_anonymous'] = array( 'label' => __( 'Mark anonymous', 'wpdonations' ), 'nonce' => true );

											break;
									}

									$actions['delete'] = array( 'label' => __( 'Delete', 'wpdonations' ), 'nonce' => true );
									$actions           = apply_filters( 'wpdonations_my_donation_actions', $actions, $donation );

									foreach ( $actions as $action => $value ) {
										$action_url = add_query_arg( array( 'action' => $action, 'donation_id' => $donation->ID ) );
										if ( $value['nonce'] )
											$action_url = wp_nonce_url( $action_url, 'wpdonations_my_donation_actions' );
										echo '<li><a href="' . $action_url . '" class="donation-dashboard-action-' . $action . '">' . $value['label'] . '</a></li>';
									}
								?>
							</ul>
						</td>
						<td class="date"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $donation->post_date ) ); ?></td>
						<td class="status"><?php the_donation_status( $donation ); ?></td>
						<td class="filled"><?php if ( is_recurring_donation( $donation ) ) echo '&#10004;'; else echo '&ndash;'; ?></td>
						<td class="filled"><?php if ( is_anonymous_donation( $donation ) ) echo '&#10004;'; else echo '&ndash;'; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>