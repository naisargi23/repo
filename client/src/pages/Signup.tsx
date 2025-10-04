import { useState } from 'react';
import { api } from '../api';

export default function Signup({ onLogin }: { onLogin: (t: string, u: any, c: any) => void }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [name, setName] = useState('');
  const [companyName, setCompanyName] = useState('');
  const [countryName, setCountryName] = useState('');
  const [error, setError] = useState('');

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setError('');
    try {
      const { data } = await api.post('/auth/signup', { email, password, name, companyName, countryName });
      onLogin(data.token, data.user, data.company);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Signup failed');
    }
  }

  return (
    <form onSubmit={submit} style={{ maxWidth: 420, display: 'grid', gap: 8 }}>
      <h2>Sign up</h2>
      {error && <div style={{ color: 'red' }}>{error}</div>}
      <input placeholder="Name" value={name} onChange={e => setName(e.target.value)} />
      <input placeholder="Email" value={email} onChange={e => setEmail(e.target.value)} />
      <input placeholder="Company Name" value={companyName} onChange={e => setCompanyName(e.target.value)} />
      <input placeholder="Country Name (e.g., India)" value={countryName} onChange={e => setCountryName(e.target.value)} />
      <input placeholder="Password" type="password" value={password} onChange={e => setPassword(e.target.value)} />
      <button type="submit">Create company and admin</button>
    </form>
  );
}
