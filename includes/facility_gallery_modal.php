<?php
/**
 * Agriculture Training Institute - Facility Showcase & Multi-Photo Gallery Modal
 */
?>
<!-- ==========================================================================
     FACILITY MULTI-PHOTO GALLERY & FULL SPECIFICATIONS MODAL
     ========================================================================== -->
<div class="facility-gallery-overlay" id="facilityGalleryModal" style="display: none;" onclick="if(event.target===this) closeFacilityGalleryModal();" role="dialog" aria-modal="true" aria-labelledby="fgModalTitle">
  <div class="facility-gallery-dialog">
    
    <!-- Modal Header -->
    <div class="facility-gallery-header">
      <div class="fg-header-title-group">
        <div class="fg-badge-row">
          <span class="fg-category-pill" id="fgModalCategory">VENUE &amp; HALLS</span>
          <span class="fg-rate-pill" id="fgModalRate">₱5,000 / day</span>
          <span class="fg-cap-pill" id="fgModalCapacity">100 PAX</span>
        </div>
        <h3 class="fg-modal-title" id="fgModalTitle">Facility Name</h3>
        <p class="fg-modal-location" id="fgModalLocation">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
            <circle cx="12" cy="10" r="3"></circle>
          </svg>
          <span id="fgModalLocationText">ATI Institutional Complex, Diliman, Quezon City</span>
        </p>
      </div>

      <button type="button" class="fg-modal-close-btn" onclick="closeFacilityGalleryModal()" aria-label="Close dialog">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Modal Body: Split Layout (Interactive Photo Gallery Left + Specifications Right) -->
    <div class="facility-gallery-body">
      
      <!-- LEFT COLUMN: Multi-Photo Carousel & Thumbnails -->
      <div class="fg-visual-col">
        <div class="fg-main-image-viewport">
          <img id="fgMainImage" src="assets/images/function_hall.jpg" alt="Facility Main View" loading="eager">
          
          <!-- Prev / Next Navigation Arrows -->
          <button type="button" class="fg-nav-btn prev" onclick="navigateFacilityGallery(-1)" aria-label="Previous photograph">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button type="button" class="fg-nav-btn next" onclick="navigateFacilityGallery(1)" aria-label="Next photograph">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>

          <!-- Top-Right Photo Counter -->
          <div class="fg-counter-badge" id="fgCounterBadge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            <span id="fgCounterText">1 of 4</span>
          </div>

          <!-- Bottom Floating Caption -->
          <div class="fg-caption-overlay">
            <span class="fg-caption-icon">📷</span>
            <span id="fgMainCaption">Executive Conference &amp; Podium View</span>
          </div>
        </div>

        <!-- Thumbnails Strip -->
        <div class="fg-thumbnails-wrapper">
          <div class="fg-thumbnails-track" id="fgThumbnailsTrack">
            <!-- Dynamically populated thumbnail elements -->
          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: Detailed Specifications & In-House Amenities -->
      <div class="fg-details-col">
        
        <!-- Description / Overview -->
        <div class="fg-detail-block">
          <h4 class="fg-block-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Overview &amp; Purpose</span>
          </h4>
          <p class="fg-desc-text" id="fgModalDescription">
            Spacious institutional facility equipped with high-performance audio-visual systems, dedicated presentation stages, and full climate control for government conventions and workshops.
          </p>
        </div>

        <!-- Key Metrics Grid -->
        <div class="fg-metrics-grid">
          <div class="fg-metric-card">
            <span class="fg-metric-label">Max Capacity</span>
            <span class="fg-metric-val" id="fgMetricPax">100 PAX</span>
          </div>
          <div class="fg-metric-card" id="fgMetricBedCard" style="display: none;">
            <span class="fg-metric-label">Bed Inventory</span>
            <span class="fg-metric-val" id="fgMetricBeds">12 BEDS</span>
          </div>
          <div class="fg-metric-card">
            <span class="fg-metric-label">Standard Rate</span>
            <span class="fg-metric-val" id="fgMetricRate">₱5,000</span>
          </div>
        </div>

        <!-- Seating & Layout Configurations (for Halls) -->
        <div class="fg-detail-block" id="fgLayoutsBlock">
          <h4 class="fg-block-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="3" y1="9" x2="21" y2="9"></line>
              <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            <span>Available Setup Configurations</span>
          </h4>
          <div class="fg-layouts-list" id="fgLayoutsList">
            <span class="fg-layout-chip">Theater Setup (Full Capacity)</span>
            <span class="fg-layout-chip">Classroom / Training Seminar</span>
            <span class="fg-layout-chip">Executive U-Shape Boardroom</span>
            <span class="fg-layout-chip">Banquet &amp; Dining Layout</span>
          </div>
        </div>

        <!-- In-House Amenities & Technical Equipment -->
        <div class="fg-detail-block">
          <h4 class="fg-block-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>Included Amenities &amp; Technical Equipment</span>
          </h4>
          <div class="fg-amenities-tags" id="fgAmenitiesList">
            <!-- Dynamically populated pills -->
          </div>
        </div>


      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div class="facility-gallery-footer">
      <button type="button" class="btn-fg-cancel" onclick="closeFacilityGalleryModal()">Close Details</button>
      <button type="button" class="btn-fg-edit" id="btnFgEditFacility" style="display: none;" onclick="openEditFromGalleryModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
        <span>Edit Facility &amp; Photos</span>
      </button>
      <button type="button" class="btn-fg-select" id="btnFgSelectFacility" onclick="selectAndBookFacilityFromGallery()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span id="btnFgSelectText">Select &amp; Book This Facility</span>
      </button>
    </div>

  </div>
</div>
