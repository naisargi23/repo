import express from 'express';
import multer from 'multer';
import path from 'path';
import { requireAuth, AuthedRequest } from '../middleware/auth.js';
import Expense from '../models/Expense.js';
import ApprovalFlow from '../models/ApprovalFlow.js';
import Company from '../models/Company.js';
import { performOCR, parseReceiptText } from '../utils/ocr.js';
import { getNextApprovers, evaluateConditional } from '../services/approvalEngine.js';

const router = express.Router();

const uploadDir = path.join(process.cwd(), 'server', process.env.UPLOAD_DIR || 'uploads');
const storage = multer.diskStorage({
  destination: (_req, _file, cb) => cb(null, uploadDir),
  filename: (_req, file, cb) => cb(null, `${Date.now()}-${file.originalname}`)
});
const upload = multer({ storage });

router.post('/', requireAuth, upload.single('receipt'), async (req: AuthedRequest, res) => {
  try {
    const { amount, currencyCode, category, description, expenseDate, approvalFlowId } = req.body;

    let receiptUrl: string | undefined;
    let parsed: any = {};
    if (req.file) {
      receiptUrl = `/uploads/${req.file.filename}`;
      const text = await performOCR(req.file.path);
      parsed = parseReceiptText(text);
    }

    const expense = await Expense.create({
      company: req.user!.companyId,
      employee: req.user!.id,
      amount: amount ? Number(amount) : parsed.amount,
      currencyCode: currencyCode || 'USD',
      category: category || 'GENERAL',
      description: description || parsed.description,
      expenseDate: expenseDate ? new Date(expenseDate) : parsed.date || new Date(),
      receiptUrl,
      approvalFlow: approvalFlowId || undefined,
      currentStep: 0,
      status: 'PENDING'
    });

    res.json(expense);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Failed to submit expense' });
  }
});

router.get('/mine', requireAuth, async (req: AuthedRequest, res) => {
  const expenses = await Expense.find({ employee: req.user!.id }).sort({ createdAt: -1 });
  res.json(expenses);
});

router.get('/pending-approvals', requireAuth, async (req: AuthedRequest, res) => {
  // find expenses where current user is a next approver
  const expenses = await Expense.find({ company: req.user!.companyId, status: 'PENDING' });
  const results: any[] = [];
  for (const exp of expenses) {
    const next = await getNextApprovers(exp);
    if (next.some(id => id.toString() === req.user!.id)) {
      results.push(exp);
    }
  }
  res.json(results);
});

router.get('/company', requireAuth, async (req: AuthedRequest, res) => {
  const expenses = await Expense.find({ company: req.user!.companyId }).sort({ createdAt: -1 });
  res.json(expenses);
});

export default router;
