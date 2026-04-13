/**
 * Request body for PUT rf/requests/types/{id}
 * Auto-generated from API validation errors
 */
export interface PutRfRequestsTypesRequest {
  /** Rules: required */
  fee_type: string;
  /** Rules: required */
  name_ar: string;
  /** Rules: required */
  name_en: string;
  /** Rules: required */
  rf_sub_category_id: string;
  icon?: string;
}
