<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaudiCISO.net - Trusted Cybersecurity Expertise for Saudi Leaders</title>
    <meta name="description" content="SaudiCISO.net connects Saudi cybersecurity leaders with trusted talent, compliance documents, advisory support, and vendor-neutral product insight.">
    <meta name="color-scheme" content="dark">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=4.6">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="landing-page">
        <nav class="site-nav" id="siteNav" aria-label="Primary navigation">
            <div class="container site-nav__inner">
                <a class="site-nav__brand" href="/" aria-label="SaudiCISO.net home">
                    <img src="{{ asset('Images/SaudiCISOLogo.png') }}" alt="SaudiCISO.net">
                </a>

                <button class="site-nav__toggle" id="navToggle" type="button" aria-expanded="false" aria-controls="primaryMenu" aria-label="Open navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="site-nav__menu" id="primaryMenu">
                    <div class="site-nav__links">
                        <a href="#solutions">Solutions</a>
                        <a href="#talent">Talent</a>
                        <a href="#documents">Documents</a>
                        <a href="#advisory">Advisory</a>
                        <a href="#about">About</a>
                        <a href="#contact">Contact</a>
                    </div>

                    <div class="site-nav__actions">
                        <a href="/vciso" class="btn btn--ghost">@auth Access Platform @else Sign In @endauth</a>
                        <button type="button" class="btn btn--primary js-open-contact">Become a Member</button>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <section class="hero" id="top">
                <div class="container hero__grid">
                    <div class="hero__content">
                        <p class="eyebrow">
                            <span class="eyebrow__dot" aria-hidden="true"></span>
                            Saudi-focused cyber leadership platform
                        </p>
                        <h1>What is Your Biggest <span class="text-accent">Problem</span> Today?</h1>
                        <p class="hero__tagline">We have the Solution. <span class="text-accent">Guaranteed!</span></p>
                        {{-- <p class="hero__lead">Access vetted cyber talent, executive advisory, compliance-ready documents, and vendor-neutral product intelligence built for Saudi enterprises and regulated organizations.</p> --}}

                        <div class="hero__actions">
                            <button type="button" class="btn btn--primary btn--large js-open-contact">Become a Member</button>
                            <a href="/vciso" class="btn btn--secondary btn--large">Access Platform</a>
                        </div>

                        <div class="hero__proof" aria-label="Platform highlights">
                            <span>1200+ Certified Staff in KSA</span>
                            <span>50+ Compliance Documents</span>
                            <span>Built for the Saudi Market</span>
                        </div>
                    </div>

                    <figure class="hero__image-card reveal">
                        <img src="{{ asset('Images/ThreePs5.png') }}" alt="ThreePs5">
                    </figure>
                </div>
            </section>

            <section class="section saudization" aria-labelledby="saudization-heading">
                <div class="container">
                    <div class="saudization-card reveal">
                        <span class="icon-box saudization-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="m16 11 2 2 4-4"></path></svg>
                        </span>
                        <div>
                            <p class="eyebrow">Saudization support</p>
                            <h2 id="saudization-heading">Good <span class="text-accent">News</span></h2>
                            <p>Enhance your Saudization goals by recruiting certified Saudi cybersecurity professionals through the People Module of SaudiCISO.net.</p>
                            <p class="saudization-card__disclaimer">
                                <span class="saudization-card__disclaimer-label">
                                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    Disclaimer
                                </span>
                                Our Cybersecurity Professionals Database is compliant with the Saudi Personal Data Protection Law (PDPL). All information contained in the database has been collected from publicly available and publicly published sources.
                            </p>
                        </div>
                        {{-- <span class="saudization-card__badge">Nitaqat Aligned</span> --}}
                    </div>
                </div>
            </section>

            <section class="section" id="solutions" aria-labelledby="services-heading">
                <div class="container">
                    <div class="section-head reveal">
                        <p class="eyebrow">Enterprise services</p>
                        <h2 id="services-heading">One platform for the work CISOs must get right.</h2>
                        {{-- <p>Purpose-built support for cyber leaders who need credible expertise, faster execution, and decisions that stand up to board-level scrutiny.</p> --}}
                    </div>

                    <div class="services-grid">
                        <article class="service-card reveal" id="talent">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </span>
                            <h3>People</h3>
                            <p>Find Top Saudi Talent Access 1200+ Certified Staff in KSA</p>
                        </article>
                        <article class="service-card reveal">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                            </span>
                            <h3>Products</h3>
                            <p>Expert Product Insights Product Categories from A to Z</p>
                        </article>
                        <article class="service-card reveal" id="documents">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"></path><path d="M14 2v6h6"></path><path d="M10 13h4"></path><path d="M10 17h6"></path><path d="M8 13h.01"></path><path d="M8 17h.01"></path></svg>
                            </span>
                            <h3>Processes</h3>
                            <p>50+ Editable Compliance Documents</p>
                        </article>

                        {{-- <article class="service-card reveal" id="advisory">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                            </span>
                            <h3>Strategic Advisory</h3>
                            <p>Priority guidance for budget planning, executive reporting, regulatory readiness, and complex cyber decisions.</p>
                        </article>

                        <article class="service-card reveal">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                            </span>
                            <h3>Training</h3>
                            <p>Focused enablement for cyber teams, managers, and executives preparing for security leadership demands.</p>
                        </article>

                        <article class="service-card reveal">
                            <span class="icon-box">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg>
                            </span>
                            <h3>Risk Assessment</h3>
                            <p>Evaluate cyber maturity, control gaps, resource needs, and board-ready remediation priorities.</p>
                        </article> --}}
                    </div>
                </div>
            </section>

            <section class="section section--deep" id="about" aria-labelledby="why-heading">
                <div class="container why-grid">
                    <figure class="why-visual reveal why-image-card">
                        <img src="{{ asset('Images/saudi-ciso.png') }}" alt="Confident CISO">
                    </figure>

                    <div class="why-content reveal">
                        <p class="eyebrow">Why SaudiCISO</p>
                        <h2 id="why-heading">Built for leaders accountable for security, resilience, and trust.</h2>
                        <p>SaudiCiso.net is a subscription-based professional platform created exclusively for CISOs in the KSA.</p>

                        <div class="feature-list">
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Vendor-Independent</strong> 
                                    {{-- Guidance shaped by local business, talent, and regulatory realities. --}}
                                </p>
                            </div>
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Executive 12-Month Access</strong></p>
                            </div>
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Built for the Saudi Market</strong></p>
                            </div>
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Pre-Developed Editable Documents</strong></p>
                            </div>
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Direct Access to hire Saudi Cybersecurity Talent</strong></p>
                            </div>
                            <div>
                                <span class="check-icon"></span>
                                <p><strong>Optional Monthly In-Person Executive Session</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section proof-points" aria-label="Member value highlights">
                <div class="container">
                    <div class="proof-point reveal">
                        <figure class="proof-point__media">
                            <img src="{{ asset('Images/doorknowtwo.png') }}" alt="Principal Consultant knocking on your office door">
                        </figure>
                        <div class="proof-point__content">
                            <span class="proof-point__index">01</span>
                            <span class="proof-point__divider" aria-hidden="true"></span>
                            <h3>The Principal Consultant may visit your office to resolve your pressing problem.</h3>
                        </div>
                    </div>

                    <div class="proof-point proof-point--reverse reveal">
                        <figure class="proof-point__media">
                            <img src="{{ asset('Images/deadlinetwo.png') }}" alt="Month end budget calendar">
                        </figure>
                        <div class="proof-point__content">
                            <span class="proof-point__index">02</span>
                            <span class="proof-point__divider" aria-hidden="true"></span>
                            <h3>Everything within your budget until the month end.</h3>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section cta-section" id="contact" aria-label="CISO bonus offer">
                <div class="container">
                    <div class="bonus-banner reveal">
                        <span class="icon-box" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><polygon points="12 2 14.9 8.5 22 9.3 16.8 14.1 18.2 21 12 17.6 5.8 21 7.2 14.1 2 9.3 9.1 8.5 12 2"></polygon></svg>
                        </span>
                        <p><strong>Bonus for CISO:</strong> Certified Information Security Manager (CISM) Examination Voucher and Quick Review Session for Purchase Orders and Payments Received by End of the Month.</p>
                    </div>
                </div>
            </section>

            <section class="section budget-section" aria-labelledby="budget-heading">
                <div class="container">
                    <div class="budget-card reveal">
                        <div class="budget-card__content">
                            <h2 id="budget-heading">Budget Issues Solved!</h2>
                            <p>If you have budget constraints, the full-year subscription is a minimal amount that your main supplier can easily include in their existing project costs.</p>
                            <button type="button" class="btn btn--primary btn--large js-open-contact">Contact Us!</button>
                        </div>
                        <figure class="budget-card__media">
                            <img src="{{ asset('Images/budgettwo.png') }}" alt="Scissors cutting a paper labeled Budget">
                        </figure>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer site-footer--minimal">
            <div class="container footer-grid footer-grid--minimal">
                <div class="footer-brand">
                    <img src="{{ asset('Images/SaudiCISOLogo.png') }}" alt="SaudiCISO.net">
                    <p>Trusted cybersecurity expertise for Saudi CISOs, enterprises, and regulated organizations.</p>
                </div>
            </div>
            <div class="container footer-bottom">
                <p>&copy; {{ date('Y') }} SaudiCISO.net. All rights reserved.</p>
                <p>SaudiCISO.Net is Owned and Managed by GISBA</p>
            </div>
        </footer>
    </div>

    <div id="contactModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="contactModalTitle" aria-hidden="true">
        <div class="modal-content">
            <button type="button" class="close-button" aria-label="Close contact form">&times;</button>
            <p class="eyebrow">Membership inquiry</p>
            <h2 id="contactModalTitle">Speak with SaudiCISO</h2>
            <p class="modal-intro">Tell us what your security office needs. We will respond with the right membership or advisory path.</p>
            <form id="contactForm">
                <div class="form-field form-field--half">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" autocomplete="name" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="email">Work Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" autocomplete="tel" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="company">Company / Organization</label>
                    <input type="text" id="company" name="company" autocomplete="organization" required>
                </div>
                <div class="form-field form-field--full">
                    <label for="problem">How can we help?</label>
                    <textarea id="problem" name="problem" rows="4" required></textarea>
                </div>
                <button type="submit" class="submit-button">Send Inquiry</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        (function() {
            var nav = document.getElementById('siteNav');
            var navToggle = document.getElementById('navToggle');
            var primaryMenu = document.getElementById('primaryMenu');
            var modal = document.getElementById('contactModal');
            var closeButton = document.querySelector('.close-button');
            var contactTriggers = document.querySelectorAll('.js-open-contact');
            var contactForm = document.getElementById('contactForm');
            var lastFocusedElement = null;

            function updateNavState() {
                nav.classList.toggle('is-scrolled', window.scrollY > 16);
            }

            function closeMobileMenu() {
                navToggle.setAttribute('aria-expanded', 'false');
                primaryMenu.classList.remove('is-open');
            }

            function openModal(event) {
                if (event) {
                    event.preventDefault();
                }

                lastFocusedElement = document.activeElement;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');
                closeMobileMenu();
                setTimeout(function() {
                    document.getElementById('name').focus();
                }, 80);
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');

                if (lastFocusedElement) {
                    lastFocusedElement.focus();
                }
            }

            navToggle.addEventListener('click', function() {
                var expanded = navToggle.getAttribute('aria-expanded') === 'true';
                navToggle.setAttribute('aria-expanded', String(!expanded));
                primaryMenu.classList.toggle('is-open');
            });

            primaryMenu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', closeMobileMenu);
            });

            contactTriggers.forEach(function(element) {
                element.addEventListener('click', openModal);
            });

            closeButton.addEventListener('click', closeModal);

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });

            contactForm.addEventListener('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(contactForm);
                var formObject = {};

                formData.forEach(function(value, key) {
                    if (key === 'name') {
                        formObject.fullname = value;
                    } else if (key === 'problem') {
                        formObject.message = value;
                    } else {
                        formObject[key] = value;
                    }
                });

                var submitButton = contactForm.querySelector('.submit-button');
                var originalButtonText = submitButton.textContent;
                submitButton.textContent = 'Sending...';
                submitButton.disabled = true;

                axios.post('/contact-inquiry', formObject)
                    .then(function(response) {
                        alert(response.data.message);
                        contactForm.reset();
                        closeModal();
                    })
                    .catch(function(error) {
                        if (error.response && error.response.status === 422) {
                            var errors = error.response.data.errors;
                            var errorMessage = 'Please correct the following errors:\n';

                            Object.keys(errors).forEach(function(field) {
                                errorMessage += '- ' + errors[field][0] + '\n';
                            });

                            alert(errorMessage);
                            return;
                        }

                        alert('There was an error submitting your inquiry. Please try again.');
                    })
                    .finally(function() {
                        submitButton.textContent = originalButtonText;
                        submitButton.disabled = false;
                    });
            });

            document.querySelectorAll('.newsletter').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    form.reset();
                });
            });

            var revealEls = document.querySelectorAll('.reveal');
            var counterEls = document.querySelectorAll('.counter');

            function animateCounter(counter) {
                var target = Number(counter.dataset.target);
                var suffix = counter.dataset.suffix || '';
                var duration = 1300;
                var startTime = performance.now();

                function tick(now) {
                    var progress = Math.min((now - startTime) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    counter.textContent = Math.round(target * eased).toLocaleString() + suffix;

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    }
                }

                requestAnimationFrame(tick);
            }

            if ('IntersectionObserver' in window) {
                var revealObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.14, rootMargin: '0px 0px -48px 0px' });

                revealEls.forEach(function(el) {
                    revealObserver.observe(el);
                });

                var counterObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            animateCounter(entry.target);
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });

                counterEls.forEach(function(counter) {
                    counterObserver.observe(counter);
                });
            } else {
                revealEls.forEach(function(el) {
                    el.classList.add('is-visible');
                });
                counterEls.forEach(animateCounter);
            }

            updateNavState();
            window.addEventListener('scroll', updateNavState, { passive: true });
        })();
    </script>
</body>

</html>
