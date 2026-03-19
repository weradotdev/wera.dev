<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<main class="flex min-h-screen flex-col pt-[72px]">
    <section class="relative w-full">
        <div class="mx-auto container px-6">
            <div class="relative">
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="left: calc(50% - 50vw);"></div>
                <div class="absolute top-0 bottom-0 w-px bg-foreground/15" style="right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                <div class="absolute h-px bg-foreground/15" style="bottom: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>

                {{-- Header --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="pt-20 lg:pt-24 pb-12 lg:pb-16 px-4 lg:px-8 flex items-center justify-between gap-8">
                        <div>
                            <div class="mb-4"><span class="font-roboto-mono text-sm tracking-wide scroll-mt-24 text-foreground/60">/ LEGAL</span></div>
                            <h1 class="text-3xl font-medium leading-tight tracking-tight text-foreground md:text-4xl lg:text-5xl mb-4">
                                Terms of Service
                            </h1>
                            <p class="text-base text-foreground/60 max-w-2xl">Last updated {{ date('F j, Y') }}</p>
                        </div>
                        <div class="hidden lg:block flex-shrink-0" style="width: 240px; height: 258px;">
                            <div class="pointer-events-none flex items-center justify-center"><canvas class="pointer-events-none" style="width: 240px; height: 258px;" width="240" height="264"></canvas></div>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="relative">
                    <div class="absolute h-px bg-foreground/15" style="top: 0px; left: calc(50% - 50vw); right: calc(50% - 50vw);"></div>
                </div>

                {{-- Content --}}
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-px bg-grid-border"></div>
                    <div class="absolute z-10 pointer-events-none top-0 left-0" style="width: 24px; height: 24px; margin-top: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none top-0 right-0" style="width: 24px; height: 24px; margin-top: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V24M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 left-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-left: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H24" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>
                    <div class="absolute z-10 pointer-events-none bottom-0 right-0" style="width: 24px; height: 24px; margin-bottom: -12px; margin-right: -12px;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 12V0M12 12H0" stroke="#74675A" stroke-width="1" fill="none"></path></svg></div>

                    <div class="grid lg:grid-cols-[240px_1fr] gap-0 divide-y lg:divide-y-0 lg:divide-x divide-foreground/10">

                        {{-- TOC sidebar --}}
                        <div class="px-4 lg:px-8 py-10 lg:py-14">
                            <nav class="sticky top-24 space-y-1">
                                <div class="font-roboto-mono text-xs tracking-wider uppercase text-foreground/50 mb-4">Contents</div>
                                @php
                                    $sections = [
                                        ['id' => 'acceptance', 'label' => '1. Acceptance'],
                                        ['id' => 'service', 'label' => '2. The Service'],
                                        ['id' => 'accounts', 'label' => '3. Accounts'],
                                        ['id' => 'acceptable-use', 'label' => '4. Acceptable Use'],
                                        ['id' => 'content', 'label' => '5. Your Content'],
                                        ['id' => 'payments', 'label' => '6. Payments'],
                                        ['id' => 'termination', 'label' => '7. Termination'],
                                        ['id' => 'liability', 'label' => '8. Liability'],
                                        ['id' => 'governing', 'label' => '9. Governing Law'],
                                        ['id' => 'changes', 'label' => '10. Changes'],
                                        ['id' => 'contact-us', 'label' => '11. Contact'],
                                    ];
                                @endphp
                                @foreach($sections as $section)
                                <a href="#{{ $section['id'] }}" class="block text-sm text-foreground/60 hover:text-foreground py-1 transition-colors">
                                    {{ $section['label'] }}
                                </a>
                                @endforeach
                            </nav>
                        </div>

                        {{-- Article body --}}
                        <div class="px-4 lg:px-8 py-10 lg:py-14">
                            <article class="prose prose-sm max-w-3xl text-foreground/80 [&_h2]:text-foreground [&_h2]:font-medium [&_h2]:text-xl [&_h2]:mt-10 [&_h2]:mb-4 [&_h3]:text-foreground [&_h3]:font-medium [&_h3]:mt-6 [&_h3]:mb-3 [&_p]:text-foreground/70 [&_p]:leading-relaxed [&_p]:mb-4 [&_ul]:text-foreground/70 [&_ul]:space-y-2 [&_li]:leading-relaxed [&_a]:underline [&_a]:decoration-eigenpal-green-light [&_a]:underline-offset-4 [&_a]:text-foreground">

                                <h2 id="acceptance">1. Acceptance of Terms</h2>
                                <p>By accessing or using Wera ("the Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, do not use the Service. These Terms apply to all users, including visitors, registered users, and workspace administrators.</p>
                                <p>These Terms constitute a legally binding agreement between you and Wera ("we", "us", or "our"). You represent that you have the authority to accept these Terms on behalf of yourself or the organisation you represent.</p>

                                <h2 id="service">2. The Service</h2>
                                <p>Wera is a project management platform that provides workspaces, kanban boards, task tracking, team collaboration tools, and related features. We reserve the right to modify, suspend, or discontinue any part of the Service at any time, with or without notice.</p>
                                <p>We do not guarantee that the Service will be available at all times. Scheduled and unscheduled maintenance may cause temporary unavailability. We will make reasonable efforts to notify users of planned downtime.</p>

                                <h2 id="accounts">3. Accounts</h2>
                                <p>To use the Service, you must create an account. You agree to provide accurate, complete, and current information when registering and to keep that information up to date. You are responsible for maintaining the confidentiality of your account credentials.</p>
                                <p>You are responsible for all activity that occurs under your account. Notify us immediately at <a href="mailto:hello@wera.dev">hello@wera.dev</a> if you suspect any unauthorised access to your account.</p>
                                <p>One person or legal entity may not maintain more than one free account. Accounts registered by bots or automated methods are not permitted.</p>

                                <h2 id="acceptable-use">4. Acceptable Use</h2>
                                <p>You agree not to use the Service to:</p>
                                <ul>
                                    <li>Violate any applicable law or regulation.</li>
                                    <li>Upload or transmit viruses, malware, or any other harmful code.</li>
                                    <li>Attempt to gain unauthorised access to any part of the Service or its infrastructure.</li>
                                    <li>Interfere with or disrupt the integrity or performance of the Service.</li>
                                    <li>Harvest or collect personally identifiable information about other users without their consent.</li>
                                    <li>Use the Service to send unsolicited communications (spam).</li>
                                    <li>Impersonate another person or entity.</li>
                                    <li>Resell, sublicense, or commercially exploit any part of the Service without our written consent.</li>
                                </ul>
                                <p>We reserve the right to suspend or terminate accounts that violate these rules without prior notice.</p>

                                <h2 id="content">5. Your Content</h2>
                                <p>You retain ownership of all content you create or upload to the Service ("Your Content"). By using the Service, you grant us a non-exclusive, worldwide, royalty-free licence to store, display, and process Your Content solely to provide and improve the Service.</p>
                                <p>You are solely responsible for Your Content and represent that you have all rights necessary to grant the above licence. You agree not to upload content that is unlawful, defamatory, obscene, or that infringes on any third-party intellectual property rights.</p>
                                <p>We do not sell Your Content to third parties. We do not use Your Content to train AI models without your explicit consent.</p>

                                <h2 id="payments">6. Payments and Billing</h2>
                                <p>Certain features of the Service may require a paid subscription. All prices are listed in USD and are exclusive of applicable taxes unless stated otherwise. Prices may change at any time, but we will give at least 30 days notice before changing prices for existing subscribers.</p>
                                <p>Subscriptions are billed in advance on a monthly or annual basis. If you cancel a subscription, you will retain access until the end of the billing period. We do not provide refunds for partial periods, except where required by law.</p>
                                <p>If payment fails, we reserve the right to downgrade or suspend your account until payment is resolved.</p>

                                <h2 id="termination">7. Termination</h2>
                                <p>You may delete your account at any time from your account settings. Upon deletion, your data will be removed in accordance with our <a href="{{ route('privacy') }}">Privacy Policy</a>.</p>
                                <p>We may suspend or terminate your account immediately, without prior notice or liability, if you breach these Terms or if we are required to do so by law. Upon termination, your right to use the Service ceases immediately.</p>

                                <h2 id="liability">8. Limitation of Liability</h2>
                                <p>To the maximum extent permitted by applicable law, Wera and its officers, directors, employees, and agents shall not be liable for any indirect, incidental, special, consequential, or punitive damages — including loss of profits, data, goodwill, or other intangible losses — arising from your use of or inability to use the Service.</p>
                                <p>The Service is provided "as is" and "as available" without warranties of any kind, either express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, or non-infringement.</p>
                                <p>Some jurisdictions do not allow the exclusion of certain warranties or limitation of liability, so some of the above exclusions may not apply to you.</p>

                                <h2 id="governing">9. Governing Law</h2>
                                <p>These Terms shall be governed by and construed in accordance with the laws of the applicable jurisdiction, without regard to conflict of law provisions. Any disputes arising under these Terms shall be resolved exclusively in the courts of that jurisdiction.</p>

                                <h2 id="changes">10. Changes to Terms</h2>
                                <p>We may revise these Terms at any time. When we do, we will update the "Last updated" date at the top of this page. For material changes, we will notify you by email or through a prominent notice in the Service at least 14 days before the changes take effect.</p>
                                <p>Your continued use of the Service after changes take effect constitutes your acceptance of the revised Terms. If you do not agree to the new Terms, please discontinue use of the Service.</p>

                                <h2 id="contact-us">11. Contact</h2>
                                <p>If you have any questions about these Terms, please contact us at <a href="mailto:hello@wera.dev">hello@wera.dev</a> or visit our <a href="{{ route('contact') }}">contact page</a>.</p>

                            </article>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>