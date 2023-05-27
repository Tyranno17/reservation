var calendar;
$(document).ready(function () {
    // $('#viewReservationModal').on('show.bs.modal', function (event) {
    //     var button = $(event.relatedTarget); // Bouton qui a déclenché la modale
    //     var creatorId = button.data('creator-id'); // Récupérer l'ID du créateur de la réservation
    //     var currentUserId = $('body').data('current-user-id'); // Récupérer l'ID de l'utilisateur actuellement connecté

    //     if (currentUserId !== creatorId) {
    //         // L'utilisateur actuellement connecté n'est pas le créateur de la réservation
    //         $('#editButtonInViewModal').hide();
    //     } else {
    //         $('#editButtonInViewModal').show();
    //     }
    // });
});
$(document).ready(function () {
    attachViewReservationHandler();
    attachCreateReservationHandler();
    // attachEditReservationHandler();
    attachEditReservationFormSubmitHandler();
    attachEditButtonInViewModalHandler();

});

function attachViewReservationHandler() {
    $(".view-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openViewReservationModal(reservationId);
    });
}

function attachEditButtonInViewModalHandler() {
    $("#editButtonInViewModal").on("click", function () {
        var reservationId = $("#viewReservationModal").data("reservation-id");
        $(this).data("reservation-id", reservationId);
        showEditModalFromViewModal();
    });
}


function openViewReservationModal(reservationId) {
    $("#viewReservationModal").modal("show");
    $("#viewReservationModal").data("reservation-id", reservationId);
    $("#viewReservationModal").find(".modal-body").load("view_reservation.php?id=" + reservationId);
}


function attachCreateReservationHandler() {
    $("#create-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openCreateReservationModal(reservationId);
    });
}

function openCreateReservationModal() {
    $("#createReservationModal").modal("show");
    $("#createReservationModal")
        .find(".modal-body")
        .load("create_reservation.php", function () {
            // Réinitialiser le formulaire à chaque fois que le modal est ouvert
            initFlatpickr();
            $("#createReservationForm input[type='text']").val("");
            $("#createReservationForm select").val("");


        });

}


function submitCreateReservationForm() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save-create-event.php", true); // Modifiez cette ligne
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var jsonResponse = JSON.parse(xhr.responseText);
                if (jsonResponse.success) {
                    // La réservation a été créée avec succès, rafraîchir le calendrier
                    if (calendar) {
                        calendar.refetchEvents();
                    }

                    $("#createReservationModal").modal("hide");

                    // Utilisez la nouvelle fonction pour afficher une alerte à l'utilisateur
                    createAlert('success', 'La réservation a été créée avec succès!');
                } else {
                    // Une erreur est survenue lors de la création de la réservation
                    createAlert('danger', jsonResponse.message);
                }
            } catch (e) {
                console.error("Parsing error:", e.message, xhr.responseText.substr(0, 100));
            }
        }
    };

    var formData = new FormData(document.getElementById("createReservationForm"));
    var params = new URLSearchParams(formData);
    xhr.send(params.toString());
}


function openEditReservationModal(reservationId) {
    $("#editReservationModal").modal("show");
    $("#editReservationModal")
        .find(".modal-body")
        .load("edit_reservation.php?id=" + reservationId, function () {
            initFlatpickr();
        });
}
//TEST
function attachEditReservationHandler() {
    $("#edit-reservation").on("click", function (e) {
        e.preventDefault();
        var reservationId = $(this).data("reservation-id");
        openEditReservationModal(reservationId);
    });
}

function showEditModalFromViewModal() {
    var reservationId = $("#editButtonInViewModal").data("reservation-id");
    $("#viewReservationModal").modal("hide");
    openEditReservationModal(reservationId);
}

function attachEditReservationFormSubmitHandler() {
    $("#editReservationForm").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            data: $(this).serialize(),
            async: true,
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

document.addEventListener("DOMContentLoaded", function () {
    var saveButton = document.getElementById("createButtonInCreateModal");
    if (saveButton) {
        saveButton.addEventListener("click", submitCreateReservationForm);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var saveButton = document.getElementById("editButtonInEditModal");
    if (saveButton) {
        saveButton.addEventListener("click", submitEditReservationForm);
    }
});



function initFlatpickr() {
    $(".flatpickr-datetime").flatpickr({
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        locale: "fr",
        minTime: "07:00",
        maxTime: "19:00",
        minuteIncrement: 00
    });
}


function createAlert(type, message) {
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.position = 'fixed';
        alertContainer.style.zIndex = '100';
        alertContainer.style.top = '10px';
        alertContainer.style.right = '10px';
        document.body.appendChild(alertContainer);
    }

    let alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dismissible fade show`;
    alertElement.role = 'alert';
    alertElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertContainer.appendChild(alertElement);
}

function submitEditReservationForm() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save-edit-event.php", true); // Modifiez cette ligne si nécessaire
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var jsonResponse = JSON.parse(xhr.responseText);
                if (jsonResponse.success) {
                    // La réservation a été modifiée avec succès, rafraîchir le calendrier
                    if (calendar) {
                        calendar.refetchEvents();
                    }
                    $("#editReservationModal").modal("hide");
                    // Utilisez la nouvelle fonction pour afficher une alerte à l'utilisateur
                    createAlert('success', 'La réservation a été modifiée avec succès!');
                } else {
                    // Une erreur est survenue lors de la modification de la réservation
                    createAlert('danger', jsonResponse.message);
                }
            } catch (e) {
                console.error("Parsing error:", e.message, xhr.responseText.substr(0, 100));
            }
        }
    };
    var formData = new FormData(document.getElementById("editReservationForm"));
    var params = new URLSearchParams(formData);
    xhr.send(params.toString());
}


