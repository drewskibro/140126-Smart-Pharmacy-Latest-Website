<?php
/**
 * One-shot seeder for the site's core content pages.
 *
 * Creates the pages the site links to but never had:
 *   /about/               (the header nav already points here)
 *   /nhs-prescriptions/   (the header + homepage CTA already point here)
 *   /contact/
 *   /faq/
 *   /terms-conditions/
 *   /privacy-policy/
 *   /cookie-policy/
 *
 * They render through page.php (branded header + `.sp-prose` content card).
 * Gutenberg is disabled for pages, so the client edits this HTML in the
 * classic editor exactly as they would any other page.
 *
 * ---------------------------------------------------------------------------
 * IMPORTANT — the legal pages are DRAFTS, not legal advice.
 *
 * They follow the structure a UK distance-selling pharmacy needs (GPhC, MHRA,
 * UK GDPR / DPA 2018, PECR, Consumer Contracts Regulations 2013). Every fact
 * below was supplied and confirmed by the client — nothing is invented, since
 * a false regulatory claim on a pharmacy site is worse than a visible gap.
 *
 * Before launch these MUST be reviewed by the superintendent pharmacist and a
 * solicitor.
 *
 * Confirmed: Emwhy Pharma Ltd, company no. 14563648, GPhC 9012842,
 * superintendent Murtaza Yusufali (GPhC 2086087), 03300 436 364,
 * emwhypharma@gmail.com, Mon-Fri 9am-5pm.
 *
 * Deliberately NOT stated yet, at the client's request: ICO registration
 * number and VAT number. Both are legal requirements for this business — the
 * ICO data protection fee is mandatory for a controller processing health
 * data, and a VAT-registered trader must show its VAT number. Add them as
 * soon as they exist.
 * ---------------------------------------------------------------------------
 *
 * Idempotency: guarded by `_sp_core_pages_seeded_v1`; each page is also
 * checked by slug before insert, and deletion is respected.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tokens substituted into the page HTML below.
 *
 * @return array<string,string>
 */
function sp_core_pages_tokens() {
	// All confirmed by the client, cross-checked against the pharmacy's
	// previous website. No invented facts remain.
	return array(
		'{{GPHC}}'           => '9012842',
		'{{ENTITY}}'         => 'Emwhy Pharma Ltd',
		'{{COMPANY_NO}}'     => '14563648',
		'{{REGISTERED}}'     => 'Emwhy Pharma Ltd, 51 Arnald Way, Houghton Regis, LU5 5UN',
		'{{TRADING}}'        => 'Unit A2 Ivinghoe Business Centre, LU5 5BQ',
		'{{SUPERINTENDENT}}' => 'Murtaza Yusufali (GPhC registration number 2086087)',
		'{{EMAIL}}'          => '<a href="mailto:emwhypharma@gmail.com">emwhypharma@gmail.com</a>',
		'{{PHONE}}'          => '<a href="tel:+443300436364">03300 436 364</a>',
		'{{HOURS}}'          => 'Monday to Friday, 9am to 5pm<br />Closed Saturday and Sunday',
		'{{UPDATED}}'        => date_i18n( 'j F Y' ),
	);
}

/**
 * The pages to create: slug => [title, excerpt, content].
 *
 * @return array<string,array{title:string,excerpt:string,content:string}>
 */
function sp_core_pages_definitions() {

	$about = <<<'HTML'
<h2>Who we are</h2>
<p>Smart Pharmacy is a UK online pharmacy. We dispense NHS and private prescriptions, and supply pharmacy medicines and everyday health products, from our registered premises and deliver them discreetly across the UK.</p>
<p>We are operated by {{ENTITY}}, registered in England and Wales (company number {{COMPANY_NO}}). Our registered office is {{REGISTERED}} and we dispense from {{TRADING}}.</p>

<h2>Registered and regulated</h2>
<p>Our pharmacy is registered with the General Pharmaceutical Council (GPhC), the regulator for pharmacies in Great Britain, under registration number <strong>{{GPHC}}</strong>. You can check our registration on the GPhC register at <a href="https://www.pharmacyregulation.org/registers" target="_blank" rel="noopener noreferrer">pharmacyregulation.org</a>.</p>
<p>Our superintendent pharmacist is {{SUPERINTENDENT}}. All medicines are dispensed under the supervision of a GPhC-registered pharmacist, and all prescriptions are issued by UK-registered prescribers.</p>
<p>As a distance-selling pharmacy we also operate under the Medicines and Healthcare products Regulatory Agency (MHRA) framework for selling medicines online.</p>

<h2>How we keep you safe</h2>
<p>Medicines are not ordinary retail products, so we do not sell them like ordinary retail products. Everything we do is built around one principle: nothing is dispensed until a healthcare professional is satisfied it is safe for you.</p>
<ol>
<li><strong>You complete a consultation.</strong> A secure medical questionnaire covering your health, your medicines and your history.</li>
<li><strong>A pharmacist or prescriber reviews it.</strong> They check for interactions, contraindications and anything that needs clarifying. They may contact you with further questions.</li>
<li><strong>Only then is it dispensed.</strong> If it is not right for you, we decline it and you are not charged. Your card is only charged once your treatment is approved.</li>
</ol>
<p>We will always say no when no is the right answer. Being able to buy something online should never be the same as it being safe for you.</p>

<h2>What we offer</h2>
<ul>
<li><strong>NHS prescriptions</strong> — nominate us as your pharmacy and get your repeat prescriptions delivered free. See our <a href="/nhs-prescriptions/">NHS prescriptions page</a>.</li>
<li><strong>Private treatments</strong> — including weight management, hair loss, erectile dysfunction and other treatments, each behind a clinical consultation.</li>
<li><strong>Everyday health</strong> — vitamins, skincare, oral care, first aid and over-the-counter medicines.</li>
</ul>

<h2>Delivery</h2>
<p>We deliver across the UK in plain, discreet packaging. Orders over £30 get <strong>free next-day delivery</strong>. Below £30 a standard delivery service applies, normally arriving within 1 to 3 working days. NHS prescriptions are always delivered free.</p>

<h2>Talk to us</h2>
<p>Our pharmacy team is here if you need advice, whether or not you have ordered from us. Visit our <a href="/contact/">contact page</a> or read our <a href="/faq/">frequently asked questions</a>.</p>

<div class="sp-notice">
<p><strong>If you need urgent medical help,</strong> do not use this website. Call <strong>111</strong> for urgent advice, or <strong>999</strong> in an emergency.</p>
</div>
HTML;

	$nhs = <<<'HTML'
<p>Order your NHS repeat prescriptions online and have them delivered to your door, free of charge, anywhere in the UK.</p>

<h2>How it works</h2>
<ol>
<li><strong>Nominate us as your pharmacy.</strong> Tell your GP surgery you would like Smart Pharmacy to dispense your prescriptions, or nominate us through the NHS App. You only need to do this once.</li>
<li><strong>Order your repeat prescription.</strong> Request it from your GP as usual, through the NHS App, your surgery's online service, or by asking us to order it for you.</li>
<li><strong>We dispense it.</strong> Your prescription arrives with us electronically. Our pharmacist checks and dispenses it.</li>
<li><strong>We deliver it, free.</strong> Sent in plain, discreet packaging to your chosen address.</li>
</ol>

<h2>Why nominate Smart Pharmacy</h2>
<ul>
<li><strong>Free delivery</strong> on all NHS prescriptions, with no minimum spend.</li>
<li><strong>No queues, no travel</strong> — nothing to collect and no waiting at a counter.</li>
<li><strong>Repeat prescriptions handled for you</strong>, so your medicines arrive before you run out.</li>
<li><strong>Pharmacist advice whenever you need it</strong>, by phone or email.</li>
</ul>

<h2>Prescription charges and exemptions</h2>
<p>If you normally pay for NHS prescriptions, the standard NHS prescription charge applies per item, exactly as it would at any other pharmacy. We do not add anything on top, and delivery is free.</p>
<p>Many people do not pay at all. You may be entitled to free NHS prescriptions if, for example, you are under 16, aged 60 or over, pregnant or have recently given birth, or hold a valid medical exemption certificate. If you pay for several items a month, a Prescription Prepayment Certificate (PPC) may save you money.</p>
<p>You can check whether you are entitled to free prescriptions on the <a href="https://www.nhs.uk/nhs-services/prescriptions/who-can-get-free-prescriptions/" target="_blank" rel="noopener noreferrer">NHS website</a>. We are required to check exemption entitlement and may ask you to confirm it.</p>

<h2>What we cannot dispense by post</h2>
<p>Some items cannot be supplied through a distance-selling pharmacy, or cannot be safely posted. These include certain controlled drugs, medicines needing cold-chain storage that we cannot guarantee in transit, and anything you need urgently today. If we cannot dispense an item, we will tell you promptly and help you find a pharmacy that can.</p>

<div class="sp-notice">
<p><strong>Do not use this service if you need medicine urgently.</strong> If you need advice fast, call <strong>111</strong>. In an emergency, call <strong>999</strong> or go to A&amp;E.</p>
</div>

<h2>Getting started</h2>
<p>Ready to nominate us? <a href="/contact/">Get in touch</a> and our team will walk you through it, or nominate Smart Pharmacy directly through the NHS App.</p>
<p>Our GPhC registration number is <strong>{{GPHC}}</strong>.</p>
HTML;

	$contact = <<<'HTML'
<div class="sp-notice">
<p><strong>This is not an emergency service.</strong> If you need urgent medical help, call <strong>111</strong>. In a medical emergency, call <strong>999</strong> or go to your nearest A&amp;E. We cannot respond to urgent clinical needs by email.</p>
</div>

<h2>Speak to our pharmacy team</h2>
<p>Our pharmacists are happy to help with questions about your order, your medicines, or a treatment you are considering, whether or not you have bought from us.</p>

<h3>Phone</h3>
<p>{{PHONE}}<br />{{HOURS}}</p>

<h3>Email</h3>
<p>{{EMAIL}}<br />We aim to reply within one working day.</p>

<h3>Post</h3>
<p><strong>Dispensing address</strong><br />Smart Pharmacy<br />{{TRADING}}</p>
<p><strong>Registered office</strong><br />{{REGISTERED}}</p>

<h2>What to contact us about</h2>
<ul>
<li><strong>Your order</strong> — where it is, changing it, or a problem with it.</li>
<li><strong>Your consultation</strong> — a question about the questionnaire, or about a decision our pharmacist made.</li>
<li><strong>Your medicines</strong> — how to take them, side effects, or interactions with anything else you take.</li>
<li><strong>NHS prescriptions</strong> — nominating us, or chasing a repeat.</li>
<li><strong>Your data</strong> — any request under the UK GDPR. See our <a href="/privacy-policy/">privacy policy</a>.</li>
</ul>

<h2>Reporting a side effect</h2>
<p>If you think you have had a side effect from a medicine, tell us, and report it through the MHRA <a href="https://yellowcard.mhra.gov.uk/" target="_blank" rel="noopener noreferrer">Yellow Card scheme</a>. If the reaction is severe, seek medical help immediately.</p>

<h2>Complaints</h2>
<p>If something has gone wrong, we want to know. Email us at {{EMAIL}} with the word "Complaint" in the subject line, along with your order number and what happened. We will acknowledge your complaint within 3 working days and aim to resolve it within 20 working days.</p>
<p>If you are not satisfied with our response, you can raise it with the General Pharmaceutical Council, our regulator:</p>
<p>General Pharmaceutical Council<br />
<a href="https://www.pharmacyregulation.org/raise-concern" target="_blank" rel="noopener noreferrer">pharmacyregulation.org/raise-concern</a></p>
<p>Our GPhC registration number is <strong>{{GPHC}}</strong>. You can verify our registration on the <a href="https://www.pharmacyregulation.org/registers" target="_blank" rel="noopener noreferrer">GPhC register</a>.</p>
HTML;

	$faq = <<<'HTML'
<h2>About the pharmacy</h2>

<h3>Is Smart Pharmacy a registered pharmacy?</h3>
<p>Yes. We are registered with the General Pharmaceutical Council (GPhC) under registration number <strong>{{GPHC}}</strong>, and you can verify this on the <a href="https://www.pharmacyregulation.org/registers" target="_blank" rel="noopener noreferrer">GPhC register</a>. All medicines are dispensed from our registered premises under the supervision of a GPhC-registered pharmacist, and all prescriptions are issued by UK-registered prescribers.</p>

<h3>Who can use Smart Pharmacy?</h3>
<p>Our services are available to adults aged 18 and over who are resident in the UK. Some treatments carry additional age or medical restrictions, which will be made clear during your consultation.</p>

<h2>Consultations and prescriptions</h2>

<h3>How does the online consultation work?</h3>
<p>When you choose a treatment, you complete a secure medical questionnaire. A qualified pharmacist or prescriber reviews your answers and may contact you if they need more information. If the treatment is approved, your medicine is dispensed and delivered to you.</p>

<h3>Are consultations free?</h3>
<p>Yes. Online consultations are free. You only pay for your medicine if your treatment is approved, and there are no hidden consultation fees.</p>

<h3>What if my treatment is not approved?</h3>
<p>If our pharmacist decides a treatment is not suitable for you, your order is cancelled and <strong>you are not charged</strong>. The payment held on your card is released. We will explain the decision and, where we can, suggest what to do next.</p>

<h3>How do you make sure a medicine is safe for me?</h3>
<p>Every order is checked against your medical history and your consultation answers. We check for interactions with your other medicines, review contraindications, verify your identity and age, and follow GPhC and MHRA guidance. If anything needs clarifying, we contact you before dispensing.</p>

<h3>Why do you need photo ID?</h3>
<p>For some pharmacy and prescription-only medicines we must verify your identity and age before dispensing. Your ID is stored securely and used only for that purpose. See our <a href="/privacy-policy/">privacy policy</a>.</p>

<h2>Orders and payment</h2>

<h3>When am I charged?</h3>
<p>For treatments that need a consultation, your card is <strong>authorised</strong> at checkout but not charged. The money is only taken once a pharmacist approves your treatment. If it is declined, the authorisation is released and no payment is taken. Everyday products without a consultation are charged at checkout as normal.</p>

<h3>What payment methods do you accept?</h3>
<p>We accept major debit and credit cards through our secure payment provider. We never see or store your full card details.</p>

<h2>Delivery</h2>

<h3>How long does delivery take?</h3>
<p>Once your order is approved it is normally dispatched the same or next working day. Orders over £30 are sent on a <strong>free next-day service</strong>. Standard delivery, used on orders below £30, normally arrives within 1 to 3 working days. Delivery times are estimates rather than guarantees.</p>

<h3>What does delivery cost?</h3>
<p><strong>Free next-day delivery on orders over £30</strong>, and free delivery on all NHS prescriptions with no minimum spend. Below £30 a standard delivery charge applies, shown at checkout.</p>

<h3>Is the packaging discreet?</h3>
<p>Yes. Everything is sent in plain, unbranded packaging with no indication of the contents.</p>

<h2>Returns and refunds</h2>

<h3>Can I return a medicine?</h3>
<p>No. For your safety and by law, medicines cannot be returned to us for reuse once they have been dispatched. Any medicine returned to a pharmacy must be destroyed. If your medicine is faulty, damaged, out of date, or we sent the wrong item, contact us straight away and we will replace it or refund you in full.</p>

<h3>Can I return other products?</h3>
<p>Yes. Non-medicinal products in unopened, resalable condition can be returned within 14 days. See our <a href="/terms-conditions/">terms and conditions</a>.</p>

<h2>Privacy</h2>

<h3>What do you do with my health information?</h3>
<p>We treat it as confidential medical information. It is used to assess and dispense your treatment safely, and it is only shared with the people who need it, such as the prescriber reviewing your consultation. Full detail is in our <a href="/privacy-policy/">privacy policy</a>.</p>

<div class="sp-notice">
<p><strong>Still need help?</strong> <a href="/contact/">Contact our pharmacy team</a>. If you need urgent medical help, call <strong>111</strong>, or <strong>999</strong> in an emergency.</p>
</div>
HTML;

	$terms = <<<'HTML'
<p><em>Last updated: {{UPDATED}}</em></p>

<h2>1. About us and these terms</h2>
<p>Smart Pharmacy is a trading name of {{ENTITY}}, a company registered in England and Wales under company number {{COMPANY_NO}}, with its registered office at {{REGISTERED}} and dispensing premises at {{TRADING}}.</p>
<p>We are a registered pharmacy, regulated by the General Pharmaceutical Council (GPhC) under registration number <strong>{{GPHC}}</strong>. Our superintendent pharmacist is {{SUPERINTENDENT}}.</p>
<p>These terms govern your use of this website and any purchase you make from us. By placing an order you agree to them. Please read them carefully, and please read our <a href="/privacy-policy/">privacy policy</a> too.</p>

<h2>2. Who can buy from us</h2>
<p>You may only order from us if you are aged 18 or over, resident in the UK, and buying as a consumer rather than for resale. Individual treatments may carry further age or clinical restrictions. We may refuse any order.</p>

<h2>3. The consultation and clinical review</h2>
<p>Prescription-only medicines and pharmacy (P) medicines cannot lawfully be sold like ordinary goods. Before we supply them you must complete an online consultation.</p>
<ul>
<li>You must answer every question <strong>truthfully, accurately and completely</strong>. Our clinical decisions depend on your answers. Giving false or incomplete information may put your health at serious risk, and may mean we cannot supply you.</li>
<li>Your answers are reviewed by a pharmacist or a UK-registered prescriber, who may contact you for more information before making a decision.</li>
<li><strong>Approval is never guaranteed.</strong> We may decline to supply any medicine, at our absolute discretion, if we do not consider it clinically appropriate for you. Placing an order does not entitle you to be supplied.</li>
<li>An online consultation is not a substitute for seeing your GP, and does not replace an in-person examination where one is needed.</li>
</ul>

<h2>4. Orders and when a contract is formed</h2>
<p>Your order is an offer to buy. No contract exists until we accept it.</p>
<ul>
<li>For <strong>everyday (non-consultation) products</strong>, we accept your order when we dispatch it.</li>
<li>For <strong>products requiring a consultation</strong>, we accept your order only when a pharmacist or prescriber approves your treatment. If we decline, no contract is formed.</li>
</ul>
<p>We may cancel an order at any time before dispatch, including where an item is out of stock, mispriced, or where supply would be clinically inappropriate or unlawful.</p>

<h2>5. Prices and VAT</h2>
<p><strong>All prices shown on this website include VAT</strong> where VAT applies. The price you see is the price you pay for the goods; any delivery charge is shown separately at checkout.</p>
<p>VAT is charged at the rate that applies to each product. Many medicines and health products are zero-rated or exempt, so no VAT is added to them.</p>
<p>We take reasonable care to price correctly, but if we discover an obvious error before dispatch we will contact you and you may confirm the corrected price or cancel.</p>

<h2>6. Payment</h2>
<p>Payment is taken by card through our secure payment provider. We do not receive or store your full card details.</p>
<p><strong>For orders needing a consultation, the way payment works is different, and it is important you understand it:</strong></p>
<ul>
<li>When you check out, your card is <strong>authorised</strong> for the order amount. The money is held by your bank but <strong>not taken</strong>.</li>
<li>If a pharmacist <strong>approves</strong> your treatment, the payment is captured at that point and your order is dispensed.</li>
<li>If a pharmacist <strong>declines</strong> your treatment, the authorisation is cancelled and <strong>no payment is taken</strong>. Depending on your bank, the held amount may take a few working days to clear from your available balance.</li>
</ul>

<h2>7. Delivery</h2>
<p>We deliver to addresses in the UK only, in plain, discreet packaging.</p>
<ul>
<li><strong>Orders over £30</strong> — free next-day delivery.</li>
<li><strong>Orders under £30</strong> — standard delivery, charged at the rate shown at checkout, normally 1 to 3 working days.</li>
<li><strong>NHS prescriptions</strong> — always delivered free, with no minimum spend.</li>
</ul>
<p>Delivery timescales are estimates, not guarantees, and next-day delivery depends on your order being approved and dispatched in time for that day's collection. Risk in the goods passes to you on delivery. If nobody is available to receive a delivery, the carrier's usual procedure applies.</p>
<p>We may be unable to post certain items, including some controlled drugs and medicines requiring cold-chain storage. We will tell you promptly if so.</p>

<h2>8. Cancellations, returns and refunds</h2>

<h3>8.1 Medicines cannot be returned</h3>
<p><strong>For safety reasons, and in line with pharmacy law and professional standards, medicines cannot be returned to us for reuse once they have been dispatched.</strong> A medicine that has left our control cannot be guaranteed to have been stored correctly, so it can never be re-dispensed to another patient. Any medicine returned to us will be destroyed.</p>
<p>Because of this, your statutory 14-day right to cancel under the Consumer Contracts (Information, Cancellation and Additional Charges) Regulations 2013 does not apply to medicinal products once they have been dispatched, as they are sealed goods that are not suitable for return for reasons of health protection and hygiene.</p>
<p>This does <strong>not</strong> affect your rights where a medicine is faulty, damaged, out of date, or where we supplied the wrong item. In those cases contact us immediately and we will replace it or refund you in full.</p>

<h3>8.2 Non-medicinal products</h3>
<p>For non-medicinal products (for example vitamins, skincare, oral care, and equipment) you have 14 days from receipt to cancel, and a further 14 days to return the goods, provided they are unused, unopened and in resalable condition. We will refund you within 14 days of receiving them back. You pay the cost of return unless the item is faulty or wrongly supplied.</p>

<h3>8.3 Before dispatch</h3>
<p>You may cancel any order before it is dispatched by contacting us. If your consultation has not yet been approved, no payment will have been taken.</p>

<h2>9. Your obligations</h2>
<p>You agree to give accurate information, to keep your account details secure, to use medicines only as directed, to read the patient information leaflet, and not to supply medicines we dispense to anyone else. Medicines are prescribed for you personally and must never be given to another person.</p>

<h2>10. Reporting side effects</h2>
<p>If you experience a side effect, tell us and report it via the MHRA <a href="https://yellowcard.mhra.gov.uk/" target="_blank" rel="noopener noreferrer">Yellow Card scheme</a>. Seek medical help immediately if the reaction is severe.</p>

<h2>11. Complaints</h2>
<p>Please contact us first, at {{EMAIL}}. We acknowledge complaints within 3 working days and aim to resolve them within 20 working days. If you remain dissatisfied you may raise your concern with the General Pharmaceutical Council at <a href="https://www.pharmacyregulation.org/raise-concern" target="_blank" rel="noopener noreferrer">pharmacyregulation.org/raise-concern</a>.</p>

<h2>12. Our liability</h2>
<p>Nothing in these terms limits or excludes our liability for death or personal injury caused by our negligence, for fraud or fraudulent misrepresentation, for any breach of your statutory rights as a consumer, or for anything else that cannot lawfully be limited.</p>
<p>Subject to that, we are not liable for loss that was not foreseeable, or for any business loss. We are not liable for harm arising from information you gave us that was untrue, inaccurate or incomplete, or from your failure to use a medicine as directed.</p>
<p>The content on this website is general information, not medical advice, and must not be relied on as a substitute for advice from a healthcare professional.</p>

<h2>13. Intellectual property</h2>
<p>All content on this site belongs to us or our licensors, and may not be reproduced without permission.</p>

<h2>14. Data protection</h2>
<p>We process your personal data, including health data, as described in our <a href="/privacy-policy/">privacy policy</a>. We use cookies as described in our <a href="/cookie-policy/">cookie policy</a>.</p>

<h2>15. Changes to these terms</h2>
<p>We may amend these terms. The version in force is the one published on this page when you place your order.</p>

<h2>16. Governing law</h2>
<p>These terms are governed by the law of England and Wales, and the courts of England and Wales have exclusive jurisdiction, save that if you live in Scotland or Northern Ireland you may bring proceedings in your local courts.</p>

<h2>17. Contact</h2>
<p>{{ENTITY}}<br />{{REGISTERED}}<br />Email: {{EMAIL}}<br />Phone: {{PHONE}}<br />GPhC registration number: <strong>{{GPHC}}</strong></p>
HTML;

	$privacy = <<<'HTML'
<p><em>Last updated: {{UPDATED}}</em></p>

<p>This policy explains how Smart Pharmacy collects, uses and protects your personal information, including your health information, and what rights you have. We take this seriously: as a pharmacy, we hold some of the most sensitive data about you that exists.</p>

<h2>1. Who we are</h2>
<p>The data controller is {{ENTITY}}, a company registered in England and Wales under company number {{COMPANY_NO}}, with its registered office at {{REGISTERED}}.</p>
<p>Our GPhC registration number is <strong>{{GPHC}}</strong>. Our superintendent pharmacist is {{SUPERINTENDENT}}.</p>
<p>For any question about this policy, or to exercise your rights, contact us at {{EMAIL}}.</p>

<h2>2. What we collect</h2>
<ul>
<li><strong>Identity and contact data</strong> — name, date of birth, email, phone, delivery and billing address.</li>
<li><strong>Health data</strong> — your consultation answers, medical history, current medicines, allergies, conditions, pregnancy status, and details of the treatment you request. This is <strong>special category data</strong> under the UK GDPR and receives extra protection.</li>
<li><strong>Identity verification data</strong> — a photograph of an identity document, where we must verify your identity or age before dispensing.</li>
<li><strong>Order and transaction data</strong> — what you bought, when, delivery details, and the clinical decision made on your consultation.</li>
<li><strong>Payment data</strong> — handled by our payment provider. <strong>We never receive or store your full card number.</strong></li>
<li><strong>Technical data</strong> — IP address, browser and device information, and how you use the site. See our <a href="/cookie-policy/">cookie policy</a>.</li>
</ul>

<h2>3. Why we use it, and our lawful basis</h2>
<table>
<thead><tr><th>What we do</th><th>Lawful basis (UK GDPR)</th></tr></thead>
<tbody>
<tr><td>Take and fulfil your order, deliver your goods, manage your account</td><td>Article 6(1)(b) — performance of a contract</td></tr>
<tr><td>Assess your consultation and dispense medicines safely</td><td>Article 6(1)(b) contract, and for health data <strong>Article 9(2)(h)</strong> — provision of health care and treatment, and the management of health care services, by or under the responsibility of a health professional subject to a duty of confidentiality (with DPA 2018 Schedule 1, Part 1, paragraph 2)</td></tr>
<tr><td>Verify your identity and age</td><td>Article 6(1)(c) — legal obligation; Article 9(2)(h)</td></tr>
<tr><td>Keep dispensing and patient records, report side effects, respond to regulators</td><td>Article 6(1)(c) — legal obligation; Article 9(2)(i) — public interest in public health</td></tr>
<tr><td>Prevent fraud, secure our site, handle complaints, defend legal claims</td><td>Article 6(1)(f) — legitimate interests; Article 9(2)(f) — legal claims</td></tr>
<tr><td>Send you marketing, where you have asked us to</td><td>Article 6(1)(a) — consent, which you can withdraw at any time</td></tr>
</tbody>
</table>
<p><strong>We do not use your health data for marketing, and we never sell your data to anyone.</strong></p>

<h2>4. Who we share it with</h2>
<ul>
<li><strong>Our pharmacists and prescribers</strong>, who review your consultation. They are bound by professional duties of confidentiality.</li>
<li><strong>Delivery partners</strong>, who receive your name and address only, never your health data.</li>
<li><strong>Our payment provider</strong>, which processes your card payment.</li>
<li><strong>IT and hosting providers</strong>, acting as our processors under contract.</li>
<li><strong>Your GP or other healthcare professionals</strong>, where clinically necessary and, other than in an emergency, with your knowledge.</li>
<li><strong>Regulators and authorities</strong> — such as the GPhC, MHRA or NHS — where we are required to disclose.</li>
</ul>

<h2>5. International transfers</h2>
<p>We keep your data in the UK or the European Economic Area wherever we can. If any provider processes data outside the UK, we ensure an appropriate safeguard is in place, such as UK adequacy regulations or the International Data Transfer Agreement.</p>

<h2>6. How long we keep it</h2>
<p>We keep your data only as long as we need it, and as long as the law requires. Pharmacy records must be retained even if you ask us to delete them.</p>
<table>
<thead><tr><th>Record</th><th>Retention</th></tr></thead>
<tbody>
<tr><td>Prescription and dispensing records</td><td>At least 2 years from the date of the last supply</td></tr>
<tr><td>Consultation records and clinical decisions</td><td>Held with your patient record, in line with GPhC and NHS records management guidance</td></tr>
<tr><td>Identity verification documents</td><td>Only as long as needed to verify your identity, then securely deleted</td></tr>
<tr><td>Order and financial records</td><td>6 years, for tax purposes</td></tr>
<tr><td>Marketing preferences</td><td>Until you withdraw consent</td></tr>
</tbody>
</table>

<h2>7. Your rights</h2>
<p>Under the UK GDPR you have the right to:</p>
<ul>
<li><strong>Access</strong> your data, and receive a copy.</li>
<li><strong>Rectify</strong> data that is inaccurate or incomplete.</li>
<li><strong>Erase</strong> your data. Note that we cannot delete pharmacy records we are legally required to keep.</li>
<li><strong>Restrict</strong> or <strong>object to</strong> our processing.</li>
<li><strong>Data portability</strong> — receive your data in a portable format.</li>
<li><strong>Withdraw consent</strong> at any time, where we rely on consent.</li>
</ul>
<p>To exercise any of these, email {{EMAIL}}. We respond within one month. There is no charge unless a request is manifestly unfounded or excessive.</p>

<h2>8. Automated decision-making</h2>
<p>We do not make decisions about your treatment by automated means. Every consultation is reviewed by a human pharmacist or prescriber before any medicine is supplied.</p>

<h2>9. Security</h2>
<p>We use encryption in transit, access controls, and staff confidentiality obligations to protect your data. Identity documents are stored in restricted-access storage. No system is perfectly secure, but we take our obligations seriously and will notify you and the ICO of a breach where the law requires.</p>

<h2>10. Cookies</h2>
<p>See our <a href="/cookie-policy/">cookie policy</a>.</p>

<h2>11. Complaints</h2>
<p>Please raise any concern with us first, at {{EMAIL}}. You also have the right to complain to the Information Commissioner's Office:</p>
<p>Information Commissioner's Office, Wycliffe House, Water Lane, Wilmslow, Cheshire SK9 5AF<br />
Helpline: 0303 123 1113<br />
<a href="https://ico.org.uk/make-a-complaint/" target="_blank" rel="noopener noreferrer">ico.org.uk/make-a-complaint</a></p>

<h2>12. Changes</h2>
<p>We may update this policy. The version on this page is the one that applies, and we will tell you about significant changes.</p>
HTML;

	$cookies = <<<'HTML'
<p><em>Last updated: {{UPDATED}}</em></p>

<h2>1. What cookies are</h2>
<p>Cookies are small text files placed on your device when you visit a website. They let a site remember what you did, such as what is in your basket or whether you are logged in. We also use similar technologies such as local storage and pixels; where we say "cookies" in this policy, we mean all of them.</p>

<h2>2. The law</h2>
<p>We use cookies in line with the Privacy and Electronic Communications Regulations 2003 (PECR) and the UK GDPR. We set <strong>strictly necessary</strong> cookies automatically, because the site cannot work without them. For every other category we ask for your consent first, and you can change your mind at any time.</p>

<h2>3. The cookies we use</h2>

<h3>Strictly necessary</h3>
<p>Required for the site to function. These cannot be switched off.</p>
<table>
<thead><tr><th>Cookie</th><th>Purpose</th><th>Expires</th></tr></thead>
<tbody>
<tr><td><code>woocommerce_cart_hash</code></td><td>Tracks the contents of your basket</td><td>Session</td></tr>
<tr><td><code>woocommerce_items_in_cart</code></td><td>Tracks whether your basket has items</td><td>Session</td></tr>
<tr><td><code>wp_woocommerce_session_*</code></td><td>Links you to your basket and order data</td><td>2 days</td></tr>
<tr><td><code>wordpress_logged_in_*</code></td><td>Keeps you signed in to your account</td><td>Session</td></tr>
<tr><td><code>__stripe_mid</code>, <code>__stripe_sid</code></td><td>Set by our payment provider to process payment and prevent fraud</td><td>1 year / 30 minutes</td></tr>
</tbody>
</table>

<h3>Functional</h3>
<p>Remember choices you make, such as your preferences. Set only with your consent.</p>

<h3>Analytics</h3>
<p>Help us understand how the site is used so we can improve it. These tell us which pages are visited and where people run into problems. Set only with your consent.</p>

<h3>Marketing</h3>
<p>Used to measure and improve advertising. Set only with your consent.</p>
<p><strong>We never use your health information, your consultation answers, or the fact that you bought a particular medicine, for advertising or targeting.</strong></p>

<h2>4. Managing your cookies</h2>
<p>You can change or withdraw your consent at any time using the cookie settings link in the footer of this site.</p>
<p>You can also control cookies in your browser, and delete cookies already stored. Blocking strictly necessary cookies will stop parts of the site, such as the basket and checkout, from working. Guidance for each browser:</p>
<ul>
<li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
<li><a href="https://support.apple.com/en-gb/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Safari</a></li>
<li><a href="https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop" target="_blank" rel="noopener noreferrer">Firefox</a></li>
<li><a href="https://support.microsoft.com/en-us/microsoft-edge" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
</ul>

<h2>5. Third-party cookies</h2>
<p>Some cookies are set by third parties, such as our payment provider. We do not control those cookies, and you should read the relevant provider's own privacy and cookie notices.</p>

<h2>6. Changes</h2>
<p>We may update this policy as the cookies we use change. Please check back from time to time.</p>

<h2>7. Contact</h2>
<p>Questions about cookies? Email {{EMAIL}}. See also our <a href="/privacy-policy/">privacy policy</a>.</p>
HTML;

	return array(
		'about' => array(
			'title'   => 'About Us',
			'excerpt' => 'A UK registered online pharmacy built around one idea: nothing is dispensed until a healthcare professional is satisfied it is safe for you.',
			'content' => $about,
		),
		'nhs-prescriptions' => array(
			'title'   => 'NHS Prescriptions',
			'excerpt' => 'Free NHS prescription delivery, straight to your door.',
			'content' => $nhs,
		),
		'contact' => array(
			'title'   => 'Contact Us',
			'excerpt' => 'Speak to our pharmacy team. We are here to help with your order, your medicines, or your health.',
			'content' => $contact,
		),
		'faq' => array(
			'title'   => 'Frequently Asked Questions',
			'excerpt' => 'Answers to the questions our patients ask most.',
			'content' => $faq,
		),
		'terms-conditions' => array(
			'title'   => 'Terms and Conditions',
			'excerpt' => 'The terms that apply when you buy from Smart Pharmacy.',
			'content' => $terms,
		),
		'privacy-policy' => array(
			'title'   => 'Privacy Policy',
			'excerpt' => 'How we collect, use and protect your personal and health information.',
			'content' => $privacy,
		),
		'cookie-policy' => array(
			'title'   => 'Cookie Policy',
			'excerpt' => 'What cookies we use, why, and how to control them.',
			'content' => $cookies,
		),
	);
}

/**
 * Create the core pages if they are missing.
 */
function sp_seed_core_pages() {
	if ( '1' === (string) get_option( '_sp_core_pages_seeded_v1', '' ) ) {
		return;
	}

	$tokens = sp_core_pages_tokens();

	foreach ( sp_core_pages_definitions() as $slug => $page ) {
		// Respect deletion / an existing page at this slug.
		if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $slug,
				'post_excerpt' => $page['excerpt'],
				'post_content' => strtr( $page['content'], $tokens ),
				'post_type'    => 'page',
				'post_status'  => 'publish',
			)
		);
	}

	sp_seed_core_pages_footer_nav();

	update_option( '_sp_core_pages_seeded_v1', '1' );
}
add_action( 'init', 'sp_seed_core_pages', 12 );

/**
 * Point the footer's Quick Links and Legal repeaters at the new pages.
 *
 * Only fills them when empty — never overwrites the client's own links.
 */
function sp_seed_core_pages_footer_nav() {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	$quick = get_field( 'nav_footer_quick', 'option' );
	if ( empty( $quick ) || ! is_array( $quick ) ) {
		update_field(
			'field_sp_nav_footer_quick',
			array(
				array( 'label' => 'About Us',          'url' => '/about/' ),
				array( 'label' => 'NHS Prescriptions', 'url' => '/nhs-prescriptions/' ),
				array( 'label' => 'Treatments',        'url' => '/treatments/' ),
				array( 'label' => 'Shop',              'url' => '/shop/' ),
				array( 'label' => 'FAQ',               'url' => '/faq/' ),
				array( 'label' => 'Contact',           'url' => '/contact/' ),
			),
			'option'
		);
	}

	$legal = get_field( 'nav_footer_legal', 'option' );
	if ( empty( $legal ) || ! is_array( $legal ) ) {
		update_field(
			'field_sp_nav_footer_legal',
			array(
				array( 'label' => 'Terms and Conditions', 'url' => '/terms-conditions/' ),
				array( 'label' => 'Privacy Policy',       'url' => '/privacy-policy/' ),
				array( 'label' => 'Cookie Policy',        'url' => '/cookie-policy/' ),
			),
			'option'
		);
	}
}
