import express from 'express';
import { requireAuth, requireRoles, AuthedRequest } from '../middleware/auth.js';
import User from '../models/User.js';

const router = express.Router();

router.get('/', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const users = await User.find({ company: req.user!.companyId }).select('-passwordHash');
  res.json(users);
});

router.post('/', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const { email, name, roles, managerId, isManagerApprover } = req.body;
  const existing = await User.findOne({ email });
  if (existing) return res.status(400).json({ error: 'Email already exists' });
  const passwordHash = await (await import('bcryptjs')).default.hash('ChangeMe123!', 10);
  const user = await User.create({ email, name, roles, manager: managerId || null, isManagerApprover: !!isManagerApprover, passwordHash, company: req.user!.companyId });
  res.json({ id: user._id, email: user.email, name: user.name, roles: user.roles });
});

router.put('/:id', requireAuth, requireRoles(['ADMIN']), async (req: AuthedRequest, res) => {
  const { name, roles, managerId, isManagerApprover } = req.body;
  const updated = await User.findOneAndUpdate({ _id: req.params.id, company: req.user!.companyId }, { name, roles, manager: managerId || null, isManagerApprover: !!isManagerApprover }, { new: true });
  res.json(updated);
});

export default router;
