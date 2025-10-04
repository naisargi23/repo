import express from 'express';
import { requireAuth, requireRoles, AuthedRequest } from '../middleware/auth.js';
import ApprovalFlow from '../models/ApprovalFlow.js';

const router = express.Router();

router.get('/flows', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const flows = await ApprovalFlow.find({ company: req.user!.companyId });
  res.json(flows);
});

router.post('/flows', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const { name, steps, conditionalRule } = req.body;
  const flow = await ApprovalFlow.create({ name, steps, conditionalRule, company: req.user!.companyId });
  res.json(flow);
});

router.put('/flows/:id', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const { name, steps, conditionalRule } = req.body;
  const flow = await ApprovalFlow.findOneAndUpdate({ _id: req.params.id, company: req.user!.companyId }, { name, steps, conditionalRule }, { new: true });
  res.json(flow);
});

export default router;
