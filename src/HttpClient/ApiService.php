<?php

namespace App\HttpClient;

enum ApiService: string
{
    case LOGIN = "/api/auth";
    case ALL_PRODUCT = "/api/products";
    case ONE_PRODUCT = "/api/products/%s";
    case ORDER = "/api/orders";
    case REGISTER="/api/users";

}