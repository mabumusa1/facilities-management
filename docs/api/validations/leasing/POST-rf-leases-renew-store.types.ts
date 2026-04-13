/**
 * Request body for POST rf/leases/renew/store
 * Auto-generated from API validation errors
 */
export interface PostRfLeasesRenewStoreRequest {
  /** Rules: required */
  autoGenerateLeaseNumber: string;
  /** Rules: required */
  created_at: string;
  /** Rules: required */
  end_date: string;
  /** Rules: required */
  payment_schedule_id: string;
  /** Rules: required */
  rental_contract_type_id: string;
  /** Rules: required */
  rental_type: string;
  /** Rules: required */
  rf_lease_id: string;
  /** Rules: required */
  start_date: string;
  /** Rules: required */
  units: string;
  /** Rules: required */
  units.0.rental_amount: string;
  contract_number?: string;
  number_of_months?: string;
  number_of_years?: string;
}
