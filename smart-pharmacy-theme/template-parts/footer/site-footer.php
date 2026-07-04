<?php
/**
 * Site footer markup — four-column grid, company registration badge, bottom meta.
 *
 * @package SmartPharmacy
 */

// Logo
$sp_logo     = function_exists( 'get_field' ) ? get_field( 'brand_logo', 'option' ) : null;
$sp_logo_url = ( is_array( $sp_logo ) && ! empty( $sp_logo['url'] ) )
	? $sp_logo['url']
	: 'https://c.animaapp.com/mhiyf2riFan5kf/assets/new-updated-logo-smart-pharmacy-(1).png';
$sp_logo_alt = sp_field( 'brand_logo_alt', 'Smart Pharmacy' );

// Content
$sp_tagline         = sp_field( 'brand_footer_tagline', 'Your trusted online pharmacy providing expert healthcare on your terms. NHS-approved, prescription-perfect, delivered tomorrow.' );
$sp_trading_address = sp_field( 'contact_trading_address', "Smart Pharmacy\nUnit A2 Ivinghoe Business Centre\nLU55BQ" );
$sp_registered      = sp_field( 'contact_registered', 'Emwhy Pharma, 51 Arnald Way, Houghton Regis, LU55UN' );
$sp_gphc            = sp_field( 'comp_gphc_number', '9012842' );
$sp_mhra_logo       = sp_field( 'comp_mhra_logo' );
$sp_mhra_url        = sp_field( 'comp_mhra_register_url' );
$sp_mhra_logo_url   = is_array( $sp_mhra_logo ) && ! empty( $sp_mhra_logo['url'] ) ? $sp_mhra_logo['url'] : '';
$sp_copyright       = sp_field( 'nav_copyright', '© ' . gmdate( 'Y' ) . ' Smart Pharmacy. All rights reserved.' );

// Payment methods image (ACF array or fallback URL).
$sp_payment_img     = function_exists( 'get_field' ) ? get_field( 'brand_payment_methods', 'option' ) : null;
$sp_payment_img_url = ( is_array( $sp_payment_img ) && ! empty( $sp_payment_img['url'] ) )
	? $sp_payment_img['url']
	: 'https://c.animaapp.com/mhiyf2riFan5kf/assets/33.svg';

// Quick links and legal links repeaters — fall back to prototype defaults.
$sp_quick_links = sp_field( 'nav_footer_quick', array() );
if ( empty( $sp_quick_links ) || ! is_array( $sp_quick_links ) ) {
	$sp_quick_links = array(
		array( 'label' => 'About Us',       'url' => '/about/' ),
		array( 'label' => 'Contact Us',     'url' => '/contact/' ),
		array( 'label' => 'Track Your Order', 'url' => 'https://www.royalmail.com/track-your-item' ),
		array( 'label' => 'FAQs',           'url' => '/faqs/' ),
	);
}

$sp_legal_links = sp_field( 'nav_footer_legal', array() );
if ( empty( $sp_legal_links ) || ! is_array( $sp_legal_links ) ) {
	$sp_legal_links = array(
		array( 'label' => 'Terms & Conditions',   'url' => '/terms-conditions/' ),
		array( 'label' => 'Privacy Policy',       'url' => '/privacy-policy/' ),
		array( 'label' => 'Cookie Policy',        'url' => '/cookie-policy/' ),
		array( 'label' => 'Complaints Procedure', 'url' => '/complaints-procedure/' ),
	);
}

// Social URLs
$sp_social = array(
	'facebook'  => sp_field( 'social_facebook', '' ),
	'instagram' => sp_field( 'social_instagram', '' ),
	'tiktok'    => sp_field( 'social_tiktok', '' ),
	'twitter'   => sp_field( 'social_twitter', '' ),
	'linkedin'  => sp_field( 'social_linkedin', '' ),
);
?>

<footer id="colophon" class="relative bg-[linear-gradient(to_right_bottom,rgb(253,252,250),rgb(248,246,243),rgb(245,243,240))] box-border break-words overflow-hidden">

	<!-- Decorative blurred orbs -->
	<div class="absolute bg-teal-500/10 box-border blur-3xl h-96 break-words w-96 rounded-full -left-20 -top-20" aria-hidden="true"></div>
	<div class="absolute bg-teal-700/10 box-border blur-3xl h-[500px] break-words w-[500px] rounded-full -right-20 top-40" aria-hidden="true"></div>

	<div class="relative box-border break-words w-full z-10 px-6 py-12 md:px-16 md:py-16">
		<div class="box-border max-w-[1600px] break-words mx-auto">

			<!-- 4-column grid -->
			<div class="box-border gap-x-12 grid grid-cols-[repeat(1,minmax(0px,1fr))] break-words gap-y-12 mb-16 md:grid-cols-[repeat(4,minmax(0px,1fr))]">

				<!-- Column 1: Logo + tagline + social -->
				<div class="box-border break-words md:col-end-[span_1] md:col-start-[span_1]">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="box-border inline-block break-words mb-6">
						<img src="<?php echo esc_url( $sp_logo_url ); ?>" alt="<?php echo esc_attr( $sp_logo_alt ); ?>" class="box-border h-16 max-w-full break-words" />
					</a>
					<p class="text-neutral-600 box-border leading-[24.375px] break-words mb-6"><?php echo esc_html( $sp_tagline ); ?></p>
					<div class="items-center box-border gap-x-3 flex break-words gap-y-3">
						<?php
						$sp_social_labels = array(
							'facebook'  => __( 'Facebook', 'smart-pharmacy' ),
							'instagram' => __( 'Instagram', 'smart-pharmacy' ),
							'tiktok'    => __( 'TikTok', 'smart-pharmacy' ),
							'twitter'   => __( 'X', 'smart-pharmacy' ),
							'linkedin'  => __( 'LinkedIn', 'smart-pharmacy' ),
						);
						$sp_social_icons = array(
							'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>',
							'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
							'tiktok'    => '<svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005.8 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1.84-.1z"/></svg>',
							'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
							'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.23 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.72V1.72C24 .77 23.21 0 22.23 0z"/></svg>',
						);
						foreach ( $sp_social as $sp_key => $sp_url ) :
							if ( empty( $sp_url ) ) { continue; }
							?>
							<a href="<?php echo esc_url( $sp_url ); ?>" aria-label="<?php echo esc_attr( $sp_social_labels[ $sp_key ] ); ?>" target="_blank" rel="noopener noreferrer" class="text-neutral-600 items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border flex h-12 justify-center break-words w-12 rounded-full hover:text-white hover:bg-teal-500 hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_4px_6px_-1px,rgba(0,0,0,0.1)_0px_2px_4px_-2px] transition-all duration-200">
								<?php echo $sp_social_icons[ $sp_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup is static, hardcoded, trusted. ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Column 2: Quick Links -->
				<div class="box-border break-words">
					<h3 class="ultra-heading text-neutral-900 text-lg font-bold box-border leading-7 break-words mb-6"><?php esc_html_e( 'Quick Links', 'smart-pharmacy' ); ?></h3>
					<ul class="box-border list-none break-words pl-0 space-y-3">
						<?php foreach ( $sp_quick_links as $sp_link ) :
							$sp_label = isset( $sp_link['label'] ) ? (string) $sp_link['label'] : '';
							$sp_url   = isset( $sp_link['url'] ) ? (string) $sp_link['url'] : '#';
							?>
							<li class="box-border break-words">
								<a href="<?php echo esc_url( $sp_url ); ?>" class="text-neutral-600 items-center box-border gap-x-2 flex break-words gap-y-2 hover:text-teal-500 transition-colors duration-200"><?php echo esc_html( $sp_label ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!-- Column 3: Legal -->
				<div class="box-border break-words">
					<h3 class="ultra-heading text-neutral-900 text-lg font-bold box-border leading-7 break-words mb-6"><?php esc_html_e( 'Legal', 'smart-pharmacy' ); ?></h3>
					<ul class="box-border list-none break-words pl-0 space-y-3">
						<?php foreach ( $sp_legal_links as $sp_link ) :
							$sp_label = isset( $sp_link['label'] ) ? (string) $sp_link['label'] : '';
							$sp_url   = isset( $sp_link['url'] ) ? (string) $sp_link['url'] : '#';
							?>
							<li class="box-border break-words">
								<a href="<?php echo esc_url( $sp_url ); ?>" class="text-neutral-600 items-center box-border gap-x-2 flex break-words gap-y-2 hover:text-teal-500 transition-colors duration-200"><?php echo esc_html( $sp_label ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!-- Column 4: Contact -->
				<div class="box-border break-words">
					<h3 class="ultra-heading text-neutral-900 text-lg font-bold box-border leading-7 break-words mb-6"><?php esc_html_e( 'Contact', 'smart-pharmacy' ); ?></h3>
					<div class="box-border break-words">

						<!-- Trading address -->
						<div class="items-start box-border gap-x-3 flex break-words gap-y-3">
							<div class="items-center bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.1),rgba(44,122,126,0.1))] box-border flex shrink-0 h-10 justify-center break-words w-10 rounded-lg">
								<svg class="w-5 h-5 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
									<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
									<circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
							<div class="box-border break-words">
								<p class="text-neutral-900 text-sm font-semibold box-border leading-5 break-words mb-1"><?php esc_html_e( 'Trading Address', 'smart-pharmacy' ); ?></p>
								<p class="text-neutral-600 text-sm box-border leading-[22.75px] break-words"><?php echo nl2br( esc_html( $sp_trading_address ) ); ?></p>
							</div>
						</div>

						<!-- GPhC number -->
						<div class="items-start box-border gap-x-3 flex break-words gap-y-3 mt-4">
							<div class="items-center bg-[linear-gradient(to_right_bottom,rgba(59,155,159,0.1),rgba(44,122,126,0.1))] box-border flex shrink-0 h-10 justify-center break-words w-10 rounded-lg">
								<svg class="w-5 h-5 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
									<path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</div>
							<div class="box-border break-words">
								<p class="text-neutral-900 text-sm font-semibold box-border leading-5 break-words mb-1"><?php esc_html_e( 'GPhC Number', 'smart-pharmacy' ); ?></p>
								<p class="text-neutral-600 text-sm box-border leading-5 break-words"><?php echo esc_html( $sp_gphc ); ?></p>
							</div>
						</div>

						<?php if ( $sp_mhra_logo_url ) : ?>
							<!-- MHRA registered pharmacy logo -->
							<div class="items-start box-border gap-x-3 flex break-words gap-y-3 mt-4">
								<div class="box-border break-words">
									<p class="text-neutral-900 text-sm font-semibold box-border leading-5 break-words mb-2"><?php esc_html_e( 'Registered Pharmacy', 'smart-pharmacy' ); ?></p>
									<?php if ( $sp_mhra_url ) : ?>
										<a href="<?php echo esc_url( $sp_mhra_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Verify this pharmacy on the MHRA register', 'smart-pharmacy' ); ?>">
											<img src="<?php echo esc_url( $sp_mhra_logo_url ); ?>" alt="<?php esc_attr_e( 'MHRA registered pharmacy', 'smart-pharmacy' ); ?>" class="h-14 w-auto" />
										</a>
									<?php else : ?>
										<img src="<?php echo esc_url( $sp_mhra_logo_url ); ?>" alt="<?php esc_attr_e( 'MHRA registered pharmacy', 'smart-pharmacy' ); ?>" class="h-14 w-auto" />
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- /.grid -->

			<!-- Company registration badge -->
			<div class="backdrop-blur-sm bg-white/60 box-border break-words border border-gray-100 mb-12 p-6 rounded-2xl border-solid">
				<div class="items-start box-border gap-x-4 flex flex-col break-words gap-y-4 md:items-center md:flex-row">
					<div class="box-border shrink-0 break-words">
						<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-14 justify-center break-words w-14 rounded-xl">
							<svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
								<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
					</div>
					<div class="box-border basis-[0%] grow break-words">
						<h4 class="text-neutral-900 text-sm font-bold box-border leading-5 break-words mb-1"><?php esc_html_e( 'Company Registration', 'smart-pharmacy' ); ?></h4>
						<p class="text-neutral-600 text-sm box-border leading-[22.75px] break-words"><strong class="font-bold"><?php esc_html_e( 'Registered:', 'smart-pharmacy' ); ?></strong> <?php echo esc_html( $sp_registered ); ?></p>
					</div>
				</div>
			</div>

			<!-- Bottom meta -->
			<div class="box-border break-words border-gray-200 pt-8 border-t border-solid">
				<div class="items-center box-border gap-x-6 flex flex-col justify-between break-words gap-y-6 md:flex-row">
					<p class="text-neutral-600 text-sm box-border leading-5 break-words"><?php echo esc_html( $sp_copyright ); ?></p>
					<div class="items-center box-border gap-x-2 flex break-words gap-y-2">
						<span class="text-neutral-500 text-xs box-border block leading-4 break-words mr-2"><?php esc_html_e( 'We accept:', 'smart-pharmacy' ); ?></span>
						<div class="items-center bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border gap-x-2 flex break-words gap-y-2 px-4 py-2 rounded-lg">
							<img src="<?php echo esc_url( $sp_payment_img_url ); ?>" alt="<?php esc_attr_e( 'Payment methods', 'smart-pharmacy' ); ?>" class="box-border h-6 max-w-full break-words" />
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</footer>
