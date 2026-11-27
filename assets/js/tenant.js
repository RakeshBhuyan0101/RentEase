$(document).ready(function () {

    let cachedProperties = [];
    let cachedBookings = [];

    // Set min date on move-in date picker to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split("T")[0];
    $("#bookMoveInDate").attr("min", tomorrowStr).val(tomorrowStr);


    // ==========================================
    // 1. TAB SWITCHING
    // ==========================================
    window.switchTenantTab = function (tab) {
        $(".dash-tab").removeClass("active");
        $("#tabTenantExploreSection").addClass("d-none");
        $("#tabTenantBookingsSection").addClass("d-none");

        if (tab === "explore") {
            $("#tabTenantExploreBtn").addClass("active");
            $("#tabTenantExploreSection").removeClass("d-none");
            $("#tenantMainHeading").text("Explore Properties");
            loadTenantProperties();
        } else if (tab === "bookings") {
            $("#tabTenantBookingsBtn").addClass("active");
            $("#tabTenantBookingsSection").removeClass("d-none");
            $("#tenantMainHeading").text("My Bookings");
            loadTenantBookings();
        }
    };


    // ==========================================
    // 2. EXPLORE PROPERTIES & SEARCH
    // ==========================================
    function loadTenantProperties() {
        const search = $("#tenantSearchInput").val().trim();
        const city = $("#tenantCityFilter").val();
        const bhk = $("#tenantBhkFilter").val();
        const maxRent = $("#tenantBudgetFilter").val();

        $.ajax({
            url: "/RentEase/api/tenant/searchProperty.php",
            type: "GET",
            data: {
                search: search,
                city: city,
                bhk_type: bhk,
                max_rent: maxRent
            },
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load properties:", res.message);
                    return;
                }

                cachedProperties = res.properties || [];

                // Populate city dropdown once if empty
                if ($("#tenantCityFilter option").length <= 1 && res.cities) {
                    res.cities.forEach(function (c) {
                        $("#tenantCityFilter").append(`<option value="${escapeHtml(c)}">${escapeHtml(c)}</option>`);
                    });
                }

                $("#tenantResultsText").text(`Showing ${cachedProperties.length} verified available properties`);
                renderTenantProperties(cachedProperties);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error searching properties:", error);
            }
        });
    }

    function renderTenantProperties(list) {
        const grid = $("#tenantPropertiesGrid");
        grid.empty();

        if (!list || list.length === 0) {
            grid.append(`
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                    No properties found matching your search preferences.<br>
                    Try adjusting the budget, BHK or city filters.
                </div>
            `);
            return;
        }

        list.forEach(function (prop) {
            const rentFormatted = Number(prop.monthly_rent || 0).toLocaleString("en-IN");
            const depFormatted = Number(prop.security_deposit || 0).toLocaleString("en-IN");
            
            // Image handling
            let imageSrc = "/RentEase/assets/images/default-apt.jpg";
            if (prop.cover_image && prop.cover_image !== "default-apt.jpg") {
                imageSrc = "/RentEase/uploads/properties/" + prop.cover_image;
            }

            // Amenities tags
            let tagsHtml = "";
            if (prop.amenities) {
                const amenitiesList = prop.amenities.split(",").map(a => a.trim()).filter(Boolean).slice(0, 3);
                amenitiesList.forEach(function (a) {
                    tagsHtml += `<span class="prop-tag"><i class="bi bi-check2 me-1"></i>${escapeHtml(a)}</span>`;
                });
            }

            const card = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="prop-card">
                        <div class="prop-img-box">
                            <img src="${imageSrc}" alt="${escapeHtml(prop.title)}" onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&auto=format&fit=crop&q=60'">
                            <span class="prop-badge-floating">${escapeHtml(prop.bhk_type)}</span>
                        </div>
                        <div class="prop-body">
                            <div class="prop-rent-highlight mb-1">
                                ₹${rentFormatted} <span>/ month</span>
                            </div>
                            <div class="prop-title-link">${escapeHtml(prop.title)}</div>
                            <div class="prop-location-sub">
                                <i class="bi bi-geo-alt me-1"></i>${escapeHtml(prop.locality)}, ${escapeHtml(prop.city)}
                            </div>

                            <div class="prop-tags">
                                <span class="prop-tag">${escapeHtml(prop.furnishing)}</span>
                                ${tagsHtml}
                            </div>

                            <div class="small text-muted mb-3">
                                Security Deposit: <strong>₹${depFormatted}</strong>
                            </div>

                            <button class="btn btn-book-prop btn-book-trigger"
                                    data-id="${prop.property_id}"
                                    data-title="${escapeHtml(prop.title)}"
                                    data-rent="₹${rentFormatted}/mo"
                                    data-deposit="₹${depFormatted}">
                                Request to Book
                            </button>
                        </div>
                    </div>
                </div>
            `;
            grid.append(card);
        });
    }

    // Live search and filter events
    $("#tenantSearchInput").on("input", debounce(loadTenantProperties, 300));
    $("#tenantCityFilter, #tenantBhkFilter, #tenantBudgetFilter").on("change", loadTenantProperties);

    $("#btnResetFilters").on("click", function () {
        $("#tenantSearchInput").val("");
        $("#tenantCityFilter").val("");
        $("#tenantBhkFilter").val("");
        $("#tenantBudgetFilter").val("0");
        loadTenantProperties();
    });


    // ==========================================
    // 3. BOOK PROPERTY MODAL & SUBMISSION
    // ==========================================
    $(document).on("click", ".btn-book-trigger", function () {
        const propId = $(this).data("id");
        const title = $(this).data("title");
        const rent = $(this).data("rent");
        const deposit = $(this).data("deposit");

        $("#bookPropId").val(propId);
        $("#modalPropTitle").text(title);
        $("#modalPropRent").text(rent);
        $("#modalPropDeposit").text(deposit);
        $("#bookMessage").val("");

        $("#bookPropertyModal").modal("show");
    });

    $("#bookPropertyForm").on("submit", function (e) {
        e.preventDefault();

        const submitBtn = $("#btnSubmitBooking");
        submitBtn.prop("disabled", true).text("Submitting...");

        $.ajax({
            url: "/RentEase/api/tenant/createBooking.php",
            type: "POST",
            data: {
                property_id: $("#bookPropId").val(),
                move_in_date: $("#bookMoveInDate").val(),
                lease_months: $("#bookLeaseMonths").val(),
                tenant_message: $("#bookMessage").val()
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert(res.message);
                    $("#bookPropertyModal").modal("hide");
                    // Reload bookings & update tab counter
                    loadTenantBookings();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function (xhr, status, error) {
                alert("Server error: " + error);
            },
            complete: function () {
                submitBtn.prop("disabled", false).text("Submit Booking Request");
            }
        });
    });


    // ==========================================
    // 4. MY BOOKINGS MANAGEMENT
    // ==========================================
    function loadTenantBookings() {
        $.ajax({
            url: "/RentEase/api/tenant/getBookings.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) return;

                cachedBookings = res.bookings || [];
                const counts = res.counts || { all: 0, pending: 0, approved: 0, rejected: 0, cancelled: 0 };

                // Update badge in tab
                $("#tenantBookingCountBadge").text(counts.all || 0);

                // Update filter pills
                $("#tenantBookingFilterPills").html(`
                    <button class="filter-pill active" onclick="filterTenantBookings('all', this)">All (${counts.all})</button>
                    <button class="filter-pill" onclick="filterTenantBookings('pending', this)">Pending (${counts.pending})</button>
                    <button class="filter-pill" onclick="filterTenantBookings('approved', this)">Approved (${counts.approved})</button>
                    <button class="filter-pill" onclick="filterTenantBookings('rejected', this)">Rejected (${counts.rejected})</button>
                    <button class="filter-pill" onclick="filterTenantBookings('cancelled', this)">Cancelled (${counts.cancelled})</button>
                `);

                renderTenantBookings(cachedBookings);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error loading bookings:", error);
            }
        });
    }

    function renderTenantBookings(list) {
        const grid = $("#tenantBookingsGrid");
        grid.empty();

        if (!list || list.length === 0) {
            grid.append(`
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    You haven't requested any property bookings yet.<br>
                    Browse available listings under <strong>Explore Properties</strong>!
                </div>
            `);
            return;
        }

        list.forEach(function (b) {
            const status = (b.booking_status || "pending").toLowerCase();
            const statusClass = "badge-" + status;
            const rentFormatted = Number(b.monthly_rent || 0).toLocaleString("en-IN");

            let moveInDateFormatted = b.move_in_date;
            try {
                moveInDateFormatted = new Date(b.move_in_date).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
            } catch (e) {}

            let contactInfo = "Hidden until approved";
            if (status === "approved") {
                contactInfo = `${escapeHtml(b.owner_phone || "Phone verified")} • ${escapeHtml(b.owner_email)}`;
            }

            let actionBtn = "";
            if (status === "pending") {
                actionBtn = `
                    <button class="btn btn-outline-danger btn-sm w-100 btn-cancel-booking mt-3" data-id="${b.booking_id}" style="border-radius:6px; font-weight:600;">
                        Cancel Request
                    </button>
                `;
            }

            const card = `
                <div class="col-12 col-lg-6" data-status="${escapeHtml(status)}">
                    <div class="tenant-booking-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 fs-6">${escapeHtml(b.property_title)}</h5>
                                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>${escapeHtml(b.locality)}, ${escapeHtml(b.city)} (${escapeHtml(b.bhk_type)})</div>
                            </div>
                            <span class="badge-status ${statusClass}">${escapeHtml(status.toUpperCase())}</span>
                        </div>

                        <div class="booking-specs-box mb-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="spec-label">MONTHLY RENT</div>
                                    <div class="spec-val">₹${rentFormatted}/mo</div>
                                </div>
                                <div class="col-6">
                                    <div class="spec-label">MOVE-IN DATE</div>
                                    <div class="spec-val">${escapeHtml(moveInDateFormatted)}</div>
                                </div>
                                <div class="col-6">
                                    <div class="spec-label">OWNER</div>
                                    <div class="spec-val">${escapeHtml(b.owner_name)}</div>
                                </div>
                                <div class="col-6">
                                    <div class="spec-label">OWNER CONTACT</div>
                                    <div class="spec-val small">${contactInfo}</div>
                                </div>
                            </div>
                        </div>

                        ${b.tenant_message ? `<div class="tenant-note mb-2">"${escapeHtml(b.tenant_message)}"</div>` : ''}

                        ${actionBtn}
                    </div>
                </div>
            `;
            grid.append(card);
        });
    }

    // Filter Bookings by status pill
    window.filterTenantBookings = function (status, elem) {
        $("#tenantBookingFilterPills .filter-pill").removeClass("active");
        $(elem).addClass("active");

        $("#tenantBookingsGrid > div").each(function () {
            const cardStatus = $(this).data("status");
            if (status === "all" || cardStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };

    // Cancel Booking Handler
    $(document).on("click", ".btn-cancel-booking", function () {
        const bookingId = $(this).data("id");
        if (!confirm("Are you sure you want to cancel this booking request?")) {
            return;
        }

        $.ajax({
            url: "/RentEase/api/tenant/cancelBooking.php",
            type: "POST",
            data: { booking_id: bookingId },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert(res.message);
                    loadTenantBookings();
                } else {
                    alert("Error: " + res.message);
                }
            },
            error: function (xhr, status, error) {
                alert("Request error: " + error);
            }
        });
    });


    // Debounce helper
    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

    // Helper for HTML escaping
    function escapeHtml(str) {
        if (!str) return "";
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initial load
    loadTenantProperties();
    loadTenantBookings();

});

