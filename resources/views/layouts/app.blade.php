<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="geist_deef94d5-module__Sms4YG__variable geist_mono_1bf8cbf6-module__FlyLvG__variable roboto_mono_a9b0d8df-module__byQ0EG__variable antialiased">
    <div id="termly-code-snippet-support" dir="ltr" class="hidden">
        <div
            style="--termly-theme-background-color: #f3f2ef; --termly-theme-button-background-color: #343d3f; --termly-theme-button-text-color: #ffffff; --termly-theme-color: #343d3f; --termly-theme-font-family: Roboto, &quot;Open Sans&quot;, Arial, Helvetica; --termly-theme-font-size: 12px; --termly-z-index: var(--termly-override-z-index, 999999);">
            <div>
                <div aria-label="Cookie Consent Prompt"
                    class="termly-styles-root-b0aebb termly-styles-compact-af2a7f termly-styles-termly-banner-e1ed59 termly-styles-bottom-d26761 t-consentPrompt"
                    role="alertdialog"
                    style="font-family: Roboto, &quot;Open Sans&quot;, Arial, Helvetica; font-size: 12px; color: rgb(52, 61, 63); background: rgb(243, 242, 239);">
                    <div class="termly-styles-main-bf5ef8">
                        <div class="termly-styles-content-fcefe4">
                            <div class="termly-styles-message-e9e76f termly-styles-message-d6c726">We use essential
                                cookies to make our site work. With your consent, we may also use non-essential cookies
                                to improve user experience and analyze website traffic. By clicking “Accept,” you agree
                                to our website's cookie use as described in our <span class="termly-styles-root-d5f974"
                                    role="button" tabindex="0" data-testid="cookie-policy-link">Cookie Policy</span>.
                                You can change your cookie settings at any time by clicking “<span
                                    class="termly-styles-root-d5f974" role="button" tabindex="0"
                                    data-testid="preferences-link">Preferences</span>.”</div>
                            <div class="termly-styles-buttons-bb7ad2 termly-styles-compact-af2a7f"
                                style="background: rgb(243, 242, 239);"><button
                                    class="termly-styles-module-root-aecb0e termly-styles-module-primary-c223ae termly-styles-module-outline-fc7224 termly-styles-button-a4543c termly-styles-compact-af2a7f t-preference-button"
                                    style="background: transparent; border-color: rgb(52, 61, 63); color: rgb(52, 61, 63); font-size: 12px; font-family: Roboto, &quot;Open Sans&quot;, Arial, Helvetica; font-weight: bold;">Preferences</button><button
                                    data-tid="banner-decline" data-tracking="cookieDeclined"
                                    class="termly-styles-module-root-aecb0e termly-styles-module-primary-c223ae termly-styles-module-solid-aab01d termly-styles-button-a4543c termly-styles-compact-af2a7f t-declineButton"
                                    style="background: rgb(52, 61, 63); border-color: rgb(52, 61, 63); color: rgb(255, 255, 255); font-size: 12px; font-family: Roboto, &quot;Open Sans&quot;, Arial, Helvetica; font-weight: bold;">Decline</button><button
                                    data-tid="banner-accept"
                                    class="termly-styles-module-root-aecb0e termly-styles-module-primary-c223ae termly-styles-module-solid-aab01d termly-styles-button-a4543c termly-styles-compact-af2a7f t-acceptAllButton"
                                    style="background: rgb(52, 61, 63); border-color: rgb(52, 61, 63); color: rgb(255, 255, 255); font-size: 12px; font-family: Roboto, &quot;Open Sans&quot;, Arial, Helvetica; font-weight: bold;">Accept</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <livewire:header />
        {{ $slot }}
        <livewire:footer />

        @livewireScripts
    </body>

</html>
