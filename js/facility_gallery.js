/**
 * Agriculture Training Institute - Facility Showcase & Multi-Photo Gallery Controller
 */

let fgCurrentIndex = 0;
let fgPhotos = [];
let fgActiveData = null;

function openFacilityGalleryModal(data) {
  if (!data) return;

  fgActiveData = data;
  fgPhotos = (Array.isArray(data.photos) && data.photos.length > 0) ? data.photos : [
    { src: data.image || 'assets/images/function_hall.jpg', caption: data.name }
  ];
  fgCurrentIndex = 0;

  // Header Elements
  const titleEl = document.getElementById('fgModalTitle');
  const catEl = document.getElementById('fgModalCategory');
  const rateEl = document.getElementById('fgModalRate');
  const capEl = document.getElementById('fgModalCapacity');
  const locText = document.getElementById('fgModalLocationText');

  if (titleEl) titleEl.textContent = data.name || 'Facility Details';
  if (catEl) catEl.textContent = (data.type === 'dorms' || data.isDorm) ? 'DORMITORY SUITE' : 'VENUE & HALLS';
  if (rateEl) rateEl.textContent = data.rate || 'Standard Institutional Rate';
  if (capEl) capEl.textContent = data.capacity || 'Standard Capacity';
  if (locText) locText.textContent = data.location || 'ATI Institutional Complex, Diliman, Quezon City';

  // Details & Metrics
  const descEl = document.getElementById('fgModalDescription');
  if (descEl) {
    const defDesc = (data.type === 'dorms' || data.isDorm)
      ? 'Comfortable institutional dormitory accommodation tailored for government trainees, agricultural extension workers, and official delegates.'
      : 'Spacious institutional conference venue equipped with complete sound systems, stage lighting, and climate control for official seminars and workshops.';
    descEl.textContent = data.description || data.notes || defDesc;
  }

  const metricPax = document.getElementById('fgMetricPax');
  if (metricPax) {
    let paxText = String(data.capacity || 'Standard PAX');
    paxText = paxText.replace(/\s*\/\s*\d+\s*beds?/i, '').trim();
    if (/^\d+$/.test(paxText)) {
      paxText = `${paxText} PAX`;
    }
    metricPax.textContent = paxText;
  }

  const metricRate = document.getElementById('fgMetricRate');
  if (metricRate) metricRate.textContent = data.rate || 'Standard Rate';

  const bedCard = document.getElementById('fgMetricBedCard');
  const metricBeds = document.getElementById('fgMetricBeds');
  if (bedCard && metricBeds) {
    const bedsCount = parseInt(data.beds, 10) || 0;
    if (data.type === 'dorms' || data.isDorm || bedsCount > 0) {
      bedCard.style.display = 'flex';
      metricBeds.textContent = bedsCount === 1 ? '1 BED' : `${bedsCount} BEDS`;
    } else {
      bedCard.style.display = 'none';
    }
  }

  // Layouts Section
  const layoutsBlock = document.getElementById('fgLayoutsBlock');
  const layoutsList = document.getElementById('fgLayoutsList');
  if (layoutsBlock && layoutsList) {
    if (data.type === 'dorms' || data.isDorm) {
      layoutsBlock.style.display = 'none';
    } else {
      layoutsBlock.style.display = 'block';
      let layoutChips = '';
      if (Array.isArray(data.layouts) && data.layouts.length > 0) {
        layoutChips = data.layouts.map(l => `<span class="fg-layout-chip">${escapeHtml(l.name || l)} (${escapeHtml(l.cap || 'Full Cap')})</span>`).join('');
      } else {
        layoutChips = `
          <span class="fg-layout-chip">Theater Setup (Full Capacity)</span>
          <span class="fg-layout-chip">Classroom / Workshop Seminar</span>
          <span class="fg-layout-chip">Executive Boardroom / U-Shape</span>
          <span class="fg-layout-chip">Banquet &amp; Fellowship Layout</span>
        `;
      }
      layoutsList.innerHTML = layoutChips;
    }
  }

  // Amenities List
  const amenitiesList = document.getElementById('fgAmenitiesList');
  if (amenitiesList) {
    let features = [];
    if (Array.isArray(data.features) && data.features.length > 0) {
      features = data.features;
    } else if (typeof data.features === 'string' && data.features.trim() !== '') {
      features = data.features.split(',').map(s => s.trim()).filter(Boolean);
    } else {
      features = (data.type === 'dorms' || data.isDorm)
        ? ['Full Air Conditioning', 'Single Bunk Beds', 'Individual Lockers', 'Study Desks & Chairs', 'Ensuite Restrooms', 'Wi-Fi Access']
        : ['Central Climate Control', 'High-Output HD Projector', 'PA Sound System & Microphones', 'Executive Rostrum & Stage', 'Wi-Fi Internet', 'Standby Power Generator'];
    }

    const checkSvg = `
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
    `;
    amenitiesList.innerHTML = features.map(feat => `
      <span class="fg-amenity-item">${checkSvg}<span>${escapeHtml(feat)}</span></span>
    `).join('');
  }


  // Action Button Text & Role Switch
  const editBtn = document.getElementById('btnFgEditFacility');
  const selectBtn = document.getElementById('btnFgSelectFacility');
  const selectBtnText = document.getElementById('btnFgSelectText');

  if (typeof openEditFacilityModal === 'function') {
    if (editBtn) editBtn.style.display = 'inline-flex';
    if (selectBtn) selectBtn.style.display = 'none';
  } else {
    if (editBtn) editBtn.style.display = 'none';
    if (selectBtn) {
      selectBtn.style.display = 'inline-flex';
      if (selectBtnText) {
        selectBtnText.textContent = `Select & Book ${data.name}`;
      }
    }
  }

  // Render Thumbnails and display first photo
  renderThumbnails();
  showFacilityPhoto(0);

  // Show Modal
  const modal = document.getElementById('facilityGalleryModal');
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function renderThumbnails() {
  const track = document.getElementById('fgThumbnailsTrack');
  if (!track) return;

  track.innerHTML = '';
  fgPhotos.forEach((photo, idx) => {
    const item = document.createElement('div');
    item.className = `fg-thumb-item ${idx === fgCurrentIndex ? 'active' : ''}`;
    item.setAttribute('role', 'button');
    item.setAttribute('aria-label', `View photo ${idx + 1}: ${photo.caption || ''}`);
    item.innerHTML = `<img src="${photo.src}" alt="${escapeHtml(photo.caption || 'Thumbnail')}" loading="lazy">`;
    item.onclick = () => showFacilityPhoto(idx);
    track.appendChild(item);
  });
}

function showFacilityPhoto(index) {
  if (!fgPhotos || fgPhotos.length === 0) return;
  
  fgCurrentIndex = (index + fgPhotos.length) % fgPhotos.length;
  const currentPhoto = fgPhotos[fgCurrentIndex];

  const mainImg = document.getElementById('fgMainImage');
  const captionEl = document.getElementById('fgMainCaption');
  const counterEl = document.getElementById('fgCounterText');

  if (mainImg) {
    mainImg.style.opacity = '0.4';
    setTimeout(() => {
      mainImg.src = currentPhoto.src;
      mainImg.alt = currentPhoto.caption || 'Facility View';
      mainImg.style.opacity = '1';
    }, 120);
  }

  if (captionEl) {
    captionEl.textContent = currentPhoto.caption || 'Facility Photograph';
  }

  if (counterEl) {
    counterEl.textContent = `${fgCurrentIndex + 1} of ${fgPhotos.length}`;
  }

  // Update active thumbnail
  const thumbs = document.querySelectorAll('.fg-thumb-item');
  thumbs.forEach((thumb, idx) => {
    if (idx === fgCurrentIndex) {
      thumb.classList.add('active');
      thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    } else {
      thumb.classList.remove('active');
    }
  });
}

function navigateFacilityGallery(step) {
  showFacilityPhoto(fgCurrentIndex + step);
}

function closeFacilityGalleryModal() {
  const modal = document.getElementById('facilityGalleryModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

function selectAndBookFacilityFromGallery() {
  if (!fgActiveData) {
    closeFacilityGalleryModal();
    return;
  }

  const targetDbId = fgActiveData.dbId || fgActiveData.id;
  
  // Find card in current page
  let card = document.querySelector(`.facility-choice-card[data-facility-db-id="${targetDbId}"]`);
  if (!card) {
    card = document.querySelector(`.facility-choice-card[data-id="${targetDbId}"]`);
  }

  closeFacilityGalleryModal();

  if (card) {
    if (typeof selectFacilityCard === 'function') {
      selectFacilityCard(card, false);
    } else {
      card.click();
    }

    // Scroll card into view smoothly
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });

    if (typeof showToast === 'function') {
      showToast(`Selected ${fgActiveData.name}! Please continue with your reservation details below.`, 'success');
    }
  }
}

function openEditFromGalleryModal() {
  if (!fgActiveData) return;
  const targetId = fgActiveData.id || fgActiveData.dbId;
  closeFacilityGalleryModal();
  if (typeof openEditFacilityModal === 'function') {
    openEditFacilityModal(targetId);
  }
}

// Helper to escape HTML characters
function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, function (m) {
    return {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[m];
  });
}

// Global Keyboard Navigation for Gallery Modal
document.addEventListener('keydown', function (e) {
  const modal = document.getElementById('facilityGalleryModal');
  if (!modal || modal.style.display === 'none') return;

  if (e.key === 'Escape') {
    closeFacilityGalleryModal();
  } else if (e.key === 'ArrowLeft') {
    navigateFacilityGallery(-1);
  } else if (e.key === 'ArrowRight') {
    navigateFacilityGallery(1);
  }
});
