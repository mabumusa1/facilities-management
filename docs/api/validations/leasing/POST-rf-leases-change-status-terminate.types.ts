/**
 * Request body for POST rf/leases/change-status/terminate
 * Auto-generated from API validation errors
 */
export interface PostRfLeasesChange_statusTerminateRequest {
  /** Rules: required */
  end_at: string;
  /** Rules: required */
  rf_lease_id: string;
}
