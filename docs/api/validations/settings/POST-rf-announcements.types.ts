/**
 * Request body for POST rf/announcements
 * Auto-generated from API validation errors
 */
export interface PostRfAnnouncementsRequest {
  /** Rules: required */
  description: string;
  /** Rules: required */
  end_date: string;
  /** Rules: required */
  end_time: string;
  /** Rules: required */
  is_visible: string;
  /** Rules: required */
  notify_user_type: string;
  /** Rules: required */
  start_date: string;
  /** Rules: required */
  start_time: string;
  /** Rules: required */
  title: string;
}
