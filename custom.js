$(document).ready(function () {
    attachViewReservationHandler();
    attachCreateReservationHandler();
    attachEditReservationHandler();
});

function attachViewReservationHandler() {
    $(".view-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openViewReservationModal(reservationId);
    });
}

function openViewReservationModal(reservationId) {
    $("#viewReservationModal").modal("show");
    $("#viewReservationModal").find(".modal-body").load("view_reservation.php?id=" + reservationId);
}

function attachCreateReservationHandler() {
    $("#create-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openCreateReservationModal(reservationId);
    });
}

function openCreateReservationModal(reservationId) {
    $("#createReservationModal").modal("show");
    $("#createReservationModal").find(".modal-body").load("create_reservation.php?id=" + reservationId);
}

function attachEditReservationHandler() {
    $("#edit-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openEditReservationModal(reservationId);
    });
}

function openEditReservationModal(reservationId) {
    $("#editReservationModal").modal("show");
    $("#editReservationModal").find(".modal-body").load("edit_reservation.php?id=" + reservationId);
}

function attachViewReservationFormSubmitHandler() {
    $("#viewReservationForm").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function () {
                $("#viewReservationModal").modal("hide");
                location.reload();
            },
            error: function () {
                alert("Erreur lors de la modification de la réservation.");
            },
        });
    });
}

function attachEditReservationFormSubmitHandler() {
    $("#editReservationForm").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function () {
                $("#editReservationModal").modal("hide");
                location.reload();
            },
            error: function () {
                alert("Erreur lors de la modification de la réservation.");
            },
        });
    });
}

function attachCreateReservationFormSubmitHandler() {
    $("#createReservationForm").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            success: function () {
                $("#createReservationModal").modal("hide");
                location.reload();
            },
            error: function () {
                alert("Erreur lors de la création de la réservation.");
            },
        });
    });
}

