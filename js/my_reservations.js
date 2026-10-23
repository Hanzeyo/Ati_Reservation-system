/**
 * ATI Reservation System - My Reservations Dashboard Controller
 * Handles filtering, category separation (Facility Reservations vs Booking History),
 * search, routing modal views, gatepass generation, and cancellation
 */

document.addEventListener('DOMContentLoaded', () => {
  initProfileDropdown();
  initMobileDrawer();
  initCategorySwitcher();
  initKpiFilters();
  initCustomFacilityDropdown();
  initSearch();
  initResetFilters();
  initViewModeToggle();
  initCardModals();
  initCancellationWorkflow();
  initCopyButtons();
  initDownloadSlip();
  initRealtimeReservationsSync();
});

/* ==========================================================================
   1. Profile Dropdown Toggle
   ========================================================================== */
function initProfileDropdown() {
  const badge = document.getElementById('userProfileBadge');
  const dropdown = document.getElementById('profileDropdown');

  syncGlobalProfileHeader();

  if (!badge || !dropdown) return;

  badge.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    dropdown.classList.remove('show');
  });
}

function syncGlobalProfileHeader() {
  const avatar = localStorage.getItem('ati_user_avatar');
  const profile = localStorage.getItem('ati_user_profile');
  if (avatar) {
    document.querySelectorAll('.user-avatar-circle, .drawer-avatar').forEach(c => {
      c.innerHTML = `<img src="${avatar}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
    });
  }
  if (profile) {
    try {
      const p = JSON.parse(profile);
      if (p.fullName) {
        document.querySelectorAll('.user-name, .dropdown-user-name, .drawer-profile-info h5').forEach(el => el.textContent = p.fullName);
      }
    } catch (e) { }
  }
}

/* ==========================================================================
   2. Category Segregation (Facility Reservations vs Booking History)
   ========================================================================== */
let activeCategoryType = 'all'; // 'all' | 'facility' | 'dormitory'
let activeStatusFilter = 'all';
let activeVenueFilter = 'all';
let activeSearchQuery = '';
let currentViewMode = 'cards';

function initCategorySwitcher() {
  const segmentBtns = document.querySelectorAll('.category-segment-btn');
  const pageHeading = document.getElementById('pageHeadingTitle');
  const pageSubtext = document.getElementById('pageHeadingSubtext');
  const pageBadge = document.getElementById('pageTopBadgeText');

  function setCategory(cat, updateUrl = true) {
    activeCategoryType = cat;

    segmentBtns.forEach(btn => {
      if (btn.dataset.categoryType === cat) {
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');
      } else {
        btn.classList.remove('active');
        btn.setAttribute('aria-pressed', 'false');
      }
    });

    // Ensure "My Reservations" nav item remains active
    const myResNav = document.querySelector('a.booking-nav-item[href*="my_reservations"]');
    if (myResNav) myResNav.classList.add('active');

    // Dynamic Heading & Meta
    if (cat === 'facility') {
      if (pageHeading) pageHeading.textContent = 'My Facility Reservations';
      if (pageSubtext) pageSubtext.textContent = 'Track your official facility reservation requests for function halls, training rooms, and boardrooms, follow live administrative routing clearances, download official slips, and access gate passes.';
      if (pageBadge) pageBadge.textContent = 'Official Facility Reservations';
      document.title = 'My Facility Reservations | ATI Reservation Portal';
    } else if (cat === 'dormitory') {
      if (pageHeading) pageHeading.textContent = 'My Booking History';
      if (pageSubtext) pageSubtext.textContent = 'Track your official dormitory lodging requests, check room & bed assignments with dormitory custodians, download lodging slips, and access gate passes.';
      if (pageBadge) pageBadge.textContent = 'Official Booking History';
      document.title = 'My Booking History | ATI Reservation Portal';
    } else {
      if (pageHeading) pageHeading.textContent = 'My Reservations';
      if (pageSubtext) pageSubtext.textContent = 'Track the status of your active reservations, monitor live administrative routing clearances, download official reservation slips, and generate QR gate passes.';
      if (pageBadge) pageBadge.textContent = 'Official Reservations & Venues';
      document.title = 'My Reservations | ATI Reservation Portal';
    }

    if (updateUrl) {
      const url = new URL(window.location.href);
      if (cat === 'all') {
        url.searchParams.delete('type');
      } else {
        url.searchParams.set('type', cat);
      }
      window.history.replaceState({}, '', url.toString());
    }

    applyAllFilters();
  }

  segmentBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (btn.dataset.categoryType) {
        e.preventDefault();
        setCategory(btn.dataset.categoryType);
      }
    });
  });

  // Read URL query parameter on initialization
  const urlParams = new URLSearchParams(window.location.search);
  const typeParam = (urlParams.get('type') || '').toLowerCase();
  if (['dorm', 'dormitory', 'dorms', 'booking', 'bookings'].includes(typeParam)) {
    setCategory('dormitory', false);
  } else if (['facility', 'facilities', 'halls', 'reservation', 'reservations'].includes(typeParam)) {
    setCategory('facility', false);
  } else {
    setCategory('all', false);
  }
}

function initResetFilters() {
  const resetBtns = [
    document.getElementById('btnResetResFilters'),
    document.getElementById('btnResetFilters')
  ];

  resetBtns.forEach(btn => {
    if (!btn) return;
    btn.addEventListener('click', () => {
      const searchInput = document.getElementById('resSearchInput');
      const clearBtn = document.getElementById('btnClearSearch');
      if (searchInput) searchInput.value = '';
      if (clearBtn) clearBtn.style.display = 'none';
      activeSearchQuery = '';

      // Reset facility dropdown to 'all'
      const allOption = document.querySelector('.facility-option[data-value="all"]');
      if (allOption) {
        allOption.click();
      } else {
        activeVenueFilter = 'all';
      }

      // Reset KPI filter to 'all'
      const allKpi = document.querySelector('.my-res-stat-card[data-filter="all"]');
      if (allKpi) {
        allKpi.click();
      } else {
        activeStatusFilter = 'all';
        applyAllFilters();
      }
    });
  });
}

function applyAllFilters() {
  const cards = document.querySelectorAll('.res-card');
  const tableRows = document.querySelectorAll('.res-table-row');
  const tableWrap = document.getElementById('reservationsTableWrap');
  const emptyFilterState = document.getElementById('emptyResState') || document.getElementById('emptyReservationsState');
  const zeroState = document.getElementById('zeroReservationsState');
  let visibleCount = 0;

  // If there are zero reservations overall (initial empty state from database)
  if (cards.length === 0) {
    if (emptyFilterState) {
      emptyFilterState.style.display = 'none';
    }
    if (zeroState) {
      zeroState.style.display = 'block';
    }
    updateKpiCounts();
    return;
  }

  // Hide the zero state since user has reservations in the database
  if (zeroState) {
    zeroState.style.display = 'none';
  }

  cards.forEach(card => {
    const cardStatus = card.dataset.status;
    const cardCat = card.dataset.category || (card.dataset.venue === 'dormitory' ? 'dormitory' : 'facility');
    const cardVenue = card.dataset.venue;
    const cardText = card.textContent.toLowerCase();

    const matchesCategory = (activeCategoryType === 'all') || (cardCat === activeCategoryType);
    const matchesStatus = (activeStatusFilter === 'all') || (cardStatus === activeStatusFilter);
    let matchesVenue = false;
    if (activeVenueFilter === 'all') {
      matchesVenue = true;
    } else if (activeVenueFilter === 'facility' || activeVenueFilter === 'dormitory' || activeVenueFilter === 'guesthouse') {
      matchesVenue = (cardCat === activeVenueFilter);
    } else if (activeVenueFilter === 'completed' || activeVenueFilter === 'cancelled') {
      matchesVenue = (cardStatus === activeVenueFilter);
    } else {
      const vFilterLower = activeVenueFilter.toLowerCase();
      const cardVenueName = (card.dataset.venueName || '').toLowerCase();
      const cardVenueTitle = (card.dataset.venueTitle || '').toLowerCase();
      matchesVenue = (
        cardVenue === activeVenueFilter ||
        card.dataset.venueId === activeVenueFilter ||
        cardVenueName === vFilterLower ||
        cardVenueTitle.includes(vFilterLower)
      );
    }
    const matchesSearch = !activeSearchQuery || cardText.includes(activeSearchQuery);

    if (currentViewMode === 'cards') {
      if (matchesCategory && matchesStatus && matchesVenue && matchesSearch) {
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
    const rowCat = row.dataset.category || (row.dataset.venue === 'dormitory' ? 'dormitory' : (row.dataset.venue === 'guesthouse' ? 'guesthouse' : 'facility'));
    const rowVenue = row.dataset.venue;
    const rowText = row.textContent.toLowerCase();

    const matchesCategory = (activeCategoryType === 'all') || (rowCat === activeCategoryType);
    const matchesStatus = (activeStatusFilter === 'all') || (rowStatus === activeStatusFilter);
    let matchesVenue = false;
    if (activeVenueFilter === 'all') {
      matchesVenue = true;
    } else if (activeVenueFilter === 'facility' || activeVenueFilter === 'dormitory' || activeVenueFilter === 'guesthouse') {
      matchesVenue = (rowCat === activeVenueFilter);
    } else if (activeVenueFilter === 'completed' || activeVenueFilter === 'cancelled') {
      matchesVenue = (rowStatus === activeVenueFilter);
    } else {
      const vFilterLower = activeVenueFilter.toLowerCase();
      const rowVenueName = (row.dataset.venueName || '').toLowerCase();
      const rowVenueTitle = (row.dataset.venueTitle || '').toLowerCase();
      matchesVenue = (
        rowVenue === activeVenueFilter ||
        row.dataset.venueId === activeVenueFilter ||
        rowVenueName === vFilterLower ||
        rowVenueTitle.includes(vFilterLower)
      );
      
    }
    const matchesSearch = !activeSearchQuery || rowText.includes(activeSearchQuery);

    if (matchesCategory && matchesStatus && matchesVenue && matchesSearch) {
      row.style.display = '';
      if (currentViewMode === 'table') visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  if (tableWrap) {
    tableWrap.style.display = currentViewMode === 'table' ? 'block' : 'none';
  }

  if (emptyFilterState) {
    emptyFilterState.style.display = visibleCount === 0 ? 'block' : 'none';
  }

  updateKpiCounts();
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
  const isHistoryPage = window.location.pathname.includes('booking_history') || document.body.classList.contains('booking-history-page') || document.title.includes('Booking History');
  const cards = document.querySelectorAll('.res-card');
  let total = 0;
  let pending = 0;
  let approved = 0;
  let completed = 0;
  let cancelled = 0;

  let totalAll = 0;
  let totalFacility = 0;
  let totalDorm = 0;

  cards.forEach(card => {
    const status = card.dataset.status;
    const cat = card.dataset.category || (card.dataset.venue === 'dormitory' ? 'dormitory' : 'facility');

    if (isHistoryPage) {
      totalAll++;
      if (cat === 'facility') totalFacility++;
      if (cat === 'dormitory') totalDorm++;

      if (activeCategoryType === 'all' || cat === activeCategoryType) {
        total++;
        if (status === 'completed') completed++;
        if (status === 'cancelled') cancelled++;
      }
    } else {
      if (status !== 'cancelled') {
        totalAll++;
        if (cat === 'facility') totalFacility++;
        if (cat === 'dormitory') totalDorm++;
      }

      if (activeCategoryType === 'all' || cat === activeCategoryType) {
        if (status !== 'cancelled') total++;
        if (status === 'pending') pending++;
        if (status === 'approved') approved++;
        if (status === 'completed') completed++;
      }
    }
  });

  const kpiTotal = document.getElementById('kpiTotal');
  const kpiPending = document.getElementById('kpiPending');
  const kpiApproved = document.getElementById('kpiApproved');
  const kpiCompleted = document.getElementById('kpiCompleted');
  const kpiCancelled = document.getElementById('kpiCancelled');
  const kpiArchive = document.getElementById('kpiArchive');
  const catCountAll = document.getElementById('catCountAll');
  const catCountFacility = document.getElementById('catCountFacility');
  const catCountDormitory = document.getElementById('catCountDormitory');
  const navBadgeFacilityCount = document.getElementById('navBadgeFacilityCount');
  const navBadgeDormCount = document.getElementById('navBadgeDormCount');

  if (kpiTotal) kpiTotal.textContent = total;
  if (kpiPending) kpiPending.textContent = pending;
  if (kpiApproved) kpiApproved.textContent = approved;
  if (kpiCompleted) kpiCompleted.textContent = completed;
  if (kpiCancelled) kpiCancelled.textContent = cancelled;
  if (kpiArchive) kpiArchive.textContent = total;

  if (catCountAll) catCountAll.textContent = totalAll;
  if (catCountFacility) catCountFacility.textContent = totalFacility;
  if (catCountDormitory) catCountDormitory.textContent = totalDorm;

  if (navBadgeFacilityCount) {
    navBadgeFacilityCount.textContent = totalAll;
  }
  if (navBadgeDormCount) navBadgeDormCount.textContent = totalDorm;
  document.querySelectorAll('.drawer-badge-count').forEach(el => {
    el.textContent = totalAll;
  });
}

function initKpiFilters() {
  const statCards = document.querySelectorAll('.my-res-stat-card[data-filter]');

  statCards.forEach(card => {
    card.addEventListener('click', () => {
      const filter = card.dataset.filter || 'all';

      statCards.forEach(c => c.classList.remove('active-filter'));
      card.classList.add('active-filter');

      activeStatusFilter = filter;
      applyAllFilters();
    });
  });

  // Support direct navigation from profile stat cards via URL query
  const urlFilter = new URLSearchParams(window.location.search).get('filter');
  if (urlFilter) {
    const targetCard = document.querySelector(`.my-res-stat-card[data-filter="${urlFilter}"]`);
    if (targetCard) {
      targetCard.click();
    }
  }
}

/* ==========================================================================
   3. Custom Facility Dropdown (ATI Themed)
   ========================================================================== */
function initCustomFacilityDropdown() {
  const dropdown = document.getElementById('facilityDropdown');
  const btn = document.getElementById('facilityDropdownBtn');
  const label = document.getElementById('facilityDropdownLabel');
  const menu = document.getElementById('facilityDropdownMenu');
  const options = document.querySelectorAll('.facility-option');

  if (!dropdown || !btn || !menu) return;

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = dropdown.classList.toggle('open');
    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  options.forEach(opt => {
    opt.addEventListener('click', (e) => {
      e.stopPropagation();
      const value = opt.dataset.value;
      const text = opt.querySelector('.opt-name')?.textContent || opt.textContent.trim();

      options.forEach(o => {
        o.classList.remove('active');
        o.setAttribute('aria-selected', 'false');
      });
      opt.classList.add('active');
      opt.setAttribute('aria-selected', 'true');

      if (label) label.textContent = text;
      activeVenueFilter = value;
      dropdown.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');

      applyAllFilters();
    });
  });

  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) {
      dropdown.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && dropdown.classList.contains('open')) {
      dropdown.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
    }
  });
}

/* ==========================================================================
   4. Live Search
   ========================================================================== */
function initSearch() {
  const searchInput = document.getElementById('resSearchInput');
  const clearBtn = document.getElementById('btnClearSearch');

  if (!searchInput) return;

  searchInput.addEventListener('input', (e) => {
    activeSearchQuery = e.target.value.toLowerCase().trim();
    if (clearBtn) {
      clearBtn.style.display = activeSearchQuery ? 'inline-flex' : 'none';
    }
    applyAllFilters();
  });

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      searchInput.value = '';
      activeSearchQuery = '';
      clearBtn.style.display = 'none';
      searchInput.focus();
      applyAllFilters();
    });
  }
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

/* ==========================================================================
   4. Reservation Details & Routing Modal
   ========================================================================== */
function initCardModals() {
  const modal = document.getElementById('resDetailsModal');
  if (!modal) return;

  // Delegated click for opening modal on any card or table row
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-view-details');
    if (!btn) return;

    let card = btn.closest('.res-card');
    if (!card) {
      const row = btn.closest('.res-table-row');
      if (row) {
        const ref = row.querySelector('.res-ref-tag')?.textContent.trim();
        if (ref) {
          card = document.querySelector(`.res-card[data-ref="${ref}"]`);
        }
        if (!card) card = row;
      }
    }
    if (card) {
      openCardDetailsModal(card);
    }
  });

  // Delegated close modal listener
  document.addEventListener('click', (e) => {
    if (e.target.closest('.js-close-res-modal') || (e.target.classList && e.target.classList.contains('modal-overlay'))) {
      document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('show'));
      document.body.style.overflow = '';
    }
  });
}

function openCardDetailsModal(card) {
  const modal = document.getElementById('resDetailsModal');
  if (!modal || !card) return;

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
      const rawStatus = card.dataset.rawStatus || statusText;
      const applicantName = card.dataset.applicantName || 'Applicant';
      const createdAt = card.dataset.createdAt || 'Recently Submitted';
      const docPath = card.dataset.docPath || '';
      const notes = card.dataset.specialNotes || '';
      const remarks = card.dataset.remarks || '';

      const endorsedBy = card.dataset.endorsedBy || '';
      const endorsedAt = card.dataset.endorsedAt || '';
      const endorsementRemarks = card.dataset.endorsementRemarks || '';

      const recommendedBy = card.dataset.recommendedBy || '';
      const recommendedAt = card.dataset.recommendedAt || '';
      const recommendationRemarks = card.dataset.recommendationRemarks || '';
      const recommendationDoc = card.dataset.recommendationDoc || '';

      const clearedBy = card.dataset.clearedBy || '';
      const clearedAt = card.dataset.clearedAt || '';
      const clearanceRemarks = card.dataset.clearanceRemarks || '';
      const clearanceDoc = card.dataset.clearanceDoc || '';

      // Populate modal
      document.getElementById('modalResRef').textContent = ref;
      document.getElementById('modalResTitle').textContent = title;
      document.getElementById('modalResVenue').textContent = venue;
      document.getElementById('modalResDates').textContent = dates;
      document.getElementById('modalResTime').textContent = time;
      document.getElementById('modalResPax').textContent = pax;
      document.getElementById('modalResDivision').textContent = division;

      modal.querySelectorAll('.js-download-slip').forEach(b => {
        b.dataset.ref = ref;
      });

      const modalBadge = document.getElementById('modalResStatusBadge');
      if (modalBadge) {
        modalBadge.className = `res-status-badge ${statusClass}`;
        modalBadge.innerHTML = `<span class="status-dot-pulse"></span><span>${statusText}</span>`;
      }

      // Populate Document Attachment (Prioritize Signed Documents if approved)
      const docLink = document.getElementById('modalDocLink');
      const docFileName = document.getElementById('modalDocFileName');
      const activeDoc = clearanceDoc || recommendationDoc || docPath;

      if (activeDoc) {
        const parts = activeDoc.split('/');
        if (docFileName) {
          if (clearanceDoc) {
            docFileName.innerHTML = `<span style="color: #166534; font-weight: 700;">✓ Signed Clearance:</span> ${escapeHtml(parts[parts.length - 1])}`;
          } else if (recommendationDoc) {
            docFileName.innerHTML = `<span style="color: #2563eb; font-weight: 700;">✓ Signed Form:</span> ${escapeHtml(parts[parts.length - 1])}`;
          } else {
            docFileName.textContent = parts[parts.length - 1];
          }
        }
        if (docLink) {
          docLink.href = activeDoc;
          docLink.style.display = 'inline-flex';
          docLink.textContent = (clearanceDoc || recommendationDoc) ? 'View Signed Document' : 'View Document';
        }
      } else {
        if (docFileName) docFileName.textContent = 'No document uploaded';
        if (docLink) docLink.style.display = 'none';
      }

      // Populate Special Notes
      const notesSection = document.getElementById('modalNotesSection');
      const notesContent = document.getElementById('modalNotesContent');
      if (notesSection && notesContent) {
        if (notes && notes.trim() !== '') {
          notesContent.textContent = notes;
          notesSection.style.display = 'block';
        } else {
          notesSection.style.display = 'none';
        }
      }

      // Populate Admin Remarks
      const remarksSection = document.getElementById('modalRemarksSection');
      const remarksContent = document.getElementById('modalRemarksContent');
      if (remarksSection && remarksContent) {
        if (remarks && remarks.trim() !== '') {
          remarksContent.textContent = remarks;
          remarksSection.style.display = 'block';

          if (statusClass === 'rejected') {
            remarksSection.style.background = '#fff5f5';
            remarksSection.style.borderLeft = '3.5px solid #dc2626';
            const titleEl = remarksSection.querySelector('div:first-child');
            if (titleEl) {
              titleEl.textContent = 'Reason for Disapproval / Instructions for Applicant';
              titleEl.style.color = '#991b1b';
            }
            remarksContent.style.color = '#7f1d1d';
            remarksContent.style.fontWeight = '600';
          } else {
            remarksSection.style.background = '#fefce8';
            remarksSection.style.borderLeft = '3px solid #eab308';
            const titleEl = remarksSection.querySelector('div:first-child');
            if (titleEl) {
              titleEl.textContent = 'Admin Remarks & Feedback';
              titleEl.style.color = '#854d0e';
            }
            remarksContent.style.color = '#713f12';
            remarksContent.style.fontWeight = 'normal';
          }
        } else {
          remarksSection.style.display = 'none';
        }
      }

      // Configure dynamic 3-Stage Governance routing trail inside modal
      const routingList = document.getElementById('modalRoutingList');
      if (routingList) {
        const isApproved = (rawStatus === 'Approved');
        const isCompleted = (rawStatus === 'Completed');
        const isCancelled = (rawStatus === 'Cancelled');
        const isRejected = (rawStatus === 'Rejected');

        const s1Done = isApproved || isCompleted || ['Pending Recommendation', 'Pending Clearance'].includes(rawStatus) || !!endorsedAt;
        const s2Done = isApproved || isCompleted || (rawStatus === 'Pending Clearance') || !!recommendedAt;
        const s3Done = isApproved || isCompleted || !!clearedAt;

        let stepsHtml = '';

        // Step 1: Request Created & Submitted
        stepsHtml += `
          <div class="routing-item">
            <div class="routing-icon-badge done">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <div class="routing-text-content">
              <h5>1. Request Created &amp; Submitted</h5>
              <p>Submitted by <strong>${escapeHtml(applicantName)}</strong> (${escapeHtml(division)}) with activity specifications and uploaded requirements.</p>
              <span class="routing-timestamp">${escapeHtml(createdAt)} &bull; Online Portal Lodging</span>
            </div>
          </div>
        `;

        // Step 2: Stage 1 - Division Chief Endorsement
        if (s1Done) {
          const endorserText = endorsedBy ? `Endorsed by <strong>${escapeHtml(endorsedBy)}</strong> (Division Chief / Originating Unit Head).` : `Endorsed by Division Chief / Originating Unit Head.`;
          const memoText = endorsementRemarks ? ` Remarks: "${escapeHtml(endorsementRemarks)}"` : ` Endorsement memo verified and aligned with annual division work plan.`;
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>2. Stage 1: Division Chief Endorsement (Approved)</h5>
                <p>${endorserText}${memoText}</p>
                <span class="routing-timestamp">${escapeHtml(endorsedAt || 'Stage 1 Completed')} &bull; Endorsed to Property Unit</span>
              </div>
            </div>
          `;
        } else if (isRejected && !s1Done) {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #fee2e2; border: 1.5px solid #fca5a5;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #991b1b;">2. Stage 1: Division Chief Endorsement (Disapproved / Returned)</h5>
                <p style="color: #7f1d1d; font-weight: 600;">${escapeHtml(remarks || 'Returned by Division Chief or Administration during initial endorsement verification.')}</p>
                <span class="routing-timestamp" style="color: #dc2626; font-weight: 700;">Action Required &bull; Disapproved at Stage 1</span>
              </div>
            </div>
          `;
        } else if (isCancelled) {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #f1f5f9; border: 1.5px solid #cbd5e1;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #475569;">2. Stage 1: Division Chief Endorsement (Cancelled)</h5>
                <p>Reservation was cancelled by the applicant prior to endorsement completion.</p>
                <span class="routing-timestamp" style="color: #64748b;">Cancelled by Requestor</span>
              </div>
            </div>
          `;
        } else {
          // In progress at Stage 1
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge pending" style="background: #fef3c7; border: 1.5px solid #fde68a;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #92400e;">2. Stage 1: Division Chief Endorsement (Under Active Review)</h5>
                <p>Awaiting review and signature from the Originating Division Chief / Section Head. Supporting documents are being verified.</p>
                <span class="routing-timestamp" style="color: #b45309; font-weight: 700;">In Progress &bull; Assigned to Originating Unit Head</span>
              </div>
            </div>
          `;
        }

        // Step 3: Stage 2 - Property & Facilities Logistics Recommendation
        if (s2Done) {
          const recOfficer = recommendedBy ? `Evaluated &amp; recommended by <strong>${escapeHtml(recommendedBy)}</strong> (Property &amp; Facilities Unit).` : `Evaluated &amp; recommended by Administrative Property &amp; Facilities Management Unit.`;
          const recNote = recommendationRemarks ? ` Remarks: "${escapeHtml(recommendationRemarks)}"` : ` Facility verified vacant; AV equipment, sound technicians, and HVAC confirmed ready.`;
          const recDocLink = recommendationDoc ? `<div style="margin-top: 0.35rem;"><a href="${escapeHtml(recommendationDoc)}" target="_blank" style="color: #166534; font-size: 0.76rem; font-weight: 700; text-decoration: underline;">View Signed Recommendation Form (PDF)</a></div>` : '';
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>3. Stage 2: Property &amp; Logistics Recommendation (Recommended)</h5>
                <p>${recOfficer}${recNote}</p>
                ${recDocLink}
                <span class="routing-timestamp">${escapeHtml(recommendedAt || 'Stage 2 Completed')} &bull; Forwarded to Directorate</span>
              </div>
            </div>
          `;
        } else if (isRejected && s1Done && !s2Done) {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #fee2e2; border: 1.5px solid #fca5a5;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #991b1b;">3. Stage 2: Property Recommendation (Disapproved / Conflict)</h5>
                <p style="color: #7f1d1d; font-weight: 600;">${escapeHtml(remarks || 'Venue schedule conflict or equipment logistics unavailable on requested dates.')}</p>
                <span class="routing-timestamp" style="color: #dc2626; font-weight: 700;">Disapproved at Stage 2 (Property Logistics)</span>
              </div>
            </div>
          `;
        } else if (rawStatus === 'Pending Recommendation') {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge pending" style="background: #eff6ff; border: 1.5px solid #bfdbfe;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #1e40af;">3. Stage 2: Property &amp; Logistics Recommendation (Under Evaluation)</h5>
                <p>Stage 1 Endorsement complete. Property and Facilities Management Unit is inspecting venue availability, utility readiness, and assigning technical support crew.</p>
                <span class="routing-timestamp" style="color: #2563eb; font-weight: 700;">In Progress &bull; Administrative Logistics Unit</span>
              </div>
            </div>
          `;
        } else if (isCancelled) {
          // Closed
        } else {
          // Waiting for stage 1
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #94a3b8;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #64748b;">3. Stage 2: Property &amp; Logistics Recommendation</h5>
                <p style="color: #94a3b8;">Pending completion of Stage 1 Division Chief Endorsement before facility inspection commences.</p>
                <span class="routing-timestamp" style="color: #94a3b8;">Scheduled after Stage 1</span>
              </div>
            </div>
          `;
        }

        // Step 4: Stage 3 - Directorate Clearance & Final Approval
        if (s3Done || isApproved) {
          const clrOfficer = clearedBy ? `Approved &amp; cleared by <strong>${escapeHtml(clearedBy)}</strong> (Director IV / Executive Office).` : `Approved &amp; cleared by Office of the Director IV.`;
          const clrNote = clearanceRemarks ? ` Executive Note: "${escapeHtml(clearanceRemarks)}"` : ` Institutional clearance granted. Master calendar officially reserved and Gate Pass generated.`;
          const clrDocLink = clearanceDoc ? `<div style="margin-top: 0.35rem;"><a href="${escapeHtml(clearanceDoc)}" target="_blank" style="color: #166534; font-size: 0.76rem; font-weight: 700; text-decoration: underline;">View Signed Executive Clearance Permit (PDF)</a></div>` : '';
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge done">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5>4. Stage 3: Directorate Final Clearance &amp; Approval (Approved)</h5>
                <p>${clrOfficer}${clrNote}</p>
                ${clrDocLink}
                <span class="routing-timestamp">${escapeHtml(clearedAt || 'Stage 3 Approved')} &bull; Official Permit Released</span>
              </div>
            </div>
          `;
        } else if (isRejected && s2Done && !s3Done) {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #fee2e2; border: 1.5px solid #fca5a5;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #991b1b;">4. Stage 3: Directorate Final Clearance (Disapproved)</h5>
                <p style="color: #7f1d1d; font-weight: 600;">${escapeHtml(remarks || 'Disapproved by Director IV on institutional policy or official executive grounds.')}</p>
                <span class="routing-timestamp" style="color: #dc2626; font-weight: 700;">Disapproved at Directorate Level</span>
              </div>
            </div>
          `;
        } else if (rawStatus === 'Pending Clearance') {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge pending" style="background: #ecfdf5; border: 1.5px solid #a7f3d0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #065f46;">4. Stage 3: Directorate Final Clearance (Transmitted to Director)</h5>
                <p>Fully endorsed by Division and recommended by Logistics. Transmitted to the Office of Director IV for final executive clearance and issuance of official security pass.</p>
                <span class="routing-timestamp" style="color: #059669; font-weight: 700;">Awaiting Director Signature &bull; Final Stage</span>
              </div>
            </div>
          `;
        } else if (isCancelled) {
          // Closed
        } else {
          // Waiting
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge" style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #94a3b8;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #64748b;">4. Stage 3: Directorate Final Clearance &amp; Approval</h5>
                <p style="color: #94a3b8;">Final approval step by Director IV following successful completion of Stages 1 and 2.</p>
                <span class="routing-timestamp" style="color: #94a3b8;">Scheduled after Stage 2</span>
              </div>
            </div>
          `;
        }

        // Optional Step 5: Concluded / Archived
        if (isCompleted) {
          stepsHtml += `
            <div class="routing-item">
              <div class="routing-icon-badge done" style="background: #e8f5e9; border: 1.5px solid #86efac;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
              <div class="routing-text-content">
                <h5 style="color: #166534;">5. Activity Concluded &amp; Facility Turnover (Archived)</h5>
                <p>Activity has taken place successfully. Facility returned in good order, post-activity inspection passed, and record permanently archived.</p>
                <span class="routing-timestamp" style="color: #16a34a; font-weight: 700;">Concluded &amp; Permanently Archived</span>
              </div>
            </div>
          `;
        }

        routingList.innerHTML = stepsHtml;
      }

  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
}

/* ==========================================================================
   5. Gate Pass & QR Modal
   ========================================================================== */
window.showGatePassModal = function (ref, title, venue, dates) {
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
  const cancelModal = document.getElementById('cancelConfirmModal');
  const btnConfirmCancel = document.getElementById('btnConfirmCancel');

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-cancel-res');
    if (!btn) return;
    cardToCancel = btn.closest('.res-card');
    const ref = cardToCancel?.dataset.ref || btn.dataset.ref || 'ATI-RES-2026-1042';
    const title = cardToCancel?.querySelector('.res-event-title')?.textContent || 'this activity';

    const refEl = document.getElementById('cancelResRef');
    const titleEl = document.getElementById('cancelResTitle');
    if (refEl) refEl.textContent = ref;
    if (titleEl) titleEl.textContent = title;

    if (cancelModal) {
      cancelModal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  });

  if (btnConfirmCancel) {
    btnConfirmCancel.addEventListener('click', () => {
      if (!cardToCancel) return;

      const ref = cardToCancel.dataset.ref;
      const reason = document.getElementById('cancelReasonSelect')?.value || 'Schedule conflict';

      btnConfirmCancel.disabled = true;
      btnConfirmCancel.textContent = 'Processing...';

      const formData = new FormData();
      formData.append('reference_no', ref);
      formData.append('reason', reason);

      fetch('ajax/cancel_reservation.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btnConfirmCancel.disabled = false;
        btnConfirmCancel.textContent = 'Confirm Cancellation';

        if (data.success) {
          // Update card state in DOM
          cardToCancel.dataset.status = 'cancelled';
          cardToCancel.dataset.statusText = 'Cancelled by Requestor';
          const badge = cardToCancel.querySelector('.res-status-badge');
          if (badge) {
            badge.className = 'res-status-badge cancelled';
            badge.innerHTML = `<span class="status-dot-pulse"></span><span>Cancelled by Requestor</span>`;
          }

          // Remove cancel button from card side actions
          const cancelBtn = cardToCancel.querySelector('.js-cancel-res');
          if (cancelBtn) cancelBtn.remove();

          // Close modal
          cancelModal.classList.remove('show');
          document.body.style.overflow = '';

          // Update KPI counters
          updateKpiCounts();
          applyAllFilters();

          showToast(`Reservation ${ref} was cancelled successfully.`);
        } else {
          showToast(data.message || 'Failed to cancel reservation.');
        }
        cardToCancel = null;
      })
      .catch(err => {
        btnConfirmCancel.disabled = false;
        btnConfirmCancel.textContent = 'Confirm Cancellation';
        showToast('Network error while cancelling reservation.');
        cardToCancel = null;
      });
    });
  }
}

/* ==========================================================================
   7. Copy Reference Button
   ========================================================================== */
function initCopyButtons() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-copy-ref');
    if (!btn) return;
    const ref = btn.dataset.ref;
    if (ref) {
      navigator.clipboard.writeText(ref).then(() => {
        showToast(`Copied reference ID: ${ref}`);
      }).catch(() => {
        showToast(`Reference ID: ${ref}`);
      });
    }
  });
}

/* ==========================================================================
   8. Download & View Official Slip Modal
   ========================================================================== */
function initDownloadSlip() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-download-slip');
    if (!btn) return;

    let ref = btn.dataset.ref;
    if (!ref) {
      ref = document.getElementById('modalResRef')?.textContent?.trim();
    }
    if (!ref) return;

    // Find card or row corresponding to this reference code
    const card = document.querySelector(`.res-card[data-ref="${ref}"]`) || document.querySelector(`.res-table-row[data-ref="${ref}"]`);
    if (!card) {
      window.open(`download_form.php?ref=${encodeURIComponent(ref)}`, '_blank');
      return;
    }

    const title = card.querySelector('.res-event-title')?.textContent || card.querySelector('td[data-label="Activity Purpose"] div:first-child')?.textContent || 'Institutional Activity';
    const venue = card.dataset.venueTitle || 'ATI Facility';
    const dates = card.dataset.dates || 'Scheduled Dates';
    const time = card.dataset.time || '08:00 AM – 05:00 PM';
    const division = card.dataset.division || 'ATI Requesting Unit';
    const pax = card.dataset.pax || 'Attendees';
    const statusText = card.dataset.statusText || 'Pending';
    const notes = card.dataset.specialNotes || 'Standard venue arrangement';

    // Populate Printable Slip Modal
    const elRef = document.getElementById('slipRefCode');
    const elTitle = document.getElementById('slipEventTitle');
    const elVenue = document.getElementById('slipVenueName');
    const elDates = document.getElementById('slipScheduleDates');
    const elTime = document.getElementById('slipTimeSlot');
    const elDiv = document.getElementById('slipApplicantDivision');
    const elPax = document.getElementById('slipPaxCount');
    const elStatus = document.getElementById('slipStatusText');
    const elNotes = document.getElementById('slipNotes');

    if (elRef) elRef.textContent = ref;
    if (elTitle) elTitle.textContent = title;
    if (elVenue) elVenue.textContent = venue;
    if (elDates) elDates.textContent = dates;
    if (elTime) elTime.textContent = time;
    if (elDiv) elDiv.textContent = division;
    if (elPax) elPax.textContent = pax;
    if (elStatus) elStatus.textContent = statusText;
    if (elNotes) elNotes.textContent = notes && notes.trim() !== '' ? notes : 'Standard venue arrangement';

    // Show slip modal if present, otherwise open official printable form
    const slipModal = document.getElementById('slipModal');
    if (slipModal) {
      slipModal.classList.add('show');
      document.body.style.overflow = 'hidden';
    } else {
      window.open(`download_form.php?ref=${encodeURIComponent(ref)}`, '_blank');
    }
  });
}

window.printSlipDocument = function () {
  window.print();
};

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

/* ==========================================================================
   11. Real-Time Booking & Reservation Synchronization Engine
   ========================================================================== */
let realtimeSyncInterval = null;
let lastReservationHash = null;
let previousStatusMap = {};
let isCheckingRealtime = false;

function initRealtimeReservationsSync() {
  // Populate initial statuses from DOM cards
  document.querySelectorAll('.res-card').forEach(card => {
    const ref = card.dataset.ref;
    const raw = card.dataset.rawStatus || card.dataset.status;
    if (ref) previousStatusMap[ref] = raw;
  });

  // Initial check to fetch hash
  checkRealtimeUpdates(true);

  // Poll server every 4 seconds for instantaneous real-time responsiveness
  if (realtimeSyncInterval) clearInterval(realtimeSyncInterval);
  realtimeSyncInterval = setInterval(() => {
    checkRealtimeUpdates(false);
  }, 4000);

  // Re-check immediately when tab becomes visible or user switches back to window
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      checkRealtimeUpdates(false);
    }
  });

  window.addEventListener('focus', () => {
    checkRealtimeUpdates(false);
  });
}

function checkRealtimeUpdates(isInitialCheck = false) {
  if (isCheckingRealtime) return;
  isCheckingRealtime = true;

  fetch('ajax/get_user_reservations.php', { cache: 'no-store' })
    .then(res => res.json())
    .then(data => {
      isCheckingRealtime = false;
      if (!data || !data.success) return;

      if (isInitialCheck || lastReservationHash === null) {
        lastReservationHash = data.hash;
        if (Array.isArray(data.reservations)) {
          data.reservations.forEach(r => {
            if (r.reference_no && !previousStatusMap[r.reference_no]) {
              previousStatusMap[r.reference_no] = r.raw_status;
            }
          });
        }
        if (data.counts) {
          updateDashboardCounts(data.counts);
        }
        return;
      }

      // Check if hash changed (i.e. status, endorsement, or approval altered by admin)
      if (data.hash === lastReservationHash) {
        return;
      }

      const isHistoryPage = window.location.pathname.includes('booking_history') || 
                            document.body.classList.contains('booking-history-page') || 
                            document.title.includes('Booking History');

      // Compare statuses against previous status map and trigger sleek toasts
      if (Array.isArray(data.reservations)) {
        data.reservations.forEach(r => {
          const prev = previousStatusMap[r.reference_no];
          if (prev && prev !== r.raw_status) {
            notifyStatusChange(r, prev, r.raw_status, isHistoryPage);
          }
          previousStatusMap[r.reference_no] = r.raw_status;
        });
      }

      lastReservationHash = data.hash;

      // Update KPIs, cards, table, and active modal
      if (data.counts) {
        updateDashboardCounts(data.counts);
      }
      if (Array.isArray(data.reservations)) {
        syncCardsWithRealtimeData(data.reservations, isHistoryPage);
        syncOpenModalIfViewing(data.reservations);
      }
    })
    .catch(err => {
      isCheckingRealtime = false;
    });
}

function notifyStatusChange(r, oldStatus, newStatus, isHistoryPage) {
  const ref = r.reference_no;
  const title = r.event_title || r.venue_name || 'Reservation';

  if (newStatus === 'Approved') {
    showRealtimeNotification(
      'Reservation Approved! 🎉',
      `Reference #${ref} for "${title}" has been granted Directorate clearance. Security Gate Pass and QR code are ready!`,
      'success'
    );
  } else if (newStatus === 'Pending Recommendation') {
    showRealtimeNotification(
      'Stage 1 Endorsed ✓',
      `Reference #${ref} was endorsed by Division Chief and transmitted to Property & Logistics.`,
      'info'
    );
  } else if (newStatus === 'Pending Clearance') {
    showRealtimeNotification(
      'Stage 2 Recommended ✓',
      `Reference #${ref} was recommended by Logistics and routed to Director IV for final executive clearance.`,
      'info'
    );
  } else if (newStatus === 'Completed') {
    showRealtimeNotification(
      'Activity Concluded',
      `Reference #${ref} for "${title}" has completed and is now permanently recorded in Booking History.`,
      'info'
    );
  } else if (newStatus === 'Cancelled') {
    showRealtimeNotification(
      'Reservation Cancelled',
      `Reference #${ref} has been cancelled.`,
      'warning'
    );
  } else if (newStatus === 'Rejected') {
    showRealtimeNotification(
      'Request Disapproved / Returned',
      `Reference #${ref} was returned. Note: "${r.admin_remarks || 'Please check remarks for details'}".`,
      'danger'
    );
  } else {
    showRealtimeNotification(
      'Status Updated',
      `Reference #${ref} status is now: ${newStatus}`,
      'info'
    );
  }
}

function updateDashboardCounts(counts) {
  const isHistoryPage = window.location.pathname.includes('booking_history') || 
                        document.body.classList.contains('booking-history-page') || 
                        document.title.includes('Booking History');

  const kpiTotal = document.getElementById('kpiTotal');
  const kpiPending = document.getElementById('kpiPending');
  const kpiApproved = document.getElementById('kpiApproved');
  const kpiCompleted = document.getElementById('kpiCompleted');
  const kpiCancelled = document.getElementById('kpiCancelled');
  const kpiArchive = document.getElementById('kpiArchive');

  if (isHistoryPage) {
    if (kpiTotal) kpiTotal.textContent = counts.total ?? (counts.active + counts.history);
    if (kpiPending) kpiPending.textContent = counts.pending;
    if (kpiApproved) kpiApproved.textContent = counts.approved;
    if (kpiCompleted) kpiCompleted.textContent = counts.completed;
    if (kpiCancelled) kpiCancelled.textContent = counts.cancelled;
    if (kpiArchive) kpiArchive.textContent = counts.completed;
  } else {
    if (kpiTotal) kpiTotal.textContent = counts.active;
    if (kpiPending) kpiPending.textContent = counts.pending;
    if (kpiApproved) kpiApproved.textContent = counts.approved;
    if (kpiCompleted) kpiCompleted.textContent = counts.completed;
  }

  // Update Category Segmented Badges on both pages
  document.querySelectorAll('.seg-count-badge:not(.blue)').forEach(el => {
    el.textContent = counts.active;
  });
  document.querySelectorAll('.seg-count-badge.blue').forEach(el => {
    el.textContent = counts.total ?? (counts.active + counts.history);
  });

  // Update Top Navigation Badges
  document.querySelectorAll('.nav-badge-count, #navBadgeFacilityCount').forEach(el => {
    el.textContent = counts.active;
  });
  document.querySelectorAll('.drawer-badge-count').forEach(el => {
    el.textContent = counts.active;
  });
}

function syncCardsWithRealtimeData(reservations, isHistoryPage) {
  const container = document.getElementById('reservationsList');
  if (!container) return;

  const relevantItems = isHistoryPage ? reservations : reservations.filter(r => !r.is_history);
  const relevantRefs = new Set(relevantItems.map(r => r.reference_no));

  // 1. Remove or fade out cards no longer in this view
  const currentCards = container.querySelectorAll('.res-card');
  currentCards.forEach(card => {
    const ref = card.dataset.ref;
    if (!relevantRefs.has(ref)) {
      card.style.transition = 'all 0.35s ease';
      card.style.opacity = '0';
      card.style.transform = 'translateY(-12px)';
      setTimeout(() => {
        card.remove();
        checkZeroState(container);
        applyAllFilters();
      }, 360);
    }
  });

  // 2. Update existing cards or prepend new cards
  relevantItems.forEach(r => {
    let card = container.querySelector(`.res-card[data-ref="${r.reference_no}"]`);
    if (card) {
      // Update data attributes
      card.dataset.status = r.filter_status;
      card.dataset.rawStatus = r.raw_status;
      card.dataset.category = r.category;
      card.dataset.venue = r.slug;
      card.dataset.venueId = r.facility_id;
      card.dataset.venueName = r.venue_name;
      card.dataset.venueTitle = r.venue_title_full;
      card.dataset.venueImage = r.venue_thumb;
      card.dataset.dates = r.formatted_dates;
      card.dataset.time = r.time_slot;
      card.dataset.pax = r.pax_text;
      card.dataset.division = r.division;
      card.dataset.applicantName = r.applicant_name;
      card.dataset.createdAt = r.created_at_text;
      card.dataset.statusText = r.status_badge_text;
      card.dataset.specialNotes = r.special_notes || '';
      card.dataset.remarks = r.admin_remarks || '';
      card.dataset.docPath = r.document_path || '';
      card.dataset.endorsedBy = r.endorsed_by || '';
      card.dataset.endorsedAt = r.endorsed_at || '';
      card.dataset.endorsementRemarks = r.endorsement_remarks || '';
      card.dataset.recommendedBy = r.recommended_by || '';
      card.dataset.recommendedAt = r.recommended_at || '';
      card.dataset.recommendationRemarks = r.recommendation_remarks || '';
      card.dataset.recommendationDoc = r.recommendation_doc_path || '';
      card.dataset.clearedBy = r.cleared_by || '';
      card.dataset.clearedAt = r.cleared_at || '';
      card.dataset.clearanceRemarks = r.clearance_remarks || '';
      card.dataset.clearanceDoc = r.clearance_doc_path || '';

      // Update Status Badge
      const badge = card.querySelector('.res-status-badge');
      if (badge) {
        badge.className = `res-status-badge ${r.status_badge_class}`;
        badge.innerHTML = `<span class="status-dot-pulse"></span><span>${escapeHtml(r.status_badge_text)}</span>`;
      }

      // Update Middle Section (Routing Stepper vs Concluded Summary vs Cancellation Notice)
      const mainDiv = card.querySelector('.res-card-main');
      if (mainDiv) {
        if (r.raw_status === 'Completed') {
          let summary = mainDiv.querySelector('.res-concluded-summary');
          const oldStepper = mainDiv.querySelector('.res-mini-routing');
          const oldNotice = mainDiv.querySelector('.res-cancellation-notice');
          if (oldStepper) oldStepper.remove();
          if (oldNotice) oldNotice.remove();
          if (!summary) {
            summary = document.createElement('div');
            summary.className = 'res-concluded-summary';
            mainDiv.appendChild(summary);
          }
          summary.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><span>${escapeHtml(r.admin_remarks || 'Activity successfully conducted. Facility inspection cleared & venue turned over. Record permanently archived.')}</span>`;
        } else if (['Cancelled', 'Rejected'].includes(r.raw_status)) {
          let notice = mainDiv.querySelector('.res-cancellation-notice');
          const oldStepper = mainDiv.querySelector('.res-mini-routing');
          const oldSummary = mainDiv.querySelector('.res-concluded-summary');
          if (oldStepper) oldStepper.remove();
          if (oldSummary) oldSummary.remove();
          if (!notice) {
            notice = document.createElement('div');
            notice.className = 'res-cancellation-notice';
            mainDiv.appendChild(notice);
          }
          const isCanc = (r.raw_status === 'Cancelled');
          notice.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><span><strong>${isCanc ? 'Cancellation Notice:' : 'Disapproval Notice:'}</strong> ${escapeHtml(r.admin_remarks || (isCanc ? 'Reservation was cancelled by the applicant. Schedule was released back to availability.' : 'Application was disapproved / returned during administrative review.'))}</span>`;
        } else {
          // Pending or Approved: show 3-Stage Governance routing tracker
          const oldSummary = mainDiv.querySelector('.res-concluded-summary');
          const oldNotice = mainDiv.querySelector('.res-cancellation-notice');
          if (oldSummary) oldSummary.remove();
          if (oldNotice) oldNotice.remove();
          let stepper = mainDiv.querySelector('.res-mini-routing');
          if (!stepper) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = `
              <div class="res-mini-routing">
                <div class="routing-stepper-header">
                  <div class="stepper-header-left">
                    <span class="stepper-badge-icon">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                      </svg>
                    </span>
                    <span class="stepper-title">GOVERNANCE ROUTING TRACKER</span>
                  </div>
                  <span class="stepper-stage-chip" style="color: ${r.current_stage_color}; border-color: ${r.current_stage_color}44; background: ${r.current_stage_color}14;">
                    ${escapeHtml(r.current_stage_label)}
                  </span>
                </div>
                <div class="routing-stepper-track">
                  <div class="stepper-step step-${r.step1_state}" title="Stage 1: Division Chief Endorsement">
                    <div class="step-circle">${getStepCircleSvg(r.step1_state, 1)}</div>
                    <div class="step-labels">
                      <span class="step-name">1. Endorsement</span>
                      <span class="step-desc">Division Chief</span>
                    </div>
                  </div>
                  <div class="stepper-connector ${r.step1_state === 'done' ? 'completed' : (r.step2_state === 'active' ? 'active' : '')}"></div>
                  <div class="stepper-step step-${r.step2_state}" title="Stage 2: Property & Logistics Recommendation">
                    <div class="step-circle">${getStepCircleSvg(r.step2_state, 2)}</div>
                    <div class="step-labels">
                      <span class="step-name">2. Recommendation</span>
                      <span class="step-desc">Logistics &amp; Property</span>
                    </div>
                  </div>
                  <div class="stepper-connector ${r.step2_state === 'done' ? 'completed' : (r.step3_state === 'active' ? 'active' : '')}"></div>
                  <div class="stepper-step step-${r.step3_state}" title="Stage 3: Directorate Final Clearance">
                    <div class="step-circle">${getStepCircleSvg(r.step3_state, 3)}</div>
                    <div class="step-labels">
                      <span class="step-name">3. Clearance</span>
                      <span class="step-desc">Directorate IV</span>
                    </div>
                  </div>
                </div>
                <div class="routing-status-label">
                  <span class="routing-step-desc"><strong>${escapeHtml(r.current_stage_label)}</strong></span>
                </div>
              </div>`;
            stepper = tempDiv.firstElementChild;
            mainDiv.appendChild(stepper);
          }
          updateCardStepper(card, r);
        }
      }

      // Update Side Actions (inject Gate Pass & QR if Approved, remove Cancel if no longer pending)
      const sideActions = card.querySelector('.res-card-side-actions');
      if (sideActions) {
        let gatePassBtn = sideActions.querySelector('.btn-gatepass-qr');
        if (r.raw_status === 'Approved' && !gatePassBtn) {
          const btnHtml = document.createElement('button');
          btnHtml.type = 'button';
          btnHtml.className = 'btn-card-action primary btn-gatepass-qr';
          btnHtml.onclick = () => showGatePassModal(r.reference_no, r.event_title, r.venue_title_full, r.formatted_dates);
          btnHtml.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Gate Pass &amp; QR</span>
          `;
          sideActions.prepend(btnHtml);
        } else if (r.raw_status !== 'Approved' && gatePassBtn) {
          gatePassBtn.remove();
        }

        const cancelBtn = sideActions.querySelector('.js-cancel-res');
        const canCancel = ['Pending', 'Pending Recommendation', 'Pending Clearance'].includes(r.raw_status);
        if (canCancel && !cancelBtn) {
          const cBtn = document.createElement('button');
          cBtn.type = 'button';
          cBtn.className = 'btn-card-action danger-outline js-cancel-res';
          cBtn.dataset.ref = r.reference_no;
          cBtn.title = 'Cancel Request';
          cBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <span>Cancel Request</span>
          `;
          sideActions.appendChild(cBtn);
        } else if (!canCancel && cancelBtn) {
          cancelBtn.remove();
        }
      }

      // Also sync Table View row if present
      const tableRow = document.querySelector(`.res-table-row[data-ref="${r.reference_no}"]`);
      if (tableRow) {
        tableRow.dataset.status = r.filter_status;
        tableRow.dataset.rawStatus = r.raw_status;
        tableRow.dataset.statusText = r.status_badge_text;
        const statusCell = tableRow.querySelector('td[data-label="Archive Status"], td[data-label="Status"]');
        if (statusCell) {
          statusCell.innerHTML = `<span class="res-status-badge ${r.status_badge_class}"><span class="status-dot-pulse"></span><span>${escapeHtml(r.status_badge_text)}</span></span>`;
        }
      }
    } else {
      // Create and prepend new card
      const newCard = createReservationCardElement(r, isHistoryPage);
      newCard.style.opacity = '0';
      newCard.style.transform = 'translateY(12px)';
      newCard.style.transition = 'all 0.35s ease';

      const zeroState = document.getElementById('zeroReservationsState');
      if (zeroState) zeroState.style.display = 'none';

      const firstCard = container.querySelector('.res-card');
      if (firstCard) {
        container.insertBefore(newCard, firstCard);
      } else {
        container.appendChild(newCard);
      }

      requestAnimationFrame(() => {
        newCard.style.opacity = '1';
        newCard.style.transform = 'translateY(0)';
      });
    }
  });

  checkZeroState(container);
  applyAllFilters();
}

function checkZeroState(container) {
  const zeroState = document.getElementById('zeroReservationsState');
  if (!zeroState) return;
  const remaining = container.querySelectorAll('.res-card');
  if (remaining.length === 0) {
    zeroState.style.display = 'block';
  } else {
    zeroState.style.display = 'none';
  }
}

function syncOpenModalIfViewing(reservations) {
  const modal = document.getElementById('resDetailsModal');
  if (!modal || !modal.classList.contains('show')) return;
  const currentRef = document.getElementById('modalResRef')?.textContent?.trim();
  if (!currentRef) return;

  const card = document.querySelector(`.res-card[data-ref="${currentRef}"]`);
  if (card) {
    openCardDetailsModal(card);
  }
}

function getStepCircleSvg(state, stepNum) {
  if (state === 'done') {
    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
  } else if (state === 'active') {
    return `<span class="step-pulse-dot"></span><span class="step-num">${stepNum}</span>`;
  } else if (state === 'rejected' || state === 'cancelled') {
    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;
  } else {
    return `<span class="step-num">${stepNum}</span>`;
  }
}

function updateCardStepper(card, r) {
  const stepper = card.querySelector('.res-mini-routing');
  if (!stepper) return;

  const stageChip = stepper.querySelector('.stepper-stage-chip');
  if (stageChip) {
    stageChip.style.color = r.current_stage_color;
    stageChip.style.borderColor = `${r.current_stage_color}44`;
    stageChip.style.backgroundColor = `${r.current_stage_color}14`;
    stageChip.textContent = r.current_stage_label;
  }

  const steps = stepper.querySelectorAll('.stepper-step');
  if (steps.length >= 3) {
    // Step 1
    steps[0].className = `stepper-step step-${r.step1_state}`;
    const circle1 = steps[0].querySelector('.step-circle');
    if (circle1) circle1.innerHTML = getStepCircleSvg(r.step1_state, 1);

    // Step 2
    steps[1].className = `stepper-step step-${r.step2_state}`;
    const circle2 = steps[1].querySelector('.step-circle');
    if (circle2) circle2.innerHTML = getStepCircleSvg(r.step2_state, 2);

    // Step 3
    steps[2].className = `stepper-step step-${r.step3_state}`;
    const circle3 = steps[2].querySelector('.step-circle');
    if (circle3) circle3.innerHTML = getStepCircleSvg(r.step3_state, 3);
  }

  const connectors = stepper.querySelectorAll('.stepper-connector');
  if (connectors.length >= 2) {
    connectors[0].className = `stepper-connector ${r.step1_state === 'done' ? 'completed' : (r.step2_state === 'active' ? 'active' : '')}`;
    connectors[1].className = `stepper-connector ${r.step2_state === 'done' ? 'completed' : (r.step3_state === 'active' ? 'active' : '')}`;
  }

  const routingDesc = stepper.querySelector('.routing-status-label');
  if (routingDesc) {
    if (r.raw_status === 'Approved') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong style="color: #15803d;">Governance Complete:</strong> Approved on Master Schedule &bull; Security Gate Pass ready</span>`;
    } else if (r.raw_status === 'Cancelled') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong style="color: #64748b;">Notice:</strong> Booking request cancelled by applicant</span>`;
    } else if (r.raw_status === 'Completed') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong style="color: #475569;">Concluded:</strong> Event successfully executed &bull; Turn-over complete</span>`;
    } else if (r.raw_status === 'Rejected') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong style="color: #dc2626;">Disapproved:</strong> Application returned during review</span>`;
    } else if (r.raw_status === 'Pending Clearance') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong>Director IV:</strong> Final approval and gate pass generation</span>`;
    } else if (r.raw_status === 'Pending Recommendation') {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong>Property Unit:</strong> Venue inspection and facilities readiness</span>`;
    } else {
      routingDesc.innerHTML = `<span class="routing-step-desc"><strong>Stage 1:</strong> Verification by Division Chief</span>`;
    }
  }
}

function createReservationCardElement(r, isHistoryPage) {
  const article = document.createElement('article');
  article.className = 'res-card';
  article.dataset.status = r.filter_status;
  article.dataset.rawStatus = r.raw_status;
  article.dataset.category = r.category;
  article.dataset.venue = r.slug;
  article.dataset.venueId = r.facility_id;
  article.dataset.venueName = r.venue_name;
  article.dataset.ref = r.reference_no;
  article.dataset.venueTitle = r.venue_title_full;
  article.dataset.venueImage = r.venue_thumb;
  article.dataset.dates = r.formatted_dates;
  article.dataset.time = r.time_slot;
  article.dataset.pax = r.pax_text;
  article.dataset.division = r.division;
  article.dataset.applicantName = r.applicant_name;
  article.dataset.createdAt = r.created_at_text;
  article.dataset.statusText = r.status_badge_text;
  article.dataset.specialNotes = r.special_notes || '';
  article.dataset.remarks = r.admin_remarks || '';
  article.dataset.docPath = r.document_path || '';
  article.dataset.endorsedBy = r.endorsed_by || '';
  article.dataset.endorsedAt = r.endorsed_at || '';
  article.dataset.endorsementRemarks = r.endorsement_remarks || '';
  article.dataset.recommendedBy = r.recommended_by || '';
  article.dataset.recommendedAt = r.recommended_at || '';
  article.dataset.recommendationRemarks = r.recommendation_remarks || '';
  article.dataset.recommendationDoc = r.recommendation_doc_path || '';
  article.dataset.clearedBy = r.cleared_by || '';
  article.dataset.clearedAt = r.cleared_at || '';
  article.dataset.clearanceRemarks = r.clearance_remarks || '';
  article.dataset.clearanceDoc = r.clearance_doc_path || '';

  const pillClass = r.is_guesthouse ? 'guesthouse-pill' : (r.is_dorm ? 'dorm-pill' : '');
  const refClass = r.is_guesthouse ? 'guesthouse-ref' : (r.is_dorm ? 'dorm-ref' : '');
  const fallbackImg = r.is_guesthouse ? 'assets/images/guesthouse_1.jpg' : (r.is_dorm ? 'assets/images/dormitory.jpg' : 'assets/images/function_hall.jpg');

  let middleHtml = '';
  if (r.raw_status === 'Completed') {
    middleHtml = `
      <div class="res-concluded-summary">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>${escapeHtml(r.admin_remarks || 'Activity successfully conducted. Facility inspection cleared & venue turned over. Record permanently archived.')}</span>
      </div>`;
  } else if (['Cancelled', 'Rejected'].includes(r.raw_status)) {
    const isCanc = (r.raw_status === 'Cancelled');
    middleHtml = `
      <div class="res-cancellation-notice">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <span><strong>${isCanc ? 'Cancellation Notice:' : 'Disapproval Notice:'}</strong> ${escapeHtml(r.admin_remarks || (isCanc ? 'Reservation was cancelled by the applicant. Schedule was released back to availability.' : 'Application was disapproved / returned during administrative review.'))}</span>
      </div>`;
  } else {
    // 3-Stage Governance Routing Tracker for Pending & Approved
    middleHtml = `
      <div class="res-mini-routing">
        <div class="routing-stepper-header">
          <div class="stepper-header-left">
            <span class="stepper-badge-icon">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </span>
            <span class="stepper-title">GOVERNANCE ROUTING TRACKER</span>
          </div>
          <span class="stepper-stage-chip" style="color: ${r.current_stage_color}; border-color: ${r.current_stage_color}44; background: ${r.current_stage_color}14;">
            ${escapeHtml(r.current_stage_label)}
          </span>
        </div>
        <div class="routing-stepper-track">
          <div class="stepper-step step-${r.step1_state}" title="Stage 1: Division Chief Endorsement">
            <div class="step-circle">${getStepCircleSvg(r.step1_state, 1)}</div>
            <div class="step-labels">
              <span class="step-name">1. Endorsement</span>
              <span class="step-desc">Division Chief</span>
            </div>
          </div>
          <div class="stepper-connector ${r.step1_state === 'done' ? 'completed' : (r.step2_state === 'active' ? 'active' : '')}"></div>
          <div class="stepper-step step-${r.step2_state}" title="Stage 2: Property & Logistics Recommendation">
            <div class="step-circle">${getStepCircleSvg(r.step2_state, 2)}</div>
            <div class="step-labels">
              <span class="step-name">2. Recommendation</span>
              <span class="step-desc">Logistics &amp; Property</span>
            </div>
          </div>
          <div class="stepper-connector ${r.step2_state === 'done' ? 'completed' : (r.step3_state === 'active' ? 'active' : '')}"></div>
          <div class="stepper-step step-${r.step3_state}" title="Stage 3: Directorate Final Clearance">
            <div class="step-circle">${getStepCircleSvg(r.step3_state, 3)}</div>
            <div class="step-labels">
              <span class="step-name">3. Clearance</span>
              <span class="step-desc">Directorate IV</span>
            </div>
          </div>
        </div>
        <div class="routing-status-label">
          <span class="routing-step-desc"><strong>${escapeHtml(r.current_stage_label)}</strong></span>
        </div>
      </div>`;
  }

  let gatepassBtn = '';
  if (r.raw_status === 'Approved') {
    gatepassBtn = `
      <button type="button" class="btn-card-action primary btn-gatepass-qr" onclick="showGatePassModal('${escapeHtml(r.reference_no)}', '${escapeHtml(r.event_title)}', '${escapeHtml(r.venue_title_full)}', '${escapeHtml(r.formatted_dates)}')">
        <svg viewBox="0 0 24 24" fill="none">
          <rect x="3" y="3" width="7" height="7"></rect>
          <rect x="14" y="3" width="7" height="7"></rect>
          <rect x="14" y="14" width="7" height="7"></rect>
          <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
        <span>Gate Pass &amp; QR</span>
      </button>`;
  }

  let cancelBtn = '';
  if (['Pending', 'Pending Recommendation', 'Pending Clearance'].includes(r.raw_status)) {
    cancelBtn = `
      <button type="button" class="btn-card-action danger-outline js-cancel-res" data-ref="${escapeHtml(r.reference_no)}" title="Cancel Request">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <span>Cancel Request</span>
      </button>`;
  }

  const slipLabel = r.raw_status === 'Completed' ? 'Archived Slip' : (['Cancelled', 'Rejected'].includes(r.raw_status) ? 'Notice Slip' : 'Download Slip');

  const sideActionsHtml = `
    ${gatepassBtn}
    <button type="button" class="btn-card-action secondary js-view-details">
      <svg viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="16" x2="12" y2="12"></line>
        <line x1="12" y1="8" x2="12.01" y2="8"></line>
      </svg>
      <span>${isHistoryPage ? 'View Details' : 'View Details &amp; Routing'}</span>
    </button>
    <button type="button" class="btn-card-action secondary js-download-slip" data-ref="${escapeHtml(r.reference_no)}" title="Download Official Slip">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
        <polyline points="7 10 12 15 17 10"></polyline>
        <line x1="12" y1="15" x2="12" y2="3"></line>
      </svg>
      <span>${slipLabel}</span>
    </button>
    ${cancelBtn}
  `;

  article.innerHTML = `
    <div class="res-card-media">
      <img src="${escapeHtml(r.venue_thumb)}" alt="${escapeHtml(r.venue_name)}" onerror="this.onerror=null; this.src='${fallbackImg}';">
      <span class="venue-thumb-pill ${pillClass}">${escapeHtml(r.venue_name)}</span>
    </div>
    <div class="res-card-main">
      <div class="res-main-top">
        <div class="res-ref-group">
          <span class="res-ref-tag ${refClass}">${escapeHtml(r.reference_no)}</span>
          <button type="button" class="btn-copy-ref" data-ref="${escapeHtml(r.reference_no)}" title="Copy Reference Code">
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
            </svg>
          </button>
          <span class="res-timestamp">&bull; ${escapeHtml(r.status_prefix || r.created_date)}</span>
        </div>
        <div class="res-status-badge ${r.status_badge_class}">
          <span class="status-dot-pulse"></span>
          <span>${escapeHtml(r.status_badge_text)}</span>
        </div>
      </div>
      <h3 class="res-event-title">${escapeHtml(r.event_title)}</h3>
      <div class="res-details-chips">
        <div class="res-chip-item">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span>${escapeHtml(r.formatted_dates)}</span>
        </div>
        <div class="res-chip-item">
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
          <span>${escapeHtml(r.time_slot)}</span>
        </div>
        <div class="res-chip-item">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          </svg>
          <span>${escapeHtml(r.venue_title_full)}</span>
        </div>
        <div class="res-chip-item">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <span>${escapeHtml(r.pax_text)} &bull; ${escapeHtml(r.division)}</span>
        </div>
      </div>
      ${middleHtml}
    </div>
    <div class="res-card-side-actions">
      ${sideActionsHtml}
    </div>
  `;

  return article;
}

function showRealtimeNotification(title, message, type = 'info') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast-notice ${type}`;
  toast.setAttribute('role', 'alert');
  toast.style.cursor = 'pointer';

  let iconSvg = '';
  if (type === 'success') {
    iconSvg = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#86efac" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
  } else if (type === 'danger') {
    iconSvg = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fca5a5" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
  } else if (type === 'warning') {
    iconSvg = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fde68a" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`;
  } else {
    iconSvg = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#5eead4" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
  }

  toast.innerHTML = `
    ${iconSvg}
    <div style="flex: 1; min-width: 0;">
      <div style="font-weight: 800; font-size: 0.92rem; margin-bottom: 2px;">${escapeHtml(title)}</div>
      <div style="font-size: 0.8rem; opacity: 0.95; line-height: 1.35;">${escapeHtml(message)}</div>
    </div>
  `;

  toast.addEventListener('click', () => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  });

  container.appendChild(toast);
  requestAnimationFrame(() => {
    toast.classList.add('show');
  });

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 350);
  }, 4500);
}

