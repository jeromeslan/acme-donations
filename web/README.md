# 🚀 **ACME Donations - Brief Complet du Projet**

## 🎯 **But de l'Application**

**ACME Donations** est une plateforme de dons solidaire moderne qui permet aux utilisateurs de créer et soutenir des campagnes de collecte de fonds pour des causes sociales. L'application suit une architecture **API-first** avec un frontend Vue.js moderne et un backend Laravel robuste.

### **Fonctionnalités Principales :**
- ✅ **Création de campagnes** avec objectifs financiers
- ✅ **Système de catégories** (Éducation, Santé, Environnement, etc.)
- ✅ **Authentification sécurisée** avec Laravel Sanctum
- ✅ **Interface responsive** en anglais
- ✅ **Gestion des statuts** (Brouillon → Publication → Modération)
- ✅ **Système de permissions** avec Spatie Laravel Permission

---

## 🏗️ **Implémentation Détaillée**

### **Architecture Générale**
```
📁 Monorepo Structure
├── api/           # Backend Laravel 12.x
├── web/           # Frontend Vue.js 3 + Vite
└── shared/        # Schémas OpenAPI (optionnel)
```

### **Backend - Laravel 12.x**

#### **Configuration Principale**
```php
// config/app.php - Version et nom
'name' => 'ACME Donations',
'version' => '1.0.0'

// Environnement
APP_NAME="ACME Donations"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
SESSION_DRIVER=file
```

#### **Modèles et Relations**
```php
// User Model - Authentification
class User extends Authenticatable {
    use HasApiTokens, HasRoles;
    
    protected $fillable = ['name', 'email', 'password'];
}

// Campaign Model - Campagnes
class Campaign extends Model {
    protected $fillable = [
        'title', 'description', 'goal_amount', 
        'category_id', 'status', 'featured'
    ];
    
    public function category() {
        return $this->belongsTo(Category::class);
    }
}

// Category Model - Catégories
class Category extends Model {
    protected $fillable = ['name', 'slug'];
}
```

#### **API Routes**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('categories', CategoryController::class);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
```

#### **Contrôleurs API**
```php
// CampaignController - Gestion des campagnes
class CampaignController extends Controller {
    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,pending',
            'featured' => 'boolean'
        ]);
        
        $campaign = Campaign::create($validated);
        return response()->json($campaign, 201);
    }
}
```

### **Frontend - Vue.js 3 + Vite**

#### **Architecture Frontend**
```typescript
// stores/auth.ts - Gestion de l'authentification
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
  }),
  
  actions: {
    async login(credentials: LoginCredentials) {
      const response = await api.post('/api/login', credentials);
      this.user = response.data.user;
      this.token = response.data.token;
    }
  }
});
```

#### **Client API avec Gestion CSRF**
```typescript
// api/client.ts - Client HTTP avec intercepteurs
export const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Intercepteur CSRF automatique
api.interceptors.request.use(async (config) => {
  if (['post', 'put', 'delete', 'patch'].includes(config.method?.toLowerCase() || '')) {
    await ensureCsrfToken();
  }
  return config;
});
```

#### **Composants Vue.js**
```vue
<!-- CreateCampaignView.vue - Formulaire de création -->
<template>
  <div class="create-campaign-view">
    <div class="container">
      <div class="page-header">
        <h1>Create a New Campaign</h1>
        <p>Fill in the information for your charitable campaign</p>
      </div>

      <form @submit.prevent="handleSubmit" class="campaign-form">
        <!-- Formulaire avec validation -->
        <div class="form-section">
          <h3>Basic Information</h3>
          <div class="form-group">
            <label for="title">Campaign Title *</label>
            <input v-model="form.title" type="text" required />
          </div>
          <!-- Autres champs... -->
        </div>

        <!-- Messages de succès/erreur -->
        <div v-if="showSuccess" class="success-message">
          <div class="success-header">
            <i class="fas fa-check-circle"></i> Success!
          </div>
          <p class="success-text">{{ successMessage }}</p>
        </div>

        <!-- Boutons d'action -->
        <div class="form-actions">
          <button @click="saveAsDraft" class="btn btn-secondary">
            Save as Draft
          </button>
          <button type="submit" class="btn btn-primary">
            Publish Campaign
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
```

### **Sécurité et Authentification**

#### **Laravel Sanctum**
```php
// Configuration Sanctum
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
'guard' => ['web'],
```

#### **Middleware Auth**
```php
// Protection des routes API
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('campaigns', CampaignController::class);
});
```

### **Base de Données**

#### **Migration Categories**
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

#### **Migration Campaigns**
```php
Schema::create('campaigns', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->decimal('goal_amount', 10, 2);
    $table->foreignId('category_id')->constrained();
    $table->enum('status', ['draft', 'pending', 'published', 'rejected']);
    $table->boolean('featured')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});
```

#### **Seeders**
```php
// CategorySeeder
Category::create(['name' => 'Education', 'slug' => 'education']);
Category::create(['name' => 'Health', 'slug' => 'health']);
Category::create(['name' => 'Environment', 'slug' => 'environment']);

// DemoSeeder - Utilisateur admin
User::create([
    'name' => 'Admin',
    'email' => 'admin@acme.test',
    'password' => Hash::make('password'),
])->assignRole('admin');
```

---

## 🐳 **Guide de Reprise - Lignes de Commandes Docker**

### **Prérequis**
- Docker Desktop installé
- Ports 8080, 5173, 6379 disponibles
- Windows/Linux/Mac avec terminal

### **1. Démarrage Initial**
```bash
# Cloner le repository (si applicable)
git clone <repository-url>
cd optimy

# Construire et démarrer tous les services
docker-compose --profile dev up --build -d
```

### **2. Vérification des Services**
```bash
# Vérifier que tous les conteneurs sont opérationnels
docker-compose --profile dev ps

# Devrait afficher :
# acme-donations-api-php-1     Running
# acme-donations-web-1         Running  
# acme-donations-api-nginx-1   Running
# acme-donations-redis-1       Running
```

### **3. Configuration de Base de Données**
```bash
# Créer la base de données SQLite (si nécessaire)
docker-compose exec api-php touch database/database.sqlite

# Exécuter les migrations
docker-compose exec api-php php artisan migrate

# Peupler la base avec des données de test
docker-compose exec api-php php artisan db:seed
```

### **4. Configuration de l'Environnement**
```bash
# Générer la clé d'application (si nécessaire)
docker-compose exec api-php php artisan key:generate

# Nettoyer les caches
docker-compose exec api-php php artisan cache:clear
docker-compose exec api-php php artisan config:clear
docker-compose exec api-php php artisan route:clear
```

### **5. Accès aux Interfaces**
```bash
# Interface utilisateur (Frontend Vue.js)
open http://localhost:5173

# API Documentation (si disponible)
open http://localhost:8080/api/documentation

# API directe
curl http://localhost:8080/api/categories
```

### **6. Comptes de Test**
```bash
# Compte administrateur
Email: admin@acme.test
Password: password

# Compte utilisateur normal  
Email: user@example.com
Password: password
```

### **7. Commandes Utiles pour le Développement**
```bash
# Redémarrer tous les services
docker-compose --profile dev restart

# Voir les logs de l'API
docker-compose --profile dev logs api-php -f

# Voir les logs du frontend
docker-compose --profile dev logs web -f

# Accéder au container API
docker-compose exec api-php bash

# Accéder au container Frontend
docker-compose exec web sh

# Arrêter tous les services
docker-compose --profile dev down

# Nettoyer complètement (volumes inclus)
docker-compose --profile dev down --volumes
docker system prune -f
```

### **8. Structure des Fichiers Importants**
```
📁 api/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── CampaignController.php
│   │   └── CategoryController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Campaign.php
│   │   └── Category.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── .env

📁 web/
├── src/
│   ├── views/
│   │   ├── LoginView.vue
│   │   ├── CreateCampaignView.vue
│   │   └── HomeView.vue
│   ├── stores/
│   │   └── auth.ts
│   ├── api/
│   │   └── client.ts
│   └── router/
│       └── index.ts
├── public/
└── vite.config.ts
```

### **9. Fonctionnalités Testées**
```bash
# Tester l'API des catégories
curl -X GET http://localhost:8080/api/categories \
  -H "Accept: application/json"

# Tester l'authentification (devrait retourner "Unauthenticated")
curl -X POST http://localhost:8080/api/campaigns \
  -H "Content-Type: application/json" \
  -d '{"title":"Test","description":"Test","goal_amount":100,"category_id":1}'

# Tester avec authentification (nécessite token)
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@acme.test","password":"password"}'
```

### **10. Dépannage**
```bash
# Si erreur 502 Bad Gateway
docker-compose --profile dev restart

# Si problème de permissions sur la DB
docker-compose exec api-php chmod 777 database/database.sqlite

# Si problème de cache
docker-compose exec api-php php artisan cache:clear
docker-compose exec api-php php artisan config:clear

# Vérifier les logs d'erreur
docker-compose --profile dev logs api-php
```

---

## 🎯 **Résumé Exécutif**

**ACME Donations** est une plateforme moderne de dons solidaires avec :
- ✅ **Backend robuste** : Laravel 12.x + Sanctum + SQLite
- ✅ **Frontend moderne** : Vue.js 3 + Vite + TypeScript
- ✅ **Architecture API-first** : RESTful avec documentation
- ✅ **Sécurité** : CSRF protection + permissions + validation
- ✅ **Interface utilisateur** : Responsive, accessible, en anglais

**Temps de démarrage** : 2-3 minutes avec Docker  
**État actuel** : ✅ Fonctionnel et prêt pour le développement

**L'application est maintenant opérationnelle et peut être reprise facilement depuis son état actuel !** 🚀