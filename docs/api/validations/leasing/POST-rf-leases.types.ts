/**
 * Request body for POST rf/leases
 * Auto-generated from API validation errors
 */
export interface PostRfLeasesRequest {
  /** Rules: required */
  autoGenerateLeaseNumber: string;
  /** Rules: required */
  created_at: string;
  /** Rules: required */
  end_date: string;
  /** Rules: required */
  handover_date: string;
  /** Rules: required */
  lease_unit_type: string;
  /** Rules: required */
  payment_schedule_id: string;
  /** Rules: required */
  rental_contract_type_id: string;
  /** Rules: required, invalid */
  rental_type: string;
  /** Rules: required */
  start_date: string;
  /** Rules: required */
  tenant: string;
  /** Rules: required */
  tenant.national_id: string;
  /** Rules: required */
  tenant_type: string;
  /** Rules: required */
  units: string;
  /** Rules: required */
  units.0.amount_type: string;
  /** Rules: required */
  units.0.rental_amount: string;
  contract_number?: string;
  number_of_months?: string;
  number_of_years?: string;
}
