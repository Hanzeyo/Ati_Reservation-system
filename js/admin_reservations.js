/**
 * Agriculture Training Institute - Super Admin Dashboard Script
 * Unified ("Terno") Interactive Controller
 */

// Initial Dataset of Reservations (loaded dynamically from MySQL via PHP)
const reservationsData = (window.initialReservationsData && typeof window.initialReservationsData === 'object' && !Array.isArray(window.initialReservationsData))
  ? Object.assign({}, window.initialReservationsData)
  : {};

let currentDeclineTargetRef = null;
let currentModalActiveRef = null;

document.addEventListener('DOMContentLoaded', function () {
  initDateDisplay();
  initSmoothSidebarScrolling();
  updateKPIBadges();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }
});

function initSmoothSidebarScrolling() {
  document.querySelectorAll('.sidebar-menu-item[href^="#"]').forEach(link => {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href').substring(1);
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (window.innerWidth <= 960) {
          toggleSidebar(false);
        }
      }
    });
  });
}

/* ==========================================================================
   SIDEBAR TOGGLE (RESPONSIVE & DESKTOP)
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

function initDateDisplay() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (dateEl) {
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString('en-US', options);
  }
}

/* ==========================================================================
   STAGE 1: ENDORSE RESERVATION ACTION
   ========================================================================== */
async function endorseReservation(refId, applicantName) {
  const row = document.querySelector(`tr[data-ref="${refId}"]`);
  if (!row) return;

  const btnEndorse = row.querySelector('.btn-table-action.endorse');
  if (btnEndorse) {
    btnEndorse.disabled = true;
    btnEndorse.innerHTML = `<span>Endorsing...</span>`;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'stage1');
    formData.append('ref', refId);
    formData.append('remarks', 'Official endorsement issued via All Reservations desk.');

    const res = await fetch('ajax/update_approval_stage.php', {
      method: 'POST',
      body: formData
    });
    const result = await res.json();

    if (!result.success) {
      showAdminToast(result.message || 'Endorsement failed.', 'decline');
      if (btnEndorse) {
        btnEndorse.disabled = false;
        btnEndorse.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Endorse</span>`;
      }
      return;
    }

    row.setAttribute('data-stage', 'stage2');
    const statusCell = row.querySelector('td:nth-child(6)');
    if (statusCell) {
      statusCell.innerHTML = `
        <span class="status-pill status-stage2">
          <span class="status-dot"></span>
          Awaiting Recommendation
        </span>
      `;
    }

    const actionsCell = row.querySelector('td:nth-child(7)');
    if (actionsCell) {
      actionsCell.innerHTML = `
        <div class="action-buttons-wrap">
          <button type="button" class="btn-table-action recommend" onclick="recommendReservation('${refId}', '${applicantName}')" title="Recommend Request (Stage 2)">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Recommend</span>
          </button>
          <button type="button" class="btn-table-action decline" onclick="openDeclineModal('${refId}', '${applicantName}')" title="Decline Request">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
          <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>
      `;
    }

    showAdminToast(result.message || `Reservation ${refId} officially endorsed!`, 'success');
  } catch (err) {
    console.error('Endorsement error:', err);
    showAdminToast('Network error while processing endorsement.', 'decline');
    if (btnEndorse) {
      btnEndorse.disabled = false;
      btnEndorse.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Endorse</span>`;
    }
  }
}

/* ==========================================================================
   STAGE 2: RECOMMEND RESERVATION ACTION
   ========================================================================== */
async function recommendReservation(refId, applicantName) {
  const row = document.querySelector(`tr[data-ref="${refId}"]`);
  if (!row) return;

  const btnRecommend = row.querySelector('.btn-table-action.recommend');
  if (btnRecommend) {
    btnRecommend.disabled = true;
    btnRecommend.innerHTML = `<span>Recommending...</span>`;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'stage2');
    formData.append('ref', refId);
    formData.append('remarks', 'Verified logistics and recommended via All Reservations desk.');

    const res = await fetch('ajax/update_approval_stage.php', {
      method: 'POST',
      body: formData
    });
    const result = await res.json();

    if (!result.success) {
      showAdminToast(result.message || 'Recommendation failed.', 'decline');
      if (btnRecommend) {
        btnRecommend.disabled = false;
        btnRecommend.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Recommend</span>`;
      }
      return;
    }

    row.setAttribute('data-stage', 'stage3');
    const statusCell = row.querySelector('td:nth-child(6)');
    if (statusCell) {
      statusCell.innerHTML = `
        <span class="status-pill status-stage3">
          <span class="status-dot"></span>
          Awaiting Clearance
        </span>
      `;
    }

    const actionsCell = row.querySelector('td:nth-child(7)');
    if (actionsCell) {
      actionsCell.innerHTML = `
        <div class="action-buttons-wrap">
          <button type="button" class="btn-table-action approve" onclick="clearanceReservation('${refId}', '${applicantName}')" title="Grant Director Clearance (Stage 3)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Approve</span>
          </button>
          <button type="button" class="btn-table-action decline" onclick="openDeclineModal('${refId}', '${applicantName}')" title="Decline Request">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
          <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>
      `;
    }

    showAdminToast(result.message || `Reservation ${refId} recommended for final clearance!`, 'success');
  } catch (err) {
    console.error('Recommendation error:', err);
    showAdminToast('Network error while processing recommendation.', 'decline');
    if (btnRecommend) {
      btnRecommend.disabled = false;
      btnRecommend.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Recommend</span>`;
    }
  }
}

/* ==========================================================================
   STAGE 3: CLEARANCE & FINAL APPROVAL
   ========================================================================== */
async function clearanceReservation(refId, applicantName, venueName) {
  const row = document.querySelector(`tr[data-ref="${refId}"]`);
  if (!row) return;

  const btnApprove = row.querySelector('.btn-table-action.approve');
  if (btnApprove) {
    btnApprove.disabled = true;
    btnApprove.innerHTML = `<span>Approving...</span>`;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'stage3');
    formData.append('ref', refId);
    formData.append('remarks', 'Directly approved by Administrator via All Reservations desk.');

    const res = await fetch('ajax/update_approval_stage.php', {
      method: 'POST',
      body: formData
    });
    const result = await res.json();

    if (!result.success) {
      showAdminToast(result.message || 'Approval failed.', 'decline');
      if (btnApprove) {
        btnApprove.disabled = false;
        btnApprove.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg><span>Approve</span>`;
      }
      return;
    }

    if (reservationsData[refId]) {
      reservationsData[refId].status = 'approved';
      reservationsData[refId].stage = 'approved';
    }

    row.setAttribute('data-status', 'approved');
    row.setAttribute('data-stage', 'approved');
    const statusCell = row.querySelector('td:nth-child(6)');
    if (statusCell) {
      statusCell.innerHTML = `
        <span class="status-pill status-approved">
          <span class="status-dot"></span>
          Confirmed & Approved
        </span>
      `;
    }

    const actionsCell = row.querySelector('td:nth-child(7)');
    if (actionsCell) {
      actionsCell.innerHTML = `
        <div class="action-buttons-wrap">
          <span class="badge-approved-note">Approved by Director</span>
          <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>
      `;
    }

    updateKPIBadges();
    showAdminToast(result.message || `Reservation ${refId} officially approved! Applicant ${applicantName} notified.`, 'success');
  } catch (err) {
    console.error('Approval error:', err);
    showAdminToast('Network error while processing approval.', 'decline');
    if (btnApprove) {
      btnApprove.disabled = false;
      btnApprove.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg><span>Approve</span>`;
    }
  }
}

// Backwards compatibility alias
const approveReservation = clearanceReservation;

/* ==========================================================================
   DECLINE MODAL & ACTION
   ========================================================================== */
function openDeclineModal(refId, applicantName) {
  currentDeclineTargetRef = refId;
  const modal = document.getElementById('declineModal');
  const modalRef = document.getElementById('declineModalRef');
  const modalApplicant = document.getElementById('declineModalApplicant');
  const remarks = document.getElementById('declineRemarks');

  if (modalRef) modalRef.textContent = refId;
  if (modalApplicant) modalApplicant.textContent = applicantName;
  if (remarks) remarks.value = '';

  if (modal) {
    modal.style.display = 'flex';
    document.body.classList.add('admin-modal-open');
  }
}

function closeDeclineModal() {
  const modal = document.getElementById('declineModal');
  if (modal) modal.style.display = 'none';
  document.body.classList.remove('admin-modal-open');
  currentDeclineTargetRef = null;
}

async function submitDeclineAction() {
  if (!currentDeclineTargetRef) return;
  const refId = currentDeclineTargetRef;
  const reasonSelect = document.getElementById('declineReasonSelect');
  const reason = reasonSelect ? reasonSelect.value : 'Administrative Grounds';
  const remarksInput = document.getElementById('declineRemarks');
  const remarks = remarksInput ? remarksInput.value.trim() : '';

  const confirmBtn = document.querySelector('#declineModal .btn-confirm-decline');
  if (confirmBtn) {
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Declining...';
  }

  try {
    const formData = new FormData();
    formData.append('action', 'reject');
    formData.append('ref', refId);
    formData.append('reason', reason);
    formData.append('remarks', remarks);

    const res = await fetch('ajax/update_approval_stage.php', {
      method: 'POST',
      body: formData
    });
    const result = await res.json();

    if (!result.success) {
      showAdminToast(result.message || 'Decline action failed.', 'decline');
      if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm & Send Notice';
      }
      return;
    }

    const row = document.querySelector(`tr[data-ref="${refId}"]`);
    if (row) {
      row.setAttribute('data-status', 'rejected');
      const statusCell = row.querySelector('td:nth-child(6)');
      if (statusCell) {
        statusCell.innerHTML = `
          <span class="status-pill status-cancelled">
            <span class="status-dot"></span>
            Declined
          </span>
        `;
      }

      const actionsCell = row.querySelector('td:nth-child(7)');
      if (actionsCell) {
        actionsCell.innerHTML = `
          <div class="action-buttons-wrap">
            <span style="font-size: 0.78rem; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 0.35rem 0.65rem; border-radius: 9999px;">Declined</span>
            <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
          </div>
        `;
      }
    }

    if (reservationsData[refId]) {
      reservationsData[refId].status = 'rejected';
    }

    closeDeclineModal();
    updateKPIBadges();
    showAdminToast(`Reservation ${refId} has been declined. Official notice sent.`, 'decline');
  } catch (err) {
    console.error('Decline error:', err);
    showAdminToast('Network error while declining reservation.', 'decline');
  } finally {
    if (confirmBtn) {
      confirmBtn.disabled = false;
      confirmBtn.textContent = 'Confirm & Send Notice';
    }
  }
}

/* ==========================================================================
   VIEW RESERVATION DETAILS MODAL
   ========================================================================== */
function viewReservationDetails(refId) {
  const data = reservationsData[refId];
  if (!data) return;

  currentModalActiveRef = refId;

  document.getElementById('modalRefId').textContent = data.ref;
  document.getElementById('modalApplicantName').textContent = data.applicant;
  document.getElementById('modalApplicantOrg').textContent = data.agency;
  document.getElementById('modalApplicantContact').textContent = `${data.email} • ${data.phone}`;
  document.getElementById('modalVenueName').textContent = data.venue;
  document.getElementById('modalSchedule').textContent = data.schedule;
  document.getElementById('modalPax').textContent = data.pax;
  document.getElementById('modalEventTitle').textContent = data.eventTitle;
  document.getElementById('modalEquipment').textContent = data.equipment;

  // Avatar initials
  const initials = (data.applicant || 'ATI').split(' ').map(n => n[0]).slice(0, 2).join('');
  document.getElementById('modalAvatar').textContent = initials;

  // =========================================================================
  // DOCUMENT HANDLING (AUTOMATICALLY POINTS TO SIGNED DOCUMENT IF PRESENT)
  // =========================================================================
  const docLink = document.getElementById('btnModalPreviewDoc');
  const docTitle = document.getElementById('modalDocTitle');
  const docSub = document.getElementById('modalDocSub');
  const noDocText = document.getElementById('modalNoDocText');
  const docLabel = document.getElementById('modalDocSectionLabel');
  const clientAltWrap = document.getElementById('modalClientDocAltWrap');
  const clientAltLink = document.getElementById('modalClientDocAltLink');

  const hasClearanceDoc = data.clearanceDocPath && data.clearanceDocPath.trim() !== '';
  const hasRecommendationDoc = data.recommendationDocPath && data.recommendationDocPath.trim() !== '';
  const hasClientDoc = data.documentPath && data.documentPath.trim() !== '';

  if (hasClearanceDoc) {
    // Stage 3 / Approved Signed Permit Document
    if (docLink) {
      docLink.style.display = 'inline-flex';
      docLink.href = data.clearanceDocPath;
      docLink.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 12 18 15 15"></polyline></svg>View Signed Permit (Clearance)`;
    }
    if (docTitle) {
      docTitle.textContent = data.clearanceDocPath.split('/').pop() || 'Signed_Clearance_Document.pdf';
    }
    if (docSub) {
      docSub.innerHTML = '<span style="color: #166534; font-weight: 700;">✓ Signed Clearance (Approved)</span>';
    }
    if (docLabel) {
      docLabel.textContent = 'Official Supporting / Signed Document';
    }
    if (noDocText) noDocText.style.display = 'none';

    // Show secondary link for original client memo if available
    if (hasClientDoc && clientAltWrap && clientAltLink) {
      clientAltWrap.style.display = 'block';
      clientAltLink.href = data.documentPath;
    } else if (clientAltWrap) {
      clientAltWrap.style.display = 'none';
    }
  } else if (hasRecommendationDoc) {
    // Stage 2 Signed Recommendation Form
    if (docLink) {
      docLink.style.display = 'inline-flex';
      docLink.href = data.recommendationDocPath;
      docLink.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 5px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 12 18 15 15"></polyline></svg>View Signed Form (Stage 2)`;
    }
    if (docTitle) {
      docTitle.textContent = data.recommendationDocPath.split('/').pop() || 'Signed_Recommendation_Form.pdf';
    }
    if (docSub) {
      docSub.innerHTML = '<span style="color: #2563eb; font-weight: 700;">✓ Signed Form (Recommendation)</span>';
    }
    if (docLabel) {
      docLabel.textContent = 'Official Supporting / Signed Document';
    }
    if (noDocText) noDocText.style.display = 'none';

    if (hasClientDoc && clientAltWrap && clientAltLink) {
      clientAltWrap.style.display = 'block';
      clientAltLink.href = data.documentPath;
    } else if (clientAltWrap) {
      clientAltWrap.style.display = 'none';
    }
  } else if (hasClientDoc) {
    // Initial Client Request Letter
    if (docLink) {
      docLink.style.display = 'inline-flex';
      docLink.href = data.documentPath;
      docLink.textContent = 'View Document';
    }
    if (docTitle) {
      docTitle.textContent = data.documentPath.split('/').pop() || 'Official_Request_Document.pdf';
    }
    if (docSub) {
      docSub.textContent = 'Original Client Supporting Document';
    }
    if (docLabel) {
      docLabel.textContent = 'Original Client Supporting Document (Letter of Intent / Request Memo)';
    }
    if (noDocText) noDocText.style.display = 'none';
    if (clientAltWrap) clientAltWrap.style.display = 'none';
  } else {
    // No file uploaded
    if (docLink) docLink.style.display = 'none';
    if (docTitle) docTitle.textContent = 'No Document Attached';
    if (docSub) docSub.textContent = 'Walang nakalakip na dokumento';
    if (docLabel) {
      docLabel.textContent = 'Original Client Supporting Document (Letter of Intent / Request Memo)';
    }
    if (noDocText) noDocText.style.display = 'inline-block';
    if (clientAltWrap) clientAltWrap.style.display = 'none';
  }

  // Button state depending on status
  const btnApprove = document.getElementById('btnModalApprove');
  const btnDecline = document.getElementById('btnModalDecline');
  const btnComplete = document.getElementById('btnModalComplete');

  if (data.status === 'completed') {
    if (btnApprove) btnApprove.style.display = 'none';
    if (btnDecline) btnDecline.style.display = 'none';
    if (btnComplete) btnComplete.style.display = 'none';
  } else if (data.status === 'approved') {
    if (btnApprove) btnApprove.style.display = 'none';
    if (btnDecline) btnDecline.style.display = 'none';
    if (btnComplete) btnComplete.style.display = 'inline-flex';
  } else if (data.status === 'rejected') {
    if (btnApprove) btnApprove.style.display = 'none';
    if (btnDecline) btnDecline.style.display = 'none';
    if (btnComplete) btnComplete.style.display = 'none';
  } else {
    if (btnApprove) btnApprove.style.display = 'inline-flex';
    if (btnDecline) btnDecline.style.display = 'inline-flex';
    if (btnComplete) btnComplete.style.display = 'none';
  }

  const modal = document.getElementById('reservationDetailsModal');
  if (modal) {
    modal.style.display = 'flex';
    document.body.classList.add('admin-modal-open');
  }
}

function closeReservationModal() {
  const modal = document.getElementById('reservationDetailsModal');
  if (modal) modal.style.display = 'none';
  document.body.classList.remove('admin-modal-open');
  currentModalActiveRef = null;
}

function modalTriggerComplete() {
  if (currentModalActiveRef && reservationsData[currentModalActiveRef]) {
    const item = reservationsData[currentModalActiveRef];
    closeReservationModal();
    confirmCompleteReservation(item.ref, item.applicant);
  }
}

function modalTriggerApprove() {
  if (currentModalActiveRef && reservationsData[currentModalActiveRef]) {
    const item = reservationsData[currentModalActiveRef];
    closeReservationModal();
    approveReservation(item.ref, item.applicant, item.venue);
  }
}

function modalTriggerDecline() {
  if (currentModalActiveRef && reservationsData[currentModalActiveRef]) {
    const item = reservationsData[currentModalActiveRef];
    closeReservationModal();
    openDeclineModal(item.ref, item.applicant);
  }
}

/* ==========================================================================
   FILTER TABS, VENUE SELECT & SYNCHRONIZED SEARCH
   ========================================================================== */
const adminFilterState = {
  tab: 'all',
  venue: 'all',
  query: ''
};

function applyTabFilter(filter) {
  adminFilterState.tab = filter;

  // Update tab buttons
  document.querySelectorAll('.filter-tab-btn').forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  applyCombinedAdminFilters();
}

function handleAdminSearch(query) {
  const cleanVal = query || '';
  adminFilterState.query = cleanVal.toLowerCase().trim();

  // Synchronize topbar and toolbar search inputs
  const topInput = document.getElementById('topbarSearchInput');
  const barInput = document.getElementById('adminSearchInput');
  if (topInput && topInput.value !== cleanVal) topInput.value = cleanVal;
  if (barInput && barInput.value !== cleanVal) barInput.value = cleanVal;

  // Toggle clear buttons
  const topClear = document.getElementById('topbarSearchClear');
  const barClear = document.getElementById('adminSearchClear');
  const isFilled = cleanVal.length > 0;
  if (topClear) topClear.style.display = isFilled ? 'flex' : 'none';
  if (barClear) barClear.style.display = isFilled ? 'flex' : 'none';

  applyCombinedAdminFilters();
}

function clearAdminSearch() {
  handleAdminSearch('');
  const barInput = document.getElementById('adminSearchInput');
  if (barInput) barInput.focus();
}

function handleVenueSelectFilter(venue) {
  adminFilterState.venue = venue;
  applyCombinedAdminFilters();
}

function resetAllAdminFilters() {
  adminFilterState.tab = 'all';
  adminFilterState.venue = 'all';
  adminFilterState.query = '';

  const topInput = document.getElementById('topbarSearchInput');
  const barInput = document.getElementById('adminSearchInput');
  if (topInput) topInput.value = '';
  if (barInput) barInput.value = '';

  const topClear = document.getElementById('topbarSearchClear');
  const barClear = document.getElementById('adminSearchClear');
  if (topClear) topClear.style.display = 'none';
  if (barClear) barClear.style.display = 'none';

  const venueSelect = document.getElementById('venueFilterSelect');
  if (venueSelect) venueSelect.value = 'all';

  document.querySelectorAll('.filter-tab-btn').forEach(btn => {
    if (btn.getAttribute('data-filter') === 'all') {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  applyCombinedAdminFilters();
}

function applyCombinedAdminFilters() {
  const rows = document.querySelectorAll('#adminTableBody tr[data-status]');
  const noResultsRow = document.getElementById('adminNoResultsRow');
  let visibleCount = 0;

  rows.forEach(row => {
    const isArchived = (row.getAttribute('data-archived') === '1');
    const status = row.getAttribute('data-status') || '';
    const category = row.getAttribute('data-category') || '';
    const rowVenue = (row.getAttribute('data-venue') || '').toLowerCase();
    const rowText = row.innerText.toLowerCase();

    // 1. Check Tab Filter
    let matchesTab = false;
    if (adminFilterState.tab === 'archived') {
      matchesTab = isArchived;
    } else {
      if (isArchived) {
        matchesTab = false; // Exclude archived rows from regular tabs
      } else if (adminFilterState.tab === 'all') {
        matchesTab = true;
      } else if (adminFilterState.tab === 'pending') {
        matchesTab = (status === 'pending');
      } else if (adminFilterState.tab === 'approved') {
        matchesTab = (status === 'approved');
      } else if (adminFilterState.tab === 'completed') {
        matchesTab = (status === 'completed');
      } else if (adminFilterState.tab === 'halls') {
        matchesTab = (category === 'halls');
      } else if (adminFilterState.tab === 'dorms') {
        matchesTab = (category === 'dorms');
      }
    }

    // 2. Check Venue Dropdown Filter
    let matchesVenue = false;
    if (adminFilterState.venue === 'all') {
      matchesVenue = true;
    } else if (rowVenue.includes(adminFilterState.venue.toLowerCase())) {
      matchesVenue = true;
    }

    // 3. Check Unified Search Query
    let matchesQuery = false;
    if (!adminFilterState.query || rowText.includes(adminFilterState.query)) {
      matchesQuery = true;
    }

    if (matchesTab && matchesVenue && matchesQuery) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  if (noResultsRow) {
    noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
  }
}

// Global keyboard shortcut: Ctrl+K or Cmd+K to jump to search
document.addEventListener('keydown', (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    const searchInput = document.getElementById('adminSearchInput') || document.getElementById('topbarSearchInput');
    if (searchInput) {
      searchInput.focus();
      searchInput.select();
    }
  }
});

/* ==========================================================================
   FACILITY MAINTENANCE TOGGLE
   ========================================================================== */
function toggleFacilityMaintenance(venueId, venueName, isMaintenance) {
  const badge = document.getElementById(`badge-${venueId}`);
  if (!badge) return;

  if (isMaintenance) {
    badge.className = 'facility-state-badge state-maintenance';
    badge.textContent = 'Maintenance Hold';
    addAuditLogEntry(`Maintenance Mode activated for <strong>${venueName}</strong> by Super Admin.`, 'amber');
    showAdminToast(`${venueName} placed on Maintenance Hold.`, 'decline');
  } else {
    badge.className = 'facility-state-badge state-available';
    badge.textContent = 'Available';
    addAuditLogEntry(`<strong>${venueName}</strong> restored to Active & Available status.`, 'green');
    showAdminToast(`${venueName} is now Available for bookings.`, 'success');
  }
}

/* ==========================================================================
   AUDIT LOG STREAM & COUNTERS
   ========================================================================== */
function addAuditLogEntry(messageHtml, dotColor = 'green') {
  const stream = document.getElementById('auditStream');
  if (!stream) return;

  const item = document.createElement('div');
  item.className = 'audit-item';
  item.innerHTML = `
    <div class="audit-dot ${dotColor}"></div>
    <div class="audit-content">
      <p>${messageHtml}</p>
      <span class="audit-time">Just now &bull; Executed by Super Admin</span>
    </div>
  `;
  stream.insertBefore(item, stream.firstChild);

  // Smooth scroll to the top of the stream to see the newest entry
  stream.scrollTo({ top: 0, behavior: 'smooth' });

  // Limit stream to 15 entries max to prevent excessive DOM growth
  while (stream.children.length > 15) {
    stream.removeChild(stream.lastChild);
  }
}

function updateKPIBadges() {
  const rows = document.querySelectorAll('#adminTableBody tr[data-status]');
  let allCount = 0;
  let pendingCount = 0;
  let approvedCount = 0;
  let completedCount = 0;
  let hallsCount = 0;
  let dormsCount = 0;
  let archivedCount = 0;

  rows.forEach(row => {
    const isArchived = (row.getAttribute('data-archived') === '1');
    if (isArchived) {
      archivedCount++;
      return;
    }

    allCount++;
    const st = row.getAttribute('data-status');
    const cat = row.getAttribute('data-category');
    if (st === 'pending') pendingCount++;
    if (st === 'approved') approvedCount++;
    if (st === 'completed') completedCount++;
    if (cat === 'halls') hallsCount++;
    if (cat === 'dorms') dormsCount++;
  });

  const tabAll = document.getElementById('tabCountAll');
  const tabPending = document.getElementById('tabCountPending');
  const tabApproved = document.getElementById('tabCountApproved');
  const tabCompleted = document.getElementById('tabCountCompleted');
  const tabHalls = document.getElementById('tabCountHalls');
  const tabDorms = document.getElementById('tabCountDorms');
  const tabArchived = document.getElementById('tabCountArchived');
  const sidebarBadge = document.getElementById('sidebarPendingCount');

  if (tabAll) tabAll.textContent = allCount;
  if (tabPending) tabPending.textContent = pendingCount;
  if (tabApproved) tabApproved.textContent = approvedCount;
  if (tabCompleted) tabCompleted.textContent = completedCount;
  if (tabHalls) tabHalls.textContent = hallsCount;
  if (tabDorms) tabDorms.textContent = dormsCount;
  if (tabArchived) tabArchived.textContent = archivedCount;

  if (sidebarBadge) {
    sidebarBadge.textContent = pendingCount;
  }
}

/* ==========================================================================
   TOAST HELPER
   ========================================================================== */
function showAdminToast(message, type = 'success') {
  const container = document.getElementById('adminToastContainer');
  if (!container) return;

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

function openSystemSettings() {
  showAdminToast('System Settings module active. Administrative policies up to date.');
}

/* ==========================================================================
   USER MANAGEMENT & ROLE-BASED ACCESS CONTROL (RBAC) CONTROLLERS
   ========================================================================== */

let activeUserEditId = null;

function applyUserFilter(filter, btn) {
  if (btn) {
    document.querySelectorAll('.user-filter-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
  }

  const rows = document.querySelectorAll('#userTableBody tr');
  rows.forEach(row => {
    const role = row.getAttribute('data-user-role');
    if (filter === 'all' || role === filter) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function handleUserSearch(query) {
  const q = (query || '').toLowerCase().trim();
  const rows = document.querySelectorAll('#userTableBody tr');
  rows.forEach(row => {
    const searchMeta = (row.getAttribute('data-search') || '') + ' ' + row.innerText.toLowerCase();
    if (!q || searchMeta.includes(q)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function openEditRoleModal(userId, name, email, role, dept) {
  activeUserEditId = userId;
  const modal = document.getElementById('editRoleModal');
  if (!modal) return;

  const refBadge = document.getElementById('editRoleUserId');
  const nameEl = document.getElementById('editRoleUserName');
  const emailEl = document.getElementById('editRoleUserEmail');
  const deptEl = document.getElementById('editRoleUserDept');
  const avatarEl = document.getElementById('editRoleAvatar');
  const roleDropdown = document.getElementById('roleSelectDropdown');

  if (refBadge) refBadge.textContent = userId;
  if (nameEl) nameEl.textContent = name;
  if (emailEl) emailEl.textContent = email;
  if (deptEl) deptEl.textContent = dept;

  if (avatarEl) {
    const parts = name.replace(/(Engr\.|Atty\.|Dr\.|Ms\.|Mr\.)/g, '').trim().split(' ');
    const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
    avatarEl.textContent = initials;
  }

  if (roleDropdown) {
    roleDropdown.value = role;
    syncRolePermissionsCheckboxes(role);
    roleDropdown.onchange = function () {
      syncRolePermissionsCheckboxes(this.value);
    };
  }

  modal.style.display = 'flex';
}

function syncRolePermissionsCheckboxes(role) {
  const permInspect = document.getElementById('permInspect');
  const permRecommend = document.getElementById('permRecommend');
  const permClearance = document.getElementById('permClearance');
  const permUserMgmt = document.getElementById('permUserMgmt');

  if (permInspect) permInspect.checked = (role === 'rec' || role === 'clear');
  if (permRecommend) permRecommend.checked = (role === 'rec');
  if (permClearance) permClearance.checked = (role === 'clear');
  if (permUserMgmt) permUserMgmt.checked = (role === 'clear');
}

function closeEditRoleModal() {
  const modal = document.getElementById('editRoleModal');
  if (modal) modal.style.display = 'none';
  activeUserEditId = null;
}

function saveRoleChanges(e) {
  if (e) e.preventDefault();
  if (!activeUserEditId) return;

  const roleDropdown = document.getElementById('roleSelectDropdown');
  const newRole = roleDropdown ? roleDropdown.value : 'staff';
  const row = document.querySelector(`tr[data-user-id="${activeUserEditId}"]`);
  const userNameEl = document.getElementById('editRoleUserName');
  const userName = userNameEl ? userNameEl.textContent : 'Personnel';

  if (row) {
    row.setAttribute('data-user-role', newRole);

    const roleCell = row.children[2];
    const privCell = row.children[3];

    if (newRole === 'clear') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-clearance">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          Stage 2: Final Clearance Authority
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
    } else if (newRole === 'rec') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-recommend">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Stage 1: Recommending Officer
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Schedule Verification & Administrative Recommendation</span>`;
    } else if (newRole === 'staff') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-staff">
          Division Personnel / Requestor
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>`;
    } else {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-external">
          External Agency Partner
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">External Facility Booking Applicant</span>`;
    }
  }

  addAuditLogEntry(`Institutional role for <strong>${userName}</strong> reconfigured to <em>${newRole.toUpperCase()}</em>.`, 'green');
  showAdminToast(`Permissions updated successfully for ${userName}.`, 'success');
  closeEditRoleModal();
}

function openAddUserModal() {
  const form = document.getElementById('addUserForm');
  if (form) form.reset();
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'none';
}

function handleAddNewUser(e) {
  if (e) e.preventDefault();

  const nameInput = document.getElementById('addUserName');
  const emailInput = document.getElementById('addUserEmail');
  const deptInput = document.getElementById('addUserDept');
  const roleInput = document.getElementById('addUserRole');

  if (!nameInput || !emailInput) return;

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const dept = deptInput ? deptInput.value : 'Administrative Services Unit';
  const role = roleInput ? roleInput.value : 'staff';

  const newId = 'U-' + (100 + Math.floor(Math.random() * 899));
  const parts = name.replace(/(Engr\.|Atty\.|Dr\.|Ms\.|Mr\.)/g, '').trim().split(' ');
  const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();

  let roleHtml = '';
  let privHtml = '';
  if (role === 'clear') {
    roleHtml = `<span class="user-role-badge status-role-clearance"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg> Stage 2: Final Clearance Authority</span>`;
    privHtml = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
  } else if (role === 'rec') {
    roleHtml = `<span class="user-role-badge status-role-recommend"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Stage 1: Recommending Officer</span>`;
    privHtml = `<span class="privilege-summary-tag">Schedule Verification & Administrative Recommendation</span>`;
  } else {
    roleHtml = `<span class="user-role-badge status-role-staff">Division Personnel / Requestor</span>`;
    privHtml = `<span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>`;
  }

  const tbody = document.getElementById('userTableBody');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.setAttribute('data-user-id', newId);
    tr.setAttribute('data-user-role', role);
    tr.setAttribute('data-search', `${name} ${email} ${dept} ${role}`.toLowerCase());
    tr.innerHTML = `
      <td>
        <div class="table-applicant-cell">
          <div class="user-avatar-circle" style="background: #246a42; color: #fff;">${initials}</div>
          <div class="applicant-meta-text">
            <strong>${name}</strong>
            <span>${email}</span>
          </div>
        </div>
      </td>
      <td>
        <span class="user-dept-text">${dept}</span>
      </td>
      <td>${roleHtml}</td>
      <td>${privHtml}</td>
      <td>
        <span class="status-badge-chip chip-confirmed">Active</span>
      </td>
      <td style="text-align: right;">
        <button type="button" class="btn-table-action edit-role" onclick="openEditRoleModal('${newId}', '${name.replace(/'/g, "\\'")}', '${email}', '${role}', '${dept.replace(/'/g, "\\'")}')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          Configure Role
        </button>
      </td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
  }

  addAuditLogEntry(`New institutional account created for <strong>${name}</strong> (${dept}).`, 'green');
  showAdminToast(`Account successfully registered for ${name}.`, 'success');
  closeAddUserModal();
}

/* ==========================================================================
   RESERVATION SOFT DELETE, RESTORE & PERMANENT PURGE HANDLERS
   ========================================================================== */

/**
 * Move reservation to Trash (Soft Delete)
 */
async function confirmDeleteReservation(refId, applicantName) {
  if (!confirm(`Are you sure you want to move reservation "${refId}" (${applicantName}) to Trash / Archived?`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'delete_reservation');
    formData.append('ref', refId);

    const response = await fetch('ajax/reservation_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-ref="${refId}"]`);
      if (row) {
        row.setAttribute('data-archived', '1');
        row.classList.add('row-reservation-archived');

        // Update status cell (column 6)
        const statusCell = row.cells[5];
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-pill status-archived" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
              <span class="status-dot" style="background: #dc2626;"></span>
              Archived / Trash
            </span>
            <span style="display: block; font-size: 0.71rem; color: #dc2626; font-weight: 600; margin-top: 3px;">
              Archived: Just now
            </span>
          `;
        }

        // Update action buttons wrap (column 7)
        const actionsWrap = row.querySelector('.action-buttons-wrap');
        if (actionsWrap) {
          actionsWrap.innerHTML = `
            <button type="button" class="btn-table-action restore" onclick="confirmRestoreReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Restore Reservation to Active Queue">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
              <span>Restore</span>
            </button>
            <button type="button" class="btn-table-action purge" onclick="confirmPermanentDeleteReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Permanently delete reservation from database">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Purge</span>
            </button>
            <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
          `;
        }
      }

      showAdminToast(result.message || `Reservation ${refId} moved to Trash.`, 'success');
      updateKPIBadges();
      applyCombinedAdminFilters();
    } else {
      showAdminToast(result.message || 'Failed to archive reservation.', 'decline');
    }
  } catch (err) {
    showAdminToast('Server error while moving reservation to Trash.', 'decline');
  }
}

/**
 * Restore reservation from Trash back to Active Queue
 */
async function confirmRestoreReservation(refId, applicantName) {
  if (!confirm(`Restore reservation "${refId}" (${applicantName}) back to the Active Reservation list?`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'restore_reservation');
    formData.append('ref', refId);

    const response = await fetch('ajax/reservation_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-ref="${refId}"]`);
      if (row) {
        row.setAttribute('data-archived', '0');
        row.classList.remove('row-reservation-archived');

        const stage = row.getAttribute('data-stage') || 'stage1';
        const statusCell = row.cells[5];
        if (statusCell) {
          if (stage === 'approved') {
            statusCell.innerHTML = `<span class="status-pill status-approved"><span class="status-dot"></span>Confirmed & Approved</span>`;
          } else if (stage === 'rejected') {
            statusCell.innerHTML = `<span class="status-pill status-cancelled"><span class="status-dot"></span>Declined</span>`;
          } else if (stage === 'stage1') {
            statusCell.innerHTML = `<span class="status-pill status-stage1"><span class="status-dot"></span>Awaiting Endorsement</span>`;
          } else if (stage === 'stage2') {
            statusCell.innerHTML = `<span class="status-pill status-stage2"><span class="status-dot"></span>Awaiting Recommendation</span>`;
          } else if (stage === 'stage3') {
            statusCell.innerHTML = `<span class="status-pill status-stage3"><span class="status-dot"></span>Awaiting Clearance</span>`;
          } else {
            statusCell.innerHTML = `<span class="status-pill status-pending"><span class="status-dot"></span>Pending Review</span>`;
          }
        }

        const actionsWrap = row.querySelector('.action-buttons-wrap');
        if (actionsWrap) {
          let routingButtons = '';
          if (stage === 'stage1') {
            routingButtons = `
              <button type="button" class="btn-table-action endorse" onclick="endorseReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Endorse Request (Stage 1)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Endorse</span>
              </button>
              <button type="button" class="btn-table-action decline" onclick="openDeclineModal('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Decline Request (Stage 1)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            `;
          } else if (stage === 'stage2') {
            routingButtons = `
              <button type="button" class="btn-table-action recommend" onclick="recommendReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Recommend Request (Stage 2)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Recommend</span>
              </button>
              <button type="button" class="btn-table-action decline" onclick="openDeclineModal('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Decline Request (Stage 2)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            `;
          } else if (stage === 'stage3') {
            routingButtons = `
              <button type="button" class="btn-table-action approve" onclick="clearanceReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Grant Director Clearance (Stage 3)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Approve</span>
              </button>
              <button type="button" class="btn-table-action decline" onclick="openDeclineModal('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Decline Request (Stage 3)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            `;
          } else if (stage === 'approved') {
            routingButtons = `<span class="badge-approved-note">Approved by Director</span>`;
          } else {
            routingButtons = `<span class="badge-declined-note">Declined</span>`;
          }

          actionsWrap.innerHTML = `
            ${routingButtons}
            <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            <button type="button" class="btn-table-action delete-res" onclick="confirmDeleteReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Move Reservation to Trash (Soft Delete)">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
          `;
        }
      }

      showAdminToast(result.message || `Reservation ${refId} restored.`, 'success');
      updateKPIBadges();
      applyCombinedAdminFilters();
    } else {
      showAdminToast(result.message || 'Failed to restore reservation.', 'decline');
    }
  } catch (err) {
    showAdminToast('Server error while restoring reservation.', 'decline');
  }
}

/**
 * Permanently Purge Reservation (Hard Delete - Super Admin Only)
 */
async function confirmPermanentDeleteReservation(refId, applicantName) {
  if (!confirm(`PERMANENT DELETE WARNING:\n\nAre you sure you want to permanently purge reservation "${refId}" (${applicantName}) from the database? This action is irreversible.`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'permanent_delete_reservation');
    formData.append('ref', refId);

    const response = await fetch('ajax/reservation_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-ref="${refId}"]`);
      if (row) {
        row.remove();
      }

      showAdminToast(result.message || `Reservation ${refId} permanently purged.`, 'success');
      updateKPIBadges();
      applyCombinedAdminFilters();
    } else {
      showAdminToast(result.message || 'Failed to delete reservation.', 'decline');
    }
  } catch (err) {
    showAdminToast('Server error while permanently deleting reservation.', 'decline');
  }
}

/**
 * Conclude and mark reservation as Completed
 */
async function confirmCompleteReservation(refId, applicantName) {
  if (!confirm(`Mark reservation "${refId}" (${applicantName}) as Completed & Concluded?\n\nThis will officially close the activity and archive it in the Completed history.`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'complete_reservation');
    formData.append('ref', refId);

    const response = await fetch('ajax/reservation_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-ref="${refId}"]`);
      if (row) {
        row.setAttribute('data-status', 'completed');
        row.setAttribute('data-stage', 'completed');

        // Update status cell (column 6)
        const statusCell = row.cells[5];
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-pill status-completed">
              <span class="status-dot"></span>
              Completed &amp; Concluded
            </span>
          `;
        }

        // Update actions wrap (column 7)
        const actionsWrap = row.querySelector('.action-buttons-wrap');
        if (actionsWrap) {
          actionsWrap.innerHTML = `
            <span class="badge-completed-note">Completed</span>
            <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            <button type="button" class="btn-table-action delete-res" onclick="confirmDeleteReservation('${refId}', '${applicantName.replace(/'/g, "\\'")}')" title="Move Reservation to Trash (Soft Delete)">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
          `;
        }
      }

      if (reservationsData[refId]) {
        reservationsData[refId].status = 'completed';
        reservationsData[refId].stage = 'completed';
      }

      showAdminToast(result.message || `Reservation ${refId} has been successfully completed!`, 'success');
      updateKPIBadges();
      applyCombinedAdminFilters();
    } else {
      showAdminToast(result.message || 'Failed to complete reservation.', 'decline');
    }
  } catch (err) {
    console.error('Complete reservation error:', err);
    showAdminToast('Server error while marking reservation as completed.', 'decline');
  }
}

/* ==========================================================================
   OCCUPANCY & UTILIZATION ANALYTICS MODAL CONTROLLER
   ========================================================================== */
function openOccupancyModal() {
  const modal = document.getElementById('occupancyModal');
  if (modal) {
    modal.style.display = 'flex';
    document.body.classList.add('admin-modal-open');
  }
}

function closeOccupancyModal() {
  const modal = document.getElementById('occupancyModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.classList.remove('admin-modal-open');
  }
}

function switchOccupancyGraph(type) {
  const barView = document.getElementById('occupancyBarGraphView');
  const lineView = document.getElementById('occupancyLineGraphView');
  const btnBar = document.getElementById('btnToggleBarGraph');
  const btnLine = document.getElementById('btnToggleLineGraph');

  if (type === 'bar') {
    if (barView) barView.style.display = 'block';
    if (lineView) lineView.style.display = 'none';
    if (btnBar) btnBar.classList.add('active');
    if (btnLine) btnLine.classList.remove('active');
  } else if (type === 'line') {
    if (barView) barView.style.display = 'none';
    if (lineView) lineView.style.display = 'block';
    if (btnBar) btnBar.classList.remove('active');
    if (btnLine) btnLine.classList.add('active');
  }
}

function updateOccupancyTimeRange(range) {
  const toastMsg = range === 'week' ? 'Loaded Current Weekly Occupancy'
    : range === 'month' ? 'Loaded Full Monthly Utilization'
      : 'Loaded Institutional Capacity Projection';
  if (typeof showAdminToast === 'function') {
    showAdminToast(toastMsg, 'info');
  }
}

function exportOccupancyReport() {
  if (typeof showAdminToast === 'function') {
    showAdminToast('Generating Official Occupancy Analytics Report (PDF)...', 'success');
  }
  setTimeout(() => {
    window.print();
  }, 600);
}

// ESC listener to close any open admin modals
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    closeReservationModal();
    closeDeclineModal();
    closeOccupancyModal();
  }
});

