<?php
session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) {
    header("Location: home.php");
    exit();
}



$email = isset($_COOKIE["skillshop_user_email"]) ? $_COOKIE["skillshop_user_email"] : "";
$rememberMe = isset($_COOKIE["skillshop_remember"]) ? true : false;
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/skg-02.png" type="images/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
    <title>Skillshop - Authentication</title>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        <!-- auth container  -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">

            <!-- header section  -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-10 text-center text-white">
                <h1 class="text-4xl font-bold mb-2">Skillshop</h1>
                <p class="text-blue-100">Buy and sell skills with confidence</p>
            </div>

            <!-- form container  -->
            <div class="px-8 py-8">

                <!-- sign In form  -->
                <div id="signin-form" class="form-container active">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back</h2>
                    <p class="text-gray-600 mb-6">Sign in to your account to continue</p>

                    <form id="signin" class="space-y-4">
                        <input type="hidden" name="action" value="signin" />

                        <!-- email input  -->
                        <div>
                            <label for="signin-email" class="block text-gray-700 text-sm font-semibold mb-2">Email
                                Address</label>
                            <input type="email" name="email" id="signin-email" placeholder="you@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 
                                focus:ring-blue-500 focus:border-transparent transition duration-200"
                                value="<?php echo $email; ?>">
                        </div>

                        <!-- Password Layout  -->
                        <div class="relative">
                            <label for="signin-password"
                                class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                            <input type="password" name="password" id="signin-password" placeholder="**************"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 
                                focus:ring-blue-500 focus:border-transparent transition duration-200">

                            <button type="button" onclick="togglePassword('signin-password', this);" class="absolute right-3 top-[38px] text-gray-500
                                focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-1"
                                aria-label="toggle-password-visibility" aria-pressed="false">
                                👁️
                            </button>
                        </div>

                        <!-- Remember Me & Forget Password section  -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 rounded"
                                    <?php if ($rememberMe) echo "checked"; ?> />
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <button type="button" onclick="openForgotPasswordModal();" class="text-sm text-blue-600 hover:text-blue-700 font-medium bg-none border-none cursor-pointor">Forgot Password</button>

                        </div>

                        <button type="button" onclick="signIn();" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-3 rounded-lg hover:from-blue-700 
                        hover:to-indigo-700 transition duration-300 transform hover:scale-105 mt-6">Sign In</button>

                        <!-- Error Message  -->
                        <div id="signIn-message" class="hidden text-red-500 mt-4 p-3 rounded-lg text-sm "></div>
                    </form>

                    <p class="text-center text-gray-600 mt-6">
                        Don't have an account?
                        <button type="button" class="text-blue-600 hover:text-blue-700 font-bold cursor-pointer"
                            onclick="toggleForms();">Sign Up</button>
                    </p>
                </div>

                <!-- Sign Up Form  -->
                <div id="signup-form" class="form-container hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Create Account</h2>
                    <p class="text-gray-600 mb-6">Join thousands of skill sellers and buyers</p>

                    <form id="signup" class="space-y-4">
                        <input type="hidden" name="action" value="signup" />

                        <!-- first name layout   -->
                        <div>
                            <label for="signup-firstname" class="block text-gray-700 text-sm font-semibold mb-2">First
                                Name</label>
                            <input type="text" name="firstname" id="signup-firstname" placeholder="Sahan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                            <span class="text-red-500 text-sm hidden" id="signup-firstname-error"></span>
                        </div>

                        <!-- last name layout   -->
                        <div>
                            <label for="signup-lastname" class="block text-gray-700 text-sm font-semibold mb-2">Last
                                Name</label>
                            <input type="text" name="lastname" id="signup-lastname" placeholder="Perera" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                            <span class="text-red-500 text-sm hidden" id="signup-lastname-error"></span>
                        </div>

                        <!-- email layout   -->
                        <div>
                            <label for="signup-email" class="block text-gray-700 text-sm font-semibold mb-2">Email
                                Address</label>
                            <input type="email" name="email" id="signup-email" placeholder="you@example.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                value="<?php echo $email ?>">
                            <span class="text-red-500 text-sm hidden" id="signup-email-error"></span>
                        </div>

                        <!-- password input  -->
                        <div class="relative">
                            <label for="signup-password"
                                class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                            <input type="password" name="password" id="signup-password" placeholder="********" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none 
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                aria-describedby="signup-password-error" />

                            <button type="button" onclick="togglePassword('signup-password', this);" class="absolute right-3 top-[38px] text-gray-500
                                focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-1"
                                aria-label="toggle-password-visibility" aria-pressed="false">
                                👁️
                            </button>
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                            <span class="text-red-500 text-sm hidden" id="signup-password-error"></span>
                        </div>

                        <!-- confirm password input  -->
                        <div class="relative">
                            <label for="signup-confirm-password"
                                class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" id="signup-confirm-password"
                                placeholder="********" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none 
                                focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                aria-describedby="signup-confirm-error" />

                            <button type="button" onclick="togglePassword('signup-confirm-password', this);" class="absolute right-3 top-[38px] text-gray-500
                                focus:outline-none focus:ring-2 focus:ring-blue-500 rounded px-1"
                                aria-label="toggle-password-visibility" aria-pressed="false">
                                👁️
                            </button>
                            <span class="text-red-500 text-sm hidden" id="signup-confirm-error"></span>
                        </div>

                        <!-- Account Type Selection  -->
                        <div>
                            <label class="block text-gray-700 text-sm font-semibold mb-3">I'm a</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="relative flex items-center cursor-pointer">
                                    <input type="radio" name="account_type" id="seller" value="seller"
                                        class="w-4 h-4 text-blue-600" />
                                    <span class="ml-2 text-sm text-gray-700">Skill Seller</span>
                                </label>
                                <label class="relative flex items-center cursor-pointer">
                                    <input type="radio" name="account_type" id="buyer" value="buyer"
                                        class="w-4 h-4 text-blue-600" />
                                    <span class="ml-2 text-sm text-gray-700">Skill Buyer</span>
                                </label>
                            </div>
                        </div>

                        <!-- Terms & Conditions  -->
                        <label for="terms_conditions" class="flex items-center">
                            <input type="checkbox" name="terms" id="terms_conditions"
                                class="w-4 h-4 text-blue-600 rounded" />
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the
                                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">Terms & Conditions</a>
                            </span>
                        </label>

                        <!-- Create Account Button  -->
                        <button type="button" onclick="createAccount();" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white
                          font-bold py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700
                          transition duration-300 transform hover:scale-105 mt-6">Create Account</button>

                        <!-- Error Message  -->
                        <div id="signup-message" class="text-red-500 hidden mt-4 p-3 rounded-lg text-sm"></div>
                    </form>

                    <p class="text-center text-gray-600 mt-6">
                        Already have an account?
                        <button type="button" class="text-blue-600 hover:text-blue-700 font-bold cursor-pointer"
                            onclick="toggleForms();">Sign In</button>
                    </p>
                </div>

            </div>
        </div>

        <!-- Footer  -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 text-center text-gray-600 text-sm mt-4 rounded-xl">
            <p>&copy; 2026 SkillShop. All Rights Reserved |
                <a href="#" class="text-blue-600 hover:text-blue-700">Privacy Policy</a> |
                <a href="#" class="text-blue-600 hover:text-blue-700">Terms of Service</a>
            </p>
        </div>



        <!-- Forgot Password Model  -->
        <div id="forgot-password-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center
                     justify-center p-4">

            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 text-white flex justify-between
                                items-center">
                    <h3 class="text-xl font-bold">Forgot Password</h3>
                    <button type="button" onclick="closeForgotPasswordModal();" class="text-white hover:text-gray-200">X</button>
                </div>

                <!-- Email Entry -->
                <div class="hidden p-6" id="forgot-step-1">
                    <p class="text-gray-600 mb-4">Enter Your Email To Receive the verification Code.</p>
                    <div class="mb-4">
                        <label for="forgot-email" class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                        <input type="email" id="forgot-email" placeholder="your@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2
                                    focus:ring-blue-500" />
                    </div>
                    <div id="forgot-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
                    <div class="flex gap-3">
                        <button type="button" onclick="forgotPassword();" id="forgot-password-send-code-btn" class="flex-1 bg-gradient-to-r
                                   from-blue-600 to-indigo-600 text-white font-semibold py-2 rounded-lg hover:from-blue-700
                                   hover:to-indigo-700">Send Code</button>
                        <button type="button" onclick="closeForgotPasswordModal();" class="flex-1 text-gray-700
                                bg-gray-300 font-semibold py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                    </div>
                </div>

                <!-- Setep 2: Verification Code -->
                <div class="hidden p-6" id="forgot-step-2">
                    <p class="text-gray-600 mb-4">Enter Your 6-digit Code Sent to Your Email</p>
                    <div class="mb-4">
                        <label for="verify-code" class="block text-gray-700 text-sm font-semibold mb-2">Verification Code</label>
                        <input type="text" id="verify-code" placeholder="000000" maxlength="6"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2
                                    focus:ring-blue-500 text-center text-2xl tracking-widest" />
                    </div>
                    <div id="verify-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                    <div class="flex gap-3">
                        <button type="button" onclick="verifyCode();" class="flex-1 bg-gradient-to-r
                                   from-blue-600 to-indigo-600 text-white font-semibold py-2 rounded-lg hover:from-blue-700
                                   hover:to-indigo-700">Verify</button>
                        <button type="button" onclick="backtoEmail();" class="flex-1 text-gray-700
                                bg-gray-300 font-semibold py-2 rounded-lg hover:bg-gray-400">Back</button>
                    </div>
                </div>

                <!-- Reset password-->
                <div class="hidden p-6" id="forgot-step-3">
                    <p class="text-gray-600 mb-4">Enter Your New Password.</p>
                    <div class="mb-4">
                        <label for="reset-password" class="block text-gray-700 text-sm font-semibold mb-2">New Password</label>
                        <input type="text" id="reset-password" placeholder="******"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2
                                    focus:ring-blue-500 " />
                    </div>
                    <div class="mb-4">
                        <label for="reset-password-confirm" class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                        <input type="text" id="reset-password-confirm" placeholder="******"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2
                                    focus:ring-blue-500 " />
                    </div>
                    <div id="reset-message" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
                    <div class="flex gap-3">
                        <button type="button" onclick="resetPassword();" class="flex-1 bg-gradient-to-r
                                   from-blue-600 to-indigo-600 text-white font-semibold py-2 rounded-lg hover:from-blue-700
                                   hover:to-indigo-700">Reset Password</button>
                        <button type="button" onclick="closeForgotPasswordModal();" class="flex-1 text-gray-700
                                bg-gray-300 font-semibold py-2 rounded-lg hover:bg-gray-400">Cancel</button>
                    </div>


                </div>



            </div>

        </div>

    </div>

    </div>
    <script src="js/auth.js"></script>
    <script src="js/script.js"></script>
</body>

</html>