/**
 * Request body for POST marketplace/admin/settings/banks/store
 * Auto-generated from API validation errors
 */
export interface PostMarketplaceAdminSettingsBanksStoreRequest {
  /** Rules: required, invalid */
  account_number: string;
  /** Rules: required */
  bank_name: string;
  /** Rules: required */
  beneficiary_name: string;
  /** Rules: required */
  iban: string;
}
