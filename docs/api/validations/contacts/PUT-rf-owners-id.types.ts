/**
 * Request body for PUT rf/owners/{id}
 * Auto-generated from API validation errors
 */
export interface PutRfOwnersRequest {
  /** Rules: required */
  phone_country_code: string;
  /** Rules: required */
  phone_number: string;
  /** Rules: invalid */
  last_name?: string;
}
