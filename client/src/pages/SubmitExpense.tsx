import { useState } from 'react';
import { api } from '../api';

export default function SubmitExpense() {
  const [amount, setAmount] = useState('');
  const [currencyCode, setCurrencyCode] = useState('USD');
  const [category, setCategory] = useState('GENERAL');
  const [description, setDescription] = useState('');
  const [expenseDate, setExpenseDate] = useState('');
  const [receipt, setReceipt] = useState<File | null>(null);
  const [message, setMessage] = useState('');

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    const form = new FormData();
    if (amount) form.append('amount', amount);
    form.append('currencyCode', currencyCode);
    form.append('category', category);
    if (description) form.append('description', description);
    if (expenseDate) form.append('expenseDate', expenseDate);
    if (receipt) form.append('receipt', receipt);
    try {
      const { data } = await api.post('/expenses', form, { headers: { 'Content-Type': 'multipart/form-data' } });
      setMessage(`Submitted expense ${data._id}`);
    } catch (err: any) {
      setMessage(err.response?.data?.error || 'Failed to submit');
    }
  }

  return (
    <form onSubmit={submit} style={{ maxWidth: 480, display: 'grid', gap: 8 }}>
      <h2>Submit Expense</h2>
      {message && <div>{message}</div>}
      <input placeholder="Amount" value={amount} onChange={e => setAmount(e.target.value)} />
      <input placeholder="Currency (e.g., USD, INR)" value={currencyCode} onChange={e => setCurrencyCode(e.target.value.toUpperCase())} />
      <input placeholder="Category" value={category} onChange={e => setCategory(e.target.value)} />
      <input placeholder="Description" value={description} onChange={e => setDescription(e.target.value)} />
      <input type="date" value={expenseDate} onChange={e => setExpenseDate(e.target.value)} />
      <input type="file" onChange={e => setReceipt(e.target.files?.[0] || null)} />
      <button type="submit">Submit</button>
    </form>
  );
}
