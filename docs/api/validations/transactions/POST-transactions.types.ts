/**
 * Request body for POST transactions
 * Auto-generated from API validation errors
 */
export interface PostTransactionsRequest {
  /** Rules: required */
  amount: string;
  /** Rules: required */
  category: string;
  /** Rules: required */
  due_on: string;
  /** Rules: required */
  unit.id: string;
  /** Rules: invalid */
  assignee.id?: string;
  /** Rules: boolean */
  type?: boolean;
}
