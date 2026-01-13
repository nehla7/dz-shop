<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents("php://input"), true);
$id = isset($input["id"]) ? (int)$input["id"] : 0;

if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["ok"=>false, "message"=>"ID invalide"]);
  exit;
}

if (!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
if (!isset($_SESSION["cart"][$id])) $_SESSION["cart"][$id] = 0;

$_SESSION["cart"][$id]++;

echo json_encode(["ok"=>true, "cart"=>$_SESSION["cart"]]);
