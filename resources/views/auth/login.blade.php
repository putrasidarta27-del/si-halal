<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk • SI-HALAL LPH UINSA</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-green: #103F35;
            --accent-gold: #D3AF61;
            --bg-light: #F5F8F7;
            --text-dark: #1B3529;
            --text-muted: #6A7C70;
            --border-light: #E4EAE6;
            --border-input: #DCE5DE;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* Two Column Container */
        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left Column: Identitas LPH UINSA */
        .identity-panel {
            width: 640px;
            background-color: var(--primary-green);
            color: #FFFFFF;
            padding: 64px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            flex-shrink: 0;
        }

        .brand-box {
            display: flex;
            align-items: flex-start;
            gap: 17px;
        }

        .gold-accent-vertical {
            width: 3px;
            height: 53px;
            background-color: var(--accent-gold);
            border-radius: 2px;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 700;
            line-height: 42px;
            color: #FFFFFF;
        }

        .brand-subtitle {
            font-size: 12px;
            font-weight: 500;
            line-height: 18px;
            color: #BDD0C8;
        }

        .hero-box {
            margin: auto 0;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .gold-accent-horizontal {
            width: 44px;
            height: 3px;
            background-color: var(--accent-gold);
            border-radius: 1px;
            margin-bottom: 26px;
        }

        .hero-eyebrow {
            font-size: 11px;
            font-weight: 600;
            line-height: 18px;
            letter-spacing: 0.1em;
            color: var(--accent-gold);
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .hero-headline {
            font-size: 39px;
            font-weight: 600;
            line-height: 53px;
            color: #FFFFFF;
            margin-bottom: 24px;
        }

        .hero-tagline {
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            color: #BDD0C8;
        }

        .footer-box {
            border-top: 1px solid #31594C;
            padding-top: 22px;
        }

        .footer-primary {
            font-size: 12px;
            font-weight: 500;
            line-height: 18px;
            color: #BDD0C8;
        }

        .footer-secondary {
            font-size: 11px;
            font-weight: 400;
            line-height: 17px;
            color: #9BB8A9;
            margin-top: 4px;
        }

        /* Right Column: Login Form */
        .form-panel {
            flex: 1;
            background-color: var(--bg-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
            position: relative;
        }

        .login-card {
            width: 100%;
            max-width: 480px;
            background-color: #FFFFFF;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .card-gold-accent {
            width: 28px;
            height: 3px;
            background-color: var(--accent-gold);
            border-radius: 1px;
            margin-bottom: 13px;
        }

        .card-title {
            font-size: 28px;
            font-weight: 600;
            line-height: 39px;
            color: #1B3529;
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 13px;
            font-weight: 400;
            line-height: 20px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            line-height: 20px;
            color: #334D3B;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input {
            width: 100%;
            height: 48px;
            border: 1px solid var(--border-input);
            border-radius: 8px;
            padding: 0 16px;
            font-size: 13px;
            font-family: inherit;
            color: #203B2E;
            background-color: #FFFFFF;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input::placeholder {
            color: #7B897E;
        }

        .form-input:focus {
            border-color: #103F35;
            box-shadow: 0 0 0 3px rgba(16, 63, 53, 0.1);
        }

        .input-password {
            padding-right: 48px;
        }

        .btn-toggle-password {
            position: absolute;
            right: 4px;
            top: 4px;
            width: 40px;
            height: 40px;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .btn-toggle-password:hover {
            background-color: #F5F8F7;
        }

        .alert-danger {
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 18px;
            margin-bottom: 20px;
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background-color: var(--primary-green);
            border: none;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: background-color 0.15s, transform 0.05s;
            margin-top: 8px;
            font-family: inherit;
        }

        .btn-submit:hover {
            background-color: #0c332b;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .card-divider {
            height: 1px;
            background-color: var(--border-light);
            margin: 24px 0 20px 0;
        }

        .info-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .info-icon {
            width: 17px;
            height: 17px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .info-title {
            font-size: 11px;
            font-weight: 500;
            line-height: 18px;
            color: #526B58;
        }

        .info-desc {
            font-size: 11px;
            font-weight: 400;
            line-height: 18px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .panel-copyright {
            margin-top: 32px;
            font-size: 11px;
            font-weight: 400;
            line-height: 17px;
            color: #7C8B80;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .identity-panel {
                width: 440px;
                padding: 40px;
            }
            .hero-headline {
                font-size: 30px;
                line-height: 42px;
            }
        }

        @media (max-width: 820px) {
            .login-container {
                flex-direction: column;
            }
            .identity-panel {
                width: 100%;
                padding: 40px 24px;
            }
            .hero-box {
                padding: 24px 0;
            }
            .form-panel {
                padding: 32px 16px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Left Panel: Identitas LPH UINSA -->
        <div class="identity-panel">
            <!-- Brand Header -->
            <div class="brand-box">
                <div class="gold-accent-vertical"></div>
                <div>
                    <div class="brand-title">LPH UINSA</div>
                    <div class="brand-subtitle">UIN Sunan Ampel Surabaya</div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="hero-box">
                <div class="gold-accent-horizontal"></div>
                <div class="hero-eyebrow">LEMBAGA PEMERIKSA HALAL</div>
                <h1 class="hero-headline">Sistem Informasi<br>Pendampingan dan<br>Sertifikasi Halal</h1>
                <p class="hero-tagline">UIN Sunan Ampel Surabaya</p>
            </div>

            <!-- Footer Identitas -->
            <div class="footer-box">
                <div class="footer-primary">Sistem internal LPH UINSA</div>
                <div class="footer-secondary">Pendampingan dan pemeriksaan halal</div>
            </div>
        </div>

        <!-- Right Panel: Form Masuk -->
        <div class="form-panel">
            <div class="login-card">
                <div class="card-gold-accent"></div>
                <h2 class="card-title">Selamat Datang</h2>
                <p class="card-subtitle">Masuk menggunakan akun yang terdaftar.</p>

                @if (isset($errors) && $errors->any())
                    <div class="alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Username / Email Field -->
                    <div class="form-group">
                        <label for="login" class="form-label">Username atau email</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                class="form-input" 
                                placeholder="Masukkan username atau email" 
                                value="{{ old('login') }}" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input input-password" 
                                placeholder="Masukkan password" 
                                required
                            >
                            <button type="button" class="btn-toggle-password" id="togglePasswordBtn" title="Tampilkan/Sembunyikan password">
                                <img src="{{ asset('images/eye.svg') }}" alt="Toggle" width="20" height="20">
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit">
                        <span>Masuk</span>
                        <img src="{{ asset('images/arrow-right.svg') }}" alt="Masuk" width="17" height="17">
                    </button>

                    <!-- Divider -->
                    <div class="card-divider"></div>

                    <!-- Info Box -->
                    <div class="info-box">
                        <img src="{{ asset('images/info.svg') }}" alt="Informasi" class="info-icon">
                        <div>
                            <div class="info-title">Sistem ini digunakan oleh Admin LPH dan Auditor.</div>
                            <div class="info-desc">Setelah berhasil masuk, Anda akan diarahkan ke dashboard sesuai peran akun.</div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="panel-copyright">
                © 2026 LPH UIN Sunan Ampel Surabaya
            </div>
        </div>
    </div>

    <!-- Toggle Password Script -->
    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
            });
        }
    </script>
</body>
</html>
