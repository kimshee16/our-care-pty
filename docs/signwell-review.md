# SignWell Review for Our Care

Reviewed: 2026-09-15

## Short Recommendation

SignWell is worth shortlisting for Our Care if the immediate need is affordable electronic signing for care onboarding, worker agreements, client service agreements, consent forms, and finalization paperwork. It looks especially strong for a small or growing team because paid plans include unlimited signing requests, recipients do not need SignWell accounts, templates are supported, and the API is usable from the current Laravel stack.

My recommendation is: run a controlled trial with non-production paperwork first, then move forward only after confirming Australian privacy and health-data requirements, vendor contract terms, and whether SignWell will provide the compliance documents Our Care needs.

## Fit for Our Care

Good fit:

- Worker onboarding documents, acknowledgements, declarations, and agreement forms.
- Client service agreements and consent forms.
- Finalization workflow after an applicant is marked as hired.
- Admin-driven sending from the current dashboard.
- Template-based documents where Our Care can prefill names, job titles, dates, client details, and worker details.
- API/webhook automation so the app can show `sent`, `viewed`, `signed`, `declined`, or `completed` status.

Less certain:

- Documents containing NDIS participant health or sensitive personal information.
- Any process requiring Australia-specific data residency.
- Workflows requiring advanced identity verification beyond email/SMS/passcodes.
- Heavy document management, contract negotiation, redlining, or complex approvals.

## Product Notes

SignWell is an electronic signature platform from Docsketch, LLC, DBA SignWell. Its public materials say it supports legally binding e-signatures, audit trails, templates, document workflows, bulk send, integrations, and an API. Recipients can sign from desktop or mobile without creating an account.

Current public pricing, as listed by SignWell:

- Free: 1 sender, 1 template, 3 documents per month.
- Light: USD $12 per month when paid monthly, 1 sender, 5 templates per sender, unlimited documents.
- Business: USD $36 per month when paid monthly, 3 senders, unlimited templates and documents.
- Enterprise: custom pricing for large teams, high volume, API, and advanced compliance.
- Annual plans are advertised as 20% cheaper than monthly billing.

API pricing:

- SignWell says API access is included and supports embedded signing, embedded requesting, embedded template editing, webhooks, SDKs, CLI, and test mode.
- Paid plans include a small number of free API documents monthly, with API pricing described as usage-based beyond the included allowance.
- Published API pricing says usage starts at USD $0.85 per API document after free monthly usage, with volume discounts.
- The API base URL is `https://www.signwell.com/api/v1` and uses an `X-Api-Key` header.
- Rate limits are listed as 100 requests per 60 seconds for most requests, 20 requests per minute in test mode, and 30 requests per minute for document/template creation.

## Security and Compliance

Strong points:

- SignWell publicly says it is SOC 2 Type II compliant.
- It advertises HIPAA support and availability of a signed BAA.
- It says documents are encrypted in transit with TLS 1.2+ and at rest with AES-256.
- It says each completed document includes a tamper-evident audit trail with timestamps, IP addresses, and signer activity.
- It supports GDPR, ESIGN, UETA, and eIDAS according to its public pages.
- It publishes a status page and, on 2026-09-15, that page showed all systems operational with no recent incidents listed for the visible period.

Important caution for Our Care:

Our Care appears to be an Australian care/NDIS-related business. Australian health and care records can contain sensitive personal information. SignWell is a US company and its privacy policy says personal information may be transferred to or processed in the United States, Canada, and other jurisdictions through cloud hosting, subprocessors, analytics, advertising, support, messaging, and SMS providers.

That does not automatically rule it out, but it means Our Care should not upload live participant or worker sensitive documents until these are checked:

- Whether SignWell's terms, privacy policy, and any DPA/BAA-style agreement are acceptable for Australian Privacy Act obligations.
- Whether overseas disclosure requirements are covered.
- Whether Our Care needs Australian data residency for any document category.
- Whether templates can avoid collecting unnecessary health or sensitive details.
- Whether SignWell will provide its latest SOC 2 report, BAA or equivalent agreement, subprocessor details, data retention details, and breach notification commitments.

## Legal Validity

SignWell says its signatures comply with ESIGN, UETA, and eIDAS. For Australia, electronic signatures are generally recognised under the Electronic Transactions Act framework, but the validity depends on the type of document, consent to use electronic communication, signer identification, intent to sign, and any document-specific exceptions.

Practical stance: electronic signing should be fine for many operational forms and agreements, but Our Care should get legal confirmation before using it for any high-risk document, statutory document, medical consent edge case, guardianship-related document, or document that may require witnessing/notarisation.

## User Experience

Likely strengths:

- Simple signing flow.
- Mobile-friendly.
- No recipient account required.
- Reusable templates.
- Automatic reminders and notifications.
- Custom branding on higher tiers.
- Good public user-review signals: SignWell claims 4.9/5 on Capterra and 4.8/5 on G2 on its healthcare page, and independent listing snippets currently show high ratings.

Likely limitations:

- Less enterprise-heavy than DocuSign or Adobe Acrobat Sign.
- May not be ideal if Our Care wants full contract lifecycle management, negotiation/redline workflows, complex conditional approvals, or a local Australian vendor.
- Compliance claims need document-level due diligence rather than relying on marketing pages.

## How It Would Fit the Current Laravel App

The current app has a natural integration point in the finalization flow:

- `app/Http/Controllers/ClientController.php`
- `resources/views/client-finalization.blade.php`
- `app/Models/Endorsement.php`
- `routes/web.php`

Current behavior: when an admin/client marks an interview as hired, the app updates the application status, creates or updates an endorsement, and sends a congratulations email.

Suggested first integration:

1. Add a `signwell_documents` table linked to `applications`, `endorsements`, `clients`, and `users`.
2. Store SignWell document ID, template ID, recipient email, signing URL if embedded, status, sent timestamp, completed timestamp, completed PDF path, and webhook payload metadata.
3. Add a `SignWellService` using Laravel's HTTP client with `SIGNWELL_API_KEY` in `.env`.
4. Add admin settings for template IDs, starting with:
   - worker agreement template
   - client service agreement template
   - consent/acknowledgement template
5. After `hireApplicant()`, create a SignWell document from template in draft or sent mode.
6. Add a webhook route such as `/webhooks/signwell` to update local status when SignWell sends document events.
7. Download completed PDFs to private storage, not public storage.
8. Show signature status in Finalization and Endorsed Workers.

Suggested pilot workflow:

- Phase 1: manual SignWell use outside the app for 3-5 sample forms.
- Phase 2: app button that sends one template for signature after hiring.
- Phase 3: webhooks and completed PDF storage.
- Phase 4: embedded signing inside the Our Care portal, only after security review.

## Decision

Decision: shortlist, but do not fully commit yet.

Why: SignWell seems affordable, simple, and API-friendly. The product capabilities line up well with Our Care's current admin and finalization workflows. The main unresolved issue is not product usability; it is whether the legal/privacy/compliance contract is good enough for Australian care-sector data.

## Due Diligence Checklist

Before using live data, ask SignWell for:

- Latest SOC 2 Type II report.
- DPA or equivalent privacy/data-processing agreement suitable for Australian customers.
- BAA details if any US HIPAA-style protected health information is involved.
- Full subprocessor list and change-notification process.
- Data hosting regions and whether Australia data residency is available.
- Retention/deletion controls for completed documents.
- Breach notification timelines.
- Whether audit logs and completed PDFs can be exported in bulk.
- Whether signing links expire and whether passcodes/OTP can be required.
- Accessibility statement for signers with disability or assistive technology needs.

## Sources Checked

- SignWell homepage: https://www.signwell.com/
- SignWell pricing: https://www.signwell.com/pricing/
- SignWell API pricing: https://www.signwell.com/api-pricing/
- SignWell security: https://www.signwell.com/security/
- SignWell healthcare page: https://www.signwell.com/industries/healthcare/
- SignWell API docs: https://developers.signwell.com/reference/getting-started-with-your-api-1
- SignWell API index: https://developers.signwell.com/llms.txt
- SignWell privacy policy: https://www.signwell.com/privacy/
- SignWell terms: https://www.signwell.com/terms/
- SignWell status page: https://status.signwell.com/
- Australian Attorney-General's Department electronic transactions materials: https://www.ag.gov.au/
- OAIC personal/sensitive information guidance: https://www.oaic.gov.au/privacy/your-privacy-rights/your-personal-information/what-is-personal-information
- OAIC health privacy guidance: https://www.oaic.gov.au/privacy/privacy-guidance-for-organisations-and-government-agencies/health-service-providers/guide-to-health-privacy/introduction-and-key-concepts
- OAIC securing personal information guidance: https://www.oaic.gov.au/privacy/privacy-guidance-for-organisations-and-government-agencies/handling-personal-information/guide-to-securing-personal-information
