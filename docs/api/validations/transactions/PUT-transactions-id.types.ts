/**
 * Request body for PUT transactions/{id}
 * Auto-generated from API validation errors
 */
export interface PutTransactionsRequest {
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
}
