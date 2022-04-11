<?php

// fonction renvoyant true si l'utilisateur est connecté sinon false
function user_is_connected()
{
    if (!empty($_SESSION['membre'])) {
        return true;
    } else {
        return false;
    }
}

// fonction permettant de savoir si l'utilisateur est statut administrateur
function user_is_admin()
{
    if (user_is_connected() && $_SESSION['membre']['statut'] == 2) {
        return true;
    }
    return false;
}

// fonction pour gérer une classe active sur les liens du menu
function class_active($page)
{
    $page_name = basename($_SERVER['PHP_SELF'], ".php");
    if (in_array($page_name, $page)) {
        return 'active';
    }
}