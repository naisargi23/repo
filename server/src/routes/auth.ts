import express from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import User from '../models/User.js';
import Company from '../models/Company.js';
import axios from 'axios';

const router = express.Router();

router.post('/signup', async (req, res) => {
  try {
    const { email, password, name, companyName, countryName } = req.body;
    const existing = await User.findOne({ email });
    if (existing) return res.status(400).json({ error: 'Email already in use' });

    // Determine currency via restcountries
    let currencyCode = 'USD';
    if (countryName) {
      const url = process.env.RESTCOUNTRIES_URL || 'https://restcountries.com/v3.1/all?fields=name,currencies';
      const { data } = await axios.get(url);
      const match = data.find((c: any) => c.name?.common?.toLowerCase() === String(countryName).toLowerCase());
      if (match && match.currencies) {
        currencyCode = Object.keys(match.currencies)[0];
      }
    }

    const company = await Company.create({ name: companyName || `${name}'s Company`, country: countryName || 'Unknown', currencyCode });
    const passwordHash = await bcrypt.hash(password, 10);
    const user = await User.create({ email, passwordHash, name, company: company._id, roles: ['ADMIN'] });

    const token = jwt.sign({ id: user._id.toString(), companyId: company._id.toString(), roles: user.roles }, process.env.JWT_SECRET || 'dev', { expiresIn: '7d' });
    res.json({ token, user: { id: user._id, email: user.email, name: user.name, roles: user.roles }, company });
  } catch (err: any) {
    console.error(err);
    res.status(500).json({ error: 'Signup failed' });
  }
});

router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    const user = await User.findOne({ email });
    if (!user) return res.status(400).json({ error: 'Invalid credentials' });
    const ok = await bcrypt.compare(password, user.passwordHash);
    if (!ok) return res.status(400).json({ error: 'Invalid credentials' });
    const token = jwt.sign({ id: user._id.toString(), companyId: user.company.toString(), roles: user.roles }, process.env.JWT_SECRET || 'dev', { expiresIn: '7d' });
    const company = await Company.findById(user.company);
    res.json({ token, user: { id: user._id, email: user.email, name: user.name, roles: user.roles, manager: user.manager, isManagerApprover: user.isManagerApprover }, company });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Login failed' });
  }
});

export default router;
