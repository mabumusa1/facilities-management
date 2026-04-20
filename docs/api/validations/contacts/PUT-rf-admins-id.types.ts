/**
 * Request body for PUT rf/admins/{id}
 * Auto-generated from API validation errors
 */
export interface PutRfAdminsRequest {
  /** Rules: required */
  phone_country_code: string;
  /** Rules: required */
  phone_number: string;
  /** Rules: required */
  role: string;
  /** Rules: invalid */
  last_name?: string;
}
