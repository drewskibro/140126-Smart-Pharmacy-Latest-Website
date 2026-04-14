<?php
/**
 * Homepage — Safety / GPhC section.
 *
 * Two-column: trust cards left, GPhC registration card right (with teal
 * gradient header band, number display, and verify CTA).
 *
 * GPhC number comes from Theme Settings → Compliance (I1). Other content
 * comes from Theme Settings → Homepage → A6 — Safety / GPhC.
 *
 * @package SmartPharmacy
 */

if ( ! (bool) sp_field( 'sf_enabled', 1 ) ) {
	return;
}

$sf_badge_label = sp_field( 'sf_badge_label', 'GPhC Registered Pharmacy' );
$sf_h_pre       = sp_field( 'sf_heading_pre', 'Safe and' );
$sf_h_hi        = sp_field( 'sf_heading_highlight', 'Secure' );
$sf_sub         = sp_field( 'sf_subheading', "Your safety is our top priority. We're fully regulated and inspected to ensure the highest standards of care." );
$sf_gphc_body   = sp_field( 'sf_gphc_body', 'The GPhC is the official body that regulates and inspects all pharmacies in the UK. They ensure we prioritise your safety and meet the highest standards.' );
$sf_verify_l    = sp_field( 'sf_verify_label', 'Verify our registration' );
$sf_verify_u    = sp_field( 'sf_verify_url', 'https://www.pharmacyregulation.org/' );
$sf_gphc_number = sp_field( 'comp_gphc_number', '9012842' );

$sf_cards = sp_field( 'sf_trust_cards', array() );
if ( empty( $sf_cards ) || ! is_array( $sf_cards ) ) {
	$sf_cards = array(
		array( 'icon' => 'shield', 'title' => 'Registered UK Pharmacy',    'body' => 'Fully licensed and regulated by the General Pharmaceutical Council' ),
		array( 'icon' => 'check_circle', 'title' => 'Fully Inspected & Regulated', 'body' => 'Regular inspections ensure we meet the highest safety standards' ),
		array( 'icon' => 'document', 'title' => 'Approved UK-licensed Treatments', 'body' => 'Only genuine, MHRA-approved medications from trusted suppliers' ),
	);
}
?>

<section class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words overflow-hidden py-12 md:py-16">
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 bottom-0" aria-hidden="true"></div>

	<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">
		<div class="items-center box-border gap-x-16 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-16 md:grid-cols-[repeat(2,minmax(0px,1fr))]">

			<!-- LEFT: heading + trust cards -->
			<div class="box-border break-words">
				<div class="box-border break-words">
					<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-8 px-6 py-3 rounded-full border-solid">
						<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
							<?php echo sp_icon( 'shield', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words"><?php echo esc_html( $sf_badge_label ); ?></span>
						<div class="bg-green-500 box-border h-2 break-words w-2 rounded-full"></div>
					</div>

					<h2 class="ultra-heading text-neutral-900 text-5xl font-black box-border leading-[1.1] break-words mb-6 md:text-6xl">
						<?php echo esc_html( $sf_h_pre ); ?>
						<?php if ( $sf_h_hi ) : ?>
							<span class="relative inline-block">
								<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $sf_h_hi ); ?></span>
							</span>
						<?php endif; ?>
					</h2>

					<p class="text-neutral-600 text-xl box-border leading-[1.6] break-words md:text-2xl md:leading-[1.65] font-medium"><?php echo esc_html( $sf_sub ); ?></p>

					<div class="items-center box-border gap-x-3 flex break-words gap-y-3 mt-6" aria-hidden="true">
						<div class="bg-[linear-gradient(to_right,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
						<div class="bg-teal-500/30 box-border h-2 break-words w-2 rounded-full"></div>
						<div class="bg-[linear-gradient(to_left,rgba(0,0,0,0),rgba(59,155,159,0.3))] box-border h-px break-words w-16"></div>
					</div>
				</div>

				<div class="box-border break-words mt-8 space-y-4">
					<?php foreach ( $sf_cards as $sp_card ) :
						$sp_icon  = isset( $sp_card['icon'] ) ? (string) $sp_card['icon'] : 'shield';
						$sp_title = isset( $sp_card['title'] ) ? (string) $sp_card['title'] : '';
						$sp_body  = isset( $sp_card['body'] ) ? (string) $sp_card['body'] : '';
						?>
						<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border break-words border border-gray-100 overflow-hidden p-8 rounded-2xl border-solid premium-hover">
							<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.05),rgba(0,0,0,0))] box-border h-20 break-words w-20 rounded-bl-full right-0 top-0" aria-hidden="true"></div>
							<div class="items-start box-border gap-x-6 flex break-words gap-y-6">
								<div class="relative box-border shrink-0 break-words">
									<div class="relative text-white items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border flex h-20 justify-center break-words w-20 rounded-2xl">
										<?php echo sp_icon( $sp_icon, 'w-10 h-10' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</div>
								<div class="box-border basis-[0%] grow break-words">
									<h3 class="ultra-heading text-neutral-900 text-2xl font-black box-border leading-[1.2] break-words mb-2"><?php echo esc_html( $sp_title ); ?></h3>
									<p class="text-neutral-600 box-border leading-[1.6] break-words"><?php echo esc_html( $sp_body ); ?></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- RIGHT: GPhC registration card -->
			<div class="relative box-border break-words">
				<div class="absolute bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.2),rgba(0,0,0,0))] box-border blur-2xl h-32 break-words w-32 rounded-full -right-6 -top-6" aria-hidden="true"></div>
				<div class="relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.25)_0px_25px_50px_-12px] box-border break-words border border-gray-100 overflow-hidden rounded-3xl border-solid">

					<!-- Gradient header band -->
					<div class="relative bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border break-words overflow-hidden p-10">
						<div class="relative box-border break-words z-10 mb-6">
							<div class="items-center backdrop-blur-sm bg-white/20 box-border gap-x-3 inline-flex break-words gap-y-3 border px-5 py-3 rounded-2xl border-solid border-white/30">
								<?php echo sp_icon( 'shield', 'h-12 w-12 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
						<h3 class="ultra-heading text-white text-3xl font-black box-border leading-[1.2] break-words md:text-4xl"><?php esc_html_e( 'General Pharmaceutical', 'smart-pharmacy' ); ?><br><?php esc_html_e( 'Council', 'smart-pharmacy' ); ?></h3>
					</div>

					<!-- Number + body -->
					<div class="box-border break-words p-10">
						<div class="box-border break-words mb-8">
							<div class="items-center bg-[linear-gradient(to_right,rgb(240,253,244),rgb(236,253,245))] box-border gap-x-3 inline-flex break-words gap-y-3 border border-green-200 mb-6 px-5 py-3 rounded-2xl border-solid">
								<div class="items-center bg-green-500 box-border flex h-8 justify-center break-words w-8 rounded-full">
									<?php echo sp_icon( 'check', 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<span class="text-green-700 text-sm font-bold box-border block tracking-[0.35px] leading-5 break-words uppercase antialiased"><?php esc_html_e( 'Verified Registration', 'smart-pharmacy' ); ?></span>
							</div>
							<div class="box-border break-words">
								<p class="text-neutral-500 text-sm font-bold box-border tracking-[0.7px] leading-5 break-words uppercase mb-2 antialiased"><?php esc_html_e( 'GPhC Registered Pharmacy', 'smart-pharmacy' ); ?></p>
								<div class="relative box-border inline-block break-words">
									<p class="text-transparent text-5xl font-black bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] box-border leading-[1.1] break-words md:text-6xl antialiased"><?php echo esc_html( $sf_gphc_number ); ?></p>
									<div class="absolute bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] box-border h-1 break-words rounded-full -bottom-2 inset-x-0" aria-hidden="true"></div>
								</div>
							</div>
						</div>
						<div class="box-border break-words border-gray-100 pt-8 border-t-2 border-solid">
							<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words mb-6"><?php echo esc_html( $sf_gphc_body ); ?></p>
							<a href="<?php echo esc_url( $sf_verify_u ); ?>" target="_blank" rel="noopener noreferrer" class="premium-button text-white font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 px-8 py-4 rounded-2xl hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] transition-all duration-300">
								<span class="box-border block break-words antialiased"><?php echo esc_html( $sf_verify_l ); ?></span>
								<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</a>
						</div>
					</div>

					<!-- Bottom accent bar -->
					<div class="bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126),rgb(59,155,159))] box-border h-2 break-words" aria-hidden="true"></div>
				</div>
			</div>

		</div>
	</div>
</section>
