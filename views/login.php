<?php
// views/login.php
$pageTitle  = 'Sign in';
$pageScript = '/billing_hospital/assets/js/login.js';
require __DIR__ . '/partials/header.php';
?>

<!-- NO <body> tag here — header.php already opened it with data-base-url -->

<div class="min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h1 class="mt-5 text-2xl font-bold text-slate-900">Billing Hospital</h1>
            <p class="mt-1 text-sm text-slate-500">Sign in to access your dashboard</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">

            <div id="alert" class="hidden mb-5 rounded-lg px-4 py-3 text-sm border" role="alert"></div>

            <form id="loginForm" method="POST" action="/billing_hospital/api/login.php" novalidate class="space-y-5">

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-slate-900 placeholder-slate-400
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="e.g. admin@gmail.com">
                    <p class="mt-1.5 text-xs text-red-600 hidden" data-error-for="email"></p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" autocomplete="current-password" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 pr-11 text-slate-900 placeholder-slate-400
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="••••••••">
                        <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs text-red-600 hidden" data-error-for="password"></p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600 select-none">
                        <input type="checkbox" id="remember" name="remember"
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        Remember me
                    </label>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">Forgot password?</a>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5
                               text-sm font-semibold text-white shadow-sm hover:bg-blue-700
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                               disabled:opacity-60 disabled:cursor-not-allowed transition">
                    <svg id="spinner" class="hidden animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span id="submitLabel">Sign in</span>
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> Billing Hospital. All rights reserved.
        </p>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>