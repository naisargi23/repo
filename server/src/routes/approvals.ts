import express from 'express';
import { requireAuth, AuthedRequest } from '../middleware/auth.js';
import Expense from '../models/Expense.js';
import ApprovalFlow from '../models/ApprovalFlow.js';
import { getNextApprovers, evaluateConditional } from '../services/approvalEngine.js';

const router = express.Router();

router.post('/:expenseId/decide', requireAuth, async (req: AuthedRequest, res) => {
  const { approved, comment } = req.body as { approved: boolean; comment?: string };
  const expense = await Expense.findById(req.params.expenseId);
  if (!expense) return res.status(404).json({ error: 'Expense not found' });
  if (expense.status !== 'PENDING') return res.status(400).json({ error: 'Already decided' });

  const next = await getNextApprovers(expense);
  const canDecide = next.some(id => id.toString() === req.user!.id);
  if (!canDecide) return res.status(403).json({ error: 'Not an approver for this step' });

  expense.approversDecisions.push({ approver: req.user!.id as any, approved, comment, decidedAt: new Date() });

  // If rejected by any approver, mark rejected immediately
  if (!approved) {
    expense.status = 'REJECTED';
    await expense.save();
    return res.json(expense);
  }

  // Evaluate conditional rules
  const flow = expense.approvalFlow ? await ApprovalFlow.findById(expense.approvalFlow) : null;
  const conditionalStatus = evaluateConditional(flow, expense.approversDecisions);
  if (conditionalStatus === 'APPROVED') {
    expense.status = 'APPROVED';
    await expense.save();
    return res.json(expense);
  }

  // Move to next step if all current step approvers approved
  expense.currentStep += 1;
  const nextApprovers = await getNextApprovers(expense);
  if (nextApprovers.length === 0) {
    expense.status = 'APPROVED';
  }
  await expense.save();
  res.json(expense);
});

export default router;
