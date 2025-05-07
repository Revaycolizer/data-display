<?php

namespace App\Helpers;

use App\Types\SweetAlert;

class SweetAlertHandle
{
    public static function handle(SweetAlert $sweetAlert, string $title,
                                  string     $message,
                                  string     $icon = "success")
    {

        switch ($sweetAlert) {

            case SweetAlert::V1:
                echo '<script>
    document.addEventListener("DOMContentLoaded", function () {
        swal({
            title: "' . addslashes($title) . '",
            text: "' . addslashes($message) . '",
            type: "' . $icon . '",
            confirmButtonText: "OK"
        }, function() {
            
        });
    });
';

                break;
            case SweetAlert::V2:
                echo '<script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: "' .
                    addslashes($title) .
                    '",
                text: "' .
                    addslashes($message) .
                    '",
                icon: "' .
                    $icon .
                    '",
                confirmButtonText: "OK"
            }).then(function () {

            });
        });
   ';
                break;
        }

    }

    public static function delete(SweetAlert $sweetAlert, string $title,
                                  string     $message, mixed $token, ?string $action = null)
    {
        switch ($sweetAlert) {
            case SweetAlert::V1:
                echo '
$(".deleteBtn").click(function() {
    var id = $(this).data("id");

    swal({
        title: "' . addslashes($title) . '",
        text: "' . addslashes($message) . '",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            const form = $("<form>", {
                method: "POST",
                action: ""
            });';

                if (!empty($action)) {
                    echo '
            form.append($("<input>", {
                type: "hidden",
                name: "action",
                value: "' . addslashes($action) . '"
            }));';
                } else {
                    echo '
            form.append($("<input>", {
                type: "hidden",
                name: "form_action",
                value: "delete"
            }));';
                }

                echo '
            form.append($("<input>", {
                type: "hidden",
                name: "id",
                value: id
            }));

            form.append($("<input>", {
                type: "hidden",
                name: "' . (!empty($action) ? 'token' : 'csrf_token') . '",
                value: "' . addslashes($token) . '"
            }));

            $("body").append(form);
            form.submit();
        }
     })
        });
    });';
                break;

            case SweetAlert::V2:
                echo '
$(".deleteBtn").click(function() {
    var id = $(this).data("id");

    Swal.fire({
        title: "' . addslashes($title) . '",
        text: "' . addslashes($message) . '",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            const form = $("<form>", {
                method: "POST",
                action: ""
            });';

                if (!empty($action)) {
                    echo '
            form.append($("<input>", {
                type: "hidden",
                name: "action",
                value: "' . addslashes($action) . '"
            }));';
                } else {
                    echo '
            form.append($("<input>", {
                type: "hidden",
                name: "form_action",
                value: "delete"
            }));';
                }

                echo '
            form.append($("<input>", {
                type: "hidden",
                name: "id",
                value: id
            }));

            form.append($("<input>", {
                type: "hidden",
                name: "' . (!empty($action) ? 'token' : 'csrf_token') . '",
                value: "' . addslashes($token) . '"
            }));

            $("body").append(form);
            form.submit();
        }
      })
        });
    });';
                break;
        }
    }

}