/**
 * Request body for POST rf/requests/sub-categories
 * Auto-generated from API validation errors
 */
export interface PostRfRequestsSub_categoriesRequest {
  /** Rules: required */
  is_all_day: string;
  /** Rules: required */
  name_ar: string;
  /** Rules: required */
  name_en: string;
  /** Rules: required */
  rf_category_id: string;
  /** Rules: required */
  terms_and_conditions: string;
  icon?: string;
}
