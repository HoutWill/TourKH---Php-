<?php include("includes/config.php"); ?>
<?php include("includes/header.php"); ?>
<!-- Page Specific CSS -->
<link href="assets/css/tours.css" rel="stylesheet">

<?php include("includes/navbar.php"); ?>

<!-- Tours Page Hero -->
<section class="tours-hero">
    <div class="tours-hero-content" data-aos="fade-up">
        <h1>Our Tour Packages</h1>
        <p>Browse through our handpicked experiences curated to show you the best of Cambodia.</p>
    </div>
</section>

<!-- Content and Filter Layout -->
<section class="tours-layout-section">
    <div class="container">
        <div class="row g-4">
            
            <!-- Filter Sidebar -->
            <div class="col-lg-3 col-md-4" data-aos="fade-right">
                <div class="filter-card">
                    <h5 class="mb-4" style="font-weight: 700; color: var(--charcoal);">Filter Tours</h5>
                    <form action="tours.php" method="GET">
                        
                        <!-- Search Keyword -->
                        <div class="filter-group">
                            <label class="filter-title">Destination or Keyword</label>
                            <input type="text" name="search" class="filter-search-input" placeholder="Search tours..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        
                        <!-- Duration Selection -->
                        <div class="filter-group">
                            <label class="filter-title">Duration</label>
                            <select name="duration" class="filter-select">
                                <option value="">Any Duration</option>
                                <option value="1-3" <?php echo (isset($_GET['duration']) && $_GET['duration'] === '1-3') ? 'selected' : ''; ?>>1 - 3 Days</option>
                                <option value="4-7" <?php echo (isset($_GET['duration']) && $_GET['duration'] === '4-7') ? 'selected' : ''; ?>>4 - 7 Days</option>
                                <option value="8+" <?php echo (isset($_GET['duration']) && $_GET['duration'] === '8+') ? 'selected' : ''; ?>>8+ Days</option>
                            </select>
                        </div>
                        
                        <!-- Price Limit Selection -->
                        <div class="filter-group">
                            <label class="filter-title">Max Budget</label>
                            <select name="max_price" class="filter-select">
                                <option value="">Any Price</option>
                                <option value="50" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] === '50') ? 'selected' : ''; ?>>Under $50</option>
                                <option value="100" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] === '100') ? 'selected' : ''; ?>>Under $100</option>
                                <option value="200" <?php echo (isset($_GET['max_price']) && $_GET['max_price'] === '200') ? 'selected' : ''; ?>>Under $200</option>
                            </select>
                        </div>
                        
                        <!-- Submit Filters -->
                        <button type="submit" class="filter-btn">Apply Filters</button>
                        
                        <!-- Reset Filters -->
                        <?php if (isset($_GET['search']) || isset($_GET['duration']) || isset($_GET['max_price'])): ?>
                            <a href="tours.php" class="filter-reset-link">Reset Filters</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            
            <!-- Tour Listings -->
            <div class="col-lg-9 col-md-8" data-aos="fade-left">
                <div class="row g-4">
                    <?php
                    // Build dynamic SQL query based on filters
                    $where = ["status='active'"];

                    if (!empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $where[] = "(title LIKE '%$search%' OR location LIKE '%$search%' OR short_description LIKE '%$search%' OR full_description LIKE '%$search%')";
                    }

                    if (!empty($_GET['duration'])) {
                        $dur = $_GET['duration'];
                        if ($dur === '1-3') {
                            $where[] = "(duration LIKE '%1 Day%' OR duration LIKE '%2 Day%' OR duration LIKE '%3 Day%' OR duration LIKE '%1-3%')";
                        } elseif ($dur === '4-7') {
                            $where[] = "(duration LIKE '%4 Day%' OR duration LIKE '%5 Day%' OR duration LIKE '%6 Day%' OR duration LIKE '%7 Day%' OR duration LIKE '%4-7%')";
                        } elseif ($dur === '8+') {
                            $where[] = "(duration NOT LIKE '%1 Day%' AND duration NOT LIKE '%2 Day%' AND duration NOT LIKE '%3 Day%' AND duration NOT LIKE '%4 Day%' AND duration NOT LIKE '%5 Day%' AND duration NOT LIKE '%6 Day%' AND duration NOT LIKE '%7 Day%')";
                        }
                    }

                    if (!empty($_GET['max_price'])) {
                        $max_price = floatval($_GET['max_price']);
                        $where[] = "price <= $max_price";
                    }

                    $sql = "SELECT * FROM tours WHERE " . implode(' AND ', $where);
                    $res = $conn->query($sql);

                    if ($res && $res->num_rows > 0):
                        while ($t = $res->fetch_assoc()):
                    ?>
                    <div class="col-lg-4 col-sm-6">
                        <div class="tour-card-modern">
                            <div class="tour-image-container">
                                <img src="<?php echo getTourImage($t['image']); ?>" alt="<?php echo htmlspecialchars($t['title']); ?>">
                                <div class="tour-badge">Active Tour</div>
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
                                <p><?php echo htmlspecialchars(substr($t['short_description'], 0, 95)); ?>...</p>
                                
                                <a href="tour-details.php?id=<?php echo $t['tour_id']; ?>" class="btn-tour-detail">
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <!-- Empty Search Result -->
                    <div class="col-12">
                        <div class="empty-results-box py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                            </svg>
                            <h3>No Adventures Found</h3>
                            <p class="text-muted">We couldn't find any tours matching your filter criteria. Try adjusting your search term, duration, or budget range.</p>
                            <a href="tours.php" class="btn btn-green mt-4 px-4 py-2" style="border-radius: 12px;">Reset All Filters</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>