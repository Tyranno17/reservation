$(document).ready(function () {
    attachViewReservationHandler();
    attachEditReservationHandler();
    attachCreateReservationHandler();
});

function attachViewReservationHandler() {
    $(".view-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        // console.log("Clic sur le lien Voir pour la réservation", reservationId);
        openViewReservationModal(reservationId);
    });
}

function openViewReservationModal(reservationId) {
    $("#viewReservationModal").modal("show");
    $("#viewReservationModal").find(".modal-body").load("view_reservation.php?id=" + reservationId);
}

function attachEditReservationHandler() {
    $(".edit-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        // console.log("Clic sur le lien Modifier pour la réservation", reservationId);
        // $("#viewReservationModal").one("hidden.bs.modal", function () {
        openEditReservationModal(reservationId);
    });
    // $("#viewReservationModal").modal("hide");
    //     });
}

function openEditReservationModal(reservationId) {
    $("#editReservationModal").modal("show");
    $("#editReservationModal").find(".modal-body").load("edit_reservation.php?id=" + reservationId);
}

function attachCreateReservationHandler() {
    $("#create-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        // console.log("Clic sur le bouton Créer une réservation");
        openCreateReservationModal();
    });
}

function openCreateReservationModal(reservationId) {
    $("#createReservationModal").modal("show");
    $("#createReservationModal").find(".modal-body").load("create_reservation.php?id=" + reservationId);
}



