<?php

namespace App\Enums;

enum OrganisasiStatus: string {
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}