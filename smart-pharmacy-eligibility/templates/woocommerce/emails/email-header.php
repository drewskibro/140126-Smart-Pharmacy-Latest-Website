<?php
/**
 * Smart Pharmacy branded email header.
 *
 * Overrides WooCommerce's emails/email-header.php via SPE's
 * woocommerce_locate_template filter. Keeps WooCommerce's table
 * structure + element IDs (so email-styles.php still styles the body
 * across email clients) and layers on the brand: the Smart Pharmacy
 * logo, the teal (#10c0a9) heading band, and a rounded white content
 * card. All styles are inline (email-client safe).
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

$sp_logo = 'https://c.animaapp.com/mhiyf2riFan5kf/assets/new-updated-logo-smart-pharmacy-(1).png';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></title>
</head>
<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color:#f4f9fa;">
	<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" style="background-color:#f4f9fa;margin:0;padding:24px 0;-webkit-text-size-adjust:none;width:100%;">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tr>
				<td align="center" valign="top">
					<div id="template_header_image" style="padding:12px 0 16px;text-align:center;">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:inline-block;">
							<img src="<?php echo esc_url( $sp_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" height="auto" style="border:none;display:block;font-size:16px;font-weight:bold;height:auto;outline:none;text-decoration:none;text-transform:capitalize;max-width:220px;margin:0 auto;" />
						</a>
					</div>
					<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,0.04);overflow:hidden;">
						<tr>
							<td align="center" valign="top">
								<!-- Header -->
								<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style="background-color:#10c0a9;color:#ffffff;border-top-left-radius:12px;border-top-right-radius:12px;">
									<tr>
										<td id="header_wrapper" style="padding:30px 36px;display:block;">
											<h1 style="color:#ffffff;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:400;line-height:130%;margin:0;text-align:left;"><?php echo wp_kses_post( $email_heading ); ?></h1>
										</td>
									</tr>
								</table>
								<!-- End Header -->
							</td>
						</tr>
						<tr>
							<td align="center" valign="top">
								<!-- Body -->
								<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
									<tr>
										<td valign="top" id="body_content" style="background-color:#ffffff;">
											<table border="0" cellpadding="0" cellspacing="0" width="100%">
												<tr>
													<td valign="top" style="padding:36px 36px 28px;">
														<div id="body_content_inner" style="color:#374151;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:165%;text-align:left;">
