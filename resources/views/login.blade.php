<x-base-layout title="Connexion">
    <div class="fixed top-0 hidden p-6 lg:block lg:px-12">
        <a href="#" class="flex items-center space-x-2">
            <img class="" src="{{ asset('images/app-logo2.png') }}" alt="logo" />
            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                <!-- {{ config('app.name') }} -->
            </p>
        </a>
    </div>
    <div class="hidden w-full place-items-center lg:grid">
        <div class="w-full max-w-lg p-6">
            <img class="w-full" x-show="!$store.global.isDarkModeEnabled"
                src="{{ asset('images/illustrations/dashboard-check.svg') }}" alt="image" />
            <img class="w-full" x-show="$store.global.isDarkModeEnabled"
                src="{{ asset('images/illustrations/dashboard-check-dark.svg') }}" alt="image" />
        </div>
    </div>
    <main class="flex w-full flex-col items-center bg-white dark:bg-navy-700 lg:max-w-md">
        <div class="flex w-full max-w-sm grow flex-col justify-center p-5">
            <div class="text-center">
                <img class="mx-auto size-16 lg:hidden" src="{{ asset('images/app-logo.png') }}" alt="logo" />
                <div class="mt-4">
                    <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100">
                        Bienvenue sur la plateforme e-CIJES
                    </h2>
                    <p class="text-slate-400 dark:text-navy-300">
                        Connectez vous ici 
                    </p>
                </div>
            </div>
            <form class="mt-16" action="{{ route('login') }}" method="post">
                @method('POST') @csrf
                <div>
                    <label class="relative flex">
                        <input
                            class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                            placeholder="Nom d'utilisateur ou e-mail" type="text" name="email"
                            value="{{ old('email') ?? '' }}"  autofocus required />
                        <span
                            class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                    </label>
                    @error('email')
                        <span class="text-tiny-plus text-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mt-4">
                    <label class="relative flex">
                        <input
                            class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 pr-20 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                            placeholder="Mot de passe" type="password" name="password" id="loginPassword"
                            value="{{ old('password') ?? '' }}" required />
                        <span
                            class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <button type="button" id="toggleLoginPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100">
                            <svg id="loginEyeIcon" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="loginEyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="size-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </label>
                    @error('password')
                        <span class="text-tiny-plus text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-4 flex items-center justify-between space-x-2">
                    <label class="inline-flex items-center space-x-2">
                        <input
                            class="form-checkbox is-outline size-5 rounded-sm border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-500 dark:bg-navy-900 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent"
                            type="checkbox" name="remember" id="remember" />
                        <span class="line-clamp-1">Se souvenir de moi</span>
                    </label>
                    
                    <a href="{{ route('forgotPasswordView') }}"
                        class="text-xs transition-colors line-clamp-1 hover:text-primary focus:text-primary dark:text-navy-300 dark:hover:text-accent dark:focus:text-accent" style="color: #4FBE96;">Mot de passe oublié ?</a>
                </div>
                <button type="submit" style="background-color: #4FBE96 !important;"
                    class="btn mt-10 h-10 w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    Connexion
                </button>
                <div class="mt-4 text-center text-xs-plus">
                    <p class="line-clamp-1">
                        <span>Vous n'avez pas de compte ?</span>

                        <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                            href="{{ route('registerView') }}">Créer un nouveau compte</a>
                    </p>
                </div>
                <!--<div class="my-7 flex items-center space-x-3">
                    <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                    <p>OR</p>
                    <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                </div>
                <div class="flex space-x-4">
                    <button
                        class="btn w-full space-x-3 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <img class="size-5.5" src="{{ asset('images/logos/google.svg') }}" alt="logo" />
                        <span>Google</span>
                    </button>
                    <button
                        class="btn w-full space-x-3 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                        <img class="size-5.5" src="{{ asset('images/logos/github.svg') }}" alt="logo" />
                        <span>Github</span>
                    </button>
                </div>-->
            </form>
        </div>
        <div class="my-5 flex justify-center text-xs text-slate-400 dark:text-navy-300">
            <a href="{{ env('APP_URL2') }}page/6-Politique-de-Confidentialité.html" target="_blank">Politique de Confidentialité</a>
            <div class="mx-3 my-1 w-px bg-slate-200 dark:bg-navy-500"></div>
            <a href="{{ env('APP_URL2') }}page/7-Conditions-Générales-d-Utilisation.html" target="_blank">Conditions Générales d’Utilisation</a>
        </div>
    </main>
</x-base-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('loginPassword');
    const togglePasswordBtn = document.getElementById('toggleLoginPassword');
    const eyeIcon = document.getElementById('loginEyeIcon');
    const eyeOffIcon = document.getElementById('loginEyeOffIcon');
    
    // Toggle visibilité mot de passe
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        eyeIcon.classList.toggle('hidden');
        eyeOffIcon.classList.toggle('hidden');
    });
});
</script>
