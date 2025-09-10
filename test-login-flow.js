// Script de test pour vérifier le flux de login complet
const axios = require('axios');

async function testLoginFlow() {
  console.log('🚀 Test du flux de login Sanctum complet\n');

  try {
    // Étape 1: Récupérer le token CSRF
    console.log('1️⃣ Récupération du token CSRF...');
    const csrfResponse = await axios.get('http://localhost:8080/sanctum/csrf-cookie', {
      withCredentials: true
    });
    console.log('✅ Token CSRF récupéré:', csrfResponse.status);

    // Étape 2: Tentative de login
    console.log('\n2️⃣ Tentative de login...');
    const loginResponse = await axios.post('http://localhost:8080/api/login', {
      email: 'admin@acme.test',
      password: 'password'
    }, {
      withCredentials: true,
      headers: {
        'Accept': 'application/json'
      }
    });

    console.log('✅ Login réussi:', loginResponse.status);
    console.log('Données utilisateur:', loginResponse.data);

    // Étape 3: Récupération des infos utilisateur
    console.log('\n3️⃣ Récupération des infos utilisateur...');
    const userResponse = await axios.get('http://localhost:8080/api/me', {
      withCredentials: true,
      headers: {
        'Accept': 'application/json'
      }
    });

    console.log('✅ Infos utilisateur récupérées:', userResponse.status);
    console.log('Utilisateur:', userResponse.data);

    console.log('\n🎉 Flux de login complètement fonctionnel !');

  } catch (error) {
    console.error('\n❌ Erreur dans le flux de login:');
    if (error.response) {
      console.error('Status:', error.response.status);
      console.error('Data:', error.response.data);
    } else {
      console.error('Error:', error.message);
    }
  }
}

testLoginFlow();
