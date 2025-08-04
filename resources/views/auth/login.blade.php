<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3f72af;
            --secondary-color: #112d4e;
            --accent-color: #dbe2ef;
            --light-color: #f9f7f7;
        }

        body {
            background: linear-gradient(135deg, var(--light-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            background: white;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            padding: 1.5rem;
            text-align: center;
        }

        .card-header h4 {
            font-weight: 600;
            margin: 0;
            color: white;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(63, 114, 175, 0.25);
        }

        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .input-with-icon {
            border-left: none;
        }

        .input-with-icon:focus {
            border-left: none;
        }

        .forgot-link {
            color: var(--primary-color);
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider-text {
            padding: 0 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            font-weight: 500;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .social-btn i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .btn-google {
            background: #fff;
            color: #757575;
            border: 1px solid #e0e0e0;
        }

        .btn-google:hover {
            background: #f5f5f5;
            border-color: #d0d0d0;
        }

        .btn-facebook {
            background: #3b5998;
            color: white;
        }

        .btn-facebook:hover {
            background: #344e86;
            color: white;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }

        .register-link a {
            color: var(--primary-color);
            font-weight: 500;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: var(--secondary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6 col-lg-5">
            @if (session('status'))
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
            @endif

            <div class="login-card animate__animated animate__fadeInUp">
                <div class="card-header">
                    <h4><i class="fas fa-sign-in-alt me-2"></i>Connexion</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label">Adresse Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email"
                                    class="form-control input-with-icon @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autofocus
                                    placeholder="votre@email.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password"
                                    class="form-control input-with-icon @error('password') is-invalid @enderror" required
                                    placeholder="••••••••">
                                <span class="input-group-text bg-transparent" id="togglePassword" style="cursor: pointer;">
                                    <i class="fas fa-eye text-muted"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-login text-white">
                                <i class="fas fa-sign-in-alt me-2"></i> Connexion
                            </button>
                        </div>


                    </form>


                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
