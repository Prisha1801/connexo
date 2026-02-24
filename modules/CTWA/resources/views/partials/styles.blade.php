{{-- CTWA Design System: light dashboard (CTWA) --}}
<style>
:root {
  --ctwa-primary: #00a884;
  --ctwa-primary-dark: #058568;
  --ctwa-primary-light: #e6fbf6;
  --ctwa-slate: #0f172a;
  --ctwa-slate-soft: #1e293b;
  --ctwa-slate-muted: #64748b;
  --ctwa-cream: #f8fafc;
  --ctwa-border: rgba(148, 163, 184, 0.22);
  --ctwa-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
  --ctwa-shadow-lg: 0 18px 40px rgba(15, 23, 42, 0.08);
  --ctwa-radius: 18px;
  --ctwa-radius-sm: 14px;
}
.ctwa-wrap { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }
.ctwa-page-header {
  background: #f4fbf9;
  border-radius: var(--ctwa-radius);
  padding: 1.5rem 2rem;
  margin-bottom: 1.5rem;
  color: var(--ctwa-slate);
  border: 1px solid rgba(148,163,184,0.18);
}
.ctwa-page-header h1 {
  font-weight: 700;
  font-size: 1.35rem;
  letter-spacing: -0.02em;
  margin-bottom: 0.25rem;
  color: #035a4a;
}
.ctwa-page-header .ctwa-subtitle { color: var(--ctwa-slate-muted); font-size: 0.9rem; }
.ctwa-nav {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}
.ctwa-nav a {
  color: #0f172a;
  text-decoration: none;
  padding: 0.45rem 0.9rem;
  border-radius: 999px;
  font-size: 0.88rem;
  font-weight: 500;
  transition: color 0.2s, background 0.2s;
}
.ctwa-nav a:hover { color: #035a4a; background: rgba(0,168,132,0.08); }
.ctwa-nav a.active { color: #ffffff; background: var(--ctwa-primary); }
.ctwa-card {
  background: #fff;
  border: 1px solid var(--ctwa-border);
  border-radius: var(--ctwa-radius);
  box-shadow: var(--ctwa-shadow);
  overflow: hidden;
}
.ctwa-stat-card {
  background: #fff;
  border: 1px solid var(--ctwa-border);
  border-radius: var(--ctwa-radius-sm);
  padding: 1.25rem 1.5rem;
  box-shadow: var(--ctwa-shadow);
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.ctwa-stat-card .stat-label {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ctwa-slate-muted);
  font-weight: 600;
}
.ctwa-stat-card .stat-value {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--ctwa-slate);
}
.ctwa-table { border-collapse: separate; border-spacing: 0; }
.ctwa-table thead th {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-weight: 600;
  color: var(--ctwa-slate-muted);
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--ctwa-border);
  background: #f9fafb;
}
.ctwa-table tbody td {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--ctwa-border);
  vertical-align: middle;
}
.ctwa-table tbody tr:hover { background: #f8fafc; }
.ctwa-table tbody tr:last-child td { border-bottom: none; }
.ctwa-btn-primary {
  background: var(--ctwa-primary);
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0.55rem 1.4rem;
  font-weight: 600;
  transition: background 0.2s;
}
.ctwa-btn-primary:hover { background: var(--ctwa-primary-dark); color: #fff; }
.ctwa-btn-soft {
  background: #e5edf3;
  color: var(--ctwa-slate);
  border: none;
  border-radius: 999px;
  padding: 0.55rem 1.2rem;
  font-weight: 500;
  transition: all 0.2s;
}
.ctwa-btn-soft:hover { background: #d2e2ee; color: var(--ctwa-slate); }
.ctwa-input, .ctwa-select {
  border: 1px solid var(--ctwa-border);
  border-radius: 999px;
  padding: 0.55rem 1rem;
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.ctwa-input:focus, .ctwa-select:focus {
  outline: none;
  border-color: var(--ctwa-primary);
  box-shadow: 0 0 0 3px rgba(0,168,132,0.25);
}
.ctwa-badge {
  padding: 0.35rem 0.75rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.ctwa-empty {
  text-align: center;
  padding: 3rem 2rem;
  color: var(--ctwa-slate-muted);
  font-size: 0.95rem;
}
.ctwa-form-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--ctwa-slate);
  margin-bottom: 0.35rem;
}
.ctwa-step-indicator {
  display: flex;
  justify-content: space-between;
  gap: 0.5rem;
  border-bottom: 1px solid var(--ctwa-border);
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
}
.ctwa-step {
  cursor: pointer;
  font-weight: 500;
  color: var(--ctwa-slate-muted);
  font-size: 0.9rem;
  padding: 0.25rem 0;
}
.ctwa-step.active {
  color: var(--ctwa-primary);
  font-weight: 600;
  border-bottom: 2px solid var(--ctwa-primary);
  margin-bottom: -1px;
}
.ctwa-preview-box {
  background: #f8fafc;
  border: 1px solid var(--ctwa-border);
  border-radius: var(--ctwa-radius-sm);
  padding: 1.5rem;
}
.ctwa-preview-profile {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--ctwa-primary);
}
.ctwa-btn-whatsapp {
  background: var(--ctwa-primary);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 0.5rem 1rem;
  font-weight: 600;
}
.ctwa-btn-whatsapp:hover { background: var(--ctwa-primary-dark); color: #fff; }
.form-step { display: none; }
.form-step.active { display: block; }
</style>

