/**
 * Request body for POST marketplace/admin/settings/sales/store
 * Auto-generated from API validation errors
 */
export interface PostMarketplaceAdminSettingsSalesStoreRequest {
  /** Rules: required */
  bank_contract_signing_days: string;
  /** Rules: required */
  cash_contract_signing_days: string;
  /** Rules: required */
  deposit_time_limit_days: string;
}
