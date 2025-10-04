export default function Dashboard() {
  const company = JSON.parse(localStorage.getItem('company') || 'null');
  const user = JSON.parse(localStorage.getItem('user') || 'null');
  return (
    <div>
      <h2>Welcome, {user?.name}</h2>
      <div>Company: {company?.name} ({company?.currencyCode})</div>
    </div>
  );
}
