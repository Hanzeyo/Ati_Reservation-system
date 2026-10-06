/**
 * Agriculture Training Institute - Facilities & Dormitories Manager Script
 * Institutional Asset Controller for Halls, Meeting Rooms, and Dorm Suites
 */

// In-memory master facilities inventory data
const facilitiesData = {
  'serrano': {
    id: 'serrano',
    name: 'Serrano Function Hall',
    category: 'halls',
    categoryLabel: 'Function & Assembly Hall',
    pax: 120,
    beds: 0,
    location: 'Main Training Building, 1st Floor',
    image: 'assets/images/function_hall.jpg',
    features: ['Airconditioned', 'Dual Laser Projectors', 'High-power Sound System', 'Wireless Mics', 'Stage Rostrum', 'Podium', 'Fiber Wi-Fi'],
    status: 'occupied',
    statusLabel: 'Occupied (Oct 06-07)',
    maintenance: false,
    maintenanceReason: '',
    estResumeDate: '',
    notes: 'Primary venue for national-level training of trainers (TOT) and general assemblies.'
  },
  'four_h': {
    id: 'four_h',
    name: '4-H Learning Center',
    category: 'halls',
    categoryLabel: 'Multi-purpose Training Hall',
    pax: 80,
    beds: 0,
    location: '4-H Youth Building, 2nd Floor',
    image: 'assets/images/training_hall.jpg',
    features: ['Airconditioned', '75" Interactive 4K TV', 'Moveable Modular Tables', 'Whiteboards', 'Public Address System', 'High-speed Wi-Fi'],
    status: 'available',
    statusLabel: 'Available',
    maintenance: false,
    maintenanceReason: '',
    estResumeDate: '',
    notes: 'Configurable layout for workshops, digital agriculture training, and group sessions.'
  },
  'boardroom': {
    id: 'boardroom',
    name: 'Executive Boardroom',
    category: 'halls',
    categoryLabel: 'Executive Conference Room',
    pax: 25,
    beds: 0,
    location: "Director's Wing, 3rd Floor",
    image: 'assets/images/boardroom.jpg',
    features: ['Hybrid Teleconferencing', 'PTZ Auto-track Cameras', 'Executive Leather Chairs', 'Private Restroom', 'Lounge Holding Area'],
    status: 'available',
    statusLabel: 'Available',
    maintenance: false,
    maintenanceReason: '',
    estResumeDate: '',
    notes: 'Restricted for Directorates, ManCom hearings, and inter-agency high-level meetings.'
  },
  'mess_hall': {
    id: 'mess_hall',
    name: 'ATI Mess Hall & Dining Pavilion',
    category: 'dining',
    categoryLabel: 'Institutional Dining Hall',
    pax: 150,
    beds: 0,
    location: 'Services Annex, Ground Floor',
    image: 'assets/images/mess_hall.jpg',
    features: ['Buffet Serving Counters', 'Industrial Ceiling Fans', 'Filtered Water Stations', 'Handwashing Sinks', 'PA Background Music'],
    status: 'available',
    statusLabel: 'Available',
    maintenance: false,
    maintenanceReason: '',
    estResumeDate: '',
    notes: 'Serves breakfast, lunch, dinner, and executive banquet catering for training participants.'
  },
  'dorm_a': {
    id: 'dorm_a',
    name: 'Dormitory Suite A (Male Wing)',
    category: 'dorms',
    categoryLabel: 'Institutional Dormitory',
    pax: 24,
    beds: 24,
    location: 'Dormitory Building, Wing A (2nd Floor)',
    image: 'assets/images/dormitory.jpg',
    features: ['24 Spring Beds (6 Quads)', 'En-suite Bathrooms', 'Hot Water Showers', 'Individual Steel Lockers', 'Study Lounge', 'Free Wi-Fi'],
    status: 'occupied',
    statusLabel: 'Reserved (Oct 15-18)',
    maintenance: false,
    maintenanceReason: '',
    estResumeDate: '',
    notes: 'Accommodates agricultural extension workers and regional training participants.'
  },
  'dorm_b': {
    id: 'dorm_b',
    name: 'Dormitory Suite B (Female Wing)',
    category: 'dorms',
    categoryLabel: 'Institutional Dormitory',
    pax: 32,
    beds: 32,
    location: 'Dormitory Building, Wing B (3rd Floor)',
    image: 'assets/images/dormitory.jpg',
    features: ['32 Spring Beds (8 Quads)', 'En-suite Bathrooms', 'Hot Water Showers', 'Individual Steel Lockers', '24/7 Security Desk'],
    status: 'maintenance',
    statusLabel: 'Under Maintenance',
    maintenance: true,
    maintenanceReason: 'Annual Airconditioning Deep Cleaning & Bunk Fumigation',
    estResumeDate: 'Oct 09, 2026',
    notes: 'Scheduled periodic maintenance hold as endorsed by General Services Unit.'
  }
};

let currentPendingMaintenanceId = null;
let currentViewMode = 'grid';

document.addEventListener('DOMContentLoaded', function () {
  initDateDisplay();
  updateKPICounters();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }
});

/* ==========================================================================
   DATE DISPLAY & SIDEBAR TOGGLE (RESPONSIVE & DESKTOP)
   ========================================================================== */
function initDateDisplay() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (dateEl) {
    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString('en-US', options);
  }
}

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
   VIEW MODE TOGGLE (GRID VS TABLE)
   ========================================================================== */
function setViewMode(mode) {
  currentViewMode = mode;
  const gridContainer = document.getElementById('facilityCardsGrid');
  const tableContainer = document.getElementById('facilityTableView');
  const btnGrid = document.getElementById('btnViewGrid');
  const btnTable = document.getElementById('btnViewTable');

  if (mode === 'grid') {
    if (gridContainer) gridContainer.style.display = 'grid';
    if (tableContainer) tableContainer.style.display = 'none';
    if (btnGrid) btnGrid.classList.add('active');
    if (btnTable) btnTable.classList.remove('active');
  } else {
    if (gridContainer) gridContainer.style.display = 'none';
    if (tableContainer) tableContainer.style.display = 'block';
    if (btnGrid) btnGrid.classList.remove('active');
    if (btnTable) btnTable.classList.add('active');
  }
}

/* ==========================================================================
   FILTER & SEARCH CONTROLLERS
   ========================================================================== */
function filterFacilitiesByCategory(category, tabBtn) {
  if (tabBtn) {
    document.querySelectorAll('.facility-filter-tab').forEach(t => t.classList.remove('active'));
    tabBtn.classList.add('active');
  }

  const query = (document.getElementById('facilitySearchInput')?.value || '').toLowerCase().trim();
  applyCombinedFilters(category, query);
}

function handleFacilitySearch(query) {
  const activeTab = document.querySelector('.facility-filter-tab.active');
  const category = activeTab ? activeTab.getAttribute('data-category') : 'all';
  applyCombinedFilters(category, query.toLowerCase().trim());
}

function applyCombinedFilters(category, query) {
  // Filter Grid Cards
  const cards = document.querySelectorAll('.facility-card-item');
  cards.forEach(card => {
    const cardCat = card.getAttribute('data-category');
    const isMaint = card.getAttribute('data-maintenance') === 'true';
    const text = card.innerText.toLowerCase();

    let matchesCategory = false;
    if (category === 'all') matchesCategory = true;
    else if (category === 'maintenance') matchesCategory = isMaint;
    else matchesCategory = (cardCat === category);

    const matchesQuery = !query || text.includes(query);

    card.style.display = (matchesCategory && matchesQuery) ? '' : 'none';
  });

  // Filter Table Rows
  const rows = document.querySelectorAll('#facilityTableBody tr');
  rows.forEach(row => {
    const rowCat = row.getAttribute('data-category');
    const isMaint = row.getAttribute('data-maintenance') === 'true';
    const text = row.innerText.toLowerCase();

    let matchesCategory = false;
    if (category === 'all') matchesCategory = true;
    else if (category === 'maintenance') matchesCategory = isMaint;
    else matchesCategory = (rowCat === category);

    const matchesQuery = !query || text.includes(query);

    row.style.display = (matchesCategory && matchesQuery) ? '' : 'none';
  });
}

/* ==========================================================================
   MAINTENANCE HOLD CONTROLLER
   ========================================================================== */
function onMaintenanceSwitchChange(facilityId, isChecked) {
  const item = facilitiesData[facilityId];
  if (!item) return;

  if (isChecked) {
    // Open maintenance modal for justification reason
    currentPendingMaintenanceId = facilityId;
    const nameEl = document.getElementById('maintModalFacilityName');
    if (nameEl) nameEl.textContent = item.name;

    const modal = document.getElementById('maintenanceModal');
    if (modal) modal.style.display = 'flex';
  } else {
    // Restore to Available
    item.maintenance = false;
    item.status = 'available';
    item.statusLabel = 'Available';
    item.maintenanceReason = '';
    item.estResumeDate = '';

    updateFacilityDOMState(facilityId);
    updateKPICounters();
    showAdminToast(`${item.name} has been restored to Active & Available status.`, 'success');
  }
}

function confirmMaintenanceHold() {
  if (!currentPendingMaintenanceId) return;

  const item = facilitiesData[currentPendingMaintenanceId];
  if (!item) return;

  const reasonSelect = document.getElementById('maintReasonSelect');
  const remarksInput = document.getElementById('maintRemarksInput');
  const dateInput = document.getElementById('maintEstResumeDate');

  const reason = reasonSelect ? reasonSelect.value : 'Routine Maintenance';
  const remarks = remarksInput ? remarksInput.value.trim() : '';
  const date = dateInput ? dateInput.value : '';

  item.maintenance = true;
  item.status = 'maintenance';
  item.statusLabel = 'Under Maintenance';
  item.maintenanceReason = remarks ? `${reason}: ${remarks}` : reason;
  item.estResumeDate = date || 'Until Further Notice';

  updateFacilityDOMState(currentPendingMaintenanceId);
  updateKPICounters();
  closeMaintenanceModal();

  showAdminToast(`${item.name} placed on Maintenance Hold.`, 'decline');
}

function closeMaintenanceModal() {
  const modal = document.getElementById('maintenanceModal');
  if (modal) modal.style.display = 'none';

  // If user cancelled, restore the checkbox to false
  if (currentPendingMaintenanceId && !facilitiesData[currentPendingPending = currentPendingMaintenanceId]?.maintenance) {
    const cardToggle = document.querySelector(`.facility-card-item[data-id="${currentPendingMaintenanceId}"] input[type="checkbox"]`);
    const tableToggle = document.querySelector(`#facilityTableBody tr[data-id="${currentPendingMaintenanceId}"] input[type="checkbox"]`);
    if (cardToggle) cardToggle.checked = false;
    if (tableToggle) tableToggle.checked = false;
  }
  currentPendingMaintenanceId = null;
}

function updateFacilityDOMState(facilityId) {
  const item = facilitiesData[facilityId];
  if (!item) return;

  // Update Card Item
  const card = document.querySelector(`.facility-card-item[data-id="${facilityId}"]`);
  if (card) {
    card.setAttribute('data-maintenance', item.maintenance ? 'true' : 'false');
    const badge = card.querySelector('.facility-status-pill-overlay');
    if (badge) {
      badge.className = `facility-status-pill-overlay state-${item.status}`;
      badge.textContent = item.statusLabel;
    }
    const checkbox = card.querySelector('input[type="checkbox"]');
    if (checkbox) checkbox.checked = item.maintenance;
  }

  // Update Table Row
  const row = document.querySelector(`#facilityTableBody tr[data-id="${facilityId}"]`);
  if (row) {
    row.setAttribute('data-maintenance', item.maintenance ? 'true' : 'false');
    const badge = row.querySelector('.status-badge-chip');
    if (badge) {
      if (item.status === 'available') {
        badge.className = 'status-badge-chip chip-confirmed';
        badge.textContent = 'Available';
      } else if (item.status === 'occupied') {
        badge.className = 'status-badge-chip chip-pending';
        badge.textContent = item.statusLabel;
      } else {
        badge.className = 'status-badge-chip chip-declined';
        badge.textContent = 'Maintenance Hold';
      }
    }
    const checkbox = row.querySelector('input[type="checkbox"]');
    if (checkbox) checkbox.checked = item.maintenance;
  }
}

/* ==========================================================================
   IMAGE UPLOAD & PRESET PREVIEW CONTROLLER
   ========================================================================== */
function handleFacilityFilePreview(fileInput, previewImgId, hiddenSrcId) {
  if (!fileInput.files || !fileInput.files[0]) return;

  const file = fileInput.files[0];
  const reader = new FileReader();

  reader.onload = function (e) {
    const previewImg = document.getElementById(previewImgId);
    const hiddenSrc = document.getElementById(hiddenSrcId);
    if (previewImg) previewImg.src = e.target.result;
    if (hiddenSrc) hiddenSrc.value = e.target.result;
    showAdminToast(`Selected "${file.name}" for upload.`, 'success');
  };

  reader.readAsDataURL(file);
}

function applyPresetImage(presetPath, previewImgId, hiddenSrcId) {
  if (!presetPath) return;

  const previewImg = document.getElementById(previewImgId);
  const hiddenSrc = document.getElementById(hiddenSrcId);
  if (previewImg) previewImg.src = presetPath;
  if (hiddenSrc) hiddenSrc.value = presetPath;
}

/* ==========================================================================
   EDIT SPECIFICATIONS MODAL
   ========================================================================== */
let activeEditFacilityId = null;

function openEditFacilityModal(facilityId) {
  const item = facilitiesData[facilityId];
  if (!item) return;

  activeEditFacilityId = facilityId;

  document.getElementById('editFacilityId').value = facilityId;
  document.getElementById('editFacilityName').value = item.name;
  document.getElementById('editFacilityCategory').value = item.category;
  document.getElementById('editFacilityPax').value = item.pax;
  document.getElementById('editFacilityBeds').value = item.beds;
  document.getElementById('editFacilityLocation').value = item.location;
  document.getElementById('editFacilityFeatures').value = item.features.join(', ');
  document.getElementById('editFacilityNotes').value = item.notes || '';

  // Populate Image Preview
  const imgPreview = document.getElementById('editFacilityImgPreview');
  const imgHidden = document.getElementById('editFacilityImageSrc');
  const presetSelect = document.getElementById('editFacilityPresetSelect');
  if (imgPreview) imgPreview.src = item.image;
  if (imgHidden) imgHidden.value = item.image;
  if (presetSelect) presetSelect.value = item.image.startsWith('assets/images/') ? item.image : '';

  const modal = document.getElementById('editFacilityModal');
  if (modal) modal.style.display = 'flex';
}

function closeEditFacilityModal() {
  const modal = document.getElementById('editFacilityModal');
  if (modal) modal.style.display = 'none';
  activeEditFacilityId = null;
}

function saveFacilityChanges(e) {
  if (e) e.preventDefault();
  if (!activeEditFacilityId) return;

  const item = facilitiesData[activeEditFacilityId];
  if (!item) return;

  item.name = document.getElementById('editFacilityName').value.trim();
  item.category = document.getElementById('editFacilityCategory').value;
  item.pax = parseInt(document.getElementById('editFacilityPax').value, 10) || 0;
  item.beds = parseInt(document.getElementById('editFacilityBeds').value, 10) || 0;
  item.location = document.getElementById('editFacilityLocation').value.trim();
  item.notes = document.getElementById('editFacilityNotes').value.trim();

  // Save updated photo if changed
  const newImgSrc = document.getElementById('editFacilityImageSrc')?.value;
  if (newImgSrc) {
    item.image = newImgSrc;
  }

  const featStr = document.getElementById('editFacilityFeatures').value;
  item.features = featStr.split(',').map(f => f.trim()).filter(Boolean);

  // Update DOM Title, Image, and Specs in Card
  const card = document.querySelector(`.facility-card-item[data-id="${activeEditFacilityId}"]`);
  if (card) {
    card.setAttribute('data-category', item.category);

    const imgEl = card.querySelector('.facility-card-img');
    if (imgEl && item.image) imgEl.src = item.image;

    const titleEl = card.querySelector('.facility-card-title');
    if (titleEl) titleEl.textContent = item.name;

    const locEl = card.querySelector('.facility-location-tag span');
    if (locEl) locEl.textContent = item.location;

    const capValEl = card.querySelector('.spec-capacity-val');
    if (capValEl) capValEl.textContent = item.category === 'dorms' ? `${item.beds} Beds (${item.pax} PAX)` : `${item.pax} Participants`;

    // Rebuild features
    const featsWrap = card.querySelector('.facility-features-wrap');
    if (featsWrap) {
      featsWrap.innerHTML = item.features.slice(0, 5).map(f => `<span class="feature-pill-tag">${f}</span>`).join('');
    }
  }

  // Update DOM in Table
  const row = document.querySelector(`#facilityTableBody tr[data-id="${activeEditFacilityId}"]`);
  if (row) {
    row.setAttribute('data-category', item.category);

    const thumbEl = row.querySelector('.facility-table-thumb');
    if (thumbEl && item.image) thumbEl.src = item.image;

    const nameEl = row.querySelector('.facility-table-info-text strong');
    if (nameEl) nameEl.textContent = item.name;
    const locEl = row.querySelector('.facility-table-info-text span');
    if (locEl) locEl.textContent = item.location;
    const capCell = row.children[2];
    if (capCell) capCell.innerHTML = `<strong>${item.pax} PAX</strong>${item.beds > 0 ? `<br><small style="color: #637f6f;">${item.beds} Beds</small>` : ''}`;
  }

  updateKPICounters();
  closeEditFacilityModal();
  showAdminToast(`Specifications and photo updated for ${item.name}.`, 'success');
}

/* ==========================================================================
   ADD NEW FACILITY MODAL
   ========================================================================== */
function openAddFacilityModal() {
  const form = document.getElementById('addFacilityForm');
  if (form) form.reset();

  const preview = document.getElementById('addFacilityImgPreview');
  const hiddenSrc = document.getElementById('addFacilityImageSrc');
  if (preview) preview.src = 'assets/images/function_hall.jpg';
  if (hiddenSrc) hiddenSrc.value = 'assets/images/function_hall.jpg';

  const modal = document.getElementById('addFacilityModal');
  if (modal) modal.style.display = 'flex';
}

function closeAddFacilityModal() {
  const modal = document.getElementById('addFacilityModal');
  if (modal) modal.style.display = 'none';
}

function handleAddNewFacility(e) {
  if (e) e.preventDefault();

  const name = document.getElementById('addFacilityName').value.trim();
  const category = document.getElementById('addFacilityCategory').value;
  const pax = parseInt(document.getElementById('addFacilityPax').value, 10) || 0;
  const beds = parseInt(document.getElementById('addFacilityBeds').value, 10) || 0;
  const location = document.getElementById('addFacilityLocation').value.trim();
  const featStr = document.getElementById('addFacilityFeatures').value;
  const features = featStr.split(',').map(f => f.trim()).filter(Boolean);

  const newId = 'fac_' + Date.now();
  const uploadedOrPresetImg = document.getElementById('addFacilityImageSrc')?.value;
  const defaultFallbackImg = category === 'dorms' ? 'assets/images/dormitory.jpg' : (category === 'halls' ? 'assets/images/function_hall.jpg' : 'assets/images/mess_hall.jpg');
  const finalImg = uploadedOrPresetImg || defaultFallbackImg;

  facilitiesData[newId] = {
    id: newId,
    name: name,
    category: category,
    categoryLabel: category === 'halls' ? 'Training & Function Hall' : (category === 'dorms' ? 'Institutional Dormitory' : 'Dining Facility'),
    pax: pax,
    beds: beds,
    location: location,
    image: finalImg,
    features: features,
    status: 'available',
    statusLabel: 'Available',
    maintenance: false,
    notes: ''
  };

  // Prepend to Grid
  const grid = document.getElementById('facilityCardsGrid');
  if (grid) {
    const cardEl = document.createElement('div');
    cardEl.className = 'facility-card-item';
    cardEl.setAttribute('data-id', newId);
    cardEl.setAttribute('data-category', category);
    cardEl.setAttribute('data-maintenance', 'false');
    cardEl.innerHTML = `
      <div class="facility-card-media">
        <img src="${finalImg}" alt="${name}" class="facility-card-img">
        <span class="facility-type-badge">${category}</span>
        <span class="facility-status-pill-overlay state-available">Available</span>
      </div>
      <div class="facility-card-body">
        <div class="facility-card-header-row">
          <h3 class="facility-card-title">${name}</h3>
        </div>
        <div class="facility-location-tag">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          <span>${location}</span>
        </div>
        <div class="facility-specs-grid">
          <div class="spec-cell-item">
            <span class="spec-label">Capacity</span>
            <strong class="spec-val spec-capacity-val">${beds > 0 ? `${beds} Beds (${pax} PAX)` : `${pax} Participants`}</strong>
          </div>
          <div class="spec-cell-item">
            <span class="spec-label">Access Mode</span>
            <strong class="spec-val">Institutional</strong>
          </div>
        </div>
        <div class="facility-features-wrap">
          ${features.slice(0, 5).map(f => `<span class="feature-pill-tag">${f}</span>`).join('')}
        </div>
        <div class="facility-card-footer">
          <div class="facility-maintenance-quick-toggle">
            <label class="switch-toggle" title="Toggle Maintenance Mode">
              <input type="checkbox" onchange="onMaintenanceSwitchChange('${newId}', this.checked)">
              <span class="slider-round"></span>
            </label>
            <span>Maintenance</span>
          </div>
          <div class="card-action-btns">
            <button type="button" class="btn-card-action" onclick="openEditFacilityModal('${newId}')">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
              <span>Edit</span>
            </button>
            <a href="admin_schedule.php" class="btn-card-action" title="View Schedule">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            </a>
          </div>
        </div>
      </div>
    `;
    grid.insertBefore(cardEl, grid.firstChild);
  }

  // Prepend to Table
  const tbody = document.getElementById('facilityTableBody');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', newId);
    tr.setAttribute('data-category', category);
    tr.setAttribute('data-maintenance', 'false');
    tr.innerHTML = `
      <td>
        <div class="facility-table-info-cell">
          <img src="${finalImg}" alt="${name}" class="facility-table-thumb">
          <div class="facility-table-info-text">
            <strong>${name}</strong>
            <span>${location}</span>
          </div>
        </div>
      </td>
      <td><span class="event-type-chip maintenance">${category.toUpperCase()}</span></td>
      <td><strong>${pax} PAX</strong>${beds > 0 ? `<br><small style="color: #637f6f;">${beds} Beds</small>` : ''}</td>
      <td><div class="facility-features-wrap" style="margin: 0;">${features.slice(0, 3).map(f => `<span class="feature-pill-tag">${f}</span>`).join('')}</div></td>
      <td>
        <label class="switch-toggle">
          <input type="checkbox" onchange="onMaintenanceSwitchChange('${newId}', this.checked)">
          <span class="slider-round"></span>
        </label>
      </td>
      <td><span class="status-badge-chip chip-confirmed">Available</span></td>
      <td style="text-align: right;">
        <button type="button" class="btn-card-action" onclick="openEditFacilityModal('${newId}')">Configure</button>
      </td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
  }

  updateKPICounters();
  closeAddFacilityModal();
  showAdminToast(`Successfully registered "${name}" with photo.`, 'success');
}

/* ==========================================================================
   KPI COUNTER UPDATER
   ========================================================================== */
function updateKPICounters() {
  const items = Object.values(facilitiesData);
  const total = items.length;
  let available = 0;
  let occupied = 0;
  let maintenance = 0;
  let totalPax = 0;
  let totalBeds = 0;

  items.forEach(it => {
    if (it.maintenance || it.status === 'maintenance') maintenance++;
    else if (it.status === 'occupied') occupied++;
    else available++;

    totalPax += (it.pax || 0);
    totalBeds += (it.beds || 0);
  });

  const kpiTotal = document.getElementById('kpiTotalFacilities');
  const kpiAvailable = document.getElementById('kpiAvailableFacilities');
  const kpiOccupied = document.getElementById('kpiOccupiedFacilities');
  const kpiMaintenance = document.getElementById('kpiMaintenanceFacilities');
  const kpiCapacity = document.getElementById('kpiTotalCapacity');

  if (kpiTotal) kpiTotal.textContent = total;
  if (kpiAvailable) kpiAvailable.textContent = available;
  if (kpiOccupied) kpiOccupied.textContent = occupied;
  if (kpiMaintenance) kpiMaintenance.textContent = maintenance;
  if (kpiCapacity) kpiCapacity.textContent = `${totalPax} PAX / ${totalBeds} Beds`;
}

/* ==========================================================================
   CSV EXPORT GENERATOR
   ========================================================================== */
function exportFacilityInventoryCSV() {
  const rows = [
    ['Asset ID', 'Facility Name', 'Category', 'Capacity (PAX)', 'Bed Count', 'Building Location', 'Operational Status', 'Maintenance Mode', 'Amenities']
  ];

  Object.values(facilitiesData).forEach(f => {
    rows.push([
      f.id,
      `"${f.name}"`,
      f.categoryLabel,
      f.pax,
      f.beds,
      `"${f.location}"`,
      f.statusLabel,
      f.maintenance ? 'YES - ' + f.maintenanceReason : 'NO',
      `"${f.features.join('; ')}"`
    ]);
  });

  const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `ATI_Institutional_Facilities_Inventory_${new Date().toISOString().slice(0, 10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showAdminToast('Official Facility Inventory exported successfully as CSV.', 'success');
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
