/**
 * NyumbaFind — API Client
 * Central HTTP client for all API calls to the Laravel backend.
 */
const API_BASE = 'http://localhost:8000/api';

const api = {
  // ─── Auth helpers ──────────────────────────────
  getToken: () => localStorage.getItem('nyumba_token'),
  getUser:  () => JSON.parse(localStorage.getItem('nyumba_user') || 'null'),
  isLoggedIn: () => !!localStorage.getItem('nyumba_token'),

  saveSession(token, user) {
    localStorage.setItem('nyumba_token', token);
    localStorage.setItem('nyumba_user', JSON.stringify(user));
  },
  clearSession() {
    localStorage.removeItem('nyumba_token');
    localStorage.removeItem('nyumba_user');
  },

  // ─── Core fetch wrapper ─────────────────────────
  async request(method, path, data = null, isFormData = false) {
    const headers = {};
    const token = this.getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;
    if (!isFormData) headers['Content-Type'] = 'application/json';
    headers['Accept'] = 'application/json';

    const opts = { method, headers };
    if (data) opts.body = isFormData ? data : JSON.stringify(data);

    const res = await fetch(`${API_BASE}${path}`, opts);
    const json = await res.json().catch(() => ({}));

    if (!res.ok) {
      const msg = json.message || json.error || `Request failed (${res.status})`;
      throw Object.assign(new Error(msg), { status: res.status, errors: json.errors });
    }
    return json;
  },

  get:    (path)        => api.request('GET',    path),
  post:   (path, body)  => api.request('POST',   path, body),
  put:    (path, body)  => api.request('PUT',    path, body),
  patch:  (path, body)  => api.request('PATCH',  path, body),
  delete: (path)        => api.request('DELETE', path),
  upload: (path, form)  => api.request('POST',   path, form, true),

  // ─── Auth endpoints ─────────────────────────────
  auth: {
    sendOtp:       (phone)       => api.post('/auth/send-otp', { phone }),
    verifyOtp:     (phone, code, name) => api.post('/auth/verify-otp', { phone, code, name }),
    me:            ()            => api.get('/auth/me'),
    logout:        ()            => api.post('/auth/logout', {}),
    updateProfile: (data)        => api.put('/auth/profile', data),
  },

  // ─── Estates ────────────────────────────────────
  estates: {
    list: () => api.get('/estates'),
    show: (slug) => api.get(`/estates/${slug}`),
  },

  // ─── Listings ───────────────────────────────────
  listings: {
    list:  (params = {}) => api.get('/listings?' + new URLSearchParams(params)),
    show:  (id)          => api.get(`/listings/${id}`),
    store: (form)        => api.upload('/listings', form),
    update:(id, data)    => api.put(`/listings/${id}`, data),
    updateStatus: (id, status) => api.patch(`/listings/${id}/status`, { status }),
    destroy: (id)        => api.delete(`/listings/${id}`),
    myListings: (page=1) => api.get(`/my-listings?page=${page}`),
    inquire: (id, msg)   => api.post(`/listings/${id}/inquire`, { message: msg }),
    report:  (id, reason, details) => api.post(`/listings/${id}/report`, { reason, details }),
    review:  (id, rating, comment) => api.post(`/listings/${id}/reviews`, { rating, comment }),
  },

  // ─── Search ─────────────────────────────────────
  search: (params = {}) => api.get('/search?' + new URLSearchParams(params)),

  // ─── Admin ──────────────────────────────────────
  admin: {
    stats:         ()       => api.get('/admin/stats'),
    allListings:   (params) => api.get('/admin/listings?' + new URLSearchParams(params || {})),
    pendingListings:()      => api.get('/admin/listings/pending'),
    approve:  (id, notes)  => api.post(`/admin/listings/${id}/approve`, { notes }),
    reject:   (id, reason) => api.post(`/admin/listings/${id}/reject`, { reason }),
    suspend:  (id, reason) => api.post(`/admin/listings/${id}/suspend`, { reason }),
    feature:  (id, days)   => api.post(`/admin/listings/${id}/feature`, { days }),
    reports:  ()           => api.get('/admin/reports'),
    resolveReport: (id, action, notes) => api.post(`/admin/reports/${id}/resolve`, { action, notes }),
    users:    (params)     => api.get('/admin/users?' + new URLSearchParams(params || {})),
  },
};

// ─── UI Utilities ────────────────────────────────────
const UI = {
  toast(message, type = 'success', duration = 3500) {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast toast--${type}`;
    toast.innerHTML = `<span>${icons[type] || ''}</span><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
  },

  formatPrice(price) {
    return 'KSh ' + Number(price).toLocaleString('en-KE');
  },

  houseTypeLabel(type) {
    const map = { bedsitter: 'Bedsitter', '1br': '1 Bedroom', '2br': '2 Bedroom', '3br': '3 Bedroom', single_room: 'Single Room', studio: 'Studio' };
    return map[type] || type;
  },

  amenityIcon(amenity) {
    const icons = { water: '💧', electricity: '⚡', wifi: '📶', parking: '🅿️', security: '🛡️', borehole: '🌊', cctv: '📷', gym: '🏋️', pool: '🏊' };
    return icons[amenity] || '✓';
  },

  stars(rating) {
    return Array.from({ length: 5 }, (_, i) =>
      `<span class="star${i < Math.round(rating) ? ' star--filled' : ''}">★</span>`
    ).join('');
  },

  renderListingCard(listing) {
    const amenities = listing.amenities ? Object.entries(listing.amenities).filter(([,v]) => v).slice(0, 3) : [];
    const img = listing.primary_photo
      ? `<img src="${listing.primary_photo}" alt="${listing.title}" loading="lazy">`
      : `<div class="listing-card__image-placeholder">🏠</div>`;

    const verified = listing.is_verified
      ? `<span class="listing-card__badge badge--verified">✓ Verified</span>` : '';
    const featured = listing.is_featured
      ? `<span class="listing-card__badge badge--featured" style="left:auto;right:12px">⭐ Featured</span>` : '';

    return `
      <a href="listing-detail.html?id=${listing.id}" class="listing-card" id="listing-${listing.id}">
        <div class="listing-card__image">
          ${img}${verified}${featured}
        </div>
        <div class="listing-card__content">
          <div class="listing-card__type">${UI.houseTypeLabel(listing.type)}</div>
          <div class="listing-card__title">${listing.title}</div>
          <div class="listing-card__estate">📍 ${listing.estate?.name || ''}, ${listing.estate?.county || ''}</div>
          <div class="listing-card__amenities">
            ${amenities.map(([k]) => `<span class="amenity-pill">${UI.amenityIcon(k)} ${k}</span>`).join('')}
          </div>
          <div class="listing-card__footer">
            <div class="listing-card__price">${UI.formatPrice(listing.price)} <span>/mo</span></div>
            <span class="text-sm text-muted">👁 ${listing.views_count || 0}</span>
          </div>
        </div>
      </a>`;
  },

  skeletonCards(count = 6) {
    return Array.from({ length: count }, () => `
      <div class="listing-card">
        <div class="skeleton" style="height:200px;border-radius:0"></div>
        <div class="listing-card__content">
          <div class="skeleton" style="height:12px;width:60%;margin-bottom:8px"></div>
          <div class="skeleton" style="height:20px;margin-bottom:8px"></div>
          <div class="skeleton" style="height:14px;width:80%;margin-bottom:16px"></div>
          <div class="skeleton" style="height:24px;width:40%"></div>
        </div>
      </div>`).join('');
  },

  showModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  },
  hideModal(id) {
    document.getElementById(id)?.classList.add('hidden');
    document.body.style.overflow = '';
  },

  setLoading(btn, loading) {
    if (loading) btn.classList.add('btn--loading'), btn.disabled = true;
    else btn.classList.remove('btn--loading'), btn.disabled = false;
  },

  formatDate(str) {
    if (!str) return '';
    return new Date(str).toLocaleDateString('en-KE', { year: 'numeric', month: 'short', day: 'numeric' });
  },

  requireAuth(redirectTo = 'auth.html') {
    if (!api.isLoggedIn()) {
      sessionStorage.setItem('redirect_after_login', window.location.href);
      window.location.href = redirectTo;
      return false;
    }
    return true;
  },
};

// Make globally available
window.api = api;
window.UI  = UI;

// ─── Kenya Locations Autocomplete ──────────────────
const locations = {
  data: null,

  async load() {
    if (this.data) return this.data;
    try {
      // 1. Fetch static counties/sub-counties/wards
      const resCounties = await fetch('js/kenya-locations.json');
      const counties = await resCounties.json();

      // 2. Fetch static villages/areas (1,800+ real places in Kenya)
      let areas = [];
      try {
        const resAreas = await fetch('js/kenya-areas.json');
        areas = await resAreas.json();
      } catch (e) {
        console.error('Failed to load kenya-areas.json', e);
      }
      
      // 3. Fetch custom active estates/villages from DB
      let dbEstates = [];
      try {
        dbEstates = await api.estates.list();
      } catch (e) {
        console.error('Failed to load estates from database', e);
      }

      this.data = { counties, areas, estates: dbEstates };
      return this.data;
    } catch(e) {
      console.error('Failed to load locations dataset', e);
      return { counties: [], areas: [], estates: [] };
    }
  },

  async search(query) {
    const { counties, areas, estates } = await this.load();
    if (!query || query.length < 2) return [];
    
    const q = query.toLowerCase();
    const matches = [];

    // 1. Search static counties/sub-counties/wards
    for (const c of counties) {
      if (c.county_name.toLowerCase().includes(q)) {
        matches.push({
          name: c.county_name,
          type: 'county',
          value: c.county_name,
          county: c.county_name
        });
      }

      for (const sub of c.constituencies || []) {
        if (sub.constituency_name.toLowerCase().includes(q)) {
          matches.push({
            name: `${sub.constituency_name} (${c.county_name})`,
            type: 'sub_county',
            value: sub.constituency_name,
            county: c.county_name,
            sub_county: sub.constituency_name
          });
        }

        for (const w of sub.wards || []) {
          if (w.toLowerCase().includes(q)) {
            matches.push({
              name: `${w} (${sub.constituency_name}, ${c.county_name})`,
              type: 'ward',
              value: w,
              county: c.county_name,
              sub_county: sub.constituency_name,
              ward: w
            });
          }
        }
      }
    }

    // 2. Search static villages/areas (1,800+ real places in Kenya)
    for (const a of areas || []) {
      if (a.name.toLowerCase().includes(q)) {
        matches.push({
          name: `${a.name} (${a.locality || ''}, ${a.county || ''})`,
          type: 'village',
          value: a.name,
          county: a.county,
          sub_county: a.locality
        });
      }
    }

    // 3. Search active estates/villages from database
    for (const est of estates) {
      if (est.name.toLowerCase().includes(q)) {
        matches.push({
          name: `${est.name} (${est.sub_county || ''}, ${est.county || ''})`,
          type: 'estate',
          value: est.name,
          slug: est.slug,
          county: est.county,
          sub_county: est.sub_county
        });
      }
    }

    // Sort: exact matches first, limit to 8
    return matches
      .sort((a, b) => {
        const aExact = a.value.toLowerCase() === q;
        const bExact = b.value.toLowerCase() === q;
        if (aExact && !bExact) return -1;
        if (!aExact && bExact) return 1;
        return 0;
      })
      .slice(0, 8);
  },

  async setupAutocomplete(inputEl, onSelect) {
    if (!inputEl) return;

    // Wrap input for suggestions dropdown styling
    if (!inputEl.parentElement.classList.contains('autocomplete-container')) {
      const container = document.createElement('div');
      container.className = 'autocomplete-container';
      inputEl.parentNode.insertBefore(container, inputEl);
      container.appendChild(inputEl);
    }

    const sugBox = document.createElement('div');
    sugBox.className = 'autocomplete-suggestions hidden';
    inputEl.parentNode.appendChild(sugBox);

    // Preload
    this.load();

    inputEl.addEventListener('input', async () => {
      const val = inputEl.value.trim();
      if (val.length < 2) {
        sugBox.classList.add('hidden');
        return;
      }

      const results = await this.search(val);
      if (!results.length) {
        sugBox.classList.add('hidden');
        return;
      }

      sugBox.innerHTML = results.map(item => `
        <div class="autocomplete-suggestion" data-val="${item.value}" data-type="${item.type}" data-county="${item.county}" data-sub_county="${item.sub_county || ''}" data-ward="${item.ward || ''}" data-slug="${item.slug || ''}">
          <span>${item.name}</span>
          <span class="autocomplete-suggestion__type">${item.type.replace('_', ' ')}</span>
        </div>`).join('');
      
      sugBox.classList.remove('hidden');
    });

    sugBox.addEventListener('click', (e) => {
      const item = e.target.closest('.autocomplete-suggestion');
      if (!item) return;

      inputEl.value = item.dataset.val;
      sugBox.classList.add('hidden');

      if (onSelect) {
        onSelect({
          value: item.dataset.val,
          type: item.dataset.type,
          county: item.dataset.county,
          sub_county: item.dataset.sub_county,
          ward: item.dataset.ward,
          slug: item.dataset.slug
        });
      }
    });

    document.addEventListener('click', (e) => {
      if (!inputEl.contains(e.target) && !sugBox.contains(e.target)) {
        sugBox.classList.add('hidden');
      }
    });
  }
};

window.locations = locations;

