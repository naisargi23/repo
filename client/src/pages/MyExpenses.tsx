import { useEffect, useState } from 'react';
import { api } from '../api';

export default function MyExpenses() {
  const [items, setItems] = useState<any[]>([]);

  useEffect(() => {
    api.get('/expenses/mine').then(({ data }) => setItems(data));
  }, []);

  return (
    <div>
      <h2>My Expenses</h2>
      <table border={1} cellPadding={6}>
        <thead>
          <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          {items.map(x => (
            <tr key={x._id}>
              <td>{new Date(x.expenseDate).toLocaleDateString()}</td>
              <td>{x.category}</td>
              <td>{x.amount} {x.currencyCode}</td>
              <td>{x.status}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
