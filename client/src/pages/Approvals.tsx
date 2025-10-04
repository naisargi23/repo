import { useEffect, useState } from 'react';
import { api } from '../api';

export default function Approvals() {
  const [items, setItems] = useState<any[]>([]);

  function refresh() {
    api.get('/expenses/pending-approvals').then(({ data }) => setItems(data));
  }

  useEffect(() => { refresh(); }, []);

  async function decide(id: string, approved: boolean) {
    await api.post(`/approvals/${id}/decide`, { approved });
    refresh();
  }

  return (
    <div>
      <h2>Pending Approvals</h2>
      {items.length === 0 && <div>No pending approvals</div>}
      {items.map(x => (
        <div key={x._id} style={{ border: '1px solid #ddd', padding: 12, marginBottom: 8 }}>
          <div><b>Employee</b>: {x.employee}</div>
          <div><b>Category</b>: {x.category}</div>
          <div><b>Amount</b>: {x.amount} {x.currencyCode}</div>
          <div><b>Date</b>: {new Date(x.expenseDate).toLocaleDateString()}</div>
          <button onClick={() => decide(x._id, true)}>Approve</button>
          <button onClick={() => decide(x._id, false)}>Reject</button>
        </div>
      ))}
    </div>
  );
}
