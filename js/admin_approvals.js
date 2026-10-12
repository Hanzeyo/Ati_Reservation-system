/**
 * Agriculture Training Institute - Central Office
 * Super Admin Approvals Queue & Three-Stage Governance Engine
 * Interactive Controller (Stage 1 Endorsement -> Stage 2 Recommendation -> Stage 3 Clearance)
 */

// Initial Dataset of Reservations (loaded from MySQL with fallback)
const approvalsData = (window.initialApprovalsData && typeof window.initialApprovalsData === 'object' && Object.keys(window.initialApprovalsData).length > 0)
  ? Object.assign({}, window.initialApprovalsData)
  : {
  'ATI-2026-0891': {
    ref: 'ATI-2026-0891',
    applicant: 'Engr. Juan Dela Cruz',
    agency: 'ATI - Career Development Division (CDD)',
    email: 'juan.delacruz@ati.da.gov.ph',
    phone: '+63 917 123 4567',
    venue: 'Serrano Function Hall',
    venueCategory: 'halls',
    dates: 'October 12 – 14, 2026',
    timeSlot: '8:00 AM – 5:00 PM (3 Days)',
    pax: 85,
    eventTitle: 'National TOT on Climate-Resilient Agriculture Technologies',
    equipment: 'Projector & Motorized Screen, 4 Wireless Microphones, Stage Rostrum, Classroom Type Seating',
    specialNotes: 'Participants include Regional Training Center Focal Persons nationwide. Catering arranged with in-house cafeteria.',
    stage: 'stage1', // 'stage1' | 'stage2' | 'stage3' | 'approved' | 'rejected'
    stage1By: null,
    stage1Date: null,
    stage1Remarks: null,
    stage2By: null,
    stage2Date: null,
    stage2Remarks: null,
    stage3By: null,
    stage3Date: null,
    stage3Remarks: null,
    urgency: 'medium',
    dateFiled: '2026-10-02'
  },
  'ATI-2026-0892': {
    ref: 'ATI-2026-0892',
    applicant: 'Dr. Maria Santos',
    agency: 'Bureau of Plant Industry (BPI - Central)',
    email: 'maria.santos@bpi.gov.ph',
    phone: '+63 918 987 6543',
    venue: 'Dormitory Suites A & B',
    venueCategory: 'dorms',
    dates: 'October 15 – 18, 2026',
    timeSlot: 'Check-in: Oct 15 2:00 PM | Check-out: Oct 18 12:00 PM',
    pax: 32,
    eventTitle: 'Inter-Agency Seed Quality & Nursery Inspection Workshop',
    equipment: '32 Single Beds, Linen Sets, Common Lounge Access, Hot Water Showers',
    specialNotes: 'Delegates arriving from Regions III and IV-A. Vehicle parking required for 3 official vans.',
    stage: 'stage1',
    stage1By: null,
    stage1Date: null,
    stage1Remarks: null,
    stage2By: null,
    stage2Date: null,
    stage2Remarks: null,
    stage3By: null,
    stage3Date: null,
    stage3Remarks: null,
    urgency: 'normal',
    dateFiled: '2026-10-03'
  },
  'ATI-2026-0893': {
    ref: 'ATI-2026-0893',
    applicant: 'Atty. Bernardo Castro',
    agency: 'Department of Agriculture - Legal Service',
    email: 'bcastro@da.gov.ph',
    phone: '+63 920 555 1212',
    venue: 'Executive Boardroom',
    venueCategory: 'halls',
    dates: 'October 08, 2026',
    timeSlot: '1:00 PM – 5:00 PM (Half Day)',
    pax: 18,
    eventTitle: 'Quasi-Judicial Administrative Adjudication Hearing',
    equipment: 'Video Conference System, Audio Recording Microphone System, Smart Interactive Display',
    specialNotes: 'Confidential executive proceeding. Strict soundproofing and dedicated network needed.',
    stage: 'stage2', // Endorsed, awaiting Stage 2 recommendation!
    stage1By: 'Division Chief',
    stage1Date: 'Oct 04, 2026 11:30 AM',
    stage1Remarks: 'Endorsed for official legal division proceedings.',
    stage2By: null,
    stage2Date: null,
    stage2Remarks: null,
    stage3By: null,
    stage3Date: null,
    stage3Remarks: null,
    urgency: 'urgent',
    dateFiled: '2026-10-01'
  },
  'ATI-2026-0894': {
    ref: 'ATI-2026-0894',
    applicant: 'Ramon Pascual',
    agency: 'Philippine Rice Research Institute (PhilRice)',
    email: 'rpascual@philrice.gov.ph',
    phone: '+63 922 444 3322',
    venue: '4-H Learning Center',
    venueCategory: 'halls',
    dates: 'October 20 – 22, 2026',
    timeSlot: '8:30 AM – 5:00 PM (3 Days)',
    pax: 60,
    eventTitle: 'Digital Agriculture Course & Rice Crop Manager Training of Trainers',
    equipment: 'High-speed WiFi Hub, Dual Projectors, 60 Ergonomic Chairs & Tables, Power extension cords',
    specialNotes: 'Participants will bring institutional laptops. Stable uninterrupted broadband required.',
    stage: 'stage3', // Endorsed & Recommended, awaiting Stage 3 Clearance!
    stage1By: 'Division Chief',
    stage1Date: 'Oct 04, 2026 09:15 AM',
    stage1Remarks: 'Endorsed for technical digital agriculture training.',
    stage2By: 'Recommending Officer',
    stage2Date: 'Oct 05, 2026 02:40 PM',
    stage2Remarks: 'Logistics and equipment verified available. Recommended for final clearance.',
    stage3By: null,
    stage3Date: null,
    stage3Remarks: null,
    urgency: 'normal',
    dateFiled: '2026-10-04'
  },
  'ATI-2026-0888': {
    ref: 'ATI-2026-0888',
    applicant: 'Grace Valenzuela',
    agency: 'Department of Agriculture - National Rice Program',
    email: 'gvalenzuela@da.gov.ph',
    phone: '+63 928 333 7788',
    venue: 'Serrano Function Hall',
    venueCategory: 'halls',
    dates: 'October 06 – 07, 2026',
    timeSlot: '8:00 AM – 5:00 PM (2 Days)',
    pax: 100,
    eventTitle: 'Q4 National Rice Assessment & Strategic Planning Workshop',
    equipment: 'Full Audio-Visual Setup, Live Stream Equipment, VIP Holding Area',
    specialNotes: 'Keynote by DA Undersecretary. Security detail coordination completed.',
    stage: 'approved',
    stage1By: 'Division Chief',
    stage1Date: 'Oct 02, 2026 9:30 AM',
    stage1Remarks: 'Endorsed for priority scheduling.',
    stage2By: 'Recommending Officer',
    stage2Date: 'Oct 03, 2026 11:00 AM',
    stage2Remarks: 'Logistics verified. Recommended.',
    stage3By: 'Clearance Authority',
    stage3Date: 'Oct 03, 2026 4:15 PM',
    stage3Remarks: 'Approved for institutional hosting. Gate pass permit #2026-0888 issued.',
    urgency: 'normal',
    dateFiled: '2026-09-28'
  }
};

// Global State
let activeRole = 'all'; // 'all' | 'endorsement' | 'recommendation' | 'clearance'
let activeFilterTab = 'awaiting'; // 'awaiting' | 'stage1' | 'stage2' | 'stage3' | 'approved' | 'rejected' | 'all'
let selectedRefIds = new Set();
let currentDossierRef = null;
let currentDisapproveRef = null;

// Initialize on Load
document.addEventListener('DOMContentLoaded', function () {
  initRoleFromURL();
  initDateDisplay();
  renderApprovalsTable();
  updateKPICounters();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }
});

function initRoleFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  const roleParam = urlParams.get('role');
  if (['endorsement', 'recommendation', 'clearance', 'all'].includes(roleParam)) {
    activeRole = roleParam;
  } else {
    activeRole = 'all';
  }
  updatePersonaBannerUI();
}

function switchAdminRole(role) {
  activeRole = role;
  const url = new URL(window.location.href);
  url.searchParams.set('role', role);
  window.history.replaceState({}, '', url);

  updatePersonaBannerUI();
  renderApprovalsTable();
  updateKPICounters();

  let name = 'All Stages';
  if (activeRole === 'endorsement') name = 'Stage 1: Endorsement';
  else if (activeRole === 'recommendation') name = 'Stage 2: Recommendation';
  else if (activeRole === 'clearance') name = 'Stage 3: Clearance';

  showAdminToast(`Switched active governance role to: ${name}`, 'info');
}

function updatePersonaBannerUI() {
  const btnAll = document.getElementById('btnRoleAll');
  const btnEnd = document.getElementById('btnRoleEnd');
  const btnRec = document.getElementById('btnRoleRec');
  const btnClear = document.getElementById('btnRoleClear');

  if (btnAll) btnAll.className = 'btn-persona-toggle' + (activeRole === 'all' ? ' active' : '');
  if (btnEnd) btnEnd.className = 'btn-persona-toggle' + (activeRole === 'endorsement' ? ' active' : '');
  if (btnRec) btnRec.className = 'btn-persona-toggle' + (activeRole === 'recommendation' ? ' active' : '');
  if (btnClear) btnClear.className = 'btn-persona-toggle' + (activeRole === 'clearance' ? ' active director' : '');

  // Update floating bulk buttons based on active role
  const btnBulkEnd = document.getElementById('btnBulkEndorse');
  const btnBulkRec = document.getElementById('btnBulkRecommend');
  const btnBulkClr = document.getElementById('btnBulkClearance');

  if (btnBulkEnd && btnBulkRec && btnBulkClr) {
    if (activeRole === 'endorsement') {
      btnBulkEnd.style.display = 'inline-flex';
      btnBulkRec.style.display = 'none';
      btnBulkClr.style.display = 'none';
    } else if (activeRole === 'recommendation') {
      btnBulkEnd.style.display = 'none';
      btnBulkRec.style.display = 'inline-flex';
      btnBulkClr.style.display = 'none';
    } else if (activeRole === 'clearance') {
      btnBulkEnd.style.display = 'none';
      btnBulkRec.style.display = 'none';
      btnBulkClr.style.display = 'inline-flex';
    } else {
      btnBulkEnd.style.display = 'inline-flex';
      btnBulkRec.style.display = 'inline-flex';
      btnBulkClr.style.display = 'inline-flex';
    }
  }
}

function initDateDisplay() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (dateEl) {
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }
}

function toggleSidebar(forceState) {
  const sidebar = document.getElementById('adminSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const layout = document.querySelector('.admin-layout-container');
  if (!sidebar) return;

  if (window.innerWidth <= 960) {
    const willOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('open');
    if (willOpen) {
      sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('show');
    } else {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('show');
    }
  } else {
    if (layout) {
      layout.classList.toggle('sidebar-collapsed');
      localStorage.setItem('admin_sidebar_collapsed', layout.classList.contains('sidebar-collapsed'));
    }
  }
}

/* ==========================================================================
   RENDER TABLE & FILTERING (3-STAGE GOVERNANCE: Endorse -> Recommend -> Clearance)
   ========================================================================== */
function renderApprovalsTable() {
  const tbody = document.getElementById('approvalsTableBody');
  if (!tbody) return;

  const searchQuery = (document.getElementById('approvalsSearchInput')?.value || '').toLowerCase().trim();
  const venueFilter = document.getElementById('approvalsVenueFilter')?.value || 'all';

  const rows = [];
  const entries = Object.values(approvalsData);

  entries.forEach(item => {
    // Stage Filter matching
    if (activeFilterTab === 'awaiting') {
      if (activeRole === 'endorsement' && item.stage !== 'stage1') return;
      if (activeRole === 'recommendation' && item.stage !== 'stage2') return;
      if (activeRole === 'clearance' && item.stage !== 'stage3') return;
      if (activeRole === 'all' && !['stage1', 'stage2', 'stage3'].includes(item.stage)) return;
    } else if (activeFilterTab === 'stage1' && item.stage !== 'stage1') {
      return;
    } else if (activeFilterTab === 'stage2' && item.stage !== 'stage2') {
      return;
    } else if (activeFilterTab === 'stage3' && item.stage !== 'stage3') {
      return;
    } else if (activeFilterTab === 'approved' && item.stage !== 'approved') {
      return;
    } else if (activeFilterTab === 'rejected' && item.stage !== 'rejected') {
      return;
    }

    // Venue filter matching
    if (venueFilter !== 'all' && item.venueCategory !== venueFilter) {
      return;
    }

    // Search query matching
    if (searchQuery) {
      const match = (
        (item.ref || '').toLowerCase().includes(searchQuery) ||
        (item.applicant || '').toLowerCase().includes(searchQuery) ||
        (item.agency || '').toLowerCase().includes(searchQuery) ||
        (item.venue || '').toLowerCase().includes(searchQuery) ||
        (item.eventTitle || '').toLowerCase().includes(searchQuery)
      );
      if (!match) return;
    }

    rows.push(item);
  });

  if (rows.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: #637f6f;">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9bb5a5" stroke-width="1.5" style="margin-bottom: 0.5rem;"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
          <p style="font-size: 0.95rem; font-weight: 700; color: #244633; margin: 0;">No reservation requests found matching current filter.</p>
          <small style="color: #637f6f;">All pending matters for this view have been processed or moved.</small>
        </td>
      </tr>
    `;
    updateBulkActionBar();
    return;
  }

  // Sort: Urgent first, then ID / ref descending
  rows.sort((a, b) => {
    if (a.urgency === 'urgent' && b.urgency !== 'urgent') return -1;
    if (b.urgency === 'urgent' && a.urgency !== 'urgent') return 1;
    return b.ref.localeCompare(a.ref);
  });

  tbody.innerHTML = rows.map(item => {
    const isChecked = selectedRefIds.has(item.ref);
    const urgencyBadge = item.urgency === 'urgent'
      ? `<span class="urgency-badge urgent"><svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99zM11 11h2v4h-2zm0 6h2v2h-2z"/></svg> Urgent (&lt;48h)</span>`
      : `<span class="urgency-badge normal">Standard</span>`;

    // 3-Stage Stepper HTML
    let step1Class = 'stage-step-item';
    let step2Class = 'stage-step-item';
    let step3Class = 'stage-step-item';
    let line1Class = 'step-connector-line';
    let line2Class = 'step-connector-line';

    if (item.stage === 'stage1') {
      step1Class += ' active';
    } else if (item.stage === 'stage2') {
      step1Class += ' completed';
      step2Class += ' active';
      line1Class += ' done';
    } else if (item.stage === 'stage3') {
      step1Class += ' completed';
      step2Class += ' completed';
      step3Class += ' active';
      line1Class += ' done';
      line2Class += ' done';
    } else if (item.stage === 'approved') {
      step1Class += ' completed';
      step2Class += ' completed';
      step3Class += ' completed';
      line1Class += ' done';
      line2Class += ' done';
    } else if (item.stage === 'rejected') {
      step1Class += ' rejected';
      step2Class += ' rejected';
      step3Class += ' rejected';
    }

    // Action buttons based on current stage and activeRole
    let actionButtonsHtml = '';
    if (item.stage === 'stage1') {
      if (activeRole === 'endorsement' || activeRole === 'all') {
        actionButtonsHtml = `
          <button type="button" class="btn-card-action" style="background:#d97706; color:#fff;" onclick="quickEndorseStage1('${item.ref}')" title="Issue Official Endorsement">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Endorse</span>
          </button>
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')" title="Return / Decline">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        `;
      } else {
        actionButtonsHtml = `<span style="font-size:0.75rem; color:#d97706; font-weight:700;">Awaiting Endorsement</span>`;
      }
    } else if (item.stage === 'stage2') {
      if (activeRole === 'recommendation' || activeRole === 'all') {
        actionButtonsHtml = `
          <button type="button" class="btn-card-action" style="background:#2563eb; color:#fff;" onclick="quickRecommendStage2('${item.ref}')" title="Issue Logistics Recommendation">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Recommend</span>
          </button>
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')" title="Return / Decline">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        `;
      } else {
        actionButtonsHtml = `<span style="font-size:0.75rem; color:#2563eb; font-weight:700;">Awaiting Recommendation</span>`;
      }
    } else if (item.stage === 'stage3') {
      if (activeRole === 'clearance' || activeRole === 'all') {
        actionButtonsHtml = `
          <button type="button" class="btn-card-action" style="background:#10b981; color:#fff;" onclick="quickGrantClearanceStage3('${item.ref}')" title="Grant Directorial Clearance & Permit">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Clearance</span>
          </button>
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')" title="Withhold / Disapprove">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        `;
      } else {
        actionButtonsHtml = `<span style="font-size:0.75rem; color:#10b981; font-weight:700;">Awaiting Clearance</span>`;
      }
    } else if (item.stage === 'approved') {
      actionButtonsHtml = `
        <button type="button" class="btn-card-action" onclick="previewPermitDocument('${item.ref}')" title="View Official Permit & Gate Pass">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
          <span>Permit</span>
        </button>
      `;
    } else {
      actionButtonsHtml = `
        <span style="font-size:0.75rem; color:#dc2626; font-weight:700;">Returned/Declined</span>
      `;
    }

    const applicantInitial = (item.applicant || 'ATI').split(' ').map(n=>n[0]).slice(0,2).join('');

    return `
      <tr class="${isChecked ? 'row-selected' : ''}" data-ref="${item.ref}">
        <td style="width: 40px; text-align: center;">
          <input type="checkbox" class="row-selector-checkbox" value="${item.ref}" ${isChecked ? 'checked' : ''} onchange="toggleSelectRow('${item.ref}', this.checked)">
        </td>
        <td>
          <div style="display: flex; flex-direction: column; gap: 0.2rem;">
            <strong style="color: #174d2f; font-family: monospace; font-size: 0.9rem;">${item.ref}</strong>
            <div style="display: flex; gap: 0.35rem; align-items: center;">
              ${urgencyBadge}
            </div>
          </div>
        </td>
        <td>
          <div class="facility-table-info-cell" style="gap: 0.65rem;">
            <div class="user-avatar-circle" style="background:#246a42; color:#fff; width:34px; height:34px; font-size:0.78rem;">
              ${applicantInitial}
            </div>
            <div class="facility-table-info-text">
              <strong>${item.applicant}</strong>
              <span style="color:#577161; font-size:0.76rem;">${item.agency}</span>
            </div>
          </div>
        </td>
        <td>
          <div style="display: flex; flex-direction: column; gap: 0.15rem;">
            <strong style="color: #153321;">${item.venue}</strong>
            <span style="color: #637f6f; font-size: 0.78rem;">${item.pax} PAX</span>
          </div>
        </td>
        <td>
          <div style="display: flex; flex-direction: column; gap: 0.15rem;">
            <span style="font-weight: 700; color: #172c1f;">${item.dates}</span>
            <span style="color: #637f6f; font-size: 0.76rem;">${item.timeSlot}</span>
          </div>
        </td>
        <td>
          <div class="stage-stepper">
            <div class="${step1Class}">
              <div class="step-node">1</div>
              <span class="step-caption">1. Endorse</span>
            </div>
            <div class="${line1Class}"></div>
            <div class="${step2Class}">
              <div class="step-node">2</div>
              <span class="step-caption">2. Recommend</span>
            </div>
            <div class="${line2Class}"></div>
            <div class="${step3Class}">
              <div class="step-node">3</div>
              <span class="step-caption">3. Clearance</span>
            </div>
          </div>
        </td>
        <td style="text-align: right; white-space: nowrap;">
          <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
            ${actionButtonsHtml}
            <button type="button" class="btn-card-action" onclick="openDossierModal('${item.ref}')" title="Inspect Full Dossier">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <span>Dossier</span>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join('');

  updateBulkActionBar();
}

function applyApprovalsFilterTab(tab) {
  activeFilterTab = tab;
  renderApprovalsTable();
}

/* ==========================================================================
   MULTI-ROW SELECTION & BULK ACTIONS
   ========================================================================== */
function toggleSelectAllRows(checked) {
  const checkboxes = document.querySelectorAll('.row-selector-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = checked;
    if (checked) {
      selectedRefIds.add(cb.value);
    } else {
      selectedRefIds.delete(cb.value);
    }
  });
  renderApprovalsTable();
}

function toggleSelectRow(ref, checked) {
  if (checked) {
    selectedRefIds.add(ref);
  } else {
    selectedRefIds.delete(ref);
  }
  updateBulkActionBar();
}

function updateBulkActionBar() {
  const bar = document.getElementById('bulkActionsBar');
  const countBadge = document.getElementById('bulkSelectedCount');
  const count = selectedRefIds.size;

  if (count > 0) {
    if (countBadge) countBadge.textContent = count;
    if (bar) bar.classList.add('visible');
  } else {
    if (bar) bar.classList.remove('visible');
  }
}

function clearRowSelections() {
  selectedRefIds.clear();
  const selectAllCb = document.getElementById('selectAllRowsCb');
  if (selectAllCb) selectAllCb.checked = false;
  renderApprovalsTable();
}

/* ==========================================================================
   BACKEND API DISPATCHER (AJAX TO update_approval_stage.php)
   ========================================================================== */
async function callApprovalApi(formData) {
  try {
    const response = await fetch('ajax/update_approval_stage.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams(formData).toString()
    });
    const result = await response.json();
    return result;
  } catch (err) {
    console.error('Approval API Error:', err);
    return { success: false, message: 'Server communication error. Please check your connection.' };
  }
}

/* ==========================================================================
   BATCH / BULK ACTIONS
   ========================================================================== */
async function executeBulkEndorse() {
  if (selectedRefIds.size === 0) return;
  const refsArray = Array.from(selectedRefIds).filter(ref => approvalsData[ref] && approvalsData[ref].stage === 'stage1');
  if (refsArray.length === 0) {
    showAdminToast('None of the selected requests are currently awaiting Stage 1 Endorsement.', 'info');
    return;
  }

  const res = await callApprovalApi({
    action: 'bulk_endorse',
    refs: refsArray.join(','),
    remarks: 'Batch endorsed by Division Head / Endorsing Officer.'
  });

  if (res.success) {
    refsArray.forEach(ref => {
      const item = approvalsData[ref];
      if (item) {
        item.stage = 'stage2';
        item.stage1By = res.stage1By || 'Endorsing Officer';
        item.stage1Date = res.stage1Date || new Date().toLocaleString();
        item.stage1Remarks = 'Batch endorsed by Division Head.';
      }
    });

    clearRowSelections();
    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Batch endorsement completed: ${refsArray.length} requests forwarded for Recommendation.`, 'success');
  } else {
    showAdminToast(res.message || 'Batch endorsement failed.', 'decline');
  }
}

async function executeBulkRecommend() {
  if (selectedRefIds.size === 0) return;
  const refsArray = Array.from(selectedRefIds).filter(ref => approvalsData[ref] && approvalsData[ref].stage === 'stage2');
  if (refsArray.length === 0) {
    showAdminToast('None of the selected requests are currently awaiting Stage 2 Recommendation.', 'info');
    return;
  }

  const res = await callApprovalApi({
    action: 'bulk_recommend',
    refs: refsArray.join(','),
    remarks: 'Batch logistics verified and recommended for Stage 3 Clearance.'
  });

  if (res.success) {
    refsArray.forEach(ref => {
      const item = approvalsData[ref];
      if (item) {
        item.stage = 'stage3';
        item.stage2By = res.stage2By || 'Recommending Officer';
        item.stage2Date = res.stage2Date || new Date().toLocaleString();
        item.stage2Remarks = 'Batch logistics verified. Recommended.';
      }
    });

    clearRowSelections();
    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Batch recommendation completed: ${refsArray.length} requests forwarded for Clearance.`, 'success');
  } else {
    showAdminToast(res.message || 'Batch recommendation failed.', 'decline');
  }
}

async function executeBulkClearance() {
  if (selectedRefIds.size === 0) return;
  const refsArray = Array.from(selectedRefIds).filter(ref => approvalsData[ref] && approvalsData[ref].stage === 'stage3');
  if (refsArray.length === 0) {
    showAdminToast('None of the selected requests are currently awaiting Stage 3 Clearance.', 'info');
    return;
  }

  const res = await callApprovalApi({
    action: 'bulk_clearance',
    refs: refsArray.join(','),
    remarks: 'Batch executive clearance granted and permits authorized.'
  });

  if (res.success) {
    refsArray.forEach(ref => {
      const item = approvalsData[ref];
      if (item) {
        item.stage = 'approved';
        item.stage3By = res.stage3By || 'Clearance Authority';
        item.stage3Date = res.stage3Date || new Date().toLocaleString();
        item.stage3Remarks = 'Batch final executive clearance granted.';
      }
    });

    clearRowSelections();
    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Batch final clearance granted: ${refsArray.length} institutional permits issued.`, 'success');
  } else {
    showAdminToast(res.message || 'Batch clearance failed.', 'decline');
  }
}

/* ==========================================================================
   QUICK ROW ACTIONS (STAGE 1, STAGE 2, STAGE 3)
   ========================================================================== */
async function quickEndorseStage1(ref) {
  const item = approvalsData[ref];
  if (!item) return;

  const res = await callApprovalApi({
    action: 'stage1',
    ref: ref
  });

  if (res.success) {
    item.stage = 'stage2';
    item.stage1By = res.stage1By || 'Endorsing Officer';
    item.stage1Date = res.stage1Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    item.stage1Remarks = res.stage1Remarks || 'Official endorsement issued.';

    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Stage 1 Endorsement recorded for ${ref}. Forwarded to Recommendation.`, 'success');
  } else {
    showAdminToast(res.message || 'Failed to record endorsement.', 'decline');
  }
}

async function quickRecommendStage2(ref) {
  const item = approvalsData[ref];
  if (!item) return;

  const res = await callApprovalApi({
    action: 'stage2',
    ref: ref
  });

  if (res.success) {
    item.stage = 'stage3';
    item.stage2By = res.stage2By || 'Recommending Officer';
    item.stage2Date = res.stage2Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    item.stage2Remarks = res.stage2Remarks || 'Verified logistics. Recommended for final clearance.';

    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Stage 2 Recommendation recorded for ${ref}. Forwarded to Final Clearance.`, 'success');
  } else {
    showAdminToast(res.message || 'Failed to record recommendation.', 'decline');
  }
}

async function quickGrantClearanceStage3(ref) {
  const item = approvalsData[ref];
  if (!item) return;

  const res = await callApprovalApi({
    action: 'stage3',
    ref: ref
  });

  if (res.success) {
    item.stage = 'approved';
    item.stage3By = res.stage3By || 'Clearance Authority';
    item.stage3Date = res.stage3Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    item.stage3Remarks = res.stage3Remarks || 'Official approval granted. Permit issued.';

    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(res.message || `Stage 3 Final Clearance granted for ${ref}! Permit generated.`, 'success');
  } else {
    showAdminToast(res.message || 'Failed to grant clearance.', 'decline');
  }
}

/* ==========================================================================
   DOSSIER INSPECTOR MODAL
   ========================================================================== */
function openDossierModal(ref) {
  const item = approvalsData[ref];
  if (!item) return;

  currentDossierRef = ref;

  document.getElementById('dossierModalRef').textContent = item.ref;
  document.getElementById('dossierApplicantName').textContent = item.applicant;
  document.getElementById('dossierApplicantAgency').textContent = item.agency;
  document.getElementById('dossierContact').textContent = `${item.email} • ${item.phone}`;
  document.getElementById('dossierVenue').textContent = item.venue;
  document.getElementById('dossierPax').textContent = `${item.pax} Participants`;
  document.getElementById('dossierDates').textContent = item.dates;
  document.getElementById('dossierTimeSlot').textContent = item.timeSlot;
  document.getElementById('dossierEventTitle').textContent = item.eventTitle;
  document.getElementById('dossierEquipment').textContent = item.equipment;
  document.getElementById('dossierNotes').textContent = item.specialNotes || 'None specified.';

  // Supporting Document Attachment
  const docContainerNone = document.getElementById('dossierDocNone');
  const docLink = document.getElementById('dossierDocLink');
  const docName = document.getElementById('dossierDocName');

  if (item.documentPath) {
    if (docContainerNone) docContainerNone.style.display = 'none';
    if (docLink) {
      docLink.style.display = 'inline-flex';
      docLink.href = item.documentPath;
      const fileName = item.documentPath.split('/').pop();
      if (docName) docName.textContent = `Open Official Document (${fileName})`;
    }
  } else {
    if (docContainerNone) docContainerNone.style.display = 'block';
    if (docLink) docLink.style.display = 'none';
  }

  // 3-Stage Timeline rendering (Submitted -> Endorsed -> Recommended -> Cleared)
  const timelineEl = document.getElementById('dossierTimelineList');
  if (timelineEl) {
    let timelineHtml = `
      <div style="display:flex; gap:0.85rem; margin-bottom:1rem;">
        <div style="width:28px; height:28px; border-radius:50%; background:#174d2f; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">✓</div>
        <div>
          <strong style="display:block; font-size:0.85rem; color:#174d2f;">Reservation Request Submitted</strong>
          <small style="color:#637f6f;">Filed on ${item.dateFiled} by ${item.applicant}</small>
        </div>
      </div>
    `;

    // 1. Endorsement Milestone
    if (item.stage1By) {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem; margin-bottom:1rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#d97706; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">✓</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#d97706;">Stage 1: Endorsed by ${item.stage1By}</strong>
            <small style="color:#637f6f;">${item.stage1Date} • "${item.stage1Remarks || 'Endorsed'}"</small>
          </div>
        </div>
      `;
    } else {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem; margin-bottom:1rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#e5e7eb; color:#6b7280; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">1</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#4b5563;">Stage 1: Awaiting Endorsement</strong>
            <small style="color:#9ca3af;">Pending action by Division / Originating Unit Head</small>
          </div>
        </div>
      `;
    }

    // 2. Recommendation Milestone
    if (item.stage2By) {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem; margin-bottom:1rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">✓</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#2563eb;">Stage 2: Recommended by ${item.stage2By}</strong>
            <small style="color:#637f6f;">${item.stage2Date} • "${item.stage2Remarks || 'Recommended'}"</small>
          </div>
        </div>
      `;
    } else {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem; margin-bottom:1rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#e5e7eb; color:#6b7280; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">2</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#4b5563;">Stage 2: Awaiting Recommendation</strong>
            <small style="color:#9ca3af;">Pending review by Administrative / Facility Section</small>
          </div>
        </div>
      `;
    }

    // 3. Clearance Milestone
    if (item.stage3By) {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">✓</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#10b981;">Stage 3: Final Clearance Granted</strong>
            <small style="color:#637f6f;">${item.stage3Date} • Cleared by ${item.stage3By}</small>
          </div>
        </div>
      `;
    } else {
      timelineHtml += `
        <div style="display:flex; gap:0.85rem;">
          <div style="width:28px; height:28px; border-radius:50%; background:#e5e7eb; color:#6b7280; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; flex-shrink:0;">3</div>
          <div>
            <strong style="display:block; font-size:0.85rem; color:#4b5563;">Stage 3: Awaiting Final Clearance</strong>
            <small style="color:#9ca3af;">Pending executive clearance from Office of the Director</small>
          </div>
        </div>
      `;
    }

    timelineEl.innerHTML = timelineHtml;
  }

  // Interactive Action Decision Section
  const actionBox = document.getElementById('dossierDecisionBox');
  if (actionBox) {
    if (item.stage === 'stage1') {
      actionBox.className = 'action-decision-box stage1';
      actionBox.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.92rem; color: #b45309;">Stage 1: Division Endorsement Desk</h4>
        <p style="margin: 0 0 0.75rem 0; font-size: 0.8rem; color: #657b6f;">Verify originating division mandate, participant alignment, and issue official endorsement for administrative review.</p>
        <textarea id="dossierRemarksInput" rows="2" placeholder="Endorsement remarks (Official division activity, documents verified)..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.85rem; margin-bottom: 0.75rem;"></textarea>
        <div style="display: flex; gap: 0.6rem; justify-content: flex-end;">
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')">Return / Decline</button>
          <button type="button" class="btn-system-primary" style="background:#d97706;" onclick="submitDossierAction('stage1')">Issue Endorsement</button>
        </div>
      `;
    } else if (item.stage === 'stage2') {
      actionBox.className = 'action-decision-box stage1';
      actionBox.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.92rem; color: #2563eb;">Stage 2: Administrative Logistics Recommendation</h4>
        <p style="margin: 0 0 0.75rem 0; font-size: 0.8rem; color: #657b6f;">Verify calendar conflicts, facility capacity, equipment availability, and submit official recommendation for Directorate clearance.</p>
        <textarea id="dossierRemarksInput" rows="2" placeholder="Recommendation remarks (Logistics verified, room and A/V equipment ready)..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.85rem; margin-bottom: 0.75rem;"></textarea>
        <div style="display: flex; gap: 0.6rem; justify-content: flex-end;">
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')">Return / Decline</button>
          <button type="button" class="btn-system-primary" style="background:#2563eb;" onclick="submitDossierAction('stage2')">Issue Recommendation</button>
        </div>
      `;
    } else if (item.stage === 'stage3') {
      actionBox.className = 'action-decision-box stage2';
      actionBox.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0; font-size: 0.92rem; color: #047857;">Stage 3: Directorate Final Clearance & Permit Issuance</h4>
        <p style="margin: 0 0 0.75rem 0; font-size: 0.8rem; color: #657b6f;">Endorsed and recommended by units. Grant final executive clearance and authorize entry permits & gate passes.</p>
        <textarea id="dossierRemarksInput" rows="2" placeholder="Directorate clearance remarks & special conditions for permit..." style="width: 100%; padding: 0.65rem; border: 1.5px solid #dce8e0; border-radius: 8px; font-family: inherit; font-size: 0.85rem; margin-bottom: 0.75rem;"></textarea>
        <div style="display: flex; gap: 0.6rem; justify-content: flex-end;">
          <button type="button" class="btn-card-action" onclick="openDisapproveModal('${item.ref}')">Withhold Clearance</button>
          <button type="button" class="btn-system-primary" style="background:#10b981;" onclick="submitDossierAction('stage3')">Grant Final Clearance & Issue Permit</button>
        </div>
      `;
    } else {
      actionBox.className = 'action-decision-box';
      actionBox.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <strong style="color: #174d2f;">Booking Governance Finalized</strong>
            <p style="margin:0.2rem 0 0 0; font-size:0.8rem; color:#637f6f;">This booking is marked as ${item.stage.toUpperCase()}.</p>
          </div>
          ${item.stage === 'approved' ? `<button type="button" class="btn-system-secondary" onclick="previewPermitDocument('${item.ref}')">View Official Permit</button>` : ''}
        </div>
      `;
    }
  }

  const modal = document.getElementById('dossierModal');
  if (modal) modal.style.display = 'flex';
}

function closeDossierModal() {
  const modal = document.getElementById('dossierModal');
  if (modal) modal.style.display = 'none';
  currentDossierRef = null;
}

async function submitDossierAction(actionType) {
  if (!currentDossierRef || !approvalsData[currentDossierRef]) return;
  const item = approvalsData[currentDossierRef];
  const remarksInput = document.getElementById('dossierRemarksInput');
  const remarks = remarksInput ? remarksInput.value.trim() : '';

  const res = await callApprovalApi({
    action: actionType,
    ref: item.ref,
    remarks: remarks
  });

  if (res.success) {
    if (actionType === 'stage1') {
      item.stage = 'stage2';
      item.stage1By = res.stage1By || 'Endorsing Officer';
      item.stage1Date = res.stage1Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
      item.stage1Remarks = res.stage1Remarks || remarks || 'Endorsed for Stage 2 Recommendation.';
      showAdminToast(`Stage 1 Endorsement saved for ${item.ref}.`, 'success');
    } else if (actionType === 'stage2') {
      item.stage = 'stage3';
      item.stage2By = res.stage2By || 'Recommending Officer';
      item.stage2Date = res.stage2Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
      item.stage2Remarks = res.stage2Remarks || remarks || 'Recommended for Stage 3 Final Clearance.';
      showAdminToast(`Stage 2 Recommendation saved for ${item.ref}.`, 'success');
    } else if (actionType === 'stage3') {
      item.stage = 'approved';
      item.stage3By = res.stage3By || 'Clearance Authority';
      item.stage3Date = res.stage3Date || new Date().toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
      item.stage3Remarks = res.stage3Remarks || remarks || 'Final clearance granted. Permit issued.';
      showAdminToast(`Stage 3 Final Clearance granted for ${item.ref}! Permit generated.`, 'success');
    }

    closeDossierModal();
    updateKPICounters();
    renderApprovalsTable();
  } else {
    showAdminToast(res.message || 'Action failed. Please try again.', 'decline');
  }
}

/* ==========================================================================
   DISAPPROVAL / RETURN FOR REVISION MODAL
   ========================================================================== */
function openDisapproveModal(ref) {
  currentDisapproveRef = ref;
  const refEl = document.getElementById('disapproveRef');
  if (refEl) refEl.textContent = ref;

  const modal = document.getElementById('disapproveModal');
  if (modal) modal.style.display = 'flex';
}

function closeDisapproveModal() {
  const modal = document.getElementById('disapproveModal');
  if (modal) modal.style.display = 'none';
  currentDisapproveRef = null;
}

async function confirmDisapproval() {
  if (!currentDisapproveRef || !approvalsData[currentDisapproveRef]) return;
  const item = approvalsData[currentDisapproveRef];
  const reasonSelect = document.getElementById('disapproveReasonSelect');
  const remarksInput = document.getElementById('disapproveRemarks');

  const reason = reasonSelect ? reasonSelect.value : 'Administrative Grounds';
  const notes = remarksInput ? remarksInput.value.trim() : '';

  const res = await callApprovalApi({
    action: 'reject',
    ref: item.ref,
    reason: reason,
    remarks: notes
  });

  if (res.success) {
    item.stage = 'rejected';
    item.rejectionGround = reason;
    item.rejectionNotes = notes;

    closeDisapproveModal();
    closeDossierModal();
    updateKPICounters();
    renderApprovalsTable();
    showAdminToast(`Request ${item.ref} has been returned/declined (${reason}). Official notice logged in audit trail.`, 'decline');
  } else {
    showAdminToast(res.message || 'Failed to decline request.', 'decline');
  }
}

/* ==========================================================================
   OFFICIAL PERMIT DOCUMENT PREVIEW & PRINT
   ========================================================================== */
function previewPermitDocument(ref) {
  const item = approvalsData[ref];
  if (!item) return;

  document.getElementById('permitModalRef').textContent = item.ref;
  document.getElementById('permitApplicant').textContent = item.applicant;
  document.getElementById('permitAgency').textContent = item.agency;
  document.getElementById('permitVenue').textContent = item.venue;
  document.getElementById('permitSchedule').textContent = `${item.dates} (${item.timeSlot})`;
  document.getElementById('permitPax').textContent = `${item.pax} Participants`;
  document.getElementById('permitEventTitle').textContent = item.eventTitle;
  document.getElementById('permitIssueDate').textContent = item.stage3Date || item.stage2Date || new Date().toLocaleDateString('en-US');

  // Populate Signatories
  const endorserEl = document.getElementById('permitEndorserName');
  const recommenderEl = document.getElementById('permitRecommenderName');
  const clearerEl = document.getElementById('permitClearerName');

  if (endorserEl) endorserEl.textContent = item.stage1By ? item.stage1By.toUpperCase() : 'ENDORSING OFFICER';
  if (recommenderEl) recommenderEl.textContent = item.stage2By ? item.stage2By.toUpperCase() : 'RECOMMENDING OFFICER';
  if (clearerEl) clearerEl.textContent = item.stage3By ? item.stage3By.toUpperCase() : 'CLEARANCE AUTHORITY';

  const modal = document.getElementById('permitPreviewModal');
  if (modal) modal.style.display = 'flex';
}

function closePermitModal() {
  const modal = document.getElementById('permitPreviewModal');
  if (modal) modal.style.display = 'none';
}

function printPermitDocument() {
  window.print();
}

/* ==========================================================================
   KPI COUNTER UPDATER (3-STAGE GOVERNANCE)
   ========================================================================== */
function updateKPICounters() {
  const items = Object.values(approvalsData);
  let awaitingCount = 0;
  let stage1Count = 0;
  let stage2Count = 0;
  let stage3Count = 0;
  let approvedCount = 0;
  let rejectedCount = 0;

  items.forEach(it => {
    if (it.stage === 'stage1') stage1Count++;
    if (it.stage === 'stage2') stage2Count++;
    if (it.stage === 'stage3') stage3Count++;
    if (it.stage === 'approved') approvedCount++;
    if (it.stage === 'rejected') rejectedCount++;
  });

  if (activeRole === 'endorsement') {
    awaitingCount = stage1Count;
  } else if (activeRole === 'recommendation') {
    awaitingCount = stage2Count;
  } else if (activeRole === 'clearance') {
    awaitingCount = stage3Count;
  } else {
    awaitingCount = stage1Count + stage2Count + stage3Count;
  }

  const kpiAwaiting = document.getElementById('kpiAwaitingCount');
  const kpiStage1 = document.getElementById('kpiStage1Count');
  const kpiStage2 = document.getElementById('kpiStage2Count');
  const kpiStage3 = document.getElementById('kpiStage3Count');
  const kpiApproved = document.getElementById('kpiApprovedCount');
  const sidebarBadge = document.getElementById('sidebarPendingCount');

  if (kpiAwaiting) kpiAwaiting.textContent = awaitingCount;
  if (kpiStage1) kpiStage1.textContent = stage1Count;
  if (kpiStage2) kpiStage2.textContent = stage2Count;
  if (kpiStage3) kpiStage3.textContent = stage3Count;
  if (kpiApproved) kpiApproved.textContent = approvedCount;
  if (sidebarBadge) sidebarBadge.textContent = (stage1Count + stage2Count + stage3Count);
}

/* ==========================================================================
   TOAST NOTIFICATIONS
   ========================================================================== */
function showAdminToast(msg, type = 'info') {
  const container = document.getElementById('adminToastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `admin-toast toast-${type}`;
  toast.innerHTML = `
    <div class="toast-indicator"></div>
    <div class="toast-body">
      <strong>${type === 'success' ? 'Action Completed' : (type === 'decline' ? 'Notice Logged' : 'Notification')}</strong>
      <p>${msg}</p>
    </div>
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}
