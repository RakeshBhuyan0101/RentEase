$(document).ready(function () {

    let cachedProperties = [];

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
            url: "/RentEase/api/owner/getProperties.php",
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (!res.success) {
                    console.error("Failed to load properties:", res.message);
                    return;
                }

                cachedProperties = res.properties || [];

                // Update counts on filter pills & units badge
                const counts = res.counts || { all: 0, approved: 0, pending: 0, rejected: 0 };
                updatePropFilterPills(counts);
                $("#propUnitsBadge").text(counts.all + " UNITS");

                // Render Table Rows
                const tbody = $("#propertiesTableBody");
                tbody.empty();

                if (!cachedProperties || cachedProperties.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                                You haven't listed any properties yet.<br>
                                Click <strong>+ Add Property</strong> to create your first listing!
                            </td>
                        </tr>
                    `);
                    return;
                }

                cachedProperties.forEach(function (prop) {
                    const status = (prop.approval_status || "pending").toLowerCase();
                    const statusClass = "badge-" + status;
                    const initial = (prop.title || "P").charAt(0).toUpperCase();
                    const formattedRent = Number(prop.monthly_rent || 0).toLocaleString("en-IN");
                    const formattedDep = Number(prop.security_deposit || 0).toLocaleString("en-IN");

                    const rowHtml = `
                        <tr data-status="${escapeHtml(status)}">
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
                                <div class="fw-bold">${escapeHtml(prop.bhk_type || "2BHK")}</div>
                                <div class="text-muted small">${escapeHtml(prop.furnishing || "Semi-Furnished")}</div>
                            </td>
                            <td>
                                <div class="fw-bold">₹${formattedRent}<span class="text-muted fw-normal">/mo</span></div>
                                <div class="text-muted small">Dep: ₹${formattedDep}</div>
                            </td>
                            <td><span class="badge-status ${statusClass}">${escapeHtml((prop.approval_status || "pending").toUpperCase())}</span></td>
                            <td class="text-end">
                                <button class="btn btn-edit btn-edit-prop" data-id="${prop.property_id}">Edit</button>
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
            url: "/RentEase/api/owner/getBookings.php",
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
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            No booking requests found for your properties.
                        </div>
                    `);
                    return;
                }

                res.bookings.forEach(function (b) {
                    const status = (b.booking_status || "pending").toLowerCase();
                    const statusClass = "badge-" + status;
                    const initial = (b.tenant_name || "T").charAt(0).toUpperCase();
                    const formattedRent = Number(b.monthly_rent || 0).toLocaleString("en-IN");
                    
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

                    if (status !== "pending") {
                        actionButtons = `
                            <div class="text-center py-2 mt-2 text-muted small fw-semibold">
                                Request was <strong>${escapeHtml(b.booking_status)}</strong>
                            </div>
                        `;
                    }

                    const cardHtml = `
                        <div class="col-12 col-lg-6" data-status="${escapeHtml(status)}">
                            <div class="booking-card">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="prop-avatar avatar-blue">${initial}</div>
                                        <div>
                                            <h5 class="fw-bold mb-0 fs-6">${escapeHtml(b.tenant_name)}</h5>
                                            <span class="text-muted small">${escapeHtml(b.tenant_email || "Verified Tenant")}</span>
                                        </div>
                                    </div>
                                    <span class="badge-status ${statusClass}">${escapeHtml((b.booking_status || "pending").toUpperCase())}</span>
                                </div>

                                <div class="booking-specs-box mb-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="spec-label">PROPERTY</div>
                                            <div class="spec-val">${escapeHtml(b.property_title || "Property")} (${escapeHtml(b.bhk_type || "BHK")})</div>
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
    // 5. ONE FORM FOR BOTH ADD & EDIT PROPERTY
    // ==========================================

    // Reset modal when clicking "+ Add Property"
    $("#btnAddPropTrigger").on("click", function () {
        $("#editPropId").val("");
        $("#propModalTitle").text("Add Property");
        $("#btnSaveProp").text("Save Property");
        $("#addPropertyForm")[0].reset();
        $("#coverUploadText").text("Click to upload cover image");
        $("#morePhotosText").text("Click to upload multiple photos");
        $("#coverImageInput").val("");
        $("#morePhotosInput").val("");
    });

    // Populate form when clicking "Edit" button
    $(document).on("click", ".btn-edit-prop", function () {
        const propId = $(this).data("id");
        const prop = cachedProperties.find(p => Number(p.property_id) === Number(propId));

        if (!prop) {
            alert("Property details not found.");
            return;
        }

        $("#editPropId").val(prop.property_id);
        $("#propModalTitle").text("Edit Property");
        $("#btnSaveProp").text("Update Property");

        $("#addPropTitle").val(prop.title);
        $("#addPropDesc").val(prop.description || "");
        $("#addPropCity").val(prop.city);
        $("#addPropLocality").val(prop.locality);
        $("#addPropAddress").val(prop.full_address);
        $("#addPropBhk").val(prop.bhk_type);
        $("#addPropFurnish").val(prop.furnishing);
        $("#addPropRent").val(parseInt(prop.monthly_rent));
        $("#addPropDeposit").val(parseInt(prop.security_deposit));
        $("#addPropAmenities").val(prop.amenities || "");

        // Show existing cover image info
        if (prop.cover_image && prop.cover_image !== "default-apt.jpg") {
            $("#coverUploadText").html(`Current: <strong>${escapeHtml(prop.cover_image)}</strong> (Click to replace)`);
        } else {
            $("#coverUploadText").text("Click to upload cover image");
        }
        $("#coverImageInput").val("");

        $("#addPropertyModal").modal("show");
    });

    // Handle Image file selection feedback (safely without destroying input element)
    $(document).on("change", "#coverImageInput", function () {
        const file = this.files[0];
        if (file) {
            const sizeKb = (file.size / 1024).toFixed(1);
            $("#coverUploadText").html(`Selected: <strong>${escapeHtml(file.name)}</strong> (${sizeKb} KB)`);
        }
    });

    $(document).on("change", "#morePhotosInput", function () {
        const files = this.files;
        if (files && files.length > 0) {
            $("#morePhotosText").html(`Selected: <strong>${files.length} photo(s)</strong>`);
        }
    });

    // Form submit: Handles both Add and Update
    $("#addPropertyForm").on("submit", function (e) {
        e.preventDefault();

        const editPropVal = ($("#editPropId").val() || "").trim();
        const isEditing = editPropVal !== "";
        const submitBtn = $("#btnSaveProp");
        submitBtn.prop("disabled", true).text(isEditing ? "Updating..." : "Saving...");

        const formData = new FormData();
        if (isEditing) {
            formData.append("property_id", editPropVal);
        }

        formData.append("title", ($("#addPropTitle").val() || "").trim());
        formData.append("description", ($("#addPropDesc").val() || "").trim());
        formData.append("city", ($("#addPropCity").val() || "").trim());
        formData.append("locality", ($("#addPropLocality").val() || "").trim());
        formData.append("full_address", ($("#addPropAddress").val() || "").trim());
        formData.append("bhk_type", $("#addPropBhk").val() || "2BHK");
        formData.append("furnishing", $("#addPropFurnish").val() || "Semi-Furnished");
        formData.append("monthly_rent", $("#addPropRent").val() || "0");
        formData.append("security_deposit", $("#addPropDeposit").val() || "0");
        formData.append("amenities", ($("#addPropAmenities").val() || "").trim());

        const coverFile = $("#coverImageInput")[0]?.files[0];
        if (coverFile) {
            formData.append("cover_image", coverFile);
        }

        const targetUrl = isEditing 
            ? "/RentEase/api/owner/updateProperty.php" 
            : "/RentEase/api/owner/addProperty.php";

        $.ajax({
            url: targetUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert(res.message || (isEditing ? "Property updated successfully!" : "Property added successfully!"));
                    $("#addPropertyForm")[0].reset();
                    $("#addPropertyModal").modal("hide");
                    // Dynamically reload properties list without reloading page
                    loadProperties();
                } else {
                    alert("Error: " + (res.message || "Failed to save property"));
                }
            },
            error: function (xhr, status, error) {
                alert("Server error: " + error);
            },
            complete: function () {
                submitBtn.prop("disabled", false).text(isEditing ? "Update Property" : "Save Property");
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
            url: "/RentEase/api/owner/updateBookingStatus.php",
            type: "POST",
            data: {
                booking_id: bookingId,
                status: status
            },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    alert("Booking " + status + " successfully!");
                    // Dynamically reload bookings list without reloading page
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
