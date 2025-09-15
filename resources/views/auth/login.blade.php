<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion - Gest_Stock-Auris</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
   <link rel="shortcut icon" href="{{ url('assets/images/logo-sm.png') }}">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #02226b;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
 .bg {
  background: url('{{ url('assets/images/bg.png') }}') no-repeat center center fixed;
  /* background-size: cover; */
  height: 100vh;
  width: 100vw;
  position: fixed;   /* 👈 important */
  top: 0;
  left: 0;
  z-index: -1;       /* 👈 passe derrière */
}

.glass-card {
  background: rgba(255, 255, 255, 0.10);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border-radius: 20px;
  padding: 40px;
  width: 100%;
  max-width: 400px;
  color: #fff;
  position: relative;  /* 👈 reste au-dessus */
  z-index: 1;
}



    .glass-card h2 {
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      border-radius: 12px;
      color: #fff;
      padding: 12px 15px;
      margin-bottom: 20px;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.25);
      box-shadow: none;
      outline: none;
    }

    .btn-glass {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(to right, #02226b, #e6ba23);
      color: white;
      font-weight: 500;
      transition: 0.3s ease;
    }

    .btn-glass:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .text-small {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .text-small a {
      color: #fff;
      text-decoration: underline;
    }
  </style>
</head>
<body>
    <div class="bg"></div>
  <div class="glass-card">
    <h2><i class="fas fa-boxes me-2"></i>Gestion_USP</h2>
    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email -->
      <div class="mb-3">
        <input type="email" name="email" placeholder="Adresse email" class="form-control @error('email') is-invalid @enderror" required autofocus>
        @error('email')
          <div class="invalid-feedback d-block text-danger">{{ $message }}</div>
        @enderror
      </div>

      <!-- Mot de passe -->
      <div class="mb-3">
        <input type="password" name="password" placeholder="Mot de passe" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')
          <div class="invalid-feedback d-block text-danger">{{ $message }}</div>
        @enderror
      </div>

      <!-- Bouton -->
      <button type="submit" class="btn-glass">
        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
      </button>


    </form>
  </div>

</body>
</html>
