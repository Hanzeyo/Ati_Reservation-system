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

const auditPaginationState = {
  currentPage: 1,
  pageSize: 15,
  filteredRows: [],
  filteredCards: []
};

let currentViewMode = 'table';

document.addEventListener('DOMContentLoaded', () => {
  updateAuditCounters();
  applyAuditFilters();

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
  auditPaginationState.currentPage = 1;

  document.querySelectorAll('.my-res-stat-card[data-cat]').forEach(card => {
    if (card.getAttribute('data-cat') === category) {
      card.classList.add('active-filter');
    } else {
      card.classList.remove('active-filter');
    }
  });

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
  auditPaginationState.currentPage = 1;

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
  auditPaginationState.currentPage = 1;
  applyAuditFilters();
}

function handleTimeframeFilter(val) {
  auditFilterState.timeframe = val;
  auditPaginationState.currentPage = 1;
  applyAuditFilters();
}

function resetAllAuditFilters() {
  auditFilterState.category = 'all';
  auditFilterState.timeframe = 'all';
  auditFilterState.severity = 'all';
  auditFilterState.query = '';
  auditPaginationState.currentPage = 1;

  const input = document.getElementById('auditSearchInput');
  if (input) input.value = '';

  const clearBtn = document.getElementById('auditSearchClear');
  if (clearBtn) clearBtn.style.display = 'none';

  const sevSelect = document.getElementById('severityFilterSelect');
  if (sevSelect) sevSelect.value = 'all';

  const timeSelect = document.getElementById('timeframeFilterSelect');
  if (timeSelect) timeSelect.value = 'all';

  document.querySelectorAll('.my-res-stat-card[data-cat]').forEach(card => {
    if (card.getAttribute('data-cat') === 'all') {
      card.classList.add('active-filter');
    } else {
      card.classList.remove('active-filter');
    }
  });

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

  auditPaginationState.filteredRows = [];
  auditPaginationState.filteredCards = [];

  // Filter Table Rows
  rows.forEach(row => {
    const cat = (row.getAttribute('data-category') || '').toLowerCase();
    const sev = (row.getAttribute('data-severity') || '').toLowerCase();
    const timeframe = (row.getAttribute('data-timeframe') || '').toLowerCase();
    const text = row.innerText.toLowerCase();

    const matchesCat = (auditFilterState.category === 'all' || auditFilterState.category === cat);
    const matchesSev = (auditFilterState.severity === 'all' || auditFilterState.severity === sev);
    const matchesTime = (
      auditFilterState.timeframe === 'all' || 
      (auditFilterState.timeframe === 'week' 
        ? (timeframe === 'today' || timeframe === 'yesterday' || timeframe === 'week') 
        : auditFilterState.timeframe === timeframe)
    );
    const matchesQuery = (!auditFilterState.query || text.includes(auditFilterState.query));

    if (matchesCat && matchesSev && matchesTime && matchesQuery) {
      auditPaginationState.filteredRows.push(row);
    } else {
      row.style.display = 'none';
    }
  });

  // Filter Timeline Cards
  timelineCards.forEach(card => {
    const cat = (card.getAttribute('data-category') || '').toLowerCase();
    const sev = (card.getAttribute('data-severity') || '').toLowerCase();
    const timeframe = (card.getAttribute('data-timeframe') || '').toLowerCase();
    const text = card.innerText.toLowerCase();

    const matchesCat = (auditFilterState.category === 'all' || auditFilterState.category === cat);
    const matchesSev = (auditFilterState.severity === 'all' || auditFilterState.severity === sev);
    const matchesTime = (
      auditFilterState.timeframe === 'all' || 
      (auditFilterState.timeframe === 'week' 
        ? (timeframe === 'today' || timeframe === 'yesterday' || timeframe === 'week') 
        : auditFilterState.timeframe === timeframe)
    );
    const matchesQuery = (!auditFilterState.query || text.includes(auditFilterState.query));

    if (matchesCat && matchesSev && matchesTime && matchesQuery) {
      auditPaginationState.filteredCards.push(card);
    } else {
      card.style.display = 'none';
    }
  });

  const rowCount = auditPaginationState.filteredRows.length;
  const timelineCount = auditPaginationState.filteredCards.length;

  if (emptyRow) emptyRow.style.display = rowCount === 0 ? '' : 'none';
  if (emptyTimeline) emptyTimeline.style.display = timelineCount === 0 ? 'block' : 'none';

  renderAuditPagination();
}

/* ==========================================================================
   PAGINATION CONTROLLER
   ========================================================================== */
function renderAuditPagination() {
  const totalItems = auditPaginationState.filteredRows.length;
  const navContainer = document.getElementById('auditPaginationNav');
  const startEl = document.getElementById('paginationStart');
  const endEl = document.getElementById('paginationEnd');
  const totalEl = document.getElementById('paginationTotal');

  if (totalEl) totalEl.textContent = totalItems;

  if (totalItems === 0) {
    if (startEl) startEl.textContent = '0';
    if (endEl) endEl.textContent = '0';
    if (navContainer) navContainer.innerHTML = '';
    return;
  }

  const isAll = (auditPaginationState.pageSize === 'all');
  const pageSize = isAll ? totalItems : parseInt(auditPaginationState.pageSize, 10);
  const totalPages = isAll ? 1 : Math.max(1, Math.ceil(totalItems / pageSize));

  // Clamp current page
  if (auditPaginationState.currentPage > totalPages) {
    auditPaginationState.currentPage = totalPages;
  }
  if (auditPaginationState.currentPage < 1) {
    auditPaginationState.currentPage = 1;
  }
  const curPage = auditPaginationState.currentPage;

  const startIdx = (curPage - 1) * pageSize;
  const endIdx = isAll ? totalItems : Math.min(startIdx + pageSize, totalItems);

  if (startEl) startEl.textContent = startIdx + 1;
  if (endEl) endEl.textContent = endIdx;

  // Render Table Rows for this page
  auditPaginationState.filteredRows.forEach((row, idx) => {
    if (idx >= startIdx && idx < endIdx) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });

  // Render Timeline Cards for this page
  auditPaginationState.filteredCards.forEach((card, idx) => {
    if (idx >= startIdx && idx < endIdx) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });

  // Build Pagination Nav Controls
  if (!navContainer) return;
  navContainer.innerHTML = '';

  if (totalPages <= 1) {
    return;
  }

  // Prev Button
  const prevBtn = document.createElement('button');
  prevBtn.type = 'button';
  prevBtn.className = 'btn-page-nav';
  prevBtn.innerHTML = `
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="15 18 9 12 15 6"></polyline>
    </svg>
    <span>Prev</span>
  `;
  prevBtn.disabled = (curPage <= 1);
  prevBtn.onclick = () => goToAuditPage(curPage - 1);
  navContainer.appendChild(prevBtn);

  // Generate Page Numbers with smart ellipsis
  const pagesToShow = getPaginationPages(curPage, totalPages);
  pagesToShow.forEach(p => {
    if (p === '...') {
      const span = document.createElement('span');
      span.className = 'pagination-ellipsis';
      span.textContent = '...';
      navContainer.appendChild(span);
    } else {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = `btn-page-nav ${p === curPage ? 'active' : ''}`;
      btn.textContent = p;
      btn.onclick = () => goToAuditPage(p);
      navContainer.appendChild(btn);
    }
  });

  // Next Button
  const nextBtn = document.createElement('button');
  nextBtn.type = 'button';
  nextBtn.className = 'btn-page-nav';
  nextBtn.innerHTML = `
    <span>Next</span>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
  `;
  nextBtn.disabled = (curPage >= totalPages);
  nextBtn.onclick = () => goToAuditPage(curPage + 1);
  navContainer.appendChild(nextBtn);
}

function getPaginationPages(currentPage, totalPages) {
  if (totalPages <= 7) {
    const pages = [];
    for (let i = 1; i <= totalPages; i++) pages.push(i);
    return pages;
  }

  const pages = [1];
  let start = Math.max(2, currentPage - 1);
  let end = Math.min(totalPages - 1, currentPage + 1);

  if (currentPage <= 3) {
    start = 2;
    end = 4;
  } else if (currentPage >= totalPages - 2) {
    start = totalPages - 3;
    end = totalPages - 1;
  }

  if (start > 2) {
    pages.push('...');
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  if (end < totalPages - 1) {
    pages.push('...');
  }

  pages.push(totalPages);
  return pages;
}

function goToAuditPage(page) {
  auditPaginationState.currentPage = page;
  renderAuditPagination();

  // Scroll to table smoothly
  const card = document.querySelector('.admin-table-card');
  if (card) {
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

function changeAuditPageSize(val) {
  auditPaginationState.pageSize = (val === 'all') ? 'all' : parseInt(val, 10);
  auditPaginationState.currentPage = 1;
  renderAuditPagination();
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

  renderAuditPagination();
}

/* ==========================================================================
   EXPORT AUDIT LOG TO CSV
   ========================================================================== */
function exportAuditLogCSV() {
  const rowsToExport = (auditPaginationState.filteredRows && auditPaginationState.filteredRows.length > 0)
    ? auditPaginationState.filteredRows
    : document.querySelectorAll('#auditTableBody tr[data-event-id]');

  const data = [
    ['Event ID', 'Timestamp', 'Category', 'Severity', 'Authority / Actor', 'Target Resource', 'Details & Narrative', 'Security Stamp']
  ];

  rowsToExport.forEach(row => {
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

  showAuditToast(`Successfully exported ${rowsToExport.length} audit records to CSV.`, 'success');
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
