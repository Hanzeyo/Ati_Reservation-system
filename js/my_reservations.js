/**
 * ATI Reservation System - My Reservations Dashboard Controller
 * Handles filtering, search, routing modal views, gatepass generation, and cancellation
 */

document.addEventListener('DOMContentLoaded', () => {
  initProfileDropdown();
  initMobileDrawer();
  initFilterTabs();
  initKpiFilters();
  initSearchAndFilter();
  initViewModeToggle();
  initCardModals();
  initCancellationWorkflow();
  initCopyButtons();
  initDownloadSlip();
});

/* ==========================================================================
   1. Profile Dropdown Toggle
   ========================================================================== */
function initProfileDropdown() {
  const badge = document.getElementById('userProfileBadge');
  const dropdown = document.getElementById('profileDropdown');

  if (!badge || !dropdown) return;

  badge.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    dropdown.classList.remove('show');
  });
}

/* ==========================================================================
   2. Filter Tabs & Dynamic Counter
   ========================================================================== */
let activeStatusFilter = 'all';
let activeVenueFilter = 'all';
let activeSearchQuery = '';
let currentViewMode = 'cards';

function applyAllFilters() {
  const cards = document.querySelectorAll('.res-card');
  const tableRows = document.querySelectorAll('.res-table-row');
  const tableWrap = document.getElementById('reservationsTableWrap');
  const emptyState = document.getElementById('emptyResState');
  let visibleCount = 0;

  cards.forEach(card => {
    const cardStatus = card.dataset.status;
    const cardVenue = card.dataset.venue;
    const cardText = card.textContent.toLowerCase();

    const matchesStatus = (activeStatusFilter === 'all') || (cardStatus === activeStatusFilter);
    const matchesVenue = (activeVenueFilter === 'all') || (cardVenue === activeVenueFilter);
    const matchesSearch = !activeSearchQuery || cardText.includes(activeSearchQuery);

    if (currentViewMode === 'cards') {
      if (matchesStatus && matchesVenue && matchesSearch) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    } else {
      card.style.display = 'none';
    }
  });

  tableRows.forEach(row => {
    const rowStatus = row.dataset.status;
    const rowVenue = row.dataset.venue;
    const rowText = row.textContent.toLowerCase();

    const matchesStatus = (activeStatusFilter === 'all') || (rowStatus === activeStatusFilter);
    const matchesVenue = (activeVenueFilter === 'all') || (rowVenue === activeVenueFilter);
    const matchesSearch = !activeSearchQuery || rowText.includes(activeSearchQuery);

    if (matchesStatus && matchesVenue && matchesSearch) {
      row.style.display = '';
      if (currentViewMode === 'table') visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  if (tableWrap) {
    tableWrap.style.display = currentViewMode === 'table' ? 'block' : 'none';
  }

  if (emptyState) {
    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
  }
}

function initViewModeToggle() {
  const btnCards = document.getElementById('btnViewCards');
  const btnTable = document.getElementById('btnViewTable');

  if (!btnCards || !btnTable) return;

  btnCards.addEventListener('click', () => {
    currentViewMode = 'cards';
    btnCards.classList.add('active');
    btnCards.setAttribute('aria-pressed', 'true');
    btnTable.classList.remove('active');
    btnTable.setAttribute('aria-pressed', 'false');
    applyAllFilters();
  });

  btnTable.addEventListener('click', () => {
    currentViewMode = 'table';
    btnTable.classList.add('active');
    btnTable.setAttribute('aria-pressed', 'true');
    btnCards.classList.remove('active');
    btnCards.setAttribute('aria-pressed', 'false');
    applyAllFilters();
  });
}

function updateKpiCounts() {
  const cards = document.querySelectorAll('.res-card');
  let total = 0;
  let pending = 0;
  let approved = 0;
  let completed = 0;

  cards.forEach(card => {
    const status = card.dataset.status;
    if (status !== 'cancelled') {
      total++;
    }
    if (status === 'pending') pending++;
    if (status === 'approved') approved++;
    if (status === 'completed') completed++;
  });

  const kpiTotal = document.getElementById('kpiTotal');
  const kpiPending = document.getElementById('kpiPending');
  const kpiApproved = document.getElementById('kpiApproved');
  const kpiCompleted = document.getElementById('kpiCompleted');

  const tabCountAll = document.getElementById('tabCountAll');
  const tabCountPending = document.getElementById('tabCountPending');
  const tabCountApproved = document.getElementById('tabCountApproved');
  const tabCountCompleted = document.getElementById('tabCountCompleted');
  const navBadgeCount = document.getElementById('navBadgeCount');

  if (kpiTotal) kpiTotal.textContent = total;
  if (kpiPending) kpiPending.textContent = pending;
  if (kpiApproved) kpiApproved.textContent = approved;
  if (kpiCompleted) kpiCompleted.textContent = completed;

  if (tabCountAll) tabCountAll.textContent = total;
  if (tabCountPending) tabCountPending.textContent = pending;
  if (tabCountApproved) tabCountApproved.textContent = approved;
  if (tabCountCompleted) tabCountCompleted.textContent = completed;
  if (navBadgeCount) navBadgeCount.textContent = pending;
}

function initFilterTabs() {
  const tabs = document.querySelectorAll('.filter-tab-btn');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      activeStatusFilter = tab.dataset.filter;

      // Sync KPI cards active class
      document.querySelectorAll('.my-res-stat-card').forEach(c => {
        c.classList.toggle('active-filter', c.dataset.filter === activeStatusFilter);
      });

      applyAllFilters();
    });
  });
}

function initKpiFilters() {
  const statCards = document.querySelectorAll('.my-res-stat-card');
  const tabs = document.querySelectorAll('.filter-tab-btn');

  statCards.forEach(card => {
    card.addEventListener('click', () => {
      const filter = card.dataset.filter;
      if (!filter) return;

      tabs.forEach(t => {
        if (t.dataset.filter === filter) {
          t.click();
        }
      });
    });
  });
}

/* ==========================================================================
   3. Live Search & Venue Filter
   ========================================================================== */
function initSearchAndFilter() {
  const searchInput = document.getElementById('resSearchInput');
  const venueSelect = document.getElementById('resVenueFilter');

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      activeSearchQuery = e.target.value.toLowerCase().trim();
      applyAllFilters();
    });
  }

  if (venueSelect) {
    venueSelect.addEventListener('change', (e) => {
      activeVenueFilter = e.target.value;
      applyAllFilters();
    });
  }
}

/* ==========================================================================
   4. Reservation Details & Routing Modal
   ========================================================================== */
function initCardModals() {
  const modal = document.getElementById('resDetailsModal');
  const closeBtns = document.querySelectorAll('.js-close-res-modal');
  const detailBtns = document.querySelectorAll('.js-view-details');

  if (!modal) return;

  detailBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      let card = btn.closest('.res-card');
      if (!card) {
        const row = btn.closest('.res-table-row');
        if (row) {
          const ref = row.querySelector('.res-ref-tag')?.textContent.trim();
          if (ref) {
            card = document.querySelector(`.res-card[data-ref="${ref}"]`);
          }
        }
      }
      if (!card) return;

      // Extract metadata from card
      const ref = card.dataset.ref || 'ATI-RES-2026-1042';
      const venue = card.dataset.venueTitle || 'Function Hall';
      const title = card.querySelector('.res-event-title')?.textContent || 'Official Activity';
      const dates = card.dataset.dates || 'Oct 14 - 16, 2026';
      const time = card.dataset.time || '08:00 AM - 05:00 PM';
      const pax = card.dataset.pax || '120 Delegates';
      const division = card.dataset.division || 'Career Development Division (CDD)';
      const statusText = card.dataset.statusText || 'Pending Administrative Review';
      const statusClass = card.dataset.status || 'pending';

      // Populate modal
      document.getElementById('modalResRef').textContent = ref;
      document.getElementById('modalResTitle').textContent = title;
      document.getElementById('modalResVenue').textContent = venue;
      document.getElementById('modalResDates').textContent = dates;
      document.getElementById('modalResTime').textContent = time;
      document.getElementById('modalResPax').textContent = pax;
      document.getElementById('modalResDivision').textContent = division;

      const modalBadge = document.getElementById('modalResStatusBadge');
      if (modalBadge) {
        modalBadge.className = `res-status-badge ${statusClass}`;
        modalBadge.innerHTML = `<span class="status-dot-pulse"></span><span>${statusText}</span>`;
      }

      // Configure routing steps display inside modal
      const routingList = document.getElementById('modalRoutingList');
      if (routingList) {
        if (statusClass === 'approved') {
          routingList.innerHTML = `
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>1. Request Created & Submitted</h5>
                <p>Submitted by Juan Dela Cruz via online reservation system with attached Activity Design.</p>
                <span class="routing-timestamp">Oct 01, 2026 • 09:15 AM</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>2. Division Chief Endorsement</h5>
                <p>Endorsed by Dr. M. Villacorta (Division Chief, CDD). Special Order verified.</p>
                <span class="routing-timestamp">Oct 01, 2026 • 02:40 PM</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>3. Administrative & Venue Operations Clearance</h5>
                <p>Venue confirmed vacant. AV sound technician and air-conditioning engineers assigned.</p>
                <span class="routing-timestamp">Oct 02, 2026 • 10:10 AM</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>4. Final Approval & Gate Pass Released</h5>
                <p>Approved by Engr. R. Santos, Chief Administrative Officer. Venue reserved on Master Schedule.</p>
                <span class="routing-timestamp">Oct 02, 2026 • 03:30 PM</span>
              </div>
            </div>
          `;
        } else {
          routingList.innerHTML = `
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>1. Request Created & Submitted</h5>
                <p>Submitted by Juan Dela Cruz with supporting documents uploaded.</p>
                <span class="routing-timestamp">Oct 02, 2026 • 09:15 AM</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>2. Division Chief Endorsement</h5>
                <p>Endorsed by Division Head. Endorsement memo attached.</p>
                <span class="routing-timestamp">Oct 02, 2026 • 11:30 AM</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge pending">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>3. Administrative Services Clearance (In Progress)</h5>
                <p>Currently undergoing schedule conflict verification and facility logistics allocation.</p>
                <span class="routing-timestamp">Active Review • Assigned to: Admin Property & Facilities Unit</span>
              </div>
            </div>
            <div class="routing-item">
              <div class="routing-icon-badge">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ab0a2" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #82998c;">4. Final Confirmation & Official Gate Pass</h5>
                <p style="color: #9ab0a2;">Awaiting Step 3 completion for official signing and booking confirmation.</p>
              </div>
            </div>
          `;
        }
      }

      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    });
  });

  closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('show'));
      document.body.style.overflow = '';
    });
  });

  // Close on backdrop click
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', (e) => {
      if (e.target === m) {
        m.classList.remove('show');
        document.body.style.overflow = '';
      }
    });
  });
}

/* ==========================================================================
   5. Gate Pass & QR Modal
   ========================================================================== */
window.showGatePassModal = function(ref, title, venue, dates) {
  const modal = document.getElementById('gatePassModal');
  if (!modal) return;

  document.getElementById('gpRefCode').textContent = ref;
  document.getElementById('gpEventTitle').textContent = title;
  document.getElementById('gpVenueName').textContent = venue;
  document.getElementById('gpEventDates').textContent = dates;

  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
};

/* ==========================================================================
   6. Cancellation Workflow
   ========================================================================== */
let cardToCancel = null;

function initCancellationWorkflow() {
  const cancelBtns = document.querySelectorAll('.js-cancel-res');
  const cancelModal = document.getElementById('cancelConfirmModal');
  const btnConfirmCancel = document.getElementById('btnConfirmCancel');

  cancelBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      cardToCancel = btn.closest('.res-card');
      const ref = cardToCancel?.dataset.ref || 'ATI-RES-2026-1042';
      const title = cardToCancel?.querySelector('.res-event-title')?.textContent || 'this activity';

      document.getElementById('cancelResRef').textContent = ref;
      document.getElementById('cancelResTitle').textContent = title;

      if (cancelModal) {
        cancelModal.classList.add('show');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  if (btnConfirmCancel) {
    btnConfirmCancel.addEventListener('click', () => {
      if (!cardToCancel) return;

      const reason = document.getElementById('cancelReasonSelect')?.value || 'Schedule conflict';

      // Update card state
      cardToCancel.dataset.status = 'cancelled';
      const badge = cardToCancel.querySelector('.res-status-badge');
      if (badge) {
        badge.className = 'res-status-badge cancelled';
        badge.innerHTML = `<span class="status-dot-pulse"></span><span>Cancelled by Requestor</span>`;
      }

      // Update actions toolbar on card
      const actionsRight = cardToCancel.querySelector('.res-actions-right');
      if (actionsRight) {
        actionsRight.innerHTML = `
          <button type="button" class="btn-card-action secondary js-view-details">
            <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>View Cancellation Info</span>
          </button>
        `;
      }

      // Re-initialize details click for the mutated button
      initCardModals();

      // Close modal
      cancelModal.classList.remove('show');
      document.body.style.overflow = '';

      // Update KPI counters
      updateKpiCounts();
      applyAllFilters();

      showToast(`Reservation ${cardToCancel.dataset.ref} was cancelled successfully.`);
      cardToCancel = null;
    });
  }
}

/* ==========================================================================
   7. Copy Reference Button
   ========================================================================== */
function initCopyButtons() {
  const copyBtns = document.querySelectorAll('.btn-copy-ref');

  copyBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const ref = btn.dataset.ref;
      if (ref) {
        navigator.clipboard.writeText(ref).then(() => {
          showToast(`Copied reference ID: ${ref}`);
        }).catch(() => {
          showToast(`Reference ID: ${ref}`);
        });
      }
    });
  });
}

/* ==========================================================================
   8. Download Slip Simulator
   ========================================================================== */
function initDownloadSlip() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-download-slip');
    if (!btn) return;

    const ref = btn.dataset.ref || 'ATI-RES';
    showToast(`Generating official reservation slip for ${ref}...`);

    setTimeout(() => {
      showToast(`Reservation slip ${ref}.pdf generated & downloaded!`);
    }, 1200);
  });
}

/* ==========================================================================
   9. Helper Toast Notification
   ========================================================================== */
function showToast(message) {
  let toast = document.getElementById('myResToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'myResToast';
    toast.className = 'toast-notice';
    document.body.appendChild(toast);
  }

  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#86efac" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>${message}</span>
  `;

  toast.classList.add('show');
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3200);
}

/* ==========================================================================
   10. Mobile Navigation Drawer Controller
   ========================================================================== */
function initMobileDrawer() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const overlay = document.getElementById('mobileDrawerOverlay');
  const closeBtn = document.getElementById('mobileDrawerClose');

  if (!toggleBtn || !overlay) return;

  function openDrawer() {
    overlay.style.display = 'block';
    requestAnimationFrame(() => {
      overlay.classList.add('show');
    });
    toggleBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    overlay.classList.remove('show');
    toggleBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    setTimeout(() => {
      if (!overlay.classList.contains('show')) {
        overlay.style.display = 'none';
      }
    }, 280);
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    openDrawer();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      closeDrawer();
    });
  }

  // Close when tapping on the dark backdrop outside the drawer
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      closeDrawer();
    }
  });

  // Close on Escape key press
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('show')) {
      closeDrawer();
    }
  });

  // Close automatically if user resizes back to desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 860 && overlay.classList.contains('show')) {
      closeDrawer();
    }
  });
}
