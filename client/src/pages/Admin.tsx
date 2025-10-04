import { useEffect, useState } from 'react';
import { api } from '../api';

export default function Admin() {
  const [users, setUsers] = useState<any[]>([]);
  const [flows, setFlows] = useState<any[]>([]);
  const [newUser, setNewUser] = useState({ email: '', name: '', roles: 'EMPLOYEE', managerId: '', isManagerApprover: true });
  const [newFlow, setNewFlow] = useState({ name: '', stepsJson: '[{"sequence":1,"approverType":"ROLE","roleName":"MANAGER"}]', conditionalJson: '{"percentage":60}' });

  function refresh() {
    api.get('/users').then(({ data }) => setUsers(data));
    api.get('/config/flows').then(({ data }) => setFlows(data));
  }

  useEffect(() => { refresh(); }, []);

  async function createUser() {
    await api.post('/users', { ...newUser, roles: newUser.roles.split(',').map(s => s.trim()) });
    refresh();
  }

  async function createFlow() {
    const steps = JSON.parse(newFlow.stepsJson);
    const conditionalRule = JSON.parse(newFlow.conditionalJson);
    await api.post('/config/flows', { name: newFlow.name, steps, conditionalRule });
    refresh();
  }

  return (
    <div style={{ display: 'grid', gap: 16 }}>
      <section>
        <h3>Users</h3>
        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
          <input placeholder="Email" value={newUser.email} onChange={e => setNewUser({ ...newUser, email: e.target.value })} />
          <input placeholder="Name" value={newUser.name} onChange={e => setNewUser({ ...newUser, name: e.target.value })} />
          <input placeholder="Roles (comma)" value={newUser.roles} onChange={e => setNewUser({ ...newUser, roles: e.target.value })} />
          <input placeholder="ManagerId" value={newUser.managerId} onChange={e => setNewUser({ ...newUser, managerId: e.target.value })} />
          <label>
            Manager is first approver
            <input type="checkbox" checked={newUser.isManagerApprover} onChange={e => setNewUser({ ...newUser, isManagerApprover: e.target.checked })} />
          </label>
          <button onClick={createUser}>Create User</button>
        </div>
        <ul>
          {users.map(u => <li key={u._id}>{u.name} - {u.email} - {u.roles?.join(',')}</li>)}
        </ul>
      </section>

      <section>
        <h3>Approval Flows</h3>
        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
          <input placeholder="Flow Name" value={newFlow.name} onChange={e => setNewFlow({ ...newFlow, name: e.target.value })} />
          <input placeholder="Steps JSON" value={newFlow.stepsJson} onChange={e => setNewFlow({ ...newFlow, stepsJson: e.target.value })} style={{ width: 420 }} />
          <input placeholder="Conditional Rule JSON" value={newFlow.conditionalJson} onChange={e => setNewFlow({ ...newFlow, conditionalJson: e.target.value })} style={{ width: 420 }} />
          <button onClick={createFlow}>Create Flow</button>
        </div>
        <ul>
          {flows.map(f => <li key={f._id}>{f.name} - steps: {f.steps?.length}</li>)}
        </ul>
      </section>
    </div>
  );
}
