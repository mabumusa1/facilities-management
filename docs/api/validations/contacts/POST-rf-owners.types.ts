/**
 * Request body for POST rf/owners
 * Auto-generated from API validation errors
 */
export interface PostRfOwnersRequest {
  /** Rules: required */
  first_name: string;
  /** Rules: invalid, required */
  last_name: string;
  /** Rules: required */
  phone_country_code: string;
  /** Rules: required */
  phone_number: string;
}
