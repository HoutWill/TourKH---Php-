<?php
require_once __DIR__ . '/../includes/config.php';
require_user();

// Self-healing database check for stripe_token column
$check = $conn->query("SHOW COLUMNS FROM `bookings` LIKE 'stripe_token'");
if ($check && $check->num_rows == 0) {
    $conn->query("ALTER TABLE `bookings` ADD `stripe_token` VARCHAR(255) NULL AFTER `status`");
}

$tourId = (int) ($_GET['id'] ?? 0);
$error = '';

$stmt = $conn->prepare("SELECT * FROM tours WHERE tour_id = ? AND status = 'active'");
$stmt->bind_param('i', $tourId);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();

if (!$tour) {
    redirect('tours.php');
}

$userId = current_user_id();
$u = get_user_by_id($conn, $userId);

// Helper function to charge Stripe via backend cURL
function stripe_charge($secret_key, $amount_cents, $token, $description, $metadata = [], $receipt_email = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/charges');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ':');
    
    $fields = [
        'amount' => $amount_cents,
        'currency' => 'usd',
        'source' => $token,
        'description' => $description
    ];

    if ($receipt_email) {
        $fields['receipt_email'] = $receipt_email;
    }

    if (!empty($metadata)) {
        foreach ($metadata as $key => $val) {
            $fields['metadata[' . $key . ']'] = $val;
        }
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_status === 200) {
        return json_decode($response, true);
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $people = max(1, (int) ($_POST['people'] ?? 1));
    $bookingDate = $_POST['booking_date'] ?? '';
    $userId = current_user_id();
    $stripeToken = $_POST['stripeToken'] ?? null;

    if ($bookingDate === '') {
        $error = 'Please select a booking date.';
    } elseif (empty($stripeToken)) {
        $error = 'Payment verification failed. Please try again.';
    } else {
        $totalPrice = $tour['price'] * $people;
        $amountCents = (int)($totalPrice * 100);
        $secretKey = $stripe_secret;
        
        $userName = !empty($u['full_name']) ? $u['full_name'] : $u['username'];
        $userEmail = !empty($u['email']) ? $u['email'] : '';
        $description = 'Booking for ' . $tour['title'] . ' (Travelers: ' . $people . ') - Customer: ' . $userName . ' (' . $userEmail . ')';

        $metadata = [
            'customer_name' => $userName,
            'customer_email' => $userEmail,
            'tour_title' => $tour['title']
        ];
        
        // Call Stripe Charge API
        $charge = stripe_charge($secretKey, $amountCents, $stripeToken, $description, $metadata, $userEmail);
        
        if ($charge && isset($charge['id'])) {
            $status = 'approved';
            $chargeId = $charge['id']; // Store the charge transaction ID (ch_...)
            
            $ins = $conn->prepare("INSERT INTO bookings (user_id, tour_id, booking_date, people, total_price, status, stripe_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param('iisidss', $userId, $tourId, $bookingDate, $people, $totalPrice, $status, $chargeId);
            
            if ($ins->execute()) {
                header("Location: " . base_url('user/profile.php?booking_success=1'));
                exit;
            }
            $error = 'Booking registration failed, but payment succeeded. Payment ID: ' . $chargeId;
        } else {
            $error = 'Stripe payment card charge failed. Please try a different card.';
        }
    }
}

$pageTitle = 'Book Tour - TravelKH';
include __DIR__ . '/../includes/header.php';
?>

<!-- Stripe JS SDK -->
<script src="https://js.stripe.com/v3/"></script>

<style>
.booking-card-modern {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid rgba(46, 125, 50, 0.08);
    box-shadow: 0 15px 35px rgba(0,0,0,0.04);
    padding: 40px;
}
.booking-tour-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding-bottom: 25px;
    margin-bottom: 25px;
}
.booking-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--primary-green);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
    display: block;
}
.booking-form-control {
    background: var(--soft-green-bg);
    border: 1px solid rgba(46, 125, 50, 0.12);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.95rem;
    color: var(--charcoal);
    font-weight: 600;
    width: 100%;
    outline: none;
    transition: all 0.3s ease;
}
.booking-form-control:focus {
    border-color: var(--primary-green);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.08);
}
.price-summary-box {
    background: var(--soft-green-bg);
    border-radius: 16px;
    padding: 20px;
    margin: 25px 0;
    border: 1px dashed rgba(46, 125, 50, 0.2);
}
.price-summary-row {
    display: flex;
    justify-content: space-between;
    font-weight: 600;
    color: var(--charcoal);
    margin-bottom: 8px;
}
.price-summary-row:last-child {
    margin-bottom: 0;
    padding-top: 8px;
    border-top: 1px solid rgba(0,0,0,0.06);
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--primary-green);
}

/* Stripe Form Card Container */
.stripe-payment-container {
    background: #fafafa;
    border-radius: 16px;
    padding: 25px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 25px;
}
.stripe-element-wrapper {
    background: #ffffff;
    border: 1px solid rgba(46, 125, 50, 0.12);
    border-radius: 12px;
    padding: 14px 16px;
    transition: all 0.3s ease;
}
.stripe-element-wrapper.focused {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.08);
}
.test-card-helper {
    font-size: 0.8rem;
    color: #777;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-pay-submit {
    background: var(--primary-green);
    color: #ffffff;
    font-weight: 700;
    border: none;
    border-radius: 14px;
    padding: 15px;
    width: 100%;
    box-shadow: 0 6px 15px rgba(46, 125, 50, 0.15);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-pay-submit:hover {
    background: var(--dark-green);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(27,94,32,0.25);
}
.btn-pay-submit:disabled {
    background: #c8d8cc;
    color: #777;
    box-shadow: none;
    cursor: not-allowed;
}
</style>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="profile-layout-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <div class="booking-card-modern" data-aos="fade-up">
                    
                    <div class="booking-tour-header">
                        <span class="booking-label">Tour Booking</span>
                        <h2 class="text-success mb-2" style="font-weight:800; font-family:'Outfit';">
                            <?php echo htmlspecialchars($tour['title']); ?>
                        </h2>
                        <p class="text-muted mb-0">
                            📍 <?php echo htmlspecialchars($tour['location']); ?> &nbsp;·&nbsp; ⏱️ <?php echo htmlspecialchars($tour['duration']); ?>
                        </p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger mb-4" style="border-radius: 12px;"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form id="payment-form" method="POST">
                        <input type="hidden" name="stripeToken" id="stripeToken">
                        
                        <!-- Travel Date -->
                        <div class="mb-3">
                            <label class="booking-label" for="booking_date">Select Travel Date</label>
                            <input type="date" id="booking_date" name="booking_date" class="booking-form-control" required
                                   min="<?php echo date('Y-m-d'); ?>"
                                   value="<?php echo htmlspecialchars($_POST['booking_date'] ?? ''); ?>">
                        </div>
                        
                        <!-- Number of People -->
                        <div class="mb-4">
                            <label class="booking-label" for="people">Number of Travelers</label>
                            <input type="number" id="people" name="people" class="booking-form-control" min="1" max="25" required
                                   value="<?php echo (int) ($_POST['people'] ?? 1); ?>">
                        </div>

                        <!-- Price Calculations Summary -->
                        <div class="price-summary-box">
                            <div class="price-summary-row">
                                <span>Base Price per Person</span>
                                <span>$<?php echo number_format($tour['price'], 2); ?></span>
                            </div>
                            <div class="price-summary-row">
                                <span>Travelers</span>
                                <span id="summary-people">x 1</span>
                            </div>
                            <div class="price-summary-row">
                                <span>Total Price</span>
                                <span id="summary-total">$<?php echo number_format($tour['price'], 2); ?></span>
                            </div>
                        </div>

                        <!-- Stripe Card Element Container -->
                        <div class="stripe-payment-container">
                            <label class="booking-label">Credit or Debit Card</label>
                            <div id="stripe-card-wrapper" class="stripe-element-wrapper">
                                <div id="card-element">
                                    <!-- Stripe Elements Inject here -->
                                </div>
                            </div>
                            
                            <!-- Card validation error messages -->
                            <div id="card-errors" role="alert" class="text-danger mt-2 small font-weight-bold"></div>
                            
                            <!-- Test card helper guide -->
                            <div class="test-card-helper">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>
                                <span>Test card: use <strong>4242 4242 4242 4242</strong></span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submit-button" class="btn-pay-submit">
                            <span id="button-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span id="button-text">Pay & Confirm Booking</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Dynamic Price summary calculations
const basePrice = <?php echo (float)$tour['price']; ?>;
const peopleInput = document.getElementById('people');
const summaryPeople = document.getElementById('summary-people');
const summaryTotal = document.getElementById('summary-total');

function updateSummary() {
    const people = Math.max(1, parseInt(peopleInput.value) || 1);
    const total = basePrice * people;
    
    summaryPeople.textContent = 'x ' + people;
    summaryTotal.textContent = '$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

peopleInput.addEventListener('input', updateSummary);
peopleInput.addEventListener('change', updateSummary);

// Stripe API integration
const stripe = Stripe('<?php echo $stripe_publishable; ?>');
const elements = stripe.elements();

// Style Stripe Form Input
const style = {
    base: {
        color: '#1E2E21',
        fontFamily: 'Outfit, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#C62828',
        iconColor: '#C62828'
    }
};

const card = elements.create('card', { style: style });
card.mount('#card-element');

const cardWrapper = document.getElementById('stripe-card-wrapper');
card.on('focus', function() {
    cardWrapper.classList.add('focused');
});
card.on('blur', function() {
    cardWrapper.classList.remove('focused');
});

// Handle errors real-time
card.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Handle Form Submission with Stripe tokenization
const form = document.getElementById('payment-form');
const submitBtn = document.getElementById('submit-button');
const spinner = document.getElementById('button-spinner');
const btnText = document.getElementById('button-text');

form.addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Disable submit button and show loading state
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.textContent = 'Processing Payment...';
    
    stripe.createToken(card).then(function(result) {
        if (result.error) {
            // Re-enable button and display error
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Pay & Confirm Booking';
            
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
        } else {
            // Inject token into form
            document.getElementById('stripeToken').value = result.token.id;
            // Submit form to server
            form.submit();
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
