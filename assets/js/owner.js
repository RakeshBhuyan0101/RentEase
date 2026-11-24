$(document).ready(function () {

    // ==========================================
    // 1. GLOBAL TAB SWITCHING
    // ==========================================
    window.switchTab = function (tab) {
        if (tab === "properties") {
            $("#tabPropertiesSection").removeClass("d-none");
            $("#tabBookingsSection").addClass("d-none");
            $("#tabPropertiesBtn").addClass("active");
            $("#tabBookingsBtn").removeClass("active");
            $("#pageMainHeading").text("My Properties");
        } else {
            $("#tabPropertiesSection").addClass("d-none");
            $("#tabBookingsSection").removeClass("d-none");
            $("#tabBookingsBtn").addClass("active");
            $("#tabPropertiesBtn").removeClass("active");
            $("#pageMainHeading").text("Booking Requests");
        }
    };

    $("#tabPropertiesBtn").on("click", function () {
        window.switchTab("properties");
    });

    $("#tabBookingsBtn").on("click", function () {
        window.switchTab("bookings");
    });


    // ==========================================
    // 2. FETCH & RENDER PROPERTIES (API CALL)
    // ==========================================
    function loadProperties() {
        $.ajax({
            url: "../../api/owner/getProperties.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load properties:", res.message);
                    return;
                }

                // Update counts on filter pills & units badge
                const counts = res.counts || { all: 0, approved: 0, pending: 0, rejected: 0 };
                updatePropFilterPills(counts);
                $("#propUnitsBadge").text(counts.all + " UNITS");

                // Render Table Rows
                const tbody = $("#propertiesTableBody");
                tbody.empty();

                if (!res.properties || res.properties.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-buildings fs-1 d-block mb-2"></i>
                                You haven't listed any properties yet.<br>
                                Click <strong>+ Add Property</strong> to create your first listing!
                            </td>
                        </tr>
                    `);
                    return;
                }

                res.properties.forEach(function (prop) {
                    const statusClass = "badge-" + prop.approval_status.toLowerCase();
                    const initial = (prop.title || "P").charAt(0).toUpperCase();
                    const formattedRent = Number(prop.monthly_rent || 0).toLocaleString("en-IN");
                    const formattedDep = Number(prop.security_deposit || 0).toLocaleString("en-IN");

                    const rowHtml = `
                        <tr data-status="${escapeHtml(prop.approval_status.toLowerCase())}">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="prop-avatar avatar-blue">${initial}</div>
                                    <div>
                                        <div class="prop-title">${escapeHtml(prop.title)}</div>
                                        <div class="prop-sub">${escapeHtml(prop.locality || "")}, ${escapeHtml(prop.city || "")}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">${escapeHtml(prop.bhk_type)}</div>
                                <div class="text-muted small">${escapeHtml(prop.furnishing)}</div>
                            </td>
                            <td>
                                <div class="fw-bold">₹${formattedRent}<span class="text-muted fw-normal">/mo</span></div>
                                <div class="text-muted small">Dep: ₹${formattedDep}</div>
                            </td>
                            <td><span class="badge-status ${statusClass}">${escapeHtml(prop.approval_status.toUpperCase())}</span></td>
                            <td class="text-end">
                                <button class="btn btn-edit" data-id="${prop.property_id}">Edit</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(rowHtml);
                });
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error loading properties:", error);
            }
        });
    }

    function updatePropFilterPills(counts) {
        const pillsGroup = $("#tabPropertiesSection .filter-pills-group");
        pillsGroup.html(`
            <button class="filter-pill active" onclick="filterProperties('all', this)">All (${counts.all})</button>
            <button class="filter-pill" onclick="filterProperties('approved', this)">Approved (${counts.approved})</button>
            <button class="filter-pill" onclick="filterProperties('pending', this)">Pending (${counts.pending})</button>
            <button class="filter-pill" onclick="filterProperties('rejected', this)">Rejected (${counts.rejected})</button>
        `);
    }


    // ==========================================
    // 3. FETCH & RENDER BOOKINGS (API CALL)
    // ==========================================
    function loadBookings() {
        $.ajax({
            url: "../../api/owner/getBookings.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load bookings:", res.message);
                    return;
                }

                const counts = res.counts || { all: 0, pending: 0, approved: 0, rejected: 0 };
                
                // Update badge count on tab header
                $("#tabBookingsBtn .badge-count").text(counts.pending || 0);

                // Update filter pills for bookings
                updateBookingFilterPills(counts);

                // Render Booking Cards
                const grid = $("#bookingsGrid");
                grid.empty();

                if (!res.bookings || res.bookings.length === 0) {
                    grid.append(`
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No booking requests found for your properties.
                        </div>
                    `);
                    return;
                }

                res.bookings.forEach(function (b) {
                    const statusClass = "badge-" + b.booking_status.toLowerCase();
                    const initial = (b.tenant_name || "T").charAt(0).toUpperCase();
                    const formattedRent = Number(b.monthly_rent || 0).toLocaleString("en-IN");
                    
                    // Format Move-in Date
                    let moveInText = b.move_in_date;
                    try {
                        const dateObj = new Date(b.move_in_date);
                        moveInText = dateObj.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
                    } catch (e) {}

                    let actionButtons = `
                        <div class="d-flex flex-column gap-2 mt-3">
                            <button class="btn btn-approve w-100" data-id="${b.booking_id}">Approve Booking</button>
                            <button class="btn btn-reject w-100" data-id="${b.booking_id}">Reject Request</button>
                        </div>
                    `;

                    if (b.booking_status !== "pending") {
                        actionButtons = `
                            <div class="text-center py-2 mt-2 text-muted small fw-semibold">
                                Request was <strong>${escapeHtml(b.booking_status)}</strong>
                            </div>
                        `;
                    }

                    const cardHtml = `
                        <div class="col-12 col-lg-6" data-status="${escapeHtml(b.booking_status.toLowerCase())}">
                            <div class="booking-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="prop-avatar avatar-blue">${initial}</div>
                                        <div>
                                            <h5 class="fw-bold mb-0 fs-6">${escapeHtml(b.tenant_name)}</h5>
                                            <span class="text-muted small">${escapeHtml(b.tenant_email || "Verified Tenant")}</span>
                                        </div>
                                    </div>
                                    <span class="badge-status ${statusClass}">${escapeHtml(b.booking_status.toUpperCase())}</span>
                                </div>

                                <div class="booking-specs-box mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="spec-label">PROPERTY</div>
                                            <div class="spec-val">${escapeHtml(b.property_title)} (${escapeHtml(b.bhk_type)})</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="spec-label">RENT</div>
                                            <div class="spec-val">₹${formattedRent}/mo</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="spec-label">MOVE-IN</div>
                                            <div class="spec-val">${escapeHtml(moveInText)}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="spec-label">CONTACT</div>
                                            <div class="spec-val">${escapeHtml(b.tenant_phone || "+91 Contact")}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tenant-note mb-3">
                                    "${escapeHtml(b.tenant_message || "No message provided by tenant.")}"
                                </div>

                                ${actionButtons}
                            </div>
                        </div>
                    `;
                    grid.append(cardHtml);
                });
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error loading bookings:", error);
            }
        });
    }

    function updateBookingFilterPills(counts) {
        const pillsGroup = $("#tabBookingsSection .filter-pills-group");
        pillsGroup.html(`
            <button class="filter-pill active" onclick="filterBookings('pending', this)">Pending (${counts.pending})</button>
            <button class="filter-pill" onclick="filterBookings('approved', this)">Approved (${counts.approved})</button>
            <button class="filter-pill" onclick="filterBookings('rejected', this)">Rejected (${counts.rejected})</button>
            <button class="filter-pill" onclick="filterBookings('all', this)">All (${counts.all})</button>
        `);
    }


    // ==========================================
    // 4. LIVE SEARCH & PILL FILTERS
    // ==========================================
    $("#propSearchInput").on("input", function () {
        const query = $(this).val().toLowerCase().trim();
        $("#propertiesTableBody tr").each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(query) > -1);
        });
    });

    window.filterProperties = function (status, element) {
        $("#tabPropertiesSection .filter-pill").removeClass("active");
        $(element).addClass("active");

        $("#propertiesTableBody tr").each(function () {
            const rowStatus = $(this).data("status");
            if (status === "all" || rowStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };

    window.filterBookings = function (status, element) {
        $("#tabBookingsSection .filter-pill").removeClass("active");
        $(element).addClass("active");

        $("#bookingsGrid > div").each(function () {
            const cardStatus = $(this).data("status");
            if (status === "all" || cardStatus === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };


    // ==========================================
    // 5. AJAX: ADD NEW PROPERTY
    // ==========================================
    $("#addPropertyForm").on("submit", function (e) {
        e.preventDefault();

        const submitBtn = $(".btn-save-prop");
        submitBtn.prop("disabled", true).text("Saving...");

        const formData = new FormData();
        formData.append("title", $("#addPropTitle").val().trim());
        formData.append("description", $("#addPropDesc").val().trim());
        formData.append("city", $("#addPropCity").val().trim());
        formData.append("locality", $("#addPropLocality").val().trim());
        formData.append("full_address", $("#addPropAddress").val().trim());
        formData.append("bhk_type", $("#addPropBhk").val());
        formData.append("furnishing", $("#addPropFurnish").val());
        formData.append("monthly_rent", $("#addPropRent").val());
        formData.append("security_deposit", $("#addPropDeposit").val());
        formData.append("amenities", $("#addPropAmenities").val().trim());

        const coverFile = $("#coverImageInput")[0]?.files[0];
        if (coverFile) {
            formData.append("cover_image", coverFile);
        }

        $.ajax({
            url: "../../api/owner/addProperty.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert("Property added successfully!");
                    $("#addPropertyForm")[0].reset();
                    $("#addPropertyModal").modal("hide");
                    // Reload properties list dynamically without full page reload
                    loadProperties();
                } else {
                    alert("Error: " + (res.message || "Failed to add property"));
                }
            },
            error: function (xhr, status, error) {
                alert("Server error: " + error);
            },
            complete: function () {
                submitBtn.prop("disabled", false).text("Save Property");
            }
        });
    });


    // ==========================================
    // 6. AJAX: APPROVE & REJECT BOOKING
    // ==========================================
    $(document).on("click", ".btn-approve", function () {
        const bookingId = $(this).data("id");
        updateBookingStatus(bookingId, "approved");
    });

    $(document).on("click", ".btn-reject", function () {
        const bookingId = $(this).data("id");
        if (confirm("Are you sure you want to reject this booking request?")) {
            updateBookingStatus(bookingId, "rejected");
        }
    });

    function updateBookingStatus(bookingId, status) {
        $.ajax({
            url: "../../api/owner/updateBookingStatus.php",
            type: "POST",
            data: {
                booking_id: bookingId,
                status: status
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert("Booking " + status + " successfully!");
                    // Reload bookings list dynamically
                    loadBookings();
                } else {
                    alert("Failed: " + res.message);
                }
            },
            error: function (xhr, status, error) {
                alert("Request error: " + error);
            }
        });
    }

    // Helper to prevent XSS
    function escapeHtml(str) {
        if (!str) return "";
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // ==========================================
    // 7. INITIAL DATA LOAD ON PAGE START
    // ==========================================
    loadProperties();
    loadBookings();

});
