{{-- CTWA Design System: Classy & Premium --}}
<style>
:root {
  --ctwa-primary: #25D366;
  --ctwa-primary-dark: #128C7E;
  --ctwa-primary-light: #DCF8C6;
  --ctwa-slate: #0f172a;
  --ctwa-slate-soft: #1e293b;
  --ctwa-slate-muted: #64748b;
  --ctwa-cream: #f8fafc;
  --ctwa-border: rgba(15, 23, 42, 0.06);
  --ctwa-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  --ctwa-shadow-lg: 0 10px 40px -10px rgba(15, 23, 42, 0.12);
  --ctwa-radius: 16px;
  --ctwa-radius-sm: 12px;
}
.ctwa-wrap { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; }
.ctwa-page-header {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  border-radius: var(--ctwa-radius);
  padding: 1.75rem 2rem;
  margin-bottom: 1.5rem;
  color: #fff;
}
.ctwa-page-header h1 { font-weight: 700; font-size: 1.5rem; letter-spacing: -0.02em; margin-bottom: 0.25rem; }
.ctwa-page-header .ctwa-subtitle { color: rgba(255,255,255,0.7); font-size: 0.95rem; }
.ctwa-nav {
  display: flex; gap: 0.25rem; flex-wrap: wrap;
}
.ctwa-nav a {
  color: rgba(255,255,255,0.75); text-decoration: none; padding: 0.5rem 1rem;
  border-radius: 10px; font-size: 0.9rem; font-weight: 500;
  transition: color 0.2s, background 0.2s;
}
.ctwa-nav a:hover { color: #fff; background: rgba(255,255,255,0.1); }
.ctwa-nav a.active { color: #25D366; background: rgba(37, 211, 102, 0.15); }
.ctwa-card {
  background: #fff; border: 1px solid var(--ctwa-border); border-radius: var(--ctwa-radius);
  box-shadow: var(--ctwa-shadow); overflow: hidden;
}
.ctwa-stat-card {
  background: #fff; border: 1px solid var(--ctwa-border); border-radius: var(--ctwa-radius-sm);
  padding: 1.5rem; text-align: center; box-shadow: var(--ctwa-shadow);
  transition: transform 0.2s, box-shadow 0.2s;
}
.ctwa-stat-card:hover { transform: translateY(-2px); box-shadow: var(--ctwa-shadow-lg); }
.ctwa-stat-card .stat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--ctwa-slate-muted); font-weight: 600; }
.ctwa-stat-card .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--ctwa-slate); margin-top: 0.25rem; }
.ctwa-panel-card {
  display: block; text-decoration: none; color: inherit;
  background: #fff; border: 1px solid var(--ctwa-border); border-radius: var(--ctwa-radius);
  box-shadow: var(--ctwa-shadow); padding: 1.75rem; transition: all 0.25s ease;
}
.ctwa-panel-card:hover {
  color: inherit; transform: translateY(-4px); box-shadow: var(--ctwa-shadow-lg);
  border-color: rgba(37, 211, 102, 0.2);
}
.ctwa-panel-card .panel-icon {
  width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: var(--ctwa-primary);
  font-size: 1.5rem;
}
.ctwa-panel-card .panel-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--ctwa-slate-muted); font-weight: 600; }
.ctwa-panel-card .panel-title { font-size: 1.1rem; font-weight: 600; color: var(--ctwa-slate); margin-bottom: 0.25rem; }
.ctwa-panel-card .panel-desc { font-size: 0.85rem; color: var(--ctwa-slate-muted); }
.ctwa-table { border-collapse: separate; border-spacing: 0; }
.ctwa-table thead th {
  font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;
  color: var(--ctwa-slate-muted); padding: 1rem 1.25rem; border-bottom: 1px solid var(--ctwa-border); background: #fafafa;
}
.ctwa-table tbody td { padding: 1rem 1.25rem; border-bottom: 1px solid var(--ctwa-border); vertical-align: middle; }
.ctwa-table tbody tr:hover { background: #fafafa; }
.ctwa-table tbody tr:last-child td { border-bottom: none; }
.ctwa-btn-primary { background: var(--ctwa-primary); color: #fff; border: none; border-radius: 10px; padding: 0.5rem 1.25rem; font-weight: 600; transition: background 0.2s; }
.ctwa-btn-primary:hover { background: var(--ctwa-primary-dark); color: #fff; }
.ctwa-btn-soft { background: #f1f5f9; color: var(--ctwa-slate); border: none; border-radius: 10px; padding: 0.5rem 1.25rem; font-weight: 500; transition: all 0.2s; }
.ctwa-btn-soft:hover { background: #e2e8f0; color: var(--ctwa-slate); }
.ctwa-input, .ctwa-select {
  border: 1px solid var(--ctwa-border); border-radius: 10px; padding: 0.5rem 1rem;
  font-size: 0.9rem; transition: border-color 0.2s, box-shadow 0.2s;
}
.ctwa-input:focus, .ctwa-select:focus {
  outline: none; border-color: var(--ctwa-primary); box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15);
}
.ctwa-badge { padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
.ctwa-empty { text-align: center; padding: 3rem 2rem; color: var(--ctwa-slate-muted); font-size: 0.95rem; }
.ctwa-form-label { font-size: 0.8rem; font-weight: 600; color: var(--ctwa-slate); margin-bottom: 0.35rem; }
.ctwa-step-indicator { display: flex; justify-content: space-between; gap: 0.5rem; border-bottom: 1px solid var(--ctwa-border); padding-bottom: 1rem; margin-bottom: 1.5rem; }
.ctwa-step { cursor: pointer; font-weight: 500; color: var(--ctwa-slate-muted); font-size: 0.9rem; padding: 0.25rem 0; }
.ctwa-step.active { color: var(--ctwa-primary); font-weight: 600; border-bottom: 2px solid var(--ctwa-primary); margin-bottom: -1px; }
.ctwa-preview-box { background: #f8fafc; border: 1px solid var(--ctwa-border); border-radius: var(--ctwa-radius-sm); padding: 1.5rem; }
.ctwa-preview-profile { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--ctwa-primary); }
.ctwa-btn-whatsapp { background: var(--ctwa-primary); color: #fff; border: none; border-radius: 12px; padding: 0.5rem 1rem; font-weight: 600; }
.ctwa-btn-whatsapp:hover { background: var(--ctwa-primary-dark); color: #fff; }
.form-step { display: none; }
.form-step.active { display: block; }
</style>
