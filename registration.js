document.addEventListener("DOMContentLoaded", () => {
    let currentPage = 1;
    const totalPages = 1; // Assuming a single-page form for now. Adjust if adding more pages.

    // Function to switch pages (useful for multi-step forms)
    function switchPage(pageNumber) {
        const pages = document.querySelectorAll(".page");
        pages.forEach((page, index) => {
            page.classList.toggle("active", index + 1 === pageNumber);
        });
        currentPage = pageNumber;
    }

    // Function to validate form data
    function collectAndValidateFormData() {
        const form = document.getElementById("signupForm");
        const firstName = form.firstName.value.trim();
        const middleInitial = form.middleInitial.value.trim();
        const lastName = form.lastName.value.trim();
        const rsbaNumber = form.rsbaNumber.value.trim();
        const contactNumber = form.contactNumber.value.trim();
        const barangay = form.barangay.value.trim();
        const homeAddress = form.homeAddress.value.trim();
        const farmSize = form.farmSize.value.trim();
        const farmUnit = form.farmUnit.value.trim();
        const farmLocation = form.farmLocation.value.trim();
        const password = form.password.value.trim();
        const confirmPassword = form.confirmPassword.value.trim();

        const errors = [];

        // Check if all fields are filled
        if (
            !firstName ||
            !middleInitial ||
            !lastName ||
            !rsbaNumber ||
            !contactNumber ||
            !barangay ||
            !homeAddress ||
            !farmSize ||
            !farmUnit ||
            !farmLocation ||
            !password ||
            !confirmPassword
        ) {
            errors.push("All fields are required.");
        }

        // Validate contact number format (+63 followed by 10 digits)
        const contactRegex = /^\+63\d{10}$/;
        if (!contactRegex.test(contactNumber)) {
            errors.push("Invalid contact number format. Use +63 followed by 10 digits.");
        }

        // Check if passwords match
        if (password !== confirmPassword) {
            errors.push("Passwords do not match.");
        }

        // Validate password strength
        const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!passwordRegex.test(password)) {
            errors.push("Password must be at least 8 characters, include an uppercase letter and a number.");
        }

        if (errors.length > 0) {
            alert(errors.join("\n"));
            return null;
        }

        // Return collected data
        return {
            first_name: firstName,
            middle_initial: middleInitial,
            last_name: lastName,
            rsba_number: rsbaNumber,
            contact_number: contactNumber,
            barangay: barangay,
            home_address: homeAddress,
            farm_size: farmSize,
            farm_unit: farmUnit,
            farm_location: farmLocation,
            password: password,
            status: "pending", // Set status to "Pending" by default
        };
    }

    // Form submission listener
    document.getElementById("signupForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = collectAndValidateFormData();

        if (!formData) return;

        // Submit data to the backend
        fetch("signup.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(formData),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Signup successful! Your status is pending approval.");
                    // Optionally redirect to another page
                    window.location.href = "user.html";
                } else {
                    alert("Signup failed. Please try again.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred. Please try again later.");
            });
    });
});
