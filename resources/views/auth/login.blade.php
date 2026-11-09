
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - SLOTIN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        :root {
            --primary: #172554;
            --accent: #f87b1b;
        }

        body {
            min-height: 100vh;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, .07);
            border: 1px solid #edf0f5;
        }

        .brand-icon {
            width: 55px;
            height: 55px;
            border-radius: 15px;
            background: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 27px;
            margin: 0 auto 15px;
        }

        .brand-name {
            color: var(--primary);
            font-weight: 800;
            letter-spacing: 2px;
            font-size: 25px;
        }

        .form-control {
            padding: 12px 14px;
            border-radius: 9px;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(23, 37, 84, .1);
        }

        .btn-login {
            background: var(--primary);
            color: white;
            padding: 12px;
            border-radius: 9px;
            font-weight: 600;
            border: none;
        }

        .btn-login:hover {
            background: #263b75;
            color: white;
        }

        .input-group-text {
            background: white;
            border-radius: 9px 0 0 9px;
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="text-center mb-4">

            <div class="brand-icon">
                <i class="bi bi-p-square-fill"></i>
            </div>

            <div class="brand-name">SLOTIN</div>

            <p class="text-muted small mt-2 mb-0">
                Smart Parking Monitoring System
            </p>

        </div>

        <div class="mb-4">
            <h5 class="fw-bold text-center mb-1">Selamat Datang</h5>

            <p class="text-muted small text-center">
                Silakan login untuk mengakses sistem SLOTIN.
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success small">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">

            @csrf

            <div class="mb-3">

                <label for="email" class="form-label fw-semibold small">
                    Email
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>

                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control"
                           placeholder="Masukkan email"
                           value="{{ old('email') }}"
                           autocomplete="username"
                           required
                           autofocus>
                </div>

            </div>

            <div class="mb-4">

                <label for="password" class="form-label fw-semibold small">
                    Password
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>

                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           required>

                    <button class="btn btn-outline-secondary"
                            type="button"
                            id="togglePassword">
                        <i class="bi bi-eye" id="passwordIcon"></i>
                    </button>
                </div>

            </div>

            <button type="submit" class="btn btn-login w-100">
                Login
                <i class="bi bi-arrow-right ms-1"></i>
            </button>

        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                SLOTIN Smart Parking &copy; {{ date('Y') }}
            </small>
        </div>

    </div>

</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';

        passwordIcon.className = isPassword
            ? 'bi bi-eye-slash'
            : 'bi bi-eye';
    });
</script>

</body>
</html>
