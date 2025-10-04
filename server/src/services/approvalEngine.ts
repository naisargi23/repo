import { Types } from 'mongoose';
import Expense, { IExpense } from '../models/Expense.js';
import User from '../models/User.js';
import ApprovalFlow, { IApprovalFlow } from '../models/ApprovalFlow.js';

export async function getNextApprovers(expense: IExpense): Promise<Types.ObjectId[]> {
  // Determine approvers based on step and flow
  if (!expense.approvalFlow) {
    // default simple: manager first if employee set isManagerApprover
    const employee = await User.findById(expense.employee);
    if (employee?.isManagerApprover && employee.manager) {
      return [employee.manager as Types.ObjectId];
    }
    return [];
  }
  const flow = await ApprovalFlow.findById(expense.approvalFlow);
  if (!flow) return [];
  const step = flow.steps.find(s => s.sequence === expense.currentStep + 1);
  if (!step) return [];
  if (step.approverType === 'USER' && step.approverRef) {
    return [step.approverRef];
  }
  if (step.approverType === 'ROLE' && step.roleName) {
    if (step.roleName === 'MANAGER') {
      const employee = await User.findById(expense.employee);
      return employee?.manager ? [employee.manager as Types.ObjectId] : [];
    }
    const users = await User.find({ company: expense.company, roles: step.roleName });
    return users.map(u => u._id as Types.ObjectId);
  }
  return [];
}

export function evaluateConditional(flow: IApprovalFlow | null, decisions: IExpense['approversDecisions']): 'PENDING' | 'APPROVED' | 'REJECTED' {
  if (!flow || !flow.conditionalRule) return 'PENDING';
  const { percentage, specificApproverUserId, logic = 'OR' } = flow.conditionalRule;
  const total = decisions.length;
  const approvals = decisions.filter(d => d.approved).length;
  const percentOk = percentage ? (total > 0 && (approvals / total) * 100 >= percentage) : false;
  const specificOk = specificApproverUserId ? decisions.some(d => d.approver.equals(specificApproverUserId) && d.approved) : false;
  const approved = logic === 'AND' ? (percentOk && specificOk) : (percentOk || specificOk);
  return approved ? 'APPROVED' : 'PENDING';
}
