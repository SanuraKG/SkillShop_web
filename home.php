<?php
include("header.php");

//GET Featured Products from the Database
$productResult = Database::search(
    "SELECT 
                                               p.`id`,
                                               p.`title`,
                                               p.`description`,
                                               p.`price`,
                                               p.`image_url`,
                                               p.`status`,
                                               u.`fname`,
                                               COUNT(f.`id`) AS `review_count`,
                                               COALESCE(AVG(f.`rating`),0) AS `avg_rating`
                                          FROM `product` p 
                                          JOIN `user` u ON p.`seller_id`=u.`id`
                                          LEFT JOIN `feedback` f ON p.`id`=f.`product_id`
                                          WHERE p.`status`=?
                                          GROUP BY p.`id`
                                          LIMIT 6",
    "s",
    ["active"]

);

$products = [];
if ($productResult && $productResult->num_rows > 0) {
    while ($product = $productResult->fetch_assoc()) {
        $products[] = $product;
    }
}

//GET Featured Testimonials from feedback table
$testimonialResult = Database::search(
    "SELECT 
                 f.`rating`,
                 f.`message`,
                 u.`fname`,
                 u.`lname`,
                 u.`id`,
                 up.`avatar_url`
    FROM `feedback` f
    JOIN `user` u ON f.`user_id`=u.`id`
    LEFT JOIN `user_profile` up ON u.`id`=up.`user_id`
    WHERE f.`is_feedback`=1
    LIMIT 3"
);

$testimonials = [];
if ($testimonialResult && $testimonialResult->num_rows > 0) {
    while ($testimonial = $testimonialResult->fetch_assoc()) {
        $testimonials[] = $testimonial;
    }
}

//GET platform statistics
$usersResult = Database::search("SELECT COUNT(DISTINCT `id`) AS `total_users` FROM `user`;");
$productsCountResult = Database::search("SELECT COUNT(`id`) AS `total_products` FROM `product`;");
$feedbackStatusResult = Database::search("SELECT AVG(`rating`) AS `avg_rating` FROM `feedback`;");
$ordersStatusResult = Database::search("SELECT COUNT(`id`) AS `total_orders`,SUM(`total_amount`) AS `total_revenue` FROM
`order`;");

$totalUsers = ($usersResult && $row = $usersResult->fetch_assoc()) ? $row["total_users"] : 0;
$totalProducts = ($productsCountResult && $row = $productsCountResult->fetch_assoc()) ? $row["total_products"] : 0;
$avgRating = ($feedbackStatusResult && $row = $feedbackStatusResult->fetch_assoc()) ?
    round(($row["avg_rating"] / 5) * 100, 0) : 0;

$totalOrders = ($ordersStatusResult && $row = $ordersStatusResult->fetch_assoc()) ? $row["total_orders"] : 0;
$totalRevenue = ($ordersStatusResult && $row = $ordersStatusResult->fetch_assoc()) ? intval($row["total_revenue"] / 1000000) : 0;

?>

<!-- Heri Section -->
<section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Learn & Earn Skill Today</h1>
                <p class="text-blue-100 text-lg mb-6">Connect with expert skill sellers and buyers in our thriving
                    community</p>


                <!-- Search Bar -->
                <form method="GET" action="search-products.php" class="mb-6 bg-white rounded-lg p-3 flex gap-2">
                    <input type="text" placeholder="What skilla are your looking for?"
                        class="flex-1 bg-trasparent text-gray-900 outline-none text-sm" />
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg
                hover:bg-blue-700 font-medium text-sm transition-colors">Search</button>
                </form>

                <div class="flex gap-4 flex-col md:flex-row">

                    <?php if (isset($_SESSION["user_email"])): ?>

                        <?php if ($userRole == "buyer"): ?>
                            <a href="buyer-dashboard.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg
                        font-bold hover:shadow-lg inline-block transition-all">Go to Dashboard</a>
                            <a href="#browse" class="border-2 border-white text-white px-6 py-3 rounded-lg
                        font-bold hover:bg-white hover:bg-opacity-10 inline-block transition-all">Browse Skills</a>
                        <?php endif; ?>

                        <?php if ($userRole == "seller"): ?>
                            <a href="seller-dashboard.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg
                        font-bold hover:shadow-lg inline-block transition-all">Go to Dashboard</a>
                            <a href="product-register.php" class="border-2 border-white text-white px-6 py-3 rounded-lg
                        font-bold hover:bg-white hover:bg-opacity-10 inline-block transition-all">Create Skills</a>
                        <?php endif; ?>

                    <?php else: ?>
                        <a href="index.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg
                        font-bold hover:shadow-lg inline-block transition-all">Get Started</a>
                        <a href="#" class="border-2 border-white text-white px-6 py-3 rounded-lg
                        font-bold hover:bg-white hover:bg-opacity-10 inline-block transition-all">Learn More</a>

                    <?php endif; ?>
                </div>
            </div>

            <div class="hidden md:block">
                <div class="bg-white bg-opacity-10 rounded-xl p-8 backdrop-blur-sm">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-blue-100">
                            <span class="text-2xl">✨</span>
                            <p>Expert Instructors ready to teach</p>
                        </div>
                        <div class="flex items-center gap-3 text-blue-100">
                            <span class="text-2xl">🚀</span>
                            <p>Grow your skills or business</p>
                        </div>
                        <div class="flex items-center gap-3 text-blue-100">
                            <span class="text-2xl">💰</span>
                            <p>Secure transactions</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Featured Carousel -->
<section id="browse" class="py-16 md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Featured Skills</h2>
        <p class="text-gray-600">Discover trending skill from top sellers</p>
    </div>

    <!-- Carousel Container -->
    <div class="relative group">
        <div class="overflow-hidden rounded-xl">
            <div id="carousel" class="flex transition-transform duration-500 ease-out"
                style="transform: translatex(0);">

                <!-- Carousel Items -->
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="carousel-item flex-shrink-0 w-full md:w-1/3 px-3 pb-4">
                            <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all h-full">
                                <img src="<?php echo $product["image_url"] ?>" class="w-full h-40 object-cover" />
                                <div class="p-5">
                                    <h3 class="font-bold text-lg mb-2"><?php echo $product["title"] ?></h3>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo $product["description"] ?></p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-blue-600 font-bold">Rs. <?php echo $product["price"] ?></span>
                                        <span class="text-yellow-400">⭐
                                            <?php echo round($product["avg_rating"], 1) ?>(<?php echo $product["review_count"] ?>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- No product message -->
                    <div class="carousel-item flex-shrink-0 w-full md:w-1/3 px-3 pb-4">
                        <div class="bg-white rounded-xl overflow-hidden shadow-lg p-8 text-center h-full flex items-center">
                            <p class="text-gray-500">No products available yet. Check back soon!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <button onclick="slideCarousel(-1);" class="absolute left-0 top-1/2 -translate-x-5 bg-blue-600 text-white p-3
             rounded-full hover:bg-blue-700 transition-all hover:scale-110 z-10 opacity-0 group-hover:opacity-100">
            < </button>
                <button onclick="slideCarousel(1);"
                    class="absolute right-0 top-1/2 -translate-x-5 bg-blue-600 text-white p-3
             rounded-full hover:bg-blue-700 transition-all hover:scale-110 z-10 opacity-0 group-hover:opacity-100">></button>

    </div>

    <!-- Carousel Indicators -->
    <div id="carousel-indicators" class="flex justify-center gap-2 mt-8">
        <button onclick="goToSlide(0);" class="carousel-dot w-3 h-3 rounded-full bg-blue-600 transition-all"></button>
        <button onclick="goToSlide(1);" class="carousel-dot w-3 h-3 rounded-full bg-gray-300 transition-all"></button>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="bg-gray-50 py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">Explore Categories</h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">🎨</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Design</p>
            </a>
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">💻</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Development</p>
            </a>
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">📱</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Mobile</p>
            </a>
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">📚</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Education</p>
            </a>
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">🎬</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Video</p>
            </a>
            <a href="#" class="group bg-white p-6 rounded-lg text-center shadow hover:shadow-lg transition-all
                    cursor-pointer hover:scale-105 hover:border-2 hover:border-blue-600">
                <div class="text-5xl mb-3 group-hover:scale-110 transition-transform">💼</div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-600">Business</p>
            </a>
        </div>

    </div>

</section>

<!-- Testimonials Section -->
<section class="py-16 md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">What Our Users Say</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <?php if (count($testimonials) > 0): ?>
            <?php foreach ($testimonials as $idx => $testimonial): ?>
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-2xl transition-shadow hover:scale-105 duration-300">
                    <div class="flex items-center mb-4">
                        <img src="<?php echo $testimonial["avatar_url"]; ?>" class="w-12 h-12 rounded-full mr-4" />
                        <div>
                            <p class="font-bold text-gray-900">
                                <?php echo $testimonial["fname"] . " " . $testimonial["lname"]; ?>
                            </p>
                            <p class="text-sm text-yellow-400">
                                <?php
                                $stars = '';
                                for ($i = 0; $i < $testimonial["rating"]; $i++) {
                                    $stars .= '⭐';
                                }
                                echo $stars;
                                ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-600"><?php echo $testimonial["message"] ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-lg p-8 text-center col-span-3">
                <p class="text-gray-500">No testimonial yet. Be the first to leave a review!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 md:py-24 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

            <div class="hover:scale-110 transition-transform duration-300 cursor-pointer">
                <div class="text-4xl md:text-5xl font-bold mb-2"><?php echo number_format($totalUsers); ?>+</div>
                <p class="text-blue-100">Active Users</p>
            </div>
            <div class="hover:scale-110 transition-transform duration-300 cursor-pointer">
                <div class="text-4xl md:text-5xl font-bold mb-2"><?php echo number_format($totalProducts); ?>+</div>
                <p class="text-blue-100">Skills Listed</p>
            </div>
            <div class="hover:scale-110 transition-transform duration-300 cursor-pointer">
                <div class="text-4xl md:text-5xl font-bold mb-2"><?php echo $avgRating; ?>%</div>
                <p class="text-blue-100">Satisfaction Rate</p>
            </div>
            <div class="hover:scale-110 transition-transform duration-300 cursor-pointer">
                <div class="text-4xl md:text-5xl font-bold mb-2"><?php echo number_format($totalRevenue); ?>M+</div>
                <p class="text-blue-100">Transaction Value</p>
            </div>

        </div>
    </div>

</section>

<!-- How It Works -->
<section class="py-16 md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">How It Works</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="text-center group">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full w-16 h-16 flex items-center
                    justify-center text-2xl text-white mx-auto mb-4 group-hover:scale-110 transition-transform">1</div>
            <h3 class="font-bold text-lg text-gray-900 mb-2">Create Account</h3>
            <p class="text-gray-600 text-sm">Sign up as a buyer or seller in minutes</p>
        </div>
        <div class="text-center group">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full w-16 h-16 flex items-center
                    justify-center text-2xl text-white mx-auto mb-4 group-hover:scale-110 transition-transform">2</div>
            <h3 class="font-bold text-lg text-gray-900 mb-2">Browser or List</h3>
            <p class="text-gray-600 text-sm">Explore skills or create your own content</p>
        </div>
        <div class="text-center group">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full w-16 h-16 flex items-center
                    justify-center text-2xl text-white mx-auto mb-4 group-hover:scale-110 transition-transform">3</div>
            <h3 class="font-bold text-lg text-gray-900 mb-2">Connect & Learn</h3>
            <p class="text-gray-600 text-sm">Interract with instructors and commiunity</p>
        </div>
        <div class="text-center group">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full w-16 h-16 flex items-center
                    justify-center text-2xl text-white mx-auto mb-4 group-hover:scale-110 transition-transform">4</div>
            <h3 class="font-bold text-lg text-gray-900 mb-2">Grow & Earn</h3>
            <p class="text-gray-600 text-sm">Build skills or passive income stream</p>
        </div>

    </div>

</section>

<!-- Call to Action -->
<section class="py-16  md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 md:0-16 text-center text-white shadow-2xl">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Start Your Joruney?</h2>
        <p class="text-blue-100 text-lg mb-8">Joint thousands of learners and sellers earning on SkillShop today</p>
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <?php if (isset($_SESSION["user_email"])): ?>

                <?php if ($userRole == "buyer"): ?>
                    <a href="buyer-dashboard.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold
                        hover:shadow-2xl inline-block transition-all hover:scale-105">Go to Dashboard</a>
                    <a href="#browse" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold
                        hover:bg-white hover:bg-opacity-20 inline-block transition-all">Explore More</a>

                <?php elseif ($userRole == "seller"): ?>
                    <a href="seller-dashboard.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold
                        hover:shadow-2xl inline-block transition-all hover:scale-105">Go to Dashboard</a>
                    <a href="#" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold
                        hover:bg-white hover:bg-opacity-20 inline-block transition-all">Create New Skill</a>

                <?php endif; ?>

            <?php else: ?>
                <a href="index.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-bold
                        hover:shadow-2xl inline-block transition-all hover:scale-105">Sign Up Now</a>
                <a href="#browse" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold
                        hover:bg-white hover:bg-opacity-20 inline-block transition-all">Learn More</a>
            <?php endif; ?>
        </div>
    </div>

</section>



<script>
    var currentSlide = 0;
    var itemsPerView = window.innerWidth >= 768 ? 3 : 1
    var totalItems = <?php echo count($products) ?>;
    var maxSlides = Math.ceil(totalItems / itemsPerView) - 1;
    // Add this function
    function getSettings() {
        var itemsPerView = window.innerWidth >= 768 ? 3 : 1;
        var totalItems = <?php echo count($products) ?>;
        var maxSlides = Math.max(0, Math.ceil(totalItems / itemsPerView) - 1);

        return {
            itemsPerView: itemsPerView,
            maxSlides: maxSlides
        };
    }

    function slideCarousel(direction) {
        var {
            maxSlides
        } = getSettings();
        currentSlide += direction;

        if (currentSlide > maxSlides) {
            currentSlide = 0;
        } else if (currentSlide < 0) {
            currentSlide = maxSlides;
        }

        updateCarousel();
    }

    function goToSlide(slide) {
        currentSlide = slide;
        updateCarousel();
    }

    function updateCarousel() {
        var carousel = document.getElementById("carousel");
        var {
            itemsPerView
        } = getSettings();
        var offset = -currentSlide * (100 / itemsPerView);
        carousel.style.transform = `translateX(${offset}%)`;

        var dotsContainer = document.getElementById("carousel-indicators");
        var dots = dotsContainer.querySelectorAll(".carousel-dot");

        dots.forEach((dot, index) => {
            if (index == currentSlide) {
                dot.classList.add("bg-blue-600");
                dot.classList.remove("bg-gray-300");
            } else {
                dot.classList.remove("bg-blue-600");
                dot.classList.add("bg-gray-300");
            }
        });
    }

    // Auto Slide
    setInterval(() => {
        slideCarousel(1);
    }, 5000);

    window.addEventListener("resize", () => {
        var {
            maxSlides
        } = getSettings();
        if (currentSlide > maxSlides) currentSlide = maxSlides;
        updateCarousel();
    });

    // Initialize dots onload
    window.addEventListener("load", () => {
        var settings = getSettings();
        var dotsContainer = document.getElementById("carousel-indicators");
        dotsContainer.innerHTML = "";

        for (let i = 0; i <= settings.maxSlides; i++) { // 'let' use karanna
            var dot = document.createElement("button");
            dot.onclick = function() {
                goToSlide(i);
            };
            dot.className = `carousel-dot w-3 h-3 rounded-full transition-all ${i == 0 ? "bg-blue-600" : "bg-gray-300"}`;
            dotsContainer.appendChild(dot);
        }
    });
</script>


<?php include("footer.php"); ?>