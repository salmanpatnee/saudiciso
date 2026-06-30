<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaudiCiso.net - Your Ultimate Strategic Partner</title>
    <meta name="color-scheme" content="light only">
    <link rel="stylesheet" href="css/landing.css?v=2.6">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>

    <div class="landing-page">

        <nav class="site-nav">
            <div class="container site-nav__inner">
                <img class="site-nav__logo" src="Images/SaudiCISOLogo.png" alt="SaudiCiso.net">
                <div class="site-nav__actions">
                    @auth
                        <form method="POST" action="{{ route('login.destroy') }}">
                            @csrf
                            <button type="submit" class="btn btn-login  btn--ghost">Logout</button>
                        </form>
                    @else
                        <a href="/vciso" class="btn btn-login  btn--ghost">Sign In</a>
                    @endauth
                    <button type="button" id="navContactButton" class="btn btn--primary">Contact Us!</button>
                </div>
            </div>
        </nav>

        <header class="hero">
            <div class="container hero__grid">
                <div class="hero__content">
                    <span class="eyebrow">Instantly Empowering Saudi CISO</span>
                    <h1>What is Your Biggest <span class="hero__accent">Problem</span> Today?</h1>
                    <p class="hero__sub">We have the Solution. Guaranteed!</p>
                    <div class="hero__actions">
                        <button type="button" id="headerContactButton" class="btn btn--primary">Contact Us!</button>
                        <a href="/vciso" class="btn btn--ghost">@auth Access Platform @else Sign In @endauth</a>
                    </div>
                    <ul class="hero__trust">
                        <li>1200+ Certified Staff in KSA</li>
                        <li>50+ Compliance Documents</li>
                        <li>Built for the Saudi Market</li>
                    </ul>
                </div>
                <div class="hero__media">
                    <div class="hero__visual">
                        <img src="Images/ThreePs5.png" alt="SaudiCiso.net">
                    </div>
                </div>
            </div>
        </header>

        <section class="section goodnews">
            <div class="container">
                <div class="goodnews__card reveal">
                    <span class="goodnews__emblem" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 4.8L19 9.6l-3.8 3.2L16.3 18 12 15.3 7.7 18l1.1-5.2L5 9.6l5.1-.8L12 3z"></path></svg>
                    </span>
                    <h2 class="goodnews__title"><span class="gn-navy">Good</span> <span class="gn-accent">News!</span></h2>
                    <p class="goodnews__text">Enhance your Saudization goals by recruiting certified Saudi cybersecurity professionals through the People Module of SaudiCISO.net</p>
                    <p class="goodnews__disclaimer">
                        <span class="goodnews__shield" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                        </span>
                        <span><b>Disclaimer:</b> Our Cybersecurity Professionals Database is compliant with the Saudi Personal Data Protection Law (PDPL). All information contained in the database has been collected from publicly available and publicly published sources.</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="section" id="ppp">
            <div class="container">
                <div class="pillars">
                    <article class="card card--hover pillar reveal">
                        <div class="pillar__icon">
                            <img src="Images/staffimage.png" alt="People">
                        </div>
                        <p class="pillar__label">1. PEOPLE:</p>
                        <p class="pillar__text">Find Top Saudi Talent<br>Access 1200+<br>Certified Staff in KSA</p>
                    </article>

                    <article class="card card--hover pillar reveal">
                        <div class="pillar__icon">
                            <img src="Images/security.png" alt="People">
                        </div>
                        <p class="pillar__label">2. PRODUCTS:</p>
                        <p class="pillar__text">Expert Product Insights<br>Product Categories<br>from A to Z</p>
                    </article>

                    <article class="card card--hover pillar reveal">
                        <div class="pillar__icon">
                            <img src="Images/document.png" alt="People">
                        </div>
                        <p class="pillar__label">3. PROCESSES:</p>
                        <p class="pillar__text">50+ Editable<br>Compliance<br>Documents</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section--alt">
            <div class="container">
                <p class="value__lead">SaudiCiso.net is a subscription-based professional platform created
                    exclusively for Chief Information Security Officers (CISOs) in the Kingdom of Saudi Arabia.</p>

                <div class="value-grid">
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Vendor-Independent</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Executive 12-Month Access</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Built for the Saudi Market</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Pre-Developed Editable Templates</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Direct Access to hire Saudi Cybersecurity Talent</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Priority WhatsApp Advisory Support</span>
                    </div>
                    <div class="value-item reveal">
                        <span class="value-item__check">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </span>
                        <span>Optional Monthly In-Person Executive Session</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="benefits">
                    <article class="card benefit reveal">
                        <h3>The Principal Consultant may visit your office to resolve your pressing problem.</h3>
                        <div class="benefit__divider"></div>
                        <div class="benefit__media">
                            <img src="Images/doorknowtwo.png" alt="door-knock">
                        </div>
                    </article>
                    <article class="card benefit reveal">
                        <h3>Everything within your budget until the month end.</h3>
                        <div class="benefit__divider"></div>
                        <div class="benefit__media">
                            <img src="Images/deadlinetwo.png" alt="deadline">
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section section--alt">
            <div class="container">
                <div class="bonus reveal">
                    <span class="bonus__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </span>
                    <p><b>Bonus for CISO:</b> Certified Information Security Manager (CISM)
                        Examination Voucher and Quick Review Session for Purchase Orders and Payments Received by
                        End of the Month.</p>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="budget reveal">
                    <div class="budget__content">
                        <h2>Budget Issues Solved!</h2>
                        <p>If you have budget constraints, the full-year subscription is a minimal
                            amount that your main supplier can easily include in their existing project costs.</p>
                        <button type="button" id="budgetContactButton" class="btn btn--primary">Contact Us!</button>
                    </div>
                    <div class="budget__media">
                        <img src="Images/budgettwo.png" alt="budget">
                    </div>
                </div>
            </div>
        </section>

        <section class="section section--alt portal">
            <div class="container">
                <div class="portal__card reveal">
                    <h2 class="signin-heading">Portal Access for Paid Members</h2>
                    <a href="/vciso" class="btn btn--navy">@auth Access Platform @else Sign In @endauth</a>
                </div>
            </div>
        </section>

        <footer class="site-footer">
            <div class="container site-footer__inner">
                <img src="Images/SaudiCISOLogo.png" alt="SaudiCiso.net">
                <p>&copy; {{ date('Y') }} SaudiCiso.net. All rights reserved.</p>
            </div>
        </footer>

    </div>

    <div id="contactModal" class="modal">
        <div class="modal-content">
            <button type="button" class="close-button" aria-label="Close">&times;</button>
            <h2>Contact Us to Solve Your Biggest Problem!</h2>
            <form id="contactForm">
                <div class="form-field form-field--half">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="email">Work Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-field form-field--half">
                    <label for="company">Company/Organization:</label>
                    <input type="text" id="company" name="company" required>
                </div>
                <div class="form-field form-field--full">
                    <label for="problem">Your Biggest Problem/Inquiry:</label>
                    <textarea id="problem" name="problem" rows="4" required></textarea>
                </div>
                <button type="submit" class="submit-button">Send Inquiry</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Get the modal element
        var modal = document.getElementById("contactModal");

        // Get the contact elements:
        // 1. Any <a> tag with href="#" (legacy trigger)
        // 2. The budget section button (id="budgetContactButton")
        // 3. The hero contact button (id="headerContactButton")
        // 4. The sticky nav contact button (id="navContactButton")
        var contactTriggers = document.querySelectorAll("a[href='#'], #budgetContactButton, #headerContactButton, #navContactButton");

        // Get the <span> element that closes the modal ('&times;')
        var span = document.getElementsByClassName("close-button")[0];

        // Function to open the modal
        function openModal(event) {
            // Check if the event target is an <a> tag and prevent default
            if (event.target.tagName === 'A') {
                event.preventDefault();
            }
            modal.style.display = "block";
        }

        // Attach click events to all contact triggers
        contactTriggers.forEach(function(element) {
            element.onclick = openModal;
        });

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Handle form submission via AJAX
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Stop the form from submitting normally

            // Get form data
            const formData = new FormData(this);
            const formObject = {};
            for (let [key, value] of formData.entries()) {
                // Map form field names to model field names
                if (key === 'name') formObject.fullname = value;
                else if (key === 'problem') formObject.message = value;
                else formObject[key] = value;
            }

            // Show a loading state or disable submit button
            const submitButton = this.querySelector('.submit-button');
            const originalButtonText = submitButton.textContent;
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;

            // Send the data to the server via an AJAX request
            axios.post('/contact-inquiry', formObject)
                .then(function(response) {
                    // On success, show success message and reset form
                    alert(response.data.message);
                    modal.style.display = "none";
                    document.getElementById('contactForm').reset();
                })
                .catch(function(error) {
                    // On error, show validation errors or generic error message
                    if (error.response && error.response.status === 422) {
                        // Validation error
                        const errors = error.response.data.errors;
                        let errorMessage = "Please correct the following errors:\n";

                        for (let field in errors) {
                            errorMessage += "- " + errors[field][0] + "\n";
                        }

                        alert(errorMessage);
                    } else {
                        // General server error
                        alert('There was an error submitting your inquiry. Please try again.');
                    }
                })
                .finally(function() {
                    // Reset button state regardless of success or error
                    submitButton.textContent = originalButtonText;
                    submitButton.disabled = false;
                });
        });

        // Subtle scroll-reveal animation (gracefully degrades without JS / IntersectionObserver)
        (function() {
            var revealEls = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                revealEls.forEach(function(el) { el.classList.add('is-visible'); });
                return;
            }
            var observer = new IntersectionObserver(function(entries, obs) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            revealEls.forEach(function(el) { observer.observe(el); });
        })();
    </script>
    <!-- Elfsight AI Chatbot | Saudi Ciso -->
    {{-- <script src="https://elfsightcdn.com/platform.js" async></script>
    <div class="elfsight-app-50a59065-4154-49f7-a375-961a269cf1c2" data-elfsight-app-lazy></div> --}}
</body>

</html>
