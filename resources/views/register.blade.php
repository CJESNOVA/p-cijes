<x-base-layout title="Inscription">
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
                src="{{ asset('images/illustrations/dashboard-meet.svg') }}" alt="image" />
            <img class="w-full" x-show="$store.global.isDarkModeEnabled"
                src="{{ asset('images/illustrations/dashboard-meet-dark.svg') }}" alt="image" />
        </div>
    </div>
    <main class="flex w-full flex-col items-center bg-white dark:bg-navy-700 lg:max-w-md">
        <div class="flex w-full max-w-sm grow flex-col justify-center p-5">
            <div class="text-center">
                <img class="mx-auto size-16 lg:hidden " src="{{ asset('images/app-logo.png') }}" alt="logo" />
                <div class="mt-4">
                    <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100">
                        Bienvenue sur {{ config('app.name') }}
                    </h2>
                    <p class="text-slate-400 dark:text-navy-300">
                        Veuillez vous inscrire pour continuer
                    </p>
                </div>
            </div>

            <!--
            <div class="mt-10 flex space-x-4">
                <button
                    class="btn w-full space-x-3 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                    <img class="size-5.5 " src="{{ asset('images/logos/google.svg') }}" alt="logo" />
                    <span>Google</span>
                </button>
                <button
                    class="btn w-full space-x-3 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                    <img class="size-5.5 " src="{{ asset('images/logos/github.svg') }}" alt="logo" />
                    <span>Github</span>
                </button>
            </div>
            <div class="my-7 flex items-center space-x-3">
                <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
                <p class="text-tiny-plus uppercase">or sign up with email</p>

                <div class="h-px flex-1 bg-slate-200 dark:bg-navy-500"></div>
            </div> -->
            
            <form class="mt-4" action="{{ route('register') }}" method="post">
                @method('POST') @csrf
                <div class="space-y-4">
                    <div>
                        <label class="relative flex">
                            <input
                                class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                                placeholder="Nom complet" type="text" name="name" value="{{ old('name') }}" />
                            <span
                                class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                        </label>
                        @error('name')
                            <span class="text-tiny-plus text-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="relative flex">
                            <input
                                class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                                placeholder="Adresse e-mail" type="text" name="email" value="{{ old('email') }}" />
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
                    <div>
                        <label class="relative flex">
                            <input
                                class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 pr-20 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                                placeholder="Mot de passe" type="password" name="password" id="password" />
                            <span
                                class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="size-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </label>
                        @error('password')
                            <span class="text-tiny-plus text-error">{{ $message }}</span>
                        @enderror
                        
                        <!-- Instructions mot de passe (initialement cachées) -->
                        <div id="passwordInstructions" class="mt-2 p-3 bg-slate-50 dark:bg-navy-800 rounded-lg border border-slate-200 dark:border-navy-600 hidden">
                            <p class="text-xs font-medium text-slate-700 dark:text-navy-200 mb-2">Votre mot de passe doit contenir :</p>
                            <ul class="space-y-1 text-xs text-slate-600 dark:text-navy-300">
                                <li class="flex items-center gap-2">
                                    <span id="lengthCheck" class="text-red-500">✗</span>
                                    <span>Au moins 8 caractères</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span id="lowercaseCheck" class="text-red-500">✗</span>
                                    <span>Au moins une lettre minuscule (a-z)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span id="uppercaseCheck" class="text-red-500">✗</span>
                                    <span>Au moins une lettre majuscule (A-Z)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span id="numberCheck" class="text-red-500">✗</span>
                                    <span>Au moins un chiffre (0-9)</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span id="specialCheck" class="text-red-500">✗</span>
                                    <span>Au moins un caractère spécial (@$!%*?&)</span>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Indicateur de force (initialement caché) -->
                        <div id="strengthIndicator" class="mt-2 hidden">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-600 dark:text-navy-300">Force du mot de passe</span>
                                <span id="strengthText" class="text-xs font-medium text-slate-500 dark:text-navy-400">Faible</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-navy-600 rounded-full h-2">
                                <div id="strengthBar" class="h-2 rounded-full transition-all duration-300 bg-red-500" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="relative flex">
                            <input
                                class="form-input peer w-full rounded-lg bg-slate-150 px-3 py-2 pl-9 pr-20 ring-primary/50 placeholder:text-slate-400 hover:bg-slate-200 focus:ring-3 dark:bg-navy-900/90 dark:ring-accent/50 dark:placeholder:text-navy-300 dark:hover:bg-navy-900 dark:focus:bg-navy-900 border border-slate-300 focus:border-primary hover:border-primary"
                                placeholder="Confirmer le mot de passe" type="password" name="password_confirmation" id="password_confirmation" />
                            <span
                                class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:text-navy-300 dark:hover:text-navy-100">
                                <svg id="confirmEyeIcon" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="confirmEyeOffIcon" xmlns="http://www.w3.org/2000/svg" class="size-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </label>
                        @error('password_confirmation')
                            <span class="text-tiny-plus text-error">{{ $message }}</span>
                        @enderror
                        
                        <!-- Indicateur de correspondance -->
                        <div id="matchIndicator" class="mt-2 hidden">
                            <span id="matchText" class="text-xs flex items-center gap-2">
                                <span id="matchIcon">✗</span>
                                <span>Les mots de passe ne correspondent pas</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between space-x-2">
                    <label class="inline-flex items-center space-x-2">
                        <input
                            class="form-checkbox is-outline size-5 rounded-sm border-slate-400/70 bg-slate-100 before:bg-primary checked:border-primary hover:border-primary focus:border-primary dark:border-navy-500 dark:bg-navy-900 dark:before:bg-accent dark:checked:border-accent dark:hover:border-accent dark:focus:border-accent"
                            type="checkbox" name="condition_general" id="condition_general" />
                        <span class="">Je comprends et j'accepte la <a style="color: #4FBE96 !important;" href="{{ env('APP_URL2') }}page/6-Politique-de-Confidentialité.html" target="_blank">Politique de Confidentialité</a> et les <a style="color: #4FBE96 !important;" href="{{ env('APP_URL2') }}page/7-Conditions-Générales-d-Utilisation.html" target="_blank">Conditions Générales d'Utilisation</a> </span>
                    </label>
                    
                </div>

                <button type="submit" style="background-color: #4FBE96 !important;"
                    class="btn mt-10 h-10 w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    S'incrire
                </button>
            </form>
            <div class="mt-4 text-center text-xs-plus">
                <p class="line-clamp-1">
                    <span>Vous avez déjà un compte ? </span>
                    <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                        href="{{ route('loginView') }}">Se connecter</a>
                </p>
            </div>
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
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const togglePasswordBtn = document.getElementById('togglePassword');
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');
    const confirmEyeIcon = document.getElementById('confirmEyeIcon');
    const confirmEyeOffIcon = document.getElementById('confirmEyeOffIcon');
    
    // Éléments de validation
    const lengthCheck = document.getElementById('lengthCheck');
    const lowercaseCheck = document.getElementById('lowercaseCheck');
    const uppercaseCheck = document.getElementById('uppercaseCheck');
    const numberCheck = document.getElementById('numberCheck');
    const specialCheck = document.getElementById('specialCheck');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const matchIndicator = document.getElementById('matchIndicator');
    const matchText = document.getElementById('matchText');
    const matchIcon = document.getElementById('matchIcon');
    
    // Toggle visibilité mot de passe
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        eyeIcon.classList.toggle('hidden');
        eyeOffIcon.classList.toggle('hidden');
    });
    
    toggleConfirmPasswordBtn.addEventListener('click', function() {
        const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
        confirmPasswordInput.type = type;
        confirmEyeIcon.classList.toggle('hidden');
        confirmEyeOffIcon.classList.toggle('hidden');
    });
    
    // Validation en temps réel
    function validatePassword(password) {
        const checks = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[@$!%*?&]/.test(password)
        };
        
        // Mise à jour des indicateurs
        updateCheck(lengthCheck, checks.length);
        updateCheck(lowercaseCheck, checks.lowercase);
        updateCheck(uppercaseCheck, checks.uppercase);
        updateCheck(numberCheck, checks.number);
        updateCheck(specialCheck, checks.special);
        
        return checks;
    }
    
    function updateCheck(element, isValid) {
        if (isValid) {
            element.textContent = '✓';
            element.classList.remove('text-red-500');
            element.classList.add('text-green-500');
        } else {
            element.textContent = '✗';
            element.classList.remove('text-green-500');
            element.classList.add('text-red-500');
        }
    }
    
    // Calcul de la force du mot de passe
    function calculateStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength += 20;
        if (password.length >= 12) strength += 10;
        if (/[a-z]/.test(password)) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[@$!%*?&]/.test(password)) strength += 15;
        
        return strength;
    }
    
    function updateStrengthBar(strength) {
        strengthBar.style.width = strength + '%';
        
        if (strength < 40) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            strengthText.textContent = 'Faible';
            strengthText.className = 'text-xs font-medium text-red-500 dark:text-red-400';
        } else if (strength < 70) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
            strengthText.textContent = 'Moyen';
            strengthText.className = 'text-xs font-medium text-yellow-500 dark:text-yellow-400';
        } else {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            strengthText.textContent = 'Fort';
            strengthText.className = 'text-xs font-medium text-green-500 dark:text-green-400';
        }
    }
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword.length > 0) {
            matchIndicator.classList.remove('hidden');
            
            if (password === confirmPassword) {
                matchIcon.textContent = '✓';
                matchIcon.classList.remove('text-red-500');
                matchIcon.classList.add('text-green-500');
                matchText.classList.remove('text-red-500');
                matchText.classList.add('text-green-500');
                matchText.innerHTML = '<span class="text-green-500">✓</span><span>Les mots de passe correspondent</span>';
            } else {
                matchIcon.textContent = '✗';
                matchIcon.classList.remove('text-green-500');
                matchIcon.classList.add('text-red-500');
                matchText.classList.remove('text-green-500');
                matchText.classList.add('text-red-500');
                matchText.innerHTML = '<span class="text-red-500">✗</span><span>Les mots de passe ne correspondent pas</span>';
            }
        } else {
            matchIndicator.classList.add('hidden');
        }
    }
    
    // Écouteurs d'événements
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Afficher les instructions et l'indicateur de force dès que l'utilisateur commence à taper
        if (password.length > 0) {
            document.getElementById('passwordInstructions').classList.remove('hidden');
            document.getElementById('strengthIndicator').classList.remove('hidden');
        } else {
            document.getElementById('passwordInstructions').classList.add('hidden');
            document.getElementById('strengthIndicator').classList.add('hidden');
        }
        
        validatePassword(password);
        updateStrengthBar(calculateStrength(password));
        checkPasswordMatch();
    });
    
    passwordInput.addEventListener('focus', function() {
        // Afficher les instructions dès que le champ est focusé
        if (this.value.length > 0) {
            document.getElementById('passwordInstructions').classList.remove('hidden');
            document.getElementById('strengthIndicator').classList.remove('hidden');
        }
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });
});
</script>
