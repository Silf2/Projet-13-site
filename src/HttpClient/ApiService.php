<?php

namespace App\HttpClient;

enum ApiService: string
{
    case LOGIN = "/api/auth";
    case ONE_PRODUCT = "/api/products/%s";
}