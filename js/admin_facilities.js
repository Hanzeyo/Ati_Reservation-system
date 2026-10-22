/**
 * Agriculture Training Institute - Facilities & Dormitories Manager Script
 * Institutional Asset Controller for Halls, Meeting Rooms, and Dorm Suites
 * Connected to MySQL Database via ajax/facility_actions.php
 */

// In-memory master facilities inventory data (dynamically loaded from PHP database)
let facilitiesData = window.initialFacilitiesData || {};

let currentPendingMaintenanceId = null;
let currentViewMode = 'grid';

document.addEventListener('DOMContentLoaded', function () {
  initDateDisplay();
  updateKPICounters();
  applyCombinedFilters('all', '');

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
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
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
  } else {
    document.querySelectorAll('.facility-filter-tab').forEach(t => {
      if (t.getAttribute('data-category') === category) {
        t.classList.add('active');
      } else {
        t.classList.remove('active');
      }
    });
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
    const isArchived = card.getAttribute('data-archived') === 'true';
    const text = card.innerText.toLowerCase();

    let matchesCategory = false;
    if (category === 'archived') {
      matchesCategory = isArchived;
    } else {
      if (isArchived) {
        matchesCategory = false;
      } else if (category === 'all') {
        matchesCategory = true;
      } else if (category === 'maintenance') {
        matchesCategory = isMaint;
      } else {
        matchesCategory = (cardCat === category);
      }
    }

    const matchesQuery = !query || text.includes(query);

    card.style.display = (matchesCategory && matchesQuery) ? '' : 'none';
  });

  // Filter Table Rows
  const rows = document.querySelectorAll('#facilityTableBody tr');
  rows.forEach(row => {
    const rowCat = row.getAttribute('data-category');
    const isMaint = row.getAttribute('data-maintenance') === 'true';
    const isArchived = row.getAttribute('data-archived') === 'true';
    const text = row.innerText.toLowerCase();

    let matchesCategory = false;
    if (category === 'archived') {
      matchesCategory = isArchived;
    } else {
      if (isArchived) {
        matchesCategory = false;
      } else if (category === 'all') {
        matchesCategory = true;
      } else if (category === 'maintenance') {
        matchesCategory = isMaint;
      } else {
        matchesCategory = (rowCat === category);
      }
    }

    const matchesQuery = !query || text.includes(query);

    row.style.display = (matchesCategory && matchesQuery) ? '' : 'none';
  });
}

/* ==========================================================================
   MAINTENANCE HOLD CONTROLLER (CONNECTED VIA AJAX)
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
    // Restore to Available via AJAX
    const formData = new FormData();
    formData.append('action', 'toggle_maintenance');
    formData.append('id', item.db_id || item.id);
    formData.append('maintenance', 'false');

    fetch('ajax/facility_actions.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        item.maintenance = false;
        item.status = 'available';
        item.statusLabel = 'Available';
        item.maintenanceReason = '';
        item.estResumeDate = '';

        updateFacilityDOMState(facilityId);
        updateKPICounters();
        showAdminToast(`${item.name} has been restored to Active & Available status.`, 'success');
      } else {
        showAdminToast(data.message || 'Failed to update maintenance status.', 'decline');
        // Revert UI toggle
        const cardToggle = document.querySelector(`.facility-card-item[data-id="${facilityId}"] input[type="checkbox"]`);
        const tableToggle = document.querySelector(`#facilityTableBody tr[data-id="${facilityId}"] input[type="checkbox"]`);
        if (cardToggle) cardToggle.checked = true;
        if (tableToggle) tableToggle.checked = true;
      }
    })
    .catch(err => {
      console.error(err);
      showAdminToast('Network error while updating maintenance status.', 'decline');
    });
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
  const finalReason = remarks ? `${reason}: ${remarks}` : reason;

  const formData = new FormData();
  formData.append('action', 'toggle_maintenance');
  formData.append('id', item.db_id || item.id);
  formData.append('maintenance', 'true');
  formData.append('reason', finalReason);
  formData.append('resume_date', date);

  fetch('ajax/facility_actions.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      item.maintenance = true;
      item.status = 'maintenance';
      item.statusLabel = 'Under Maintenance';
      item.maintenanceReason = finalReason;
      item.estResumeDate = date || 'Until Further Notice';

      updateFacilityDOMState(currentPendingMaintenanceId);
      updateKPICounters();
      closeMaintenanceModal();

      showAdminToast(`${item.name} placed on Maintenance Hold.`, 'decline');
    } else {
      showAdminToast(data.message || 'Failed to activate maintenance hold.', 'decline');
    }
  })
  .catch(err => {
    console.error(err);
    showAdminToast('Network error while updating maintenance hold.', 'decline');
  });
}

function closeMaintenanceModal() {
  const modal = document.getElementById('maintenanceModal');
  if (modal) modal.style.display = 'none';

  // If user cancelled, restore the checkbox to false
  if (currentPendingMaintenanceId && !facilitiesData[currentPendingMaintenanceId]?.maintenance) {
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
   MULTI-PHOTO GALLERY MANAGER STATE & HANDLERS
   ========================================================================== */
let editGalleryList = [];
let addGalleryList = [];

function renderGalleryEditor(mode) {
  const list = mode === 'edit' ? editGalleryList : addGalleryList;
  const grid = document.getElementById(`${mode}GalleryItemsGrid`);
  const badge = document.getElementById(`${mode}GalleryCountBadge`);
  if (badge) badge.textContent = `${list.length} Photo${list.length === 1 ? '' : 's'}`;
  if (!grid) return;

  if (list.length === 0) {
    grid.innerHTML = `
      <div class="gallery-empty-state">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="margin-bottom:0.35rem; display:inline-block;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        <div>No gallery photos added yet.</div>
        <small style="color:#8ca696;">Click <strong>"Upload Multiple Photos"</strong> or pick an official preset above.</small>
      </div>
    `;
    return;
  }

  grid.innerHTML = list.map((item) => `
    <div class="gallery-item-card" data-gallery-id="${item.id}">
      <img src="${item.src}" alt="${escapeHtml(item.caption || 'Photo')}" class="gallery-item-thumb" onerror="this.src='assets/images/function_hall.jpg'">
      <div class="gallery-item-info">
        <input type="text" class="gallery-item-caption-input" placeholder="Angle Caption (e.g. Stage View)" value="${escapeHtml(item.caption || '')}" oninput="updateGalleryItemCaption('${item.id}', this.value, '${mode}')">
      </div>
      <button type="button" class="gallery-item-remove-btn" onclick="removeGalleryItem('${item.id}', '${mode}')" title="Remove photo">✕</button>
    </div>
  `).join('');
}

function handleGalleryFilesSelected(input, mode) {
  if (!input || !input.files || input.files.length === 0) return;
  const list = mode === 'edit' ? editGalleryList : addGalleryList;

  Array.from(input.files).forEach((file, idx) => {
    let cleanName = file.name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ');
    cleanName = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);

    list.push({
      id: 'gal_upload_' + Date.now() + '_' + idx + '_' + Math.random().toString(36).substr(2, 5),
      src: URL.createObjectURL(file),
      caption: cleanName || 'Facility Photograph',
      file: file
    });
  });

  input.value = '';
  renderGalleryEditor(mode);
}

function handleGalleryPresetSelected(selectEl, mode) {
  if (!selectEl || !selectEl.value) return;
  const parts = selectEl.value.split('|');
  const src = parts[0];
  const caption = parts[1] || 'Official Facility Preset';
  const list = mode === 'edit' ? editGalleryList : addGalleryList;

  list.push({
    id: 'gal_preset_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
    src: src,
    caption: caption,
    file: null
  });

  selectEl.value = '';
  renderGalleryEditor(mode);
}

function removeGalleryItem(itemId, mode) {
  if (mode === 'edit') {
    editGalleryList = editGalleryList.filter(item => item.id !== itemId);
  } else {
    addGalleryList = addGalleryList.filter(item => item.id !== itemId);
  }
  renderGalleryEditor(mode);
}

function updateGalleryItemCaption(itemId, newCaption, mode) {
  const list = mode === 'edit' ? editGalleryList : addGalleryList;
  const item = list.find(i => i.id === itemId);
  if (item) {
    item.caption = newCaption;
  }
}

/* ==========================================================================
   EDIT SPECIFICATIONS MODAL (AJAX PERSISTENCE)
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
  document.getElementById('editFacilityBeds').value = item.beds || 0;
  if (document.getElementById('editFacilityRate')) {
    let cleanRate = (item.rate || '').trim();
    cleanRate = cleanRate.replace(/^(₱|PHP|P)\s*/i, '');
    document.getElementById('editFacilityRate').value = cleanRate;
  }
  document.getElementById('editFacilityLocation').value = item.location;
  document.getElementById('editFacilityFeatures').value = (item.features || []).join(', ');
  const defaultDesc = (item.category === 'dorms') 
    ? 'Fully air-conditioned institutional dormitory accommodation tailored for government trainees, agricultural extension workers, and official delegates.'
    : 'Spacious institutional conference venue equipped with complete sound systems, stage lighting, and climate control for official seminars and workshops.';
  document.getElementById('editFacilityNotes').value = item.notes || item.description || defaultDesc;

  // Populate Gallery Manager
  editGalleryList = [];
  if (Array.isArray(item.gallery) && item.gallery.length > 0) {
    item.gallery.forEach((g, idx) => {
      editGalleryList.push({
        id: 'gal_init_' + idx + '_' + Math.random().toString(36).substr(2, 5),
        src: g.src,
        caption: g.caption || 'Facility Photograph',
        file: null
      });
    });
  }
  renderGalleryEditor('edit');

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

  const facilityId = activeEditFacilityId || document.getElementById('editFacilityId')?.value;
  if (!facilityId) {
    showAdminToast('No active facility selected to update.', 'decline');
    return;
  }
  activeEditFacilityId = facilityId;

  const item = facilitiesData[facilityId];
  if (!item) {
    showAdminToast('Facility record not found in system.', 'decline');
    return;
  }

  const name = document.getElementById('editFacilityName').value.trim();
  const category = document.getElementById('editFacilityCategory').value;
  const pax = parseInt(document.getElementById('editFacilityPax').value, 10) || 0;
  const beds = parseInt(document.getElementById('editFacilityBeds').value, 10) || 0;
  let rawRate = document.getElementById('editFacilityRate') ? document.getElementById('editFacilityRate').value.trim() : '';
  rawRate = rawRate.replace(/^(₱|PHP|P)\s*/i, '');
  let rate = '';
  if (rawRate) {
    rate = (rawRate.toLowerCase() === 'institutional' || rawRate.toLowerCase() === 'free') ? rawRate : `₱${rawRate}`;
  }
  const location = document.getElementById('editFacilityLocation').value.trim();
  const featStr = document.getElementById('editFacilityFeatures').value.trim();
  const notes = document.getElementById('editFacilityNotes').value.trim();
  const newImgSrc = document.getElementById('editFacilityImageSrc')?.value || item.image;

  if (!name) {
    showAdminToast('Facility name is required.', 'decline');
    document.getElementById('editFacilityName')?.focus();
    return;
  }
  if (!pax || pax < 1) {
    showAdminToast('Participant capacity must be at least 1 PAX.', 'decline');
    document.getElementById('editFacilityPax')?.focus();
    return;
  }
  if (!location) {
    showAdminToast('Building & floor location is required.', 'decline');
    document.getElementById('editFacilityLocation')?.focus();
    return;
  }

  const submitBtn = document.getElementById('btnSaveFacilitySpecs') || document.querySelector('#editFacilityForm button[type="submit"]');
  let origBtnHtml = '';
  if (submitBtn) {
    origBtnHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation: fgSpin 0.9s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
      <span>Saving Specifications...</span>
    `;
  }

  const formData = new FormData();
  formData.append('action', 'update');
  formData.append('id', item.db_id || item.id);
  formData.append('name', name);
  formData.append('category', category);
  formData.append('capacity', pax);
  formData.append('beds', beds);
  formData.append('rate', rate);
  formData.append('location', location);
  formData.append('features', featStr);
  formData.append('notes', notes);
  formData.append('image', newImgSrc);

  // Append Multi-Photo Gallery items
  const existingGallery = editGalleryList
    .filter(item => item.file === null)
    .map(item => ({ src: item.src, caption: item.caption }));
  formData.append('existing_gallery', JSON.stringify(existingGallery));

  const newFiles = editGalleryList.filter(item => item.file !== null);
  newFiles.forEach(item => {
    formData.append('gallery_uploads[]', item.file);
    formData.append('gallery_upload_captions[]', item.caption || 'Facility Photograph');
  });

  const fileInput = document.getElementById('editFacilityFileInput');
  if (fileInput && fileInput.files && fileInput.files[0]) {
    formData.append('facility_image', fileInput.files[0]);
  }

  fetch('ajax/facility_actions.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      item.name = name;
      item.category = category;
      item.categoryLabel = category === 'halls' ? 'Function & Assembly Hall' : (category === 'dorms' ? 'Institutional Dormitory' : 'Dining Facility');
      item.pax = pax;
      item.beds = beds;
      item.rate = rate;
      item.location = location;
      if (data.facility && data.facility.cooling_type) {
        item.cooling_type = data.facility.cooling_type;
      }
      item.notes = notes;
      item.description = notes;
      if (data.facility && data.facility.image) {
        item.image = data.facility.image;
      } else if (newImgSrc) {
        item.image = newImgSrc;
      }
      if (data.facility && Array.isArray(data.facility.gallery)) {
        item.gallery = data.facility.gallery;
      }
      item.features = featStr.split(',').map(f => f.trim()).filter(Boolean);

      // Update DOM Title, Image, and Specs in Card
      const card = document.querySelector(`.facility-card-item[data-id="${activeEditFacilityId}"]`);
      if (card) {
        card.setAttribute('data-category', item.category);

        const imgEl = card.querySelector('.facility-card-img');
        if (imgEl && item.image) imgEl.src = item.image;

        const typeBadge = card.querySelector('.facility-type-badge');
        if (typeBadge) typeBadge.textContent = item.categoryLabel;

        const titleEl = card.querySelector('.facility-card-title');
        if (titleEl) titleEl.textContent = item.name;

        const locEl = card.querySelector('.facility-location-tag span');
        if (locEl) locEl.textContent = item.location;

        const capValEl = card.querySelector('.spec-capacity-val');
        if (capValEl) capValEl.textContent = item.category === 'dorms' ? `${item.beds} Beds (${item.pax} PAX)` : `${item.pax} Participants`;

        const rateEl = card.querySelector('.spec-rate-val');
        if (rateEl) rateEl.textContent = item.rate || 'Institutional';

        // Update Photos Preview Button
        const photoBtn = card.querySelector('.btn-card-action[onclick*="openFacilityGalleryModal"]');
        if (photoBtn && item.gallery) {
          const span = photoBtn.querySelector('span');
          if (span) span.textContent = `Photos (${item.gallery.length})`;
          const galPayload = {
            id: activeEditFacilityId,
            dbId: item.db_id || item.id,
            name: item.name,
            type: item.category,
            isDorm: (item.category === 'dorms'),
            rate: item.rate,
            capacity: item.category === 'dorms' ? `${item.beds} Bunk Beds` : `${item.pax} Participants`,
            beds: item.beds,
            cooling_type: item.cooling_type || '',
            location: item.location,
            description: item.notes,
            features: item.features,
            image: item.image,
            photos: item.gallery
          };
          photoBtn.onclick = () => openFacilityGalleryModal(galPayload);
        }

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

        const chip = row.querySelector('.event-type-chip');
        if (chip) chip.textContent = item.category.toUpperCase();

        const nameEl = row.querySelector('.facility-table-info-text strong');
        if (nameEl) nameEl.textContent = item.name;
        const locEl = row.querySelector('.facility-table-info-text span');
        if (locEl) locEl.textContent = item.location;
        const capCell = row.children[2];
        if (capCell) capCell.innerHTML = `<strong>${item.pax} PAX</strong>${item.beds > 0 ? `<br><small style="color: #637f6f;">${item.beds} Beds</small>` : ''}`;

        const tblPhotoBtn = row.querySelector('.btn-tbl-action[onclick*="openFacilityGalleryModal"]');
        if (tblPhotoBtn && item.gallery) {
          const span = tblPhotoBtn.querySelector('span');
          if (span) span.textContent = `Photos (${item.gallery.length})`;
          const galPayload = {
            id: activeEditFacilityId,
            dbId: item.db_id || item.id,
            name: item.name,
            type: item.category,
            isDorm: (item.category === 'dorms'),
            rate: item.rate,
            capacity: item.category === 'dorms' ? `${item.beds} Bunk Beds` : `${item.pax} Participants`,
            beds: item.beds,
            cooling_type: item.cooling_type || '',
            location: item.location,
            description: item.notes,
            features: item.features,
            image: item.image,
            photos: item.gallery
          };
          tblPhotoBtn.onclick = () => openFacilityGalleryModal(galPayload);
        }
      }

      updateKPICounters();
      closeEditFacilityModal();
      showAdminToast(`Specifications and photo updated for ${item.name}.`, 'success');
    } else {
      showAdminToast(data.message || 'Failed to save facility changes.', 'decline');
    }
  })
  .catch(err => {
    console.error(err);
    showAdminToast('Network error while saving specifications.', 'decline');
  })
  .finally(() => {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = origBtnHtml || `
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
        <span>Save Specifications</span>
      `;
    }
  });
}

/* ==========================================================================
   ADD NEW FACILITY MODAL (AJAX PERSISTENCE)
   ========================================================================== */
function openAddFacilityModal() {
  const form = document.getElementById('addFacilityForm');
  if (form) form.reset();

  const preview = document.getElementById('addFacilityImgPreview');
  const hiddenSrc = document.getElementById('addFacilityImageSrc');
  if (preview) preview.src = 'assets/images/function_hall.jpg';
  if (hiddenSrc) hiddenSrc.value = 'assets/images/function_hall.jpg';

  addGalleryList = [];
  renderGalleryEditor('add');

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
  const rate = document.getElementById('addFacilityRate') ? document.getElementById('addFacilityRate').value.trim() : '';
  const location = document.getElementById('addFacilityLocation').value.trim();
  const featStr = document.getElementById('addFacilityFeatures').value.trim();
  const uploadedOrPresetImg = document.getElementById('addFacilityImageSrc')?.value;
  const defaultFallbackImg = category === 'dorms' ? 'assets/images/dormitory.jpg' : 'assets/images/function_hall.jpg';
  const finalImg = uploadedOrPresetImg || defaultFallbackImg;

  const formData = new FormData();
  formData.append('action', 'create');
  formData.append('name', name);
  formData.append('category', category);
  formData.append('capacity', pax);
  formData.append('beds', beds);
  formData.append('rate', rate);
  formData.append('location', location);
  formData.append('features', featStr);
  const notes = document.getElementById('addFacilityNotes')?.value.trim() || '';
  formData.append('notes', notes);
  formData.append('image', finalImg);

  // Append Multi-Photo Gallery items
  const existingGallery = addGalleryList
    .filter(item => item.file === null)
    .map(item => ({ src: item.src, caption: item.caption }));
  formData.append('existing_gallery', JSON.stringify(existingGallery));

  const newFiles = addGalleryList.filter(item => item.file !== null);
  newFiles.forEach(item => {
    formData.append('gallery_uploads[]', item.file);
    formData.append('gallery_upload_captions[]', item.caption || 'Facility Photograph');
  });

  const fileInput = document.getElementById('addFacilityFileInput');
  if (fileInput && fileInput.files && fileInput.files[0]) {
    formData.append('facility_image', fileInput.files[0]);
  }

  fetch('ajax/facility_actions.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const newId = String(data.id);
      const returnedImg = data.facility?.image || finalImg;
      const features = featStr.split(',').map(f => f.trim()).filter(Boolean);

      facilitiesData[newId] = {
        id: newId,
        db_id: data.id,
        name: name,
        category: category,
        categoryLabel: category === 'halls' ? 'Function & Assembly Hall' : (category === 'dorms' ? 'Institutional Dormitory' : 'Dining Facility'),
        pax: pax,
        beds: beds,
        rate: rate,
        location: location,
        cooling_type: data.facility?.cooling_type || '',
        image: returnedImg,
        features: features,
        gallery: (data.facility && Array.isArray(data.facility.gallery)) ? data.facility.gallery : [
          { src: returnedImg, caption: `${name} View` }
        ],
        status: 'available',
        statusLabel: 'Available',
        maintenance: false,
        notes: notes,
        description: notes
      };

      // Prepend to Grid
      const grid = document.getElementById('facilityCardsGrid');
      if (grid) {
        // Remove empty state placeholder if present
        const emptyPlaceholder = grid.querySelector('div[style*="grid-column: 1 / -1"]');
        if (emptyPlaceholder) emptyPlaceholder.remove();

        const cardEl = document.createElement('div');
        cardEl.className = 'facility-card-item';
        cardEl.setAttribute('data-id', newId);
        cardEl.setAttribute('data-category', category);
        cardEl.setAttribute('data-maintenance', 'false');
        cardEl.innerHTML = `
          <div class="facility-card-media">
            <img src="${returnedImg}" alt="${name}" class="facility-card-img" onerror="this.src='assets/images/function_hall.jpg'">
            <span class="facility-type-badge">${facilitiesData[newId].categoryLabel}</span>
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
                <span class="spec-label">Standard Rate</span>
                <strong class="spec-val spec-rate-val">${rate || 'Institutional'}</strong>
              </div>
            </div>
            <div class="facility-features-wrap">
              ${features.slice(0, 5).map(f => `<span class="feature-pill-tag">${f}</span>`).join('')}
            </div>
            <div class="facility-card-footer">
              <div class="facility-card-footer-top">
                <div class="facility-maintenance-quick-toggle">
                  <label class="switch-toggle" title="Toggle Maintenance Mode">
                    <input type="checkbox" onchange="onMaintenanceSwitchChange('${newId}', this.checked)">
                    <span class="slider-round"></span>
                  </label>
                  <span class="maintenance-label">Maintenance Mode</span>
                </div>
              </div>
              <div class="card-action-btns">
                <button type="button" class="btn-card-action" onclick="openFacilityGalleryModal(facilitiesData['${newId}'])" title="Preview Client Showcase &amp; Multi-Photo Gallery">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                  <span>Photos (${facilitiesData[newId].gallery.length})</span>
                </button>
                <button type="button" class="btn-card-action" onclick="openEditFacilityModal('${newId}')">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                  <span>Edit</span>
                </button>
                <button type="button" class="btn-card-action btn-delete-facility" onclick="confirmDeleteFacility('${newId}', '${name.replace(/'/g, "\\'")}')" title="Move to Trash / Archive">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
              </div>
            </div>
          </div>
        `;
        grid.insertBefore(cardEl, grid.firstChild);
      }

      // Prepend to Table
      const tbody = document.getElementById('facilityTableBody');
      if (tbody) {
        const emptyRow = tbody.querySelector('td[colspan="7"]')?.closest('tr');
        if (emptyRow) emptyRow.remove();

        const tr = document.createElement('tr');
        tr.setAttribute('data-id', newId);
        tr.setAttribute('data-category', category);
        tr.setAttribute('data-maintenance', 'false');
        tr.innerHTML = `
          <td>
            <div class="facility-table-info-cell">
              <img src="${returnedImg}" alt="${name}" class="facility-table-thumb" onerror="this.src='assets/images/function_hall.jpg'">
              <div class="facility-table-info-text">
                <strong>${name}</strong>
                <span>${location}</span>
              </div>
            </div>
          </td>
          <td><span class="event-type-chip ${category === 'dorms' ? 'schedule' : (category === 'dining' ? 'maintenance' : 'approval')}">${category.toUpperCase()}</span></td>
          <td><strong>${pax} PAX</strong>${beds > 0 ? `<br><small style="color: #637f6f;">${beds} Beds</small>` : ''}</td>
          <td><div class="facility-features-wrap" style="margin: 0;">${features.slice(0, 3).map(f => `<span class="feature-pill-tag">${f}</span>`).join('')}</div></td>
          <td>
            <label class="switch-toggle">
              <input type="checkbox" onchange="onMaintenanceSwitchChange('${newId}', this.checked)">
              <span class="slider-round"></span>
            </label>
          </td>
          <td><span class="status-badge-chip chip-confirmed">Available</span></td>
          <td style="text-align: right; white-space: nowrap; padding-right: 1.25rem;">
            <div class="facility-table-action-btns">
              <button type="button" class="btn-tbl-action" onclick="openFacilityGalleryModal(facilitiesData['${newId}'])" title="Preview Client Showcase &amp; Multi-Photo Gallery">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <span>Photos (${facilitiesData[newId].gallery.length})</span>
              </button>
              ${category === 'dorms' ? `
                <button type="button" class="btn-tbl-action btn-manage-rooms" onclick="openManageRoomsModal('${newId}', '${name.replace(/'/g, "\\'")}')" title="Manage Individual Dormitory Rooms">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                  <span>Rooms</span>
                </button>
              ` : `
                <button type="button" class="btn-tbl-action btn-manage-setups" onclick="openManageSetupsModal('${newId}', '${name.replace(/'/g, "\\'")}')" title="Manage Venue Setups &amp; Floor Plans">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M3 9h18M9 21V9"></path></svg>
                  <span>Setups</span>
                </button>
              `}
              <button type="button" class="btn-tbl-action" onclick="openEditFacilityModal('${newId}')" title="Edit Facility Details">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                <span>Edit</span>
              </button>
              <button type="button" class="btn-tbl-action btn-tbl-delete" onclick="confirmDeleteFacility('${newId}', '${name.replace(/'/g, "\\'")}')" title="Move to Trash / Archive">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              </button>
            </div>
          </td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);
      }

      updateKPICounters();
      closeAddFacilityModal();
      showAdminToast(`Successfully registered "${name}".`, 'success');
    } else {
      showAdminToast(data.message || 'Failed to register new facility.', 'decline');
    }
  })
  .catch(err => {
    console.error(err);
    showAdminToast('Network error while registering facility.', 'decline');
  });
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
   CSV EXPORT GENERATOR (DYNAMIC REAL DATABASE INVENTORY)
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
      `"${(f.features || []).join('; ')}"`
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

/* ==========================================================================
   DORMITORY ROOMS MANAGEMENT CONTROLLER (AJAX TO ajax/room_actions.php)
   ========================================================================== */
function openManageRoomsModal(facilityId, facilityName) {
  const hiddenId = document.getElementById('currentManageFacilityId');
  const titleEl = document.getElementById('manageRoomsFacilityName');
  const modal = document.getElementById('manageRoomsModal');

  if (hiddenId) hiddenId.value = facilityId;
  if (titleEl) titleEl.textContent = `Manage Rooms: ${facilityName || 'Dormitory'}`;
  if (modal) modal.style.display = 'flex';

  loadFacilityRooms(facilityId);
}

function closeManageRoomsModal() {
  const modal = document.getElementById('manageRoomsModal');
  if (modal) modal.style.display = 'none';
  const quickInput = document.getElementById('newRoomNumber');
  if (quickInput) quickInput.value = '';
}

function loadFacilityRooms(facilityId) {
  const grid = document.getElementById('manageRoomsGrid');
  if (!grid) return;

  grid.innerHTML = `
    <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem 1rem; color: #637f6f;">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-bottom: 0.5rem;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
      <div>Loading rooms inventory...</div>
    </div>
  `;

  fetch(`ajax/room_actions.php?action=get_rooms&facility_id=${encodeURIComponent(facilityId)}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
            ${data.message || 'Failed to load rooms.'}
          </div>
        `;
        return;
      }

      const rooms = data.rooms || [];

      // Update KPI Badges
      const kpiTotal = document.getElementById('roomKpiTotal');
      const kpiAvail = document.getElementById('roomKpiAvailable');
      const kpiOcc = document.getElementById('roomKpiOccupied');
      const kpiMaint = document.getElementById('roomKpiMaintenance');

      let availCount = 0;
      let occCount = 0;
      let maintCount = 0;

      rooms.forEach(r => {
        if (r.status === 'maintenance') maintCount++;
        else if (r.status === 'occupied') occCount++;
        else availCount++;
      });

      if (kpiTotal) kpiTotal.textContent = rooms.length;
      if (kpiAvail) kpiAvail.textContent = availCount;
      if (kpiOcc) kpiOcc.textContent = occCount;
      if (kpiMaint) kpiMaint.textContent = maintCount;

      if (rooms.length === 0) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; background: #fbfdfc; border: 1.5px dashed #cde2d5; border-radius: 12px; color: #637f6f;">
            <p style="margin: 0 0 0.5rem 0; font-weight: 700; color: #174d2f;">Walang nakatalang kuwarto para sa dormitoryong ito.</p>
            <p style="margin: 0; font-size: 0.82rem;">Gamitin ang form sa itaas upang magdagdag ng unang kuwarto.</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = '';
      rooms.forEach(r => {
        const tile = document.createElement('div');
        const isMaint = r.status === 'maintenance';
        const isOcc = r.status === 'occupied';

        tile.className = `room-admin-tile ${isMaint ? 'is-maintenance' : (isOcc ? 'is-occupied' : '')}`;

        let statusBadgeHtml = '<span class="room-tile-status-pill avail">Available</span>';
        let occupantBoxHtml = '';

        if (isMaint) {
          statusBadgeHtml = '<span class="room-tile-status-pill maint">Maintenance</span>';
        } else if (isOcc) {
          const occ = r.occupant || {};
          const refNo = occ.booking_ref || r.booking_ref || '';
          const refText = refNo ? ` • #${refNo}` : '';
          
          const dormModalTitle = (document.getElementById('manageRoomsModalTitle')?.textContent || 'Dormitory').replace('Manage Rooms: ', '').trim();
          const occPayloadStr = JSON.stringify({
            facilityName: `${dormModalTitle} • Room ${r.room_number}`,
            facilityType: `Dormitory Suite (Room ${r.room_number})`,
            occupants: [occ],
            primary: occ
          }).replace(/"/g, '&quot;');

          statusBadgeHtml = `<span class="room-tile-status-pill occ" onclick="openOccupantModal(${occPayloadStr})" style="cursor: pointer;" title="View Occupant Details">Occupied</span>`;

          occupantBoxHtml = `
            <div class="room-occupant-box" onclick="openOccupantModal(${occPayloadStr})" title="Click to view full reservation details">
              <div class="r-occ-header">
                <div class="r-occ-tag-wrap">
                  <span class="f-occ-pulse-dot"></span>
                  <span class="r-occ-status-tag">Occupant</span>
                </div>
                <span class="r-occ-ref" title="${escapeHtml(refNo)}">#${escapeHtml(refNo)}</span>
              </div>
              <div class="r-occ-title" title="${escapeHtml(occ.event_title || 'Institutional Activity')}">
                ${escapeHtml(occ.event_title || 'Institutional Activity')}
              </div>
              <div class="r-occ-client" title="${escapeHtml(occ.client_name || 'Official Guest')}">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="flex-shrink:0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span class="r-occ-client-name">${escapeHtml(occ.client_name || 'Official Guest')}</span>
              </div>
              <div class="r-occ-meta">
                <span class="r-occ-info-text" title="${escapeHtml(occ.client_contact || occ.client_agency || 'ATI Central Office')}">
                  ${escapeHtml(occ.client_contact || occ.client_agency || 'ATI Central Office')}
                </span>
                <span class="r-occ-link">Details &rarr;</span>
              </div>
            </div>
          `;
        }

        tile.innerHTML = `
          <div class="room-tile-top">
            <div>
              <div class="room-tile-number">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v11M21 11v7M3 15h18M3 11h14a4 4 0 0 1 4 4v0M7 11V8a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3"/></svg>
                <span>Room ${r.room_number}</span>
              </div>
              <div class="room-tile-floor">${r.floor}</div>
            </div>
            ${statusBadgeHtml}
          </div>

          ${occupantBoxHtml}

          <div class="room-tile-actions">
            <label class="room-maint-toggle-wrap" title="Toggle Maintenance Hold">
              <label class="switch-toggle" style="margin: 0;">
                <input type="checkbox" ${isMaint ? 'checked' : ''} onchange="toggleRoomMaintenance(${r.id}, this.checked)">
                <span class="slider-round"></span>
              </label>
              <span>${isMaint ? 'Under Maintenance' : 'Operational'}</span>
            </label>

            <button type="button" class="btn-room-delete" onclick="handleDeleteRoom(${r.id}, '${r.room_number}')" title="Delete Room ${r.room_number}">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
          </div>
        `;

        grid.appendChild(tile);
      });
    })
    .catch(err => {
      console.error('Error fetching rooms:', err);
      grid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
          Nagkaroon ng network error habang kinukuha ang mga kuwarto.
        </div>
      `;
    });
}

function handleQuickAddRoom(event) {
  event.preventDefault();
  const facilityId = document.getElementById('currentManageFacilityId').value;
  const numInput = document.getElementById('newRoomNumber');
  const floorSelect = document.getElementById('newRoomFloor');
  const submitBtn = document.getElementById('btnSubmitAddRoom');

  const roomNum = numInput ? numInput.value.trim() : '';
  const floor = floorSelect ? floorSelect.value : '1st Floor';

  if (!roomNum) {
    alert('Pakilagay ang Room Number.');
    return;
  }

  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<span>Adding...</span>`;
  }

  const formData = new FormData();
  formData.append('action', 'add_room');
  formData.append('facility_id', facilityId);
  formData.append('room_number', roomNum);
  formData.append('floor', floor);

  fetch('ajax/room_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          <span>Add Room</span>
        `;
      }

      if (data.success) {
        showAdminToast(data.message || `Room ${roomNum} added successfully.`, 'success');
        if (numInput) numInput.value = '';
        loadFacilityRooms(facilityId);
      } else {
        showAdminToast(data.message || 'Hindi maidadagdag ang kuwarto.', 'decline');
      }
    })
    .catch(err => {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<span>Add Room</span>`;
      }
      showAdminToast('Network error while adding room.', 'decline');
    });
}

function toggleRoomMaintenance(roomId, isMaintenance) {
  const facilityId = document.getElementById('currentManageFacilityId').value;
  const formData = new FormData();
  formData.append('action', 'toggle_maintenance');
  formData.append('room_id', roomId);
  formData.append('maintenance', isMaintenance ? '1' : '0');

  fetch('ajax/room_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        showAdminToast(data.message, isMaintenance ? 'decline' : 'success');
        loadFacilityRooms(facilityId);
      } else {
        showAdminToast(data.message || 'Failed to update maintenance state.', 'decline');
        loadFacilityRooms(facilityId);
      }
    })
    .catch(err => {
      showAdminToast('Network error updating room.', 'decline');
      loadFacilityRooms(facilityId);
    });
}

function handleDeleteRoom(roomId, roomNumber) {
  openDeleteConfirmModal({
    title: 'Delete Room / Unit?',
    subtitle: 'Dormitory Inventory Management',
    name: `Room ${roomNumber}`,
    meta: `Room ID: #${roomId}`,
    message: `Sigurado ka bang nais mong tanggalin ang <strong>Room ${escapeHtml(roomNumber)}</strong> mula sa pasilidad na ito?`,
    btnText: 'Yes, Delete Room',
    isPurge: true,
    onConfirm: () => {
      const facilityId = document.getElementById('currentManageFacilityId').value;
      const formData = new FormData();
      formData.append('action', 'delete_room');
      formData.append('room_id', roomId);

      fetch('ajax/room_actions.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showAdminToast(data.message || `Room ${roomNumber} deleted.`, 'success');
            loadFacilityRooms(facilityId);
          } else {
            showAdminToast(data.message || 'Hindi maaaring burahin ang kuwarto.', 'decline');
          }
        })
        .catch(err => {
          showAdminToast('Network error deleting room.', 'decline');
        });
    }
  });
}

/* ==========================================================================
   VENUE SETUPS & FLOOR PLANS MANAGEMENT CONTROLLER (ajax/facility_actions.php)
   ========================================================================== */
let currentLoadedFacilitySetups = [];
let currentFacilityMeta = null;

const setupPresetLibrary = {
  theater: { name: 'Theater Setup', desc: 'Row seating facing main presentation screen' },
  classroom: { name: 'Classroom Setup', desc: 'Desks and chairs configuration for workshops' },
  workshop: { name: 'Workshop Pods', desc: 'Collaborative group table arrangements' },
  banquet: { name: 'Banquet Dining', desc: 'Round dining tables for official gatherings' },
  ushape: { name: 'U-Shape Workshop', desc: 'Open U-configuration facing facilitator screen' },
  conference: { name: 'Conference Setup', desc: 'Central aisle with wide projection clearance' },
  roundtable: { name: 'Round Table Discussion', desc: 'Circular discussions and panel breakout tables' },
  videoconf: { name: 'Hybrid Video-Conference', desc: 'Camera-optimized setup with mic clearance' },
  exhibition: { name: 'Open Exhibition Booths', desc: 'Open space for display booths and agricultural showcases' }
};

function onSetupPresetChange(presetKey) {
  const nameInput = document.getElementById('newSetupName');
  const descInput = document.getElementById('newSetupDesc');
  const capInput = document.getElementById('newSetupCap');

  if (presetKey && setupPresetLibrary[presetKey]) {
    const item = setupPresetLibrary[presetKey];
    if (nameInput) nameInput.value = item.name;
    if (descInput) descInput.value = item.desc;
    if (capInput && (!capInput.value || capInput.value.trim() === '')) {
      const defCap = currentFacilityMeta?.capacity ? (currentFacilityMeta.capacity.includes('PAX') ? currentFacilityMeta.capacity : currentFacilityMeta.capacity + ' PAX') : '50 PAX';
      capInput.value = defCap;
    }
  }
}

function openManageSetupsModal(facilityId, facilityName) {
  const hiddenId = document.getElementById('currentManageSetupsFacilityId');
  const titleEl = document.getElementById('manageSetupsFacilityName');
  const modal = document.getElementById('manageSetupsModal');

  if (hiddenId) hiddenId.value = facilityId;
  if (titleEl) titleEl.textContent = `Manage Setups: ${facilityName || 'Venue'}`;
  if (modal) modal.style.display = 'flex';

  loadFacilitySetups(facilityId);
}

function closeManageSetupsModal() {
  const modal = document.getElementById('manageSetupsModal');
  if (modal) modal.style.display = 'none';
  const quickForm = document.getElementById('quickAddSetupForm');
  if (quickForm) quickForm.reset();
}

function loadFacilitySetups(facilityId) {
  const grid = document.getElementById('manageSetupsGrid');
  if (!grid) return;

  grid.innerHTML = `
    <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem 1rem; color: #637f6f;">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-bottom: 0.5rem;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
      <div>Loading venue setups & layouts...</div>
    </div>
  `;

  fetch(`ajax/facility_actions.php?action=get_layouts&facility_id=${encodeURIComponent(facilityId)}`)
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        grid.innerHTML = `
          <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
            ${data.message || 'Failed to load setups.'}
          </div>
        `;
        return;
      }

      currentFacilityMeta = data.facility;
      currentLoadedFacilitySetups = data.layouts || [];

      // Update max cap badge in header
      const capPill = document.getElementById('setupKpiMaxCap');
      if (capPill && currentFacilityMeta) {
        const c = currentFacilityMeta.capacity || '50';
        capPill.textContent = c.includes('PAX') ? c : c + ' PAX';
      }

      // Prefill default cap in add form
      const capInput = document.getElementById('newSetupCap');
      if (capInput && currentFacilityMeta) {
        const c = currentFacilityMeta.capacity || '50';
        capInput.value = c.includes('PAX') ? c : c + ' PAX';
      }

      renderSetupsGrid();
    })
    .catch(err => {
      grid.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
          Network error loading setups.
        </div>
      `;
    });
}

function renderSetupsGrid() {
  const grid = document.getElementById('manageSetupsGrid');
  if (!grid) return;

  let totalCount = currentLoadedFacilitySetups.length;
  let availCount = 0;
  let resCount = 0;

  currentLoadedFacilitySetups.forEach(s => {
    if (s.available) availCount++;
    else resCount++;
  });

  const kpiTotal = document.getElementById('setupKpiTotal');
  const kpiAvail = document.getElementById('setupKpiAvailable');
  const kpiRes = document.getElementById('setupKpiReserved');

  if (kpiTotal) kpiTotal.textContent = totalCount;
  if (kpiAvail) kpiAvail.textContent = availCount;
  if (kpiRes) kpiRes.textContent = resCount;

  if (totalCount === 0) {
    grid.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; background: #fbfdfc; border: 1.5px dashed #cde2d5; border-radius: 12px; color: #637f6f;">
        <p style="margin: 0 0 0.5rem 0; font-weight: 700; color: #174d2f;">Walang naka-set na layout para sa venue na ito.</p>
        <span style="font-size: 0.8rem;">Magdagdag sa itaas o pindutin ang "Reset to Standard 4 Presets" sa ibaba.</span>
      </div>
    `;
    return;
  }

  const iconSvgs = {
    'theater': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M7 18h10M3 21h18"/></svg>',
    'classroom': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg>',
    'workshop': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
    'banquet': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>',
    'ushape': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4v11a5 5 0 0 0 10 0V4"/><rect x="3" y="3" width="4" height="3"/><rect x="13" y="3" width="4" height="3"/></svg>',
    'conference': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="2"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/></svg>',
    'roundtable': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>',
    'videoconf': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
    'exhibition': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
    'default': '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>'
  };

  grid.innerHTML = '';
  currentLoadedFacilitySetups.forEach((setup, index) => {
    const isAvail = Boolean(setup.available);
    const key = setup.id ? setup.id.toLowerCase() : '';
    let iconKey = 'default';
    for (const k in iconSvgs) {
      if (key.includes(k)) {
        iconKey = k;
        break;
      }
    }
    const iconSvg = iconSvgs[iconKey] || iconSvgs['default'];

    const tile = document.createElement('div');
    tile.className = `setup-admin-tile ${isAvail ? 'is-available' : 'is-reserved'}`;
    tile.innerHTML = `
      <div class="setup-tile-top">
        <div class="setup-tile-icon-wrap">${iconSvg}</div>
        <button type="button" class="btn-room-delete" onclick="deleteSetupItem(${index})" title="Remove this setup layout">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </button>
      </div>
      <div class="setup-tile-name">${setup.name}</div>
      <div class="setup-tile-cap-badge">${setup.cap || 'Standard PAX'}</div>
      <div class="setup-tile-desc">${setup.desc || 'Standard venue arrangement'}</div>
      <div class="setup-tile-actions">
        <div class="setup-status-toggle-wrap ${isAvail ? 'avail' : 'res'}">
          <label class="switch-toggle" style="margin: 0;" title="Toggle setup availability">
            <input type="checkbox" ${isAvail ? 'checked' : ''} onchange="toggleSetupStatus(${index}, this.checked)">
            <span class="slider-round"></span>
          </label>
          <span>${isAvail ? 'Available' : 'Reserved / Closed'}</span>
        </div>
        <span style="font-size: 0.68rem; font-weight: 700; color: #94a3b8;">#${index + 1}</span>
      </div>
    `;
    grid.appendChild(tile);
  });
}

function toggleSetupStatus(index, newAvailable) {
  if (currentLoadedFacilitySetups[index]) {
    currentLoadedFacilitySetups[index].available = Boolean(newAvailable);
    renderSetupsGrid();
  }
}

function handleQuickAddSetup(e) {
  e.preventDefault();
  const nameInput = document.getElementById('newSetupName');
  const capInput = document.getElementById('newSetupCap');
  const descInput = document.getElementById('newSetupDesc');
  const presetSelect = document.getElementById('newSetupPresetSelect');

  const name = nameInput ? nameInput.value.trim() : '';
  let cap = capInput ? capInput.value.trim() : '';
  const desc = descInput ? descInput.value.trim() : '';
  const presetKey = presetSelect ? presetSelect.value : 'custom';

  if (!name) {
    showAdminToast('Ilagay ang pangalan ng layout setup.', 'warning');
    return;
  }

  if (cap && !cap.toLowerCase().includes('pax')) {
    cap += ' PAX';
  } else if (!cap) {
    cap = currentFacilityMeta?.capacity ? (currentFacilityMeta.capacity.includes('PAX') ? currentFacilityMeta.capacity : currentFacilityMeta.capacity + ' PAX') : '50 PAX';
  }

  const idSlug = (presetKey !== 'custom' ? presetKey : name.toLowerCase().replace(/[^a-z0-9]/g, '-')) + '-' + Date.now();

  currentLoadedFacilitySetups.push({
    id: idSlug,
    name: name,
    cap: cap,
    available: true,
    desc: desc || `${name} arrangement for delegates`
  });

  if (nameInput) nameInput.value = '';
  if (descInput) descInput.value = '';
  if (presetSelect) presetSelect.value = 'custom';

  showAdminToast(`Layout "${name}" added to list. I-click ang "Save Setups Changes" para ma-save.`, 'success');
  renderSetupsGrid();
}

function deleteSetupItem(index) {
  if (!currentLoadedFacilitySetups[index]) return;
  const item = currentLoadedFacilitySetups[index];

  openDeleteConfirmModal({
    title: 'Remove Layout Setup?',
    subtitle: 'Floor Plan Preset Configuration',
    name: item.name,
    meta: `Layout Preset #${index + 1}`,
    message: `Sigurado ka bang nais mong tanggalin ang layout na <strong>${escapeHtml(item.name)}</strong> mula sa pasilidad na ito?`,
    btnText: 'Yes, Remove Layout',
    isPurge: false,
    onConfirm: () => {
      currentLoadedFacilitySetups.splice(index, 1);
      renderSetupsGrid();
      showAdminToast(`Removed "${item.name}" layout preset.`, 'success');
    }
  });
}

function resetSetupsToDefaults() {
  const ok = confirm('Nais mo bang ibalik ang setups sa 4 standard defaults (Theater, Classroom, Workshop, Banquet)?');
  if (!ok) return;

  const capVal = currentFacilityMeta?.capacity ? (currentFacilityMeta.capacity.includes('PAX') ? currentFacilityMeta.capacity : currentFacilityMeta.capacity + ' PAX') : '50 PAX';

  currentLoadedFacilitySetups = [
    { id: 'theater', name: 'Theater Setup', cap: capVal, available: true, desc: 'Row seating facing main presentation screen' },
    { id: 'classroom', name: 'Classroom Setup', cap: capVal, available: true, desc: 'Desks and chairs configuration for workshops' },
    { id: 'workshop', name: 'Workshop Pods', cap: capVal, available: true, desc: 'Collaborative group table arrangements' },
    { id: 'banquet', name: 'Banquet Dining', cap: capVal, available: true, desc: 'Round dining tables for official gatherings' }
  ];

  renderSetupsGrid();
  showAdminToast('Naibalik sa 4 standard layout presets. I-click ang "Save Setups Changes" para ma-save.', 'info');
}

function saveFacilitySetups() {
  const facilityId = document.getElementById('currentManageSetupsFacilityId').value;
  const saveBtn = document.getElementById('btnSaveSetupsSubmit');

  if (!facilityId) {
    showAdminToast('Walang napiling pasilidad.', 'decline');
    return;
  }

  if (saveBtn) {
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span>Saving setups...</span>`;
  }

  const formData = new FormData();
  formData.append('action', 'save_layouts');
  formData.append('facility_id', facilityId);
  formData.append('layouts', JSON.stringify(currentLoadedFacilitySetups));

  fetch('ajax/facility_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Save Setups Changes</span>`;
      }
      if (data.success) {
        showAdminToast('Matagumpay na na-save ang Venue Setups! Live na ito sa booking portal.', 'success');
        if (window.initialFacilitiesData && window.initialFacilitiesData[facilityId]) {
          window.initialFacilitiesData[facilityId].layouts = JSON.stringify(currentLoadedFacilitySetups);
        }
        setTimeout(() => {
          closeManageSetupsModal();
        }, 600);
      } else {
        showAdminToast(data.message || 'Hindi na-save ang setups.', 'decline');
      }
    })
    .catch(err => {
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg><span>Save Setups Changes</span>`;
      }
      showAdminToast('Network error saving setups.', 'decline');
    });
}

/* ==========================================================================
   POP-UP DELETE CONFIRMATION MODAL CONTROLLER
   ========================================================================== */
let currentDeleteConfirmCallback = null;

function openDeleteConfirmModal(options) {
  const modal = document.getElementById('deleteConfirmModal');
  if (!modal) return;

  const titleEl = document.getElementById('deleteModalTitle');
  const subEl = document.getElementById('deleteModalSubtitle');
  const nameEl = document.getElementById('deleteModalItemName');
  const metaEl = document.getElementById('deleteModalItemMeta');
  const msgEl = document.getElementById('deleteModalMessage');
  const btnEl = document.getElementById('btnConfirmDeleteAction');
  const btnTxtEl = document.getElementById('btnConfirmDeleteText');
  const headerEl = document.getElementById('deleteModalHeader');

  if (titleEl) titleEl.textContent = options.title || 'Confirm Deletion';
  if (subEl) subEl.textContent = options.subtitle || 'Asset & Inventory Control';
  if (nameEl) nameEl.textContent = options.name || 'Selected Item';
  if (metaEl) metaEl.textContent = options.meta || '';
  if (msgEl) msgEl.innerHTML = options.message || 'Are you sure you want to proceed?';
  if (btnTxtEl) btnTxtEl.textContent = options.btnText || 'Yes, Delete';

  if (options.isPurge) {
    if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #7f1d1d, #991b1b)';
    if (btnEl) {
      btnEl.style.background = '#991b1b';
      btnEl.style.borderColor = '#991b1b';
    }
  } else {
    if (headerEl) headerEl.style.background = 'linear-gradient(135deg, #b91c1c, #dc2626)';
    if (btnEl) {
      btnEl.style.background = '#dc2626';
      btnEl.style.borderColor = '#dc2626';
    }
  }

  currentDeleteConfirmCallback = options.onConfirm || null;

  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeDeleteConfirmModal() {
  const modal = document.getElementById('deleteConfirmModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
  currentDeleteConfirmCallback = null;
}

// Global modal backdrop click listener
document.addEventListener('click', function (e) {
  const deleteModal = document.getElementById('deleteConfirmModal');
  if (deleteModal && e.target === deleteModal) {
    closeDeleteConfirmModal();
  }
});

// Confirm button click handler
document.addEventListener('DOMContentLoaded', function () {
  const confirmBtn = document.getElementById('btnConfirmDeleteAction');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      if (typeof currentDeleteConfirmCallback === 'function') {
        const cb = currentDeleteConfirmCallback;
        closeDeleteConfirmModal();
        cb();
      }
    });
  }
});

/* ==========================================================================
   FACILITY SOFT DELETE, RESTORE & PERMANENT PURGE
   ========================================================================== */
function confirmDeleteFacility(id, name) {
  openDeleteConfirmModal({
    title: 'Move Facility to Archive (Trash)?',
    subtitle: 'Institutional Asset Control',
    name: name,
    meta: `Asset ID: #${id} • Move to Trash`,
    message: `Are you sure you want to move <strong>${escapeHtml(name)}</strong> to the Archive (Trash)?<br><br>It will be hidden from applicants and the booking catalog, but can be restored anytime from the Archive tab.`,
    btnText: 'Yes, Move to Archive',
    isPurge: false,
    onConfirm: () => {
      const formData = new FormData();
      formData.append('action', 'delete');
      formData.append('id', id);

      fetch('ajax/facility_actions.php', {
        method: 'POST',
        body: formData
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAdminToast(data.message || `Facility "${name}" moved to archive.`, 'success');
            setTimeout(() => window.location.reload(), 900);
          } else {
            showAdminToast(data.message || 'Failed to archive facility.', 'decline');
          }
        })
        .catch(err => {
          console.error(err);
          showAdminToast('A network error occurred while archiving the facility.', 'decline');
        });
    }
  });
}

function confirmRestoreFacility(id, name) {
  openDeleteConfirmModal({
    title: 'Restore Facility to Active Catalog?',
    subtitle: 'Re-activate Monitored Asset',
    name: name,
    meta: `Asset ID: #${id} • Active Status Restoration`,
    message: `Restore <strong>${escapeHtml(name)}</strong> back to the active catalog? It will immediately be available for reservations again.`,
    btnText: 'Yes, Restore Facility',
    isPurge: false,
    onConfirm: () => {
      const formData = new FormData();
      formData.append('action', 'restore');
      formData.append('id', id);

      fetch('ajax/facility_actions.php', {
        method: 'POST',
        body: formData
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAdminToast(data.message || `Facility "${name}" restored.`, 'success');
            setTimeout(() => window.location.reload(), 900);
          } else {
            showAdminToast(data.message || 'Failed to restore facility.', 'decline');
          }
        })
        .catch(err => {
          console.error(err);
          showAdminToast('A network error occurred while restoring the facility.', 'decline');
        });
    }
  });
}

function confirmPermanentDeleteFacility(id, name) {
  openDeleteConfirmModal({
    title: 'Permanently Purge Facility?',
    subtitle: 'Permanent Database Deletion (Irreversible)',
    name: name,
    meta: `Asset ID: #${id} • Permanent Deletion`,
    message: `<strong style="color: #b91c1c;">WARNING:</strong> This will permanently delete <strong>${escapeHtml(name)}</strong> and all of its room configurations from the database.<br><br>This action <strong style="color: #b91c1c;">CANNOT</strong> be undone.`,
    btnText: 'Yes, Permanently Purge',
    isPurge: true,
    onConfirm: () => {
      const formData = new FormData();
      formData.append('action', 'purge');
      formData.append('id', id);

      fetch('ajax/facility_actions.php', {
        method: 'POST',
        body: formData
      })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            showAdminToast(data.message || `Facility "${name}" permanently deleted.`, 'success');
            setTimeout(() => window.location.reload(), 900);
          } else {
            showAdminToast(data.message || 'Failed to permanently delete facility.', 'decline');
          }
        })
        .catch(err => {
          console.error(err);
          showAdminToast('A network error occurred while purging the facility.', 'decline');
        });
    }
  });
}

/* ==========================================================================
   OCCUPANT & ACTIVE EVENT MODAL CONTROLLER
   ========================================================================== */
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function openOccupantModal(payload) {
  if (!payload) return;
  const modal = document.getElementById('facilityOccupantModal');
  const titleEl = document.getElementById('occupantModalFacilityName');
  const bodyEl = document.getElementById('occupantModalBody');
  const btnBooking = document.getElementById('btnOccupantGoToBooking');
  if (!modal || !bodyEl) return;

  const facName = payload.facilityName || 'Institutional Asset';
  const facType = payload.facilityType || 'Facility';
  const primary = payload.primary || {};
  const occupants = Array.isArray(payload.occupants) && payload.occupants.length > 0 ? payload.occupants : [primary];
  const refNo = primary.booking_ref || primary.reference_no || '';

  if (titleEl) {
    titleEl.textContent = facName;
  }

  if (btnBooking) {
    if (refNo) {
      btnBooking.href = `admin_reservations.php?search=${encodeURIComponent(refNo)}`;
      btnBooking.style.display = 'inline-flex';
    } else {
      btnBooking.style.display = 'none';
    }
  }

  // Format dates
  const formatIsoDate = (dStr) => {
    if (!dStr) return '';
    try {
      const parts = dStr.split('-');
      if (parts.length === 3) {
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
      }
    } catch (e) {}
    return dStr;
  };

  const startDateFmt = formatIsoDate(primary.start_date);
  const endDateFmt = formatIsoDate(primary.end_date);
  const dateRangeStr = (startDateFmt === endDateFmt || !endDateFmt) ? startDateFmt : `${startDateFmt} — ${endDateFmt}`;

  // Build body HTML
  let html = `
    <!-- Event Banner -->
    <div class="occupant-modal-event-banner">
      <div class="o-event-tag-row">
        <span class="o-badge-chip active-session">
          <span class="f-occ-pulse-dot" style="background:#16a34a;"></span>
          Active Occupant Today
        </span>
        <span class="o-badge-chip ref">Ref #${escapeHtml(refNo)}</span>
      </div>
      <h3 class="o-event-title">${escapeHtml(primary.event_title || 'Institutional Event / Activity')}</h3>
      <div class="o-event-facility-sub">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
        <span>${escapeHtml(facType)} • Official Allocation</span>
      </div>
    </div>

    <!-- Client / Contact Person Card -->
    <div class="occupant-modal-section">
      <h5 class="o-section-heading">Client &amp; Contact Person</h5>
      <div class="o-client-detail-card">
        <div class="o-client-avatar">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <div class="o-client-info">
          <div class="o-client-name">${escapeHtml(primary.client_name || 'Authorized Staff / Guest')}</div>
          <div class="o-client-agency">${escapeHtml(primary.client_agency || 'Agricultural Training Institute')}</div>
        </div>
      </div>

      <div class="o-contact-chips-grid">
        ${primary.client_contact ? `
          <a href="tel:${escapeHtml(primary.client_contact)}" class="o-contact-chip" title="Call Contact Person">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span><strong>${escapeHtml(primary.client_contact)}</strong> (Official Mobile)</span>
          </a>
        ` : `
          <div class="o-contact-chip" style="color: #64748b;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span>No contact number recorded</span>
          </div>
        `}

        ${primary.client_email ? `
          <a href="mailto:${escapeHtml(primary.client_email)}" class="o-contact-chip" title="Send Email">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            <span>${escapeHtml(primary.client_email)}</span>
          </a>
        ` : ''}
      </div>
    </div>

    <!-- Booking Schedule & Specs -->
    <div class="occupant-modal-section">
      <h5 class="o-section-heading">Schedule &amp; Attendance</h5>
      <div class="o-specs-table-grid">
        <div class="o-spec-card">
          <span class="o-spec-label">Duration Dates</span>
          <strong class="o-spec-value">${dateRangeStr}</strong>
        </div>
        <div class="o-spec-card">
          <span class="o-spec-label">Time Slot / Schedule</span>
          <strong class="o-spec-value">${escapeHtml(primary.time_slot || 'Full Day Activity')}</strong>
        </div>
        <div class="o-spec-card">
          <span class="o-spec-label">Participants / Pax</span>
          <strong class="o-spec-value">${primary.pax_count || '—'} Participants</strong>
        </div>
        <div class="o-spec-card">
          <span class="o-spec-label">Approval Status</span>
          <strong class="o-spec-value" style="color: #166534;">Officially Cleared &amp; Approved</strong>
        </div>
      </div>
    </div>
  `;

  // If there are multiple room occupants (e.g. multiple dormitory rooms booked under this dorm today)
  if (occupants.length > 1) {
    html += `
      <div class="occupant-modal-section" style="margin-top: 1.25rem;">
        <h5 class="o-section-heading">Other Active Rooms in this Dormitory (${occupants.length})</h5>
        <div class="o-multi-rooms-list">
          ${occupants.map(o => `
            <div class="o-multi-room-item">
              <div class="o-mr-badge">Room ${escapeHtml(o.room_number || 'N/A')}</div>
              <div class="o-mr-info">
                <strong>${escapeHtml(o.client_name)}</strong>
                <span>${escapeHtml(o.event_title)} • #${escapeHtml(o.booking_ref || o.reference_no)}</span>
              </div>
              ${o.client_contact ? `<a href="tel:${escapeHtml(o.client_contact)}" class="o-mr-phone" title="Call">${escapeHtml(o.client_contact)}</a>` : ''}
            </div>
          `).join('')}
        </div>
      </div>
    `;
  }

  bodyEl.innerHTML = html;

  modal.style.display = 'flex';
  void modal.offsetHeight;
  modal.classList.add('show');
}

function closeOccupantModal() {
  const modal = document.getElementById('facilityOccupantModal');
  if (!modal) return;
  modal.classList.remove('show');
  setTimeout(() => {
    modal.style.display = 'none';
  }, 220);
}


