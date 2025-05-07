<?php

namespace App\Helpers;

use App\Types\BootStrap;

class Form
{
    public static function Addform(BootStrap $bootstrap, string $addModalId, string $addDialogSize, string $addFormId, string $customAddAction, mixed $customAddFormRenderer, mixed $token, string $customAddFormHeader, ?string $addAction = null)
    {
        switch ($bootstrap) {

            case BootStrap::V5:
                echo '
    <div class="modal fade" id="' .
                    $addModalId .
                    '" tabindex="-1" aria-labelledby="' .
                    $addModalId .
                    'Label" aria-hidden="true">
    <div class="modal-dialog ' . $addDialogSize . '">
    <div class="modal-content">
      <form id="' .
                    $addFormId .
                    '" method="POST" action="' .
                    htmlspecialchars($customAddAction ?? "") .
                    '">
        <input type="hidden" name="' .
                    ($customAddFormRenderer || !empty($addAction) ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">';

                if (!empty($addAction)) {
                    echo '<input type="hidden" name="action" value="' . htmlspecialchars($addAction) . '">';
                } else {
                    echo '<input type="hidden" name="form_action" value="add">';
                }

                echo '
        <div class="modal-header">
          <h5 class="modal-title" id="' .
                    $addModalId .
                    'Label">' .
                    htmlspecialchars($customAddFormHeader) .
                    '</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">';

                break;

            case BootStrap::V3:

                echo '
<div class="modal fade" id="' .
                    $addModalId .
                    '" tabindex="-1" role="dialog" aria-labelledby="' .
                    $addModalId .
                    'Label" aria-hidden="true">
  <div class="modal-dialog ' . $addDialogSize . '">
    <div class="modal-content">
      <form id="' .
                    $addFormId .
                    '" method="POST" action="' .
                    htmlspecialchars($customAddAction ?? "") .
                    '">
        <input type="hidden" name="' .
                    ($customAddFormRenderer || !empty($addAction) ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">';

                if (!empty($addAction)) {
                    echo '<input type="hidden" name="action" value="' . htmlspecialchars($addAction) . '">';
                } else {
                    echo '<input type="hidden" name="form_action" value="add">';
                }

                echo '
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
          <h4 class="modal-title" id="' .
                    $addModalId .
                    'Label">' .
                    htmlspecialchars($customAddFormHeader) .
                    '</h4>
        </div>
        <div class="modal-body">';

                break;
        }
    }

    public static function editForm(BootStrap $bootStrap, string $editModalId, string $editDialogSize, string $editFormId, string $customEditAction, mixed $customEditFormRenderer, mixed $token, string $customEditFormHeader, ?string $editAction = null)
    {
        switch ($bootStrap) {
            case BootStrap::V5:
                echo '
<div class="modal fade" id="' .
                    $editModalId .
                    '" tabindex="-1" aria-labelledby="' .
                    $editModalId .
                    'Label" aria-hidden="true">
  <div class="modal-dialog ' . $editDialogSize . '">
    <div class="modal-content">
      <form id="' .
                    $editFormId .
                    '" method="POST" action="' .
                    htmlspecialchars($customEditAction ?? "") .
                    '">
        <input type="hidden" name="' .
                    ($customEditFormRenderer || !empty($editAction) ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">
        <input type="hidden" id="' .
                    $editModalId .
                    'editId" name="id" value="">';

                if (!empty($editAction)) {
                    echo '<input type="hidden" name="action" value="' . htmlspecialchars($editAction) . '">';
                } else {
                    echo '<input type="hidden" name="form_action" value="edit">';
                }

                echo '
        <div class="modal-header">
          <h5 class="modal-title" id="' .
                    $editModalId .
                    'Label">' .
                    $customEditFormHeader .
                    '</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="' .
                    $editFormId .
                    'Body">';


                break;

            case BootStrap::V3:
                echo '
<div class="modal fade" id="' .
                    $editModalId .
                    '" tabindex="-1" role="dialog" aria-labelledby="' .
                    $editModalId .
                    'Label" aria-hidden="true">
  <div class="modal-dialog ' . $editDialogSize . '">
    <div class="modal-content">
      <form id="' .
                    $editFormId .
                    '" method="POST" action="' .
                    htmlspecialchars($customEditAction ?? "") .
                    '">
        <input type="hidden" name="' .
                    ($customEditFormRenderer || !empty($editAction) ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">
        <input type="hidden" id="' .
                    $editModalId .
                    'editId" name="id" value="">';

                if (!empty($editAction)) {
                    echo '<input type="hidden" name="action" value="' . htmlspecialchars($editAction) . '">';
                } else {
                    echo '<input type="hidden" name="form_action" value="edit">';
                }

                echo '
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
          <h4 class="modal-title" id="' .
                    $editModalId .
                    'Label">' .
                    $customEditFormHeader .
                    '</h4>
        </div>
        <div class="modal-body" id="' .
                    $editFormId .
                    'Body">';


                break;
        }

    }

    public static function viewForm(BootStrap $bootStrap, string $viewModalId, string $viewDialogSize, string $viewFormId, mixed $customViewFormRenderer, mixed $token, string $customViewFormHeader, ?string $viewAction = null)
    {
        switch ($bootStrap) {
            case BootStrap::V5:

                echo '
    <div class="modal fade" id="' .
                    $viewModalId .
                    '" tabindex="-1" aria-labelledby="' .
                    $viewModalId .
                    'Label" aria-hidden="true">
            <div class="modal-dialog ' . $viewDialogSize . '">
        <div class="modal-content">
          <form id="' .
                    $viewFormId .
                    '" method="POST">
            <input type="hidden" name="' .
                    ($customViewFormRenderer ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">
            <input type="hidden" id="' .
                    $viewModalId .
                    'editId" name="id" value="">

            <input type="hidden" name="form_action" value="edit">
            <div class="modal-header">
              <h5 class="modal-title" id="' .
                    $viewModalId .
                    'Label">' .
                    $customViewFormHeader .
                    '</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="' .
                    $viewFormId .
                    'Body">';
                break;

            case BootStrap::V3:
                echo '
<div class="modal fade" id="' .
                    $viewModalId .
                    '" tabindex="-1" role="dialog" aria-labelledby="' .
                    $viewModalId .
                    'Label" aria-hidden="true">
  <div class="modal-dialog ' . $viewDialogSize . '">
    <div class="modal-content">
      <form id="' .
                    $viewFormId .
                    '" method="POST">
        <input type="hidden" name="' .
                    ($customViewFormRenderer ? "token" : "csrf_token") .
                    '" value="' .
                    htmlspecialchars($token) .
                    '">
        <input type="hidden" id="' .
                    $viewModalId .
                    'editId" name="id" value="">
        <input type="hidden" name="form_action" value="edit">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
          <h4 class="modal-title" id="' .
                    $viewModalId .
                    'Label">' .
                    $customViewFormHeader .
                    '</h4>
        </div>
        <div class="modal-body" id="' .
                    $viewFormId .
                    'Body">';

                break;

        }

    }
}