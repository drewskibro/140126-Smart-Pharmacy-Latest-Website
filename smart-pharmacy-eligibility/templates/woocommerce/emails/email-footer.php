<?php
/**
 * Smart Pharmacy branded email footer.
 *
 * Pair to email-header.php — closes the tags it opened and renders a
 * branded footer (pharmacy name, GPhC number, address). Overrides
 * WooCommerce's emails/email-footer.php via SPE's locate_template
 * filter.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

$sp_gphc = function_exists( 'sp_field' ) ? sp_field( 'comp_gphc_number', '9012842' ) : '9012842';
$sp_addr = function_exists( 'sp_field' )
	? sp_field( 'contact_trading_address', "Smart Pharmacy\nUnit A2 Ivinghoe Business Centre\nLU5 5BQ" )
	: "Unit A2 Ivinghoe Business Centre, LU5 5BQ";
$sp_addr = trim( str_replace( array( "\r\n", "\n" ), ', ', (string) $sp_addr ) );
?>
														</div>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
								<!-- End Body -->
							</td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_footer">
						<tr>
							<td valign="top" style="padding:24px 36px 8px;text-align:center;">
								<p style="margin:0 0 6px;color:#0da592;font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:400;"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
								<p style="margin:0 0 4px;color:#6b7280;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:170%;">
									<?php
									/* translators: %s: GPhC registration number. */
									printf( esc_html__( 'GPhC-registered UK pharmacy · GPhC number %s', 'smart-pharmacy-eligibility' ), esc_html( $sp_gphc ) );
									?>
								</p>
								<p style="margin:0;color:#9ca3af;font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:170%;"><?php echo esc_html( $sp_addr ); ?></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
