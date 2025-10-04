import mongoose, { Schema, Document, Types } from 'mongoose';

export type ApproverType = 'USER' | 'ROLE';

export interface IApprovalStep {
  sequence: number; // 1,2,3...
  approverType: ApproverType; // USER or ROLE
  approverRef?: Types.ObjectId; // when USER
  roleName?: 'MANAGER' | 'FINANCE' | 'DIRECTOR' | 'ADMIN'; // extensible
}

export interface IConditionalRule {
  percentage?: number; // e.g. 60 means >=60% approves
  specificApproverUserId?: Types.ObjectId; // if this user approves -> auto approve
  logic?: 'OR' | 'AND'; // combine when both set; default OR
}

export interface IApprovalFlow extends Document {
  company: Types.ObjectId;
  name: string;
  steps: IApprovalStep[];
  conditionalRule?: IConditionalRule;
  createdAt: Date;
}

const ApprovalStepSchema = new Schema<IApprovalStep>({
  sequence: { type: Number, required: true },
  approverType: { type: String, enum: ['USER', 'ROLE'], required: true },
  approverRef: { type: Schema.Types.ObjectId, ref: 'User' },
  roleName: { type: String }
}, { _id: false });

const ConditionalRuleSchema = new Schema<IConditionalRule>({
  percentage: { type: Number },
  specificApproverUserId: { type: Schema.Types.ObjectId, ref: 'User' },
  logic: { type: String, enum: ['OR', 'AND'], default: 'OR' }
}, { _id: false });

const ApprovalFlowSchema = new Schema<IApprovalFlow>({
  company: { type: Schema.Types.ObjectId, ref: 'Company', required: true },
  name: { type: String, required: true },
  steps: { type: [ApprovalStepSchema], default: [] },
  conditionalRule: { type: ConditionalRuleSchema },
  createdAt: { type: Date, default: Date.now }
});

export default mongoose.model<IApprovalFlow>('ApprovalFlow', ApprovalFlowSchema);
