import mongoose, { Schema, Document, Types } from 'mongoose';

export type ExpenseStatus = 'PENDING' | 'APPROVED' | 'REJECTED';

export interface IApprovalDecision {
  approver: Types.ObjectId; // User
  approved: boolean;
  comment?: string;
  decidedAt: Date;
}

export interface IExpense extends Document {
  company: Types.ObjectId;
  employee: Types.ObjectId;
  amount: number; // in original currency
  currencyCode: string; // original currency submitted
  category: string;
  description?: string;
  expenseDate: Date;
  receiptUrl?: string;
  status: ExpenseStatus;
  approvalFlow?: Types.ObjectId; // optional link to flow used
  currentStep: number; // 0 means before steps start, increments on approvals
  approversDecisions: IApprovalDecision[];
  createdAt: Date;
}

const ApprovalDecisionSchema = new Schema<IApprovalDecision>({
  approver: { type: Schema.Types.ObjectId, ref: 'User', required: true },
  approved: { type: Boolean, required: true },
  comment: { type: String },
  decidedAt: { type: Date, default: Date.now }
}, { _id: false });

const ExpenseSchema = new Schema<IExpense>({
  company: { type: Schema.Types.ObjectId, ref: 'Company', required: true },
  employee: { type: Schema.Types.ObjectId, ref: 'User', required: true },
  amount: { type: Number, required: true },
  currencyCode: { type: String, required: true },
  category: { type: String, required: true },
  description: { type: String },
  expenseDate: { type: Date, required: true },
  receiptUrl: { type: String },
  status: { type: String, enum: ['PENDING', 'APPROVED', 'REJECTED'], default: 'PENDING' },
  approvalFlow: { type: Schema.Types.ObjectId, ref: 'ApprovalFlow' },
  currentStep: { type: Number, default: 0 },
  approversDecisions: { type: [ApprovalDecisionSchema], default: [] },
  createdAt: { type: Date, default: Date.now }
});

export default mongoose.model<IExpense>('Expense', ExpenseSchema);
