const menuBtn = document.getElementById("menu-btn");
const navbar = document.querySelector(".navbar");
const closeBtn = document.getElementById("nav-close");

menuBtn.onclick = function () {
    navbar.classList.add("active");
};

closeBtn.onclick = function () {
    navbar.classList.remove("active");
};

document.querySelectorAll(".navbar a").forEach(function (link) {
    link.onclick = function () {
        navbar.classList.remove("active");
    };
});



// LOGIN
const loginLink = document.querySelector('a[href="#login"]');
const loginSection = document.getElementById('login');

if (loginLink && loginSection) {
    loginLink.onclick = function (e) {
        e.preventDefault();

        loginSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    };
}

// Tour Navbar Dropdown

const tourButtons = document.querySelectorAll(".tour-btn");

tourButtons.forEach(function(button) {

    button.addEventListener("click", function(e) {

        e.stopPropagation();

        const dropdown = this.nextElementSibling;

        document.querySelectorAll(".tour-dropdown").forEach(function(menu) {
            if (menu !== dropdown) {
                menu.style.display = "none";
            }
        });

        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "block";
        }

    });

});

document.addEventListener("click", function() {
    document.querySelectorAll(".tour-dropdown").forEach(function(menu) {
        menu.style.display = "none";
    });
});

const searchButton = document.getElementById("searchButton");
const searchInput = document.getElementById("searchInput");

if (searchButton && searchInput) {
    searchButton.onclick = function () {

        let searchText = searchInput.value.trim().toLowerCase();

        if (searchText === "") {
            alert("Please enter a destination!");
        }
        else if (searchText.includes("adventure")) {
            document.getElementById("category").scrollIntoView({
                behavior: "smooth"
            });
        }
        else if (searchText.includes("package")) {
            document.getElementById("packages").scrollIntoView({
                behavior: "smooth"
            });
        }
        else if (searchText.includes("book")) {
            document.getElementById("booking").scrollIntoView({
                behavior: "smooth"
            });
        }
        else {
            alert("Destination not found!");
        }
    };
}

// SHOW ALL PACKAGES

function showAllPackages() {

    const cards = document.querySelectorAll(".travel-card");

    cards.forEach(function(card) {
        card.style.display = "";
    });

    document.getElementById("packages").scrollIntoView({
        behavior: "smooth"
    });
}


// FILTER PACKAGES

function filterPackages(category) {

    const cards = document.querySelectorAll(".travel-card");

    cards.forEach(function(card) {

        const categories = card.getAttribute("data-category");

        if (categories && categories.includes(category)) {

            card.style.display = "";

        } else {

            card.style.display = "none";

        }

    });

    document.getElementById("packages").scrollIntoView({
        behavior: "smooth"
    });
}

// ===============================
// PACKAGE DETAILS DATA
// ===============================

const packageData = {

    mahabaleshwar: {
        title: "Mahabaleshwar",
        image: "images/mahabaleshwar.jpeg",
        duration: "3 Days / 2 Nights",
        price: "₹6,999*",
        description: "Explore the beautiful hill station of Mahabaleshwar with scenic valleys, waterfalls and famous viewpoints.",
        itinerary: [
            "Day 1: Arrival at Mahabaleshwar and local sightseeing",
            "Day 2: Explore Venna Lake, Mapro Garden and viewpoints",
            "Day 3: Visit Panch Ganga Temple and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Daily breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Lunch and dinner",
            "Entry fees"
        ]
    },

    lonavala: {
        title: "Lonavala",
        image: "images/lonavala.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,999*",
        description: "Enjoy the scenic beauty of Lonavala with hills, waterfalls, lakes and beautiful viewpoints.",
        itinerary: [
            "Day 1: Arrival and visit Bhushi Dam and Lonavala Lake",
            "Day 2: Visit Tiger Point, Rajmachi Point and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Local sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry tickets",
            "Meals other than breakfast"
        ]
    },

    matheran: {
        title: "Matheran",
        image: "images/matheran.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,499*",
        description: "Experience the peaceful hill station of Matheran surrounded by greenery and beautiful viewpoints.",
        itinerary: [
            "Day 1: Arrival and Matheran local sightseeing",
            "Day 2: Visit Echo Point, Panorama Point and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Horse riding charges",
            "Entry fees"
        ]
    },

    panchgani: {
        title: "Panchgani",
        image: "images/panchgani.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,999*",
        description: "Discover Panchgani's beautiful hills, valleys, viewpoints and strawberry farms.",
        itinerary: [
            "Day 1: Arrival and sightseeing",
            "Day 2: Visit Table Land, Sydney Point and departure"
        ],
        includes: [
            "Hotel stay",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry fees",
            "Additional activities"
        ]
    },

    igatpuri: {
        title: "Igatpuri",
        image: "images/igatpuri.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,499*",
        description: "Enjoy the mountains, waterfalls and peaceful natural surroundings of Igatpuri.",
        itinerary: [
            "Day 1: Arrival and local sightseeing",
            "Day 2: Visit Bhatsa River Valley and nearby attractions"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry charges",
            "Additional activities"
        ]
    },

    bhandardara: {
        title: "Bhandardara",
        image: "images/bhandardara.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,999*",
        description: "Explore the natural beauty of Bhandardara with lakes, mountains and trekking spots.",
        itinerary: [
            "Day 1: Arrival and Bhandardara Lake sightseeing",
            "Day 2: Visit Randha Falls and nearby attractions"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Trekking charges",
            "Entry fees"
        ]
    },

    rajgad: {
        title: "Rajgad",
        image: "images/rajgad.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹3,999*",
        description: "Experience the historic Rajgad Fort and enjoy an exciting trekking adventure.",
        itinerary: [
            "Day 1: Arrival and Rajgad Fort trek",
            "Day 2: Explore nearby attractions and departure"
        ],
        includes: [
            "Accommodation",
            "Breakfast",
            "Trekking guidance",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Trekking equipment",
            "Additional activities"
        ]
    },

    sinhagad: {
        title: "Sinhagad",
        image: "images/sinhagad.jpeg",
        duration: "1 Day",
        price: "₹1,999*",
        description: "Visit the historic Sinhagad Fort and enjoy a memorable one-day trekking experience.",
        itinerary: [
            "Morning: Departure for Sinhagad",
            "Afternoon: Fort trekking and sightseeing",
            "Evening: Return journey"
        ],
        includes: [
            "Transportation",
            "Sightseeing",
            "Trek guidance"
        ],
        excludes: [
            "Personal expenses",
            "Food expenses",
            "Entry fees"
        ]
    },

    alibaug: {
        title: "Alibaug",
        image: "images/alibaug.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,999*",
        description: "Enjoy the beautiful beaches, forts and coastal attractions of Alibaug.",
        itinerary: [
            "Day 1: Arrival and Alibaug Beach",
            "Day 2: Visit Kolaba Fort and nearby attractions"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Water sports",
            "Entry fees"
        ]
    },

    tarkarli: {
        title: "Tarkarli",
        image: "images/tarkarli.jpeg",
        duration: "3 Days / 2 Nights",
        price: "₹7,999*",
        description: "Enjoy the beautiful beaches, water sports and peaceful coastal atmosphere of Tarkarli.",
        itinerary: [
            "Day 1: Arrival and Tarkarli Beach",
            "Day 2: Water sports and Sindhudurg Fort",
            "Day 3: Local sightseeing and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Water sports charges",
            "Entry fees"
        ]
    },

    ganpatipule: {
        title: "Ganpatipule",
        image: "images/ganpatipule.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,999*",
        description: "Visit the famous Ganpatipule Temple and enjoy the beautiful Konkan coastline.",
        itinerary: [
            "Day 1: Arrival and Ganpatipule Beach",
            "Day 2: Visit Ganpatipule Temple and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry fees",
            "Additional activities"
        ]
    },

    ajanta: {
        title: "Ajanta & Ellora",
        image: "images/ajanta-ellora.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹5,999*",
        description: "Explore the magnificent Ajanta and Ellora Caves, famous for their ancient art and architecture.",
        itinerary: [
            "Day 1: Visit Ajanta Caves",
            "Day 2: Visit Ellora Caves and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry tickets",
            "Guide charges"
        ]
    },

    shirdi: {
        title: "Shirdi",
        image: "images/shirdi.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹3,999*",
        description: "Visit the sacred town of Shirdi and experience the famous Sai Baba Temple.",
        itinerary: [
            "Day 1: Arrival and Sai Baba Temple visit",
            "Day 2: Visit nearby temples and departure"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Temple sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Special darshan charges",
            "Additional activities"
        ]
    },

    trimbakeshwar: {
        title: "Trimbakeshwar",
        image: "images/trimbakeshwar.jpeg",
        duration: "1 Day",
        price: "₹2,499*",
        description: "Visit the famous Trimbakeshwar Jyotirlinga Temple and explore the surrounding natural beauty.",
        itinerary: [
            "Morning: Departure for Trimbakeshwar",
            "Afternoon: Temple visit and local sightseeing",
            "Evening: Return journey"
        ],
        includes: [
            "Transportation",
            "Sightseeing",
            "Trip assistance"
        ],
        excludes: [
            "Personal expenses",
            "Darshan charges",
            "Food expenses"
        ]
    },

    kolhapur: {
        title: "Kolhapur",
        image: "images/kolhapur.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹4,499*",
        description: "Explore Kolhapur's temples, historical attractions and famous local culture.",
        itinerary: [
            "Day 1: Arrival and Mahalaxmi Temple visit",
            "Day 2: Visit Panhala Fort and local attractions"
        ],
        includes: [
            "Hotel accommodation",
            "Breakfast",
            "Sightseeing",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Entry fees",
            "Shopping expenses"
        ]
    },

    kolad: {
        title: "Kolad",
        image: "images/kolad.jpeg",
        duration: "2 Days / 1 Night",
        price: "₹5,499*",
        description: "Enjoy an exciting adventure trip to Kolad with river rafting and nature activities.",
        itinerary: [
            "Day 1: Arrival and adventure activities",
            "Day 2: River rafting and departure"
        ],
        includes: [
            "Accommodation",
            "Breakfast",
            "River rafting",
            "Transportation"
        ],
        excludes: [
            "Personal expenses",
            "Additional adventure activities",
            "Entry fees"
        ]
    }

};


// ======================================
// SHOW PACKAGE DETAILS
// ======================================

function showPackage(packageName) {

    const data = packageData[packageName];

    if (!data) {
        return;
    }

    document.getElementById("detail-image").src = data.image;
    document.getElementById("detail-image").alt = data.title;

    document.getElementById("detail-title").innerText = data.title;

    document.getElementById("detail-duration").innerText =
        data.duration;

    document.getElementById("detail-price").innerText =
        data.price;

    document.getElementById("detail-description").innerText =
        data.description;


    // ITINERARY

    const itineraryBox =
        document.getElementById("detail-itinerary");

    itineraryBox.innerHTML = "";

    data.itinerary.forEach(function(item) {

        const p = document.createElement("p");

        p.innerText = item;

        itineraryBox.appendChild(p);

    });


    // INCLUDES

    const includesBox =
        document.getElementById("detail-includes");

    includesBox.innerHTML = "";

    data.includes.forEach(function(item) {

        const li = document.createElement("li");

        li.innerText = item;

        includesBox.appendChild(li);

    });


    // EXCLUDES

    const excludesBox =
        document.getElementById("detail-excludes");

    excludesBox.innerHTML = "";

    data.excludes.forEach(function(item) {

        const li = document.createElement("li");

        li.innerText = item;

        excludesBox.appendChild(li);

    });


    // SHOW DETAILS SECTION

    const details =
        document.getElementById("package-details");

    details.style.display = "block";

    details.scrollIntoView({
        behavior: "smooth"
    });
}


// ======================================
// CLOSE PACKAGE DETAILS
// ======================================

function closePackage() {

    document.getElementById("package-details").style.display = "none";

}


// ======================================
// BOOK NOW
// ======================================

function openBooking() {

    const booking =
        document.getElementById("booking");

    if (booking) {

        booking.scrollIntoView({
            behavior: "smooth"
        });

    } else {

        alert("Booking section is coming soon!");

    }

}
// ================================
// PACKAGE SEARCH
// ================================

document.getElementById("searchButton").addEventListener("click", searchPackages);

document.getElementById("searchInput").addEventListener("keyup", function(event) {
    if (event.key === "Enter") {
        searchPackages();
    }
});

function searchPackages() {

    const searchText = document
        .getElementById("searchInput")
        .value
        .toLowerCase()
        .trim();

    const packages = document.querySelectorAll(".travel-card");

    let found = false;

    packages.forEach(function(card) {

        const cardText = card.innerText.toLowerCase();

        if (searchText === "" || cardText.includes(searchText)) {
            card.style.display = "block";
            found = true;
        } else {
            card.style.display = "none";
        }

    });

    // Scroll to packages
    document.getElementById("packages").scrollIntoView({
        behavior: "smooth"
    });

    // No package found
    let noResult = document.getElementById("noSearchResult");

    if (!found && searchText !== "") {

        if (!noResult) {
            noResult = document.createElement("p");
            noResult.id = "noSearchResult";
            noResult.style.textAlign = "center";
            noResult.style.fontSize = "18px";
            noResult.style.margin = "30px";
            noResult.style.color = "#e74c3c";

            document.getElementById("packages").appendChild(noResult);
        }

        noResult.innerText = "No package found for \"" + searchText + "\"";

    } else {

        if (noResult) {
            noResult.remove();
        }
    }
}