<?php
require "header.php";

if (!$loggedIn) {
    header("Location: index.php");
}

//fetch user details
$userResult = Database::search(
    "SELECT u.`id`,u.`fname`,u.`lname`,u.`email`,u.`active_account_type_id`
    FROM `user`u
    WHERE u.`id`=?",
    "i",
    [$userId]
);

$user = $userResult && $userResult->num_rows > 0 ? $userResult->fetch_assoc() : null;
$profileResult = Database::search(
    "SELECT up.`avatar_url`,up.`bio`,up.`gender_id`,up.`mobile`,up.`address_id`,
    g.name AS `gender_name`,
    a.`line1`,a.`line2`,a.`city_id`,
    c.`name` AS `city_name`,c.`country_id`,
    co.`name`AS `country_name`
    FROM `user_profile` up
    LEFT JOIN `gender` g ON up.`gender_id`=g.`id`
    LEFT JOIN `address` a ON up.`address_id`=a.`id`
    LEFT JOIN `city` c ON a.`city_id`=c.`id`
    LEFT JOIN  `country` co ON c.`country_id`=co.`id`
    WHERE up.`user_id`=?",
    "i",
    [$userId]
);

$profile = $profileResult && $profileResult->num_rows > 0 ? $profileResult->fetch_assoc() : null;



//Fetch all genders
$genderResult = Database::search("SELECT `id`,`name` FROM `gender` ORDER BY `name`");
$gender = [];
if ($genderResult && $genderResult->num_rows > 0) {
    while ($gender = $genderResult->fetch_assoc()) {
        $genders[] = $gender;
    }
}

//fetch all countries
$countriesResult = Database::search("SELECT `id`,`name` FROM `country` ORDER BY `name`");
$country = [];
if ($countriesResult && $countriesResult->num_rows > 0) {
    while ($country = $countriesResult->fetch_assoc()) {
        $countries[] = $country;
    }
}

$cities = [];
$selectedCountryId = $profile ? $profile["country_id"] : null;
if ($selectedCountryId) {
    $citiesResult = Database::search(
        "SELECT `id`,`name` FROM `city` WHERE `country_id`= ? ORDER BY `name`",
        "i",
        [$selectedCountryId]
    );
    if ($citiesResult && $citiesResult->num_rows > 0) {
        while ($city = $citiesResult->fetch_assoc()) {
            $cities[] = $city;
        }
    }
}

//Avatar URL 
$avatarUrl = $profile && $profile["avatar_url"] ? $profile["avatar_url"] : "assets/images/man.png";

$profileMsg = isset($_GET["msg"]) ? $_GET["msg"] : "";




?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- page Header  -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-600">my profile</h1>
            <p class="text-gray-600 mt-2">Update your profile information</p>
        </div>

        <!-- Success/Error Messgae  -->

        <?php if ($profileMsg): ?>
            <div id="alertBox" class="mb-6 p-4 rounded-lg
          <?php echo strpos($profileMsg, 'success') != false ? "bg-green-50 border border-green-200" : "bg-red-50 border border-red-200"; ?> ">

                <p class="<?php echo strpos($profileMsg, 'success') != false ? "text-green-800" : "text-red-800"; ?>">
                    <?php echo $profileMsg; ?>
                </p>
            </div>

        <?php endif; ?>


        <form id="profileForm" class="space-y-8">
            <!-- AVATAR SECTION  -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profile Picture</h2>
                <div class="flex items-center space-x-6">
                    <img id="avatarPreview" src="<?php echo $avatarUrl; ?>" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200" alt="" />
                    <div class="">
                        <label for="avatarFile" class="block mb-2">
                            <span class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg cursor-pointer transition-colors inline-block">
                                Choose Photo
                                <input type="file" id="avatarFile" name="avatarfile" accept="image/*" class="hidden">
                            </span>
                        </label>
                        <p class="text-sm text-gray-500">Recomended : JPG,PNG (under 5MB)</p>
                        <input type="hidden" id="avatarUrl" name="avatarUrl" value="<?php echo $avatarUrl; ?>" />
                    </div>
                </div>
            </div>



            <!-- Basic information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic information</h2>
                <div class="grid grid-col-1 sm:grid-cols-2 gap-6">
                    <!-- First Name  -->
                    <div class="">
                        <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="fname" name="fname" value="<?php echo $user["fname"]; ?>" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition" />
                        <span class="error text-red-500 text-sm hidden"></span>
                    </div>


                    <!-- Last Name  -->

                    <div class="">
                        <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="lname" name="lname" required value="<?php echo $user["lname"]; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition" />
                        <span class="error text-red-500 text-sm hidden"></span>
                    </div>

                    <!-- Email  -->

                    <div class="sm:col-span-2">
                        <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required readonly value="<?php echo $user["email"]; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition" />
                        <span class="error text-red-500 text-sm hidden"></span>
                    </div>

                </div>
            </div>

            <!-- Profile Information Section  -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-600 mb-6">Profile Information</h2>
                <div class="space-y-6">

                    <!-- Bio  -->
                    <div class="">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea name="bio" id="bio" placeholder="Tell us about yourself"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transperant outline-none transition"><?php echo $profile ? $profile["bio"] : ""; ?></textarea>
                        <p class="text-sm text-500 mt-1">Max 500 Character</p>
                        <span class="error text-red-500 text-sm hidden"></span>
                    </div>

                    <div class="grid grid-cols sm-grid-cols-2 gap-6">
                        <!-- Gender  -->
                        <div class="">
                            <label for="genderId" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="genderId" id="genderId" class="w-full px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                <option value="0">Select a Gender</option>

                                <?php foreach ($genders as $gender): ?>
                                    <option value="<?php echo $gender["id"]; ?>"
                                        <?php echo ($profile && $profile["gender_id"] == $gender["id"]) ? "selected" : ""; ?>>
                                        <?php echo $gender["name"]; ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                            <span class="error text-red-500 text-sm hidden"></span>
                        </div>
                        <!-- Mobile  -->
                        <div class="">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                            <input type="tel" id="mobile" name="mobile" placeholder="10 digits"
                                value="<?php echo $profile ? $profile["mobile"] : ""; ?>" pattern="[0-9]{10}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none-transition" />
                            <p class="text-sm text-gray-500 mt-1">Enter 10 digits only</p>
                            <span class="error text-red-500 text-sm hidden"></span>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Address Information</h2>
                            <div class="space-y-6">

                                <div>
                                    <label for="line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                                    <input type="text" id="line1" name="line1" value="<?php echo $profile ? $profile["line1"] : ""; ?>" placeholder="Street address"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                                    <span class="error text-red-500 text-sm hidden"></span>
                                </div>

                                <div>
                                    <label for="line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                                    <input type="text" id="line2" name="line2" value="<?php echo $profile ? $profile["line2"] : ""; ?>" placeholder="Apartment, suite, etc."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" />
                                    <span class="error text-red-500 text-sm hidden"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        <div>
                            <label for="countryId" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                            <select name="countryId" id="countryId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                <option value="0">Select Country</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?php echo $country["id"]; ?>"
                                        <?php echo ($profile && $profile["country_id"] == $country["id"]) ? "selected" : ""; ?>>
                                        <?php echo $country["name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error text-red-500 text-sm hidden"></span>
                        </div>

                        <div>
                            <label for="cityId" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <select name="cityId" id="cityId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                <option value="0">Select City</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo $city["id"]; ?>"
                                        <?php echo ($profile && $profile["city_id"] == $city["id"]) ? "selected" : ""; ?>>
                                        <?php echo $city["name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error text-red-500 text-sm hidden"></span>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Form Actions   -->
            <div class="flex gap-4 justify-end">
                <a href="<?php echo $userRole == "buyer" ? "buyer-dashboard.php" : "seller-dashboard.php"; ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="saveBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    //Avatar file upload Preview
    const avatarFile = document.getElementById("avatarFile");
    const avatarPreview = document.getElementById("avatarPreview");
    const avatarUrl = document.getElementById("avatarUrl");

    //AddEventlistner eken wenne deyk wenakn balan inna eka

    avatarFile.addEventListener("change", function(e) {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                avatarPreview.src = event.target.result;
                avatarUrl.value = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });


    //Country Change Event - load ciites
    const countrySelect = document.getElementById("countryId");
    const citySelect = document.getElementById("cityId");

    countrySelect.addEventListener("change", function() {
        const countryid = this.value;
        if (!countryid) {
            citySelect.innerHTML = "<option value='0'>Select City</option>";
            return;
        }

        //fetch cities for selected country
        fetch("Process/getCities.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "country_id=" + countryid
            })

            .then(response => response.json())
            .then(data => {
                citySelect.innerHTML = "<option value='0'>Select City</option>";
                if (data.success && data.cities.length > 0) {
                    data.cities.forEach(city => {
                        const option = document.createElement("option");
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                }
            }).catch(error => console.error("Error:", error));

    });





    //Form Submission 
    const profileForm = document.getElementById("profileForm");

    profileForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = document.getElementById("saveBtn");
        submitBtn.disabled = true;
        submitBtn.textContent = "Saving....";

        fetch("Process/userProfileUpdateProcess.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.textContent = "Save Changes";

                if (data.success) {
                    window.location.href = "user-profile.php?msg=" + encodeURIComponent(data.message);
                } else {
                    alert(data.message || "Error updating Profile");
                }

            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error updating profile");
                submitBtn.disabled = false;
                submitBtn.textContent = "Save Changes";
            });

    });



    //Alert Box 
    const alertBox = document.getElementById("alertBox");
    if (alertBox) {
        setTimeout(() => {
            alertBox.style.display = "none";
        }, 5000);
    }
</script>

<?php require "footer.php"; ?>