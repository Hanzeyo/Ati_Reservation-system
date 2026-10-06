/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Institutional Audit Trail & Security Logs Controller
 */

const auditFilterState = {
  category: 'all',
  timeframe: 'all',
  severity: 'all',
  query: ''
};

let currentViewMode = 'table';

document.addEventListener('DOMContentLoaded', () => {
  updateAuditCounters();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }

  // Keyboard shortcut Ctrl+K to jump to search
  document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      const input = document.getElementById('auditSearchInput');
      if (input) {
        input.focus();
        input.select();
      }
    }
  });
});

/* ==========================================================================
   FILTERING & SEARCH
   ========================================================================== */
function filterAuditByCategory(category) {
  auditFilterState.category = category;

  document.querySelectorAll('.user-filter-pill').forEach(pill => {
    if (pill.getAttribute('data-cat') === category) {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  applyAuditFilters();
}

function handleAuditSearch(query) {
  auditFilterState.query = (query || '').toLowerCase().trim();

  const clearBtn = document.getElementById('auditSearchClear');
  if (clearBtn) {
    clearBtn.style.display = auditFilterState.query ? 'flex' : 'none';
  }

  applyAuditFilters();
}

function clearAuditSearch() {
  const input = document.getElementById('auditSearchInput');
  if (input) {
    input.value = '';
    input.focus();
  }
  handleAuditSearch('');
}

function handleSeverityFilter(val) {
  auditFilterState.severity = val;
  applyAuditFilters();
}

function handleTimeframeFilter(val) {
  auditFilterState.timeframe = val;
  applyAuditFilters();
}

function resetAllAuditFilters() {
  auditFilterState.category = 'all';
  auditFilterState.timeframe = 'all';
  auditFilterState.severity = 'all';
  auditFilterState.query = '';

  const input = document.getElementById('auditSearchInput');
  if (input) input.value = '';

  const clearBtn = document.getElementById('auditSearchClear');
  if (clearBtn) clearBtn.style.display = 'none';

  const sevSelect = document.getElementById('severityFilterSelect');
  if (sevSelect) sevSelect.value = 'all';

  const timeSelect = document.getElementById('timeframeFilterSelect');
  if (timeSelect) timeSelect.value = 'all';

  document.querySelectorAll('.user-filter-pill').forEach(pill => {
    if (pill.getAttribute('data-cat') === 'all') {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  applyAuditFilters();
}

function applyAuditFilters() {
  const rows = document.querySelectorAll('#auditTableBody tr[data-event-id]');
  const timelineCards = document.querySelectorAll('.timeline-event-card[data-event-id]');
  const emptyRow = document.getElementById('auditNoResultsRow');
  const emptyTimeline = document.getElementById('auditNoResultsTimeline');
  let visibleCount = 0;

  // Process Table Rows
  rows.forEach(row => {
    const cat = (row.getAttribute('data-category') || '').toLowerCase();
    const sev = (row.getAttribute('data-severity') || '').toLowerCase();
    const timeframe = (row.getAttribute('data-timeframe') || '').toLowerCase();
    const text = row.innerText.toLowerCase();

    const matchesCat = (auditFilterState.category === 'all' || auditFilterState.category === cat);
    const matchesSev = (auditFilterState.severity === 'all' || auditFilterState.severity === sev);
    const matchesTime = (auditFilterState.timeframe === 'all' || auditFilterState.timeframe === timeframe);
    const matchesQuery = (!auditFilterState.query || text.includes(auditFilterState.query));

    if (matchesCat && matchesSev && matchesTime && matchesQuery) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  // Process Timeline Cards
  timelineCards.forEach(card => {
    const cat = (card.getAttribute('data-category') || '').toLowerCase();
    const sev = (card.getAttribute('data-severity') || '').toLowerCase();
    const timeframe = (card.getAttribute('data-timeframe') || '').toLowerCase();
    const text = card.innerText.toLowerCase();

    const matchesCat = (auditFilterState.category === 'all' || auditFilterState.category === cat);
    const matchesSev = (auditFilterState.severity === 'all' || auditFilterState.severity === sev);
    const matchesTime = (auditFilterState.timeframe === 'all' || auditFilterState.timeframe === timeframe);
    const matchesQuery = (!auditFilterState.query || text.includes(auditFilterState.query));

    if (matchesCat && matchesSev && matchesTime && matchesQuery) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });

  if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
  if (emptyTimeline) emptyTimeline.style.display = visibleCount === 0 ? 'block' : 'none';
}

function updateAuditCounters() {
  const rows = document.querySelectorAll('#auditTableBody tr[data-event-id]');
  let total = rows.length;
  let approvals = 0;
  let maintenance = 0;
  let security = 0;

  rows.forEach(row => {
    const cat = row.getAttribute('data-category');
    if (cat === 'approval') approvals++;
    else if (cat === 'maintenance') maintenance++;
    else if (cat === 'security') security++;
  });

  const totalEl = document.getElementById('kpiTotalEvents');
  const appEl = document.getElementById('kpiApprovalEvents');
  const maintEl = document.getElementById('kpiMaintenanceEvents');
  const secEl = document.getElementById('kpiSecurityEvents');

  if (totalEl) totalEl.textContent = total;
  if (appEl) appEl.textContent = approvals;
  if (maintEl) maintEl.textContent = maintenance;
  if (secEl) secEl.textContent = security;

  const countPillAll = document.getElementById('countPillAll');
  const countPillApp = document.getElementById('countPillApp');
  const countPillMaint = document.getElementById('countPillMaint');
  const countPillSec = document.getElementById('countPillSec');

  if (countPillAll) countPillAll.textContent = total;
  if (countPillApp) countPillApp.textContent = approvals;
  if (countPillMaint) countPillMaint.textContent = maintenance;
  if (countPillSec) countPillSec.textContent = security;
}

/* ==========================================================================
   VIEW MODE TOGGLE: TABLE VS TIMELINE
   ========================================================================== */
function switchAuditView(mode) {
  currentViewMode = mode;
  const tableView = document.getElementById('auditTableViewContainer');
  const timelineView = document.getElementById('auditTimelineViewContainer');

  const btnTable = document.getElementById('btnViewTable');
  const btnTimeline = document.getElementById('btnViewTimeline');

  if (mode === 'table') {
    if (tableView) tableView.style.display = 'block';
    if (timelineView) timelineView.style.display = 'none';
    if (btnTable) btnTable.classList.add('active');
    if (btnTimeline) btnTimeline.classList.remove('active');
  } else {
    if (tableView) tableView.style.display = 'none';
    if (timelineView) timelineView.style.display = 'block';
    if (btnTable) btnTable.classList.remove('active');
    if (btnTimeline) btnTimeline.classList.add('active');
  }

  applyAuditFilters();
}

/* ==========================================================================
   EXPORT AUDIT LOG TO CSV
   ========================================================================== */
function exportAuditLogCSV() {
  const visibleRows = document.querySelectorAll('#auditTableBody tr[data-event-id]');
  const data = [
    ['Event ID', 'Timestamp', 'Category', 'Severity', 'Authority / Actor', 'Target Resource', 'Details & Narrative', 'Security Stamp']
  ];

  visibleRows.forEach(row => {
    if (row.style.display === 'none') return;

    const id = row.getAttribute('data-event-id') || '';
    const time = row.children[0]?.innerText.replace(/\n/g, ' ') || '';
    const cat = row.children[1]?.innerText.replace(/\n/g, ' ') || '';
    const details = row.children[2]?.innerText.replace(/\n/g, ' ').replace(/"/g, '""') || '';
    const actor = row.children[3]?.innerText.replace(/\n/g, ' ') || '';
    const target = row.children[4]?.innerText.replace(/\n/g, ' ') || '';
    const hash = row.children[5]?.innerText.replace(/\n/g, ' ') || '';

    data.push([`"${id}"`, `"${time}"`, `"${cat}"`, `"Normal"`, `"${actor}"`, `"${target}"`, `"${details}"`, `"${hash}"`]);
  });

  const csvContent = 'data:text/csv;charset=utf-8,' + data.map(e => e.join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `ATI_Audit_Trail_Log_${new Date().toISOString().slice(0, 10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showAuditToast('Audit Trail Log successfully exported as official CSV.', 'success');
}

/* ==========================================================================
   SIDEBAR TOGGLE
   ========================================================================== */
function toggleSidebar(forceState) {
  const layout = document.querySelector('.admin-layout-container');
  const sidebar = document.getElementById('adminSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (!sidebar) return;

  const isMobile = window.innerWidth <= 960;

  if (isMobile) {
    const willOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('open');
    if (willOpen) {
      sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('show');
    } else {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('show');
    }
  } else {
    // Desktop: toggle collapsed state
    const isCollapsed = layout ? layout.classList.contains('sidebar-collapsed') : false;
    const willCollapse = typeof forceState === 'boolean' ? !forceState : !isCollapsed;

    if (layout) {
      if (willCollapse) {
        layout.classList.add('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', 'true');
      } else {
        layout.classList.remove('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', 'false');
      }
    }
  }
}

/* ==========================================================================
   TOAST HELPER
   ========================================================================== */
function showAuditToast(message, type = 'success') {
  let container = document.getElementById('adminToastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'adminToastContainer';
    container.className = 'admin-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `admin-toast ${type === 'decline' ? 'decline' : ''}`;
  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
      ${type === 'decline'
      ? '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>'
      : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'}
    </svg>
    <span>${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 320);
  }, 4000);
}
