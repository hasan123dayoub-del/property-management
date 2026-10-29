<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RealEstateSys - Login</title>

<link rel="icon" href="{{ asset('logo.png') }}">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
html, body {
  height: 100%;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  background: #f8fafc;
}

/* صورة مُستنسخة أثناء التمدد */
.cloned-image {
  position: fixed;
  z-index: 1000;
  object-fit: cover;
  pointer-events: none;
  transition: transform .6s cubic-bezier(.2,.9,.3,1), opacity .35s ease;
  filter: brightness(0.6) drop-shadow(0 0 2px rgba(0,0,0,0.25));
}

/* لودر */
#loader {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  z-index: 1100;
  color: white;
  backdrop-filter: blur(6px);
}

#loader.show {
  display: flex;
}

#loader::after {
  content: '';
  position: absolute;
  inset: 0;
  background-color: rgba(0,0,0,0.35);
  z-index: 10;
}

#loader .loader-text {
  position: relative;
  z-index: 20;
  font-size: 1.6rem;
  font-weight: 700;
  text-align: center;
  text-shadow: 0 2px 6px rgba(0,0,0,0.5);
}

.spinner {
  position: relative;
  z-index: 20;
  width: 56px;
  height: 56px;
  border: 6px solid rgba(255, 255, 255, 0.25);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* subtle card backdrop blur for the form */
.form-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.85), rgba(255,255,255,0.8));
  backdrop-filter: blur(6px);
  box-shadow: 0 10px 30px rgba(15,23,42,0.06);
}

/* password toggle button inside input */
.pwd-toggle {
  -webkit-tap-highlight-color: transparent;
}
</style>
</head>

<body class="bg-gray-50">

<div class="min-h-screen grid grid-cols-1 md:grid-cols-2">

  <!-- FORM -->
  <div class="flex items-center justify-center px-6 md:order-2">
    <div class="w-full max-w-md">

      <div class="flex flex-col items-center justify-center gap-3 mb-8">
        <img src="{{ asset('logo.png') }}" class="h-12 w-12 rounded">
        <span class="text-indigo-600 text-3xl font-extrabold tracking-tight">RealEstate<span class="text-gray-400 font-medium">Sys</span></span>
        <p class="text-gray-500 text-sm mt-1">Manage properties, bookings & more</p>
      </div>

      <div class="form-card rounded-2xl p-6 md:p-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Welcome back</h3>
        <p class="text-xs text-gray-500 mb-6">Sign in to continue to your dashboard</p>

        @if ($errors->any())
          <div class="mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-700" role="alert">
            {{ $errors->first() }}
          </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
          @csrf

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="emailInput" name="email" value="{{ old('email') }}" required
              class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-300 shadow-sm"
              placeholder="you@example.com" autocomplete="username">
          </div>

          <div class="mb-2 relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>

            <div class="relative">
              <input id="passwordInput" type="password" name="password" required
                class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-300 shadow-sm"
                placeholder="Enter your password" autocomplete="current-password" aria-describedby="pwd-toggle-desc">

              <!-- eye toggle -->
              <button type="button" id="pwdToggle" aria-label="Toggle password visibility" title="Show password"
                      class="pwd-toggle absolute inset-y-0 right-2 flex items-center px-2 text-gray-500 hover:text-indigo-600 focus:outline-none">
                <!-- eye icon (closed) -->
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>

                <!-- eye icon (closed) shown by default -->
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.558M3 3l18 18"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.88 9.88A3 3 0 0014.12 14.12"/>
                </svg>
              </button>
            </div>

            <p id="pwd-toggle-desc" class="sr-only">Toggle password visibility</p>
          </div>

          <div class="flex items-center justify-between mt-4">
            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">Forgot your password?</a>
          </div>

          <button id="loginBtn"
            class="w-full mt-6 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white py-3 rounded-xl font-semibold shadow-md focus:outline-none focus:ring-4 focus:ring-indigo-200">
            Login
          </button>
        </form>

        @if (config('app.demo_mode'))
          {{-- Public demo: one click fills the form with a demo account. --}}
          <div class="mt-6 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/60 p-4">
            <p class="text-sm font-semibold text-gray-700">Demo accounts <span class="font-normal text-gray-500">(password: password)</span></p>
            <div class="mt-3 flex flex-wrap gap-2">
              <button type="button" data-demo-email="admin@demo.com"
                class="demo-account rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Admin</button>
              <button type="button" data-demo-email="employee@demo.com"
                class="demo-account rounded-lg border border-indigo-200 bg-white px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100">Employee</button>
            </div>
            <p class="mt-3 text-xs text-gray-500">Customers use the REST API (<code>POST /api/login</code> with customer@demo.com). Free hosting: the first load can take up to a minute while the server wakes up.</p>
          </div>
        @endif
      </div>

      <p class="text-center text-xs text-gray-400 mt-4">© {{ date('Y') }} RealEstateSys</p>
    </div>
  </div>

  <!-- IMAGE -->
  <div id="imageCol" class="relative hidden md:block overflow-hidden">
    <img id="sideImg" src="{{ asset('Estate.webp') }}" class="absolute inset-0 w-full h-full object-cover brightness-60">
    <div class="absolute inset-0 flex items-center justify-center text-white text-4xl md:text-5xl font-extrabold z-10 px-6 text-center">
      Login To Manage Your Properties
    </div>
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm z-0"></div>
  </div>

</div>

<!-- LOADER -->
<div id="loader">
  <div class="flex flex-col items-center gap-6 relative z-20">
    <div class="spinner"></div>
    <div class="loader-text">
      Preparing your dashboard…<br>
      Please wait a moment
    </div>
  </div>
</div>

<script>
(function(){
  // password toggle
  const pwdInput = document.getElementById('passwordInput');
  const pwdToggle = document.getElementById('pwdToggle');
  const eyeOpen = document.getElementById('eyeOpen');
  const eyeClosed = document.getElementById('eyeClosed');

  pwdToggle.addEventListener('click', function(e){
    e.preventDefault();
    const isPwd = pwdInput.getAttribute('type') === 'password';
    if (isPwd) {
      pwdInput.setAttribute('type', 'text');
      eyeOpen.classList.remove('hidden');
      eyeClosed.classList.add('hidden');
      pwdToggle.setAttribute('title','Hide password');
    } else {
      pwdInput.setAttribute('type', 'password');
      eyeOpen.classList.add('hidden');
      eyeClosed.classList.remove('hidden');
      pwdToggle.setAttribute('title','Show password');
    }
    // keep focus on input after toggle
    pwdInput.focus();
  });

  // existing submit + animation logic
  const form   = document.getElementById('loginForm');
  const btn    = document.getElementById('loginBtn');
  const img    = document.getElementById('sideImg');
  const imgCol = document.getElementById('imageCol');
  const loader = document.getElementById('loader');

  let submitted = false;

  form.addEventListener('submit', function(e){
    if (submitted) return;
    e.preventDefault();
    submitted = true;

    btn.disabled = true;
    btn.classList.add('opacity-80', 'cursor-not-allowed');
    document.body.classList.add('no-scroll');
    document.activeElement?.blur();

    if (!img || window.innerWidth < 768) {
      loader.style.backgroundImage = 'url("{{ asset('Estate.webp') }}")';
      loader.classList.add('show');
      form.submit();
      return;
    }

    const r = img.getBoundingClientRect();
    const clone = img.cloneNode(true);
    clone.className = 'cloned-image';

    Object.assign(clone.style,{
      left: r.left + 'px',
      top: r.top + 'px',
      width: r.width + 'px',
      height: r.height + 'px'
    });

    imgCol.style.visibility = 'hidden';
    document.body.appendChild(clone);

    const tx = window.innerWidth / 2 - (r.left + r.width / 2);
    const ty = window.innerHeight / 2 - (r.top + r.height / 2);
    const scale = Math.max(
      window.innerWidth / r.width,
      window.innerHeight / r.height
    ) * 1.05;

    requestAnimationFrame(() => {
      clone.style.transform = `translate3d(${tx}px, ${ty}px, 0) scale(${scale})`;
    });

    clone.addEventListener('transitionend', () => {
      loader.style.backgroundImage = `url('${img.src}')`;
      loader.classList.add('show');
      clone.style.opacity = '0';
      setTimeout(() => form.submit(), 120);
    }, { once: true });
  });

  // demo accounts: fill the form with one click
  document.querySelectorAll('.demo-account').forEach(function (button) {
    button.addEventListener('click', function () {
      document.getElementById('emailInput').value = button.dataset.demoEmail;
      pwdInput.value = 'password';
    });
  });

  // accessibility: toggle via Enter when focused
  pwdToggle.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault(); pwdToggle.click();
    }
  });
})();
</script>

</body>
</html>
