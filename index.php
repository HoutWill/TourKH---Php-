<?php include("includes/config.php"); ?>
<?php include("includes/header.php"); ?>
<!-- Page Specific CSS -->
<link href="assets/css/home.css" rel="stylesheet">

<?php include("includes/navbar.php"); ?>

<!-- Premium Hero Section -->
<section class="home-hero">
    <div class="home-hero-content" data-aos="fade-up">
        <p class="section-subtitle text-white">Welcome to TravelKH</p>
        <h1>Explore the Wonders of Cambodia</h1>
        <p>Uncover ancient mysteries, pristine tropical islands, and breathtaking mountain landscapes with local experts.</p>
    </div>
</section>

<!-- Floating Search Bar -->
<div class="container search-container">
    <div class="search-bar-card" data-aos="fade-up" data-aos-delay="100">
        <form action="tours.php" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="search-input-group">
                        <label for="dest-input">Where to?</label>
                        <input type="text" id="dest-input" name="search" placeholder="e.g. Siem Reap, Kampot..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="search-input-group">
                        <label for="dur-select">Duration</label>
                        <select id="dur-select" name="duration">
                            <option value="">Any Duration</option>
                            <option value="1-3">1 - 3 Days</option>
                            <option value="4-7">4 - 7 Days</option>
                            <option value="8+">8+ Days</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="search-input-group">
                        <label for="price-select">Budget Limit</label>
                        <select id="price-select" name="max_price">
                            <option value="">Any Price</option>
                            <option value="50">Under $50</option>
                            <option value="100">Under $100</option>
                            <option value="200">Under $200</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="search-btn-modern">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle">Choose Your Adventure</span>
            <h2 class="section-title-modern">Explore by Category</h2>
        </div>

        <div class="row g-4">
            <!-- Category 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <a href="tours.php?search=Siem+Reap" class="text-decoration-none">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1544735716-392fe2489ffa?auto=format&fit=crop&w=600&q=80" alt="Cultural Heritage">
                        <div class="category-info">
                            <h4>Cultural Heritage</h4>
                            <p>Temples, ancient history & traditions</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Category 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <a href="tours.php?search=Kampot" class="text-decoration-none">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80" alt="Coastal Escapes">
                        <div class="category-info">
                            <h4>Nature & Coastal</h4>
                            <p>Riverside, beaches & sea views</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Category 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <a href="tours.php?search=Cardamom" class="text-decoration-none">
                    <div class="category-card">
                        <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=600&q=80" alt="Eco Adventures">
                        <div class="category-info">
                            <h4>Eco-Adventures</h4>
                            <p>Mountain hiking, jungle treks & wildlife</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Featured Tours Section -->
<section class="featured-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle">Recommended For You</span>
            <h2 class="section-title-modern">Popular Tour Packages</h2>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            // Pull the top 3 active tours from database
            $stmt = $conn->query("SELECT * FROM tours WHERE status='active' LIMIT 3");
            if ($stmt && $stmt->num_rows > 0):
                $delay = 100;
                while ($t = $stmt->fetch_assoc()):
            ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                <div class="tour-card-modern">
                    <div class="tour-image-container">
                        <img src="<?php echo getTourImage($t['image']); ?>" alt="<?php echo htmlspecialchars($t['title']); ?>">
                        <div class="tour-badge">Best Seller</div>
                        <div class="tour-price-badge">$<?php echo number_format($t['price'], 2); ?></div>
                    </div>
                    
                    <div class="tour-body-modern">
                        <div class="tour-meta-info">
                            <span>
                                📍 <?php echo htmlspecialchars($t['location']); ?>
                            </span>
                            <span>
                                ⏱️ <?php echo htmlspecialchars($t['duration']); ?>
                            </span>
                        </div>
                        <h3><?php echo htmlspecialchars($t['title']); ?></h3>
                        <p><?php echo htmlspecialchars(substr($t['short_description'], 0, 110)); ?>...</p>
                        
                        <a href="tour-details.php?id=<?php echo $t['tour_id']; ?>" class="btn-tour-detail">
                            View Experience
                        </a>
                    </div>
                </div>
            </div>
            <?php
                    $delay += 100;
                endwhile;
            else:
            ?>
            <div class="col-12 text-center">
                <p class="text-muted">No featured tours found. Check back soon!</p>
            </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="tours.php" class="btn-outline-green">Browse All Tours</a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-subtitle">Guest Reviews</span>
            <h2 class="section-title-modern">What Our Travelers Say</h2>
        </div>

        <div class="row g-4">
            <!-- Review 1 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="stars-container">
                        ★ ★ ★ ★ ★
                    </div>
                    <p class="testimonial-quote">"The sunset tour at Angkor Wat was absolutely breathtaking. Our Khmer guide was extremely knowledgeable, friendly, and spoke fluent English. Highly recommend TravelKH!"</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80" alt="Sophia M.">
                        <div>
                            <div class="author-name">Sophia Mitchell</div>
                            <div class="author-role">Traveler from Australia</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Review 2 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="stars-container">
                        ★ ★ ★ ★ ★
                    </div>
                    <p class="testimonial-quote">"We booked a custom family trip to Kep and Kampot. The transport was clean and safe, the guesthouse was amazing, and the local seafood cooking class was the highlight of our vacation."</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&q=80" alt="Sokha P.">
                        <div>
                            <div class="author-name">Sokha Phally</div>
                            <div class="author-role">Traveler from Phnom Penh</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Review 3 -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="stars-container">
                        ★ ★ ★ ★ ★
                    </div>
                    <p class="testimonial-quote">"Hiking the Cardamom Mountains with their guides felt safe, authentic, and eco-friendly. I loved knowing that a portion of the tour cost goes directly to local conservation programs."</p>
                    <div class="testimonial-author">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80" alt="David K.">
                        <div>
                            <div class="author-name">David Kaelen</div>
                            <div class="author-role">Traveler from Germany</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>