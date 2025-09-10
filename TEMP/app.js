// ACME Corp CSR Platform - Main Application Logic

// Application Data
const appData = {
  "users": [
    {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean.dupont@acmecorp.com",
      "role": "employee",
      "department": "Marketing",
      "employeeId": "EMP001",
      "avatar": "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150",
      "totalDonated": 205,
      "campaignsSupported": 4,
      "badges": ["first_timer", "regular_donor", "community_helper"]
    },
    {
      "id": 2,
      "name": "Marie Martin",
      "email": "marie.martin@acmecorp.com",
      "role": "admin",
      "department": "IT",
      "employeeId": "ADM001",
      "avatar": "https://images.unsplash.com/photo-1494790108755-2616b612b5bb?w=150",
      "totalDonated": 425,
      "campaignsSupported": 3,
      "badges": ["admin", "top_supporter", "campaign_creator", "green_champion"]
    }
  ],
  "campaigns": [
    {
      "id": 1,
      "title": "Aide d'urgence pour les victimes d'inondations",
      "description": "Les récentes inondations ont touché des milliers de familles. ACME Corp souhaite apporter une aide immédiate pour l'eau potable, la nourriture et les abris temporaires.",
      "owner": 1,
      "goalAmount": 50000,
      "currentAmount": 32450,
      "status": "active",
      "category": "Urgence",
      "tags": ["urgence", "humanitaire", "solidarité"],
      "startDate": "2025-08-25",
      "endDate": "2025-10-24",
      "image": "https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=600",
      "likes": 156,
      "comments": 23,
      "supporters": 87
    },
    {
      "id": 2,
      "title": "Reforestation de la forêt de Fontainebleau",
      "description": "Projet de plantation de 1000 arbres dans la forêt de Fontainebleau pour compenser notre empreinte carbone et préserver la biodiversité locale.",
      "owner": 2,
      "goalAmount": 25000,
      "currentAmount": 8750,
      "status": "active",
      "category": "Environnement",
      "tags": ["environnement", "local", "carbone"],
      "startDate": "2025-09-02",
      "endDate": "2025-12-01",
      "image": "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=600",
      "likes": 203,
      "comments": 31,
      "supporters": 64
    },
    {
      "id": 3,
      "title": "Bourses d'études pour enfants défavorisés",
      "description": "Programme de bourses pour permettre à 50 enfants issus de familles en difficulté d'accéder à l'enseignement supérieur.",
      "owner": 1,
      "goalAmount": 75000,
      "currentAmount": 45300,
      "status": "active",
      "category": "Éducation",
      "tags": ["éducation", "jeunesse", "égalité"],
      "startDate": "2025-08-10",
      "endDate": "2025-10-09",
      "image": "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600",
      "likes": 298,
      "comments": 45,
      "supporters": 142
    },
    {
      "id": 4,
      "title": "Équipement médical pour l'hôpital local",
      "description": "Financement d'un nouvel équipement de dialyse pour l'hôpital de notre région, bénéficiant à plus de 200 patients.",
      "owner": 2,
      "goalAmount": 100000,
      "currentAmount": 67890,
      "status": "active",
      "category": "Santé",
      "tags": ["santé", "local", "médical"],
      "startDate": "2025-07-25",
      "endDate": "2025-09-24",
      "image": "https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=600",
      "likes": 187,
      "comments": 29,
      "supporters": 95
    },
    {
      "id": 5,
      "title": "Reconstruction école en Afrique",
      "description": "Reconstruction complète d'une école primaire au Sénégal détruite par une tempête, pour 300 élèves.",
      "owner": 1,
      "goalAmount": 40000,
      "currentAmount": 42150,
      "status": "completed",
      "category": "Éducation",
      "tags": ["international", "éducation", "reconstruction"],
      "startDate": "2025-05-12",
      "endDate": "2025-08-30",
      "image": "https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=600",
      "likes": 421,
      "comments": 67,
      "supporters": 198
    },
    {
      "id": 6,
      "title": "Aide aux refugiés ukrainiens",
      "description": "Soutien logistique et financier pour l'accueil des familles de réfugiés ukrainiens dans notre région.",
      "owner": 2,
      "goalAmount": 60000,
      "currentAmount": 0,
      "status": "pending",
      "category": "International",
      "tags": ["international", "urgence", "guerre"],
      "startDate": null,
      "endDate": null,
      "image": "https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=600",
      "likes": 0,
      "comments": 0,
      "supporters": 0
    }
  ],
  "donations": [
    {
      "id": 1,
      "amount": 25,
      "donor": 1,
      "campaign": 5,
      "message": "Bravo pour cette belle initiative !",
      "anonymous": false,
      "date": "2025-07-25"
    },
    {
      "id": 2,
      "amount": 50,
      "donor": 1,
      "campaign": 1,
      "message": "",
      "anonymous": false,
      "date": "2025-08-28"
    },
    {
      "id": 3,
      "amount": 100,
      "donor": 1,
      "campaign": 3,
      "message": "L'éducation est notre avenir",
      "anonymous": false,
      "date": "2025-09-01"
    },
    {
      "id": 4,
      "amount": 30,
      "donor": 1,
      "campaign": 4,
      "message": "",
      "anonymous": false,
      "date": "2025-09-06"
    },
    {
      "id": 5,
      "amount": 200,
      "donor": 2,
      "campaign": 5,
      "message": "Magnifique projet solidaire",
      "anonymous": false,
      "date": "2025-07-20"
    },
    {
      "id": 6,
      "amount": 75,
      "donor": 2,
      "campaign": 2,
      "message": "Pour notre planète !",
      "anonymous": false,
      "date": "2025-09-04"
    },
    {
      "id": 7,
      "amount": 150,
      "donor": 2,
      "campaign": 3,
      "message": "",
      "anonymous": false,
      "date": "2025-08-25"
    }
  ],
  "comments": [
    {
      "id": 1,
      "campaign": 1,
      "user": 1,
      "content": "Initiative très importante, merci de porter cette cause !",
      "date": "2025-08-28",
      "likes": 5
    },
    {
      "id": 2,
      "campaign": 1,
      "user": 2,
      "content": "ACME Corp montre encore une fois son engagement social. Fier de faire partie de cette entreprise !",
      "date": "2025-08-30",
      "likes": 12
    },
    {
      "id": 3,
      "campaign": 2,
      "user": 1,
      "content": "Excellent projet pour notre empreinte environnementale. Comment peut-on s'impliquer bénévolement ?",
      "date": "2025-09-03",
      "likes": 8
    }
  ],
  "globalStats": {
    "totalRaised": 196540,
    "activeCampaigns": 4,
    "totalParticipants": 388,
    "departmentLeaderboard": [
      {"department": "Marketing", "amount": 15420},
      {"department": "IT", "amount": 23100},
      {"department": "Sales", "amount": 18750},
      {"department": "HR", "amount": 12300}
    ]
  }
};

// Application State
let currentUser = appData.users[0]; // Default to Jean Dupont
let currentCampaign = null;
let filteredCampaigns = appData.campaigns;
let formStep = 1;

// Badge definitions
const badges = {
  first_timer: { icon: '🎯', name: 'Premier Don', description: 'Premier don effectué' },
  regular_donor: { icon: '💚', name: 'Donateur Régulier', description: '3+ dons effectués' },
  community_helper: { icon: '🤝', name: 'Aide Communautaire', description: 'Soutien actif aux campagnes' },
  admin: { icon: '👑', name: 'Administrateur', description: 'Accès administration' },
  top_supporter: { icon: '🌟', name: 'Top Supporter', description: 'Parmi les plus gros donateurs' },
  campaign_creator: { icon: '🚀', name: 'Créateur de Campagne', description: 'A créé une campagne validée' },
  green_champion: { icon: '🌱', name: 'Champion Vert', description: 'Engagement environnemental' }
};

// Utility Functions
function formatCurrency(amount) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount);
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function getDaysRemaining(endDate) {
  const now = new Date();
  const end = new Date(endDate);
  const diffTime = end - now;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays;
}

function getProgressPercentage(current, goal) {
  return Math.min((current / goal) * 100, 100);
}

function showToast(message, type = 'success') {
  const toastContainer = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<div>${message}</div>`;
  
  toastContainer.appendChild(toast);
  
  setTimeout(() => {
    toast.remove();
  }, 4000);
}

// Navigation
function navigateToPage(pageId) {
  console.log('Navigating to:', pageId);
  
  // Update nav links
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.classList.remove('active');
    if (link.dataset.page === pageId) {
      link.classList.add('active');
    }
  });
  
  // Show page
  const pages = document.querySelectorAll('.page-content');
  pages.forEach(page => {
    page.classList.remove('active');
  });
  
  const targetPage = document.getElementById(pageId + 'Page');
  if (targetPage) {
    targetPage.classList.add('active');
  }
  
  // Load page content
  switch (pageId) {
    case 'home':
      loadHomePage();
      break;
    case 'campaigns':
      loadCampaignsPage();
      break;
    case 'profile':
      loadProfilePage();
      break;
    case 'admin':
      if (currentUser.role === 'admin') {
        loadAdminPage();
      }
      break;
    case 'campaign-detail':
      // Will be loaded when campaign is selected
      break;
    case 'create-campaign':
      // Reset form when navigating to create campaign
      resetCreateCampaignForm();
      break;
  }
}

// Campaign Card Creation
function createCampaignCard(campaign) {
  const owner = appData.users.find(u => u.id === campaign.owner);
  const progress = getProgressPercentage(campaign.currentAmount, campaign.goalAmount);
  const daysRemaining = campaign.endDate ? getDaysRemaining(campaign.endDate) : null;
  
  return `
    <div class="campaign-card status-${campaign.status}" data-campaign-id="${campaign.id}">
      <img src="${campaign.image}" alt="${campaign.title}" class="campaign-image" onerror="this.style.display='none'">
      <div class="campaign-content">
        <div class="campaign-category">${campaign.category}</div>
        <h3 class="campaign-title">${campaign.title}</h3>
        <p class="campaign-description">${campaign.description}</p>
        
        ${campaign.status === 'active' ? `
        <div class="campaign-progress">
          <div class="progress-bar">
            <div class="progress-fill" style="width: ${progress}%"></div>
          </div>
          <div class="progress-stats">
            <span class="progress-amount">${formatCurrency(campaign.currentAmount)}</span>
            <span class="progress-goal">objectif ${formatCurrency(campaign.goalAmount)}</span>
          </div>
        </div>
        ` : ''}
        
        <div class="campaign-footer">
          <div class="campaign-stats">
            <span class="campaign-stat">
              <i class="fas fa-heart"></i>
              ${campaign.likes}
            </span>
            <span class="campaign-stat">
              <i class="fas fa-comment"></i>
              ${campaign.comments}
            </span>
            <span class="campaign-stat">
              <i class="fas fa-users"></i>
              ${campaign.supporters}
            </span>
          </div>
          ${daysRemaining && daysRemaining > 0 ? `
          <div class="campaign-time">
            <span class="time-remaining">${daysRemaining} jours restants</span>
          </div>
          ` : campaign.status === 'completed' ? `
          <div class="status status--success">Terminée</div>
          ` : campaign.status === 'pending' ? `
          <div class="status status--warning">En attente</div>
          ` : ''}
        </div>
      </div>
    </div>
  `;
}

// Home Page
function loadHomePage() {
  // Update global stats
  document.getElementById('totalRaised').textContent = formatCurrency(appData.globalStats.totalRaised);
  document.getElementById('activeCampaigns').textContent = appData.globalStats.activeCampaigns;
  document.getElementById('totalParticipants').textContent = appData.globalStats.totalParticipants;
  
  // Load popular campaigns (sorted by likes)
  const popularCampaigns = [...appData.campaigns]
    .filter(c => c.status === 'active')
    .sort((a, b) => b.likes - a.likes)
    .slice(0, 3);
  
  const popularContainer = document.getElementById('popularCampaigns');
  if (popularContainer) {
    popularContainer.innerHTML = popularCampaigns
      .map(campaign => createCampaignCard(campaign))
      .join('');
  }
  
  // Load recent campaigns (sorted by start date)
  const recentCampaigns = [...appData.campaigns]
    .filter(c => c.status === 'active')
    .sort((a, b) => new Date(b.startDate) - new Date(a.startDate))
    .slice(0, 3);
  
  const recentContainer = document.getElementById('recentCampaigns');
  if (recentContainer) {
    recentContainer.innerHTML = recentCampaigns
      .map(campaign => createCampaignCard(campaign))
      .join('');
  }
}

// Campaigns Page
function loadCampaignsPage() {
  applyFiltersAndSort();
}

function applyFiltersAndSort() {
  let filtered = [...appData.campaigns];
  
  // Apply filters
  const statusFilter = document.getElementById('filterStatus')?.value || '';
  const categoryFilter = document.getElementById('filterCategory')?.value || '';
  const searchTerm = document.getElementById('campaignSearchInput')?.value?.toLowerCase() || '';
  
  if (statusFilter) {
    filtered = filtered.filter(c => c.status === statusFilter);
  }
  
  if (categoryFilter) {
    filtered = filtered.filter(c => c.category === categoryFilter);
  }
  
  if (searchTerm) {
    filtered = filtered.filter(c => 
      c.title.toLowerCase().includes(searchTerm) ||
      c.description.toLowerCase().includes(searchTerm) ||
      c.tags.some(tag => tag.toLowerCase().includes(searchTerm))
    );
  }
  
  // Apply sorting
  const sortBy = document.getElementById('sortBy')?.value || 'popular';
  switch (sortBy) {
    case 'popular':
      filtered.sort((a, b) => b.likes - a.likes);
      break;
    case 'amount':
      filtered.sort((a, b) => b.currentAmount - a.currentAmount);
      break;
    case 'progress':
      filtered.sort((a, b) => {
        const progressA = getProgressPercentage(a.currentAmount, a.goalAmount);
        const progressB = getProgressPercentage(b.currentAmount, b.goalAmount);
        return progressB - progressA;
      });
      break;
    case 'recent':
      filtered.sort((a, b) => new Date(b.startDate || 0) - new Date(a.startDate || 0));
      break;
  }
  
  filteredCampaigns = filtered;
  const allCampaignsContainer = document.getElementById('allCampaigns');
  if (allCampaignsContainer) {
    allCampaignsContainer.innerHTML = filtered
      .map(campaign => createCampaignCard(campaign))
      .join('');
  }
}

// Campaign Detail Page
function loadCampaignDetail(campaignId) {
  currentCampaign = appData.campaigns.find(c => c.id === parseInt(campaignId));
  if (!currentCampaign) return;
  
  const owner = appData.users.find(u => u.id === currentCampaign.owner);
  const progress = getProgressPercentage(currentCampaign.currentAmount, currentCampaign.goalAmount);
  const daysRemaining = currentCampaign.endDate ? getDaysRemaining(currentCampaign.endDate) : null;
  const campaignComments = appData.comments.filter(c => c.campaign === currentCampaign.id);
  
  const campaignDetailContainer = document.getElementById('campaignDetail');
  if (campaignDetailContainer) {
    campaignDetailContainer.innerHTML = `
      <div class="campaign-detail">
        <div class="campaign-detail-header">
          <img src="${currentCampaign.image}" alt="${currentCampaign.title}" class="campaign-detail-image" onerror="this.style.display='none'">
        </div>
        
        <div class="campaign-detail-info">
          <div class="campaign-detail-main">
            <div class="campaign-category">${currentCampaign.category}</div>
            <h1>${currentCampaign.title}</h1>
            <div class="campaign-detail-description">
              ${currentCampaign.description}
            </div>
            
            <div class="campaign-owner">
              <h3>Organisateur</h3>
              <div class="owner-info">
                <img src="${owner.avatar}" alt="${owner.name}" class="avatar-img">
                <div>
                  <div class="owner-name">${owner.name}</div>
                  <div class="owner-department">${owner.department}</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="campaign-detail-sidebar">
            <div class="sidebar-amount">${formatCurrency(currentCampaign.currentAmount)}</div>
            <div class="sidebar-goal">collectés sur ${formatCurrency(currentCampaign.goalAmount)}</div>
            
            <div class="sidebar-progress">
              <div class="progress-bar">
                <div class="progress-fill" style="width: ${progress}%"></div>
              </div>
            </div>
            
            <div class="sidebar-stats">
              <div class="sidebar-stat">
                <span class="sidebar-stat-value">${currentCampaign.supporters}</span>
                <span class="sidebar-stat-label">Donateurs</span>
              </div>
              <div class="sidebar-stat">
                <span class="sidebar-stat-value">${daysRemaining || 0}</span>
                <span class="sidebar-stat-label">Jours restants</span>
              </div>
            </div>
            
            ${currentCampaign.status === 'active' ? `
            <button class="btn btn--primary donate-button" onclick="openDonationModal(${currentCampaign.id})">
              <i class="fas fa-heart"></i>
              Faire un don
            </button>
            ` : ''}
            
            <div class="share-buttons">
              <button class="share-btn" onclick="shareCampaign('email')">
                <i class="fas fa-envelope"></i>
              </button>
              <button class="share-btn" onclick="shareCampaign('teams')">
                <i class="fab fa-microsoft"></i>
              </button>
              <button class="share-btn" onclick="likeCampaign(${currentCampaign.id})">
                <i class="fas fa-heart"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="comments-section">
          <div class="comments-header">
            <h3>Commentaires (${campaignComments.length})</h3>
          </div>
          
          <div class="comment-form">
            <textarea placeholder="Ajouter un commentaire de soutien..." rows="3" id="newComment"></textarea>
            <div class="comment-form-actions">
              <button class="btn btn--outline btn--sm" onclick="clearComment()">Annuler</button>
              <button class="btn btn--primary btn--sm" onclick="addComment()">Commenter</button>
            </div>
          </div>
          
          <div class="comments-list">
            ${campaignComments.map(comment => createComment(comment)).join('')}
          </div>
        </div>
      </div>
    `;
  }
}

function createComment(comment) {
  const user = appData.users.find(u => u.id === comment.user);
  return `
    <div class="comment">
      <div class="comment-header">
        <div class="comment-author">
          <img src="${user.avatar}" alt="${user.name}" class="comment-avatar">
          <span class="comment-name">${user.name}</span>
        </div>
        <span class="comment-date">${formatDate(comment.date)}</span>
      </div>
      <div class="comment-content">${comment.content}</div>
      <div class="comment-actions">
        <span class="comment-action" onclick="likeComment(${comment.id})">
          <i class="fas fa-heart"></i>
          ${comment.likes}
        </span>
        <span class="comment-action">
          <i class="fas fa-reply"></i>
          Répondre
        </span>
      </div>
    </div>
  `;
}

// Profile Page
function loadProfilePage() {
  // Update profile info
  const profileAvatar = document.getElementById('profileAvatar');
  const profileName = document.getElementById('profileName');
  const profileDepartment = document.getElementById('profileDepartment');
  const profileTotalDonated = document.getElementById('profileTotalDonated');
  const profileCampaignsSupported = document.getElementById('profileCampaignsSupported');
  
  if (profileAvatar) profileAvatar.src = currentUser.avatar;
  if (profileName) profileName.textContent = currentUser.name;
  if (profileDepartment) profileDepartment.textContent = currentUser.department;
  if (profileTotalDonated) profileTotalDonated.textContent = formatCurrency(currentUser.totalDonated);
  if (profileCampaignsSupported) profileCampaignsSupported.textContent = currentUser.campaignsSupported;
  
  // Load badges
  const badgesContainer = document.getElementById('profileBadges');
  if (badgesContainer) {
    badgesContainer.innerHTML = currentUser.badges.map(badgeKey => {
      const badge = badges[badgeKey];
      return `
        <div class="badge" title="${badge.description}">
          <span>${badge.icon}</span>
          <span>${badge.name}</span>
        </div>
      `;
    }).join('');
  }
  
  // Load user donations
  const userDonations = appData.donations.filter(d => d.donor === currentUser.id);
  const donationsContainer = document.getElementById('userDonations');
  if (donationsContainer) {
    donationsContainer.innerHTML = userDonations.map(donation => {
      const campaign = appData.campaigns.find(c => c.id === donation.campaign);
      return `
        <div class="donation-item card">
          <div class="card__body">
            <div class="donation-header">
              <h4>${campaign.title}</h4>
              <span class="donation-amount">${formatCurrency(donation.amount)}</span>
            </div>
            <div class="donation-date">${formatDate(donation.date)}</div>
            ${donation.message ? `<div class="donation-message">"${donation.message}"</div>` : ''}
          </div>
        </div>
      `;
    }).join('');
  }
  
  // Load user campaigns
  const userCampaigns = appData.campaigns.filter(c => c.owner === currentUser.id);
  const campaignsContainer = document.getElementById('userCampaigns');
  if (campaignsContainer) {
    campaignsContainer.innerHTML = userCampaigns.map(campaign => createCampaignCard(campaign)).join('');
  }
  
  // Load activity
  const activityContainer = document.getElementById('userActivity');
  if (activityContainer) {
    activityContainer.innerHTML = `
      <div class="activity-item">
        <div class="activity-icon"><i class="fas fa-heart"></i></div>
        <div class="activity-content">
          <div>Don de ${formatCurrency(30)} pour l'équipement médical</div>
          <div class="activity-date">Il y a 3 jours</div>
        </div>
      </div>
      <div class="activity-item">
        <div class="activity-icon"><i class="fas fa-comment"></i></div>
        <div class="activity-content">
          <div>Commentaire sur la reforestation</div>
          <div class="activity-date">Il y a 6 jours</div>
        </div>
      </div>
    `;
  }
}

// Admin Page
function loadAdminPage() {
  // Load pending campaigns
  const pendingCampaigns = appData.campaigns.filter(c => c.status === 'pending');
  const pendingContainer = document.getElementById('pendingCampaigns');
  
  if (pendingContainer) {
    pendingContainer.innerHTML = pendingCampaigns.map(campaign => {
      const owner = appData.users.find(u => u.id === campaign.owner);
      return `
        <div class="pending-campaign">
          <div class="pending-campaign-header">
            <div>
              <h4>${campaign.title}</h4>
              <div>Par ${owner.name} (${owner.department})</div>
              <div class="campaign-category">${campaign.category}</div>
            </div>
            <div>
              <div>Objectif: ${formatCurrency(campaign.goalAmount)}</div>
            </div>
          </div>
          <div class="campaign-description">${campaign.description}</div>
          <div class="pending-campaign-actions">
            <button class="btn btn--primary btn--sm" onclick="approveCampaign(${campaign.id})">
              <i class="fas fa-check"></i> Approuver
            </button>
            <button class="btn btn--outline btn--sm" onclick="requestChanges(${campaign.id})">
              <i class="fas fa-edit"></i> Demander modifications
            </button>
            <button class="btn btn--outline btn--sm" onclick="rejectCampaign(${campaign.id})">
              <i class="fas fa-times"></i> Refuser
            </button>
          </div>
        </div>
      `;
    }).join('');
    
    if (pendingCampaigns.length === 0) {
      pendingContainer.innerHTML = '<p>Aucune campagne en attente de validation.</p>';
    }
  }
  
  // Initialize charts
  setTimeout(() => {
    initializeCharts();
  }, 100);
}

function initializeCharts() {
  // Donations chart
  const donationsCtx = document.getElementById('donationsChart');
  if (donationsCtx && typeof Chart !== 'undefined') {
    new Chart(donationsCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep'],
        datasets: [{
          label: 'Dons (€)',
          data: [12000, 15000, 18000, 22000, 25000, 28000, 32000, 35000, 38000],
          borderColor: '#1FB8CD',
          backgroundColor: 'rgba(31, 184, 205, 0.1)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
  
  // Department chart
  const departmentCtx = document.getElementById('departmentChart');
  if (departmentCtx && typeof Chart !== 'undefined') {
    new Chart(departmentCtx, {
      type: 'doughnut',
      data: {
        labels: ['IT', 'Sales', 'Marketing', 'HR'],
        datasets: [{
          data: [23100, 18750, 15420, 12300],
          backgroundColor: ['#1FB8CD', '#FFC185', '#B4413C', '#ECEBD5']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  }
}

// Campaign Creation
function resetCreateCampaignForm() {
  formStep = 1;
  const form = document.getElementById('createCampaignForm');
  if (form) {
    form.reset();
    updateFormStep();
  }
}

function updateFormStep() {
  const steps = document.querySelectorAll('.form-step');
  const stepIndicators = document.querySelectorAll('.step');
  const nextBtn = document.getElementById('nextStep');
  const prevBtn = document.getElementById('prevStep');
  const submitBtn = document.getElementById('submitCampaign');
  
  // Update step indicators
  stepIndicators.forEach((step, index) => {
    if (index + 1 <= formStep) {
      step.classList.add('active');
    } else {
      step.classList.remove('active');
    }
  });
  
  // Update form steps
  steps.forEach((step, index) => {
    if (index + 1 === formStep) {
      step.classList.add('active');
    } else {
      step.classList.remove('active');
    }
  });
  
  // Update buttons
  if (prevBtn) prevBtn.style.display = formStep > 1 ? 'inline-block' : 'none';
  if (nextBtn) nextBtn.style.display = formStep < 3 ? 'inline-block' : 'none';
  if (submitBtn) submitBtn.style.display = formStep === 3 ? 'inline-block' : 'none';
  
  // Update preview on step 3
  if (formStep === 3) {
    updateCampaignPreview();
  }
}

function updateCampaignPreview() {
  const title = document.getElementById('campaignTitle')?.value || '';
  const category = document.getElementById('campaignCategory')?.value || '';
  const description = document.getElementById('campaignDescription')?.value || '';
  const goal = document.getElementById('campaignGoal')?.value || '';
  const endDate = document.getElementById('campaignEndDate')?.value || '';
  const image = document.getElementById('campaignImage')?.value || '';
  
  const preview = document.getElementById('campaignPreview');
  if (preview) {
    preview.innerHTML = `
      <div class="campaign-card">
        ${image ? `<img src="${image}" alt="${title}" class="campaign-image" onerror="this.style.display='none'">` : ''}
        <div class="campaign-content">
          <div class="campaign-category">${category}</div>
          <h3 class="campaign-title">${title || 'Titre de votre campagne'}</h3>
          <p class="campaign-description">${description || 'Description de votre campagne'}</p>
          <div class="campaign-progress">
            <div class="progress-bar">
              <div class="progress-fill" style="width: 0%"></div>
            </div>
            <div class="progress-stats">
              <span class="progress-amount">0 €</span>
              <span class="progress-goal">objectif ${goal ? formatCurrency(parseInt(goal)) : '0 €'}</span>
            </div>
          </div>
          ${endDate ? `<div class="campaign-time">Se termine le ${formatDate(endDate)}</div>` : ''}
        </div>
      </div>
    `;
  }
}

function submitCampaign() {
  const loadingOverlay = document.getElementById('loadingOverlay');
  if (loadingOverlay) {
    loadingOverlay.classList.remove('hidden');
  }
  
  setTimeout(() => {
    if (loadingOverlay) {
      loadingOverlay.classList.add('hidden');
    }
    showToast('Votre campagne a été soumise pour validation. Vous recevrez une notification dès qu\'elle sera examinée.', 'success');
    
    // Reset form
    resetCreateCampaignForm();
    
    // Navigate to profile
    navigateToPage('profile');
  }, 2000);
}

// Donation Modal
function openDonationModal(campaignId) {
  currentCampaign = appData.campaigns.find(c => c.id === campaignId);
  const donationModal = document.getElementById('donationModal');
  if (donationModal) {
    donationModal.classList.remove('hidden');
    
    // Reset form
    document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
    const customAmount = document.getElementById('customAmount');
    const anonymousDonation = document.getElementById('anonymousDonation');
    const donationMessage = document.getElementById('donationMessage');
    
    if (customAmount) customAmount.value = '';
    if (anonymousDonation) anonymousDonation.checked = false;
    if (donationMessage) donationMessage.value = '';
  }
}

function closeDonationModal() {
  const donationModal = document.getElementById('donationModal');
  if (donationModal) {
    donationModal.classList.add('hidden');
  }
}

function processDonation() {
  const selectedAmount = document.querySelector('.amount-btn.selected');
  const customAmount = document.getElementById('customAmount')?.value || '';
  const anonymous = document.getElementById('anonymousDonation')?.checked || false;
  const message = document.getElementById('donationMessage')?.value || '';
  
  const amount = selectedAmount ? parseInt(selectedAmount.dataset.amount) : parseInt(customAmount);
  
  if (!amount || amount <= 0) {
    showToast('Veuillez sélectionner un montant valide', 'error');
    return;
  }
  
  const loadingOverlay = document.getElementById('loadingOverlay');
  if (loadingOverlay) {
    loadingOverlay.classList.remove('hidden');
  }
  closeDonationModal();
  
  setTimeout(() => {
    if (loadingOverlay) {
      loadingOverlay.classList.add('hidden');
    }
    
    // Update campaign amount
    if (currentCampaign) {
      currentCampaign.currentAmount += amount;
      currentCampaign.supporters += 1;
    }
    
    // Update user total
    currentUser.totalDonated += amount;
    
    // Add donation record
    const newDonation = {
      id: appData.donations.length + 1,
      amount: amount,
      donor: currentUser.id,
      campaign: currentCampaign.id,
      message: message,
      anonymous: anonymous,
      date: new Date().toISOString().split('T')[0]
    };
    appData.donations.push(newDonation);
    
    showToast(`Merci pour votre don de ${formatCurrency(amount)} !`, 'success');
    
    // Refresh current view
    if (document.getElementById('campaignDetailPage')?.classList.contains('active') && currentCampaign) {
      loadCampaignDetail(currentCampaign.id);
    }
  }, 2000);
}

// Social Features
function likeCampaign(campaignId) {
  const campaign = appData.campaigns.find(c => c.id === campaignId);
  if (campaign) {
    campaign.likes += 1;
    showToast('👍 Campagne likée !', 'success');
    
    // Refresh if on detail page
    if (currentCampaign && currentCampaign.id === campaignId) {
      loadCampaignDetail(campaignId);
    }
  }
}

function shareCampaign(platform) {
  showToast(`Campagne partagée sur ${platform} !`, 'success');
}

function addComment() {
  const newCommentInput = document.getElementById('newComment');
  const content = newCommentInput?.value.trim() || '';
  if (!content) return;
  
  const newComment = {
    id: appData.comments.length + 1,
    campaign: currentCampaign.id,
    user: currentUser.id,
    content: content,
    date: new Date().toISOString().split('T')[0],
    likes: 0
  };
  
  appData.comments.push(newComment);
  if (currentCampaign) {
    currentCampaign.comments += 1;
  }
  
  if (newCommentInput) {
    newCommentInput.value = '';
  }
  showToast('Commentaire ajouté !', 'success');
  
  // Refresh campaign detail
  if (currentCampaign) {
    loadCampaignDetail(currentCampaign.id);
  }
}

function clearComment() {
  const newCommentInput = document.getElementById('newComment');
  if (newCommentInput) {
    newCommentInput.value = '';
  }
}

function likeComment(commentId) {
  const comment = appData.comments.find(c => c.id === commentId);
  if (comment) {
    comment.likes += 1;
    showToast('Commentaire liké !', 'success');
    if (currentCampaign) {
      loadCampaignDetail(currentCampaign.id);
    }
  }
}

// Admin Functions
function approveCampaign(campaignId) {
  const campaign = appData.campaigns.find(c => c.id === campaignId);
  if (campaign) {
    campaign.status = 'active';
    campaign.startDate = new Date().toISOString().split('T')[0];
    
    showToast('Campagne approuvée et publiée !', 'success');
    loadAdminPage();
  }
}

function requestChanges(campaignId) {
  showToast('Demande de modifications envoyée au créateur', 'warning');
}

function rejectCampaign(campaignId) {
  showToast('Campagne refusée', 'error');
}

// User switching (for demo purposes)
function switchUser() {
  currentUser = currentUser.id === 1 ? appData.users[1] : appData.users[0];
  
  // Update UI
  const userName = document.getElementById('userName');
  const userAvatarImg = document.querySelector('#userAvatar .avatar-img');
  const adminLink = document.getElementById('adminLink');
  
  if (userName) userName.textContent = currentUser.name;
  if (userAvatarImg) userAvatarImg.src = currentUser.avatar;
  
  // Show/hide admin link
  if (adminLink) {
    adminLink.style.display = currentUser.role === 'admin' ? 'block' : 'none';
  }
  
  showToast(`Connecté en tant que ${currentUser.name}`, 'success');
  
  // Refresh current page
  const activePage = document.querySelector('.page-content.active');
  if (activePage) {
    const pageId = activePage.id.replace('Page', '');
    navigateToPage(pageId);
  }
}

// Event Listeners
function initializeEventListeners() {
  // Navigation links
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.page;
      if (page === 'admin' && currentUser.role !== 'admin') {
        showToast('Accès non autorisé', 'error');
        return;
      }
      navigateToPage(page);
    });
  });
  
  // User dropdown
  const userAvatar = document.getElementById('userAvatar');
  const userDropdown = document.getElementById('userDropdown');
  
  if (userAvatar && userDropdown) {
    userAvatar.addEventListener('click', () => {
      userDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!userAvatar.contains(e.target)) {
        userDropdown.classList.remove('show');
      }
    });
  }
  
  // Campaign cards click handling
  document.addEventListener('click', (e) => {
    const campaignCard = e.target.closest('.campaign-card');
    if (campaignCard && !e.target.closest('button')) {
      e.preventDefault();
      const campaignId = campaignCard.dataset.campaignId;
      loadCampaignDetail(campaignId);
      navigateToPage('campaign-detail');
    }
  });
  
  // Search and filters
  const campaignSearchInput = document.getElementById('campaignSearchInput');
  if (campaignSearchInput) {
    campaignSearchInput.addEventListener('input', applyFiltersAndSort);
  }
  
  const homeSearchInput = document.getElementById('homeSearchInput');
  if (homeSearchInput) {
    homeSearchInput.addEventListener('input', (e) => {
      console.log('Home search:', e.target.value);
    });
  }
  
  [document.getElementById('filterStatus'), document.getElementById('filterCategory'), document.getElementById('sortBy')]
    .forEach(filter => {
      if (filter) {
        filter.addEventListener('change', applyFiltersAndSort);
      }
    });
  
  // Modal handling
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay') || e.target.classList.contains('modal-close')) {
      closeDonationModal();
    }
  });
  
  // Amount buttons in donation modal
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('amount-btn')) {
      document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
      e.target.classList.add('selected');
      const customAmount = document.getElementById('customAmount');
      if (customAmount) customAmount.value = '';
    }
  });
  
  // Custom amount input
  const customAmount = document.getElementById('customAmount');
  if (customAmount) {
    customAmount.addEventListener('input', () => {
      document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('selected'));
    });
  }
  
  // Donation buttons
  const confirmDonation = document.getElementById('confirmDonation');
  const cancelDonation = document.getElementById('cancelDonation');
  
  if (confirmDonation) confirmDonation.addEventListener('click', processDonation);
  if (cancelDonation) cancelDonation.addEventListener('click', closeDonationModal);
  
  // Tab management
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const tabName = btn.dataset.tab;
      if (tabName) {
        // Profile tabs
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        btn.classList.add('active');
        const tabContent = document.getElementById(tabName + 'Tab');
        if (tabContent) tabContent.classList.add('active');
      } else if (btn.dataset.adminTab) {
        // Admin tabs
        const adminTabName = btn.dataset.adminTab;
        document.querySelectorAll('[data-admin-tab]').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
        
        btn.classList.add('active');
        const adminTabContent = document.getElementById(adminTabName + 'Tab');
        if (adminTabContent) adminTabContent.classList.add('active');
        
        if (adminTabName === 'overview') {
          setTimeout(initializeCharts, 100);
        }
      }
    });
  });
  
  // Campaign form steps
  const nextStep = document.getElementById('nextStep');
  const prevStep = document.getElementById('prevStep');
  const createCampaignForm = document.getElementById('createCampaignForm');
  
  if (nextStep) {
    nextStep.addEventListener('click', () => {
      if (formStep < 3) {
        formStep++;
        updateFormStep();
      }
    });
  }
  
  if (prevStep) {
    prevStep.addEventListener('click', () => {
      if (formStep > 1) {
        formStep--;
        updateFormStep();
      }
    });
  }
  
  if (createCampaignForm) {
    createCampaignForm.addEventListener('submit', (e) => {
      e.preventDefault();
      submitCampaign();
    });
  }
  
  // Logout/switch user
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', switchUser);
  }
  
  // Button navigation
  document.addEventListener('click', (e) => {
    if (e.target.dataset.page && !e.target.classList.contains('nav-link')) {
      e.preventDefault();
      navigateToPage(e.target.dataset.page);
    }
  });
}

// Initialize Application
function initializeApp() {
  console.log('Initializing ACME Corp CSR Platform...');
  
  // Set up user
  const userName = document.getElementById('userName');
  const userAvatarImg = document.querySelector('#userAvatar .avatar-img');
  const adminLink = document.getElementById('adminLink');
  
  if (userName) userName.textContent = currentUser.name;
  if (userAvatarImg) userAvatarImg.src = currentUser.avatar;
  
  if (currentUser.role === 'admin' && adminLink) {
    adminLink.style.display = 'block';
  }
  
  // Initialize components
  initializeEventListeners();
  
  // Initialize form step tracking
  updateFormStep();
  
  // Load initial page
  loadHomePage();
  
  console.log('ACME Corp CSR Platform initialized successfully');
}

// Global functions for inline onclick handlers
window.openDonationModal = openDonationModal;
window.closeDonationModal = closeDonationModal;
window.processDonation = processDonation;
window.likeCampaign = likeCampaign;
window.shareCampaign = shareCampaign;
window.addComment = addComment;
window.clearComment = clearComment;
window.likeComment = likeComment;
window.approveCampaign = approveCampaign;
window.requestChanges = requestChanges;
window.rejectCampaign = rejectCampaign;

// Start the application when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeApp);