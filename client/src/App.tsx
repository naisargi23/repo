import { Routes, Route, Navigate, Link } from 'react-router-dom';
import { useEffect, useState } from 'react';
import Login from './pages/Login';
import Signup from './pages/Signup';
import Dashboard from './pages/Dashboard';
import SubmitExpense from './pages/SubmitExpense';
import MyExpenses from './pages/MyExpenses';
import Approvals from './pages/Approvals';
import Admin from './pages/Admin';

function useAuth() {
  const [token, setToken] = useState<string | null>(() => localStorage.getItem('token'));
  const [user, setUser] = useState<any>(() => JSON.parse(localStorage.getItem('user') || 'null'));
  const [company, setCompany] = useState<any>(() => JSON.parse(localStorage.getItem('company') || 'null'));
  const login = (t: string, u: any, c: any) => {
    setToken(t); setUser(u); setCompany(c);
    localStorage.setItem('token', t);
    localStorage.setItem('user', JSON.stringify(u));
    localStorage.setItem('company', JSON.stringify(c));
  };
  const logout = () => { setToken(null); setUser(null); setCompany(null); localStorage.clear(); };
  return { token, user, company, login, logout };
}

export default function App() {
  const auth = useAuth();
  const isAuthed = !!auth.token;
  const isAdmin = !!auth.user?.roles?.includes('ADMIN');

  return (
    <div style={{ fontFamily: 'system-ui, Arial', padding: 16 }}>
      <nav style={{ display: 'flex', gap: 12, marginBottom: 16 }}>
        <Link to="/">Home</Link>
        {isAuthed && (<>
          <Link to="/submit">Submit Expense</Link>
          <Link to="/mine">My Expenses</Link>
          <Link to="/approvals">Approvals</Link>
          {isAdmin && <Link to="/admin">Admin</Link>}
          <button onClick={auth.logout}>Logout</button>
        </>)}
        {!isAuthed && (<>
          <Link to="/login">Login</Link>
          <Link to="/signup">Signup</Link>
        </>)}
      </nav>
      <Routes>
        <Route path="/" element={isAuthed ? <Dashboard /> : <Navigate to="/login" />} />
        <Route path="/login" element={<Login onLogin={auth.login} />} />
        <Route path="/signup" element={<Signup onLogin={auth.login} />} />
        <Route path="/submit" element={isAuthed ? <SubmitExpense /> : <Navigate to="/login" />} />
        <Route path="/mine" element={isAuthed ? <MyExpenses /> : <Navigate to="/login" />} />
        <Route path="/approvals" element={isAuthed ? <Approvals /> : <Navigate to="/login" />} />
        <Route path="/admin" element={isAuthed && isAdmin ? <Admin /> : <Navigate to="/" />} />
      </Routes>
    </div>
  );
}
